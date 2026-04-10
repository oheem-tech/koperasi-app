<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h2 class="mb-0"><i class="fas fa-chart-line"></i> Laporan Arus Kas</h2>
    <div class="d-flex flex-wrap gap-2">
        <form class="d-flex gap-2" method="get">
            <input type="month" name="bulan" class="form-control" value="<?= $bulan !== 'all' ? $bulan : date('Y-m') ?>">
            <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="?bulan=all" class="btn btn-secondary text-nowrap">Semua Waktu</a>
        </form>
        <div class="d-none d-md-block vr mx-1"></div>
        <a href="?bulan=<?= $bulan ?>&action=print" class="btn btn-outline-dark" id="btn-print-kas" onclick="handlePrint(this.href); return false;"><i class="fas fa-print me-1"></i> Print / PDF</a>
        <a href="?bulan=<?= $bulan ?>&action=excel" class="btn btn-success"><i class="fas fa-file-excel me-1"></i> Excel</a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h6>Total Kas Masuk</h6><h4>Rp <?= number_format($totalMasuk, 0, ',', '.') ?></h4></div>
                    <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h6>Total Kas Keluar</h6><h4>Rp <?= number_format($totalKeluar, 0, ',', '.') ?></h4></div>
                    <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white <?= $saldoBersih >= 0 ? 'bg-primary' : 'bg-warning' ?> shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h6>Saldo Bersih</h6><h4>Rp <?= number_format($saldoBersih, 0, ',', '.') ?></h4></div>
                    <i class="fas fa-wallet fa-2x opacity-50"></i>
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
