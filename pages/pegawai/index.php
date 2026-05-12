<?php
$title = 'Manajemen Pegawai';
include __DIR__ . '/../layout/header.php';

$db = getDB();
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where  = "WHERE 1=1";
$params = []; $types = '';

if ($search) {
    $where   .= " AND (nama LIKE ? OR nip LIKE ? OR jabatan LIKE ? OR departemen LIKE ?)";
    $like     = "%$search%";
    $params   = [$like, $like, $like, $like];
    $types   .= 'ssss';
}
if ($status) {
    $where   .= " AND status = ?";
    $params[] = $status; $types .= 's';
}

$cStmt = $db->prepare("SELECT COUNT(*) as total FROM pegawais $where");
if ($params) $cStmt->bind_param($types, ...$params);
$cStmt->execute();
$total = $cStmt->get_result()->fetch_assoc()['total'];
$pages = (int)ceil($total / $limit);

$p = $params; $p[] = $limit; $p[] = $offset; $t = $types . 'ii';
$stmt = $db->prepare("SELECT * FROM pegawais $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param($t, ...$p);
$stmt->execute();
$pegawais = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people" style="color:var(--mid-blue)"></i>
            <span style="font-size:.9rem;font-weight:700;color:var(--navy)">Manajemen Pegawai</span>
        </div>
        <a href="/manajemen_karyawan/pages/pegawai/create.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pegawai
        </a>
    </div>

    <!-- Filter -->
    <div class="px-4 py-3 border-bottom" style="background:#f8fbff">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <div class="search-shell">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       style="width:240px" placeholder="Cari nama, NIP, jabatan..."
                       value="<?= e($search) ?>">
            </div>
            <select name="status" class="form-select form-select-sm" style="width:130px">
                <option value="">Semua Status</option>
                <option value="aktif"    <?= $status==='aktif'   ?'selected':'' ?>>Aktif</option>
                <option value="nonaktif" <?= $status==='nonaktif'?'selected':'' ?>>Nonaktif</option>
            </select>
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            <?php if ($search || $status): ?>
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
                    <th>Foto</th>
                    <th>Nama / NIP</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Tgl Masuk</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pegawais)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2" style="color:#dbeafe"></i>
                        Tidak ada data pegawai ditemukan
                    </td>
                </tr>
            <?php else: ?>
            <?php $no = $offset + 1; foreach ($pegawais as $p): ?>
                <tr>
                    <td class="text-muted" style="font-size:.82rem"><?= $no++ ?></td>
                    <td>
                        <?php
                        $fotoPath = dirname(__DIR__, 2) . '/uploads/foto_pegawai/' . $p['foto'];
                        if ($p['foto'] && file_exists($fotoPath)):
                        ?>
                            <img src="/manajemen_karyawan/uploads/foto_pegawai/<?= e($p['foto']) ?>"
                                 class="avatar-sm" alt="">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:.85rem;font-weight:600;color:var(--navy)"><?= e($p['nama']) ?></div>
                        <div style="font-size:.75rem;color:var(--mid-blue)"><?= e($p['nip']) ?></div>
                    </td>
                    <td style="font-size:.85rem"><?= e($p['jabatan'] ?? '-') ?></td>
                    <td style="font-size:.85rem"><?= e($p['departemen'] ?? '-') ?></td>
                    <td style="font-size:.82rem;color:var(--mid-blue)">
                        <?= $p['tanggal_masuk'] ? date('d M Y', strtotime($p['tanggal_masuk'])) : '-' ?>
                    </td>
                    <td>
                        <span class="badge-<?= $p['status'] ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="/manajemen_karyawan/pages/pegawai/edit.php?id=<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="/manajemen_karyawan/pages/pegawai/delete.php?id=<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
        <small class="text-muted"><?= count($pegawais) ?> dari <?= $total ?> data</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i===$page?'active':'' ?>">
                    <a class="page-link" href="?p=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
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