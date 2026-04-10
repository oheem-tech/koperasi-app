<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<style>
.stat-card {
    border-radius: 14px;
    border: none;
    padding: 22px 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.08), 0 8px 24px rgba(0,0,0,.06);
    transition: transform .2s ease, box-shadow .2s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
}
.stat-card .stat-icon {
    position: absolute; right: 20px; top: 50%;
    transform: translateY(-50%);
    font-size: 3.2rem; opacity: .15;
}
.stat-card .stat-label {
    font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .8px;
    opacity: .85; margin-bottom: 6px;
}
.stat-card .stat-value {
    font-size: 1.65rem; font-weight: 700; line-height: 1.2;
    margin-bottom: 2px;
}
.stat-card .stat-sub {
    font-size: 0.76rem; opacity: .75;
}
.stat-blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; }
.stat-green  { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
.stat-cyan   { background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; }
.stat-amber  { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
.stat-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }

.welcome-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #1e4080 100%);
    border-radius: 14px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(30,64,175,.3);
}
.welcome-banner::before {
    content: '';
    position: absolute; right: -60px; top: -60px;
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(59,130,246,.3) 0%, transparent 70%);
    border-radius: 50%;
}
.welcome-banner::after {
    content: '';
    position: absolute; right: 120px; bottom: -80px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(6,182,212,.2) 0%, transparent 70%);
    border-radius: 50%;
}
.welcome-banner h3 { font-weight: 700; margin-bottom: 4px; font-size: 1.5rem; }
.welcome-banner p  { opacity: .75; margin: 0; font-size: 0.88rem; }
.welcome-banner .banner-date {
    font-size: 0.78rem; opacity: .6; margin-bottom: 8px; font-weight: 500;
}

