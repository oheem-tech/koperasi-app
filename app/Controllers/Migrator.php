<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AnggotaModel;
use App\Models\KasKoperasiModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\AngsuranModel;
use CodeIgniter\Controller;

class Migrator extends BaseController
{
    protected $legacyDb;
    protected $defaultDb;

    public function __construct()
    {
        $this->legacyDb = \Config\Database::connect('legacy');
        $this->defaultDb = \Config\Database::connect();
    }

    public function index()
    {
        return "Migrator System Ready. Gunakan /migrator/reset?confirm=yes untuk bersihkan data, dan /migrator/eksekusi?confirm=yes untuk migrasi.";
    }

    public function reset()
    {
        // Peringatan Hati-hati
        if (!$this->request->getGet('confirm')) {
            return "Apakah Anda yakin ingin RESET? Buka URL ini: /migrator/reset?confirm=yes";
        }

        // Disable Foreign Key Checks temporarily
        $this->defaultDb->query('SET FOREIGN_KEY_CHECKS=0');

        // Truncate Transaksi
        $this->defaultDb->table('angsuran')->truncate();
        $this->defaultDb->table('pinjaman')->truncate();
        $this->defaultDb->table('simpanan')->truncate();
        $this->defaultDb->table('kas_koperasi')->truncate();
        $this->defaultDb->table('anggota')->truncate();

        // Hapus hanya User dengan Role = anggota
        // Kita tidak men-truncate karena akan menghapus Admin!
        $this->defaultDb->table('users')->where('role', 'anggota')->delete();

        // Re-enable Foreign Key Checks
        $this->defaultDb->query('SET FOREIGN_KEY_CHECKS=1');

        return "Reset Selesai! Data transaksi telah menjadi Rp 0.";
    }

