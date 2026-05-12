<?php
$title = 'Dashboard';
include __DIR__ . '/layout/header.php';

$db = getDB();

$totalPegawai = $db->query("SELECT COUNT(*) as c FROM pegawais")->fetch_assoc()['c'];
$pegawaiAktif = $db->query("SELECT COUNT(*) as c FROM pegawais WHERE status='aktif'")->fetch_assoc()['c'];
$pegawaiNonaktif = $db->query("SELECT COUNT(*) as c FROM pegawais WHERE status='nonaktif'")->fetch_assoc()['c'];
$totalUser = $db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];

$recentPegawai = $db->query("SELECT * FROM pegawais ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<div class="content-wrap">

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:12px;background:#f0f6ff;border:1.5px solid #dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-people fs-4" style="color:#052659"></i>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5483B3">Total Pegawai</div>
                    <div style="font-size:1.75rem;font-weight:800;color:#021024;line-height:1"><?= $totalPegawai ?></div>
                </div>
                <div style="width:4px;height:48px;border-radius:4px;background:#052659;margin-left:auto"></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:12px;background:#f0fdf4;border:1.5px solid #bbf7d0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-check fs-4" style="color:#15803d"></i>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#15803d">Pegawai Aktif</div>
                    <div style="font-size:1.75rem;font-weight:800;color:#021024;line-height:1"><?= $pegawaiAktif ?></div>
                </div>
                <div style="width:4px;height:48px;border-radius:4px;background:#16a34a;margin-left:auto"></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:12px;background:#fff5f5;border:1.5px solid #fecaca;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-x fs-4" style="color:#dc2626"></i>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#dc2626">Pegawai Nonaktif</div>
                    <div style="font-size:1.75rem;font-weight:800;color:#021024;line-height:1"><?= $pegawaiNonaktif ?></div>
                </div>
                <div style="width:4px;height:48px;border-radius:4px;background:#dc2626;margin-left:auto"></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:12px;background:#fffbeb;border:1.5px solid #fde68a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-gear fs-4" style="color:#d97706"></i>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#d97706">Total User</div>
                    <div style="font-size:1.75rem;font-weight:800;color:#021024;line-height:1"><?= $totalUser ?></div>
                </div>
                <div style="width:4px;height:48px;border-radius:4px;background:#d97706;margin-left:auto"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history" style="color:#5483B3"></i>
                <span style="font-size:.88rem;font-weight:700;color:#021024">Pegawai Terbaru</span>
            </div>
            <a href="/manajemen_karyawan/pages/pegawai/index.php"
               class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                <i class="bi bi-arrow-right"></i> Lihat Semua
            </a>
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
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($recentPegawai)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                            Belum ada data pegawai
                        </td>
                    </tr>
                <?php else: ?>
                <?php $no = 1; foreach ($recentPegawai as $p): ?>
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
                            <div style="font-size:.85rem;font-weight:600;color:#021024"><?= e($p['nama']) ?></div>
                            <div style="font-size:.75rem;color:#5483B3"><?= e($p['nip']) ?></div>
                        </td>
                        <td style="font-size:.85rem"><?= e($p['jabatan'] ?? '-') ?></td>
                        <td style="font-size:.85rem"><?= e($p['departemen'] ?? '-') ?></td>
                        <td style="font-size:.82rem;color:#5483B3">
                            <?= $p['tanggal_masuk'] ? date('d M Y', strtotime($p['tanggal_masuk'])) : '-' ?>
                        </td>
                        <td>
                            <span class="badge-<?= $p['status'] ?>">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>