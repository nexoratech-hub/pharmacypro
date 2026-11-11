<?php
// ================================================================
// FILE: config.php
// ================================================================

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'pharmacypro');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App
define('APP_NAME', 'PharmacyPro');
define('APP_OWNER', 'BismartTech');
define('APP_URL', 'http://localhost/pharmacypro');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', true);

// Security
define('SESSION_NAME', 'pharmacypro_session');
define('SESSION_LIFETIME', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 1800);
define('PASSWORD_MIN_LENGTH', 8);

// Timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Errors
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}