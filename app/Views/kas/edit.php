<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Kas Manual</h2>
    <a href="<?= base_url('kas') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('kas/update/' . $kas['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d', strtotime($kas['tanggal'])) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Jenis Kas</label>
                <select name="jenis" id="jenis_kas" class="form-select" required>
                    <option value="masuk" <?= $kas['jenis'] == 'masuk' ? 'selected' : '' ?>>Pemasukan (Debet)</option>
                    <option value="keluar" <?= $kas['jenis'] == 'keluar' ? 'selected' : '' ?>>Pengeluaran (Kredit)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori Akuntansi</label>
                <select name="kategori" id="kategori_kas" class="form-select" required>
                    <!-- Options populated by JS below -->
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nominal (Rp)</label>
                <input type="text" class="form-control" name="nominal" id="nominal" required value="<?= number_format($kas['nominal'], 0, ',', '.') ?>" placeholder="Contoh: 50.000">
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan / Uraian</label>
                <textarea class="form-control" name="keterangan" rows="3" required placeholder="Misal: Pembayaran listrik, Beli ATK, Modal awal..."><?= htmlspecialchars($kas['keterangan']) ?></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-warning px-4"><i class="fas fa-save"></i> Perbarui Transaksi</button>
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

    const currentKategori = '<?= $kas['kategori'] ?? "" ?>';
    
    const optMasuk = `
        <option value="pendapatan_biaya">Pendapatan Lain-lain (Menambah Laba/SHU)</option>
        <option value="kewajiban_hutang">Titipan / Pinjaman Koperasi (Menambah Kewajiban)</option>
        <option value="aset_modal">Modal Koperasi / Hibah (Menambah Ekuitas)</option>
        <option value="sistem_lainnya">Sistem / Lainnya (Default)</option>
    `;
    const optKeluar = `
        <option value="pendapatan_biaya">Biaya Operasional (Memotong Laba/SHU)</option>
        <option value="kewajiban_hutang">Pembayaran Hutang Titipan (Mengurangi Kewajiban)</option>
        <option value="aset_modal">Pengambilan Modal / Hibah (Mengurangi Ekuitas)</option>
        <option value="sistem_lainnya">Sistem / Lainnya (Default)</option>
    `;

    function updateKategoriOptions() {
        const jenis = document.getElementById('jenis_kas').value;
        const katSelect = document.getElementById('kategori_kas');
        if(jenis === 'masuk') {
            katSelect.innerHTML = optMasuk;
        } else {
            katSelect.innerHTML = optKeluar;
        }
        
        // Coba set selected index sesuai data lama
        if (currentKategori) {
            for(let i=0; i<katSelect.options.length; i++){
                if(katSelect.options[i].value === currentKategori){
                    katSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }

    document.getElementById('jenis_kas').addEventListener('change', updateKategoriOptions);
    
    // Inisialisasi saat load
    updateKategoriOptions();
</script>
<?= $this->endSection() ?>
