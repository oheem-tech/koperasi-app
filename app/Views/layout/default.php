<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? esc(get_pengaturan('koperasi_nama', 'Koperasi App')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-width: 250px;
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-muted-sidebar: #94a3b8;
            --sidebar-hover: rgba(59,130,246,0.15);
            --sidebar-active: rgba(59,130,246,0.25);
            --content-bg: #f1f5f9;
            --card-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--content-bg);
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,.25);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .brand-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; 
            margin: 0 auto 10px;
            box-shadow: 0 4px 12px rgba(59,130,246,.4);
        }
        .sidebar-brand h5 {
            color: #fff; font-size: 0.95rem; font-weight: 700;
            margin: 0; letter-spacing: .3px;
        }
        .sidebar-brand small {
            color: var(--text-muted-sidebar); font-size: 0.72rem; font-weight: 400;
        }
        .role-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(59,130,246,.2); color: var(--primary);
            border-radius: 20px; padding: 2px 10px; font-size: 0.7rem;
            font-weight: 600; margin-top: 4px; letter-spacing: .4px;
        }

        .sidebar-nav { padding: 12px 0; flex: 1; }

        .nav-section-label {
            padding: 12px 20px 5px;
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.2px;
            color: #475569;
        }

        .sidebar a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px;
            color: var(--text-muted-sidebar);
            text-decoration: none;
            font-size: 0.83rem; font-weight: 500;
            border-radius: 0;
            transition: all .18s ease;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
            border-left-color: rgba(59,130,246,.5);
        }
        .sidebar a.active {
            background: var(--sidebar-active);
            color: #fff;
            border-left-color: var(--primary);
            font-weight: 600;
        }
        .sidebar a .nav-icon {
            width: 18px; text-align: center; font-size: 0.85rem;
            opacity: .85;
        }
        .sidebar a.active .nav-icon { opacity: 1; color: var(--primary); }

        .sidebar-footer {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer a {
            color: #ef4444 !important;
        }
        .sidebar-footer a:hover {
            background: rgba(239,68,68,.12) !important;
            border-left-color: #ef4444 !important;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 28px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .topbar .page-title {
            font-size: 0.88rem; font-weight: 500; color: #64748b; margin: 0;
        }
        .topbar .page-title span { color: #1e293b; font-weight: 600; }
        .topbar-right {
            display: flex; align-items: center; gap: 14px;
        }
        .topbar-user {
            display: flex; align-items: center; gap: 9px;
        }
        .topbar-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.8rem; font-weight: 700;
        }
        .topbar-user-info small { display: block; font-size: 0.72rem; color: #94a3b8; }
        .topbar-user-info strong { font-size: 0.82rem; color: #1e293b; }

        .content-body {
            padding: 24px 28px;
        }

        /* ===== CARDS ===== */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            padding: 14px 20px;
            font-size: 0.88rem;
            border-bottom: 1px solid rgba(0,0,0,.06);
        }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.875rem;
        }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }

        /* ===== TABLES ===== */
        .table { font-size: 0.855rem; }
        .table thead th {
            font-weight: 600; font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: .5px;
        }

        /* ===== BUTTONS ===== */
        .btn { font-size: 0.84rem; font-weight: 500; border-radius: 8px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* ===== BADGES ===== */
        .badge { font-weight: 500; border-radius: 6px; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .page-header h2 {
            font-size: 1.4rem; font-weight: 700; color: #0f172a; margin: 0;
        }
        .page-header h2 i { color: var(--primary); margin-right: 10px; }

        /* ===== MOBILE RESPONSIVE ===== */
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.3rem; color: #1e293b; cursor: pointer; padding: 0; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.6); z-index: 90; backdrop-filter: blur(2px); }
        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .sidebar { transform: translateX(-100%); transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: none; }
            .sidebar.show { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.4); }
            .sidebar-overlay.show { display: block; }
            .main-wrapper { margin-left: 0; }
            .topbar { padding: 12px 20px; }
            .content-body { padding: 20px; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
</head>
<body>

<?php if (session()->get('isLoggedIn')): ?>
    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-brand text-center">
            <?php if(get_koperasi_logo()): ?>
                <div class="mb-2">
                    <img src="<?= get_koperasi_logo() ?>" alt="Logo" style="max-height: 52px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                </div>
            <?php else: ?>
                <div class="brand-logo"><i class="fas fa-landmark"></i></div>
            <?php endif; ?>
            <h5><?= esc(get_pengaturan('koperasi_nama', 'Koperasi App')) ?></h5>
            <div class="role-badge">
                <i class="fas fa-circle" style="font-size:6px;"></i>
                <?= strtoupper(str_replace('_', ' ', session()->get('role') ?? 'anggota')) ?>
            </div>
            <?php if(!is_premium()): ?>
            <div style="margin-top: 8px;">
                <?php if(has_permission('manage_pengaturan')): ?>
                <a href="<?= base_url('informasi/support') ?>" class="bg-warning text-dark text-decoration-none shadow-sm" style="display: inline-flex !important; align-items: center; gap: 5px; font-size: 0.7rem; padding: 3px 12px; border-radius: 20px; font-weight: 600; width: max-content; margin: 0 auto;" title="Klik untuk Upgrade Lisensi"><i class="fas fa-crown text-dark" style="margin: 0; width: auto;"></i> VERSI GRATIS</a>
                <?php else: ?>
                <span class="bg-warning text-dark shadow-sm" style="display: inline-flex !important; align-items: center; gap: 5px; font-size: 0.7rem; padding: 3px 12px; border-radius: 20px; font-weight: 600; width: max-content; margin: 0 auto;"><i class="fas fa-crown text-dark" style="margin: 0; width: auto;"></i> VERSI GRATIS</span>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div style="margin-top: 8px;">
                <span style="display: inline-flex !important; align-items: center; gap: 5px; font-size: 0.7rem; padding: 3px 12px; border-radius: 20px; font-weight: 700; width: max-content; margin: 0 auto; background: linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.4px;"><i class="fas fa-crown" style="margin:0;width:auto;color:#fbbf24;"></i> VERSI PRO</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-label">Utama</div>
            <a href="<?= base_url('dashboard') ?>" class="<?= current_url(true)->getSegment(1) == 'dashboard' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-th-large"></i> Dashboard
            </a>

            <div class="nav-section-label">Transaksi</div>
            <a href="<?= base_url('simpanan') ?>" class="<?= current_url(true)->getSegment(1) == 'simpanan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-piggy-bank"></i> Simpanan
            </a>
            <a href="<?= base_url('pinjaman') ?>" class="<?= current_url(true)->getSegment(1) == 'pinjaman' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-hand-holding-usd"></i> Pinjaman
            </a>
            <a href="<?= base_url('angsuran') ?>" class="<?= current_url(true)->getSegment(1) == 'angsuran' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-money-bill-wave"></i> Angsuran
            </a>
            <?php if(has_permission('manage_simpanan')): ?>
            <?php if(is_premium()): ?>
            <a href="<?= base_url('massal') ?>" class="<?= current_url(true)->getSegment(1) == 'massal' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-layer-group"></i> Input Massal
            </a>
            <?php else: ?>
            <a href="<?= base_url('massal') ?>" class="<?= current_url(true)->getSegment(1) == 'massal' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-layer-group"></i> Input Massal
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if(has_permission('manage_kas')): ?>
            <a href="<?= base_url('kas') ?>" class="<?= current_url(true)->getSegment(1) == 'kas' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-book-open"></i> Buku Kas Umum
            </a>
            <?php endif; ?>

            <?php if(has_permission('view_laporan')): ?>
            <div class="nav-section-label">Laporan</div>
            <a href="<?= base_url('laporan/kas') ?>" class="<?= current_url(true)->getSegment(1) == 'laporan' && current_url(true)->getSegment(2) == 'kas' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-chart-line"></i> Arus Kas
            </a>
            <a href="<?= base_url('laporan/shu') ?>" class="<?= current_url(true)->getSegment(1) == 'laporan' && current_url(true)->getSegment(2) == 'shu' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-chart-pie"></i> Laporan SHU
            </a>
            <a href="<?= base_url('laporan/neraca') ?>" class="<?= current_url(true)->getSegment(1) == 'laporan' && current_url(true)->getSegment(2) == 'neraca' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-balance-scale"></i> Neraca Saldo
            </a>
            <a href="<?= base_url('laporan/anggota') ?>" class="<?= current_url(true)->getSegment(1) == 'laporan' && current_url(true)->getSegment(2) == 'anggota' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-users-viewfinder"></i> Keuangan Anggota
            </a>
            <?php endif; ?>

            <?php if(has_permission('manage_anggota') || has_permission('manage_pengaturan') || has_permission('manage_roles') || has_permission('manage_backup')): ?>
            <div class="nav-section-label">Master Data</div>
            
            <?php if(has_permission('manage_anggota')): ?>
            <a href="<?= base_url('anggota') ?>" class="<?= current_url(true)->getSegment(1) == 'anggota' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-users"></i> Data Anggota
            </a>
            <?php if(is_premium()): ?>
            <a href="<?= base_url('kelompok') ?>" class="<?= current_url(true)->getSegment(1) == 'kelompok' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-layer-group"></i> Master Kelompok
            </a>
            <?php else: ?>
            <a href="<?= base_url('kelompok') ?>" class="<?= current_url(true)->getSegment(1) == 'kelompok' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-layer-group"></i> Master Kelompok
            </a>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if(has_permission('manage_pengaturan')): ?>
            <a href="<?= base_url('jenis-simpanan') ?>" class="<?= current_url(true)->getSegment(1) == 'jenis-simpanan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-tags"></i> Jenis Simpanan
            </a>
            <a href="<?= base_url('pengaturan') ?>" class="<?= current_url(true)->getSegment(1) == 'pengaturan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-sliders-h"></i> Pengaturan Master
            </a>
            <?php endif; ?>

            <?php if(has_permission('manage_backup')): ?>
            <a href="<?= base_url('backup') ?>" class="<?= current_url(true)->getSegment(1) == 'backup' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-database"></i> Backup & Restore
            </a>
            <?php endif; ?>

            <?php if(has_permission('manage_roles')): ?>
            <a href="<?= base_url('user') ?>" class="<?= current_url(true)->getSegment(1) == 'user' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-users-cog"></i> Manajemen Staf/User
            </a>
            <a href="<?= base_url('role') ?>" class="<?= current_url(true)->getSegment(1) == 'role' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-user-shield"></i> Hak Akses & Role
            </a>
            <?php endif; ?>
            
            <?php endif; ?>

            <?php if(session()->get('role') !== 'anggota'): ?>
            <div class="nav-section-label">Bantuan & Info</div>
            <a href="<?= base_url('informasi/fitur') ?>" class="<?= current_url(true)->getSegment(1) == 'informasi' && current_url(true)->getSegment(2) == 'fitur' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-star text-warning"></i> Detail Fitur
            </a>
            <a href="<?= base_url('informasi/panduan') ?>" class="<?= current_url(true)->getSegment(1) == 'informasi' && current_url(true)->getSegment(2) == 'panduan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-book-reader text-info"></i> Panduan
            </a>
            <a href="<?= base_url('informasi/support') ?>" class="<?= current_url(true)->getSegment(1) == 'informasi' && current_url(true)->getSegment(2) == 'support' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-headset text-success"></i> Kustomisasi & Support
            </a>
            <?php endif; ?>
        </div>


        <div class="sidebar-footer">
            <a href="<?= base_url('profil') ?>" class="<?= current_url(true)->getSegment(1) == 'profil' ? 'active' : '' ?>" style="color:#94a3b8 !important;">
                <i class="nav-icon fas fa-user-circle"></i> Profil Saya
            </a>
            <a href="<?= base_url('auth/logout') ?>">
                <i class="nav-icon fas fa-sign-out-alt"></i> Logout
            </a>
            <?php if (!is_premium()): ?>
            <div class="mt-3 px-3 text-center">
                <div class="p-2" style="background: rgba(255,255,255,0.05); border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1);">
                    <small style="color: var(--text-muted-sidebar); font-size: 0.65rem; display: block;">Dikembangkan Oleh</small>
                    <strong style="color: #fff; font-size: 0.75rem; letter-spacing: 0.5px;">CirebonTech</strong>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
                <p class="page-title mb-0 d-none d-sm-block">
                    <i class="fas fa-home me-1" style="color:#94a3b8;font-size:.75rem;"></i>
                    <span><?= $title ?? 'Halaman' ?></span>
                </p>
            </div>
            <div class="topbar-right">
                <div class="dropdown">
                    <button class="topbar-user btn p-0 border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        <div class="topbar-avatar"><?= strtoupper(substr(session()->get('username') ?? 'A', 0, 1)) ?></div>
                        <div class="topbar-user-info d-none d-sm-block text-start">
                            <strong><?= session()->get('username') ?? '' ?></strong>
                            <small><?= strtoupper(str_replace('_', ' ', session()->get('role') ?? 'anggota')) ?></small>
                        </div>
                        <i class="fas fa-chevron-down ms-2 d-none d-sm-block" style="font-size:0.65rem;color:#94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:180px;border-radius:10px;border:1px solid #e2e8f0;font-size:0.85rem;">
                        <li><div class="px-3 py-2 border-bottom">
                            <div class="fw-600" style="color:#1e293b;"><?= esc(session()->get('username')) ?></div>
                            <small class="text-muted"><?= strtoupper(str_replace('_', ' ', session()->get('role') ?? 'anggota')) ?></small>
                        </div></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('profil') ?>">
                            <i class="fas fa-user-circle me-2 text-primary"></i> Profil Saya
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content-body">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

<?php else: ?>
    <?= $this->renderSection('content') ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile Sidebar Toggle
const mobileToggle = document.getElementById('mobileToggle');
const sidebar = document.querySelector('.sidebar');
const overlay = document.getElementById('sidebarOverlay');

if(mobileToggle && sidebar && overlay) {
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.add('show');
        overlay.classList.add('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
}

// Auto responsive tables
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('table.table').forEach(table => {
        if(!table.parentElement.classList.contains('table-responsive') && !table.parentElement.classList.contains('overflow-auto')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive border-0';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
});

/**
 * handlePrint(url) — Cetak laporan berdasarkan perangkat:
 *   - Desktop : buka tab baru → halaman print auto-trigger window.print()
 *   - Mobile  : navigasi langsung ke halaman print (Web Share / tombol cetak tersedia di sana)
 */
function handlePrint(url) {
    var isMobile = /Mobi|Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);
    if (isMobile) {
        window.location.href = url;  // buka di tab yang sama, cocok untuk HP
    } else {
        window.open(url, '_blank');  // buka tab baru, agar auto-print bisa berjalan
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.searchable-select').forEach(function(el) {
        new TomSelect(el, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
});
</script>
</body>
</html>
