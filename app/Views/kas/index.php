<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-book"></i> Buku Kas Umum (General Ledger)</h2>
    <a href="<?= base_url('kas/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Input Kas Manual</a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('kas') ?>" method="get" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <label>Bulan Transaksi:</label>
            </div>
            <?php
                // Tentukan default select dari filter saat ini
                $selBulan = $bulan !== 'all' ? date('m', strtotime($bulan . '-01')) : date('m');
                $selTahun = $bulan !== 'all' ? date('Y', strtotime($bulan . '-01')) : date('Y');
                $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            ?>
            <div class="col-auto">
                <select name="filter_bulan" class="form-select">
                    <?php foreach($namaBulan as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $selBulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="filter_tahun" class="form-select">
                    <?php for($y = date('Y') + 2; $y >= 2010; $y--): ?>
                        <option value="<?= $y ?>" <?= $selTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?= base_url('kas?bulan=all') ?>" class="btn btn-outline-secondary">Semua Waktu</a>
            </div>
            <?php if ($bulan !== 'all'): ?>
            <div class="col-auto ms-auto">
                <div class="bg-light border border-info rounded px-3 py-1">
                    <span class="text-muted small d-block mb-n1" style="font-size: 0.8rem;">Saldo Periode Sebelumnya</span>
                    <strong class="text-info fs-6">Rp <?= number_format($awalSaldo ?? 0, 0, ',', '.') ?></strong>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped table-hover mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Keterangan</th>
                    <th width="15%">Debet (Masuk)</th>
                    <th width="15%">Kredit (Keluar)</th>
                    <th width="15%">Saldo Akhir</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $totalMasuk = 0;
                $totalKeluar = 0;
                ?>
                <?php if ($bulan !== 'all'): ?>
                <tr class="table-info">
                    <td colspan="5" class="text-end fw-bold">SALDO PERIODE SEBELUMNYA:</td>
                    <td class="text-end fw-bold">Rp <?= number_format($awalSaldo ?? 0, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
                <?php endif; ?>
                <?php foreach($kas as $row): ?>
                <?php
                    $isMasuk = $row['jenis'] == 'masuk';
                    if ($isMasuk) $totalMasuk += $row['nominal'];
                    else $totalKeluar += $row['nominal'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td>
                        <?= esc($row['keterangan_display'] ?? $row['keterangan']) ?>
                        <?php if(isset($row['is_massal']) && $row['is_massal']): ?>
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.7em;">Massal</span>
                        <?php endif; ?>
                        
                        <?php if(isset($row['is_auto']) && $row['is_auto']): ?>
                            <span class="badge bg-secondary ms-1" style="font-size:0.7em;">Auto</span>
                        <?php else: ?>
                            <span class="badge bg-info ms-1" style="font-size:0.7em;">Manual</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-success"><?= $isMasuk ? 'Rp ' . number_format($row['nominal'], 0, ',', '.') : '-' ?></td>
                    <td class="text-end text-danger"><?= !$isMasuk ? 'Rp ' . number_format($row['nominal'], 0, ',', '.') : '-' ?></td>
                    <td class="text-end fw-bold">Rp <?= number_format($row['saldo_akhir'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php if(isset($row['is_auto']) && !$row['is_auto']): ?>
                            <a href="<?= base_url('kas/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="<?= base_url('kas/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus entri kas manual ini?');" title="Hapus"><i class="fas fa-trash"></i></a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($kas)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi kas pada periode ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <?php if(!empty($kas)): ?>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="3" class="text-end">TOTAL MUTASI PERIODE INI:</td>
                    <td class="text-end text-success">Rp <?= number_format($totalMasuk, 0, ',', '.') ?></td>
                    <td class="text-end text-danger">Rp <?= number_format($totalKeluar, 0, ',', '.') ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
