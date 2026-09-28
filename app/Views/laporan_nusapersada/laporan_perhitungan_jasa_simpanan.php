
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perhitungan Jasa Simpanan Pokok, Wajib, Sukarela</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; }
        .header-container { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .logo-placeholder { width: 50px; height: 50px; border: 1px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8pt; text-align: center; margin-right: 20px; }
        .header-title { font-weight: bold; font-size: 14pt; text-align: center; }
        .sub-header { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 4px 6px; vertical-align: middle; }
        th { text-align: center; font-weight: bold; }
        
        .col-no { text-align: center; width: 30px; }
        .col-nama { width: 180px; }
        
        .curr-rp { float: left; }
        .curr-val { float: right; }
        .clear { clear: both; }
        
        .footer-table { width: 100%; border: none; margin-top: 30px; page-break-inside: avoid; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .date-text { text-align: right; margin-bottom: 20px; padding-right: 30px; }
        .signature-name { margin-top: 70px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo-placeholder">LOGO<br>KOPERASI</div>
        <div class="header-title">
            KOPERASI KARYAWAN SMK NUSAPERSADA
        </div>
    </div>
    
    <div class="sub-header">
        PERHITUNGAN JASA SIMPANAN POKOK, WAJIB, SUKARELA<br>
        PER 31 DESEMBER 2024
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>SIMPANAN<br>POKOK</th>
                <th>SIMPANAN<br>WAJIB</th>
                <th>SIMPANAN<br>SUKARELA</th>
                <th>JUMLAH</th>
                <th>JASA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr>
                <td class="col-no"><?= $row[0] ?></td>
                <td class="col-nama"><?= $row[1] ?></td>
                <td><span class="curr-rp">Rp</span><span class="curr-val"><?= $row[2] ?></span><div class="clear"></div></td>
                <td><span class="curr-rp">Rp</span><span class="curr-val"><?= $row[3] ?></span><div class="clear"></div></td>
                <td><span class="curr-rp">Rp</span><span class="curr-val"><?= $row[4] ?></span><div class="clear"></div></td>
                <td><span class="curr-rp">Rp</span><span class="curr-val"><?= $row[5] ?></span><div class="clear"></div></td>
                <td><span class="curr-rp">Rp</span><span class="curr-val"><?= $row[6] ?></span><div class="clear"></div></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">JUMLAH</th>
                <?php foreach ($totals as $tot): ?>
                <th><span class="curr-rp">Rp</span><span class="curr-val"><?= $tot ?></span><div class="clear"></div></th>
                <?php endforeach; ?>
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
