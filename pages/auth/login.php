<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (isLoggedIn()) {
    header('Location: /manajemen_karyawan/pages/dashboard.php'); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND status = 1 LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_uname'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: /manajemen_karyawan/pages/dashboard.php'); exit;
        }
        $error = 'Username atau password salah.';
    } else {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Manajemen Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy:      #021024;
            --dark-blue: #052659;
            --mid-blue:  #5483B3;
            --light-blue:#7DA0CA;
            --pale-blue: #C1E8FF;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
        }

        .login-left {
            width: 420px;
            flex-shrink: 0;
            background: var(--dark-blue);
            display: flex;
            flex-direction: column;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--mid-blue), var(--pale-blue));
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 40px solid rgba(193,232,255,0.05);
            bottom: -80px; right: -80px;
        }

        .left-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: auto;
        }
        .left-brand-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: rgba(193,232,255,0.12);
            border: 1px solid rgba(193,232,255,0.18);
            display: flex; align-items: center; justify-content: center;
        }
        .left-brand-icon i { color: var(--pale-blue); font-size: 1.2rem; }
        .left-brand-text {
            font-size: .95rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }
        .left-brand-sub {
            font-size: .65rem;
            color: var(--light-blue);
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .left-body { margin-top: 3rem; }
        .left-body h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: .75rem;
        }
        .left-body p {
            font-size: .85rem;
            color: var(--light-blue);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .left-feature {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1rem;
            border-radius: 10px;
            background: rgba(193,232,255,0.06);
            border: 1px solid rgba(193,232,255,0.1);
            margin-bottom: .5rem;
        }
        .left-feature i {
            font-size: 1rem;
            color: var(--pale-blue);
            flex-shrink: 0;
        }
        .left-feature span {
            font-size: .82rem;
            color: rgba(193,232,255,0.8);
        }

        .left-footer {
            margin-top: auto;
            padding-top: 2rem;
            font-size: .72rem;
            color: rgba(125,160,202,0.5);
        }

        .login-right {
            flex: 1;
            background: #f0f6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-form-wrap {
            width: 100%;
            max-width: 400px;
        }

        .login-form-wrap .greeting {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--mid-blue);
            margin-bottom: .4rem;
        }
        .login-form-wrap h3 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: .3rem;
        }
        .login-form-wrap .subtitle {
            font-size: .85rem;
            color: var(--mid-blue);
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--navy);
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--mid-blue);
            font-size: .95rem;
            pointer-events: none;
        }
        .input-wrap input {
            padding-left: 2.4rem;
            border: 1.5px solid #dbeafe;
            border-radius: 10px;
            font-size: .88rem;
            color: var(--navy);
            background: #fff;
            width: 100%;
            height: 44px;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .input-wrap input:focus {
            border-color: var(--mid-blue);
            box-shadow: 0 0 0 3px rgba(84,131,179,0.12);
        }
        .input-wrap input::placeholder { color: #94a3b8; }

        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 0;
            color: #94a3b8;
            font-size: .78rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #dbeafe;
        }

        .btn-masuk {
            width: 100%;
            height: 46px;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .btn-masuk:hover { background: var(--dark-blue); transform: translateY(-1px); }
        .btn-masuk:active { transform: translateY(0); }

        .alert-danger {
            background: #fff5f5;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 10px;
            font-size: .83rem;
            padding: .7rem 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        @media (max-width: 640px) {
            .login-left { display: none; }
        }
    </style>
</head>
<body>

<div class="login-left">
    <div class="left-brand">
        <div class="left-brand-icon"><i class="bi bi-building"></i></div>
        <div>
            <div class="left-brand-text">Manajemen Karyawan</div>
            <div class="left-brand-sub">HR System</div>
        </div>
    </div>

    <div class="left-body">
        <h2>Kelola Data Karyawan dengan Mudah</h2>
        <p>Kelola data karyawan dengan mudah, cepat, dan terpusat.</p>

        <div class="left-feature">
            <i class="bi bi-people"></i>
            <span>Manajemen data pegawai lengkap</span>
        </div>
        <div class="left-feature">
            <i class="bi bi-shield-check"></i>
            <span>Akses berbasis peran (Admin & Staff)</span>
        </div>
        <div class="left-feature">
            <i class="bi bi-bar-chart-line"></i>
            <span>Dashboard & laporan terintegrasi</span>
        </div>
    </div>

    <div class="left-footer">© <?= date('Y') ?> HR System. By Zain Ofc.</div>
</div>

<div class="login-right">
    <div class="login-form-wrap">
        <div class="greeting">Selamat Datang</div>
        <h3>Masuk ke Sistem</h3>
        <p class="subtitle">Silakan login untuk melanjutkan ke dashboard.</p>

        <?php if ($error): ?>
        <div class="alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-wrap mt-1">
                    <i class="bi bi-person"></i>
                    <input type="text" name="username"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           placeholder="Masukkan username"
                           autocomplete="username" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-wrap mt-1">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password"
                           placeholder="Masukkan password"
                           autocomplete="current-password" required>
                </div>
            </div>

            <div class="divider">atau lanjutkan dengan akun Anda</div>

            <button type="submit" class="btn-masuk">
                <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>