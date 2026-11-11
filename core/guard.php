<?php
// ================================================================
// FILE: core/guard.php
// ================================================================

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

function require_login(): array
{
    $user = Auth::user();
    if (!$user) {
        redirect(APP_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
    }
    if (!empty($_SESSION['login_at']) && (time() - $_SESSION['login_at']) > SESSION_LIFETIME) {
        Auth::logout();
        redirect(APP_URL . '/auth/login.php?timeout=1');
    }
    return $user;
}

function require_role($roles): array
{
    $user = require_login();
    $roles = (array) $roles;
    if (!in_array($user['role_code'], $roles, true)) {
        http_response_code(403);
        die('403 — Access denied.');
    }
    return $user;
}

function can(string $permission): bool
{
    if (empty($_SESSION['user_id'])) return false;
    $user = Auth::user();
    if (!$user) return false;
    return in_array($permission, Auth::permissions((int)$user['role_id']), true);
}

function enforce_password_change(): void
{
    if (empty($_SESSION['must_change_password'])) return;
    $current = basename($_SERVER['PHP_SELF']);
    if ($current === 'change-password.php') return;
    redirect(APP_URL . '/auth/change-password.php?force=1');
}