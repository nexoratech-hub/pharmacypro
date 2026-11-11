<?php
require_once __DIR__ . '/../core/auth.php';

Auth::logout();
redirect(APP_URL . '/auth/login.php?logged_out=1');