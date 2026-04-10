<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Pembayaran Angsuran</h2>

<div class="card shadow-sm" style="max-width: 650px;">
    <div class="card-body">
        <form action="<?= base_url('angsuran/update/'.$angsuran['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="alert alert-info py-2" style="font-size: 0.9rem;">
                <strong><i class="fas fa-info-circle"></i> Info:</strong> Anda hanya dapat mengubah nominal dan tanggal untuk transaksi yang sudah terjadi. Pinjaman dan urutan cicilan tidak dapat diubah dari form ini.
            </div>

            <!-- Detail Pinjaman (Read-Only) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted">Anggota & Pinjaman Terkait</label>
                <?php
                    $nama = '';
                    foreach($anggota as $a) {
                        if($a['id'] == $pinjaman['anggota_id']) {
                            $nama = $a['nama_lengkap'] . ' (' . $a['no_anggota'] . ')';
                            break;
                        }
                    }
                ?>
                <input type="text" class="form-control" value="<?= $nama ?> — <?= $pinjaman['jenis_pinjaman'] ?> (Tenor <?= $pinjaman['lama_tenor'] ?>x)" disabled>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">Tanggal Pembayaran</label>
                <input type="date" class="form-control" name="tanggal_bayar" value="<?= $angsuran['tanggal_bayar'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Pembayaran Cicilan Ke-</label>
                <input type="text" class="form-control" value="<?= $angsuran['cicilan_ke'] ?>" disabled>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Angsuran Pokok (Rp)</label>
                    <input type="number" class="form-control" name="jumlah_pokok" id="edit_jumlah_pokok" value="<?= $angsuran['jumlah_pokok'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Angsuran Jasa (Rp)</label>
                    <input type="number" class="form-control" name="jumlah_jasa" id="edit_jumlah_jasa" value="<?= $angsuran['jumlah_jasa'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Total Angsuran (Rp)</label>
                    <input type="number" class="form-control fw-bold" name="jumlah_bayar" id="edit_jumlah_bayar" value="<?= $angsuran['jumlah_bayar'] ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Denda Keterlambatan (Rp)</label>
                <input type="number" class="form-control" name="denda" value="<?= $angsuran['denda'] ?>">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= base_url('angsuran') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateEditTotal() {
        var pokok = parseInt(document.getElementById('edit_jumlah_pokok').value) || 0;
        var jasa  = parseInt(document.getElementById('edit_jumlah_jasa').value)  || 0;
        document.getElementById('edit_jumlah_bayar').value = pokok + jasa;
    }

    document.getElementById('edit_jumlah_pokok').addEventListener('input', calculateEditTotal);
    document.getElementById('edit_jumlah_jasa').addEventListener('input',  calculateEditTotal);
</script>
<?= $this->endSection() ?>
