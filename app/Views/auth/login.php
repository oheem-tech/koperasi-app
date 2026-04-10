<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login – <?= esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="margin:0;">

<style>
    /* ===== BASE ===== */
    * { box-sizing: border-box; }
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: stretch;
        font-family: 'Inter', sans-serif;
    }

    /* ===== DESKTOP: LEFT PANEL ===== */
    .login-left {
        flex: 1;
        background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 50%, #0f4c8c 100%);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 48px;
        position: relative; overflow: hidden;
    }
    .login-left::before {
        content: '';
        position: absolute; width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(59,130,246,.25) 0%, transparent 70%);
        border-radius: 50%; top: -80px; right: -80px;
    }
    .login-left::after {
        content: '';
        position: absolute; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(6,182,212,.2) 0%, transparent 70%);
        border-radius: 50%; bottom: -60px; left: -60px;
    }
    .login-left-content { position: relative; z-index: 2; text-align: center; color: #fff; }
    .login-logo {
        height: 72px;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;
    }
    .login-logo i { font-size: 32px; width: 72px; height: 72px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #3b82f6, #06b6d4); border-radius: 18px; box-shadow: 0 8px 32px rgba(59,130,246,.5); color: #fff;}
    .login-left h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 10px; }
    .login-left p  { opacity: .65; font-size: .9rem; max-width: 280px; line-height: 1.6; }
    .login-features { margin-top: 36px; display: flex; flex-direction: column; gap: 14px; text-align: left; }
    .login-feature {
        display: flex; align-items: center; gap: 12px;
        background: rgba(255,255,255,.07); border-radius: 10px;
        padding: 12px 16px; font-size: .83rem;
    }
    .login-feature i { color: #60a5fa; font-size: 1rem; width: 18px; text-align: center; }

    /* ===== DESKTOP: RIGHT PANEL ===== */
    .login-right {
        width: 100%;
        max-width: 460px;
        display: flex; align-items: center; justify-content: center;
        padding: 48px 40px;
        background: #fff;
    }
    .login-form-wrap { width: 100%; }
    .login-form-wrap h3 { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .login-form-wrap > p  { color: #94a3b8; font-size: .84rem; margin-bottom: 32px; }
    .form-label { font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    .form-control {
        width: 100%;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: 10px 14px 10px 38px; font-size: .88rem;
        transition: border-color .2s, box-shadow .2s;
        outline: none; font-family: 'Inter', sans-serif;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.15);
    }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap .field-icon {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: .88rem; pointer-events: none;
    }
    .input-icon-wrap .toggle-icon {
        position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: .88rem; cursor: pointer;
    }
    .form-control.has-toggle { padding-right: 40px; }
    .btn-login {
        width: 100%; padding: 12px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border: none; border-radius: 10px;
        color: #fff; font-weight: 700; font-size: .95rem;
        cursor: pointer; transition: opacity .2s, transform .15s;
        margin-top: 8px; font-family: 'Inter', sans-serif;
        letter-spacing: .2px;
    }
    .btn-login:hover { opacity: .92; transform: translateY(-1px); }
    .btn-login:active { transform: translateY(0); }
    .alert-danger-custom {
        background: #fee2e2; color: #991b1b; border-radius: 10px;
        padding: 11px 14px; font-size: .84rem; margin-bottom: 18px;
        border: 1px solid #fecaca;
    }

    /* Edge/IE native password reveal */
    input::-ms-reveal, input::-ms-clear { display: none; }

    /* ===== MOBILE ===== */
    .login-mobile-header { display: none; }

    @media (max-width: 768px) {
        body {
            background: linear-gradient(160deg, #0a1628 0%, #152847 40%, #0d3d75 100%);
            min-height: 100vh;
        }

        .login-page {
            flex-direction: column;
            min-height: 100vh;
            align-items: stretch;
        }

        /* Sembunyikan panel desktop kiri */
        .login-left { display: none; }

        /* Panel kanan: transparan, biarkan gradient body terlihat */
        .login-right {
            max-width: 100%;
            background: transparent;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-end;
        }

        /* MOBILE HEADER: area besar di atas */
        .login-mobile-header {
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 56px 28px 40px;
            color: #fff;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        /* Orb decoration */
        .login-mobile-header::before {
            content: '';
            position: absolute; width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(59,130,246,.3) 0%, transparent 65%);
            border-radius: 50%; top: -40px; right: -60px; z-index: -1;
        }
        .login-mobile-header::after {
            content: '';
            position: absolute; width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(6,182,212,.2) 0%, transparent 65%);
            border-radius: 50%; bottom: 20px; left: -40px; z-index: -1;
        }

        .mobile-logo-wrap {
            width: 90px; height: 90px;
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 2px solid rgba(255,255,255,.22);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 12px 40px rgba(0,0,0,.3), 0 0 0 1px rgba(255,255,255,.05) inset;
        }
        .mobile-logo-wrap img { max-height: 60px; border-radius: 12px; }
        .mobile-logo-wrap i { font-size: 36px; color: #fff; }

        .login-mobile-header h1 {
            font-size: 1.6rem; font-weight: 800;
            margin: 0 0 8px; letter-spacing: -.4px;
            text-shadow: 0 2px 12px rgba(0,0,0,.2);
        }
        .login-mobile-header .tagline {
            font-size: .82rem; opacity: .65; margin: 0 0 24px;
            line-height: 1.5;
        }

        /* Fitur strip mini */
        .mobile-features {
            display: flex; gap: 10px; justify-content: center;
            flex-wrap: wrap;
        }
        .mobile-feature-chip {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            padding: 5px 12px; font-size: .72rem; color: rgba(255,255,255,.85);
        }
        .mobile-feature-chip i { color: #60a5fa; font-size: .72rem; }

        /* FORM CARD: melengkung dari bawah */
        .login-form-wrap {
            background: #fff;
            border-radius: 28px 28px 0 0;
            padding: 32px 28px 40px;
            box-shadow: 0 -12px 48px rgba(0,0,0,.3);
            position: relative;
        }

        /* Handle bar atas kartu */
        .login-form-wrap::before {
            content: '';
            display: block;
            width: 40px; height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            margin: 0 auto 24px;
        }

        .login-form-wrap h3 { font-size: 1.25rem; margin-bottom: 2px; }
        .login-form-wrap > p { font-size: .8rem; margin-bottom: 20px; }

        .form-label { font-size: .85rem; }
        .form-control { font-size: .95rem; padding: 13px 14px 13px 40px; border-radius: 12px; }
        .input-icon-wrap .field-icon { font-size: .92rem; }
        .btn-login { padding: 14px; font-size: 1rem; border-radius: 13px; margin-top: 12px; }
    }
</style>

<div class="login-page">
    <!-- ===== DESKTOP: LEFT PANEL ===== -->
    <div class="login-left">
        <div class="login-left-content">
            <div class="login-logo">
                <?php if(get_koperasi_logo()): ?>
                    <img src="<?= get_koperasi_logo() ?>" alt="Logo Koperasi" style="max-height: 72px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.3);">
                <?php else: ?>
                    <i class="fas fa-landmark"></i>
                <?php endif; ?>
            </div>
            <h2><?= esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam')) ?></h2>
            <p>Sistem manajemen keuangan koperasi yang terintegrasi dan mudah digunakan.</p>

            <div class="login-features">
                <div class="login-feature">
                    <i class="fas fa-piggy-bank"></i>
                    <span>Kelola simpanan & pinjaman anggota</span>
                </div>
                <div class="login-feature">
                    <i class="fas fa-chart-pie"></i>
                    <span>Laporan SHU otomatis & akurat</span>
                </div>
                <div class="login-feature">
                    <i class="fas fa-balance-scale"></i>
                    <span>Neraca keuangan real-time</span>
                </div>
                <div class="login-feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Data aman dengan kontrol akses</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DESKTOP: RIGHT PANEL ===== -->
    <div class="login-right">

        <!-- MOBILE HEADER (hanya tampil di HP) -->
        <div class="login-mobile-header">
            <div class="mobile-logo-wrap">
                <?php if(get_koperasi_logo()): ?>
                    <img src="<?= get_koperasi_logo() ?>" alt="Logo">
                <?php else: ?>
                    <i class="fas fa-landmark"></i>
                <?php endif; ?>
            </div>
            <h1><?= esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam')) ?></h1>
            <p class="tagline">Sistem manajemen keuangan koperasi<br>yang terintegrasi & mudah digunakan</p>
            <div class="mobile-features">
                <div class="mobile-feature-chip"><i class="fas fa-piggy-bank"></i> Simpanan</div>
                <div class="mobile-feature-chip"><i class="fas fa-hand-holding-usd"></i> Pinjaman</div>
                <div class="mobile-feature-chip"><i class="fas fa-chart-pie"></i> Laporan SHU</div>
                <div class="mobile-feature-chip"><i class="fas fa-shield-alt"></i> Aman</div>
            </div>
        </div>

        <!-- FORM -->
        <div class="login-form-wrap">
            <h3>Masuk ke Akun Anda</h3>
            <p>Silakan masukkan kredensial Anda untuk melanjutkan</p>

            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert-danger-custom">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/process') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user field-icon"></i>
                        <input type="text" id="username" name="username" class="form-control"
                               placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="loginPassword">Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" id="loginPassword" name="password" class="form-control has-toggle"
                               placeholder="Masukkan password" required>
                        <i class="fas fa-eye-slash toggle-icon" id="togglePassword"></i>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
            </form>

            <p class="mt-4 text-center" style="font-size:.75rem;color:#94a3b8;">
                &copy; <?= date('Y') ?> <?= esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam')) ?> &middot; Semua hak dilindungi
            </p>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('loginPassword');
        const isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');
        this.classList.toggle('fa-eye-slash');
        this.classList.toggle('fa-eye');
    });
</script>

<?= $this->endSection() ?>
