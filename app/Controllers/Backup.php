<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Backup extends BaseController
{
    public function index()
    {
        if (!has_permission('manage_backup')) {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk mengelola backup & restore.');
        }

        $data = [
            'title' => 'Backup & Restore Database'
        ];
        return view('backup/index', $data);
    }

    public function download()
    {
        if (!has_permission('manage_backup')) return redirect()->to('/dashboard');

        // Increase memory and exec time for large databases
        ini_set('memory_limit', '256M');
        set_time_limit(300);

        try {
            $db = \Config\Database::connect();
            $tables = $db->listTables();
            
            $sql = "-- ========================================================\n";
            $sql .= "-- Koperasi App Database Backup\n";
            $sql .= "-- Terbuat Oleh : Pure PHP Exporter\n";
            $sql .= "-- Waktu Backup : " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- ========================================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                // Struktur Tabel
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Struktur tabel `$table`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                
                $query = $db->query("SHOW CREATE TABLE `$table`");
                $row = $query->getRowArray();
                $sql .= $row['Create Table'] . ";\n\n";

                // Data Tabel
                $rows = $db->table($table)->get()->getResultArray();
                if (count($rows) > 0) {
                    $sql .= "-- Data untuk tabel `$table`\n";
                    foreach ($rows as $r) {
                        $insertKeys = array_keys($r);
                        $insertKeysStr = "`" . implode("`, `", $insertKeys) . "`";
                        
                        $insertVals = [];
                        foreach ($r as $val) {
                            if ($val === null) {
                                $insertVals[] = 'NULL';
                            } else {
                                // Prevent CI4 from nesting string literal quotes too much if it does
                                $insertVals[] = $db->escape($val);
                            }
                        }
                        $insertValsStr = implode(", ", $insertVals);
                        
                        $sql .= "INSERT INTO `$table` ($insertKeysStr) VALUES ($insertValsStr);\n";
                    }
                    $sql .= "\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            $filename = 'backup_koperasi_' . date('Y-m-d_His') . '.sql';

            return $this->response->download($filename, $sql);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membackup database: ' . $e->getMessage());
        }
    }
    
    public function restore()
    {
        if (!has_permission('manage_backup')) return redirect()->to('/dashboard');

        // Validasi kata KONFIRMASI
        $konfirmasi = $this->request->getPost('konfirmasi');
        if (strtoupper(trim($konfirmasi)) !== 'KONFIRMASI') {
            return redirect()->back()->with('error', 'Gagal memulihkan database. Anda harus mengetikkan kata "KONFIRMASI" dengan benar pada form validasi.');
        }

        $file = $this->request->getFile('backup_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak diunggah.');
        }

        // File extension check loosely (getMimeType sometimes sees sql as text/plain or octet-stream, extension is better)
        if (strtolower($file->getExtension()) !== 'sql' && strtolower($file->getClientExtension()) !== 'sql') {
            return redirect()->back()->with('error', 'Format file tidak diizinkan. Harus berupa file berekstensi .sql.');
        }

        // File read stream is handled below

        // Increase memory and exec time
        ini_set('memory_limit', '256M');
        set_time_limit(300);

        $db = \Config\Database::connect();

        try {
            $db->query("SET FOREIGN_KEY_CHECKS = 0");
            
            $handle = fopen($file->getTempName(), "r");
            if ($handle) {
                $templine = '';
                while (($line = fgets($handle)) !== false) {
                    $lineTrim = trim($line);
                    
                    // Skip comments and empty lines
                    if (empty($lineTrim) || substr($lineTrim, 0, 2) == '--' || substr($lineTrim, 0, 2) == '/*') {
                        continue;
                    }
                    
                    $templine .= $line;
                    
                    // Eksekusi jika baris diakhiri titik koma
                    if (substr(rtrim($line), -1, 1) == ';') {
                        try {
                            $db->query($templine);
                        } catch (\Exception $ex) {
                            // Abaikan error drop statement dll jika ada
                        }
                        $templine = '';
                    }
                }
                fclose($handle);
            }
            
            $db->query("SET FOREIGN_KEY_CHECKS = 1");
            
            return redirect()->back()->with('success', 'Database berhasil dipulihkan secara penuh.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat melakukan restore: ' . $e->getMessage());
        }
    }
}
