<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Input Pembayaran Angsuran</h2>

<div class="card shadow-sm" style="max-width: 650px;">
    <div class="card-body">
        <form action="<?= base_url('angsuran/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- STEP 1: Pilih Anggota -->
            <div class="mb-3">
                <label class="form-label fw-semibold">1. Pilih Anggota</label>
                <select class="form-select searchable-select" id="anggota_id" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['no_anggota'] ?> - <?= $a['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- STEP 2: Pilih Pinjaman berdasarkan Jenis (diisi via AJAX) -->
            <div class="mb-3" id="section-pinjaman" style="display:none;">
                <label class="form-label fw-semibold">2. Pilih Pinjaman Aktif Anggota</label>
                <select class="form-select" name="pinjaman_id" id="pinjaman_id" required>
                    <option value="">-- Pilih Pinjaman --</option>
                </select>
                <small class="text-muted">Menampilkan pinjaman berstatus aktif (disetujui) milik anggota terpilih.</small>
            </div>

            <!-- STEP 3: Detail Angsuran (diisi otomatis) -->
            <div id="section-detail" style="display:none;">
                <hr>
                <div class="mb-3">
                    <label class="form-label">Tanggal Pembayaran</label>
                    <input type="date" class="form-control" name="tanggal_bayar" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pembayaran Cicilan Ke-</label>
                    <input type="number" class="form-control" name="cicilan_ke" id="cicilan_ke" min="1" required readonly>
                    <small class="text-muted" id="info-cicilan"></small>
                </div>
                
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_pelunasan_toggle" value="1">
                    <input type="hidden" name="is_pelunasan" id="is_pelunasan" value="0">
                    <label class="form-check-label fw-bold text-danger" for="is_pelunasan_toggle">Pelunasan Dipercepat / Terakhir</label>
                    <small class="d-block text-muted">Centang untuk melunasi sisa pinjaman sekaligus sesuai kebijakan master.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Angsuran Pokok (Rp)</label>
                        <input type="number" class="form-control" name="jumlah_pokok" id="jumlah_pokok" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Angsuran Jasa/Bunga (Rp)</label>
                        <input type="number" class="form-control" name="jumlah_jasa" id="jumlah_jasa" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Angsuran (Rp)</label>
                        <input type="number" class="form-control fw-bold" name="jumlah_bayar" id="jumlah_bayar" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Denda Keterlambatan (Rp) <small class="text-muted">(Jika ada)</small></label>
                    <input type="number" class="form-control" name="denda" value="0">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Proses Pembayaran</button>
                    <a href="<?= base_url('angsuran') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const BASE_URL = '<?= base_url() ?>';

    function calculateTotal() {
        var pokok = parseInt(document.getElementById('jumlah_pokok').value) || 0;
        var jasa  = parseInt(document.getElementById('jumlah_jasa').value)  || 0;
        document.getElementById('jumlah_bayar').value = pokok + jasa;
    }

    document.getElementById('jumlah_pokok').addEventListener('input', calculateTotal);
    document.getElementById('jumlah_jasa').addEventListener('input',  calculateTotal);

    // Variables to store current loan calculation data
    var currentLoanData = {
        pokok_normal: 0,
        jasa_normal: 0,
        bayar_normal: 0,
        pokok_lunas: 0,
        jasa_lunas: 0,
        bayar_lunas: 0,
        is_last_installment: false
    };

    // Step 1: Anggota dipilih → ambil daftar pinjamannya via AJAX
    document.getElementById('anggota_id').addEventListener('change', function() {
        var anggotaId = this.value;
        var pinjamanSelect = document.getElementById('pinjaman_id');
        var sectionPinjaman = document.getElementById('section-pinjaman');
        var sectionDetail   = document.getElementById('section-detail');

        // Reset
        pinjamanSelect.innerHTML = '<option value="">-- Pilih Pinjaman --</option>';
        sectionPinjaman.style.display = 'none';
        sectionDetail.style.display   = 'none';

        if (!anggotaId) return;

        fetch(BASE_URL + 'angsuran/api/pinjaman/' + anggotaId)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    pinjamanSelect.innerHTML = '<option value="">Tidak ada pinjaman aktif</option>';
                } else {
                    data.forEach(function(p) {
                        var label = '[' + p.jenis_pinjaman + '] Tenor ' + p.lama_tenor + ' bln'
                                  + ' | Cicilan ke-' + p.cicilan_berikutnya + ' dari ' + p.lama_tenor;
                        var opt = document.createElement('option');
                        opt.value = p.id;
                        opt.text  = label;
                        opt.dataset.cicilanKe    = p.cicilan_berikutnya;
                        opt.dataset.tenor        = p.lama_tenor;
                        opt.dataset.pokok        = p.pokok_per_bulan;
                        opt.dataset.jasa         = p.jasa_per_bulan;
                        opt.dataset.cicilanBayar = p.cicilan_per_bulan;
                        
                        // Data pelunasan
                        opt.dataset.pokokLunas   = p.sisa_pokok_lunas;
                        opt.dataset.jasaLunas    = p.sisa_jasa_lunas;
                        opt.dataset.bayarLunas   = p.total_lunas;
                        pinjamanSelect.appendChild(opt);
                    });
                }
                sectionPinjaman.style.display = 'block';
            })
            .catch(() => alert('Gagal memuat data pinjaman.'));
    });

    // Handle toggle pelunasan
    document.getElementById('is_pelunasan_toggle').addEventListener('change', function() {
        var isChecked = this.checked;
        document.getElementById('is_pelunasan').value = isChecked ? '1' : '0';
        
        if (isChecked) {
            document.getElementById('jumlah_pokok').value  = currentLoanData.pokok_lunas;
            document.getElementById('jumlah_jasa').value   = currentLoanData.jasa_lunas;
            document.getElementById('jumlah_bayar').value  = currentLoanData.bayar_lunas;
            document.getElementById('info-cicilan').innerHTML = '<span class="text-danger fw-bold"><i class="fas fa-exclamation-circle"></i> Mode Pelunasan Dipercepat Aktif</span>';
        } else {
            document.getElementById('jumlah_pokok').value  = currentLoanData.pokok_normal;
            document.getElementById('jumlah_jasa').value   = currentLoanData.jasa_normal;
            document.getElementById('jumlah_bayar').value  = currentLoanData.bayar_normal;
            document.getElementById('info-cicilan').textContent =
                'Cicilan berikutnya: ke-' + document.getElementById('cicilan_ke').value + ' dari ' + currentLoanData.tenor + ' bulan tenor.';
        }
    });

    // Step 2: Pinjaman dipilih → isi detail otomatis
    document.getElementById('pinjaman_id').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        var sectionDetail = document.getElementById('section-detail');

        if (!opt.value) {
            sectionDetail.style.display = 'none';
            return;
        }

        var cicilanKe = parseInt(opt.dataset.cicilanKe);
        var tenor     = parseInt(opt.dataset.tenor);

        currentLoanData = {
            tenor: tenor,
            pokok_normal: opt.dataset.pokok,
            jasa_normal: opt.dataset.jasa,
            bayar_normal: opt.dataset.cicilanBayar,
            pokok_lunas: opt.dataset.pokokLunas,
            jasa_lunas: opt.dataset.jasaLunas,
            bayar_lunas: opt.dataset.bayarLunas,
            is_last_installment: (cicilanKe >= tenor)
        };

        document.getElementById('cicilan_ke').value = cicilanKe;
        
        var toggle = document.getElementById('is_pelunasan_toggle');
        
        // Cek jika ini adalah cicilan terakhir normal
        if (currentLoanData.is_last_installment) {
            toggle.checked = true;
            // Kita bisa disable atau biarkan, tapi lebih baik beri info saja
            // biarkan tetap bisa di-toggle up if user somehow wants to? 
            // Actually user specified it should automatically select.
        } else {
            toggle.checked = false;
        }
        
        // Trigger the change event manually to update values
        toggle.dispatchEvent(new Event('change'));

        sectionDetail.style.display = 'block';
    });
</script>
<?= $this->endSection() ?>
