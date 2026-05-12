<?php

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /manajemen_karyawan/pages/pegawai/index.php');
} else {
    header('Location: /manajemen_karyawan/pages/auth/login.php');
}
exit;