<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h2 class="mb-0"><i class="fas fa-hand-holding-usd"></i> Laporan Sisa Hasil Usaha (SHU)</h2>
    <div class="d-flex flex-wrap gap-2">
        <form class="d-flex gap-2" method="get">
            <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>" min="2020" max="2099" style="width:120px;">
            <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
        </form>
        <div class="d-none d-md-block vr mx-1"></div>
        <a href="?tahun=<?= $tahun ?>&action=print" class="btn btn-outline-dark" onclick="handlePrint(this.href); return false;"><i class="fas fa-print me-1"></i> Print / PDF</a>
        <a href="?tahun=<?= $tahun ?>&action=excel" class="btn btn-success"><i class="fas fa-file-excel me-1"></i> Excel</a>
    </div>
</div>

<!-- Alokasi SHU Global -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark fw-bold">
        <i class="fas fa-chart-pie"></i> Alokasi SHU Tahun <?= $tahun ?>
        <span class="float-end fw-normal">Total SHU: <strong>Rp <?= number_format($alokasiSHU['total_shu'], 0, ',', '.') ?></strong></span>
    </div>
    <div class="card-body">
        <div class="row g-3 text-center">
            <!-- Jasa Modal & Jasa Anggota -->
            <div class="col-6 col-md-3">
                <div class="border border-success rounded p-3 h-100">
                    <div class="text-muted small">Jasa Modal (<?= $settings['shu_jasa_modal'] ?? 20 ?>%)</div>
                    <div class="fs-6 fw-bold text-success">Rp <?= number_format($alokasiSHU['jasa_modal'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border border-primary rounded p-3 h-100">
                    <div class="text-muted small">Jasa Anggota (<?= $settings['shu_jasa_anggota'] ?? 25 ?>%)</div>
                    <div class="fs-6 fw-bold text-primary">Rp <?= number_format($alokasiSHU['jasa_anggota'], 0, ',', '.') ?></div>
                </div>
            </div>

            <!-- Pengurus (3 item terpisah) -->
            <div class="col-12 col-sm-6 col-md-2">
                <div class="border border-info rounded p-3 h-100">
                    <div class="text-muted small">Pengurus (<?= $settings['shu_pengurus_anggota'] ?? 10 ?>%)</div>
                    <div class="fs-6 fw-bold text-info">Rp <?= number_format($alokasiSHU['pengurus_anggota'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="border border-info rounded p-3 h-100">
                    <div class="text-muted small">Pengawas (<?= $settings['shu_pengawas'] ?? 5 ?>%)</div>
                    <div class="fs-6 fw-bold text-info">Rp <?= number_format($alokasiSHU['pengawas'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="border border-info rounded p-3 h-100">
                    <div class="text-muted small">Pembina (<?= $settings['shu_pembina'] ?? 5 ?>%)</div>
                    <div class="fs-6 fw-bold text-info">Rp <?= number_format($alokasiSHU['pembina'], 0, ',', '.') ?></div>
                </div>
            </div>

            <!-- Dana Sosial, Dana Pendidikan & Dana Cadangan -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="border border-warning rounded p-3 h-100">
                    <div class="text-muted small">Dana Sosial (<?= $settings['shu_dana_sosial'] ?? 5 ?>%)</div>
                    <div class="fs-6 fw-bold text-warning">Rp <?= number_format($alokasiSHU['dana_sosial'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="border border-purple rounded p-3 h-100" style="border-color:#6f42c1!important;">
                    <div class="text-muted small">Dana Pendidikan (<?= $settings['shu_dana_pendidikan'] ?? 5 ?>%)</div>
                    <div class="fs-6 fw-bold" style="color:#6f42c1;">Rp <?= number_format($alokasiSHU['dana_pendidikan'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="border border-danger rounded p-3 h-100">
                    <div class="text-muted small">Dana Cadangan (<?= $settings['shu_cadangan'] ?? 20 ?>%)</div>
                    <div class="fs-6 fw-bold text-danger">Rp <?= number_format($alokasiSHU['dana_cadangan'], 0, ',', '.') ?></div>
                </div>
            </div>

            <!-- Total dialokasikan -->
            <div class="col-6 col-md-3">
                <div class="border border-dark rounded p-3 h-100 bg-light">
                    <div class="text-muted small">Total Dialokasikan</div>
                    <div class="fs-6 fw-bold text-dark">Rp <?= number_format($alokasiSHU['total_dialokasikan'], 0, ',', '.') ?></div>
                    <?php $sisa = $alokasiSHU['total_shu'] - $alokasiSHU['total_dialokasikan']; ?>
                    <?php if($sisa != 0): ?>
                        <div class="small <?= $sisa > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $sisa > 0 ? 'Sisa: ' : 'Lebih: ' ?> Rp <?= number_format(abs($sisa), 0, ',', '.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SHU Per Anggota -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white"><i class="fas fa-users"></i> Rincian SHU per Anggota (Bagian Jasa Anggota + Jasa Modal)</div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-hover mb-0" style="white-space: nowrap;">
            <thead class="table-dark">
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th class="text-center">Hak Jasa</th>
                    <th class="text-end">SHU Jasa Modal</th>
                    <th class="text-end">SHU Jasa Anggota</th>
                    <th class="text-end">SHU Jasa Jabatan</th>
                    <th class="text-end fw-bold">Total SHU Diterima</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($shuPerAnggota as $r): ?>
                <tr>
                    <td><?= $r['no_anggota'] ?></td>
                    <td><?= $r['nama_lengkap'] ?> <?= $r['jabatan'] != 'anggota' ? '<span class="badge bg-secondary ms-1">'.ucfirst($r['jabatan']).'</span>' : '' ?></td>
                    <td class="text-center">
                        <small class="text-muted d-block">Simpanan: Rp <?= number_format($r['saldo_simpanan'], 0, ',', '.') ?></small>
                        <small class="text-muted d-block">Angsuran: Rp <?= number_format($r['total_jasa'], 0, ',', '.') ?></small>
                    </td>
                    <td class="text-end text-success">Rp <?= number_format($r['shu_jasa_modal'], 0, ',', '.') ?></td>
                    <td class="text-end text-primary">Rp <?= number_format($r['shu_jasa_anggota'], 0, ',', '.') ?></td>
                    <td class="text-end text-info">Rp <?= number_format($r['shu_jabatan'], 0, ',', '.') ?></td>
                    <td class="text-end fw-bold text-warning">Rp <?= number_format($r['shu_total'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($shuPerAnggota)): ?>
                <tr><td colspan="7" class="text-center text-muted">Belum ada data anggota aktif yang berhak menerima SHU pada tahun <?= $tahun ?></td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot class="fw-bold table-warning">
                <tr>
                    <td colspan="3">Total Estimasi Pembagian Perorangan</td>
                    <td class="text-end">Rp <?= number_format(array_sum(array_column($shuPerAnggota,'shu_jasa_modal')), 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(array_sum(array_column($shuPerAnggota,'shu_jasa_anggota')), 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(array_sum(array_column($shuPerAnggota,'shu_jabatan')), 0, ',', '.') ?></td>
                    <td class="text-end text-danger fs-6">Rp <?= number_format(array_sum(array_column($shuPerAnggota,'shu_total')), 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
