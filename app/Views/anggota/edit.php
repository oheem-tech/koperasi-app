<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Data Anggota</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('anggota/update/'.$anggota['id']) ?>" method="post">
            <?= csrf_field() ?>
            <h5 class="mb-3 text-secondary">Data Profil Anggota</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Anggota</label>
                    <input type="text" class="form-control" value="<?= $anggota['no_anggota'] ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= $anggota['nama_lengkap'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" name="no_telp" value="<?= $anggota['no_telp'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="aktif" <?= $anggota['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $anggota['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jabatan <small class="text-muted">(berpengaruh pada distribusi SHU)</small></label>
                    <select class="form-select" name="jabatan">
                        <option value="anggota"  <?= ($anggota['jabatan'] ?? 'anggota') == 'anggota'  ? 'selected' : '' ?>>Anggota</option>
                        <option value="pengurus" <?= ($anggota['jabatan'] ?? '') == 'pengurus' ? 'selected' : '' ?>>Pengurus</option>
                        <option value="pengawas" <?= ($anggota['jabatan'] ?? '') == 'pengawas' ? 'selected' : '' ?>>Pengawas</option>
                        <option value="pembina"  <?= ($anggota['jabatan'] ?? '') == 'pembina'  ? 'selected' : '' ?>>Pembina</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label d-flex align-items-center gap-2">
                        Kelompok Anggota
                        <?php if(!is_premium()): ?>
                        <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px;"><i class="fas fa-crown" style="color:#fbbf24;font-size:0.55rem;"></i> PRO</span>
                        <?php endif; ?>
                    </label>
                    <?php 
                        $currKel = $anggota['kelompok'] ?? 'Umum';
                        $existsInMaster = false;
                    ?>
                    <?php if(is_premium()): ?>
                    <select class="form-select" name="kelompok">
                        <?php foreach($kelompok as $k): ?>
                            <?php if ($currKel == $k['nama_kelompok']) $existsInMaster = true; ?>
                            <option value="<?= esc($k['nama_kelompok']) ?>" <?= $currKel == $k['nama_kelompok'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelompok']) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if(!$existsInMaster && !empty($currKel)): ?>
                            <option value="<?= esc($currKel) ?>" selected><?= esc($currKel) ?> (Hanya Historis)</option>
                        <?php endif; ?>
                    </select>
                    <?php else: ?>
                    <select class="form-select bg-light" disabled>
                        <option><?= esc($currKel) ?></option>
                    </select>
                    <input type="hidden" name="kelompok" value="<?= esc($currKel) ?>">
                    <small class="text-muted" style="font-size:0.75rem;"><i class="fas fa-lock me-1"></i>Pengelompokan anggota hanya tersedia di Versi PRO.</small>
                    <?php endif; ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="3" required><?= $anggota['alamat'] ?></textarea>
                </div>
            </div>

            <h5 class="mb-3 mt-4 text-secondary">Data Akun (Login)</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= base_url('anggota') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
