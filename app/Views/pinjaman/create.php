<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Pengajuan Pinjaman</h2>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('pinjaman/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if(has_permission('manage_pinjaman')): ?>
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle"></i> Anda mengajukan pinjaman atas nama anggota.
            </div>
            <div class="mb-3">
                <label class="form-label">Anggota Pemohon</label>
                <select class="form-select searchable-select" name="anggota_id" id="anggota_id" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['no_anggota'] ?> - <?= $a['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle"></i> Anggota yang terhormat, bunga pinjaman kami tetapkan sebesar <strong>1.5% per bulan</strong> dari jumlah awal pinjaman.
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label class="form-label">Tanggal Pengajuan</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Pinjaman</label>
                <select class="form-select" name="jenis_pinjaman" required>
                    <option value="">-- Pilih Jenis Pinjaman --</option>
                    <?php if(isset($opsi_jenis)): ?>
                        <?php foreach($opsi_jenis as $jenis): ?>
                            <option value="<?= trim($jenis) ?>"><?= trim($jenis) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Pinjaman (Rp)</label>
                <input type="number" class="form-control" name="jumlah_pinjaman" required min="100000" step="1000">
                <small class="text-muted">Minimal pengajuan Rp 100.000</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Lama Tenor Pinjaman</label>
                <select class="form-select" name="lama_tenor" required>
                    <option value="">-- Pilih Lama Cicilan --</option>
                    <?php if(isset($opsi_tenor)): ?>
                        <?php foreach($opsi_tenor as $tenor): ?>
                            <option value="<?= trim($tenor) ?>"><?= trim($tenor) ?> Bulan</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                <a href="<?= base_url('pinjaman') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
