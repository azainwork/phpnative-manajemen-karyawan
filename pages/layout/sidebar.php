<?php
$currentFile = basename($_SERVER['PHP_SELF'], '.php');
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
$cu = currentUser();
?>
<div id="sidebar">
    <a href="/manajemen_karyawan/pages/dashboard.php" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-building"></i>
        </div>
        <div>
            <div class="sidebar-brand-text">Manajemen Karyawan</div>
            <div class="sidebar-brand-sub">HR System</div>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>

        <a href="/manajemen_karyawan/pages/dashboard.php"
           class="nav-link <?= $currentFile === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <a href="/manajemen_karyawan/pages/pegawai/index.php"
           class="nav-link <?= $currentDir === 'pegawai' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Manajemen Pegawai
        </a>

        <?php if (isAdmin()): ?>
        <a href="/manajemen_karyawan/pages/users/index.php"
           class="nav-link <?= $currentDir === 'users' ? 'active' : '' ?>">
            <i class="bi bi-shield-lock"></i> Manajemen User
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-user">
        <div class="sidebar-avatar">
            <?= strtoupper(substr($cu['nama'], 0, 2)) ?>
        </div>
        <div>
            <div class="sidebar-user-name"><?= e($cu['nama']) ?></div>
            <div class="sidebar-user-role"><?= $cu['role'] ?></div>
        </div>
    </div>
</div>