<?php
// ================================================================
// FILE: index.php
// ================================================================

require_once __DIR__ . '/core/auth.php';

$user = Auth::user();

if (!$user) {
    redirect(APP_URL . '/auth/login.php');
}

match ($user['role_code']) {
    'super_admin'                       => redirect(APP_URL . '/super-admin/'),
    'pharmacy_owner', 'pharmacy_admin'  => redirect(APP_URL . '/admin/'),
    default                             => redirect(APP_URL . '/employee/'),
};