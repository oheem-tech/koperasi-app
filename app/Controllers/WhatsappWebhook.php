<?php

namespace App\Controllers;

use App\Libraries\WaGateway;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;

/**
 * WhatsappWebhook Controller
 *
 * Menerima pesan masuk (inbound) dari Fonnte dan membalas otomatis
 * berdasarkan perintah yang dikirimkan anggota koperasi.
 *
 * Endpoint ini TIDAK membutuhkan autentikasi (dipanggil oleh server Fonnte).
 * URL yang perlu didaftarkan di Fonnte: https://[domain]/webhook/whatsapp
 */
class WhatsappWebhook extends BaseController
{
    protected $anggotaModel;
    protected $pengaturanModel;
    protected $waGateway;

    public function __construct()
    {
        $this->anggotaModel   = new AnggotaModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->waGateway      = new WaGateway();
    }

    /**
     * Endpoint utama yang menerima POST dari Fonnte.
     * Daftarkan URL ini di Fonnte Dashboard → Device → Webhook.
     */
    public function receive()
    {
        // --- 1. Cek apakah fitur chatbot aktif ---
        $chatbotAktif = $this->pengaturanModel->where('pengaturan_key', 'wa_chatbot_aktif')->first();
        if (!$chatbotAktif || $chatbotAktif['pengaturan_value'] != '1') {
            return $this->response->setJSON(['status' => 'ignored', 'reason' => 'chatbot_nonaktif']);
        }

        // --- 2. Ambil data dari Fonnte ---
        // Fonnte bisa mengirim data sebagai Form Data (POST) atau JSON.
        $sender = '';
        $message = '';
        
        $json = $this->request->getJSON();
        if ($json) {
            $sender  = $json->sender ?? '';
            $message = trim($json->message ?? '');
        } else {
            $sender  = $this->request->getPost('sender');
            $message = trim($this->request->getPost('message') ?? '');
        }

        // Jika tidak ada sender atau pesan, abaikan
        if (empty($sender) || empty($message)) {
            log_message('error', 'WA Chatbot: Payload kosong atau format tidak dikenali.');
            return $this->response->setJSON(['status' => 'ignored', 'reason' => 'empty_payload']);
        }

        log_message('info', 'WA Chatbot: Pesan dari ' . $sender . ' → "' . $message . '"');

        // --- 3. Normalkan nomor pengirim (Fonnte kirim dalam format 628xx) ---
        $senderNormalized = $this->waGateway->formatPhoneNumber($sender);

        // --- 4. Cari anggota berdasarkan nomor HP ---
        $anggota = $this->cariAnggotaByNomor($senderNormalized);

        if (!$anggota) {
            $balasan = "⚠️ Maaf, nomor WhatsApp Anda ({$sender}) tidak terdaftar sebagai anggota koperasi.\n\nJika Anda anggota koperasi, silakan hubungi pengurus untuk memperbarui nomor HP Anda.";
            $this->waGateway->sendMessage($sender, $balasan);
            return $this->response->setJSON(['status' => 'replied', 'result' => 'not_registered']);
        }

        // --- 5. Parse perintah ---
        $perintah = strtoupper(trim($message));

        switch ($perintah) {
            case 'SALDO':
                $balasan = $this->handleSaldo($anggota);
                break;
            case 'PINJAMAN':
                $balasan = $this->handlePinjaman($anggota);
                break;
            case 'ANGSURAN':
                $balasan = $this->handleAngsuran($anggota);
                break;
            case 'INFO':
                $balasan = $this->handleInfo($anggota);
                break;
            case 'BANTUAN':
            case 'HELP':
            case 'MENU':
                $balasan = $this->handleBantuan($anggota);
                break;
            default:
                $balasan = "❓ Perintah *\"{$message}\"* tidak dikenal.\n\nKetik *BANTUAN* untuk melihat daftar perintah yang tersedia.";
                break;
        }

        // --- 6. Kirim balasan ---
        $this->waGateway->sendMessage($sender, $balasan);

        return $this->response->setJSON(['status' => 'replied', 'perintah' => $perintah]);
    }

