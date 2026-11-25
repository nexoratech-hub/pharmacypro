<?php
// ================================================================
// FILE: includes/super-admin-header.php
// Inahitaji: $page_title, $super_admin_name
// ================================================================
?>
<header class="sa-header">
    <div class="sa-header-left">
        <button class="sa-toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <h2 class="sa-page-title">
                <?= e($page_title) ?>
                <small>Welcome, <?= e($super_admin_name) ?> 👑</small>
            </h2>
        </div>
    </div>

    <div class="sa-header-right">
        <button class="sa-header-btn" title="Search"><i class="fas fa-search"></i></button>
        <button class="sa-header-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="sa-dot"></span>
        </button>
        <div class="sa-header-divider"></div>
        <a href="<?= APP_URL ?>/super-admin/settings.php" class="sa-header-user">
            <div class="sa-user-avatar"><?= strtoupper(substr($super_admin_name, 0, 1)) ?></div>
            <span><?= e($super_admin_name) ?></span>
        </a>
        <a href="<?= APP_URL ?>/auth/logout.php" class="sa-header-btn" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</header>