<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Input Kas Manual</h2>
    <a href="<?= base_url('kas') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('kas/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Jenis Kas</label>
                <select name="jenis" id="jenis_kas" class="form-select" required>
                    <option value="" disabled selected>Pilih Jenis</option>
                    <option value="masuk">Pemasukan (Debet)</option>
                    <option value="keluar">Pengeluaran (Kredit)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Akuntansi</label>
                <select name="kategori" id="kategori_kas" class="form-select" required>
                    <option value="" disabled selected>Pilih Jenis Kas Dulu</option>
                </select>
                <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Mempengaruhi titik alokasi di laporan neraca letak uang ini dicatat.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Nominal (Rp)</label>
                <input type="text" class="form-control" name="nominal" id="nominal" required placeholder="Contoh: 50.000">
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan / Uraian</label>
                <textarea class="form-control" name="keterangan" rows="3" required placeholder="Misal: Pembayaran listrik, Beli ATK, Modal awal..."></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Format nominal ke Rupiah
    document.getElementById('nominal').addEventListener('input', function(e) {
        let value = this.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        this.value = rupiah;
    });

    const optMasuk = `
        <option value="pendapatan_biaya">Pendapatan Lain-lain (Menambah Laba/SHU)</option>
        <option value="kewajiban_hutang">Titipan / Pinjaman Koperasi (Menambah Kewajiban)</option>
        <option value="aset_modal">Modal Koperasi / Hibah (Menambah Ekuitas)</option>
    `;
    const optKeluar = `
        <option value="pendapatan_biaya">Biaya Operasional (Memotong Laba/SHU)</option>
        <option value="kewajiban_hutang">Pembayaran Hutang Titipan (Mengurangi Kewajiban)</option>
        <option value="aset_modal">Pengambilan Modal / Hibah (Mengurangi Ekuitas)</option>
    `;

    document.getElementById('jenis_kas').addEventListener('change', function() {
        if(this.value === 'masuk') {
            document.getElementById('kategori_kas').innerHTML = optMasuk;
        } else if(this.value === 'keluar') {
            document.getElementById('kategori_kas').innerHTML = optKeluar;
        } else {
            document.getElementById('kategori_kas').innerHTML = '<option value="" disabled selected>Pilih Jenis Kas Dulu</option>';
        }
    });
</script>
<?= $this->endSection() ?>
