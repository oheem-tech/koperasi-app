<?php
$dir = 'd:/xampp8/htdocs/koperasi/app/Views/laporan_nusapersada';
$files = glob("$dir/*.php");

$kop_surat = '
    <?php 
    $koperasi_nama = esc(get_pengaturan(\'koperasi_nama\', \'Koperasi Simpan Pinjam\'));
    $koperasi_alamat = esc(get_pengaturan(\'koperasi_alamat\', \'Jl. Merdeka\'));
    $koperasi_telepon = esc(get_pengaturan(\'koperasi_telepon\', \'021-123456\'));
    ?>
    <div class="kop-surat" style="border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 20px;">
        <?php if(get_koperasi_logo()): ?>
            <img src="<?= get_koperasi_logo() ?>" alt="Logo" style="max-height: 70px;">
        <?php endif; ?>
        <div>
            <h3 style="margin: 0; font-weight: bold; font-size: 22px; text-transform: uppercase;"><?= $koperasi_nama ?></h3>
            <p style="margin: 0; font-size: 13px;"><?= $koperasi_alamat ?></p>
            <p style="margin: 0; font-size: 13px;">Telepon: <?= $koperasi_telepon ?></p>
        </div>
    </div>
';

$ttd_box = '
    <?php 
    $koperasi_pembina = esc(get_pengaturan(\'koperasi_pembina\', \'_______________\'));
    $koperasi_pengawas = esc(get_pengaturan(\'koperasi_pengawas\', \'_______________\'));
    $koperasi_ketua = esc(get_pengaturan(\'koperasi_ketua\', \'_______________\'));
    $koperasi_kota = esc(get_pengaturan(\'koperasi_kota\', \'Jakarta\'));
    $koperasi_bendahara = esc(get_pengaturan(\'koperasi_bendahara\', \'_______________\'));
    ?>
    <table style="width: 100%; margin-top: 40px; font-size: 13px; border:none;">
        <tr>
            <td style="text-align: center; vertical-align: top; width: 25%; padding-bottom: 70px; border: none;">
                Mengetahui,<br>Pembina
                <div style="font-weight: bold; text-decoration: underline; margin-top: 70px;"><?= $koperasi_pembina ?></div>
            </td>
            <td style="text-align: center; vertical-align: top; width: 25%; padding-bottom: 70px; border: none;">
                Mengesahkan,<br>Pengawas
                <div style="font-weight: bold; text-decoration: underline; margin-top: 70px;"><?= $koperasi_pengawas ?></div>
            </td>
            <td style="text-align: center; vertical-align: top; width: 25%; padding-bottom: 70px; border: none;">
                Menyetujui,<br>Ketua Koperasi
                <div style="font-weight: bold; text-decoration: underline; margin-top: 70px;"><?= $koperasi_ketua ?></div>
            </td>
            <td style="text-align: center; vertical-align: top; width: 25%; padding-bottom: 70px; border: none;">
                <?= $koperasi_kota ?>, <?= date(\'d F Y\') ?><br>Bendahara
                <div style="font-weight: bold; text-decoration: underline; margin-top: 70px;"><?= $koperasi_bendahara ?></div>
            </td>
        </tr>
    </table>
';

foreach($files as $file) {
    $content = file_get_contents($file);
    // Replace custom header
    $content = preg_replace('/<div class="header">.*?<\/div>/s', $kop_surat, $content);
    // Replace custom signature
    $content = preg_replace('/<div class="signature">.*?<\/div>/s', $ttd_box, $content);
    file_put_contents($file, $content);
}
echo "Berhasil update kop & ttd!";
