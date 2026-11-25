<?php
// ================================================================
// FILE: super-admin/index.php
// Dashboard — aligned with actual DB (subscriptions, not payments)
// ================================================================

require_once __DIR__ . '/../core/guard.php';

$current_user = require_role('super_admin');
$page_title   = 'Dashboard';
$super_admin_name = $current_user['full_name'] ?? 'Super Admin';

// ── Live queries (aligned with pharmacypro.sql) ─────────────
$total_pharmacies = 0;
$trial_count      = 0;
$active_count     = 0;
$expired_count    = 0;
$monthly_revenue  = 0;
$active_subs      = 0;

try {
    $total_pharmacies = (int) (DB::one("SELECT COUNT(*) AS c FROM pharmacies")['c'] ?? 0);
    $trial_count      = (int) (DB::one("SELECT COUNT(*) AS c FROM pharmacies WHERE status = 'trial'")['c'] ?? 0);
    $active_count     = (int) (DB::one("SELECT COUNT(*) AS c FROM pharmacies WHERE status = 'active'")['c'] ?? 0);
    $expired_count    = (int) (DB::one("SELECT COUNT(*) AS c FROM pharmacies WHERE status IN ('expired','cancelled')")['c'] ?? 0);

    // Active subscriptions (paid)
    $active_subs = (int) (DB::one("SELECT COUNT(*) AS c FROM subscriptions WHERE status = 'paid'")['c'] ?? 0);

    // Monthly revenue from subscriptions
    $revenue_row = DB::one("
        SELECT COALESCE(SUM(amount), 0) AS total
        FROM subscriptions
        WHERE status = 'paid'
          AND MONTH(paid_at) = MONTH(CURRENT_DATE())
          AND YEAR(paid_at)  = YEAR(CURRENT_DATE())
    ");
    $monthly_revenue = (float) ($revenue_row['total'] ?? 0);

} catch (Exception $e) {
    error_log("Dashboard query error: " . $e->getMessage());
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> — PharmacyPro SaaS</title>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/logo.png">
    <link rel="apple-touch-icon" href="<?= APP_URL ?>/assets/img/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           THEME — Blue #007EFF
           ============================================================ */
        :root {
            --primary:    #007EFF;
            --primary-dk: #0066CC;

            --blue-50:    #EFF6FF;
            --blue-100:   #DBEAFE;
            --blue-200:   #BFDBFE;
            --blue-300:   #93C5FD;
            --blue-400:   #60A5FA;
            --blue-500:   #3B82F6;
            --blue-600:   #2563EB;
            --blue-700:   #1D4ED8;
            --blue-800:   #1E40AF;
            --blue-900:   #1E3A8A;
            --blue-950:   #172554;

            --green-50:   #ECFDF5;
            --green-200:  #A7F3D0;
            --green-500:  #10B981;
            --green-600:  #059669;
            --green-700:  #047857;

            --red-50:     #FEF2F2;
            --red-200:    #FECACA;
            --red-500:    #EF4444;
            --red-600:    #DC2626;
            --red-700:    #B91C1C;

            --amber-50:   #FFFBEB;
            --amber-500:  #F59E0B;
            --amber-600:  #D97706;
            --amber-700:  #B45309;

            --slate-50:   #F8FAFC;
            --slate-100:  #F1F5F9;
            --slate-200:  #E2E8F0;
            --slate-300:  #CBD5E1;
            --slate-400:  #94A3B8;
            --slate-500:  #64748B;
            --slate-600:  #475569;
            --slate-700:  #334155;
            --slate-800:  #1E293B;
            --slate-900:  #0F172A;

            --sidebar-w: 270px;
            --header-h:  70px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--slate-50);
            background-image:
                radial-gradient(at 0% 0%, rgba(0, 126, 255, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--slate-700);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ---- SIDEBAR ---- */
        .sa-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #1E3A8A 0%, #1E40AF 50%, #172554 100%);
            color: #fff;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 32px rgba(23, 37, 84, 0.3);
            overflow: hidden;
        }

        .sa-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 240px;
            background: radial-gradient(ellipse at top, rgba(0, 126, 255, 0.35) 0%, transparent 70%);
            pointer-events: none;
        }

        .sa-logo {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            min-height: var(--header-h);
            position: relative;
            z-index: 1;
        }

        .sa-logo-icon {
            width: 44px; height: 44px;
            background: #007EFF;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 4px 14px rgba(0, 126, 255, 0.6);
            color: #fff;
        }

        .sa-logo-text h1 { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .sa-logo-text p { font-size: 0.68rem; color: rgba(191, 219, 254, 0.9); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; margin-top: 2px; }

        .sa-nav { flex: 1; padding: 1rem 0.75rem; overflow-y: auto; position: relative; z-index: 1; }

        .sa-nav-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(191, 219, 254, 0.7);
            font-weight: 700;
            padding: 0.85rem 0.85rem 0.5rem;
        }

        .sa-nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.72rem 0.85rem;
            color: rgba(219, 234, 254, 0.85);
            text-decoration: none;
            border-radius: 11px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 0.2rem;
            transition: all 0.22s ease;
            border: 1px solid transparent;
        }

        .sa-nav-link i { width: 20px; text-align: center; font-size: 1rem; }
        .sa-nav-link:hover { background: rgba(255, 255, 255, 0.1); color: #fff; transform: translateX(2px); }
        .sa-nav-link.active {
            background: #007EFF;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 6px 18px -2px rgba(0, 126, 255, 0.6);
        }

        .sa-sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); position: relative; z-index: 1; }

        .sa-user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sa-user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #007EFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
        }

        .sa-user-info h4 { font-size: 0.83rem; font-weight: 600; color: #fff; }
        .sa-user-info p { font-size: 0.7rem; color: rgba(191, 219, 254, 0.8); }

        /* ---- HEADER (LIGHT) ---- */
        .sa-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--header-h);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            border-bottom: 1px solid var(--slate-200);
        }

        .sa-header-left { display: flex; align-items: center; gap: 1rem; }

        .sa-toggle-btn {
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            width: 40px; height: 40px;
            border-radius: 11px;
            color: var(--slate-700);
            font-size: 1.05rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sa-toggle-btn:hover { background: var(--blue-50); color: var(--primary-dk); border-color: var(--blue-200); }

        .sa-page-title { font-size: 1.12rem; font-weight: 700; color: var(--slate-800); }
        .sa-page-title small { display: block; font-size: 0.74rem; color: var(--slate-500); margin-top: 2px; }

        .sa-header-right { display: flex; align-items: center; gap: 0.6rem; }

        .sa-header-btn {
            position: relative;
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            color: var(--slate-600);
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sa-header-btn:hover { background: var(--blue-50); color: var(--primary-dk); border-color: var(--blue-200); }

        .sa-header-btn .sa-dot {
            position: absolute;
            top: 8px; right: 10px;
            width: 8px; height: 8px;
            background: var(--red-500);
            border-radius: 50%;
            border: 2px solid #fff;
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.25); opacity: 0.7; }
        }

        .sa-header-divider { width: 1px; height: 28px; background: var(--slate-200); }

        .sa-header-user {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.35rem 0.75rem 0.35rem 0.35rem;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            text-decoration: none;
        }

        .sa-header-user .sa-user-avatar { width: 32px; height: 32px; font-size: 0.75rem; }
        .sa-header-user span { font-size: 0.85rem; font-weight: 600; color: var(--slate-700); }

        /* ---- MAIN ---- */
        .sa-main { margin-left: var(--sidebar-w); padding-top: var(--header-h); min-height: 100vh; }
        .sa-content { padding: 1.75rem; max-width: 1600px; margin: 0 auto; }

        /* ---- PAGE HEADER ---- */
        .sa-page-header { margin-bottom: 1.5rem; }
        .sa-page-header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--slate-800);
            letter-spacing: -0.03em;
            margin: 0 0 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .sa-page-header h1 i { color: var(--primary); }
        .sa-page-header p { color: var(--slate-500); font-size: 0.875rem; margin: 0; }

        /* ---- BUTTONS ---- */
        .sa-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.15rem;
            border-radius: 11px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sa-btn-primary {
            background: #007EFF;
            color: #fff;
            box-shadow: 0 4px 14px -2px rgba(0, 126, 255, 0.55);
        }
        .sa-btn-primary:hover { background: #0066CC; transform: translateY(-2px); color: #fff; }

        /* ---- STATS MINI ---- */
        .sa-stats-mini {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .sa-mini-card {
            background: #fff;
            border-radius: 14px;
            padding: 1rem 1.15rem;
            border: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.25s ease;
        }
        .sa-mini-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); }
        .sa-mini-icon {
            width: 44px; height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .sa-mini-icon.blue  { background: rgba(0, 126, 255, 0.1);  color: #007EFF; }
        .sa-mini-icon.green { background: var(--green-50); color: var(--green-600); }
        .sa-mini-icon.amber { background: var(--amber-50); color: var(--amber-600); }
        .sa-mini-icon.red   { background: var(--red-50);   color: var(--red-600); }
        .sa-mini-value { font-size: 1.35rem; font-weight: 800; color: var(--slate-800); line-height: 1.1; }
        .sa-mini-label { font-size: 0.75rem; color: var(--slate-500); font-weight: 500; margin-top: 2px; }

        /* ---- QUICK ACTIONS ---- */
        .sa-quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .sa-quick-action {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.7rem;
            padding: 1.15rem;
            border-radius: 14px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }
        .sa-quick-action:hover { transform: translateY(-5px); box-shadow: 0 18px 36px -8px rgba(0, 0, 0, 0.22); }
        .sa-quick-action i {
            width: 42px; height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.35s ease;
        }
        .sa-quick-action:hover i { transform: scale(1.1) rotate(-5deg); }
        .sa-quick-action span { font-size: 0.88rem; font-weight: 600; }
        .sa-quick-action.blue   { background: #007EFF; }
        .sa-quick-action.green  { background: #10B981; }
        .sa-quick-action.amber  { background: #F59E0B; }
        .sa-quick-action.red    { background: #EF4444; }

        /* ---- CHARTS ---- */
        .sa-charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .sa-chart-card {
            background: #fff;
            border-radius: 18px;
            padding: 1.4rem;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
        }
        .sa-chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .sa-chart-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sa-chart-header h3 i { color: var(--primary); font-size: 0.9rem; }
        .sa-chart-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            background: var(--blue-50);
            color: var(--blue-700);
            border: 1px solid var(--blue-200);
        }
        .sa-chart-body { position: relative; flex: 1; min-height: 240px; }
        .sa-chart-body canvas { max-height: 280px; }

        /* ---- ALERTS ---- */
        .sa-alert { padding: 0.9rem 1.1rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.7rem; }
        .sa-alert.success { background: var(--green-50); color: var(--green-700); border-left: 4px solid var(--green-500); }
        .sa-alert.error { background: var(--red-50); color: var(--red-600); border-left: 4px solid var(--red-500); }

        /* ---- FOOTER ---- */
        .sa-footer {
            margin-top: 2rem;
            padding: 1.15rem 1.5rem;
            border-top: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.82rem;
            color: var(--slate-500);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .sa-footer-brand { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--slate-800); }
        .sa-footer-brand i { color: #007EFF; }
        .sa-footer-links { display: flex; gap: 1.25rem; flex-wrap: wrap; }
        .sa-footer-links a { color: var(--slate-500); text-decoration: none; font-weight: 500; }
        .sa-footer-links a:hover { color: var(--primary); }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 1199.98px) {
            .sa-charts-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 991.98px) {
            .sa-sidebar { display: none; }
            .sa-header { left: 0; }
            .sa-main { margin-left: 0; }
        }
        @media (max-width: 767.98px) {
            .sa-stats-mini { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 575.98px) {
            .sa-content { padding: 1rem; }
            .sa-page-header h1 { font-size: 1.35rem; }
            .sa-stats-mini { grid-template-columns: 1fr; }
            .sa-header-user span { display: none; }
            .sa-footer { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
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
            <a href="<?= APP_URL ?>/super-admin/index.php" class="sa-nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/pharmacies.php" class="sa-nav-link <?= $current_page === 'pharmacies.php' ? 'active' : '' ?>">
                <i class="fas fa-hospital"></i><span>Pharmacies</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/plans.php" class="sa-nav-link <?= $current_page === 'plans.php' ? 'active' : '' ?>">
                <i class="fas fa-box-open"></i><span>Plans</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/subscriptions.php" class="sa-nav-link <?= $current_page === 'subscriptions.php' ? 'active' : '' ?>">
                <i class="fas fa-rotate"></i><span>Subscriptions</span>
            </a>

            <div class="sa-nav-label">Finance</div>
            <a href="<?= APP_URL ?>/super-admin/payments.php" class="sa-nav-link <?= $current_page === 'payments.php' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave"></i><span>Payments</span>
            </a>

            <div class="sa-nav-label">System</div>
            <a href="<?= APP_URL ?>/super-admin/announcements.php" class="sa-nav-link <?= $current_page === 'announcements.php' ? 'active' : '' ?>">
                <i class="fas fa-bullhorn"></i><span>Announcements</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/tickets.php" class="sa-nav-link <?= $current_page === 'tickets.php' ? 'active' : '' ?>">
                <i class="fas fa-life-ring"></i><span>Support Tickets</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/activity-logs.php" class="sa-nav-link <?= $current_page === 'activity-logs.php' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-list"></i><span>Activity Logs</span>
            </a>
            <a href="<?= APP_URL ?>/super-admin/settings.php" class="sa-nav-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-gear"></i><span>Settings</span>
            </a>
        </nav>

        <div class="sa-sidebar-footer">
            <div class="sa-user-card">
                <div class="sa-user-avatar"><?= strtoupper(substr($super_admin_name, 0, 1)) ?></div>
                <div class="sa-user-info">
                    <h4><?= e($super_admin_name) ?></h4>
                    <p>Super Admin</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- HEADER -->
    <header class="sa-header">
        <div class="sa-header-left">
            <button class="sa-toggle-btn"><i class="fas fa-bars"></i></button>
            <div>
                <h2 class="sa-page-title">
                    <?= e($page_title) ?>
                    <small>Welcome, <?= e($super_admin_name) ?> 👑</small>
                </h2>
            </div>
        </div>

        <div class="sa-header-right">
            <button class="sa-header-btn"><i class="fas fa-search"></i></button>
            <button class="sa-header-btn"><i class="fas fa-bell"></i><span class="sa-dot"></span></button>
            <div class="sa-header-divider"></div>
            <a href="<?= APP_URL ?>/super-admin/settings.php" class="sa-header-user">
                <div class="sa-user-avatar"><?= strtoupper(substr($super_admin_name, 0, 1)) ?></div>
                <span><?= e($super_admin_name) ?></span>
            </a>
            <a href="<?= APP_URL ?>/auth/logout.php" class="sa-header-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <!-- MAIN -->
    <main class="sa-main">
        <div class="sa-content">

            <!-- Page Header -->
            <div class="sa-page-header">
                <h1><i class="fas fa-crown"></i> Super Admin Dashboard</h1>
                <p>Welcome, <strong><?= e($super_admin_name) ?></strong>! 👑 Overview of the platform.</p>
            </div>

            <!-- Flash Alerts -->
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="sa-alert success">
                    <i class="fas fa-circle-check"></i>
                    <span><?= e($_SESSION['flash_success']) ?></span>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="sa-alert error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?= e($_SESSION['flash_error']) ?></span>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- Stats -->
            <div class="sa-stats-mini">
                <div class="sa-mini-card">
                    <div class="sa-mini-icon blue"><i class="fas fa-hospital"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($total_pharmacies) ?></div>
                        <div class="sa-mini-label">Total Pharmacies</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon green"><i class="fas fa-circle-check"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($active_subs) ?></div>
                        <div class="sa-mini-label">Active Subscriptions</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon amber"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($trial_count) ?></div>
                        <div class="sa-mini-label">Trial Accounts</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon red"><i class="fas fa-money-bill-trend-up"></i></div>
                    <div>
                        <div class="sa-mini-value">TSh <?= number_format($monthly_revenue) ?></div>
                        <div class="sa-mini-label">Monthly Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="sa-quick-actions">
                <a href="<?= APP_URL ?>/super-admin/pharmacy-add.php" class="sa-quick-action blue">
                    <i class="fas fa-plus"></i>
                    <span>Add Pharmacy</span>
                </a>
                <a href="<?= APP_URL ?>/super-admin/plans.php" class="sa-quick-action green">
                    <i class="fas fa-box-open"></i>
                    <span>Manage Plans</span>
                </a>
                <a href="<?= APP_URL ?>/super-admin/payments.php" class="sa-quick-action amber">
                    <i class="fas fa-money-check-dollar"></i>
                    <span>Verify Payments</span>
                </a>
                <a href="<?= APP_URL ?>/super-admin/tickets.php" class="sa-quick-action red">
                    <i class="fas fa-life-ring"></i>
                    <span>Support Tickets</span>
                </a>
            </div>

            <!-- Charts -->
            <div class="sa-charts-grid">
                <div class="sa-chart-card">
                    <div class="sa-chart-header">
                        <h3><i class="fas fa-chart-area"></i> Revenue Overview</h3>
                        <span class="sa-chart-badge">Last 6 Months</span>
                    </div>
                    <div class="sa-chart-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="sa-chart-card">
                    <div class="sa-chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Subscriptions</h3>
                        <span class="sa-chart-badge">Distribution</span>
                    </div>
                    <div class="sa-chart-body">
                        <canvas id="subscriptionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sa-footer">
                <div class="sa-footer-brand">
                    <i class="fas fa-crown"></i>
                    <span>&copy; <?= date('Y') ?> BismartTech. All rights reserved.</span>
                </div>
                <div class="sa-footer-links">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Support</a>
                    <a href="#">v1.0.0</a>
                </div>
            </footer>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Charts
        (function () {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
            Chart.defaults.color = '#64748B';

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                const ctx = revenueCtx.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(0, 126, 255, 0.35)');
                gradient.addColorStop(1, 'rgba(0, 126, 255, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                        datasets: [{
                            label: 'Revenue (TSh)',
                            data: [0, 0, 0, 0, 0, <?= (int)$monthly_revenue ?>],
                            borderColor: '#007EFF',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#007EFF',
                            pointBorderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0F172A',
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false,
                                callbacks: {
                                    label: (c) => 'TSh ' + Number(c.parsed.y).toLocaleString()
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, border: { display: false } },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F1F5F9' },
                                border: { display: false },
                                ticks: { callback: (v) => 'TSh ' + v.toLocaleString() }
                            }
                        }
                    }
                });
            }

            // Subscription Doughnut
            const subCtx = document.getElementById('subscriptionChart');
            if (subCtx) {
                new Chart(subCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Trial', 'Expired'],
                        datasets: [{
                            data: [
                                <?= (int)$active_count ?>,
                                <?= (int)$trial_count ?>,
                                <?= (int)$expired_count ?>
                            ],
                            backgroundColor: ['#007EFF', '#F59E0B', '#EF4444'],
                            borderColor: '#ffffff',
                            borderWidth: 4,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 14,
                                    font: { size: 12, weight: '600' },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    boxHeight: 8
                                }
                            }
                        }
                    }
                });
            }
        })();
    </script>
</body>
</html>