<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT foto FROM pegawais WHERE id = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$pegawai = $stmt->get_result()->fetch_assoc();

if ($pegawai) {
    if ($pegawai['foto']) {
        $fotoPath = dirname(__DIR__, 2) . '/uploads/foto_pegawai/' . $pegawai['foto'];
        if (file_exists($fotoPath)) unlink($fotoPath);
    }
    $del = $db->prepare("DELETE FROM pegawais WHERE id = ?");
    $del->bind_param('i', $id);
    $del->execute()
        ? setFlash('success', 'Pegawai berhasil dihapus.')
        : setFlash('danger', 'Gagal menghapus data.');
} else {
    setFlash('danger', 'Data tidak ditemukan.');
}

header('Location: /manajemen_karyawan/pages/pegawai/index.php'); exit;