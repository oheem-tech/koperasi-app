
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>JASA PINJAMAN DAN SIMPANAN</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; }
        .header-title { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 4px 6px; }
        th { text-align: center; font-weight: bold; }
        .col-no { text-align: center; width: 30px; }
        .rupiah { float: left; }
        .nominal { float: right; }
        .clear { clear: both; }
        .footer-table { width: 100%; border: none; margin-top: 20px; page-break-inside: avoid; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .date-text { margin-bottom: 20px; }
        .signature-name { margin-top: 80px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-title">
        JASA PINJAMAN DAN SIMPANAN<br>
        PER 31 DESEMBER 2024
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA</th>
                <th colspan="2">JASA</th>
                <th rowspan="2">SHU TOKO</th>
                <th rowspan="2">TOTAL JASA<br>DITERIMA</th>
            </tr>
            <tr>
                <th>SIMPANAN</th>
                <th>PINJAMAN</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $index => $row): ?>
            <tr>
                <td class="col-no"><?= $index + 1 ?></td>
                <td><?= $row[0] ?></td>
                <td>
                    <?php if($row[1]): ?>
                    <span class="rupiah">Rp</span><span class="nominal"><?= $row[1] ?></span><div class="clear"></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row[2]): ?>
                    <span class="rupiah">Rp</span><span class="nominal"><?= $row[2] ?></span><div class="clear"></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row[3]): ?>
                    <span class="rupiah">Rp</span><span class="nominal"><?= $row[3] ?></span><div class="clear"></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row[4]): ?>
                    <span class="rupiah">Rp</span><span class="nominal"><?= $row[4] ?></span><div class="clear"></div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">Jumlah</th>
                <th><span class="rupiah">Rp</span><span class="nominal">500,000</span><div class="clear"></div></th>
                <th><span class="rupiah">Rp</span><span class="nominal">150,000</span><div class="clear"></div></th>
                <th><span class="rupiah">Rp</span><span class="nominal">74,000</span><div class="clear"></div></th>
                <th><span class="rupiah">Rp</span><span class="nominal">724,000</span><div class="clear"></div></th>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="date-text">Kab. Semarang, 31 Desember 2024</div>
            </td>
        </tr>
        <tr>
            <td>
                <div>Ketua</div>
                <div class="signature-name">Hafid Nur Prasetyo, S.Pd.</div>
            </td>
            <td>
                <div>Bendahara</div>
                <div class="signature-name">Yuli Setyowati, S.Pd.</div>
            </td>
        </tr>
    </table>
</body>
</html>
