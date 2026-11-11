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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
</head>
<body class="login-page">

    <div class="bg-decoration">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>
    </div>

    <div class="login-container">

        <!-- Left: Branding -->
        <div class="login-brand-panel">
            <div class="brand-content">
                <div class="brand-logo">
                    <div class="brand-logo-icon">
                        <i class="fas fa-prescription-bottle-medical"></i>
                    </div>
                </div>
                <h1 class="brand-title">PharmacyPro</h1>
                <p class="brand-tagline">Smart Pharmacy. Smart Business.</p>

                <div class="brand-features">
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

        <!-- Right: Form -->
        <div class="login-form-panel">
            <div class="form-header">
                <h2>Welcome Back</h2>
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

            <form method="post" autocomplete="on" novalidate>
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
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span class="checkbox-mark"></span>
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link" onclick="alert('Please contact your pharmacy administrator to reset your password.'); return false;">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
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

    <script>
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
    </script>

</body>
</html>