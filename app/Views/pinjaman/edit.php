<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Pinjaman</h2>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('pinjaman/update/'.$pinjaman['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if(has_permission('manage_pinjaman')): ?>
            <div class="mb-3">
                <label class="form-label">Anggota Peminjam</label>
                <select class="form-select searchable-select" name="anggota_id" id="anggota_id" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $pinjaman['anggota_id'] == $a['id'] ? 'selected' : '' ?>><?= $a['no_anggota'] ?> - <?= $a['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Tanggal Pengajuan</label>
                <input type="date" name="tanggal_pengajuan" class="form-control" value="<?= date('Y-m-d', strtotime($pinjaman['tanggal_pengajuan'])) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Pinjaman</label>
                <select class="form-select" name="jenis_pinjaman" required>
                    <option value="">-- Pilih Jenis Pinjaman --</option>
                    <?php if(isset($opsi_jenis)): ?>
                        <?php foreach($opsi_jenis as $jenis): ?>
                            <option value="<?= trim($jenis) ?>" <?= $pinjaman['jenis_pinjaman'] == trim($jenis) ? 'selected' : '' ?>><?= trim($jenis) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Pinjaman (Rp)</label>
                <input type="number" class="form-control" name="jumlah_pinjaman" value="<?= $pinjaman['jumlah_pinjaman'] ?>" required min="100000" step="1000">
                <small class="text-muted">Minimal pengajuan Rp 100.000</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Lama Tenor Pinjaman</label>
                <select class="form-select" name="lama_tenor" required>
                    <option value="">-- Pilih Lama Cicilan --</option>
                    <?php if(isset($opsi_tenor)): ?>
                        <?php foreach($opsi_tenor as $tenor): ?>
                            <option value="<?= trim($tenor) ?>" <?= $pinjaman['lama_tenor'] == trim($tenor) ? 'selected' : '' ?>><?= trim($tenor) ?> Bulan</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= base_url('pinjaman') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
