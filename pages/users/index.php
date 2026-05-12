<?php
$title = 'Manajemen User';
include __DIR__ . '/../layout/header.php';
requireAdmin();

$db = getDB();
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where  = "WHERE 1=1";
$params = []; $types = '';

if ($search) {
    $where   .= " AND (nama LIKE ? OR username LIKE ? OR email LIKE ?)";
    $like     = "%$search%";
    $params   = [$like, $like, $like];
    $types   .= 'sss';
}

$cStmt = $db->prepare("SELECT COUNT(*) as total FROM users $where");
if ($params) $cStmt->bind_param($types, ...$params);
$cStmt->execute();
$total = $cStmt->get_result()->fetch_assoc()['total'];
$pages = (int)ceil($total / $limit);

$p = $params; $p[] = $limit; $p[] = $offset; $t = $types . 'ii';
$stmt = $db->prepare("SELECT * FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param($t, ...$p);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock" style="color:var(--mid-blue)"></i>
            <span style="font-size:.9rem;font-weight:700;color:var(--navy)">Manajemen User</span>
        </div>
        <a href="/manajemen_karyawan/pages/users/create.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah User
        </a>
    </div>

    <!-- Filter -->
    <div class="px-4 py-3 border-bottom" style="background:#f8fbff">
        <form class="d-flex gap-2" method="GET">
            <div class="search-shell">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       style="width:260px" placeholder="Cari nama, username, email..."
                       value="<?= e($search) ?>">
            </div>
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            <?php if ($search): ?>
            <a href="?" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x me-1"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2" style="color:#dbeafe"></i>
                        Tidak ada data user ditemukan
                    </td>
                </tr>
            <?php else: ?>
            <?php $no = $offset + 1; foreach ($users as $u): ?>
                <tr>
                    <td class="text-muted" style="font-size:.82rem"><?= $no++ ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;
                                        background:<?= $u['role']==='admin' ? '#e0e7ff' : '#f0f6ff' ?>;
                                        border:1.5px solid <?= $u['role']==='admin' ? '#c7d2fe' : '#dbeafe' ?>;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:.72rem;font-weight:700;flex-shrink:0;
                                        color:<?= $u['role']==='admin' ? '#3730a3' : 'var(--mid-blue)' ?>">
                                <?= strtoupper(substr($u['nama'], 0, 2)) ?>
                            </div>
                            <span style="font-size:.85rem;font-weight:600;color:var(--navy)">
                                <?= e($u['nama']) ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <code style="font-size:.8rem;color:var(--mid-blue);background:#f0f6ff;padding:2px 8px;border-radius:5px">
                            <?= e($u['username']) ?>
                        </code>
                    </td>
                    <td style="font-size:.85rem"><?= e($u['email']) ?></td>
                    <td>
                        <span class="badge-<?= $u['role'] ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="<?= $u['status'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                            <?= $u['status'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:var(--mid-blue)">
                        <?= $u['created_at'] ? date('d M Y', strtotime($u['created_at'])) : '-' ?>
                    </td>
                    <td class="text-center">
                        <a href="/manajemen_karyawan/pages/users/edit.php?id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] !== (int)currentUser()['id']): ?>
                        <a href="/manajemen_karyawan/pages/users/delete.php?id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
        <small class="text-muted"><?= count($users) ?> dari <?= $total ?> data</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i===$page?'active':'' ?>">
                    <a class="page-link" href="?p=<?= $i ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>