<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$db     = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $departemen = trim($_POST['departemen'] ?? '');
    $tanggal_masuk = $_POST['tanggal_masuk'] ?? '';
    $status = $_POST['status'] ?? 'aktif';
    $foto = null;

    if (!$nip)  $errors[] = 'NIP wajib diisi.';
    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Format email tidak valid.';

    $chk = $db->prepare("SELECT id FROM pegawais WHERE nip = ?");
    $chk->bind_param('s', $nip); $chk->execute();
    if ($chk->get_result()->num_rows > 0) $errors[] = 'NIP sudah digunakan.';

    if (!empty($_FILES['foto']['name'])) {
        $upload = uploadFoto($_FILES['foto']);
        if (!$upload['ok']) $errors[] = $upload['msg'];
        else $foto = $upload['filename'];
    }

    if (empty($errors)) {
        $now  = date('Y-m-d H:i:s');
        $stmt = $db->prepare("INSERT INTO pegawais (nip,nama,email,telepon,jabatan,departemen,tanggal_masuk,foto,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssssss', $nip,$nama,$email,$telepon,$jabatan,$departemen,$tanggal_masuk,$foto,$status,$now,$now);
        if ($stmt->execute()) {
            setFlash('success', 'Pegawai berhasil ditambahkan.');
            header('Location: /manajemen_karyawan/pages/pegawai/index.php'); exit;
        }
        $errors[] = 'Gagal menyimpan data.';
    }
}

$title = 'Tambah User';
include __DIR__ . '/../layout/header.php';
?>

<div class="mb-3">
    <a href="/manajemen_karyawan/pages/pegawai/index.php"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:700px">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-person-plus" style="color:var(--mid-blue)"></i>
        <span style="font-size:.9rem;font-weight:700;color:var(--navy)">Tambah Pegawai</span>
    </div>
    <div class="card-body p-4">

        <?php if ($errors): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= implode('<br>', array_map('e', $errors)) ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="text-center mb-4">
                <img id="previewFoto"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='110' height='110' viewBox='0 0 110 110'%3E%3Ccircle cx='55' cy='55' r='55' fill='%23f0f6ff'/%3E%3Ccircle cx='55' cy='42' r='18' fill='%23dbeafe'/%3E%3Cellipse cx='55' cy='85' rx='28' ry='20' fill='%23dbeafe'/%3E%3C/svg%3E"
                     alt="Preview Foto">
                <div class="mt-2">
                    <label for="foto" class="btn btn-sm btn-outline-primary" style="cursor:pointer">
                        <i class="bi bi-camera me-1"></i>Upload Foto
                    </label>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg" class="d-none">
                    <div class="text-muted mt-1" style="font-size:.72rem">
                        Format JPG/JPEG · Maks 300KB
                    </div>
                </div>
            </div>

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Informasi Dasar
                </span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <input type="text" name="nip" class="form-control"
                           value="<?= e($_POST['nip'] ?? '') ?>" placeholder="Nomor Induk Pegawai">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama lengkap">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= e($_POST['email'] ?? '') ?>" placeholder="email@contoh.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control"
                           value="<?= e($_POST['telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Informasi Pekerjaan
                </span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control"
                           value="<?= e($_POST['jabatan'] ?? '') ?>" placeholder="Jabatan / posisi">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Departemen</label>
                    <input type="text" name="departemen" class="form-control"
                           value="<?= e($_POST['departemen'] ?? '') ?>" placeholder="Nama departemen">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control"
                           value="<?= e($_POST['tanggal_masuk'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="aktif"    <?= ($_POST['status']??'aktif')==='aktif'   ?'selected':'' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($_POST['status']??'')==='nonaktif'?'selected':'' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                    <a href="/manajemen_karyawan/pages/pegawai/index.php"
                       class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>