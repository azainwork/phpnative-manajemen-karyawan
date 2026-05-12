<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirm = $_POST['konfirm_password'] ?? '';
    $role = $_POST['role'] ?? 'staff';
    $status = (int)($_POST['status'] ?? 1);

    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if (!$username) $errors[] = 'Username wajib diisi.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email tidak valid.';
    if (strlen($password) < 6)
        $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $konfirm)
        $errors[] = 'Konfirmasi password tidak cocok.';

    $chk = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $chk->bind_param('ss', $username, $email); $chk->execute();
    if ($chk->get_result()->num_rows > 0)
        $errors[] = 'Username atau email sudah digunakan.';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $now  = date('Y-m-d H:i:s');
        $stmt = $db->prepare("INSERT INTO users (nama,username,email,password,role,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssiss', $nama,$username,$email,$hash,$role,$status,$now,$now);
        if ($stmt->execute()) {
            setFlash('success', 'User berhasil ditambahkan.');
            header('Location: /manajemen_karyawan/pages/users/index.php'); exit;
        }
        $errors[] = 'Gagal menyimpan data.';
    }
}
$title = 'Tambah User';
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
        <i class="bi bi-person-plus" style="color:var(--mid-blue)"></i>
        <span style="font-size:.9rem;font-weight:700;color:var(--navy)">Tambah User</span>
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
                    <input type="text" name="nama" class="form-control"
                           value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama lengkap">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control"
                           value="<?= e($_POST['username'] ?? '') ?>" placeholder="Username unik">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= e($_POST['email'] ?? '') ?>" placeholder="email@contoh.com">
                </div>
            </div>

            <div class="mb-3 pb-1" style="border-bottom:1.5px solid #f0f6ff">
                <span style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--mid-blue)">
                    Keamanan
                </span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Min. 6 karakter">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="konfirm_password" class="form-control"
                           placeholder="Ulangi password">
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
                        <option value="staff" <?= ($_POST['role']??'staff')==='staff'?'selected':'' ?>>Staff</option>
                        <option value="admin" <?= ($_POST['role']??'')==='admin'?'selected':'' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($_POST['status']??'1')==='1'?'selected':'' ?>>Aktif</option>
                        <option value="0" <?= ($_POST['status']??'')==='0'?'selected':'' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                    <a href="/manajemen_karyawan/pages/users/index.php"
                       class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>