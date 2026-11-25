<?php
// ================================================================
// FILE: includes/head.php
// Shared <head> — favicon, fonts, bootstrap, global CSS
// Inahitaji: $page_title
// ================================================================
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Dashboard') ?> — PharmacyPro</title>

<!-- FAVICON — inafanya kazi kwenye roles zote -->
<link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/logo.png">
<link rel="apple-touch-icon" href="<?= APP_URL ?>/assets/img/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">