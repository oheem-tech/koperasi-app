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
        $this->forge->createTable('master_kelompok');

        // Seeding default data
        $db = \Config\Database::connect();
        $db->table('master_kelompok')->insertBatch([
            ['nama_kelompok' => 'Umum', 'keterangan' => 'Kelompok standar untuk anggota umum', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'ASN/PNS', 'keterangan' => 'Pegawai Negeri Sipil & ASN', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'PPPK', 'keterangan' => 'Pegawai Pemerintah dengan Perjanjian Kerja', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Honorer', 'keterangan' => 'Tenaga Honorer / Kontrak', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Pensiunan', 'keterangan' => 'Anggota yang telah pensiun', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('master_kelompok');
    }
}
