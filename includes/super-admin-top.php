<?php
// ================================================================
// FILE: includes/super-admin-top.php
// Inafungua <html><body> + sidebar + header
// Inahitaji: $page_title, $current_page, $super_admin_name
// ================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/head.php'; ?>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/super-admin.css">
</head>
<body>

    <?php require __DIR__ . '/super-admin-sidebar.php'; ?>
    <?php require __DIR__ . '/super-admin-header.php'; ?>

    <main class="sa-main">
        <div class="sa-content">