<?php
// ================================================================
// FILE: core/auth.php
// Login: USERNAME or EMAIL + password
// ================================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

class Auth
{
    public static function login(string $identifier, string $password): array
    {
        $identifier = trim($identifier);
        if ($identifier === '' || $password === '') {
            return ['ok' => false, 'message' => 'Enter your username/email and password.'];
        }

        // Login with USERNAME or EMAIL (case-insensitive)
        $user = DB::one(
            "SELECT u.*, r.role_code, r.level AS role_level,
                    p.pharmacy_name, p.status AS pharmacy_status,
                    p.logo AS pharmacy_logo,
                    p.subscription_ends_at, p.trial_ends_at
             FROM users u
             INNER JOIN roles r ON u.role_id = r.id
             LEFT JOIN pharmacies p ON u.pharmacy_id = p.id
             WHERE (LOWER(u.username) = LOWER(?) OR LOWER(u.email) = LOWER(?))
             LIMIT 1",
            [$identifier, $identifier]
        );

        if (!$user) {
            return ['ok' => false, 'message' => 'Invalid username/email or password.'];
        }

        // Lockout check
        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $mins = ceil((strtotime($user['locked_until']) - time()) / 60);
            return ['ok' => false, 'message' => "Account locked. Try again in {$mins} minute(s)."];
        }

        // Status check
        if ($user['status'] !== 'active') {
            return ['ok' => false, 'message' => 'Your account is inactive. Contact your administrator.'];
        }

        // Pharmacy status check (skip for super_admin)
        if ($user['role_code'] !== 'super_admin' && $user['pharmacy_id']) {
            if (in_array($user['pharmacy_status'], ['suspended', 'cancelled'])) {
                return ['ok' => false, 'message' => 'Your pharmacy account is suspended.'];
            }
        }

        // Password verify
        if (!password_verify($password, $user['password_hash'])) {
            self::recordFailedAttempt($user['id'], $user);
            return ['ok' => false, 'message' => 'Invalid username/email or password.'];
        }

        // Success
        DB::run(
            "UPDATE users SET failed_attempts = 0, locked_until = NULL,
                              last_login_at = NOW(), last_login_ip = ?, is_online = 1, last_online = NOW()
             WHERE id = ?",
            [client_ip(), $user['id']]
        );

        session_regenerate_id(true);

        $_SESSION['user_id']       = (int) $user['id'];
        $_SESSION['pharmacy_id']   = $user['pharmacy_id'] ? (int)$user['pharmacy_id'] : null;
        $_SESSION['pharmacy_name'] = $user['pharmacy_name'] ?? 'PharmacyPro Platform';
        $_SESSION['pharmacy_logo'] = $user['pharmacy_logo'] ?? null;
        $_SESSION['role_code']     = $user['role_code'];
        $_SESSION['role_level']    = $user['role_level'];
        $_SESSION['full_name']     = $user['full_name'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['email']         = $user['email'];
        $_SESSION['profile_pic']   = $user['profile_pic'];
        $_SESSION['login_at']      = time();
        $_SESSION['must_change_password'] = (int)($user['must_change_password'] ?? 0);

        // Determine landing page
        $redirect = match ($user['role_code']) {
            'super_admin'                       => APP_URL . '/super-admin/',
            'pharmacy_owner', 'pharmacy_admin'  => APP_URL . '/admin/',
            default                             => APP_URL . '/employee/',
        };

        // Force password change
        if (!empty($user['must_change_password']) && $user['role_code'] !== 'super_admin') {
            $redirect = APP_URL . '/auth/change-password.php?force=1';
        }

        return ['ok' => true, 'role' => $user['role_code'], 'redirect' => $redirect];
    }

    private static function recordFailedAttempt(int $userId, array $user): void
    {
        $attempts = ((int)$user['failed_attempts']) + 1;
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            DB::run(
                "UPDATE users SET failed_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL ? SECOND) WHERE id = ?",
                [$attempts, LOCKOUT_DURATION, $userId]
            );
        } else {
            DB::run("UPDATE users SET failed_attempts = ? WHERE id = ?", [$attempts, $userId]);
        }
    }

    public static function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            DB::run("UPDATE users SET is_online = 0 WHERE id = ?", [$_SESSION['user_id']]);
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) return null;

        return DB::one(
            "SELECT u.*, r.role_code, r.level AS role_level,
                    p.pharmacy_name, p.status AS pharmacy_status,
                    p.logo AS pharmacy_logo, p.phone AS pharmacy_phone,
                    p.email AS pharmacy_email, p.address AS pharmacy_address,
                    p.subscription_ends_at, p.trial_ends_at,
                    b.branch_name
             FROM users u
             INNER JOIN roles r ON u.role_id = r.id
             LEFT JOIN pharmacies p ON u.pharmacy_id = p.id
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE u.id = ?",
            [$_SESSION['user_id']]
        );
    }

    public static function permissions(int $roleId): array
    {
        static $cache = [];
        if (isset($cache[$roleId])) return $cache[$roleId];

        $rows = DB::all(
            "SELECT p.permission_code
             FROM role_permissions rp
             INNER JOIN permissions p ON rp.permission_id = p.id
             WHERE rp.role_id = ?",
            [$roleId]
        );

        $cache[$roleId] = array_column($rows, 'permission_code');
        return $cache[$roleId];
    }
}