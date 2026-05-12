<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    setFlash('danger', 'User tidak ditemukan.');
    header('Location: /manajemen_karyawan/pages/users/index.php'); exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirm  = $_POST['konfirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';
    $status   = (int)($_POST['status'] ?? 1);

    if (!$nama)     $errors[] = 'Nama wajib diisi.';
    if (!$username) $errors[] = 'Username wajib diisi.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email tidak valid.';
    if ($password && strlen($password) < 6)
        $errors[] = 'Password minimal 6 karakter.';
    if ($password && $password !== $konfirm)
        $errors[] = 'Konfirmasi password tidak cocok.';

    $chk = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $chk->bind_param('ssi', $username, $email, $id); $chk->execute();
    if ($chk->get_result()->num_rows > 0)
        $errors[] = 'Username atau email sudah digunakan.';

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET nama=?,username=?,email=?,password=?,role=?,status=?,updated_at=? WHERE id=?");
            $stmt->bind_param('ssssisi', $nama,$username,$email,$hash,$role,$status,$now,$id);
        } else {
            $stmt = $db->prepare("UPDATE users SET nama=?,username=?,email=?,role=?,status=?,updated_at=? WHERE id=?");
            $stmt->bind_param('ssssisi', $nama, $username, $email, $role, $status, $now, $id);
        }
        if ($stmt->execute()) {
            setFlash('success', 'User berhasil diperbarui.');
            header('Location: /manajemen_karyawan/pages/users/index.php'); exit;
        }
        $errors[] = 'Gagal memperbarui data.';
    }
}

$f = $_SERVER['REQUEST_METHOD'] === 'POST' ? array_merge($user, $_POST) : $user;
$title = 'Edit User';
include __DIR__ . '/../layout/header.php';
?>

<div class="mb-3">
    <a href="/manajemen_karyawan/pages/users/index.php"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:620px">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square" style="color:var(--mid-blue)"></i>
        <span style="font-size:.9rem;font-weight:700;color:var(--navy)">
            Edit User — <?= e($user['nama']) ?>
        </span>
    </div>
    <div class="card-body p-4">

        <?php if ($errors): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= implode('<br>', array_map('e', $errors)) ?>
        </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Informasi Akun
                </span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="<?= e($f['nama']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?= e($f['username']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= e($f['email']) ?>">
                </div>
            </div>

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Keamanan
                </span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="konfirm_password" class="form-control"
                           placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Hak Akses
                </span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select">
                        <option value="staff" <?= $f['role']==='staff'?'selected':'' ?>>Staff</option>
                        <option value="admin" <?= $f['role']==='admin'?'selected':'' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= $f['status']==1?'selected':'' ?>>Aktif</option>
                        <option value="0" <?= $f['status']==0?'selected':'' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-check-lg me-1"></i>Perbarui
                    </button>
                    <a href="/manajemen_karyawan/pages/users/index.php"
                       class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>