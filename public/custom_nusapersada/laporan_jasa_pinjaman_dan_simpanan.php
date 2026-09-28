<?php
$data = [
    ["Hadiyanto Budiono, SE", "14,035", "", "2,643", "16,678"],
    ["Andi Hernowo, S.Pd.", "19,117", "12,917", "2,643", "34,677"],
    ["Hafid Nur P, S.Pd.", "20,845", "", "2,643", "23,488"],
    ["Yuli Setyowati, S.Pd.", "6,786", "", "2,643", "9,429"],
    ["Adinda Puspitasari, SE.", "20,379", "11,250", "2,643", "34,272"],
    ["Retnaningsih, S.Pd", "45,062", "12,500", "2,643", "60,204"],
    ["Prisca Candra D, M.Pd.", "5,907", "", "2,643", "8,550"],
    ["Didik Isdiyanto, BA", "43,308", "31,250", "2,643", "77,201"],
    ["Lukito Budi N, S.Kom", "34,612", "", "2,643", "37,255"],
    ["Elly Pravitasari, Amd", "23,481", "", "2,643", "26,124"],
    ["Kustiyono", "9,129", "", "2,643", "11,772"],
    ["apt.Eny Kusnarti, S.Farm.", "48,345", "27,500", "2,643", "78,488"],
    ["Alma Syifa Trihapsari, S.Farm.", "7,079", "", "2,643", "9,721"],
    ["Gunawan", "3,710", "", "2,643", "6,353"],
    ["Yulia Saffira, S.Pd.", "25,705", "18,333", "2,643", "46,680"],
    ["Mungki Vitaresti, M.Pd", "11,741", "", "2,643", "14,384"],
    ["Sekar Arum Septianti, S.Pd", "12,900", "8,750", "2,643", "24,292"],
    ["Ganik Arianti, SPd.", "31,820", "27,500", "2,643", "61,963"],
    ["Ulfa Arbain, S.Ak", "22,798", "", "2,643", "25,441"],
    ["Lazuardi Rakata, S.Pd.", "3,710", "", "2,643", "6,353"],
    ["Muh Muzaki", "6,200", "", "2,643", "8,843"],
    ["Lilis Setyanta", "5,224", "", "2,643", "7,866"],
    ["Muji Syukur", "7,372", "", "2,643", "10,014"],
    ["Muh Ispandi", "32,244", "", "2,643", "34,887"],
    ["Agus Susanto", "1,953", "", "2,643", "4,596"],
    ["Drs. H.Purwanto, BSc. MPd.", "14,133", "", "2,643", "16,776"],
    ["Suroso Kuntjoro", "8,275", "", "2,643", "10,917"],
    ["Eddy Laksono", "14,133", "", "2,643", "16,776"]
];
?>
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
