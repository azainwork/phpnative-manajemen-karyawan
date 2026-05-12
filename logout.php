<?php

require_once __DIR__ . '/includes/auth.php';

$_SESSION = [];
session_destroy();
header('Location: /manajemen_karyawan/pages/auth/login.php');
exit;