<?php
// ================================================================
// FILE: super-admin/index.php
// ================================================================

require_once __DIR__ . '/../core/guard.php';

$current_user = require_role('super_admin');

$page_title = 'Super Admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-crown"></i> Super Admin Dashboard</h1>
</div>

<?php include __DIR__ . '/../includes/alerts.php'; ?>

<div class="card">
    <h2>Karibu, <?= e($current_user['full_name']) ?>! 👑</h2>
    <p style="color:var(--text-secondary);margin-top:8px;">
        Umeingia kama <strong>BismartTech Super Admin</strong>. Hapa utaweza kusimamia pharmacies zote, plans, subscriptions, na payments.
    </p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">1</div>
        <div class="stat-label">Total Pharmacies</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">1</div>
        <div class="stat-label">Trial Accounts</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">0</div>
        <div class="stat-label">Active Subscriptions</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">TSh 0</div>
        <div class="stat-label">Monthly Revenue</div>
    </div>
</div>

<div class="card">
    <h3>🚧 Under Construction</h3>
    <p style="color:var(--text-secondary);margin-top:8px;">
        Pharmacies management, plans, subscriptions, na payments zinakuja Phase 2.
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>