<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-chart-line"></i> Laporan Arus Kas</h2>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form class="row gx-2 gy-2 align-items-center" method="get">
            <?php
                // Tentukan default select dari filter saat ini
                $selType = is_array($periode) ? $periode['type'] : 'bulan';
                $selBulan = is_array($periode) ? $periode['bulan'] : date('m');
                $selSemester = (is_array($periode) && isset($periode['semester'])) ? $periode['semester'] : '1';
                $selTahun = is_array($periode) ? $periode['tahun'] : date('Y');
                $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            ?>
            <div class="col-auto">
                <select name="filter_type" id="filter_type" class="form-select" onchange="toggleFilterType()">
                    <option value="bulan" <?= $selType == 'bulan' ? 'selected' : '' ?>>Bulan</option>
                    <option value="semester" <?= $selType == 'semester' ? 'selected' : '' ?>>Semester</option>
                    <option value="tahun" <?= $selType == 'tahun' ? 'selected' : '' ?>>Tahun</option>
                </select>
            </div>
            
            <div class="col-auto" id="col_filter_bulan" style="<?= $selType !== 'bulan' ? 'display:none;' : '' ?>">
                <select name="filter_bulan" id="filter_bulan" class="form-select">
                    <?php foreach($namaBulan as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $selBulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-auto" id="col_filter_semester" style="<?= $selType !== 'semester' ? 'display:none;' : '' ?>">
                <select name="filter_semester" id="filter_semester" class="form-select">
                    <option value="1" <?= $selSemester == '1' ? 'selected' : '' ?>>Semester 1 (Jan-Jun)</option>
                    <option value="2" <?= $selSemester == '2' ? 'selected' : '' ?>>Semester 2 (Jul-Des)</option>
                </select>
            </div>
            
            <div class="col-auto" id="col_filter_tahun">
                <select name="filter_tahun" id="filter_tahun" class="form-select">
                    <?php for($y = date('Y') + 2; $y >= 2010; $y--): ?>
                        <option value="<?= $y ?>" <?= $selTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="?bulan=all" class="btn btn-secondary">Semua Waktu</a>
            </div>
            
            <?php
                $qsParams = $_GET;
                unset($qsParams['action']);
                $qs = http_build_query($qsParams);
                if (empty($qs)) $qs = "bulan=" . (is_array($periode) ? date('Y-m') : 'all');
            ?>
            <div class="col-auto ms-auto d-flex gap-2">
                <a href="?<?= $qs ?>&action=print" class="btn btn-outline-dark" id="btn-print-kas" onclick="handlePrint(this.href); return false;"><i class="fas fa-print me-1"></i> Print / PDF</a>
                <a href="?<?= $qs ?>&action=excel" class="btn btn-success"><i class="fas fa-file-excel me-1"></i> Excel</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFilterType() {
    const type = document.getElementById('filter_type').value;
    document.getElementById('col_filter_bulan').style.display = type === 'bulan' ? 'block' : 'none';
    document.getElementById('col_filter_semester').style.display = type === 'semester' ? 'block' : 'none';
}
</script>

<!-- Summary Cards -->
<div class="row row-cols-1 row-cols-md-5 mb-4 g-2">
    <div class="col">
        <div class="card bg-white border-info shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-info flex-shrink-0 pe-2"><i class="fas fa-history fa-2x opacity-50"></i></div>
                    <div class="text-end" style="min-width: 0;"><h6 class="text-muted mb-1 text-truncate">Saldo Awal</h6><h5 class="mb-0 text-info fw-bold" style="word-break: break-word; font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.25rem);">Rp <?= number_format($awalSaldo, 0, ',', '.') ?></h5></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-shrink-0 pe-2"><i class="fas fa-arrow-down fa-2x opacity-50"></i></div>
                    <div class="text-end" style="min-width: 0;"><h6 class="mb-1 text-truncate">Kas Masuk</h6><h5 class="mb-0 fw-bold" style="word-break: break-word; font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.25rem);">Rp <?= number_format($totalMasuk, 0, ',', '.') ?></h5></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-danger shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-shrink-0 pe-2"><i class="fas fa-arrow-up fa-2x opacity-50"></i></div>
                    <div class="text-end" style="min-width: 0;"><h6 class="mb-1 text-truncate">Kas Keluar</h6><h5 class="mb-0 fw-bold" style="word-break: break-word; font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.25rem);">Rp <?= number_format($totalKeluar, 0, ',', '.') ?></h5></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white <?= $saldoBersih >= 0 ? 'bg-primary' : 'bg-warning text-dark' ?> shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-shrink-0 pe-2"><i class="fas fa-exchange-alt fa-2x opacity-50"></i></div>
                    <div class="text-end" style="min-width: 0;"><h6 class="mb-1 text-truncate">Mutasi Bersih</h6><h5 class="mb-0 fw-bold" style="word-break: break-word; font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.25rem);">Rp <?= number_format($saldoBersih, 0, ',', '.') ?></h5></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-dark shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-shrink-0 pe-2"><i class="fas fa-wallet fa-2x opacity-50"></i></div>
                    <div class="text-end" style="min-width: 0;"><h6 class="mb-1 text-truncate">Saldo Akhir</h6><h5 class="mb-0 fw-bold" style="word-break: break-word; font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.25rem);">Rp <?= number_format($saldoAkhir, 0, ',', '.') ?></h5></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Simpanan Masuk -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white"><i class="fas fa-plus-circle"></i> Simpanan Masuk (Setoran)</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Anggota</th><th>Jenis</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php foreach($simpananMasuk as $r): ?>
                <tr><td><?= date('d M Y', strtotime($r['tanggal_transaksi'])) ?></td><td><?= $r['nama_lengkap'] ?></td><td><?= $r['nama_simpanan'] ?></td><td class="text-end">Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td></tr>
                <?php endforeach; ?>
                <?php if(empty($simpananMasuk)): ?><tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
            <tfoot class="fw-bold"><tr><td colspan="3">Total</td><td class="text-end text-success">Rp <?= number_format(array_sum(array_column($simpananMasuk,'jumlah')), 0, ',', '.') ?></td></tr></tfoot>
        </table>
    </div>
</div>

<!-- Detail Angsuran Masuk -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white"><i class="fas fa-money-bill-wave"></i> Angsuran Diterima</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Anggota</th><th>Kode</th><th>Cicilan Ke-</th><th>Pokok</th><th>Jasa</th><th class="text-end">Total</th></tr></thead>
            <tbody>
                <?php foreach($angsuranMasuk as $r): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($r['tanggal_bayar'])) ?></td>
                    <td><?= $r['nama_lengkap'] ?></td>
                    <td><span class="badge bg-secondary">PJ-<?= str_pad($r['pinjaman_id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                    <td><?= $r['cicilan_ke'] ?></td>
                    <td>Rp <?= number_format($r['jumlah_pokok'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($r['jumlah_jasa'], 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format($r['jumlah_bayar'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($angsuranMasuk)): ?><tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
            <tfoot class="fw-bold"><tr><td colspan="6">Total</td><td class="text-end text-info">Rp <?= number_format(array_sum(array_column($angsuranMasuk,'jumlah_bayar')), 0, ',', '.') ?></td></tr></tfoot>
        </table>
    </div>
</div>

<!-- Detail Kas Operasional / Manual Masuk -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white"><i class="fas fa-plus-square"></i> Kas Lainnya Masuk</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Keterangan</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php foreach($manualMasuk as $r): ?>
                <tr><td><?= date('d M Y', strtotime($r['tanggal'])) ?></td><td><?= $r['keterangan'] ?></td><td class="text-end text-success">Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td></tr>
                <?php endforeach; ?>
                <?php if(empty($manualMasuk)): ?><tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
            <tfoot class="fw-bold"><tr><td colspan="2">Total</td><td class="text-end text-success">Rp <?= number_format(array_sum(array_column($manualMasuk,'nominal')), 0, ',', '.') ?></td></tr></tfoot>
        </table>
    </div>
</div>

<!-- Detail Simpanan Keluar -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white"><i class="fas fa-minus-circle"></i> Simpanan Keluar (Penarikan)</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Anggota</th><th>Jenis</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php foreach($simpananKeluar as $r): ?>
                <tr><td><?= date('d M Y', strtotime($r['tanggal_transaksi'])) ?></td><td><?= $r['nama_lengkap'] ?></td><td><?= $r['nama_simpanan'] ?></td><td class="text-end">Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td></tr>
                <?php endforeach; ?>
                <?php if(empty($simpananKeluar)): ?><tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
            <tfoot class="fw-bold"><tr><td colspan="3">Total</td><td class="text-end text-danger">Rp <?= number_format(array_sum(array_column($simpananKeluar,'jumlah')), 0, ',', '.') ?></td></tr></tfoot>
        </table>
    </div>
</div>

<!-- Detail Kas Operasional / Manual Keluar -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white"><i class="fas fa-minus-square"></i> Kas Lainnya Keluar / Pengeluaran Operasional</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Keterangan</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php foreach($manualKeluar as $r): ?>
                <tr><td><?= date('d M Y', strtotime($r['tanggal'])) ?></td><td><?= $r['keterangan'] ?></td><td class="text-end text-danger">Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td></tr>
                <?php endforeach; ?>
                <?php if(empty($manualKeluar)): ?><tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
            <tfoot class="fw-bold"><tr><td colspan="2">Total</td><td class="text-end text-danger">Rp <?= number_format(array_sum(array_column($manualKeluar,'nominal')), 0, ',', '.') ?></td></tr></tfoot>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
