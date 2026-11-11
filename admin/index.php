<?php
// ================================================================
// FILE: admin/index.php
// ================================================================

require_once __DIR__ . '/../core/guard.php';

$current_user = require_role(['pharmacy_owner', 'pharmacy_admin']);
enforce_password_change();

$page_title = 'Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-pie"></i> Dashboard</h1>
</div>

<?php include __DIR__ . '/../includes/alerts.php'; ?>

<div class="card">
    <h2>Karibu, <?= e($current_user['full_name']) ?>! 👋</h2>
    <p style="color:var(--text-secondary);margin-top:8px;">
        Umeingia kama <strong><?= ucfirst(str_replace('_', ' ', $current_user['role_code'])) ?></strong>
        wa <strong><?= e($current_user['pharmacy_name']) ?></strong>.
    </p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">0</div>
        <div class="stat-label">Today's Sales</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">0</div>
        <div class="stat-label">Products</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">0</div>
        <div class="stat-label">Low Stock</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">0</div>
        <div class="stat-label">Debts (TSh)</div>
    </div>
</div>

<div class="card">
    <h3>🚧 Under Construction</h3>
    <p style="color:var(--text-secondary);margin-top:8px;">
        Dashboard modules (POS, Products, Inventory, Sales, Reports) zinakuja Phase 2.
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>