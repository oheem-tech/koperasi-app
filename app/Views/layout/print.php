<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cetak Laporan' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; background: #fff; padding: 20px; }
        .kop-surat { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; line-height: 1.4; }
        .kop-surat h3 { margin: 0; font-weight: bold; font-size: 22px; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 13px; }
        .laporan-title { text-align: center; margin-bottom: 25px; font-weight: bold; text-transform: uppercase; font-size: 16px; margin-top: 10px;}
        table.table th, table.table td { border: 1px solid #000 !important; color: #000 !important; padding: 5px; font-size: 12px; vertical-align: middle; }
        table.table thead th { background-color: #f1f5f9 !important; font-weight: bold; text-align: center; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .ttd-box { width: 100%; margin-top: 40px; font-size: 13px; }
        .ttd-box td { text-align: center; vertical-align: top; width: 25%; padding-bottom: 70px; border: none; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 70px; }
        /* Toolbar mobile */
        .toolbar-mobile { background: #1e3a5f; color: #fff; padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
        .toolbar-mobile .info { flex: 1; font-size: 13px; font-family: sans-serif; }
        .toolbar-mobile .info strong { display: block; font-size: 15px; }
        .toolbar-mobile .info small { opacity: .8; }
        @media screen and (max-width: 767px) {
            body { padding: 10px; font-size: 12px; }
            table.table th, table.table td { font-size: 10px; padding: 4px 3px; }
            .ttd-box { display: none; }
        }
        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
            #debugbar, #toolbarContainer { display: none !important; }
            @page { size: A4 portrait; margin: 15mm; }
        }
        #debugbar, #toolbarContainer { display: none !important; }
    </style>
</head>
<body>
    <div class="no-print" id="print-toolbar" style="margin-bottom: 20px;">
        <!-- Toolbar diisi oleh JS berdasarkan deteksi perangkat -->
    </div>
    <script>
    (function() {
        var isMobile = /Mobi|Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);
        var toolbar  = document.getElementById('print-toolbar');
        var title    = document.title;
        var url      = window.location.href;

        if (isMobile) {
            // === MOBILE: Tampilkan toolbar share, jangan auto-print ===
            var shareBtn = '';
            if (navigator.share) {
                shareBtn = '<button onclick="sharePage()" class="btn btn-warning text-dark fw-bold">'
                         + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-top:-2px;margin-right:4px">'
                         + '<path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>'
                         + '</svg> Bagikan Laporan</button>';
            }
            toolbar.innerHTML =
                '<div class="toolbar-mobile">'
                + '<div class="info"><strong>📄 ' + title + '</strong>'
                + '<small>Gunakan menu browser → Cetak / Simpan PDF</small></div>'
                + '<div class="d-flex gap-2 flex-wrap">'
                + shareBtn
                + '<button onclick="window.print()" class="btn btn-light">🖨️ Cetak</button>'
                + '</div></div>';
        } else {
            // === DESKTOP: tombol biasa + auto-print ===
            toolbar.innerHTML =
                '<div style="text-align:right">'
                + '<button onclick="window.print()" class="btn btn-primary me-2">'
                + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-1" viewBox="0 0 16 16">'
                + '<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>'
                + '<path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0-2-2h-1V3a2 2 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>'
                + '</svg> Cetak Laporan</button>'
                + '<button onclick="window.close()" class="btn btn-secondary">Tutup</button>'
                + '</div>';
            // Auto-print hanya di desktop setelah halaman termuat
            window.addEventListener('load', function() {
                setTimeout(function() { window.print(); }, 500); // 500ms delay to ensure heavy CSS/Images apply
            });
        }

        function sharePage() {
            navigator.share({ title: title, url: url }).catch(function() {});
        }
        window.sharePage = sharePage;
    })();
    </script>

    <?php 
    // Optimization: Batch configuration reads for header
    $koperasi_nama = esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam'));
    $koperasi_alamat = esc(get_pengaturan('koperasi_alamat', 'Jl. Merdeka No. 1, Jakarta'));
    $koperasi_telepon = esc(get_pengaturan('koperasi_telepon', '021-123456'));
    ?>
    <div class="kop-surat" style="display: flex; align-items: center; justify-content: center; gap: 20px;">
        <?php if(get_koperasi_logo()): ?>
            <img src="<?= get_koperasi_logo() ?>" alt="Logo" style="max-height: 70px;">
        <?php endif; ?>
        <div style="text-align: center;">
            <h3><?= $koperasi_nama ?></h3>
            <p><?= $koperasi_alamat ?></p>
            <p>Telepon: <?= $koperasi_telepon ?></p>
        </div>
    </div>

    <?= $this->renderSection('content') ?>

    <?php 
    // Optimization: Batch configuration reads for footer
    $koperasi_pembina = esc(get_pengaturan('koperasi_pembina', '_______________'));
    $koperasi_pengawas = esc(get_pengaturan('koperasi_pengawas', '_______________'));
    $koperasi_ketua = esc(get_pengaturan('koperasi_ketua', '_______________'));
    $koperasi_kota = esc(get_pengaturan('koperasi_kota', 'Jakarta'));
    $koperasi_bendahara = esc(get_pengaturan('koperasi_bendahara', '_______________'));
    ?>

    <?php if(!isset($hide_ttd) || !$hide_ttd): ?>
    <table class="ttd-box">
        <tr>
            <td style="width: 25%;">
                Mengetahui,<br>
                Pembina
                <div class="ttd-name"><?= $koperasi_pembina ?></div>
            </td>
            <td style="width: 25%;">
                Mengesahkan,<br>
                Pengawas
                <div class="ttd-name"><?= $koperasi_pengawas ?></div>
            </td>
            <td style="width: 25%;">
                Menyetujui,<br>
                Ketua Koperasi
                <div class="ttd-name"><?= $koperasi_ketua ?></div>
            </td>
            <td style="width: 25%;">
                <?= $koperasi_kota ?>, <?= date('d F Y') ?><br>
                Bendahara
                <div class="ttd-name"><?= $koperasi_bendahara ?></div>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <?php if (!is_premium()): ?>
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; pointer-events: none; overflow: hidden;">
        <div style="transform: rotate(-35deg); font-size: 8rem; font-weight: bold; color: rgba(0,0,0,0.08); font-family: sans-serif; white-space: nowrap; user-select: none;">
            CirebonTech
        </div>
    </div>
    <div style="text-align: center; font-size: 10px; color: #666; margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 10px; font-family: sans-serif;">
        Printed via Free Koperasi App - By <strong>CirebonTech</strong>
    </div>
    <?php endif; ?>
</body>
</html>