    // =========================================================================
    //  HELPER: Cari anggota berdasarkan nomor HP (handle format campuran)
    // =========================================================================

    /**
     * Cari anggota di database berdasarkan nomor HP pengirim.
     * Menangani format campuran: 08xx, 628xx, +628xx, dll.
     */
    private function cariAnggotaByNomor($senderNormalized)
    {
        // Ambil core number: lepas prefix 62
        // Contoh: 6281234567890 → 81234567890
        $core = ltrim($senderNormalized, '0');
        if (substr($core, 0, 2) === '62') {
            $core = substr($core, 2);
        }

        // Cari semua anggota aktif, lalu cocokkan secara manual
        $semuaAnggota = $this->anggotaModel->where('status', 'aktif')->findAll();

        foreach ($semuaAnggota as $anggota) {
            $dbPhone = $this->waGateway->formatPhoneNumber($anggota['no_telp'] ?? '');
            if (empty($dbPhone)) continue;

            // Normalisasi DB phone ke core
            $dbCore = ltrim($dbPhone, '0');
            if (substr($dbCore, 0, 2) === '62') {
                $dbCore = substr($dbCore, 2);
            }

            // Cocokkan core number
            if ($core === $dbCore && strlen($core) >= 8) {
                return $anggota;
            }
        }

        return null;
    }

    // =========================================================================
    //  HANDLER: SALDO
    // =========================================================================

    private function handleSaldo($anggota)
    {
        $db = \Config\Database::connect();

        $rows = $db->table('simpanan')
            ->select('jenis_simpanan.nama_simpanan,
                      SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE 0 END) -
                      SUM(CASE WHEN jenis_transaksi = "tarik" THEN jumlah ELSE 0 END) AS saldo')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('simpanan.anggota_id', $anggota['id'])
            ->groupBy('simpanan.jenis_simpanan_id')
            ->get()->getResultArray();

        if (empty($rows)) {
            return "📋 *INFO SALDO SIMPANAN*\n\nHalo *{$anggota['nama_lengkap']}*,\nBelum ada data simpanan yang tercatat untuk akun Anda.";
        }

        $totalSaldo = 0;
        $detail = '';
        foreach ($rows as $row) {
            $saldo = max(0, (float)$row['saldo']);
            $totalSaldo += $saldo;
            $detail .= "• {$row['nama_simpanan']}: *Rp " . number_format($saldo, 0, ',', '.') . "*\n";
        }

        $tgl = date('d/m/Y H:i');
        return "📋 *INFO SALDO SIMPANAN*\n\n"
            . "Halo *{$anggota['nama_lengkap']}* (No. {$anggota['no_anggota']})\n\n"
            . $detail
            . "─────────────────\n"
            . "💰 *Total Saldo: Rp " . number_format($totalSaldo, 0, ',', '.') . "*\n\n"
            . "📅 Per: {$tgl}\n\n"
            . "_Ketik BANTUAN untuk perintah lain._";
    }

    // =========================================================================
    //  HANDLER: PINJAMAN
    // =========================================================================

