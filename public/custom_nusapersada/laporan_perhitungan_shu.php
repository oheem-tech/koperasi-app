<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perhitungan Sisa Hasil Usaha</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; }
        .header-container { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .logo-placeholder { width: 60px; height: 60px; border: 1px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8pt; text-align: center; margin-right: 20px; }
        .header-title { font-weight: bold; font-size: 14pt; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        th, td { border: 1px solid black; padding: 4px 8px; vertical-align: middle; }
        
        .title-row { text-align: center; font-weight: bold; background-color: #fff; }
        .font-bold { font-weight: bold; }
        .indent-1 { padding-left: 20px !important; }
        .indent-2 { padding-left: 40px !important; }
        
        .currency { display: flex; justify-content: space-between; }
        .curr-rp { float: left; }
        .curr-val { float: right; }
        .clear { clear: both; }
        
        .text-center { text-align: center; }
        
        .footer-table { width: 100%; border: none; margin-top: 30px; page-break-inside: avoid; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .date-text { text-align: right; margin-bottom: 20px; padding-right: 50px;}
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
    
    <table>
        <!-- SECTION 1 -->
        <tr>
            <td colspan="4" class="title-row">
                PERHITUNGAN SISA HASIL USAHA SIMPAN PINJAM<br>
                PER 31 DESEMBER 2025
            </td>
        </tr>
        <tr>
            <td style="width: 55%;" class="font-bold">A. PENDAPATAN</td>
            <td style="width: 20%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 5%;"></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">1. Unit Usaha Simpan pinjam</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">a. Pinjaman jangka panjang</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">3,800,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">Total Pendapatan</td>
            <td></td>
            <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val">3,800,000.00</span><div class="clear"></div></td>
            <td></td>
        </tr>
        
        <tr>
            <td class="font-bold">B. BIAYA</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">1. Biaya RAT Tutup Buku Tahun 2025</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">a. Konsumsi 28 anggota x Rp 25.000,-</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">700,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">b. Administrasi</td>
            <td style="border-bottom: 1px solid #000;"><span class="curr-rp">Rp</span><span class="curr-val">100,000.00</span><div class="clear"></div></td>
            <td></td>
            <td class="text-center font-bold">+</td>
        </tr>
        <tr>
            <td class="font-bold indent-1">Total Biaya</td>
            <td></td>
            <td style="border-bottom: 1px solid #000;" class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val">800,000.00</span><div class="clear"></div></td>
            <td class="text-center font-bold">-</td>
        </tr>
        <tr>
            <td class="font-bold indent-1">Sisa hasil Usaha (SHU)</td>
            <td></td>
            <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val">3,000,000.00</span><div class="clear"></div></td>
            <td></td>
        </tr>
        
        <!-- SECTION 2 -->
        <tr>
            <td colspan="4" class="title-row" style="border-top: 2px solid #000;">
                PEMBAGIAN HASIL USAHA SIMPAN PINJAM<br>
                PER 31 DESEMBER 2025
            </td>
        </tr>
        <tr>
            <td class="font-bold">A. SISA HASIL USAHA</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold text-center">Jumlah</td>
            <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val">3,000,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold">B. PEMBAGIAN SISA HASIL USAHA</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">1. Cadangan 30%</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">900,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">2. Anggota 50%</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">1,500,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">3. Pengurus 20%</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">600,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">a. Ketua 35%</td>
            <td><span class="curr-rp"></span><span class="curr-val">210,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">b. Bendahara 25%</td>
            <td><span class="curr-rp"></span><span class="curr-val">150,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">c. Sekretaris 15%</td>
            <td><span class="curr-rp"></span><span class="curr-val">90,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">d. BP-1 12.5%</td>
            <td><span class="curr-rp"></span><span class="curr-val">75,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="indent-2">e. BP-2 12.5%</td>
            <td><span class="curr-rp"></span><span class="curr-val">75,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>

        <!-- SECTION 3 -->
        <tr>
            <td colspan="4" class="title-row" style="border-top: 2px solid #000;">
                PEMBAGIAN HASIL USAHA UNTUK ANGGOTA<br>
                PER 31 DESEMBER 2024
            </td>
        </tr>
        <tr>
            <td class="font-bold">A. SISA HASIL USAHA UNTUK ANGGOTA</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold text-center">Jumlah</td>
            <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val">1,500,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold">B. PEMBAGIAN SISA HASIL USAHA</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">1. Simpanan 70%</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">1,050,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-bold indent-1">2. Pinjaman 30%</td>
            <td><span class="curr-rp">Rp</span><span class="curr-val">450,000.00</span><div class="clear"></div></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="date-text">Kab Semarang, 31 Desember 2025</div>
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
