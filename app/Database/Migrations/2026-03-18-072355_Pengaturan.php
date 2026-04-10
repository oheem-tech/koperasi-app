<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pengaturan extends Migration
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
            'pengaturan_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pengaturan_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->createTable('pengaturan');

        $data = [
            [
                'pengaturan_key' => 'bunga_pinjaman',
                'pengaturan_value' => '1.5',
                'keterangan' => 'Persentase Bunga/Jasa Pinjaman per Bulan (%)'
            ],
            [
                'pengaturan_key' => 'denda_keterlambatan',
                'pengaturan_value' => '10000',
                'keterangan' => 'Nominal Denda Keterlambatan Angsuran (Rp/Bulan)'
            ],
            [
                'pengaturan_key' => 'shu_jasa_modal',
                'pengaturan_value' => '20',
                'keterangan' => 'Persentase Pembagian SHU - Jasa Modal/Simpanan (%)'
            ],
            [
                'pengaturan_key' => 'shu_jasa_anggota',
                'pengaturan_value' => '25',
                'keterangan' => 'Persentase Pembagian SHU - Jasa Anggota/Pinjaman (%)'
            ],
            [
                'pengaturan_key' => 'shu_pengurus',
                'pengaturan_value' => '15',
                'keterangan' => 'Persentase Pembagian SHU - Pengurus (%)'
            ],
            [
                'pengaturan_key' => 'shu_cadangan',
                'pengaturan_value' => '40',
                'keterangan' => 'Persentase Pembagian SHU - Cadangan Koperasi (%)'
            ],
        ];
        
        $this->db->table('pengaturan')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
    }
}