    private function handlePinjaman($anggota)
    {
        $db = \Config\Database::connect();

        // Ambil pinjaman yang masih aktif (disetujui)
        $pinjaman = $db->table('pinjaman')
            ->where('anggota_id', $anggota['id'])
            ->whereIn('status', ['disetujui', 'aktif'])
            ->get()->getResultArray();

        if (empty($pinjaman)) {
            return "📋 *INFO PINJAMAN*\n\nHalo *{$anggota['nama_lengkap']}*,\nAnda tidak memiliki pinjaman aktif saat ini.";
        }

        $detail = '';
        $totalSisa = 0;

        foreach ($pinjaman as $p) {
            // Hitung total angsuran yang sudah dibayar (pokok saja)
            $sudahBayar = $db->table('angsuran')
                ->selectSum('jumlah_pokok')
                ->where('pinjaman_id', $p['id'])
                ->get()->getRow()->jumlah_pokok ?? 0;

            $sisa = max(0, (float)$p['jumlah_pinjaman'] - (float)$sudahBayar);
            $totalSisa += $sisa;

            $tglJatuhTempo = $p['tanggal_jatuh_tempo']
                ? date('d/m/Y', strtotime($p['tanggal_jatuh_tempo']))
                : '-';

            $detail .= "• Pinjaman: *Rp " . number_format($p['jumlah_pinjaman'], 0, ',', '.') . "*\n"
                . "  Tenor: {$p['lama_tenor']} bulan | Bunga: {$p['bunga_persen']}%\n"
                . "  Sisa hutang: *Rp " . number_format($sisa, 0, ',', '.') . "*\n"
                . "  Jatuh tempo: {$tglJatuhTempo}\n\n";
        }

        $tgl = date('d/m/Y H:i');
        return "📋 *INFO PINJAMAN AKTIF*\n\n"
            . "Halo *{$anggota['nama_lengkap']}* (No. {$anggota['no_anggota']})\n\n"
            . $detail
            . "─────────────────\n"
            . "💳 *Total Sisa Hutang: Rp " . number_format($totalSisa, 0, ',', '.') . "*\n\n"
            . "📅 Per: {$tgl}\n\n"
            . "_Ketik ANGSURAN untuk cek tagihan, BANTUAN untuk perintah lain._";
    }

    // =========================================================================
    //  HANDLER: ANGSURAN
    // =========================================================================

    private function handleAngsuran($anggota)
    {
        $db = \Config\Database::connect();

        // Ambil pinjaman aktif
        $pinjaman = $db->table('pinjaman')
            ->where('anggota_id', $anggota['id'])
            ->whereIn('status', ['disetujui', 'aktif'])
            ->get()->getResultArray();

        if (empty($pinjaman)) {
            return "📋 *INFO ANGSURAN*\n\nHalo *{$anggota['nama_lengkap']}*,\nAnda tidak memiliki pinjaman aktif saat ini.";
        }

        $detail = '';

        foreach ($pinjaman as $p) {
            // Hitung cicilan ke berapa sudah dibayar
            $jumlahAngsuranDibayar = $db->table('angsuran')
                ->where('pinjaman_id', $p['id'])
                ->countAllResults();

            $sudahBayarPokok = (float)($db->table('angsuran')
                ->selectSum('jumlah_pokok')
                ->where('pinjaman_id', $p['id'])
                ->get()->getRow()->jumlah_pokok ?? 0);

            $sudahBayarJasa = (float)($db->table('angsuran')
                ->selectSum('jumlah_jasa')
                ->where('pinjaman_id', $p['id'])
                ->get()->getRow()->jumlah_jasa ?? 0);

            $sisaPokok = max(0, (float)$p['jumlah_pinjaman'] - $sudahBayarPokok);
            $tenorSisa = max(0, (int)$p['lama_tenor'] - $jumlahAngsuranDibayar);

            // Estimasi angsuran per bulan (flat)
            $bungaTotal = (float)$p['jumlah_pinjaman'] * ((float)$p['bunga_persen'] / 100) * ((int)$p['lama_tenor'] / 12);
            $totalBayar = (float)$p['jumlah_pinjaman'] + $bungaTotal;
            $angsuranPerBulan = $p['lama_tenor'] > 0 ? $totalBayar / (int)$p['lama_tenor'] : 0;

            $detail .= "• Pinjaman Rp " . number_format($p['jumlah_pinjaman'], 0, ',', '.') . "\n"
                . "  Sudah dibayar: {$jumlahAngsuranDibayar} dari {$p['lama_tenor']} cicilan\n"
                . "  Sisa cicilan: *{$tenorSisa} kali*\n"
                . "  Sisa pokok: *Rp " . number_format($sisaPokok, 0, ',', '.') . "*\n"
                . "  Angsuran/bulan: ≈Rp " . number_format($angsuranPerBulan, 0, ',', '.') . "\n\n";
        }

        $tgl = date('d/m/Y H:i');
        return "📋 *INFO ANGSURAN*\n\n"
            . "Halo *{$anggota['nama_lengkap']}* (No. {$anggota['no_anggota']})\n\n"
            . $detail
            . "📅 Per: {$tgl}\n\n"
            . "_Ketik PINJAMAN untuk detail, BANTUAN untuk perintah lain._";
    }

