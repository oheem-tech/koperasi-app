<?php

namespace App\Controllers;

use App\Models\AngsuranModel;
use App\Models\PinjamanModel;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;

class Angsuran extends BaseController
{
    protected $angsuranModel;
    protected $pinjamanModel;
    protected $anggotaModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->angsuranModel = new AngsuranModel();
        $this->pinjamanModel = new PinjamanModel();
        $this->anggotaModel = new AnggotaModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('angsuran');
        $builder->select('angsuran.*, pinjaman.status as pinjaman_status, pinjaman.jumlah_pinjaman, pinjaman.lama_tenor, pinjaman.bunga_persen, pinjaman.jenis_pinjaman, anggota.nama_lengkap, anggota.no_anggota, kas_koperasi.keterangan as kas_keterangan');
        $builder->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id');
        $builder->join('anggota', 'anggota.id = pinjaman.anggota_id');
        $builder->join('kas_koperasi', 'kas_koperasi.id = angsuran.kas_id', 'left');
        
        $anggotaList = [];
        if (!has_permission('manage_angsuran')) {
            $anggota = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if ($anggota) {
                $builder->where('pinjaman.anggota_id', $anggota['id']);
            } else {
                $builder->where('pinjaman.anggota_id', 0);
            }
        } else {
            $anggotaList = $this->anggotaModel->findAll();
            if ($this->request->getGet('anggota_id')) {
                $builder->where('pinjaman.anggota_id', $this->request->getGet('anggota_id'));
            }
        }
        
        $builder->orderBy('angsuran.tanggal_bayar', 'DESC');
        $builder->orderBy('angsuran.id', 'DESC');
        
