<?php

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): string {
    if (empty($_SESSION['flash'])) return '';
    ['type' => $type, 'msg' => $msg] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                " . htmlspecialchars($msg) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES);
}

function uploadFoto(array $file, ?string $oldFoto = null): array {
    $maxSize = 300 * 1024;
    $allowedExt = ['jpg', 'jpeg'];
    $uploadDir  = dirname(__DIR__) . '/uploads/foto_pegawai/';

    if ($file['error'] !== UPLOAD_ERR_OK)
        return ['ok' => false, 'msg' => 'Upload gagal.'];

    if ($file['size'] > $maxSize)
        return ['ok' => false, 'msg' => 'Ukuran foto maksimal 300KB.'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt))
        return ['ok' => false, 'msg' => 'Format foto harus JPG/JPEG.'];

    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/jpg']))
        return ['ok' => false, 'msg' => 'File bukan JPEG yang valid.'];

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = 'foto_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename))
        return ['ok' => false, 'msg' => 'Gagal menyimpan foto.'];

    if ($oldFoto && file_exists($uploadDir . $oldFoto))
        unlink($uploadDir . $oldFoto);

    return ['ok' => true, 'filename' => $filename];
}

function fotoUrl(?string $filename): string {
    if ($filename && file_exists(dirname(__DIR__) . '/uploads/foto_pegawai/' . $filename))
        return '/manajemen_karyawan/uploads/foto_pegawai/' . $filename;
    return '/manajemen_karyawan/assets/img/default-avatar.png';
}