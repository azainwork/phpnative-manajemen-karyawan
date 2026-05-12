<?php

if(session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if(!isLoggedIn()) {
        header('Location: /manajemen_karyawan/index.php');
        exit;
    }
}

function isAdmin(): bool {
    return($_SESSION['user_role'] ?? '') === 'admin';
}

function requireAdmin(): void {
    requireLogin();
    if(!isAdmin()) {
        header('Location: /manajemen_karyawan/pages/pegawai/index.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nama' => $_SESSION['user_nama'] ?? '',
        'username' => $_SESSION['user_uname'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
    ];
}