        $data = [
            'title' => 'Data Angsuran | Koperasi',
            'angsuran' => $builder->get()->getResultArray(),
            'anggota' => $anggotaList,
            'selected_anggota' => $this->request->getGet('anggota_id')
        ];
        return view('angsuran/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $data = [
            'title'   => 'Input Pembayaran Angsuran',
            'anggota' => $this->anggotaModel->where('status', 'aktif')->findAll()
        ];
        return view('angsuran/create', $data);
    }

    public function getPinjamanByAnggota($anggota_id)
    {
        if (!has_permission('manage_angsuran')) return $this->response->setStatusCode(403);

        $db = \Config\Database::connect();
        $pinjaman = $db->table('pinjaman')
            ->where('anggota_id', $anggota_id)
            ->where('status', 'disetujui')
            ->get()->getResultArray();

        // Ambil pengaturan master terkait pelunasan
        $minTenorConf  = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_min_tenor_persen')->first();
        $aktifConf     = $this->pengaturanModel->where('pengaturan_key', 'kebijakan_pelunasan_aktif')->first();
        $jasaBebasConf = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_jasa_bebas_persen')->first();
        
        $min_persen        = $minTenorConf  ? (float)$minTenorConf['pengaturan_value']  : 50;
        $kebijakan_aktif   = $aktifConf     ? (int)$aktifConf['pengaturan_value']        : 1;
        $jasa_bebas_persen = $jasaBebasConf ? (float)$jasaBebasConf['pengaturan_value'] : 100;

        foreach ($pinjaman as &$p) {
            $sudahDibayar = $this->angsuranModel
                ->where('pinjaman_id', $p['id'])
                ->countAllResults();
            
            $p['cicilan_dibayar'] = $sudahDibayar;
            $p['cicilan_berikutnya'] = $p['cicilan_dibayar'] + 1;
            
            // Cicilan normal bulanan
            $p['pokok_per_bulan'] = round($p['jumlah_pinjaman'] / $p['lama_tenor']);
            $p['jasa_per_bulan']  = round($p['jumlah_pinjaman'] * ($p['bunga_persen'] / 100));
            $p['cicilan_per_bulan'] = $p['pokok_per_bulan'] + $p['jasa_per_bulan'];

            // Kalkulasi Pelunasan
            $tenor = (int)$p['lama_tenor'];
            $jumlah_pokok = (float)$p['jumlah_pinjaman'];
            $pokok_terbayar = $sudahDibayar * $p['pokok_per_bulan'];
            
            $p['sisa_pokok_lunas'] = max(0, $jumlah_pokok - $pokok_terbayar);

            $batas_cicilan  = ceil($tenor * ($min_persen / 100));
            $kena_jasa_full = $kebijakan_aktif && ($sudahDibayar < $batas_cicilan);

            $total_jasa_full = $jumlah_pokok * ($p['bunga_persen'] / 100) * $tenor;

            if ($kena_jasa_full) {
                // Belum memenuhi minimum tenor
                $jasa_terbayar = $this->angsuranModel->selectSum('jumlah_jasa')->where('pinjaman_id', $p['id'])->get()->getRow()->jumlah_jasa ?? 0;
                $sisa_jasa = max(0, $total_jasa_full - $jasa_terbayar);
            } else {
                // Sudah memenuhi minimum tenor / kebijakan tidak aktif
                $sisa_jasa = round($p['jasa_per_bulan'] * ($jasa_bebas_persen / 100));
            }

            $p['sisa_jasa_lunas'] = $sisa_jasa;
            $p['total_lunas'] = $p['sisa_pokok_lunas'] + $p['sisa_jasa_lunas'];
        }

        return $this->response->setJSON($pinjaman);
    }

    public function store()
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $pinjaman_id = $this->request->getPost('pinjaman_id');
        $jumlah_bayar = str_replace('.', '', $this->request->getPost('jumlah_bayar'));
        $denda = str_replace('.', '', $this->request->getPost('denda') ?? '0');
        $cicilan_ke = $this->request->getPost('cicilan_ke');
        $is_pelunasan = $this->request->getPost('is_pelunasan') == '1';
        
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Eksekusi Kas Koperasi terlebih dahulu untuk dapat id
        $kasModel = new \App\Models\KasKoperasiModel();
        $pinjaman = $this->pinjamanModel->find($pinjaman_id);
        
        $anggota = $pinjaman ? $this->anggotaModel->find($pinjaman['anggota_id']) : null;
        $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
        $kodePinjaman = $pinjaman ? 'PJ-' . str_pad($pinjaman['id'], 4, '0', STR_PAD_LEFT) : '';
        
        if ($is_pelunasan) {
            $keteranganKas = 'Pelunasan Pinjaman ' . ($pinjaman ? $pinjaman['jenis_pinjaman'] : '') . ' - ' . $namaAnggota . ' [' . $kodePinjaman . ']';
            // Paksa cicilan_ke menjadi lama_tenor agar tercatat lunas untuk logic yang lainnya
            if ($pinjaman) {
                $cicilan_ke = $pinjaman['lama_tenor'];
            }
        } else {
            $keteranganKas = 'Setoran Angsuran ' . ($pinjaman ? $pinjaman['jenis_pinjaman'] : '') . ' - ' . $namaAnggota . ' (Cicilan ke-' . $cicilan_ke . ') [' . $kodePinjaman . ']';
        }
        
        $kas_id = $kasModel->catatTransaksi($this->request->getPost('tanggal_bayar'), $keteranganKas, 'masuk', $jumlah_bayar, 'angsuran');

        // 2. Simpan Angsuran
        $this->angsuranModel->save([
            'pinjaman_id'   => $pinjaman_id,
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar'),
            'jumlah_bayar'  => $jumlah_bayar,
            'jumlah_pokok'  => str_replace('.', '', $this->request->getPost('jumlah_pokok')),
            'jumlah_jasa'   => str_replace('.', '', $this->request->getPost('jumlah_jasa')),
            'denda'         => $denda,
            'cicilan_ke'    => $cicilan_ke,
            'kas_id'        => $kas_id
        ]);

        if ($pinjaman && ($is_pelunasan || $cicilan_ke >= $pinjaman['lama_tenor'])) { 
            $this->pinjamanModel->update($pinjaman_id, ['status' => 'lunas']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran angsuran.');
        }

        // === WA NOTIFICATION ANGSURAN ===
        $waAktif = $this->pengaturanModel->where('pengaturan_key', 'wa_angsuran_aktif')->first();
        if ($waAktif && $waAktif['pengaturan_value'] == '1' && $anggota && !empty($anggota['no_telp'])) {
            $waTemplate = $this->pengaturanModel->where('pengaturan_key', 'wa_template_angsuran')->first();
            if ($waTemplate) {
                // Hitung sisa tagihan jika belum lunas, anggap sisa pokok
                $sisaTagihan = ($pinjaman && !$is_pelunasan && $cicilan_ke < $pinjaman['lama_tenor']) ? 
                               ($pinjaman['jumlah_pinjaman'] - ($pinjaman['jumlah_pinjaman'] / $pinjaman['lama_tenor'] * $cicilan_ke)) : 0;

                $pesan = str_replace(
                    ['{Nama}', '{Nominal}', '{Tanggal}', '{CicilanKe}', '{Sisa}'],
                    [$namaAnggota, number_format($jumlah_bayar, 0, ',', '.'), date('d/m/Y', strtotime($this->request->getPost('tanggal_bayar'))), $cicilan_ke, number_format($sisaTagihan, 0, ',', '.')],
                    $waTemplate['pengaturan_value']
                );
                
                $waService = new \App\Libraries\WaGateway();
                $waService->sendMessage($anggota['no_telp'], $pesan);
            }
        }
        // ================================

        return redirect()->to('/angsuran')->with('success', 'Pembayaran angsuran berhasil diproses.');
    }

    /**
     * Halaman konfirmasi + kalkulasi pelunasan
     */
    public function pelunasan($pinjaman_id)
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $pinjaman = $this->pinjamanModel->find($pinjaman_id);
        if (!$pinjaman || $pinjaman['status'] != 'disetujui') {
            return redirect()->to('/angsuran')->with('error', 'Pinjaman tidak ditemukan atau tidak aktif.');
        }

        // Ambil cicilan yang sudah dibayar
        $sudahDibayar = $this->angsuranModel->where('pinjaman_id', $pinjaman_id)->countAllResults();
        $tenor = (int)$pinjaman['lama_tenor'];
        $jumlah_pokok = (float)$pinjaman['jumlah_pinjaman'];
        $bunga_persen = (float)$pinjaman['bunga_persen'];

        // Total jasa/bunga seluruh tenor
        $total_jasa_full = $jumlah_pokok * ($bunga_persen / 100) * $tenor;

        // Ambil pengaturan kebijakan pelunasan
        $minTenorConf  = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_min_tenor_persen')->first();
        $aktifConf     = $this->pengaturanModel->where('pengaturan_key', 'kebijakan_pelunasan_aktif')->first();
        $jasaBebasConf = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_jasa_bebas_persen')->first();
        
        $min_persen      = $minTenorConf  ? (float)$minTenorConf['pengaturan_value']  : 50;
        $kebijakan_aktif = $aktifConf     ? (int)$aktifConf['pengaturan_value']        : 1;
        $jasa_bebas_persen = $jasaBebasConf ? (float)$jasaBebasConf['pengaturan_value'] : 100; // 0=gratis, 100=1 bln penuh

        $batas_cicilan = ceil($tenor * ($min_persen / 100));
        $kena_jasa_full = $kebijakan_aktif && ($sudahDibayar < $batas_cicilan);

        // Hitung sisa pokok yang belum dibayar
        $pokok_per_cicilan = $jumlah_pokok / $tenor;
        $pokok_terbayar = $sudahDibayar * $pokok_per_cicilan;
        $sisa_pokok = $jumlah_pokok - $pokok_terbayar;

        // Hitung jasa yang harus dibayar saat pelunasan
        $jasa_satu_bulan = $jumlah_pokok * ($bunga_persen / 100);
        if ($kena_jasa_full) {
            // Belum memenuhi minimum tenor → kenakan sisa jasa penuh
            $jasa_terbayar = $this->angsuranModel->selectSum('jumlah_jasa')->where('pinjaman_id', $pinjaman_id)->get()->getRow()->jumlah_jasa ?? 0;
            $sisa_jasa = $total_jasa_full - $jasa_terbayar;
        } else {
            // Sudah memenuhi minimum tenor → kenakan proporsional dari 1 bulan jasa sesuai pengaturan
            $sisa_jasa = round($jasa_satu_bulan * ($jasa_bebas_persen / 100));
        }

        $total_pelunasan = $sisa_pokok + $sisa_jasa;

        $db = \Config\Database::connect();
        $anggota = $db->table('anggota')->where('id', $pinjaman['anggota_id'])->get()->getRowArray();

        $data = [
            'title'              => 'Pelunasan Pinjaman',
            'pinjaman'           => $pinjaman,
            'anggota'            => $anggota,
            'sudah_dibayar'      => $sudahDibayar,
            'batas_cicilan'      => $batas_cicilan,
            'min_persen'         => $min_persen,
            'kena_jasa_full'     => $kena_jasa_full,
            'sisa_pokok'         => $sisa_pokok,
            'sisa_jasa'          => $sisa_jasa,
            'jasa_bebas_persen'  => $jasa_bebas_persen,
            'total_pelunasan'    => $total_pelunasan,
            'kebijakan_aktif'    => $kebijakan_aktif
        ];
        return view('angsuran/pelunasan', $data);
    }

    /**
     * Proses simpan pelunasan
     */
    public function prosespelunasan($pinjaman_id)
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $pinjaman = $this->pinjamanModel->find($pinjaman_id);
        if (!$pinjaman) return redirect()->to('/angsuran')->with('error', 'Pinjaman tidak ditemukan.');

        $total_pelunasan = str_replace('.', '', $this->request->getPost('total_pelunasan'));
        $sisa_pokok = str_replace('.', '', $this->request->getPost('sisa_pokok'));
        $sisa_jasa = str_replace('.', '', $this->request->getPost('sisa_jasa'));
        $cicilan_ke = (int)$pinjaman['lama_tenor']; // tandai lunas = angsuran ke-tenor

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Catat ke Kas
        $kasModel = new \App\Models\KasKoperasiModel();
        $anggota = $this->anggotaModel->find($pinjaman['anggota_id']);
        $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
        $kodePinjaman = 'PJ-' . str_pad($pinjaman_id, 4, '0', STR_PAD_LEFT);
        
        $keteranganKas = 'Pelunasan Pinjaman ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' [' . $kodePinjaman . ']';
        $kas_id = $kasModel->catatTransaksi(date('Y-m-d'), $keteranganKas, 'masuk', $total_pelunasan, 'angsuran');

        // 2. Simpan Angsuran
        $this->angsuranModel->save([
            'pinjaman_id'   => $pinjaman_id,
            'tanggal_bayar' => date('Y-m-d'),
            'jumlah_bayar'  => $total_pelunasan,
            'jumlah_pokok'  => $sisa_pokok,
            'jumlah_jasa'   => $sisa_jasa,
            'denda'         => 0,
            'cicilan_ke'    => $cicilan_ke,
            'kas_id'        => $kas_id
        ]);

        $this->pinjamanModel->update($pinjaman_id, ['status' => 'lunas']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal proses pelunasan.');
        }

        // === WA NOTIFICATION PELUNASAN (ANGSURAN) ===
        $waAktif = $this->pengaturanModel->where('pengaturan_key', 'wa_angsuran_aktif')->first();
        if ($waAktif && $waAktif['pengaturan_value'] == '1' && $anggota && !empty($anggota['no_telp'])) {
            $waTemplate = $this->pengaturanModel->where('pengaturan_key', 'wa_template_angsuran')->first();
            if ($waTemplate) {
                $pesan = str_replace(
                    ['{Nama}', '{Nominal}', '{Tanggal}', '{CicilanKe}', '{Sisa}'],
                    [$namaAnggota, number_format($total_pelunasan, 0, ',', '.'), date('d/m/Y'), 'Lunas', '0'],
                    $waTemplate['pengaturan_value']
                );
                
                $waService = new \App\Libraries\WaGateway();
                $waService->sendMessage($anggota['no_telp'], $pesan);
            }
        }
        // ================================

        return redirect()->to('/angsuran')->with('success', 'Pinjaman berhasil dilunasi!');
    }

    public function print($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('angsuran')
            ->select('angsuran.*, pinjaman.jumlah_pinjaman, pinjaman.lama_tenor, pinjaman.bunga_persen, pinjaman.jenis_pinjaman, anggota.nama_lengkap, anggota.no_anggota')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where('angsuran.id', $id)
            ->get()->getRowArray();

        if (!$row) return redirect()->to('/angsuran')->with('error', 'Data tidak ditemukan.');

        // Izinkan anggota cetak angsuran milik sendiri
        if (!has_permission('manage_angsuran')) {
            $anggotaSelf = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if (!$anggotaSelf || $anggotaSelf['id'] != $row['anggota_id']) {
                return redirect()->to('/angsuran')->with('error', 'Akses ditolak.');
            }
        }

        return view('angsuran/print_kwitansi', ['data' => $row]);
    }

    public function edit($id)
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $angsuran = $this->angsuranModel->find($id);
        if (!$angsuran) return redirect()->to('/angsuran')->with('error', 'Data angsuran tidak ditemukan.');

        $pinjaman = $this->pinjamanModel->find($angsuran['pinjaman_id']);
        if (!$pinjaman) return redirect()->to('/angsuran')->with('error', 'Data pinjaman terkait tidak ditemukan.');

        $data = [
            'title'    => 'Edit Transaksi Angsuran',
            'angsuran' => $angsuran,
            'pinjaman' => $pinjaman,
            'anggota'  => $this->anggotaModel->where('status', 'aktif')->findAll()
        ];
        return view('angsuran/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $angsuranLama = $this->angsuranModel->find($id);
        if (!$angsuranLama) return redirect()->to('/angsuran')->with('error', 'Data angsuran tidak ditemukan.');

        $pinjamanLama = $this->pinjamanModel->find($angsuranLama['pinjaman_id']);
        
        $jumlah_bayarBaru = str_replace('.', '', $this->request->getPost('jumlah_bayar'));
        $jumlah_pokokBaru = str_replace('.', '', $this->request->getPost('jumlah_pokok'));
        $jumlah_jasaBaru  = str_replace('.', '', $this->request->getPost('jumlah_jasa'));
        $dendaBaru        = str_replace('.', '', $this->request->getPost('denda') ?? '0');
        $tanggal_bayarBaru = $this->request->getPost('tanggal_bayar');
        // Cicilan_ke tidak diubah via edit form untuk menghindari loncat urutan
        $cicilan_ke       = $angsuranLama['cicilan_ke']; 
        
        $db = \Config\Database::connect();
        $db->transStart();

        // 1 & 2. Update Kas Lama atau Insert Baru
        $kasModel = new \App\Models\KasKoperasiModel();
        
        $anggotaLama = $this->anggotaModel->find($pinjamanLama['anggota_id']);
        $namaAnggotaLama = $anggotaLama ? $anggotaLama['nama_lengkap'] : 'Anggota';
        $kodePinjaman = 'PJ-' . str_pad($pinjamanLama['id'], 4, '0', STR_PAD_LEFT);
        $keteranganKasBaru = 'Setoran Angsuran ' . $pinjamanLama['jenis_pinjaman'] . ' - ' . $namaAnggotaLama . ' (Cicilan ke-' . $cicilan_ke . ') [' . $kodePinjaman . ']';
        
        if ($angsuranLama['kas_id']) {
            $kasModel->update($angsuranLama['kas_id'], [
                'tanggal'    => $tanggal_bayarBaru,
                'keterangan' => $keteranganKasBaru,
                'nominal'    => $jumlah_bayarBaru,
                'jenis'      => 'masuk',
                'kategori'   => 'angsuran'
            ]);
            $kas_id_baru = $angsuranLama['kas_id'];
        } else {
            // Fallback backward compatibility
            $ketKasLamaBiasa = 'Setoran Angsuran ' . $pinjamanLama['jenis_pinjaman'] . ' - ' . $namaAnggotaLama . ' (Cicilan ke-' . $cicilan_ke . ') [' . $kodePinjaman . ']';
            $ketKasLamaLunas = 'Pelunasan Pinjaman ' . $pinjamanLama['jenis_pinjaman'] . ' - ' . $namaAnggotaLama . ' [' . $kodePinjaman . ']';

            $kasSekarang = $kasModel->where('tanggal', $angsuranLama['tanggal_bayar'])
                                    ->where('nominal', $angsuranLama['jumlah_bayar'])
                                    ->groupStart()
                                        ->like('keterangan', $ketKasLamaBiasa, 'both')
                                        ->orLike('keterangan', $ketKasLamaLunas, 'both')
                                    ->groupEnd()
                                    ->first();
            
            if ($kasSekarang) {
                $kasModel->update($kasSekarang['id'], [
                    'tanggal'    => $tanggal_bayarBaru,
                    'keterangan' => $keteranganKasBaru,
                    'nominal'    => $jumlah_bayarBaru,
                    'jenis'      => 'masuk',
                    'kategori'   => 'angsuran'
                ]);
                $kas_id_baru = $kasSekarang['id'];
            } else {
                $kas_id_baru = $kasModel->catatTransaksi($tanggal_bayarBaru, $keteranganKasBaru, 'masuk', $jumlah_bayarBaru, 'angsuran');
            }
        }

        // 3. Update Data Angsuran
        $this->angsuranModel->update($id, [
            'tanggal_bayar' => $tanggal_bayarBaru,
            'jumlah_bayar'  => $jumlah_bayarBaru,
            'jumlah_pokok'  => $jumlah_pokokBaru,
            'jumlah_jasa'   => $jumlah_jasaBaru,
            'denda'         => $dendaBaru,
            'kas_id'        => $kas_id_baru
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pembaruan angsuran.');
        }

        return redirect()->to('/angsuran')->with('success', 'Transaksi angsuran berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_angsuran')) return redirect()->to('/dashboard');

        $angsuran = $this->angsuranModel->find($id);
        if (!$angsuran) return redirect()->to('/angsuran')->with('error', 'Data angsuran tidak ditemukan.');

        $pinjaman = $this->pinjamanModel->find($angsuran['pinjaman_id']);

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus Kas menggunakan kas_id
        $kasModel = new \App\Models\KasKoperasiModel();
        if ($angsuran['kas_id']) {
            $kasModel->delete($angsuran['kas_id']);
        } else if ($pinjaman) {
            // Backward compatibility fallback
            $anggota = $this->anggotaModel->find($pinjaman['anggota_id']);
            $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
            $kodePinjaman = 'PJ-' . str_pad($pinjaman['id'], 4, '0', STR_PAD_LEFT);
            
            $ketKasBiasa = 'Setoran Angsuran ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' (Cicilan ke-' . $angsuran['cicilan_ke'] . ') [' . $kodePinjaman . ']';
            $ketKasLunas = 'Pelunasan Pinjaman ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' [' . $kodePinjaman . ']';

            $kasSekarang = $kasModel->where('tanggal', $angsuran['tanggal_bayar'])
                                    ->where('nominal', $angsuran['jumlah_bayar'])
                                    ->groupStart()
                                        ->like('keterangan', $ketKasBiasa, 'both')
                                        ->orLike('keterangan', $ketKasLunas, 'both')
                                    ->groupEnd()
                                    ->first();
            if ($kasSekarang) $kasModel->delete($kasSekarang['id']);
        }

        // 2. Hapus Angsuran
        $this->angsuranModel->delete($id);

        // 3. Rollback Status Pinjaman
        if ($pinjaman && $pinjaman['status'] == 'lunas') {
            $totalAngsuran = $this->angsuranModel->where('pinjaman_id', $pinjaman['id'])->countAllResults();
            if ($totalAngsuran < $pinjaman['lama_tenor']) {
                $this->pinjamanModel->update($pinjaman['id'], ['status' => 'disetujui']);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/angsuran')->with('error', 'Gagal menghapus transaksi angsuran.');
        }

        return redirect()->to('/angsuran')->with('success', 'Data angsuran berhasil dihapus, catatan kas dicabut, dan status pinjaman disesuaikan.');
    }
}
