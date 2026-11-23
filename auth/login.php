<?php
// ================================================================
// FILE: auth/login.php
// ================================================================

require_once __DIR__ . '/../core/auth.php';

if (!empty($_SESSION['user_id'])) {
    redirect(APP_URL . '/index.php');
}

$error = '';
$identifier_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $identifier_value = post('identifier', '');
        $password = post('password', '');
        $result = Auth::login((string)$identifier_value, (string)$password);

        if ($result['ok']) {
            redirect($result['redirect']);
        }
        $error = $result['message'];
    }
}

$timeout = !empty($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — PharmacyPro</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --secondary: #0ea5e9;
            --dark: #0f172a;
            --dark-2: #1e293b;
            --text-muted: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body.login-page {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #064e3b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* ============ BACKGROUND DECORATION ============ */
        .bg-decoration {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            animation: floatCircle 20s infinite ease-in-out;
        }

        .bg-circle-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #10b981, transparent);
            top: -150px; left: -150px;
            animation-delay: 0s;
        }

        .bg-circle-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #0ea5e9, transparent);
            bottom: -120px; right: -120px;
            animation-delay: 5s;
        }

        .bg-circle-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #8b5cf6, transparent);
            top: 40%; left: 45%;
            animation-delay: 10s;
        }

        @keyframes floatCircle {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%      { transform: translate(40px, -40px) scale(1.1); }
            66%      { transform: translate(-30px, 30px) scale(0.95); }
        }

        /* ============ GRID PATTERN OVERLAY ============ */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ============ MAIN CONTAINER ============ */
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ============ LEFT BRAND PANEL ============ */
        .brand-panel {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #0f766e 100%);
            padding: 3rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 100%;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(16,185,129,0.3), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(14,165,233,0.2), transparent 50%);
            pointer-events: none;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.05), transparent, rgba(255,255,255,0.05), transparent);
            animation: rotate 30s linear infinite;
            pointer-events: none;
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .brand-logo-icon {
            width: 70px; height: 70px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.2);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(16,185,129,0.3); }
            50%      { transform: scale(1.05); box-shadow: 0 15px 40px rgba(16,185,129,0.5); }
        }

        .brand-title {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #fff 0%, #a7f3d0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-tagline {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            margin-bottom: 0.85rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(5px);
            border-color: rgba(16, 185, 129, 0.4);
        }

        .feature-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #10b981, #0ea5e9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 5px 15px rgba(16,185,129,0.3);
        }

        .feature-text h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
            color: #fff;
        }

        .feature-text p {
            font-size: 0.8rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.6);
        }

        .brand-footer {
            margin-top: auto;
            padding-top: 2rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* ============ RIGHT FORM PANEL ============ */
        .form-panel {
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        /* ============ ALERTS ============ */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.5rem;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%      { transform: translateX(-5px); }
            75%      { transform: translateX(5px); }
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-left: 4px solid var(--warning);
        }

        .alert i {
            font-size: 1.1rem;
        }

        /* ============ FORM FIELDS ============ */
        .form-field {
            margin-bottom: 1.5rem;
        }

        .form-field label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark-2);
            margin-bottom: 0.6rem;
        }

        .form-field label i {
            color: var(--primary);
            font-size: 0.8rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            transition: color 0.3s ease;
            pointer-events: none;
            z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            font-size: 0.95rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-family: inherit;
            color: var(--dark);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .form-control:focus ~ .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        /* Password toggle */
        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1rem;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        /* ============ OPTIONS ============ */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            border-color: var(--primary);
        }

        .form-check-label {
            font-size: 0.875rem;
            color: var(--dark-2);
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* ============ BUTTON ============ */
        .btn-login {
            width: 100%;
            padding: 0.95rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            letter-spacing: 0.01em;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.45);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .btn-login i, .btn-login span {
            position: relative;
            z-index: 1;
        }

        /* Ripple effect */
        .btn-login .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: rippleAnim 0.6s linear;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to { transform: scale(4); opacity: 0; }
        }

        /* ============ FOOTER ============ */
        .form-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .security-note, .support-note {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            padding: 0.5rem 0;
        }

        .security-note i {
            color: var(--primary);
        }

        .support-note i {
            color: var(--secondary);
        }

        .support-note strong {
            color: var(--dark-2);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991.98px) {
            .brand-panel {
                display: none;
            }
            .form-panel {
                padding: 2.5rem 2rem;
            }
            .login-card {
                max-width: 480px;
                margin: 0 auto;
            }
        }

        @media (max-width: 575.98px) {
            .form-panel {
                padding: 2rem 1.5rem;
            }
            .form-header h2 {
                font-size: 1.6rem;
            }
            .login-card {
                border-radius: 20px;
            }
            body.login-page {
                padding: 12px;
            }
        }

        /* ============ LOADER STATE ============ */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .btn-login.loading .btn-text::after {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-left: 8px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="login-page">

    <!-- Background decorations -->
    <div class="bg-decoration">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="row g-0">

                <!-- Left: Branding Panel -->
                <div class="col-lg-5">
                    <div class="brand-panel h-100">
                        <div class="brand-content">
                            <div class="brand-logo-icon">
                                <i class="fas fa-prescription-bottle-medical"></i>
                            </div>
                            <h1 class="brand-title">PharmacyPro</h1>
                            <p class="brand-tagline">Smart Pharmacy. Smart Business.</p>

                            <div class="brand-features mt-4">
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-cash-register"></i></div>
                                    <div class="feature-text">
                                        <h3>Fast POS</h3>
                                        <p>Sell products quickly</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-boxes-stacked"></i></div>
                                    <div class="feature-text">
                                        <h3>Inventory</h3>
                                        <p>Track stock & expiry</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                                    <div class="feature-text">
                                        <h3>Reports</h3>
                                        <p>Know your business</p>
                                    </div>
                                </div>
                            </div>

                            <div class="brand-footer">
                                <p>&copy; <?= date('Y') ?> BismartTech. All rights reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form Panel -->
                <div class="col-lg-7">
                    <div class="form-panel h-100">
                        <div class="form-header">
                            <h2>Welcome Back 👋</h2>
                            <p>Sign in to your account to continue</p>
                        </div>

                        <?php if ($timeout): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-clock"></i>
                                <span>Session expired. Please log in again.</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <span><?= e($error) ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="post" autocomplete="on" novalidate id="loginForm">
                            <?= csrf_field() ?>

                            <div class="form-field">
                                <label for="identifier">
                                    <i class="fas fa-user"></i>
                                    Username or Email
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input
                                        type="text"
                                        id="identifier"
                                        name="identifier"
                                        class="form-control"
                                        placeholder="Enter your username or email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        value="<?= e($identifier_value) ?>"
                                    >
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="password">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Enter your password"
                                        required
                                        autocomplete="current-password"
                                    >
                                    <button type="button" class="toggle-password" onclick="togglePassword()" title="Show/hide password">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="#" class="forgot-link"
                                   onclick="alert('Please contact your pharmacy administrator to reset your password.'); return false;">
                                    Forgot password?
                                </a>
                            </div>

                            <button type="submit" class="btn-login" id="loginBtn">
                                <i class="fas fa-sign-in-alt"></i>
                                <span class="btn-text">Sign In</span>
                            </button>
                        </form>

                        <div class="form-footer">
                            <div class="security-note">
                                <i class="fas fa-shield-halved"></i>
                                <span>Your data is protected with bank-level encryption</span>
                            </div>
                            <div class="support-note">
                                <i class="fas fa-headset"></i>
                                <span>Need help? Contact <strong>BismartTech Support</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Ripple effect on button
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);
            ripple.className = 'ripple';
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
            ripple.style.top  = (e.clientY - rect.top  - size/2) + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.querySelector('.btn-text').textContent = 'Signing in...';
        });
    </script>

</body>
</html>