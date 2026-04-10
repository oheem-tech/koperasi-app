<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>

<div class="laporan-title">LAPORAN PEMBAGIAN SISA HASIL USAHA (SHU)<br>Tahun Buku: <?= $tahun ?></div>

<table class="table" style="margin-bottom: 30px;">
    <tr>
        <td width="50%">Total Pendapatan Jasa Angsuran (100%)</td>
        <td style="text-align:right; font-weight:bold;">Rp <?= number_format($alokasiSHU['total_shu'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">1. Jasa Modal (<?= $settings['shu_jasa_modal'] ?? 20 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['jasa_modal'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">2. Jasa Anggota (<?= $settings['shu_jasa_anggota'] ?? 25 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['jasa_anggota'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">3. Jasa Pengurus (<?= $settings['shu_pengurus_anggota'] ?? 10 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['pengurus_anggota'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">4. Jasa Pengawas (<?= $settings['shu_pengawas'] ?? 5 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['pengawas'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">5. Jasa Pembina (<?= $settings['shu_pembina'] ?? 5 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['pembina'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">6. Dana Sosial (<?= $settings['shu_dana_sosial'] ?? 5 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['dana_sosial'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">7. Dana Pendidikan (<?= $settings['shu_dana_pendidikan'] ?? 5 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['dana_pendidikan'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td style="padding-left: 20px;">8. Dana Cadangan (<?= $settings['shu_cadangan'] ?? 20 ?>%)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['dana_cadangan'], 0, ',', '.') ?></td>
    </tr>
    <tr style="font-weight:bold; background:#f1f5f9;">
        <td>TOTAL ALOKASI YANG TERBAGI (*Pembulatan)</td>
        <td style="text-align:right">Rp <?= number_format($alokasiSHU['total_dialokasikan'], 0, ',', '.') ?></td>
    </tr>
</table>

<h4 style="font-size: 14px; margin-bottom: 10px; font-weight: bold;">Rincian Pembagian SHU per Anggota</h4>
<table class="table">
    <thead>
        <tr>
            <th>No. Anggota</th>
            <th>Nama Lengkap</th>
            <th>Status/Jabatan</th>
            <th>SHU Jasa Modal (Rp)</th>
            <th>SHU Jasa Anggota (Rp)</th>
            <th>SHU Jabatan (Rp)</th>
            <th>TOTAL SHU DITERIMA (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($shuPerAnggota as $row): ?>
        <tr>
            <td><?= $row['no_anggota'] ?></td>
            <td><?= $row['nama_lengkap'] ?></td>
            <td style="text-align:center"><?= ucfirst($row['jabatan']) ?></td>
            <td style="text-align:right"><?= number_format($row['shu_jasa_modal'], 0, ',', '.') ?></td>
            <td style="text-align:right"><?= number_format($row['shu_jasa_anggota'], 0, ',', '.') ?></td>
            <td style="text-align:right"><?= number_format($row['shu_jabatan'], 0, ',', '.') ?></td>
            <td style="text-align:right; font-weight:bold;"><?= number_format($row['shu_total'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background:#f1f5f9;">
            <td colspan="6" style="text-align:right">TOTAL ESTIMASI DITERIMA KESELURUHAN ANGGOTA</td>
            <td style="text-align:right; font-size:13px;">Rp <?= number_format(array_sum(array_column($shuPerAnggota, 'shu_total')), 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
