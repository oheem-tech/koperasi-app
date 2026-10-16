<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class LaporanNusapersada extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function jasa_pinjaman_dan_simpanan()
    {
        $anggota = $this->db->table('anggota')->where('status', 'aktif')->get()->getResultArray();
        $data = [];
        $totalSimpanan = 0; $totalPinjaman = 0; $totalSHU = 0; $totalTerima = 0;
        
        // Ambil pengaturan persentase, default ke nilai sebelumnya jika tidak ada
        $p_jasa_simpanan = get_pengaturan('shu_jasa_modal', 35) / 100;
        $p_jasa_pinjaman = get_pengaturan('shu_jasa_anggota', 15) / 100;

        foreach ($anggota as $a) {
            $jasaPinjamanQuery = $this->db->query("SELECT SUM(a.jumlah_jasa) as total FROM angsuran a JOIN pinjaman p ON p.id = a.pinjaman_id WHERE p.anggota_id = ?", [$a['id']])->getRow();
            $jasaPinjaman = $jasaPinjamanQuery ? $jasaPinjamanQuery->total : 0;
            
            $simpananQuery = $this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ?", [$a['id']])->getRow();
            $simpananTotal = $simpananQuery ? $simpananQuery->total : 0;
            
            // Jasa proporsional (dinamis dari pengaturan)
            $jasaSimpanan = $simpananTotal > 0 ? ($simpananTotal * $p_jasa_simpanan) : 0;
            $jasaPinjamanHitung = $jasaPinjaman > 0 ? ($jasaPinjaman * $p_jasa_pinjaman) : 0;
            $shuToko = 0; 
            
            if ($jasaSimpanan > 0 || $jasaPinjamanHitung > 0 || $shuToko > 0) {
                $terima = $jasaSimpanan + $jasaPinjamanHitung + $shuToko;
                $data[] = [
                    $a['nama_lengkap'],
                    $jasaSimpanan > 0 ? number_format($jasaSimpanan, 0, ',', '.') : "-",
                    $jasaPinjamanHitung > 0 ? number_format($jasaPinjamanHitung, 0, ',', '.') : "-",
                    $shuToko > 0 ? number_format($shuToko, 0, ',', '.') : "-",
                    number_format($terima, 0, ',', '.')
                ];
                $totalSimpanan += $jasaSimpanan;
                $totalPinjaman += $jasaPinjamanHitung;
                $totalSHU += $shuToko;
                $totalTerima += $terima;
            }
        }
        
        return view('laporan_nusapersada/laporan_jasa_pinjaman_dan_simpanan', [
            'data' => $data,
            'totals' => [
                number_format($totalSimpanan, 0, ',', '.'),
                number_format($totalPinjaman, 0, ',', '.'),
                number_format($totalSHU, 0, ',', '.'),
                number_format($totalTerima, 0, ',', '.')
            ]
        ]);
    }

    public function pembagian_jasa_pinjaman()
    {
        $anggota = $this->db->table('anggota')->where('status', 'aktif')->get()->getResultArray();
        $data2 = [];
        $totals = array_fill(0, 14, 0);
        
        // Menggunakan pengaturan master
        $p_jasa_pinjaman = get_pengaturan('shu_jasa_anggota', 15) / 100;

        foreach ($anggota as $a) {
            $row = [$a['nama_lengkap']];
            $totalJasaMember = 0;
            $hasData = false;

            for ($m = 1; $m <= 12; $m++) {
                $jasaBulanQuery = $this->db->query("SELECT SUM(a.jumlah_jasa) as total FROM angsuran a JOIN pinjaman p ON p.id = a.pinjaman_id WHERE p.anggota_id = ? AND MONTH(a.tanggal_bayar) = ? AND YEAR(a.tanggal_bayar) = YEAR(CURDATE())", [$a['id'], $m])->getRow();
                $jasaBulan = $jasaBulanQuery ? $jasaBulanQuery->total : 0;
                
                if ($jasaBulan > 0) {
                    $row[] = number_format($jasaBulan, 0, ',', '.');
                    $totalJasaMember += $jasaBulan;
                    $totals[$m-1] += $jasaBulan;
                    $hasData = true;
                } else {
                    $row[] = "-";
                }
            }
            
            if ($hasData) {
                // Kalkulasi jasa otomatis mengambil persentase dinamis
                $jasaFinal = $totalJasaMember * $p_jasa_pinjaman;
                $row[] = number_format($totalJasaMember, 0, ',', '.');
                $row[] = number_format($jasaFinal, 0, ',', '.');
                $totals[12] += $totalJasaMember;
                $totals[13] += $jasaFinal;
                $data2[] = $row;
            }
        }

        $formattedTotals = array_map(function($val) { return number_format($val, 0, ',', '.'); }, $totals);
        return view('laporan_nusapersada/laporan_pembagian_jasa_pinjaman', [
            'data2' => $data2, 
            'totals' => $formattedTotals
        ]);
    }

    public function perhitungan_jasa_simpanan()
    {
        $anggota = $this->db->table('anggota')->where('status', 'aktif')->get()->getResultArray();
        $data = [];
        $totalPokok = 0; $totalWajib = 0; $totalSukarela = 0; $totalJumlah = 0; $totalJasa = 0;
        
        $p_jasa_simpanan = get_pengaturan('shu_jasa_modal', 35) / 100;

        foreach ($anggota as $idx => $a) {
            $pokok = $this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 1", [$a['id']])->getRow()->total ?? 0;
            $wajib = $this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 2", [$a['id']])->getRow()->total ?? 0;
            $sukarela = $this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 3", [$a['id']])->getRow()->total ?? 0;
            
            $jumlah = $pokok + $wajib + $sukarela;
            $jasa = $jumlah * $p_jasa_simpanan; 

            if ($jumlah > 0) {
                $data[] = [
                    $idx + 1,
                    $a['nama_lengkap'],
                    $pokok > 0 ? number_format($pokok, 0, ',', '.') : "-",
                    $wajib > 0 ? number_format($wajib, 0, ',', '.') : "-",
                    $sukarela > 0 ? number_format($sukarela, 0, ',', '.') : "-",
                    number_format($jumlah, 0, ',', '.'),
                    number_format($jasa, 0, ',', '.')
                ];
                $totalPokok += $pokok;
                $totalWajib += $wajib;
                $totalSukarela += $sukarela;
                $totalJumlah += $jumlah;
                $totalJasa += $jasa;
            }
        }
        
        $totals = [
            number_format($totalPokok, 0, ',', '.'),
            number_format($totalWajib, 0, ',', '.'),
            number_format($totalSukarela, 0, ',', '.'),
            number_format($totalJumlah, 0, ',', '.'),
            number_format($totalJasa, 0, ',', '.')
        ];
        return view('laporan_nusapersada/laporan_perhitungan_jasa_simpanan', ['data' => $data, 'totals' => $totals]);
    }

    public function kas_rat()
    {
        $tahun = date('Y');
        
        // Pemasukan
        $p_pokok = []; $p_wajib = []; $p_sukarela = [];
        $p_angsuran = []; $p_jasa = []; $p_pelunasan = [];
        
        // Pengeluaran
        $k_pinjaman = [];
        $k_keluar = [];
        
        for($i=1; $i<=12; $i++) {
            // Simpanan Pokok
            $p_pokok[$i] = $this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=1 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0;
            $p_wajib[$i] = $this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=2 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0;
            $p_sukarela[$i] = $this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=3 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0;
            
            $angsuranData = $this->db->query("SELECT SUM(jumlah_pokok) as pokok, SUM(jumlah_jasa) as jasa FROM angsuran WHERE MONTH(tanggal_bayar)=? AND YEAR(tanggal_bayar)=?", [$i, $tahun])->getRow();
            $p_angsuran[$i] = $angsuranData->pokok ?? 0;
            $p_jasa[$i] = $angsuranData->jasa ?? 0;
            $p_pelunasan[$i] = 0; // Opsional jika tabel angsuran punya flag pelunasan
            
            $k_pinjaman[$i] = $this->db->query("SELECT SUM(jumlah_pinjaman) as t FROM pinjaman WHERE status IN ('disetujui', 'lunas') AND MONTH(tanggal_pengajuan)=? AND YEAR(tanggal_pengajuan)=?", [$i, $tahun])->getRow()->t ?? 0;
            $k_keluar[$i] = $this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='tarik' AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0;
        }

        return view('laporan_nusapersada/laporan_kas_rat', [
            'tahun' => $tahun,
            'p_pokok' => $p_pokok,
            'p_wajib' => $p_wajib,
            'p_sukarela' => $p_sukarela,
            'p_angsuran' => $p_angsuran,
            'p_jasa' => $p_jasa,
            'p_pelunasan' => $p_pelunasan,
            'k_pinjaman' => $k_pinjaman,
            'k_keluar' => $k_keluar
        ]);
    }

    public function perhitungan_shu()
    {
        $tahun = date('Y');

        // Logika 100% sama dengan Laporan Utama (Total Jasa + Total Denda + Pendapatan Kas Lainnya)
        $totalJasaGlobal = $this->db->table('angsuran')->where("YEAR(tanggal_bayar)", $tahun)->selectSum('jumlah_jasa')->get()->getRow()->jumlah_jasa ?? 0;
        $totalDendaGlobal = $this->db->table('angsuran')->where("YEAR(tanggal_bayar)", $tahun)->selectSum('denda')->get()->getRow()->denda ?? 0;
        
        $pendapatanMasukGlobal = $this->db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'masuk')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $biayaKeluarGlobal = $this->db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'keluar')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;

        $pendapatan = $totalJasaGlobal + $totalDendaGlobal + $pendapatanMasukGlobal;
        $biaya = $biayaKeluarGlobal;
        $shu_bersih = $pendapatan - $biaya;
        
        $p_cadangan = get_pengaturan('shu_cadangan', 30);
        $p_modal = get_pengaturan('shu_jasa_modal', 35);
        $p_anggota = get_pengaturan('shu_jasa_anggota', 15);
        $p_total_anggota = $p_modal + $p_anggota;
        $p_pengurus = 100 - ($p_cadangan + $p_total_anggota);
        
        $p_ketua = get_pengaturan('shu_pengurus_ketua', 35);
        $p_bendahara = get_pengaturan('shu_pengurus_bendahara', 25);
        $p_sekretaris = get_pengaturan('shu_pengurus_sekretaris', 15);
        $p_bp1 = get_pengaturan('shu_pengurus_bp1', 12.5);
        $p_bp2 = get_pengaturan('shu_pengurus_bp2', 12.5);
        
        $val_cadangan = $shu_bersih * ($p_cadangan / 100);
        $val_anggota = $shu_bersih * ($p_total_anggota / 100);
        $val_pengurus = $shu_bersih * ($p_pengurus / 100);
        
        $data = [
            'pendapatan' => $pendapatan, 'biaya' => $biaya, 'shu_bersih' => $shu_bersih,
            'p_cadangan' => $p_cadangan, 'p_total_anggota' => $p_total_anggota, 'p_pengurus' => $p_pengurus,
            'p_ketua' => $p_ketua, 'p_bendahara' => $p_bendahara, 'p_sekretaris' => $p_sekretaris, 'p_bp1' => $p_bp1, 'p_bp2' => $p_bp2,
            'val_cadangan' => $val_cadangan, 'val_anggota' => $val_anggota, 'val_pengurus' => $val_pengurus,
            'val_ketua' => $val_pengurus * ($p_ketua / 100),
            'val_bendahara' => $val_pengurus * ($p_bendahara / 100),
            'val_sekretaris' => $val_pengurus * ($p_sekretaris / 100),
            'val_bp1' => $val_pengurus * ($p_bp1 / 100),
            'val_bp2' => $val_pengurus * ($p_bp2 / 100),
            'p_modal' => $p_modal, 'p_anggota' => $p_anggota,
            'val_simpanan' => $shu_bersih * ($p_modal / 100),
            'val_pinjaman' => $shu_bersih * ($p_anggota / 100),
        ];
        
        return view('laporan_nusapersada/laporan_perhitungan_shu', $data);
    }
}