    // =========================================================================
    //  HANDLER: INFO (gabungan SALDO + PINJAMAN)
    // =========================================================================

    private function handleInfo($anggota)
    {
        $db = \Config\Database::connect();

        // === Saldo ===
        $saldoRows = $db->table('simpanan')
            ->select('jenis_simpanan.nama_simpanan,
                      SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE 0 END) -
                      SUM(CASE WHEN jenis_transaksi = "tarik" THEN jumlah ELSE 0 END) AS saldo')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('simpanan.anggota_id', $anggota['id'])
            ->groupBy('simpanan.jenis_simpanan_id')
            ->get()->getResultArray();

        $totalSaldo = 0;
        $saldoDetail = '';
        foreach ($saldoRows as $row) {
            $s = max(0, (float)$row['saldo']);
            $totalSaldo += $s;
            $saldoDetail .= "  • {$row['nama_simpanan']}: Rp " . number_format($s, 0, ',', '.') . "\n";
        }
        if (empty($saldoDetail)) $saldoDetail = "  Belum ada data simpanan.\n";

        // === Pinjaman ===
        $pinjamanAktif = $db->table('pinjaman')
            ->where('anggota_id', $anggota['id'])
            ->whereIn('status', ['disetujui', 'aktif'])
            ->get()->getResultArray();

        $totalSisaHutang = 0;
        $pinjamanDetail = '';
        foreach ($pinjamanAktif as $p) {
            $sudahBayar = (float)($db->table('angsuran')
                ->selectSum('jumlah_pokok')
                ->where('pinjaman_id', $p['id'])
                ->get()->getRow()->jumlah_pokok ?? 0);
            $sisa = max(0, (float)$p['jumlah_pinjaman'] - $sudahBayar);
            $totalSisaHutang += $sisa;
            $pinjamanDetail .= "  • Sisa hutang: Rp " . number_format($sisa, 0, ',', '.') . "\n";
        }
        if (empty($pinjamanDetail)) $pinjamanDetail = "  Tidak ada pinjaman aktif.\n";

        $tgl = date('d/m/Y H:i');
        return "📊 *RINGKASAN AKUN KOPERASI*\n\n"
            . "Halo *{$anggota['nama_lengkap']}*\n"
            . "No. Anggota: {$anggota['no_anggota']}\n\n"
            . "💰 *SIMPANAN:*\n"
            . $saldoDetail
            . "Total: *Rp " . number_format($totalSaldo, 0, ',', '.') . "*\n\n"
            . "💳 *PINJAMAN AKTIF:*\n"
            . $pinjamanDetail
            . "Total sisa hutang: *Rp " . number_format($totalSisaHutang, 0, ',', '.') . "*\n\n"
            . "📅 Per: {$tgl}\n\n"
            . "_Ketik BANTUAN untuk perintah lain._";
    }

    // =========================================================================
    //  HANDLER: BANTUAN
    // =========================================================================

    private function handleBantuan($anggota)
    {
        return "🏦 *KOPERASI — LAYANAN WA*\n\n"
            . "Halo *{$anggota['nama_lengkap']}*! 👋\n\n"
            . "Berikut perintah yang tersedia:\n\n"
            . "📌 *SALDO* — Cek saldo simpanan Anda\n"
            . "📌 *PINJAMAN* — Cek sisa pinjaman aktif\n"
            . "📌 *ANGSURAN* — Cek detail cicilan\n"
            . "📌 *INFO* — Ringkasan lengkap akun Anda\n"
            . "📌 *BANTUAN* — Tampilkan menu ini\n\n"
            . "_Ketik salah satu perintah di atas (huruf besar/kecil tidak masalah)._";
    }
}
