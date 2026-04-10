<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisPinjaman extends Migration
{
    public function up()
    {
        $fields = [
            'jenis_pinjaman' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Uang',
                'after'      => 'anggota_id'
            ]
        ];
        $this->forge->addColumn('pinjaman', $fields);

        $data = [
            [
                'pengaturan_key' => 'opsi_jenis_pinjaman',
                'pengaturan_value' => 'Uang,Barang,Emas',
                'keterangan' => 'Pilihan Jenis Pinjaman (Pisahkan dengan koma)'
            ],
            [
                'pengaturan_key' => 'maks_pinjaman_aktif',
                'pengaturan_value' => '1',
                'keterangan' => 'Batas Maksimal Pinjaman Aktif untuk Jenis yang Sama'
            ]
        ];
        $this->db->table('pengaturan')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjaman', 'jenis_pinjaman');
        $this->db->table('pengaturan')->where('pengaturan_key', 'opsi_jenis_pinjaman')->delete();
        $this->db->table('pengaturan')->where('pengaturan_key', 'maks_pinjaman_aktif')->delete();
    }
}
