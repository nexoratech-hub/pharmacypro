<?php
// ================================================================
// FILE: super-admin/pharmacies.php
// Final version — Blue #007EFF, Orange edit, Red delete
// ================================================================

require_once __DIR__ . '/../core/guard.php';

$current_user = require_role('super_admin');
$page_title   = 'Pharmacies';
$super_admin_name = $current_user['full_name'] ?? 'Super Admin';

// ── Query halisi ───────────────────────────────────────────
$pharmacies = DB::all("
    SELECT 
        p.*,
        sp.plan_name
    FROM pharmacies p
    LEFT JOIN subscription_plans sp ON sp.id = p.subscription_plan_id
    ORDER BY p.created_at DESC
");

$total_pharmacies = count($pharmacies);

$trial_count = $active_count = $expired_count = 0;
foreach ($pharmacies as $p) {
    if ($p['status'] === 'trial')   $trial_count++;
    if ($p['status'] === 'active')  $active_count++;
    if ($p['status'] === 'expired') $expired_count++;
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
           SELF-CONTAINED CSS
           ============================================================ */

        :root {
            --primary:    #007EFF;
            --primary-dk: #0066CC;
            --primary-lt: #40C4FF;

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

        /* ---- HEADER ---- */
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
        .sa-main {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
        }

        .sa-content { padding: 1.75rem; max-width: 1600px; margin: 0 auto; }

        /* ---- PAGE HEADER ---- */
        .sa-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .sa-page-header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--slate-800);
            letter-spacing: -0.03em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.3rem;
        }

        .sa-page-header h1 i { color: var(--primary); }
        .sa-page-header p { color: var(--slate-500); font-size: 0.875rem; }

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

        /* ---- MINI STATS ---- */
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
        }

        .sa-mini-icon.blue  { background: rgba(0, 126, 255, 0.1);  color: #007EFF; }
        .sa-mini-icon.green { background: var(--green-50); color: var(--green-600); }
        .sa-mini-icon.amber { background: var(--amber-50); color: var(--amber-600); }
        .sa-mini-icon.red   { background: var(--red-50);   color: var(--red-600); }

        .sa-mini-value { font-size: 1.35rem; font-weight: 800; color: var(--slate-800); line-height: 1.1; }
        .sa-mini-label { font-size: 0.75rem; color: var(--slate-500); font-weight: 500; }

        /* ---- CARD ---- */
        .sa-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        /* ---- TABLE HEADER — #007EFF ---- */
        .sa-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1.25rem 1.4rem;
            background: #007EFF;
            color: #fff;
        }

        .sa-table-title { display: flex; align-items: center; gap: 0.85rem; }

        .sa-table-title-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .sa-table-title h3 { font-size: 1.05rem; font-weight: 700; color: #fff; }
        .sa-table-title p { font-size: 0.78rem; color: rgba(255, 255, 255, 0.9); margin-top: 2px; }

        /* Search */
        .sa-search { position: relative; width: 320px; max-width: 100%; }

        .sa-search-input {
            width: 100%;
            height: 44px;
            padding: 0 44px 0 44px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: #fff;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .sa-search-input::placeholder { color: rgba(255, 255, 255, 0.85); }
        .sa-search-input:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: #fff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .sa-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .sa-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px; height: 30px;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.15s ease;
        }

        .sa-search-clear:hover { background: rgba(255, 255, 255, 0.4); }

        /* ---- TABLE ---- */
        .sa-table-wrap { overflow-x: auto; }

        .sa-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }

        .sa-table thead { background: var(--slate-50); }
        .sa-table th {
            text-align: left;
            padding: 0.85rem 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--slate-500);
            border-bottom: 1px solid var(--slate-200);
        }

        .sa-table td { padding: 1rem; border-bottom: 1px solid var(--slate-100); vertical-align: middle; }

        .sa-table tbody tr { transition: all 0.2s ease; }
        .sa-table tbody tr:hover { background: rgba(0, 126, 255, 0.05); }

        /* Cells */
        .sa-pharmacy-cell { display: flex; align-items: center; gap: 0.75rem; }

        .sa-pharmacy-avatar {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: #007EFF;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0, 126, 255, 0.35);
        }

        .sa-pharmacy-info strong { display: block; color: var(--slate-800); font-weight: 700; font-size: 0.9rem; }
        .sa-pharmacy-info small { color: var(--slate-500); font-size: 0.73rem; font-weight: 600; font-family: 'Courier New', monospace; }

        .sa-contact-cell, .sa-location-cell { display: flex; flex-direction: column; gap: 0.3rem; font-size: 0.82rem; color: var(--slate-600); }
        .sa-contact-cell > div, .sa-location-cell > div { display: flex; align-items: center; gap: 0.4rem; }
        .sa-contact-cell i, .sa-location-cell i { color: var(--slate-400); font-size: 0.72rem; width: 12px; }

        .sa-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.7rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .sa-status-dot { width: 6px; height: 6px; border-radius: 50%; }

        .sa-status-badge.green { background: var(--green-50); color: var(--green-700); border-color: var(--green-200); }
        .sa-status-badge.green .sa-status-dot { background: var(--green-500); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }

        .sa-status-badge.amber { background: var(--amber-50); color: var(--amber-700); border-color: #FDE68A; }
        .sa-status-badge.amber .sa-status-dot { background: var(--amber-500); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }

        .sa-status-badge.red { background: var(--red-50); color: var(--red-700); border-color: var(--red-200); }
        .sa-status-badge.red .sa-status-dot { background: var(--red-500); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2); }

        .sa-status-badge.gray { background: var(--slate-100); color: var(--slate-600); border-color: var(--slate-200); }
        .sa-status-badge.gray .sa-status-dot { background: var(--slate-400); }

        /* ---- ACTION BUTTONS ---- */
        .sa-actions {
            display: flex;
            gap: 0.4rem;
            align-items: center;
            justify-content: flex-end;
        }

        .sa-action-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.22s ease;
            overflow: visible;
        }

        .sa-action-btn i {
            position: relative;
            z-index: 2;
            transition: transform 0.22s ease;
        }

        .sa-action-btn::after {
            content: attr(title);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%) translateY(4px);
            background: var(--slate-800);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3rem 0.55rem;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 10;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }

        .sa-action-btn:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* VIEW — Blue #007EFF */
        .sa-action-btn.view {
            background: #007EFF;
            color: #fff;
        }

        .sa-action-btn.view:hover {
            background: #0066CC;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px -4px rgba(0, 126, 255, 0.5);
        }

        .sa-action-btn.view:hover i { transform: scale(1.15); }

        .sa-action-btn.view:active {
            transform: translateY(-1px);
            background: #0052A3;
        }

        /* EDIT — Orange #F59E0B */
        .sa-action-btn.edit {
            background: #F59E0B;
            color: #fff;
        }

        .sa-action-btn.edit:hover {
            background: #D97706;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px -4px rgba(245, 158, 11, 0.55);
        }

        .sa-action-btn.edit:hover i { transform: scale(1.15) rotate(-8deg); }

        .sa-action-btn.edit:active {
            transform: translateY(-1px);
            background: #B45309;
        }

        /* DELETE — Red #EF4444 */
        .sa-action-btn.delete {
            background: #EF4444;
            color: #fff;
        }

        .sa-action-btn.delete:hover {
            background: #DC2626;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px -4px rgba(239, 68, 68, 0.55);
        }

        .sa-action-btn.delete:hover i {
            transform: scale(1.15);
            animation: shakeDelete 0.4s ease;
        }

        .sa-action-btn.delete:active {
            transform: translateY(-1px);
            background: #B91C1C;
        }

        @keyframes shakeDelete {
            0%, 100% { transform: scale(1.15) translateX(0); }
            25%      { transform: scale(1.15) translateX(-2px); }
            75%      { transform: scale(1.15) translateX(2px); }
        }

        /* ---- SEARCH HIGHLIGHT ---- */
        mark.sa-highlight {
            background: #FEF08A;
            color: #854D0E;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(245, 158, 11, 0.3);
        }

        /* Empty state */
        .sa-empty { text-align: center; padding: 3rem 1rem; }
        .sa-empty-icon {
            width: 72px; height: 72px;
            margin: 0 auto 1rem;
            border-radius: 20px;
            background: rgba(0, 126, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: #007EFF;
            border: 1px solid rgba(0, 126, 255, 0.25);
        }

        .sa-empty h4 { font-size: 1.05rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.35rem; }
        .sa-empty p { font-size: 0.875rem; color: var(--slate-500); margin-bottom: 1.25rem; }

        /* No results */
        .sa-no-results { padding: 3rem 1rem; text-align: center; }
        .sa-no-results-icon {
            width: 64px; height: 64px;
            margin: 0 auto 1rem;
            border-radius: 18px;
            background: rgba(0, 126, 255, 0.1);
            color: #007EFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 1px solid rgba(0, 126, 255, 0.25);
        }
        .sa-no-results h4 { font-size: 1rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.3rem; }
        .sa-no-results p { font-size: 0.85rem; color: var(--slate-500); }

        /* Alerts */
        .sa-alert { padding: 0.9rem 1.1rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.7rem; }
        .sa-alert.success { background: var(--green-50); color: var(--green-700); border-left: 4px solid var(--green-500); }
        .sa-alert.error { background: var(--red-50); color: var(--red-600); border-left: 4px solid var(--red-500); }

        /* Footer */
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
        }
        .sa-footer-brand { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--slate-800); }
        .sa-footer-brand i { color: #007EFF; }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sa-sidebar { display: none; }
            .sa-header { left: 0; }
            .sa-main { margin-left: 0; }
            .sa-stats-mini { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 767.98px) {
            .sa-action-btn { width: 34px; height: 34px; font-size: 0.78rem; }
            .sa-action-btn::after { display: none; }
        }

        @media (max-width: 575.98px) {
            .sa-content { padding: 1rem; }
            .sa-page-header h1 { font-size: 1.35rem; }
            .sa-search { width: 100%; }
            .sa-table-header-right { width: 100%; }
            .sa-stats-mini { grid-template-columns: 1fr; }
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
                <div>
                    <h1><i class="fas fa-hospital"></i> Pharmacies</h1>
                    <p>Manage all pharmacies registered on the platform.</p>
                </div>
                <a href="<?= APP_URL ?>/super-admin/pharmacy-add.php" class="sa-btn sa-btn-primary">
                    <i class="fas fa-plus"></i> Add Pharmacy
                </a>
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
                        <div class="sa-mini-label">Total</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon green"><i class="fas fa-circle-check"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($active_count) ?></div>
                        <div class="sa-mini-label">Active</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon amber"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($trial_count) ?></div>
                        <div class="sa-mini-label">Trial</div>
                    </div>
                </div>
                <div class="sa-mini-card">
                    <div class="sa-mini-icon red"><i class="fas fa-circle-xmark"></i></div>
                    <div>
                        <div class="sa-mini-value"><?= number_format($expired_count) ?></div>
                        <div class="sa-mini-label">Expired</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="sa-card">
                <div class="sa-table-header">
                    <div class="sa-table-title">
                        <div class="sa-table-title-icon"><i class="fas fa-hospital"></i></div>
                        <div>
                            <h3>All Pharmacies</h3>
                            <p><span id="visibleCount"><?= number_format($total_pharmacies) ?></span> of <?= number_format($total_pharmacies) ?> pharmacies</p>
                        </div>
                    </div>
                    <div class="sa-table-header-right">
                        <div class="sa-search">
                            <i class="fas fa-search sa-search-icon"></i>
                            <input type="text" id="pharmacySearch" class="sa-search-input" placeholder="Search pharmacies..." autocomplete="off">
                            <button type="button" class="sa-search-clear" id="searchClear" style="display:none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (empty($pharmacies)): ?>
                    <div class="sa-empty">
                        <div class="sa-empty-icon"><i class="fas fa-hospital"></i></div>
                        <h4>No pharmacies yet</h4>
                        <p>Start by adding your first pharmacy to the platform.</p>
                        <a href="<?= APP_URL ?>/super-admin/pharmacy-add.php" class="sa-btn sa-btn-primary">
                            <i class="fas fa-plus"></i> Add First Pharmacy
                        </a>
                    </div>
                <?php else: ?>
                    <div class="sa-table-wrap">
                        <table class="sa-table" id="pharmaciesTable">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Pharmacy</th>
                                    <th>Owner</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th style="width:140px;text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pharmaciesBody">
                                <?php foreach ($pharmacies as $i => $p): 
                                    $status = $p['status'] ?? 'trial';
                                    $status_class = [
                                        'active'    => 'green',
                                        'trial'     => 'amber',
                                        'expired'   => 'red',
                                        'suspended' => 'gray',
                                    ][$status] ?? 'gray';
                                    
                                    $search_text = strtolower(
                                        $p['pharmacy_name'] . ' ' .
                                        $p['pharmacy_code'] . ' ' .
                                        $p['owner_name'] . ' ' .
                                        $p['email'] . ' ' .
                                        $p['phone'] . ' ' .
                                        ($p['district'] ?? '') . ' ' .
                                        ($p['region'] ?? '') . ' ' .
                                        ($p['plan_name'] ?? '') . ' ' .
                                        $status
                                    );
                                ?>
                                    <tr data-search="<?= e($search_text) ?>">
                                        <td style="color:var(--slate-400);font-weight:700;font-size:0.8rem;text-align:center;"><?= $i + 1 ?></td>
                                        <td>
                                            <div class="sa-pharmacy-cell">
                                                <div class="sa-pharmacy-avatar"><?= strtoupper(substr($p['pharmacy_name'], 0, 1)) ?></div>
                                                <div class="sa-pharmacy-info">
                                                    <strong class="sa-searchable"><?= e($p['pharmacy_name']) ?></strong>
                                                    <small class="sa-searchable"><?= e($p['pharmacy_code']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="sa-searchable"><?= e($p['owner_name']) ?></span></td>
                                        <td>
                                            <div class="sa-contact-cell">
                                                <div><i class="fas fa-envelope"></i> <span class="sa-searchable"><?= e($p['email']) ?></span></div>
                                                <div><i class="fas fa-phone"></i> <span class="sa-searchable"><?= e($p['phone']) ?></span></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="sa-location-cell">
                                                <div><i class="fas fa-location-dot"></i> <span class="sa-searchable"><?= e($p['district']) ?></span></div>
                                                <div><i class="fas fa-map"></i> <span class="sa-searchable"><?= e($p['region']) ?></span></div>
                                            </div>
                                        </td>
                                        <td><span class="sa-searchable"><?= e($p['plan_name'] ?? '—') ?></span></td>
                                        <td>
                                            <span class="sa-status-badge <?= $status_class ?>">
                                                <span class="sa-status-dot"></span>
                                                <?= e(ucfirst($status)) ?>
                                            </span>
                                        </td>
                                        <td style="font-size:0.82rem;color:var(--slate-600);"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                                        <td>
                                            <div class="sa-actions">
                                                <a href="<?= APP_URL ?>/super-admin/pharmacy-view.php?id=<?= (int)$p['id'] ?>"
                                                   class="sa-action-btn view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= APP_URL ?>/super-admin/pharmacy-edit.php?id=<?= (int)$p['id'] ?>"
                                                   class="sa-action-btn edit" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <button type="button"
                                                        class="sa-action-btn delete"
                                                        title="Delete"
                                                        onclick="confirmDelete(<?= (int)$p['id'] ?>, '<?= e(addslashes($p['pharmacy_name'])) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="sa-no-results" id="noResults" style="display:none;">
                            <div class="sa-no-results-icon"><i class="fas fa-search"></i></div>
                            <h4>No pharmacies found</h4>
                            <p>Try a different search term.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <footer class="sa-footer">
                <div class="sa-footer-brand">
                    <i class="fas fa-crown"></i>
                    <span>&copy; <?= date('Y') ?> BismartTech. All rights reserved.</span>
                </div>
                <div style="display:flex;gap:1.25rem;">
                    <a href="#" style="color:var(--slate-500);">Privacy</a>
                    <a href="#" style="color:var(--slate-500);">Terms</a>
                    <a href="#" style="color:var(--slate-500);">Support</a>
                </div>
            </footer>

        </div>
    </main>

    <!-- Hidden delete form -->
    <form id="deleteForm" method="post" action="<?= APP_URL ?>/super-admin/pharmacy-delete.php" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        // ============================================================
        // LIVE SEARCH WITH HIGHLIGHT
        // ============================================================
        (function () {
            const input      = document.getElementById('pharmacySearch');
            const clearBtn   = document.getElementById('searchClear');
            const tbody      = document.getElementById('pharmaciesBody');
            const noResults  = document.getElementById('noResults');
            const visibleCnt = document.getElementById('visibleCount');
            const table      = document.getElementById('pharmaciesTable');

            if (!input || !tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));

            tbody.querySelectorAll('.sa-searchable').forEach(span => {
                span.dataset.original = span.textContent;
            });

            function escapeRegex(str) {
                return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function highlight(text, query) {
                if (!query) return escapeHtml(text);
                const safe = escapeHtml(text);
                const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
                return safe.replace(regex, '<mark class="sa-highlight">$1</mark>');
            }

            function filterRows() {
                const q = input.value.toLowerCase().trim();
                let visible = 0;

                rows.forEach(row => {
                    const haystack = row.getAttribute('data-search') || '';
                    const match = q === '' || haystack.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;

                    row.querySelectorAll('.sa-searchable').forEach(span => {
                        const original = span.dataset.original || '';
                        span.innerHTML = highlight(original, q);
                    });
                });

                visibleCnt.textContent = visible;
                clearBtn.style.display = q.length > 0 ? 'flex' : 'none';

                if (visible === 0) {
                    noResults.style.display = 'block';
                    table.style.display = 'none';
                } else {
                    noResults.style.display = 'none';
                    table.style.display = '';
                }
            }

            input.addEventListener('input', filterRows);
            clearBtn.addEventListener('click', () => { input.value = ''; input.focus(); filterRows(); });
            input.addEventListener('keydown', (e) => { if (e.key === 'Escape') { input.value = ''; filterRows(); } });
        })();

        // ============================================================
        // DELETE CONFIRM
        // ============================================================
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete "' + name + '"?\n\nThis action cannot be undone.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>