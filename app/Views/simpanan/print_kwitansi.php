<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Simpanan #<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .kwitansi {
            background: #fff;
            width: 680px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.1);
            overflow: hidden;
            position: relative;
        }

        /* Header strip */
        .kw-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: 18px;
            color: #fff;
        }
        .kw-logo {
            height: 52px;
            display: flex; align-items: center; justify-content: center;
        }
        .kw-logo i { color: #fff; font-size: 22px; width: 52px; background: linear-gradient(135deg, #3b82f6, #06b6d4); border-radius: 12px; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(59,130,246,.5); height: 52px; }
        .kw-title-block h1 { font-size: 1.1rem; font-weight: 700; letter-spacing: .3px; }
        .kw-title-block p { font-size: 0.75rem; color: #93c5fd; margin-top: 2px; }

        .kw-badge {
            margin-left: auto;
            text-align: right;
        }
        .kw-badge .badge-label { font-size: 0.65rem; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px; }
        .kw-badge .badge-no { font-size: 1.1rem; font-weight: 700; letter-spacing: .5px; }

        /* Jenis transaksi ribbon */
        .kw-ribbon {
            padding: 8px 32px;
            font-size: 0.75rem; font-weight: 600; letter-spacing: .4px;
            text-transform: uppercase;
            display: flex; align-items: center; gap: 8px;
        }
        .kw-ribbon.setor { background: #d1fae5; color: #065f46; }
        .kw-ribbon.tarik { background: #fee2e2; color: #991b1b; }

        /* Body */
        .kw-body { padding: 28px 32px; }

        .kw-section-label {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; color: #94a3b8; margin-bottom: 10px;
        }

        .kw-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 32px;
            margin-bottom: 24px;
        }
        .kw-info-item label {
            display: block; font-size: 0.68rem; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .8px; margin-bottom: 2px;
        }
        .kw-info-item span {
            font-size: 0.88rem; font-weight: 500; color: #1e293b;
        }

        /* Nominal box */
        .kw-nominal-box {
            background: linear-gradient(135deg, #eff6ff, #e0f2fe);
            border: 1.5px solid #bfdbfe;
            border-radius: 10px;
            padding: 18px 24px;
            text-align: center;
            margin: 20px 0;
        }
        .kw-nominal-box .label { font-size: 0.72rem; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .kw-nominal-box .amount { font-size: 1.8rem; font-weight: 700; color: #0f172a; margin-top: 4px; }

        /* Separator */
        .kw-separator {
            border: none; border-top: 1.5px dashed #e2e8f0;
            margin: 22px 0;
        }

        /* Footer */
        .kw-footer {
            padding: 20px 32px 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .kw-tanda-tangan {
            text-align: center;
            width: 160px;
        }
        .kw-tanda-tangan .ttd-box {
            height: 64px;
            border-bottom: 1.5px solid #1e293b;
        }
        .kw-tanda-tangan .ttd-label { font-size: 0.75rem; color: #64748b; margin-top: 6px; }

        .kw-stamp {
            width: 90px; height: 90px;
            border: 3px solid #3b82f6;
            border-radius: 50%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            color: #3b82f6;
            font-size: 0.55rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            text-align: center;
            opacity: .45;
            transform: rotate(-12deg);
        }
        .kw-stamp i { font-size: 1.4rem; margin-bottom: 4px; }

        /* Watermark LUNAS / VALID */
        .kw-watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 5rem;
            font-weight: 900;
            color: rgba(59,130,246,.07);
            pointer-events: none;
            letter-spacing: -2px;
            z-index: 0;
        }

        /* Print Button */
        .print-actions {
            text-align: center;
            margin-top: 24px;
            display: flex; gap: 12px; justify-content: center;
        }
        .btn-print {
            padding: 10px 28px;
            background: #0f172a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-back {
            padding: 10px 28px;
            background: #fff;
            color: #1e293b;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .kwitansi { box-shadow: none; border-radius: 0; }
            .print-actions { display: none; }
            #debugbar, #toolbarContainer { display: none !important; }
        }
        #debugbar, #toolbarContainer { display: none !important; }
    </style>
    <!-- Inline Font Awesome for print -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php
    $isSetor = $data['jenis_transaksi'] == 'setor';
    $noKwitansi = 'KW-SP-' . str_pad($data['id'], 5, '0', STR_PAD_LEFT);
?>

<div>
    <div class="kwitansi">
        <div class="kw-watermark">VALID</div>
        <?php if (!is_premium()): ?>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 7rem; font-weight: 900; color: rgba(0,0,0,0.08); pointer-events: none; letter-spacing: -2px; z-index: 9999; white-space: nowrap;">
            CirebonTech
        </div>
        <?php endif; ?>

        <!-- HEADER -->
        <div class="kw-header">
            <div class="kw-logo">
                <?php if(get_koperasi_logo()): ?>
                    <img src="<?= get_koperasi_logo() ?>" alt="Logo" style="max-height: 52px; border-radius: 6px;">
                <?php else: ?>
                    <i class="fas fa-landmark"></i>
                <?php endif; ?>
            </div>
            <div class="kw-title-block">
                <h1><?= esc(get_pengaturan('koperasi_nama', 'Koperasi Simpan Pinjam')) ?></h1>
                <p><?= esc(get_pengaturan('koperasi_alamat', 'Kwitansi Transaksi')) ?></p>
            </div>
            <div class="kw-badge">
                <div class="badge-label">No. Kwitansi</div>
                <div class="badge-no"><?= $noKwitansi ?></div>
            </div>
        </div>

        <!-- JENIS RIBBON -->
        <div class="kw-ribbon <?= $isSetor ? 'setor' : 'tarik' ?>">
            <i class="fas <?= $isSetor ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
            <?= $isSetor ? 'Setoran Simpanan (Masuk)' : 'Penarikan Simpanan (Keluar)' ?>
        </div>

        <!-- BODY -->
        <div class="kw-body">
            <div class="kw-section-label">Informasi Anggota</div>
            <div class="kw-info-grid">
                <div class="kw-info-item">
                    <label>Nama Anggota</label>
                    <span><?= esc($data['nama_lengkap']) ?></span>
                </div>
                <div class="kw-info-item">
                    <label>No. Anggota</label>
                    <span><?= esc($data['no_anggota']) ?></span>
                </div>
                <div class="kw-info-item">
                    <label>Jenis Simpanan</label>
                    <span><?= esc($data['nama_simpanan']) ?></span>
                </div>
                <div class="kw-info-item">
                    <label>Tanggal Transaksi</label>
                    <span><?= date('d F Y', strtotime($data['tanggal_transaksi'])) ?></span>
                </div>
            </div>

            <!-- NOMINAL -->
            <div class="kw-nominal-box">
                <div class="label"><?= $isSetor ? 'Jumlah Disetor' : 'Jumlah Ditarik' ?></div>
                <div class="amount">Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></div>
            </div>

            <?php if(!empty($data['keterangan'])): ?>
            <div style="background:#f8fafc;border-radius:8px;padding:12px 16px;border:1px solid #e2e8f0;margin-top:4px;">
                <div style="font-size:0.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:3px;">Keterangan</div>
                <div style="font-size:0.88rem;color:#334155;"><?= esc($data['keterangan']) ?></div>
            </div>
            <?php endif; ?>

            <hr class="kw-separator">

            <div style="font-size:0.75rem;color:#94a3b8;text-align:center;">
                Dicetak pada: <?= date('d F Y, H:i') ?> WIB &nbsp;·&nbsp; Dokumen ini sah tanpa tanda tangan basah.
            </div>
        </div>

        <!-- FOOTER -->
        <div class="kw-footer">
            <div class="kw-tanda-tangan">
                <div class="ttd-box"></div>
                <div class="ttd-label">Tanda Tangan Penerima</div>
            </div>
            <div class="kw-stamp">
                <i class="fas fa-check-circle"></i>
                <span>Telah<br>Diverifikasi</span>
            </div>
            <div class="kw-tanda-tangan">
                <div class="ttd-box"></div>
                <div class="ttd-label">Petugas Koperasi</div>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS (tidak ikut print) -->
    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <a class="btn-back" href="javascript:history.back()">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<script>
    // Auto-print saat halaman terbuka (opsional, bisa di-comment)
    // window.onload = () => window.print();
</script>
</body>
</html>