.activity-table th { font-size: 0.75rem; }
.activity-table td { font-size: 0.84rem; vertical-align: middle; }
.section-title {
    font-size: 0.95rem; font-weight: 700;
    color: #0f172a; margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.section-title i { color: #3b82f6; }
</style>

<?php if(has_permission('manage_kas') || has_permission('view_laporan') || has_permission('manage_anggota')): ?>

<!-- Welcome Banner -->
<div class="welcome-banner position-relative">
    <div class="banner-date"><i class="fas fa-calendar-alt me-1"></i><?= date('l, d F Y') ?></div>
    <h3><i class="fas fa-landmark me-2" style="opacity:.8;"></i>Dashboard Koperasi</h3>
    <p>Selamat datang kembali, <strong><?= session()->get('username') ?></strong>. Berikut ringkasan keuangan koperasi hari ini.</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Total Anggota Aktif</div>
            <div class="stat-value"><?= $total_anggota ?></div>
            <div class="stat-sub">Anggota terdaftar</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="stat-label">Total Saldo Simpanan</div>
            <div class="stat-value" style="font-size:1.25rem;">Rp <?= number_format($saldo_simpanan, 0, ',', '.') ?></div>
            <div class="stat-sub mb-2">Dana tersimpan anggota</div>
            <div style="border-top:1px solid rgba(255,255,255,0.2); margin-top:8px; padding-top:8px;">
                <?php foreach($rincian_simpanan as $rs): ?>
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
                        <span><?= $rs['nama_simpanan'] ?></span>
                        <span class="fw-semibold">Rp <?= number_format($rs['total'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card stat-cyan">
            <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="stat-label">Total Dana Dipinjam</div>
            <div class="stat-value" style="font-size:1.25rem;">Rp <?= number_format($total_pinjaman, 0, ',', '.') ?></div>
            <div class="stat-sub mb-2">Seluruh pinjaman aktif & lunas</div>
            <div style="border-top:1px solid rgba(255,255,255,0.2); margin-top:8px; padding-top:8px;">
                <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
                    <span>Aktif</span>
                    <span class="fw-semibold">Rp <?= number_format($rincian_pinjaman['Aktif'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
                    <span>Lunas</span>
                    <span class="fw-semibold">Rp <?= number_format($rincian_pinjaman['Lunas'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card <?= $pinjaman_pending > 0 ? 'stat-amber' : 'stat-purple' ?>">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-label">Menunggu Persetujuan</div>
            <div class="stat-value"><?= $pinjaman_pending ?></div>
            <div class="stat-sub"><?= $pinjaman_pending > 0 ? 'Perlu tindakan segera' : 'Semua sudah diproses' ?></div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="section-title"><i class="fas fa-bolt"></i> Aksi Cepat</div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="<?= base_url('anggota/create') ?>" class="card text-decoration-none p-3 d-flex flex-row align-items-center gap-3 border border-2 border-primary-subtle" style="border-radius:12px;transition:transform .15s;">
            <div style="width:42px;height:42px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-user-plus text-primary"></i>
            </div>
            <div>
                <div style="font-size:.82rem;font-weight:600;color:#1e293b;">Tambah Anggota</div>
                <div style="font-size:.72rem;color:#94a3b8;">Daftarkan anggota baru</div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="<?= base_url('simpanan/create') ?>" class="card text-decoration-none p-3 d-flex flex-row align-items-center gap-3 border border-2 border-success-subtle" style="border-radius:12px;transition:transform .15s;">
            <div style="width:42px;height:42px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-plus-circle text-success"></i>
            </div>
            <div>
                <div style="font-size:.82rem;font-weight:600;color:#1e293b;">Input Simpanan</div>
                <div style="font-size:.72rem;color:#94a3b8;">Setor / tarik simpanan</div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="<?= base_url('angsuran/create') ?>" class="card text-decoration-none p-3 d-flex flex-row align-items-center gap-3 border border-2 border-warning-subtle" style="border-radius:12px;">
            <div style="width:42px;height:42px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-money-bill-wave text-warning"></i>
            </div>
            <div>
                <div style="font-size:.82rem;font-weight:600;color:#1e293b;">Bayar Angsuran</div>
                <div style="font-size:.72rem;color:#94a3b8;">Input pembayaran cicilan</div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <a href="<?= base_url('laporan/shu') ?>" class="card text-decoration-none p-3 d-flex flex-row align-items-center gap-3 border border-2 border-info-subtle" style="border-radius:12px;">
            <div style="width:42px;height:42px;background:#ecfeff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-chart-pie" style="color:#06b6d4;"></i>
            </div>
            <div>
                <div style="font-size:.82rem;font-weight:600;color:#1e293b;">Laporan SHU</div>
                <div style="font-size:.72rem;color:#94a3b8;">Distribusi hasil usaha</div>
            </div>
        </a>
    </div>
</div>

<?php if ($pinjaman_pending > 0): ?>
<div class="alert d-flex align-items-center gap-3" style="background:#fef3c7;color:#92400e;border-radius:12px;border:1px solid #fde68a;">
    <i class="fas fa-exclamation-triangle fa-lg"></i>
    <div>
        Ada <strong><?= $pinjaman_pending ?> pengajuan pinjaman</strong> yang menunggu persetujuan Anda.
        <a href="<?= base_url('pinjaman') ?>" class="ms-2 fw-semibold" style="color:#92400e;">Lihat Sekarang &rarr;</a>
    </div>
</div>
<?php endif; ?>

<?php else: ?>

<!-- ========== VIEW ANGGOTA ========== -->
<?php
    $db = \Config\Database::connect();
    $anggotaInfo = $db->table('anggota')->where('user_id', session()->get('user_id'))->get()->getRowArray();
?>
<div class="welcome-banner position-relative">
    <div class="banner-date"><i class="fas fa-calendar-alt me-1"></i><?= date('l, d F Y') ?></div>
    <h3><i class="fas fa-hand-wave me-2" style="opacity:.8;"></i>Selamat Datang!</h3>
    <p>Halo, <strong><?= session()->get('username') ?></strong>. Ini adalah ringkasan akun simpan pinjam Anda.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="stat-label">Saldo Simpanan Saya</div>
            <div class="stat-value">Rp <?= number_format($saldo_saya ?? 0, 0, ',', '.') ?></div>
            <div class="stat-sub mb-2">Akumulasi seluruh jenis simpanan</div>
            <div style="border-top:1px solid rgba(255,255,255,0.2); margin-top:8px; padding-top:8px;">
                <?php if(isset($rincian_simpanan_saya)): foreach($rincian_simpanan_saya as $rs): ?>
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                        <span><?= $rs['nama_simpanan'] ?></span>
                        <span class="fw-semibold">Rp <?= number_format($rs['total'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <?php if(!empty($pinjaman_aktif_list)): foreach($pinjaman_aktif_list as $pinjaman_aktif): ?>
    <div class="col-md-6">
        <div class="stat-card stat-amber">
            <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-label">Pinjaman Aktif (PJ-<?= str_pad($pinjaman_aktif['id'], 4, '0', STR_PAD_LEFT) ?>)</div>
            <div class="stat-value">Rp <?= number_format($pinjaman_aktif['jumlah_pinjaman'], 0, ',', '.') ?></div>
            <div class="stat-sub mb-2">Jatuh tempo: <?= date('d M Y', strtotime($pinjaman_aktif['tanggal_jatuh_tempo'])) ?></div>
            <div style="border-top:1px solid rgba(255,255,255,0.2); margin-top:8px; padding-top:8px;">
                <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                    <span>Sisa Pokok</span>
                    <span class="fw-semibold text-warning">Rp <?= number_format(max(0, $pinjaman_aktif['jumlah_pinjaman'] - ($pinjaman_aktif['pokok_terbayar'] ?? 0)), 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                    <span>Tenor & Bunga</span>
                    <span class="fw-semibold"><?= $pinjaman_aktif['lama_tenor'] ?> Bulan (<?= $pinjaman_aktif['bunga_persen'] ?>%)</span>
                </div>
                <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                    <span>Jenis Pinjaman</span>
                    <span class="fw-semibold"><?= $pinjaman_aktif['jenis_pinjaman'] ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="col-md-6">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label">Status Pinjaman</div>
            <div class="stat-value" style="font-size:1.1rem;">Tidak Ada Tagihan</div>
            <div class="stat-sub">Anda bebas dari cicilan aktif</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="section-title"><i class="fas fa-link"></i> Menu Cepat</div>
            <div class="d-flex flex-column gap-2">
                <a href="<?= base_url('simpanan') ?>" class="btn btn-outline-success btn-sm text-start">
                    <i class="fas fa-piggy-bank me-2"></i> Lihat Riwayat Simpanan
                </a>
                <a href="<?= base_url('angsuran') ?>" class="btn btn-outline-warning btn-sm text-start">
                    <i class="fas fa-money-bill-wave me-2"></i> Riwayat Angsuran
                </a>
                <?php if(empty($pinjaman_aktif_list)): ?>
                <a href="<?= base_url('pinjaman/create') ?>" class="btn btn-primary btn-sm text-start">
                    <i class="fas fa-hand-holding-usd me-2"></i> Ajukan Pinjaman Baru
                </a>
                <?php else: ?>
                <a href="<?= base_url('pinjaman/create') ?>" class="btn btn-primary btn-sm text-start">
                    <i class="fas fa-hand-holding-usd me-2"></i> Ajukan Pinjaman Tambahan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>
