<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id === (int)currentUser()['id']) {
    setFlash('danger', 'Tidak dapat menghapus akun sendiri.');
    header('Location: /manajemen_karyawan/pages/users/index.php'); exit;
}

$del = $db->prepare("DELETE FROM users WHERE id = ?");
$del->bind_param('i', $id);
$del->execute()
    ? setFlash('success', 'User berhasil dihapus.')
    : setFlash('danger', 'Gagal menghapus user.');

header('Location: /manajemen_karyawan/pages/users/index.php'); exit;