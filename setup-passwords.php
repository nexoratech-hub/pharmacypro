<?php
// ================================================================
// FILE: setup-passwords.php
// Run ONCE after importing database.sql — DELETE after use!
// ================================================================

require_once __DIR__ . '/core/db.php';

$defaultPassword = '12345678';
$hash = password_hash($defaultPassword, PASSWORD_BCRYPT);

// Update both users
DB::run(
    "UPDATE users SET password_hash = ? WHERE username IN ('BismartTech Admin','Admin1')",
    [$hash]
);

echo "<pre>";
echo "✅ Passwords updated successfully!\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "SUPER ADMIN (BismartTech)\n";
echo "  Username : BismartTech Admin\n";
echo "  Email    : admin@bismarttech.co.tz\n";
echo "  Password : {$defaultPassword}\n";
echo "  Login at : /super-admin/\n\n";
echo "PHARMACY ADMIN (Demo Pharmacy)\n";
echo "  Username : Admin1\n";
echo "  Email    : admin1@pharmacypro.co.tz\n";
echo "  Password : {$defaultPassword}\n";
echo "  Login at : /admin/\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "⚠️  DELETE THIS FILE NOW!\n";
echo "URL: " . APP_URL . "/setup-passwords.php\n";
echo "</pre>";