<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Karyawan' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy:       #021024;
            --dark-blue:  #052659;
            --mid-blue:   #5483B3;
            --light-blue: #7DA0CA;
            --pale-blue:  #C1E8FF;
            --sidebar-w:  255px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: #f0f6ff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--navy);
        }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--dark-blue);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        #sidebar::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, var(--mid-blue), var(--pale-blue));
        }

        .sidebar-brand {
            padding: 1.1rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(193,232,255,0.1);
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: rgba(193,232,255,0.12);
            border: 1px solid rgba(193,232,255,0.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand-icon i { color: var(--pale-blue); font-size: 1.1rem; }
        .sidebar-brand-text {
            font-size: .85rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
        }
        .sidebar-brand-sub {
            font-size: .65rem;
            color: var(--light-blue);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .sidebar-nav { flex: 1; padding: .5rem; }

        .nav-section {
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(125,160,202,0.45);
            padding: 1rem 1rem .3rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(193,232,255,0.65);
            padding: .6rem .85rem;
            border-radius: 9px;
            margin-bottom: 2px;
            font-size: .85rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            transition: background .15s, color .15s;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(193,232,255,0.08);
        }
        .sidebar-nav .nav-link.active {
            color: var(--navy);
            background: var(--pale-blue);
            font-weight: 600;
        }
        .sidebar-nav .nav-link.active i { color: var(--dark-blue); }

        .sidebar-user {
            margin: .5rem;
            padding: .75rem 1rem;
            border-radius: 10px;
            background: rgba(193,232,255,0.07);
            border: 1px solid rgba(193,232,255,0.1);
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(193,232,255,0.15);
            border: 1px solid rgba(193,232,255,0.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: .78rem;
            font-weight: 700;
            color: var(--pale-blue);
        }
        .sidebar-user-name {
            font-size: .8rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-user-role {
            font-size: .68rem;
            color: var(--light-blue);
            text-transform: capitalize;
        }

        #main-content { margin-left: var(--sidebar-w); min-height: 100vh; }

        #topbar {
            background: #fff;
            border-bottom: 1px solid #dbeafe;
            padding: .65rem 1.5rem;
            position: sticky;
            top: 0; z-index: 99;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-title {
            font-size: .95rem;
            font-weight: 700;
            color: var(--navy);
        }
        .topbar-right { display: flex; align-items: center; gap: .6rem; }
        .topbar-date {
            font-size: .78rem;
            color: var(--mid-blue);
            background: #f0f6ff;
            border: 1px solid #dbeafe;
            border-radius: 7px;
            padding: .3rem .7rem;
            display: flex;
            align-items: center;
            gap: .35rem;
        }
        .btn-logout {
            font-size: .78rem;
            font-weight: 600;
            color: #dc2626;
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 7px;
            padding: .3rem .8rem;
            display: flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-logout:hover { background: #fee2e2; color: #b91c1c; }

        .content-wrap { padding: 1.5rem; }

        .card {
            border: 1px solid #dbeafe;
            border-radius: 14px;
            box-shadow: none;
            background: #fff;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f6ff;
            border-radius: 14px 14px 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .table thead th {
            background: #f0f6ff;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--mid-blue);
            border-bottom: 1.5px solid #dbeafe;
            padding: .75rem 1rem;
        }
        .table tbody td { padding: .75rem 1rem; vertical-align: middle; border-color: #f0f6ff; }
        .table tbody tr:hover { background: #f8fbff; }

        .badge-aktif {
            background: #dcfce7; color: #15803d;
            font-size: .72rem; font-weight: 600;
            padding: .3rem .65rem; border-radius: 6px;
            display: inline-block;
        }
        .badge-nonaktif {
            background: #fee2e2; color: #dc2626;
            font-size: .72rem; font-weight: 600;
            padding: .3rem .65rem; border-radius: 6px;
            display: inline-block;
        }
        .badge-admin {
            background: #e0e7ff; color: #3730a3;
            font-size: .72rem; font-weight: 600;
            padding: .3rem .65rem; border-radius: 6px;
            display: inline-block;
        }
        .badge-staff {
            background: #f0f6ff; color: #5483B3;
            font-size: .72rem; font-weight: 600;
            padding: .3rem .65rem; border-radius: 6px;
            display: inline-block;
        }

        .avatar-sm {
            width: 36px; height: 36px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #dbeafe;
        }
        .avatar-placeholder {
            width: 36px; height: 36px;
            background: #f0f6ff;
            border-radius: 50%;
            border: 2px solid #dbeafe;
            display: inline-flex;
            align-items: center; justify-content: center;
            color: var(--mid-blue);
            font-size: .95rem;
        }

        .alert { border-radius: 10px; font-size: .85rem; }
        .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .alert-danger  { background: #fff5f5; border-color: #fecaca; color: #b91c1c; }

        .form-label { font-size: .8rem; font-weight: 600; color: var(--navy); }
        .form-control, .form-select {
            border-color: #dbeafe;
            border-radius: 9px;
            font-size: .88rem;
            color: var(--navy);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--mid-blue);
            box-shadow: 0 0 0 3px rgba(84,131,179,0.12);
        }

        .btn-primary {
            background: var(--dark-blue);
            border-color: var(--dark-blue);
            font-size: .85rem;
            font-weight: 600;
        }
        .btn-primary:hover { background: var(--mid-blue); border-color: var(--mid-blue); }
        .btn { border-radius: 8px; font-size: .82rem; font-weight: 500; }
        .btn-sm { font-size: .75rem; }

        .search-shell { position: relative; display: flex; align-items: center; }
        .search-shell .bi-search {
            position: absolute; left: 11px;
            font-size: .9rem; color: var(--mid-blue);
            pointer-events: none; z-index: 1;
        }
        .search-shell input { padding-left: 2.2rem; }

        .page-link { font-size: .8rem; color: var(--mid-blue); border-color: #dbeafe; }
        .page-item.active .page-link { background: var(--dark-blue); border-color: var(--dark-blue); }
        .page-link:hover { background: #f0f6ff; color: var(--dark-blue); }

        #previewFoto {
            width: 110px; height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dbeafe;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div id="main-content" class="flex-grow-1">

        <div id="topbar">
            <span class="topbar-title"><?= $title ?? '' ?></span>
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="bi bi-calendar3"></i>
                    <?= date('d M Y') ?>
                </div>
                <a href="/manajemen_karyawan/logout.php" class="btn-logout"
                   onclick="return confirm('Yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <div class="content-wrap">
            <?= getFlash() ?>