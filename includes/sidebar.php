<?php
// ================================================================
// FILE: includes/sidebar.php
// ================================================================

$current_user = Auth::user();
if (!$current_user) return;

$role = $current_user['role_code'];
$current_page = basename($_SERVER['PHP_SELF']);
$current_url = $_SERVER['REQUEST_URI'];
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <?php if (!empty($current_user['pharmacy_logo'])): ?>
            <img src="<?= APP_URL ?>/storage/uploads/logos/<?= e($current_user['pharmacy_logo']) ?>"
                 alt="Logo" class="sidebar-logo-img">
        <?php else: ?>
            <div class="sidebar-logo-icon">
                <i class="fas fa-prescription-bottle-medical"></i>
            </div>
        <?php endif; ?>
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">
                <?= $role === 'super_admin' ? APP_NAME : e($current_user['pharmacy_name']) ?>
            </div>
            <div class="sidebar-brand-role">
                <?= ucfirst(str_replace('_', ' ', $role)) ?>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php if ($role === 'super_admin'): ?>
            <!-- SUPER ADMIN MENU -->
            <div class="sidebar-section">Platform</div>
            <a href="<?= APP_URL ?>/super-admin/index.php" class="sidebar-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?= APP_URL ?>/super-admin/pharmacies.php" class="sidebar-link <?= $current_page === 'pharmacies.php' ? 'active' : '' ?>">
                <i class="fas fa-hospital"></i> Pharmacies
            </a>
            <a href="<?= APP_URL ?>/super-admin/plans.php" class="sidebar-link <?= $current_page === 'plans.php' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> Plans
            </a>
            <a href="<?= APP_URL ?>/super-admin/subscriptions.php" class="sidebar-link <?= $current_page === 'subscriptions.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Subscriptions
            </a>

            <div class="sidebar-section">Communication</div>
            <a href="<?= APP_URL ?>/super-admin/announcements.php" class="sidebar-link <?= $current_page === 'announcements.php' ? 'active' : '' ?>">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            <a href="<?= APP_URL ?>/super-admin/activity-logs.php" class="sidebar-link <?= $current_page === 'activity-logs.php' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Activity Logs
            </a>

        <?php else: ?>
            <!-- PHARMACY USER MENU -->
            <div class="sidebar-section">Main Menu</div>
            <a href="<?= APP_URL ?>/admin/index.php" class="sidebar-link <?= strpos($current_url, '/admin/index.php') !== false ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?= APP_URL ?>/admin/pos.php" class="sidebar-link <?= $current_page === 'pos.php' ? 'active' : '' ?>">
                <i class="fas fa-cash-register"></i> POS
            </a>
            <a href="<?= APP_URL ?>/admin/products.php" class="sidebar-link <?= $current_page === 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="<?= APP_URL ?>/admin/inventory.php" class="sidebar-link <?= $current_page === 'inventory.php' ? 'active' : '' ?>">
                <i class="fas fa-warehouse"></i> Inventory
            </a>
            <a href="<?= APP_URL ?>/admin/sales.php" class="sidebar-link <?= $current_page === 'sales.php' ? 'active' : '' ?>">
                <i class="fas fa-receipt"></i> Sales
            </a>

            <div class="sidebar-section">Reports</div>
            <a href="<?= APP_URL ?>/admin/reports.php" class="sidebar-link <?= $current_page === 'reports.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Reports
            </a>

            <div class="sidebar-section">Account</div>
            <a href="<?= APP_URL ?>/admin/settings.php" class="sidebar-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
        <?php endif; ?>

        <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link sidebar-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>