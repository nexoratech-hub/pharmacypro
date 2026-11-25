<?php
// ================================================================
// FILE: super-admin/pharmacy-add.php
// Add Pharmacy — Full form with validation (fixed for subscription_plans)
// ================================================================

require_once __DIR__ . '/../core/guard.php';

$current_user = require_role('super_admin');
$page_title   = 'Add Pharmacy';
$super_admin_name = $current_user['full_name'] ?? 'Super Admin';

$errors = [];
$old = [
    'pharmacy_name' => '',
    'owner_name'    => '',
    'email'         => '',
    'phone'         => '',
    'alt_phone'     => '',
    'whatsapp'      => '',
    'website'       => '',
    'region'        => '',
    'district'      => '',
    'address'       => '',
    'plan_id'       => '',
];

// ── Fetch plans — columns halisi kutoka subscription_plans ──
$plans = DB::all("
    SELECT id, plan_name, plan_code, billing_cycle, price, trial_days,
           max_employees, max_products, max_branches
    FROM subscription_plans
    WHERE is_active = 1
    ORDER BY price ASC
");

// ── Handle POST ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Security token invalid. Please try again.';
    } else {
        // Sanitize
        $old['pharmacy_name'] = trim(post('pharmacy_name', ''));
        $old['owner_name']    = trim(post('owner_name', ''));
        $old['email']         = trim(post('email', ''));
        $old['phone']         = trim(post('phone', ''));
        $old['alt_phone']     = trim(post('alt_phone', ''));
        $old['whatsapp']      = trim(post('whatsapp', ''));
        $old['website']       = trim(post('website', ''));
        $old['region']        = trim(post('region', ''));
        $old['district']      = trim(post('district', ''));
        $old['address']       = trim(post('address', ''));
        $old['plan_id']       = (int) post('plan_id', 0);

        // Validation
        if ($old['pharmacy_name'] === '') {
            $errors[] = 'Pharmacy name is required.';
        }
        if ($old['owner_name'] === '') {
            $errors[] = 'Owner name is required.';
        }
        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if ($old['phone'] === '') {
            $errors[] = 'Phone number is required.';
        }
        if ($old['region'] === '') {
            $errors[] = 'Region is required.';
        }
        if ($old['district'] === '') {
            $errors[] = 'District is required.';
        }

        // Duplicate check
        if (empty($errors)) {
            $exists = DB::one("SELECT id FROM pharmacies WHERE email = ?", [$old['email']]);
            if ($exists) {
                $errors[] = 'Email is already registered to another pharmacy.';
            }
        }

        // Insert
        if (empty($errors)) {
            try {
                // Generate pharmacy code
                $count = (int) (DB::one("SELECT COUNT(*) AS c FROM pharmacies")['c'] ?? 0);
                $code  = 'PHARM-' . str_pad((string)($count + 1), 3, '0', STR_PAD_LEFT);

                // Trial ends in 14 days
                $trial_ends = date('Y-m-d H:i:s', strtotime('+14 days'));

                $plan_id_val = $old['plan_id'] > 0 ? $old['plan_id'] : null;

                DB::run("
                    INSERT INTO pharmacies
                    (pharmacy_code, pharmacy_name, owner_name, email, phone, alt_phone, whatsapp, website,
                     region, district, address, status, trial_ends_at, subscription_plan_id,
                     created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'trial', ?, ?, NOW(), NOW())
                ", [
                    $code,
                    $old['pharmacy_name'],
                    $old['owner_name'],
                    $old['email'],
                    $old['phone'],
                    $old['alt_phone'] ?: null,
                    $old['whatsapp'] ?: null,
                    $old['website'] ?: null,
                    $old['region'],
                    $old['district'],
                    $old['address'] ?: null,
                    $trial_ends,
                    $plan_id_val,
                ]);

                $_SESSION['flash_success'] = "Pharmacy '{$old['pharmacy_name']}' added successfully (Code: {$code}).";
                redirect(APP_URL . '/super-admin/pharmacies.php');

            } catch (Exception $e) {
                error_log("Add pharmacy error: " . $e->getMessage());
                $errors[] = 'Failed to add pharmacy. Please try again.';
            }
        }
    }
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
           SELF-CONTAINED CSS — Blue #007EFF
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

        .sa-btn-ghost {
            background: #fff;
            color: var(--slate-700);
            border: 1px solid var(--slate-200);
        }

        .sa-btn-ghost:hover { border-color: var(--blue-300); background: var(--blue-50); color: var(--primary-dk); }

        /* ---- FORM CARD ---- */
        .sa-form-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .sa-form-header {
            padding: 1.25rem 1.4rem;
            background: #007EFF;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .sa-form-header-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .sa-form-header h3 { font-size: 1.05rem; font-weight: 700; color: #fff; margin: 0; }
        .sa-form-header p { font-size: 0.78rem; color: rgba(255, 255, 255, 0.9); margin: 2px 0 0; }

        .sa-form-body { padding: 1.5rem 1.4rem; }

        /* ---- FORM FIELDS ---- */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-field.full { grid-column: 1 / -1; }

        .form-field label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--slate-700);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .form-field label i {
            color: var(--primary);
            font-size: 0.75rem;
        }

        .form-field label .required {
            color: var(--red-500);
            font-weight: 700;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border-radius: 11px;
            border: 1px solid var(--slate-200);
            background: var(--slate-50);
            color: var(--slate-800);
            font-size: 0.88rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-textarea {
            height: auto;
            min-height: 90px;
            padding: 12px 14px;
            resize: vertical;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--slate-400);
        }

        .form-input:hover,
        .form-select:hover,
        .form-textarea:hover {
            border-color: var(--slate-300);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 126, 255, 0.12);
        }

        .form-help {
            font-size: 0.75rem;
            color: var(--slate-500);
            margin-top: 2px;
        }

        /* ---- PLAN CARDS ---- */
        .plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.75rem;
            margin-top: 0.25rem;
        }

        .plan-card {
            position: relative;
            cursor: pointer;
            border-radius: 12px;
            border: 2px solid var(--slate-200);
            background: #fff;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s ease;
        }

        .plan-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 126, 255, 0.1);
        }

        .plan-card.selected {
            border-color: var(--primary);
            background: var(--blue-50);
            box-shadow: 0 0 0 3px rgba(0, 126, 255, 0.15);
        }

        .plan-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .plan-card-icon {
            width: 40px; height: 40px;
            margin: 0 auto 0.5rem;
            border-radius: 10px;
            background: var(--blue-50);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .plan-card.selected .plan-card-icon {
            background: var(--primary);
            color: #fff;
        }

        .plan-card-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 0.25rem;
        }

        .plan-card-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.15rem;
        }

        .plan-card-sub {
            font-size: 0.72rem;
            color: var(--slate-500);
        }

        /* ---- FORM ACTIONS ---- */
        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1.25rem 1.4rem;
            border-top: 1px solid var(--slate-200);
            background: var(--slate-50);
        }

        /* ---- ALERTS ---- */
        .sa-alert { padding: 0.9rem 1.1rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: 0.9rem; font-weight: 500; display: flex; align-items: flex-start; gap: 0.7rem; line-height: 1.5; }
        .sa-alert.error { background: var(--red-50); color: var(--red-600); border-left: 4px solid var(--red-500); }
        .sa-alert ul { margin: 0; padding-left: 1.2rem; }
        .sa-alert li { margin-bottom: 0.25rem; }
        .sa-alert i { flex-shrink: 0; margin-top: 2px; }

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
        }

        @media (max-width: 767.98px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 575.98px) {
            .sa-content { padding: 1rem; }
            .sa-page-header h1 { font-size: 1.35rem; }
            .sa-page-header { flex-direction: column; align-items: flex-start; }
            .form-actions { flex-direction: column-reverse; }
            .form-actions .sa-btn { width: 100%; justify-content: center; }
            .plan-grid { grid-template-columns: repeat(2, 1fr); }
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
                    <h1><i class="fas fa-plus-circle"></i> Add Pharmacy</h1>
                    <p>Register a new pharmacy on the platform.</p>
                </div>
                <a href="<?= APP_URL ?>/super-admin/pharmacies.php" class="sa-btn sa-btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back to Pharmacies
                </a>
            </div>

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
                <div class="sa-alert error">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="post" novalidate>
                <?= csrf_field() ?>

                <div class="sa-form-card">
                    <div class="sa-form-header">
                        <div class="sa-form-header-icon"><i class="fas fa-hospital"></i></div>
                        <div>
                            <h3>Pharmacy Information</h3>
                            <p>Fill in the details below to register a new pharmacy.</p>
                        </div>
                    </div>

                    <div class="sa-form-body">
                        <div class="form-grid">

                            <!-- Pharmacy Name -->
                            <div class="form-field">
                                <label for="pharmacy_name">
                                    <i class="fas fa-store"></i>
                                    Pharmacy Name <span class="required">*</span>
                                </label>
                                <input type="text" id="pharmacy_name" name="pharmacy_name" class="form-input"
                                       placeholder="e.g., PharmacyPro Demo"
                                       value="<?= e($old['pharmacy_name']) ?>"
                                       required autofocus>
                            </div>

                            <!-- Owner Name -->
                            <div class="form-field">
                                <label for="owner_name">
                                    <i class="fas fa-user-tie"></i>
                                    Owner Name <span class="required">*</span>
                                </label>
                                <input type="text" id="owner_name" name="owner_name" class="form-input"
                                       placeholder="e.g., John Doe"
                                       value="<?= e($old['owner_name']) ?>"
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="form-field">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email <span class="required">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="form-input"
                                       placeholder="e.g., owner@pharmacy.co.tz"
                                       value="<?= e($old['email']) ?>"
                                       required>
                            </div>

                            <!-- Phone -->
                            <div class="form-field">
                                <label for="phone">
                                    <i class="fas fa-phone"></i>
                                    Phone <span class="required">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" class="form-input"
                                       placeholder="e.g., +255712345678"
                                       value="<?= e($old['phone']) ?>"
                                       required>
                            </div>

                            <!-- Alt Phone -->
                            <div class="form-field">
                                <label for="alt_phone">
                                    <i class="fas fa-phone-volume"></i>
                                    Alternative Phone
                                </label>
                                <input type="tel" id="alt_phone" name="alt_phone" class="form-input"
                                       placeholder="Optional"
                                       value="<?= e($old['alt_phone']) ?>">
                            </div>

                            <!-- WhatsApp -->
                            <div class="form-field">
                                <label for="whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    WhatsApp
                                </label>
                                <input type="tel" id="whatsapp" name="whatsapp" class="form-input"
                                       placeholder="Optional"
                                       value="<?= e($old['whatsapp']) ?>">
                            </div>

                            <!-- Website -->
                            <div class="form-field">
                                <label for="website">
                                    <i class="fas fa-globe"></i>
                                    Website
                                </label>
                                <input type="url" id="website" name="website" class="form-input"
                                       placeholder="e.g., https://pharmacy.co.tz"
                                       value="<?= e($old['website']) ?>">
                            </div>

                            <!-- Region -->
                            <div class="form-field">
                                <label for="region">
                                    <i class="fas fa-map"></i>
                                    Region <span class="required">*</span>
                                </label>
                                <input type="text" id="region" name="region" class="form-input"
                                       placeholder="e.g., Dar es Salaam"
                                       value="<?= e($old['region']) ?>"
                                       required>
                            </div>

                            <!-- District -->
                            <div class="form-field">
                                <label for="district">
                                    <i class="fas fa-location-dot"></i>
                                    District <span class="required">*</span>
                                </label>
                                <input type="text" id="district" name="district" class="form-input"
                                       placeholder="e.g., Kinondoni"
                                       value="<?= e($old['district']) ?>"
                                       required>
                            </div>

                            <!-- Address -->
                            <div class="form-field full">
                                <label for="address">
                                    <i class="fas fa-map-pin"></i>
                                    Physical Address
                                </label>
                                <textarea id="address" name="address" class="form-textarea"
                                          placeholder="e.g., Mikocheni, Dar es Salaam"><?= e($old['address']) ?></textarea>
                            </div>

                            <!-- Subscription Plan — Plan Cards -->
                            <div class="form-field full">
                                <label>
                                    <i class="fas fa-box-open"></i>
                                    Subscription Plan
                                </label>

                                <div class="plan-grid">
                                    <!-- Trial option -->
                                    <label class="plan-card <?= empty($old['plan_id']) ? 'selected' : '' ?>">
                                        <input type="radio" name="plan_id" value=""
                                               <?= empty($old['plan_id']) ? 'checked' : '' ?>>
                                        <div class="plan-card-body">
                                            <div class="plan-card-icon"><i class="fas fa-gift"></i></div>
                                            <div class="plan-card-title">Trial</div>
                                            <div class="plan-card-price">Free</div>
                                            <div class="plan-card-sub">14 days</div>
                                        </div>
                                    </label>

                                    <?php foreach ($plans as $plan): ?>
                                        <label class="plan-card <?= $old['plan_id'] == $plan['id'] ? 'selected' : '' ?>">
                                            <input type="radio" name="plan_id" value="<?= (int)$plan['id'] ?>"
                                                   <?= $old['plan_id'] == $plan['id'] ? 'checked' : '' ?>>
                                            <div class="plan-card-body">
                                                <div class="plan-card-icon"><i class="fas fa-box-open"></i></div>
                                                <div class="plan-card-title"><?= e($plan['plan_name']) ?></div>
                                                <div class="plan-card-price">TSh <?= number_format($plan['price']) ?></div>
                                                <div class="plan-card-sub">/ <?= ucfirst($plan['billing_cycle']) ?></div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <span class="form-help">Leave empty to start with a 14-day free trial.</span>
                            </div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= APP_URL ?>/super-admin/pharmacies.php" class="sa-btn sa-btn-ghost">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="sa-btn sa-btn-primary">
                            <i class="fas fa-save"></i> Save Pharmacy
                        </button>
                    </div>
                </div>

            </form>

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

    <script>
        // Plan cards — selection
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            });
        });
    </script>

</body>
</html>