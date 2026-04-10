<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit text-primary me-2"></i> Edit Hak Akses: <span class="badge bg-secondary"><?= strtoupper($role['name']) ?></span></h2>
    <a href="<?= base_url('role') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row custom-container">
    <div class="col-12 col-lg-8">
        <form action="<?= base_url('role/update/'.$role['id']) ?>" method="post" class="card shadow-sm border-0" style="border-radius:12px;">
            <?= csrf_field() ?>
            <div class="card-body p-4">
                
                <?php if($role['name'] == 'admin' || $role['name'] == 'anggota'): ?>
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                        <div>
                            <strong>Peringatan Sistem Otomatis</strong><br>
                            Anda sedang mengedit <em>reserved role</em> bawaan sistem. Pastikan untuk tidak keliru saat mengubah izinnya agar fitur inti koperasi bisa berjalan dengan lancar. Nama role tidak dapat diubah.
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">ID / Identifier Role</label>
                    <input type="text" name="name" class="form-control" value="<?= esc($role['name']) ?>" <?= in_array($role['name'], ['admin', 'anggota']) ? 'readonly' : '' ?> required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Deskripsi Jabatan</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Singkat, contoh: Mengelola khusus survey dan akad kredit"><?= esc($role['description']) ?></textarea>
                </div>
                
                <hr class="mb-4 text-muted">

                <div class="d-flex justify-content-between align-items-end mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-key text-warning"></i> Matriks Hak Akses Modul</h5>
                        <p class="text-muted small mb-0">Centang modul mana saja yang boleh digunakan dan dimodifikasi oleh Role ini.</p>
                    </div>
                    <?php if($role['name'] == 'admin'): ?>
                        <span class="badge bg-success" style="font-size:.8rem;">Administrator Mode</span>
                    <?php endif; ?>
                </div>

                <div class="row g-3">
                    <?php foreach($available_permissions as $key => $label): ?>
                    <div class="col-12 col-md-6">
                        <div class="form-check form-switch border p-2 rounded bg-light" style="padding-left:3rem !important;">
                            <input class="form-check-input mt-2" type="checkbox" role="switch" name="permissions[]" value="<?= $key ?>" id="perm_<?= $key ?>"
                                <?= in_array($key, $current_permissions) ? 'checked' : '' ?>
                                <?= ($role['name'] == 'admin' && false) ? 'disabled checked' : '' ?>
                            >
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
                        <i class="fas fa-save me-2"></i> Perbarui Hak Akses
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
