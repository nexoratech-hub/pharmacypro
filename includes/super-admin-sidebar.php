<?php
// ================================================================
// FILE: includes/super-admin-sidebar.php
// Blue sidebar + logout button
// Inahitaji: $current_page, $super_admin_name
// ================================================================
?>
<div class="sa-overlay" onclick="toggleSidebar()"></div>

<aside class="sa-sidebar">
    <div class="sa-logo">
        <div class="sa-logo-icon"><i class="fas fa-crown"></i></div>
        <div class="sa-logo-text">
            <h1>BismartTech</h1>
            <p>Super Admin</p>
        </div>
    </div>

    <nav class="sa-nav">
        <div class="sa-nav-label">Main</div>
        <a href="<?= APP_URL ?>/super-admin/index.php"
           class="sa-nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i><span>Dashboard</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/pharmacies.php"
           class="sa-nav-link <?= $current_page === 'pharmacies.php' ? 'active' : '' ?>">
            <i class="fas fa-hospital"></i><span>Pharmacies</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/plans.php"
           class="sa-nav-link <?= $current_page === 'plans.php' ? 'active' : '' ?>">
            <i class="fas fa-box-open"></i><span>Plans</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/subscriptions.php"
           class="sa-nav-link <?= $current_page === 'subscriptions.php' ? 'active' : '' ?>">
            <i class="fas fa-rotate"></i><span>Subscriptions</span>
        </a>

        <div class="sa-nav-label">Finance</div>
        <a href="<?= APP_URL ?>/super-admin/payments.php"
           class="sa-nav-link <?= $current_page === 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-money-bill-wave"></i><span>Payments</span>
        </a>

        <div class="sa-nav-label">System</div>
        <a href="<?= APP_URL ?>/super-admin/announcements.php"
           class="sa-nav-link <?= $current_page === 'announcements.php' ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i><span>Announcements</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/tickets.php"
           class="sa-nav-link <?= $current_page === 'tickets.php' ? 'active' : '' ?>">
            <i class="fas fa-life-ring"></i><span>Support Tickets</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/activity-logs.php"
           class="sa-nav-link <?= $current_page === 'activity-logs.php' ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i><span>Activity Logs</span>
        </a>
        <a href="<?= APP_URL ?>/super-admin/settings.php"
           class="sa-nav-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-gear"></i><span>Settings</span>
        </a>
    </nav>

    <div class="sa-sidebar-footer">
        <!-- User card -->
        <div class="sa-user-card">
            <div class="sa-user-avatar"><?= strtoupper(substr($super_admin_name, 0, 1)) ?></div>
            <div class="sa-user-info">
                <h4><?= e($super_admin_name) ?></h4>
                <p>Super Admin</p>
            </div>
        </div>

        <!-- Logout button -->
        <a href="<?= APP_URL ?>/auth/logout.php" class="sa-logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>