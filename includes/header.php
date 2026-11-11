<?php
// ================================================================
// FILE: includes/header.php
// ================================================================

$current_user = Auth::user();
if (!$current_user) redirect(APP_URL . '/auth/login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body class="app-layout">

    <!-- Top bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-brand">
                <i class="fas fa-prescription-bottle-medical"></i>
                <span><?= APP_NAME ?></span>
            </div>
        </div>
        <div class="topbar-right">
            <?php if ($current_user['role_code'] === 'super_admin'): ?>
                <span class="topbar-badge"><i class="fas fa-crown"></i> Super Admin</span>
            <?php else: ?>
                <span class="topbar-badge"><i class="fas fa-hospital"></i> <?= e($current_user['pharmacy_name']) ?></span>
            <?php endif; ?>
            <div class="topbar-user">
                <i class="fas fa-user-circle"></i>
                <div>
                    <div class="topbar-user-name"><?= e($current_user['full_name']) ?></div>
                    <div class="topbar-user-role"><?= ucfirst(str_replace('_', ' ', $current_user['role_code'])) ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main content start -->
    <div class="app-body">