<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="mb-0"><i class="fas fa-balance-scale"></i> Neraca Koperasi</h2>
        <small class="text-muted">Per Tanggal: <?= date('d F Y', strtotime($cutoff)) ?></small>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <?php
            $selisih = $totalAktiva - $totalPasiva;
            $balanced = abs($selisih) < 1;
        ?>
        <span class="badge fs-6 <?= $balanced ? 'bg-success' : 'bg-danger' ?> me-2">
            <i class="fas <?= $balanced ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
            <?= $balanced ? 'BALANCE' : 'TIDAK BALANCE (Selisih: Rp '.number_format(abs($selisih),0,',','.').')' ?>
        </span>
        
        <form class="d-flex gap-2" method="get">
            <input type="date" name="cutoff" class="form-control form-control-sm" value="<?= $cutoff ?>" required title="Tanggal Cut-Off Perhitungan">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </form>

        <div class="d-none d-md-block vr mx-1"></div>
        <a href="?cutoff=<?= $cutoff ?>&action=print" class="btn btn-sm btn-outline-dark" onclick="handlePrint(this.href); return false;"><i class="fas fa-print me-1"></i> Print</a>
        <a href="?cutoff=<?= $cutoff ?>&action=excel" class="btn btn-sm btn-success"><i class="fas fa-file-excel me-1"></i> Excel</a>
    </div>
</div>

<div class="row">
    <!-- AKTIVA -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-success h-100">
            <div class="card-header bg-success text-white fw-bold">
                <i class="fas fa-plus-circle"></i> AKTIVA (Harta)
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th colspan="2" class="text-muted small">I. AKTIVA LANCAR</th></tr>
                    </thead>
                    <tbody>
                        <!-- Kas & Bank -->
                        <tr>
                            <td class="ps-3">Kas & Bank</td>
                            <td class="text-end fw-semibold">Rp <?= number_format($kasBank, 0, ',', '.') ?></td>
                        </tr>
                        <!-- Piutang Pinjaman -->
                        <tr>
                            <td class="ps-3">Piutang Pinjaman Anggota</td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Total Dikucurkan</td>
                            <td class="text-end text-muted small">Rp <?= number_format($totalDikucurkan, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Dikurangi Angsuran Pokok</td>
                            <td class="text-end text-danger small">(Rp <?= number_format($totalPokokTerbayar, 0, ',', '.') ?>)</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3 fw-semibold">Sisa Piutang Pinjaman</td>
                            <td class="text-end fw-semibold">Rp <?= number_format($piutangPinjaman, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-success fw-bold">
                        <tr>
                            <td class="fs-6">TOTAL AKTIVA</td>
                            <td class="text-end fs-6">Rp <?= number_format($totalAktiva, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- PASIVA -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-danger h-100">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="fas fa-minus-circle"></i> PASIVA (Kewajiban & Ekuitas)
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <!-- KEWAJIBAN -->
                    <thead class="table-light">
                        <tr><th colspan="2" class="text-muted small">I. KEWAJIBAN LUAR</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3">
                                Simpanan Sukarela Anggota
                                <small class="d-block text-muted">(Dapat ditarik sewaktu-waktu)</small>
                            </td>
                            <td class="text-end fw-semibold">Rp <?= number_format($simpananSukarela, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-3">
                                Kewajiban / Titipan Pihak Lain
                                <small class="d-block text-muted">(Dari Kas Manual)</small>
                            </td>
                            <td class="text-end fw-semibold">Rp <?= number_format($kewajibanManual, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="table-warning fw-semibold">
                            <td class="ps-3">Total Kewajiban</td>
                            <td class="text-end">Rp <?= number_format($totalKewajiban, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>

                    <!-- EKUITAS -->
                    <thead class="table-light">
                        <tr><th colspan="2" class="text-muted small">II. EKUITAS (MODAL KOPERASI)</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3 fw-semibold">Modal Anggota</td>
                            <td class="text-end text-success fw-semibold">Rp <?= number_format($simpananPokokWajib, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Simpanan Pokok & Wajib</td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td class="ps-3 mt-2 fw-semibold">SHU Tahun Berjalan Belum Dibagi</td>
                            <td class="text-end text-success fw-semibold">Rp <?= number_format($shuBelumDibagi, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Pendapatan Jasa Pinjaman</td>
                            <td class="text-end text-muted small">Rp <?= number_format($totalJasaDiterima, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Pendapatan Denda</td>
                            <td class="text-end text-muted small">Rp <?= number_format($totalDendaDiterima, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">Pemasukan/Biaya Kas Manual</td>
                            <td class="text-end text-muted small">Rp <?= number_format($pendapatanLainnya, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-3 mt-2">Dana Cadangan (<?= $settingCadangan ?>% dari SHU)</td>
                            <td class="text-end">Rp <?= number_format($danaCadangan, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="ps-3">Modal Penyeimbang / Laba Ditahan</td>
                            <td class="text-end <?= $modalLainnya < 0 ? 'text-danger' : '' ?>">
                                Rp <?= number_format($modalLainnya, 0, ',', '.') ?>
                                <small class="d-block text-muted" style="font-size:.65rem">(Termasuk aset tak terdefinisi)</small>
                            </td>
                        </tr>
                        <tr class="table-primary fw-semibold">
                            <td>Total Ekuitas</td>
                            <td class="text-end">Rp <?= number_format($totalEkuitas, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-danger fw-bold">
                        <tr>
                            <td class="fs-6">TOTAL PASIVA</td>
                            <td class="text-end fs-6">Rp <?= number_format($totalPasiva, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rincian Simpanan Per Anggota -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <i class="fas fa-users"></i> Rincian Simpanan per Anggota (Detail Kewajiban)
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-dark">
                <tr><th>No. Anggota</th><th>Nama</th><th class="text-end">Saldo Simpanan</th></tr>
            </thead>
            <tbody>
                <?php foreach($perAnggota as $r): ?>
                <tr>
                    <td><?= $r['no_anggota'] ?></td>
                    <td><?= $r['nama_lengkap'] ?></td>
                    <td class="text-end <?= $r['saldo'] < 0 ? 'text-danger' : 'text-success' ?>">
                        Rp <?= number_format($r['saldo'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="fw-bold table-secondary">
                <tr>
                    <td colspan="2">Total Kewajiban Simpanan Sukarela</td>
                    <td class="text-end">Rp <?= number_format($totalKewajiban, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
