<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>

<div class="laporan-title">NERACA BUKU KOPERASI<br>Per Tanggal: <?= date('d F Y', strtotime($cutoff)) ?></div>

<table class="table" style="margin-bottom: 30px;">
    <thead>
        <tr>
            <th width="50%">AKTIVA (HARTA)</th>
            <th width="50%">PASIVA (KEWAJIBAN & EKUITAS)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <!-- Kolom Aktiva -->
            <td style="vertical-align:top; border-right:1px solid #000;">
                <table style="width:100%; font-size:12px;">
                    <tr><td colspan="2"><strong>A. Aktiva Lancar</strong></td></tr>
                    <tr>
                        <td style="padding-left:15px; width:65%;">Kas & Bank</td>
                        <td align="right">Rp <?= number_format($kasBank, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">Piutang Pinjaman Anggota</td>
                        <td align="right">Rp <?= number_format($piutangPinjaman, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px; font-size:10px; color:#555;">(Total Dicairkan: Rp <?= number_format($totalDikucurkan, 0, ',', '.') ?>)</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px; font-size:10px; color:#555;">(Total Terbayar: Rp <?= number_format($totalPokokTerbayar, 0, ',', '.') ?>)</td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <!-- Kolom Pasiva -->
            <td style="vertical-align:top;">
                <table style="width:100%; font-size:12px;">
                    <tr><td colspan="2"><strong>A. Kewajiban (Hutang Luar)</strong></td></tr>
                    <tr>
                        <td style="padding-left:15px; width:65%;">Simpanan Sukarela Anggota</td>
                        <td align="right">Rp <?= number_format($simpananSukarela, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px; width:65%;">Titipan / Hutang Lainnya</td>
                        <td align="right">Rp <?= number_format($kewajibanManual, 0, ',', '.') ?></td>
                    </tr>
                    <tr style="border-top:1px dashed #ccc;">
                        <td style="padding-left:15px; font-weight:bold;">Total Kewajiban</td>
                        <td align="right" style="font-weight:bold;">Rp <?= number_format($totalKewajiban, 0, ',', '.') ?></td>
                    </tr>
                    <tr><td colspan="2"><br><strong>B. Ekuitas (Modal Sendiri Koperasi)</strong></td></tr>
                    <tr>
                        <td style="padding-left:15px;">Modal Anggota (Pokok & Wajib)</td>
                        <td align="right">Rp <?= number_format($simpananPokokWajib, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">Modal Penyeimbang / Laba Ditahan</td>
                        <td align="right">Rp <?= number_format($modalLainnya, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">Alokasi Dana Cadangan (<?= $settingCadangan ?>%)</td>
                        <td align="right">Rp <?= number_format($danaCadangan, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px;">SHU Tahun Berjalan <small style="color:#666;">(+Rp <?= number_format($pendapatanLainnya, 0, ',', '.') ?> Kas)</small></td>
                        <td align="right">Rp <?= number_format($shuBelumDibagi, 0, ',', '.') ?></td>
                    </tr>
                    <tr style="border-top:1px dashed #ccc;">
                        <td style="padding-left:15px; font-weight:bold;">Total Ekuitas</td>
                        <td align="right" style="font-weight:bold;">Rp <?= number_format($totalEkuitas, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background:#f1f5f9; font-size:14px;">
            <td>TOTAL AKTIVA <span style="float:right">Rp <?= number_format($totalAktiva, 0, ',', '.') ?></span></td>
            <td>TOTAL PASIVA <span style="float:right">Rp <?= number_format($totalPasiva, 0, ',', '.') ?></span></td>
        </tr>
    </tfoot>
</table>

<!-- ===== TANDA TANGAN — tepat di bawah neraca utama (halaman 1) ===== -->
<?php 
$koperasi_pembina = esc(get_pengaturan('koperasi_pembina', '_______________'));
$koperasi_pengawas = esc(get_pengaturan('koperasi_pengawas', '_______________'));
$koperasi_ketua = esc(get_pengaturan('koperasi_ketua', '_______________'));
$koperasi_kota = esc(get_pengaturan('koperasi_kota', 'Jakarta'));
$koperasi_bendahara = esc(get_pengaturan('koperasi_bendahara', '_______________'));
?>
<table style="width:100%; margin-top:40px; font-size:13px; border-collapse:collapse; font-family:'Times New Roman',Times,serif;">
    <tr>
        <td style="text-align:center; vertical-align:top; width:25%; border:none; padding-bottom:70px;">
            Mengetahui,<br>
            Pembina
            <div style="font-weight:bold; text-decoration:underline; margin-top:70px;"><?= $koperasi_pembina ?></div>
        </td>
        <td style="text-align:center; vertical-align:top; width:25%; border:none; padding-bottom:70px;">
            Mengesahkan,<br>
            Pengawas
            <div style="font-weight:bold; text-decoration:underline; margin-top:70px;"><?= $koperasi_pengawas ?></div>
        </td>
        <td style="text-align:center; vertical-align:top; width:25%; border:none; padding-bottom:70px;">
            Menyetujui,<br>
            Ketua Koperasi
            <div style="font-weight:bold; text-decoration:underline; margin-top:70px;"><?= $koperasi_ketua ?></div>
        </td>
        <td style="text-align:center; vertical-align:top; width:25%; border:none; padding-bottom:70px;">
            <?= $koperasi_kota ?>, <?= date('d F Y') ?><br>
            Bendahara
            <div style="font-weight:bold; text-decoration:underline; margin-top:70px;"><?= $koperasi_bendahara ?></div>
        </td>
    </tr>
</table>

<!-- Pass hide_ttd true to the layout via variable injection conceptually, if we were in a view directly, 
     but since this is an extended view, we can just let neraca render its own TTD and hide the layout's. 
     Wait, it's simpler to set $hide_ttd before extending. However, CI4 doesn't support $this->extend() sharing vars easily like that.
     So keep the custom CSS but clean it up. -->
<style>.ttd-box { display: none !important; }</style>

<!-- ===== LAMPIRAN: halaman baru ===== -->
<div style="page-break-before: always;"></div>
<div class="laporan-title">LAMPIRAN: RINCIAN KEWAJIBAN SIMPANAN (SUKARELA)</div>
<table class="table">
    <thead>
        <tr>
            <th width="15%">No. Anggota</th>
            <th width="50%">Nama Anggota</th>
            <th width="35%" style="text-align: right;">Saldo Simpanan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($perAnggota as $r): ?>
        <tr>
            <td style="text-align: center;"><?= $r['no_anggota'] ?></td>
            <td><?= $r['nama_lengkap'] ?></td>
            <td align="right"><?= number_format($r['saldo'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background:#f1f5f9;">
            <td colspan="2">Total Kewajiban Simpanan Sukarela</td>
            <td align="right">Rp <?= number_format($simpananSukarela, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
