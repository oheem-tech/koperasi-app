<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<style>
.guide-step {
    display: flex;
    margin-bottom: 24px;
    align-items: flex-start;
}
.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
    margin-right: 16px;
    margin-top: 4px;
}
.step-content h6 {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
    font-size: 1rem;
}
.step-content p {
    color: #475569;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0;
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h4 class="mb-0 fw-bold" style="color: var(--primary-dark);"><i class="fas fa-book-open text-primary me-2"></i> Panduan Operasional Sistem</h4>
                <p class="text-muted mt-2 mb-0">Petunjuk langkah demi langkah mengoperasikan aplikasi Koperasi Simpan Pinjam.</p>
            </div>
            
            <div class="card-body p-4">
                <div class="accordion" id="accordionPanduan">
                    
                    <!-- Panduan Anggota -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan1">
                                <i class="fas fa-user-plus me-2 text-primary"></i> 1. Menambahkan Anggota Baru
                            </button>
                        </h2>
                        <div id="panduan1" class="accordion-collapse collapse show" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Buka Menu Data Anggota</h6>
                                        <p>Arahkan kursor ke menu sidebar kiri di bagian <b>"Master Data"</b> lalu klik <b>"Data Anggota"</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Klik Tombol "Tambah Anggota"</h6>
                                        <p>Klik tombol warna biru di pojok kanan atas tabel. Isi form pendaftaran seperti Nama, No. HP, dan Alamat dengan lengkap. Pilih <b>Kelompok</b> dari dropdown yang sudah terisi dari Master Kelompok.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Akun Login Otomatis</h6>
                                        <p>Setelah formulir disimpan, sistem secara otomatis membuatkan akun login untuk anggota baru tersebut.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Master Kelompok -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanKelompok">
                                <i class="fas fa-layer-group me-2 text-primary"></i> 2. Pengaturan & Pemindahan Kelompok
                            </button>
                        </h2>
                        <div id="panduanKelompok" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Kelola Master Kelompok</h6>
                                        <p>Buka menu sidebar <b>"Master Kelompok"</b> (di bawah Data Anggota). Di sini Anda bisa menambah kelompok baru, mengedit nama, atau menghapus kelompok yang sudah kosong.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Edit Nama Kelompok (Cascade Update)</h6>
                                        <p>Jika Anda mengubah nama kelompok (misal: "PNS" → "ASN"), sistem akan <b>otomatis memperbarui</b> label kelompok pada seluruh anggota yang terhubung secara serentak tanpa harus edit satu per satu.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Pemindahan Anggota Massal (Bulk Assign)</h6>
                                        <p>Klik tombol kuning <b>"Pindah Massal Anggota"</b>. Saring berdasarkan kelompok asal, centang anggota yang ingin dipindahkan, pilih kelompok tujuan di panel bawah, lalu klik <b>"Proses Pemindahan"</b>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Simpanan -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan2">
                                <i class="fas fa-piggy-bank me-2 text-success"></i> 3. Transaksi Simpanan (Setor dan Tarik)
                            </button>
                        </h2>
                        <div id="panduan2" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Buka Menu Simpanan</h6>
                                        <p>Pada sidebar, pilih opsi <b>"Simpanan"</b> di bawah label Transaksi.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Input Transaksi Fisik</h6>
                                        <p>Pilih anggota menggunakan kolom <b>pencarian dinamis (live-search)</b>. Pilih Jenis Transaksi ('Setor' atau 'Tarik'), jenis simpanan, dan masukkan Nominal.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Otomatis Masuk Kas</h6>
                                        <p>Setelah simpanan diinput, dana tersebut akan <b>secara otomatis</b> masuk ke pencatatan Buku Kas Umum dan muncul di semua laporan keuangan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Input Massal -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanMassal">
                                <i class="fas fa-layer-group me-2 text-success"></i> 4. Input Massal (Potong Gaji)
                            </button>
                        </h2>
                        <div id="panduanMassal" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Setup Kelompok & Tanggal</h6>
                                        <p>Klik menu <b>"Input Massal"</b>. Pilih Kelompok anggota yang ingin diproses dan tentukan Tanggal Transaksi, lalu klik <b>"Buka Form Input"</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Centang Baris yang Diproses</h6>
                                        <p>Tabel akan menampilkan semua anggota beserta baris simpanan dan angsuran aktifnya. Gunakan checkbox utama (<b>Check All</b>) untuk memilih semua baris sekaligus — simpanan maupun angsuran ikut tercentang.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Panel Pelunasan & Konfirmasi</h6>
                                        <p>Untuk anggota yang ingin melunasi pinjaman, aktifkan <b>toggle "Lunas?"</b> pada baris angsurannya. Nilai akan otomatis berubah ke total pelunasan. Cek panel preview bawah, lalu klik <b>"Proses Pembayaran Massal"</b>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Pinjaman -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan3">
                                <i class="fas fa-hand-holding-usd me-2 text-warning"></i> 5. Pengajuan & Persetujuan Pinjaman
                            </button>
                        </h2>
                        <div id="panduan3" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Input Pengajuan Pinjaman</h6>
                                        <p>Buka menu <b>"Pinjaman"</b>. Klik <b>Ajukan Pinjaman</b>. Pilih anggota peminjam (dengan live-search), jenis pinjaman, lama tenor, dan isikan nominal yang ingin dipinjam.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Proses Persetujuan (Approve)</h6>
                                        <p>Pinjaman akan berstatus <b>"Pending"</b>. Admin/Bendahara klik tombol Approval (centang hijau) agar dana cair. Sistem akan <b>memotong</b> total Kas Koperasi saat pinjaman 'Disetujui'. Pinjaman yang ditolak tidak memengaruhi kas.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Pembayaran Angsuran / Pelunasan Awal</h6>
                                        <p>Pergi ke menu <b>"Angsuran"</b>. Untuk pelunasan bulanan biasa klik "Bayar Angsuran". Jika anggota ingin melunasi seluruh sisa pinjaman sekaligus, aktifkan <b>toggle "Lunas?"</b> — nominal akan otomatis berubah ke total sisa pinjaman.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Kas & Laporan -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan4">
                                <i class="fas fa-chart-line me-2" style="color:#8b5cf6;"></i> 6. Mengelola Kas Umum & Laporan
                            </button>
                        </h2>
                        <div id="panduan4" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Menginput Transaksi Manual di Kas</h6>
                                        <p>Jika terdapat pemasukan sumbangan luar, atau pengeluaran ATK harian, pergi ke <b>"Buku Kas Umum"</b> dan input manual transaksinya. Segala mutasi pinjaman dan simpanan bersifat <i>Read-Only</i> di sini karena dikelola di menu masing-masing.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Analisis Laporan Arus Kas & Neraca</h6>
                                        <p>Di akhir bulan, lihat performa keuangan lewat menu <b>Laporan → Arus Kas</b>. Neraca saldo juga tersedia untuk melihat realisasi Piutang dan Ekuitas secara menyeluruh.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Cetak Laporan (Device-Aware)</h6>
                                        <p>Semua laporan menyediakan tombol <b>"Cetak"</b>. Di PC/desktop, halaman cetak otomatis terbuka. Di perangkat HP, tersedia tombol <b>Share</b> untuk berbagi ke WhatsApp/aplikasi lain. Laporan versi gratis dilengkapi <i>watermark</i> "CirebonTech" sebagai tanda lisensi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Agar ketika dibuka satu list, yang lain akan menutup, sudah otomatis bawaan bootstrap accordion component (jika data-bs-parent di set).
</script>

<?= $this->endSection() ?>
