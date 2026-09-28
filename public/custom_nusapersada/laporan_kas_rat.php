<?php
$months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember"];
$pemasukan = [
    ["1", "Saldo awal bulan", "9,135,000", "10,500,000", "14,980,000", "21,555,000", "27,775,000", "27,350,000", "34,675,000", "40,985,000", "41,035,000", "32,715,000", "39,445,000", "46,030,000"],
    ["2", "Simpanan", "", "", "", "", "", "", "", "", "", "", "", ""],
    ["", "a. Simpanan Pokok", "40,000", "40,000", "40,000", "40,000", "20,000", "-", "-", "-", "-", "-", "-", "-"],
    ["", "b. Simpanan wajib", "145,000", "145,000", "145,000", "140,000", "140,000", "140,000", "135,000", "135,000", "130,000", "130,000", "125,000", "125,000"],
    ["", "c. Simpanan Sukarela", "5,140,000", "5,140,000", "5,140,000", "5,040,000", "5,040,000", "5,040,000", "4,690,000", "4,690,000", "4,690,000", "4,540,000", "4,540,000", "4,540,000"],
    ["3", "Angsuran", "", "", "", "", "", "", "", "", "", "", "", ""],
    ["", "a. Angsuran jangka panjang", "1,400,000", "1,050,000", "1,250,000", "1,250,000", "1,250,000", "1,950,000", "2,120,000", "700,000", "2,300,000", "1,900,000", "2,500,000", "2,300,000"],
    ["4", "Jasa", "", "", "", "", "", "", "", "", "", "", "", ""],
    ["", "a. Jasa Jangka panjang", "140,000", "105,000", "145,000", "145,000", "145,000", "215,000", "185,000", "100,000", "230,000", "173,000", "220,000", "282,000"],
    ["5", "Pelunasan", "", "", "", "", "", "", "", "", "", "", "", ""],
    ["", "a. Pelunasan jangka panjang", "200,000", "", "", "", "", "", "2,700,000", "1,800,000", "", "", "", "3,145,000"],
    ["6", "Cadangan", "450,000", "", "", "", "", "", "", "", "", "", "", ""]
];
$pemasukan_jumlah = ["16,650,000", "16,980,000", "21,555,000", "28,170,000", "34,370,000", "34,695,000", "44,505,000", "48,410,000", "48,385,000", "39,458,000", "46,830,000", "56,422,000"];

$pengeluaran = [
    ["1", "Pinjaman", "", "", "", "", "", "", "", "", "", "", "", ""],
    ["", "a. Pinjaman jangka panjang", "6,000,000", "2,000,000", "", "", "7,000,000", "", "1,000,000", "6,300,000", "15,000,000", "", "800,000", ""],
    ["2", "Anggota Keluar", "", "", "", "375,000", "", "", "2,500,000", "1,045,000", "670,000", "", "", ""],
    ["3", "Saldo Tunai", "10,500,000", "14,980,000", "21,555,000", "27,775,000", "27,350,000", "34,675,000", "40,985,000", "41,035,000", "32,715,000", "39,445,000", "46,030,000", "56,422,000"]
];
$pengeluaran_jumlah = ["16,500,000", "16,980,000", "21,555,000", "28,150,000", "34,350,000", "34,675,000", "44,485,000", "48,380,000", "48,385,000", "39,445,000", "46,830,000", "56,422,000"];

function renderCell($val) {
    if ($val === "" || $val === "-") return '<span style="float: right;">'.$val.'</span>';
    return '<span class="currency-symbol">Rp</span><span class="currency-value">' . $val . '</span><div class="clear"></div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Simpan Pinjam</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 8pt; color: #000; }
        .header-container { display: flex; align-items: center; justify-content: center; margin-bottom: 10px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .logo-placeholder { width: 50px; height: 50px; border: 1px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 7pt; text-align: center; margin-right: 20px; }
        .header-title { font-weight: bold; font-size: 14pt; text-align: center; }
        .sub-header { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 3px; }
        th { text-align: center; font-weight: bold; }
        .col-no { text-align: center; width: 20px; }
        .col-rincian { width: 140px; }
        .currency-symbol { float: left; }
        .currency-value { float: right; }
        .clear { clear: both; }
        .font-bold { font-weight: bold; }
        .footer-table { width: 100%; border: none; margin-top: 15px; page-break-inside: avoid; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .date-text { text-align: right; margin-bottom: 20px; font-size: 9pt;}
        .signature-name { margin-top: 60px; font-weight: bold; font-size: 9pt;}
        .signature-title { font-size: 9pt; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo-placeholder">LOGO<br>KOPERASI</div>
        <div class="header-title">
            KOPERASI GUKAR SMK NUSAPERSADA
        </div>
    </div>
    
    <div class="sub-header">
        KAS SIMPAN PINJAM PER 31 DESEMBER 2025
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>RINCIAN</th>
                <?php foreach ($months as $m): ?>
                <th><?= $m ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">A</td>
                <td colspan="13" class="font-bold">PEMASUKAN</td>
            </tr>
            <?php foreach ($pemasukan as $row): ?>
            <tr>
                <td class="col-no"><?= $row[0] ?></td>
                <td class="col-rincian <?= $row[0] !== "" ? 'font-bold' : '' ?>"><?= $row[1] ?></td>
                <?php for($i=2; $i<=13; $i++): ?>
                <td><?= renderCell($row[$i]) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">JUMLAH</th>
                <?php foreach ($pemasukan_jumlah as $val): ?>
                <th style="font-weight: bold;"><?= renderCell($val) ?></th>
                <?php endforeach; ?>
            </tr>

            <tr>
                <td class="font-bold">B</td>
                <td colspan="13" class="font-bold">PENGELUARAN</td>
            </tr>
            <?php foreach ($pengeluaran as $row): ?>
            <tr>
                <td class="col-no"><?= $row[0] ?></td>
                <td class="col-rincian <?= $row[0] !== "" ? 'font-bold' : '' ?>"><?= $row[1] ?></td>
                <?php for($i=2; $i<=13; $i++): ?>
                <td><?= renderCell($row[$i]) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">JUMLAH</th>
                <?php foreach ($pengeluaran_jumlah as $val): ?>
                <th style="font-weight: bold;"><?= renderCell($val) ?></th>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="date-text">Kab. Semarang, 3 Januari 2026</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="signature-title">Ketua</div>
                <div class="signature-name">Hafid Nur Prasetyo, S.Pd.</div>
            </td>
            <td>
                <div class="signature-title">Bendahara</div>
                <div class="signature-name">Yuli Setyowati, S.Pd.</div>
            </td>
        </tr>
    </table>
</body>
</html>
