<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Role Baru</h2>
    <a href="<?= base_url('role') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row custom-container">
    <div class="col-12 col-lg-8">
        <form action="<?= base_url('role/store') ?>" method="post" class="card shadow-sm border-0" style="border-radius:12px;">
            <?= csrf_field() ?>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">ID / Identifier Role (Tanpa spasi)</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: surveyor_pinjaman, ketua_koperasi" required>
                    <div class="form-text">Nama unik role untuk sistem, dilarang memakai 'admin' atau 'anggota'.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Deskripsi Jabatan</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Singkat, contoh: Mengelola khusus survey dan akad kredit"></textarea>
                </div>
                
                <hr class="mb-4 text-muted">

                <h5 class="fw-bold mb-3"><i class="fas fa-key text-warning"></i> Matriks Hak Akses Modul</h5>
                <p class="text-muted small mb-4">Centang modul mana saja yang boleh digunakan dan dimodifikasi oleh Role ini.</p>

                <div class="row g-3">
                    <?php foreach($available_permissions as $key => $label): ?>
                    <div class="col-12 col-md-6">
                        <div class="form-check form-switch border p-2 rounded bg-light" style="padding-left:3rem !important;">
                            <input class="form-check-input mt-2" type="checkbox" role="switch" name="permissions[]" value="<?= $key ?>" id="perm_<?= $key ?>">
                            <label class="form-check-label d-block text-dark mt-1" for="perm_<?= $key ?>" style="cursor:pointer; font-weight: 500;">
                                <?= $label ?>
                                <small class="d-block text-muted" style="font-size:0.75rem;">(<?= $key ?>)</small>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius:8px;">
                        <i class="fas fa-save me-2"></i> Simpan Role Baru
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
