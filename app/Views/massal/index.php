<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h2><i class="fas fa-layer-group"></i> Input Simpanan & Angsuran Massal</h2>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius:16px; overflow:hidden;">
            <div class="card-header text-white fw-bold py-3" style="background: linear-gradient(135deg, #3b82f6, #06b6d4);">
                <i class="fas fa-filter me-2"></i> Pilih Kelompok Anggota & Tanggal Transaksi
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('massal/form') ?>" method="get">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Kelompok Anggota</label>
                            <select name="kelompok" class="form-select form-select-lg" required>
                                <?php foreach ($kelompokList as $k): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Anggota yang memiliki kelompok ini akan ditampilkan.</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required>
                            <small class="text-muted">Untuk semua catatan simpanan & angsuran.</small>
                        </div>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-table me-2"></i> Muat Daftar Anggota
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4 border-0 shadow-sm" style="border-radius:16px; background:#f8fafc;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Cara Kerja Input Massal</h6>
                <ol class="mb-0" style="line-height:2; font-size:0.88rem; color:#475569;">
                    <li>Pilih <strong>Kelompok</strong> anggota (misal: <em>PNS</em>) dan <strong>Tanggal</strong> transaksi potong gaji.</li>
                    <li>Sistem menampilkan semua anggota aktif pada kelompok tersebut, lengkap dengan kolom Simpanan Wajib.</li>
                    <li>Pada kolom Angsuran, pilih <strong>Pinjaman mana</strong> yang akan dibayarkan dari dropdown — nominal otomatis terisi.</li>
                    <li>Tekan <strong>Proses Pembayaran</strong> — sistem menyimpan semua transaksi sekaligus dan membuat <em>satu baris ringkas</em> di Buku Kas Umum.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
