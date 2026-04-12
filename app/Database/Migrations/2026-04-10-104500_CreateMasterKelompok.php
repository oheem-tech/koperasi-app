<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterKelompok extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        try {
            $this->forge->createTable('master_kelompok', true);
        } catch (\Exception $e) {
            // Abaikan jika sudah ada (meskipun flag TRUE digunakan, terkadang koneksi ketat melempar pengecualian)
        }

        // Seeding default data (Only if table is empty)
        $db = \Config\Database::connect();
        try {
            if ($db->table('master_kelompok')->countAllResults() == 0) {
                $db->table('master_kelompok')->ignore(true)->insertBatch([
                    ['nama_kelompok' => 'Umum', 'keterangan' => 'Kelompok standar untuk anggota umum', 'created_at' => date('Y-m-d H:i:s')],
                    ['nama_kelompok' => 'ASN/PNS', 'keterangan' => 'Pegawai Negeri Sipil & ASN', 'created_at' => date('Y-m-d H:i:s')],
                    ['nama_kelompok' => 'PPPK', 'keterangan' => 'Pegawai Pemerintah dengan Perjanjian Kerja', 'created_at' => date('Y-m-d H:i:s')],
                    ['nama_kelompok' => 'Honorer', 'keterangan' => 'Tenaga Honorer / Kontrak', 'created_at' => date('Y-m-d H:i:s')],
                    ['nama_kelompok' => 'Pensiunan', 'keterangan' => 'Anggota yang telah pensiun', 'created_at' => date('Y-m-d H:i:s')],
                ]);
            }
        } catch (\Exception $e) {
            // Abaikan jika ada konflik duplicate key
        }
    }

    public function down()
    {
        $this->forge->dropTable('master_kelompok');
    }
}
