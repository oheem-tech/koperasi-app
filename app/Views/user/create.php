<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-plus text-primary me-2"></i> Tambah Akun Staf (Sistem)</h2>
    <a href="<?= base_url('user') ?>" class="btn btn-secondary d-inline-flex gap-2 align-items-center shadow-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row custom-container">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i> Data Akses Sistem</h6>
            </div>
            <div class="card-body p-4">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="Contoh: petugas_andi" autocomplete="off" style="border-radius:8px;">
                        <small class="text-muted" style="font-size:.75rem;">Akan digunakan untuk masuk ke dalam aplikasi sistem Koperasi.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" style="border-radius:8px;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Jabatan Akses / Role</label>
                        <select name="role" class="form-select" required style="border-radius:8px;">
                            <option value="">-- Pilih Hak Akses Role --</option>
                            <?php foreach($roles as $r): ?>
                                <option value="<?= $r['name'] ?>"><?= strtoupper($r['name']) ?> - <?= esc($r['description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-2 text-muted" style="font-size:.75rem;">
                            <i class="fas fa-info-circle text-info"></i> Jabatan ini murni untuk mengontrol modul mana yang dapat diakses di aplikasi. Berbeda dengan Jabatan di Data Anggota yang mempengaruhi pembagian SHU.
                        </div>
                    </div>

                    <hr class="text-secondary opacity-25">

                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius:8px;">
                            <i class="fas fa-save me-1"></i> Simpan Staff & Akses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="card-body bg-light p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-shield-alt text-success me-2"></i> Info Keamanan</h6>
                <ul class="mb-0 text-muted" style="font-size:.85rem;line-height:1.6;list-style-type:circle;">
                    <li class="mb-2"><strong>Super Admin</strong> dapat melihat seluruh data dan mengubah semua pengaturan kritis.</li>
                    <li class="mb-2"><strong>Hak Akses / Role</strong> akan memberikan batasan pada modul mana yang ditampilkan di bilah menu kiri.</li>
                    <li>Pastikan Role sesuai dengan tugas dari staf tersebut untuk menghindari celah privasi data keanggotaan/kas.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
