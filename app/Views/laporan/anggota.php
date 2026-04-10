<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Laporan Keuangan per Anggota</h2>
    <div>
        <a href="<?= base_url('laporan/anggota?action=print' . ($filterKelompok !== 'all' ? '&kelompok='.urlencode($filterKelompok) : '')) ?>" class="btn btn-secondary me-2" onclick="handlePrint(this.href); return false;">
            <i class="fas fa-print"></i> Cetak PDF
        </a>
        <a href="<?= base_url('laporan/anggota?action=excel' . ($filterKelompok !== 'all' ? '&kelompok='.urlencode($filterKelompok) : '')) ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<div class="alert alert-info py-2">
    <i class="fas fa-info-circle me-1"></i> Laporan Rekap Keuangan ini menyajikan akumulasi bersih simpanan dan hutang tiap anggota (Status Real-Time).
</div>

<!-- Filter Kelompok -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="get" action="<?= base_url('laporan/anggota') ?>" class="d-flex align-items-center gap-3 flex-wrap">
            <label class="fw-semibold mb-0 d-flex align-items-center gap-2" style="font-size:0.88rem; white-space:nowrap;">
                <i class="fas fa-filter text-primary"></i> Filter Kelompok
                <?php if(!is_premium()): ?>
                <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px;"><i class="fas fa-crown" style="color:#fbbf24;font-size:0.55rem;"></i> PRO</span>
                <?php endif; ?>
            </label>
            <?php if(is_premium()): ?>
            <select name="kelompok" class="form-select form-select-sm" style="max-width:220px;" onchange="this.form.submit()">
                <option value="all" <?= $filterKelompok === 'all' ? 'selected' : '' ?>>— Semua Kelompok —</option>
                <?php foreach($kelompokList as $k): ?>
                <option value="<?= esc($k['nama_kelompok']) ?>" <?= $filterKelompok === $k['nama_kelompok'] ? 'selected' : '' ?>>
                    <?= esc($k['nama_kelompok']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if($filterKelompok !== 'all'): ?>
            <a href="<?= base_url('laporan/anggota') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Reset
            </a>
            <span class="badge" style="background:linear-gradient(135deg,#059669,#0d9488);color:#fff;padding:5px 10px;border-radius:8px;font-size:0.78rem;">
                <i class="fas fa-layer-group me-1"></i><?= esc($filterKelompok) ?>
            </span>
            <?php endif; ?>
            <?php else: ?>
            <select class="form-select form-select-sm bg-light" style="max-width:220px;" disabled>
                <option>— Semua Kelompok —</option>
            </select>
            <small class="text-muted" style="font-size:0.75rem;"><i class="fas fa-lock me-1"></i>Filter kelompok hanya tersedia di Versi PRO.</small>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" id="datatable">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle" style="width: 50px;">No</th>
                        <th rowspan="2" class="align-middle">No. Anggota</th>
                        <th rowspan="2" class="align-middle">Nama Lengkap</th>
                        <th colspan="3">Saldo Simpanan</th>
                        <th rowspan="2" class="align-middle text-danger">Total Sisa<br>Pinjaman</th>
                    </tr>
                    <tr>
                        <th>Pokok</th>
                        <th>Wajib</th>
                        <th>Sukarela / Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekapAnggota)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data anggota.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($rekapAnggota as $r): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= esc($r['no_anggota']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($r['nama_lengkap']) ?></div>
                                    <?php if ($r['status'] == 'nonaktif'): ?>
                                        <span class="badge bg-danger" style="font-size: 0.65em;">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-semibold text-success">Rp <?= number_format($r['pokok'], 0, ',', '.') ?></td>
                                <td class="text-end fw-semibold text-success">Rp <?= number_format($r['wajib'], 0, ',', '.') ?></td>
                                <td class="text-end fw-semibold text-success">Rp <?= number_format($r['sukarela'], 0, ',', '.') ?></td>
                                <td class="text-end fw-bold text-danger">
                                    <?php if (empty($r['list_pinjaman'])): ?>
                                        Rp 0
                                    <?php else: ?>
                                        <?php foreach($r['list_pinjaman'] as $lp): ?>
                                            <div style="font-size: 0.85em; border-bottom: 1px dashed #f5c2c7; padding-bottom: 2px; margin-bottom: 2px;">
                                                PJ-<?= str_pad($lp['pinjaman_id'], 4, '0', STR_PAD_LEFT) ?> : Rp <?= number_format($lp['sisa'], 0, ',', '.') ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="mt-1">Rp <?= number_format($r['sisa_pinjaman'], 0, ',', '.') ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL KESELURUHAN :</td>
                        <td class="text-end text-success">Rp <?= number_format($total_pokok, 0, ',', '.') ?></td>
                        <td class="text-end text-success">Rp <?= number_format($total_wajib, 0, ',', '.') ?></td>
                        <td class="text-end text-success">Rp <?= number_format($total_sukarela, 0, ',', '.') ?></td>
                        <td class="text-end text-danger">Rp <?= number_format($total_sisa_pinjaman, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        const table = document.getElementById('datatable');
        if (table && !$.fn.DataTable.isDataTable(table)) {
            $(table).DataTable({ 
                "pageLength": 50,
                "ordering": false
            });
        }
    }
});
</script>

<?= $this->endSection() ?>