    public function eksekusi()
    {
        ini_set('max_execution_time', 300); // 5 menit karena data banyak

        if (!$this->request->getGet('confirm')) {
            return "Peringatan: Pastikan sudah RESET. Buka URL ini untuk Eksekusi: /migrator/eksekusi?confirm=yes";
        }

        $userModel = new UserModel();
        $anggotaModel = new AnggotaModel();
        $kasModel = new KasKoperasiModel();
        $simpananModel = new SimpananModel();
        $pinjamanModel = new PinjamanModel();
        $angsuranModel = new AngsuranModel();

        // 1. MIGRASI ANGGOTA
        $leg_anggota = $this->legacyDb->table('tbl_anggota')->get()->getResultArray();
        $anggota_map  = []; // Map: old anggota_id => new anggota_id
        $nama_anggota_map = []; // Map: old anggota_id => nama_lengkap (untuk keterangan kas)

        foreach ($leg_anggota as $la) {
            // Hasilkan password default: identitas / nik mereka, atau '123456'
            $defaultPassword = !empty($la['identitas']) ? $la['identitas'] : '123456';
            if (strlen($defaultPassword) < 5) $defaultPassword = '123456';
            
            // Buat username unik berdasar nama
            $username = strtolower(str_replace([' ', ',', '.', '(', ')', "'", '"'], '', substr($la['nama'], 0, 8))) . rand(10,99);

            try {
                // Insert to Users
                $this->defaultDb->table('users')->insert([
                    'username' => $username,
                    'password' => password_hash($defaultPassword, PASSWORD_DEFAULT),
                    'role' => 'anggota'
                ]);
                $userId = $this->defaultDb->insertID();

                // Tentukan Jabatan di Koperasi kita (sesuai ENUM: anggota|pengurus|pengawas|pembina)
                $jabatan = 'anggota';
                if (!empty($la['Pengurus'])) {
                    $pengurus_lower = strtolower($la['Pengurus']);
                    if (strpos($pengurus_lower, 'pengawas') !== false)       $jabatan = 'pengawas';
                    elseif (strpos($pengurus_lower, 'pengurus') !== false)   $jabatan = 'pengurus';
                    elseif (strpos($pengurus_lower, 'pembina') !== false)    $jabatan = 'pembina';
                }

                // Insert to Anggota
                $this->defaultDb->table('anggota')->insert([
                    'user_id'          => $userId,
                    'no_anggota'       => 'A' . str_pad($la['id'], 4, '0', STR_PAD_LEFT),
                    'nama_lengkap'     => $la['nama'],
                    'alamat'           => $la['alamat'] . ' ' . $la['kota'],
                    'no_telp'          => $la['notelp'],
                    'tanggal_bergabung'=> $la['tgl_daftar'],
                    'status'           => ($la['aktif'] == 'Y') ? 'aktif' : 'nonaktif',
                    'jabatan'          => $jabatan,
                    'kelompok'         => !empty($la['departement']) ? $la['departement'] : (!empty($la['pekerjaan']) ? $la['pekerjaan'] : 'Umum')
                ]);
                $anggotaId = $this->defaultDb->insertID();

                $anggota_map[$la['id']]       = $anggotaId;
                $nama_anggota_map[$la['id']]  = $la['nama'];
            } catch (\Exception $e) {
                // Log failed member migration
                log_message('error', 'Error Migrasi Anggota ID ' . $la['id'] . ': ' . $e->getMessage());
            }
        }

        // 2. MIGRASI SIMPANAN
        // Mapping jenis simpanan dari DB lama (id 40=Pokok, 41=Wajib, 32=SHR) ke DB baru
        $jenis_simpanan_lama = $this->legacyDb->table('jns_simpan')->get()->getResultArray();
        $jenis_simpan_map = [];
        foreach ($jenis_simpanan_lama as $js) {
            $nama_lower = strtolower($js['jns_simpan']);
            if (strpos($nama_lower, 'pokok') !== false)  $jenis_simpan_map[$js['id']] = ['id_baru' => 1, 'nama' => 'Simpanan Pokok'];
            elseif (strpos($nama_lower, 'wajib') !== false) $jenis_simpan_map[$js['id']] = ['id_baru' => 2, 'nama' => 'Simpanan Wajib'];
            else $jenis_simpan_map[$js['id']] = ['id_baru' => 3, 'nama' => $js['jns_simpan']];
        }

        $leg_simpanan = $this->legacyDb->table('tbl_trans_sp')->get()->getResultArray();
        foreach ($leg_simpanan as $ls) {
            if (!isset($anggota_map[$ls['anggota_id']])) continue;

            $namaAnggota = $nama_anggota_map[$ls['anggota_id']] ?? 'Anggota';
            $jenisSimpanInfo = $jenis_simpan_map[$ls['jenis_id']] ?? ['id_baru' => 3, 'nama' => 'Simpanan'];
            $aksiLabel = ($ls['akun'] == 'Setoran') ? 'Setoran' : 'Penarikan';

            // Format keterangan sama persis dengan Simpanan::store()
            $keteranganKas = $aksiLabel . ' ' . $jenisSimpanInfo['nama'] . ' - ' . $namaAnggota;
            $jenisKas = ($ls['akun'] == 'Setoran') ? 'masuk' : 'keluar';

            $kasId = $kasModel->catatTransaksi(
                $ls['tgl_transaksi'],
                $keteranganKas,
                $jenisKas,
                $ls['jumlah'],
                'simpanan'
            );

            $simpananModel->insert([
                'anggota_id'        => $anggota_map[$ls['anggota_id']],
                'jenis_simpanan_id' => $jenisSimpanInfo['id_baru'],
                'tanggal_transaksi' => $ls['tgl_transaksi'],
                'jumlah'            => $ls['jumlah'],
                'jenis_transaksi'   => ($ls['akun'] == 'Setoran') ? 'setor' : 'tarik',
                'keterangan'        => $ls['keterangan'],
                'kas_id'            => $kasId
            ]);
        }

        // 3. MIGRASI PINJAMAN (Header)
        // Ambil data barang untuk membedakan Barang biasa dan Emas
        $leg_barang = $this->legacyDb->table('tbl_barang')->get()->getResultArray();
        $barang_map = [];
        foreach ($leg_barang as $brg) {
            // Asumsi kolom id dan nama barang (bisa disesuaikan fieldnya misal nm_barang)
            $kolom_nama = isset($brg['nama_barang']) ? $brg['nama_barang'] : (isset($brg['nm_barang']) ? $brg['nm_barang'] : '');
            $barang_map[$brg['id']] = strtolower($kolom_nama);
        }

        $leg_pinjaman = $this->legacyDb->table('tbl_pinjaman_h')->get()->getResultArray();
        $pinjaman_map      = []; // Old pinjaman_id => New pinjaman_id
        $pinjaman_meta_map = []; // New pinjaman_id => [nama_anggota, kode, jenis]

        foreach ($leg_pinjaman as $lp) {
            if (!isset($anggota_map[$lp['anggota_id']])) continue;

            $namaAnggota = $nama_anggota_map[$lp['anggota_id']] ?? 'Anggota';

            $status = ($lp['lunas'] == 'Lunas') ? 'Lunas' : 'Disetujui';
            $tempo  = date('Y-m-d', strtotime($lp['tgl_pinjam'] . ' + ' . $lp['lama_angsuran'] . ' months'));

            // Cek jenis pinjaman berdasarkan legacy `barang_id` dan cek kata 'emas'
            $jenis_pinjaman = 'Uang';
            if (!empty($lp['barang_id']) && (int)$lp['barang_id'] > 0) {
                $nama_b = $barang_map[$lp['barang_id']] ?? '';
                if (strpos($nama_b, 'emas') !== false || strpos($nama_b, 'logam mulia') !== false || strpos($nama_b, 'lm') === 0 || strpos($nama_b, 'antam') !== false) {
                    $jenis_pinjaman = 'Emas';
                } else {
                    $jenis_pinjaman = 'Barang';
                }
            }

            $this->defaultDb->table('pinjaman')->insert([
                'anggota_id'          => $anggota_map[$lp['anggota_id']],
                'jenis_pinjaman'      => $jenis_pinjaman,
                'tanggal_pengajuan'   => $lp['tgl_pinjam'],
                'jumlah_pinjaman'     => $lp['jumlah'],
                'lama_tenor'          => $lp['lama_angsuran'],
                'bunga_persen'        => $lp['bunga'],
                'tanggal_jatuh_tempo' => $tempo,
                'status'              => $status
            ]);
            $pinjamanId = $this->defaultDb->insertID();

            // Format kode pinjaman sama persis dengan Angsuran::store()
            $kodePinjaman = 'PJ-' . str_pad($pinjamanId, 4, '0', STR_PAD_LEFT);

            // Catat Pengeluaran Kas untuk Cair Pinjaman
            $kasId = $kasModel->catatTransaksi(
                $lp['tgl_pinjam'],
                'Pencairan Pinjaman ' . $jenis_pinjaman . ' - ' . $namaAnggota . ' [' . $kodePinjaman . ']',
                'keluar',
                $lp['jumlah'],
                'pinjaman'
            );

            $pinjaman_map[$lp['id']]      = $pinjamanId;
            $pinjaman_meta_map[$pinjamanId] = ['nama' => $namaAnggota, 'kode' => $kodePinjaman, 'jenis' => $jenis_pinjaman];
        }

        // 4. MIGRASI ANGSURAN (Detail)
        $leg_angsuran = $this->legacyDb->table('tbl_pinjaman_d')->get()->getResultArray();
        foreach ($leg_angsuran as $la) {
            if (!isset($pinjaman_map[$la['pinjam_id']])) continue;

            $newPinjamanId = $pinjaman_map[$la['pinjam_id']];
            $meta          = $pinjaman_meta_map[$newPinjamanId] ?? ['nama' => 'Anggota', 'kode' => 'PJ-0000', 'jenis' => ''];
            $jenisAngsuran = ($la['ket_bayar'] == 'Pelunasan') ? 'Pelunasan Pinjaman' : 'Setoran Angsuran';

            // Format keterangan sama persis dengan Angsuran::store()
            $keteranganKas = trim($jenisAngsuran . ' ' . $meta['jenis']) . ' - ' . $meta['nama']
                           . ' (Cicilan ke-' . $la['angsuran_ke'] . ') [' . $meta['kode'] . ']';

            // Catat Pemasukan Kas
            $kasId = $kasModel->catatTransaksi(
                $la['tgl_bayar'],
                $keteranganKas,
                'masuk',
                $la['jumlah_bayar'],
                'angsuran'
            );

            $angsuranModel->insert([
                'pinjaman_id'  => $newPinjamanId,
                'tanggal_bayar'=> $la['tgl_bayar'],
                'jumlah_bayar' => $la['jumlah_bayar'],
                'jumlah_pokok' => $la['pokok'],
                'jumlah_jasa'  => $la['jasa'],
                'denda'        => $la['denda_rp'],
                'cicilan_ke'   => $la['angsuran_ke'],
                'kas_id'       => $kasId
            ]);
        }

        // 5. MIGRASI KAS MANUAL (tbl_trans_kas)
        $leg_kas = $this->legacyDb->table('tbl_trans_kas')->get()->getResultArray();
        foreach ($leg_kas as $lk) {
            // Ignore Transfer
            if ($lk['akun'] == 'Transfer') continue;

            $jenisKas = ($lk['akun'] == 'Pemasukan') ? 'masuk' : 'keluar';

            // Gunakan keterangan asli langsung tanpa prefix apapun
            $kasModel->catatTransaksi(
                $lk['tgl_catat'],
                $lk['keterangan'],
                $jenisKas,
                $lk['jumlah'],
                'operasional'
            );
        }

        return "Proses Migrasi Skala Besar Berhasil Dieksekusi Secara Sempurna!";
    }
}
