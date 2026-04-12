<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-users-cog"></i> Pemindahan Massal Anggota</h2>
        <p class="text-muted">Pilih beberapa anggota sekaligus dan pindahkan mereka ke kelompok baru.</p>
    </div>
    <a href="<?= base_url('kelompok') ?>" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i> Kembali ke Master</a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Filter Panel -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0" style="border-radius:12px;">
            <div class="card-body bg-light" style="border-radius:12px;">
                <form action="<?= base_url('kelompok/bulk') ?>" method="get">
                    <label class="form-label fw-bold"><i class="fas fa-filter"></i> Saring Kelompok Asal</label>
                    <select class="form-select mb-3" name="filter_kelompok" onchange="this.form.submit()">
                        <option value="all" <?= ($filter_kelompok == 'all' || empty($filter_kelompok)) ? 'selected' : '' ?>>-- Tampilkan Semua --</option>
                        <?php foreach($kelompok as $k): ?>
                        <option value="<?= esc($k['nama_kelompok']) ?>" <?= ($filter_kelompok == $k['nama_kelompok']) ? 'selected' : '' ?>>
                            Kelompok: <?= esc($k['nama_kelompok']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info mt-3" style="font-size:0.85rem;">
            <i class="fas fa-info-circle mb-2" style="font-size:1.5rem;"></i><br>
            <strong>Cara Penggunaan:</strong><br>
            1. Saring anggota (opsional).<br>
            2. Centang nama anggota di tabel sebelah kanan.<br>
            3. Pilih kelompok tujuan pemindahan.<br>
            4. Klik proses pemindahan.
        </div>
    </div>

    <!-- Data Panel -->
    <div class="col-md-9">
        <?php if(empty($anggota)): ?>
            <div class="alert alert-warning">Tidak ada data anggota ditemukan untuk kriteria ini.</div>
        <?php else: ?>
            <form action="<?= base_url('kelompok/bulk_process') ?>" method="post" id="formBulk" onsubmit="return confirm('Apakah Anda yakin ingin memindahkan seluruh anggota yang dicentang ke kelompok yang dipilih?');">
                <?= csrf_field() ?>
                
                <div class="card shadow-sm border-0 mb-3" style="border-radius:12px;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius:12px 12px 0 0;">
                        <h6 class="mb-0"><i class="fas fa-list-check me-2"></i>Daftar Anggota</h6>
                        <span class="badge bg-light text-primary"><?= count($anggota) ?> Ditemukan</span>
                    </div>
                    <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAll" checked>
                                    </th>
                                    <th width="45%">Nama Anggota</th>
                                    <th width="25%">Status</th>
                                    <th width="25%">Kelompok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($anggota as $a): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input chk-anggota" name="anggota_ids[]" value="<?= $a['id'] ?>" checked>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="color:#1e293b;"><?= esc($a['nama_lengkap']) ?></div>
                                        <small class="text-muted"><?= esc($a['no_anggota']) ?></small>
                                    </td>
                                    <td>
                                        <?php if($a['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($a['kelompok']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Aksi Bawah -->
                <div class="card border-0 shadow-sm" style="border-radius:12px; background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                    <div class="card-body p-3 d-flex align-items-center flex-wrap gap-3">
                        <div style="flex:1;">
                            <label class="form-label fw-bold text-primary mb-1"><i class="fas fa-random"></i> Pindahkan Menjadi Kelompok:</label>
                            <select name="target_kelompok" class="form-select border-primary" required>
                                <option value="">-- Silakan Pilih Tujuan --</option>
                                <?php foreach($kelompok as $k): ?>
                                <option value="<?= esc($k['nama_kelompok']) ?>">Kelompok: <?= esc($k['nama_kelompok']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="pt-3">
                            <?php if(is_premium()): ?>
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnProses">
                                <i class="fas fa-check-double me-2"></i>Proses Pemindahan (<span id="countPilih"><?= count($anggota) ?></span>)
                            </button>
                            <?php else: ?>
                            <a href="<?= base_url('informasi/support') ?>" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-crown text-warning me-2"></i>Proses Pemindahan
                                <span style="font-size:0.7rem; background:rgba(255,255,255,0.2); border-radius:10px; padding:2px 8px; margin-left:4px;">PRO</span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.chk-anggota');
    const countDisplay = document.getElementById('countPilih');
    const btnProses = document.getElementById('btnProses');

    function updateCount() {
        let counted = document.querySelectorAll('.chk-anggota:checked').length;
        countDisplay.innerText = counted;
        if(counted === 0) {
            btnProses.disabled = true;
        } else {
            btnProses.disabled = false;
        }
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            let isChecked = this.checked;
            checkboxes.forEach(function(c) {
                c.checked = isChecked;
            });
            updateCount();
        });
    }

    checkboxes.forEach(function(c) {
        c.addEventListener('change', function() {
            updateCount();
            if(!this.checked && checkAll) {
                checkAll.checked = false;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
