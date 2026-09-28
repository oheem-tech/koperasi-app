<?php $months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agustus", "Sept", "Okt", "Nov", "Des"]; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PEMBAGIAN JASA PINJAMAN</title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; }
        .header-container { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .logo-placeholder { width: 60px; height: 60px; border: 1px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8pt; text-align: center; margin-right: 20px; }
        .header-title { font-weight: bold; font-size: 14pt; text-align: center; }
        .sub-header { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        .col-nama { text-align: left; width: 180px; }
        .nominal { text-align: right; }
        .footer-table { width: 100%; border: none; margin-top: 20px; page-break-inside: avoid; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .date-text { text-align: right; margin-bottom: 20px; }
        .signature-name { margin-top: 60px; font-weight: bold; }
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
        PEMBAGIAN JASA PINJAMAN<br>
        PER 31 DESEMBER 2024
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA ANGGOTA</th>
                <?php foreach ($months as $m): ?>
                <th><?= $m ?></th>
                <?php endforeach; ?>
                <th rowspan="2">JUMLAH</th>
                <th rowspan="2">JASA PINJAMAN</th>
            </tr>
            <tr>
                <?php foreach ($months as $m): ?>
                <th>JASA</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data2 as $index => $row): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td class="col-nama"><?= $row[0] ?></td>
                <?php for($i=1; $i<=14; $i++): ?>
                <td class="nominal"><?= $row[$i] ?></td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2"></th>
                <?php foreach ($totals as $tot): ?>
                <th class="nominal"><?= $tot ?></th>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="date-text">Kab.Semarang, 31 Desember 2024</div>
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
