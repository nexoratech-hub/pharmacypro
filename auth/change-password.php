<?php
// ================================================================
// FILE: auth/change-password.php
// ================================================================

require_once __DIR__ . '/../core/auth.php';

$user = Auth::user();
if (!$user) redirect(APP_URL . '/auth/login.php');

$errors = [];
$force = !empty($_GET['force']) || !empty($_SESSION['must_change_password']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Security token invalid.';
    } else {
        $current  = post('current_password');
        $new      = post('new_password');
        $confirm  = post('confirm_password');

        if (!password_verify($current, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }
        if (strlen($new) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        }
        if ($new !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            DB::run(
                "UPDATE users SET password_hash = ?, must_change_password = 0, updated_at = NOW() WHERE id = ?",
                [$hash, $user['id']]
            );
            $_SESSION['must_change_password'] = 0;

            DB::run(
                "INSERT INTO audit_logs (pharmacy_id, user_id, user_name, action, module, description, created_at)
                 VALUES (?, ?, ?, 'password_change', 'users', 'User changed own password', NOW())",
                [$user['pharmacy_id'], $user['id'], $user['full_name']]
            );

            $redirect = match ($user['role_code']) {
                'super_admin' => APP_URL . '/super-admin/',
                'pharmacy_owner', 'pharmacy_admin' => APP_URL . '/admin/',
                default => APP_URL . '/employee/',
            };
            flash('dashboard', 'Password updated successfully!', 'success');
            redirect($redirect);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — PharmacyPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
</head>
<body class="login-page">
    <div class="bg-decoration">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
    </div>

    <div class="login-container" style="grid-template-columns: 1fr; max-width: 560px;">
        <div class="login-form-panel">
            <div class="form-header">
                <h2><i class="fas fa-key" style="color:#159B62;"></i> Change Password</h2>
                <?php if ($force): ?>
                    <p style="color:#D97706;font-weight:600;">
                        <i class="fas fa-exclamation-triangle"></i>
                        First login — you must change your password before continuing.
                    </p>
                <?php else: ?>
                    <p>Update your account password</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <?php foreach ($errors as $err): ?>
                            <div><?= e($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-field">
                    <label><i class="fas fa-lock"></i> Current Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="current_password" required placeholder="Enter current password">
                    </div>
                </div>

                <div class="form-field">
                    <label><i class="fas fa-key"></i> New Password (min <?= PASSWORD_MIN_LENGTH ?> chars)</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="new_password" required minlength="<?= PASSWORD_MIN_LENGTH ?>" placeholder="Enter new password">
                    </div>
                </div>

                <div class="form-field">
                    <label><i class="fas fa-check"></i> Confirm New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-check input-icon"></i>
                        <input type="password" name="confirm_password" required placeholder="Repeat new password">
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-save"></i>
                    <span>Update Password</span>
                </button>

                <?php if (!$force): ?>
                    <div style="text-align:center;margin-top:16px;">
                        <a href="<?= APP_URL ?>/index.php" style="color:#1769AA;font-weight:600;font-size:0.9rem;">
                            ← Back to Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>