<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-database text-primary me-2"></i> Backup & Restore</h2>
        <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Amankan data Koperasi Anda secara berkala atau pulihkan data dari file backup sebelumnya.</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show d-flex align-items-center" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm border-0 alert-dismissible fade show d-flex align-items-center" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-3 fs-5"></i>
        <div><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- UNDUH BACKUP CARD -->
    <div class="col-lg-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(59,130,246,0.08);">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold text-primary mb-0"><i class="fas fa-cloud-download-alt me-2"></i> Download Backup Data</h5>
            </div>
            <div class="card-body px-4 py-4 d-flex flex-column h-100">
                <p class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
                    Fitur ini akan mengekspor seluruh Database ke dalam satu file tunggal berformat <strong>.SQL</strong>. 
                    Sangat direkomendasikan untuk melakukan backup sebelum melakukan perubahan besar atau secara periodik (mingguan/bulanan) agar dapat meminimalkan risiko kehilangan data.
                </p>
                <div class="mt-4 mt-auto p-3" style="background: rgba(59,130,246,0.06); border-radius: 12px; border: 1px dashed rgba(59,130,246,0.2);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold mb-1" style="color:var(--primary-dark); font-size: 0.95rem;">File Backup Koperasi</div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Ukuran file bervariasi bergantung besaran data.</small>
                        </div>
                        <a href="<?= base_url('backup/download') ?>" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-download me-2"></i> Ekspor Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RESTORE DATBASE CARD -->
    <div class="col-lg-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(239,68,68,0.08);">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold text-danger mb-0"><i class="fas fa-cloud-upload-alt me-2"></i> Restore (Pulihkan) Backup</h5>
            </div>
            <div class="card-body px-4 py-4 d-flex flex-column h-100">
                <div class="alert bg-danger bg-opacity-10 text-danger border-0 mb-4 px-3 py-2" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>Peringatan!</strong> Proses ini akan <strong>menimpa secara permanen</strong> seluruh data Anda saat ini. Jangan lakukan apabila Anda belum meyakini kredibilitas file secara terperinci.
                </div>
                
                <form action="<?= base_url('backup/restore') ?>" method="post" enctype="multipart/form-data" class="d-flex flex-column flex-grow-1">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted" style="font-size: 0.85rem;">Upload File Format .SQL</label>
                        <input type="file" name="backup_file" class="form-control" accept=".sql" required style="border-radius: 10px; padding: 10px 15px;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted" style="font-size: 0.85rem;">Verifikasi Keamanan</label>
                        <p class="text-muted mb-2" style="font-size: 0.78rem;">Mohon ketikkan kata <strong>KONFIRMASI</strong> (huruf kapital) di bawah ini sebagai langkah validasi bahwa Anda memahami konsekuensinya.</p>
                        <input type="text" name="konfirmasi" class="form-control" autocomplete="off" placeholder="Ketik KONFIRMASI" required style="border-radius: 10px;">
                    </div>

                    <div class="mt-auto text-end">
                        <button type="submit" class="btn btn-danger px-4 py-2 shadow-sm" style="border-radius: 10px; font-weight: 600;" onclick="return confirm('APAKAH ANDA SANGAT YAKIN? Data saat ini akan digantikan seluruhnya oleh isi dari File SQL tersebut!');">
                            <i class="fas fa-sync-alt me-2"></i> Mulai Proses Restore
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Animasi ringan untuk card ketika on hover agar dynamic */
.card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.1) !important;}
</style>

<?= $this->endSection() ?>
