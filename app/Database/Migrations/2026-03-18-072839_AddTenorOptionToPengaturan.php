<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTenorOptionToPengaturan extends Migration
{
    public function up()
    {
        $data = [
            'pengaturan_key' => 'opsi_tenor_pinjaman',
            'pengaturan_value' => '3,6,12,24',
            'keterangan' => 'Pilihan Lama Tenor Pinjaman dalam Bulan (Pisahkan dengan koma)'
        ];

        $this->db->table('pengaturan')->insert($data);
    }

    public function down()
    {
        $this->db->table('pengaturan')->where('pengaturan_key', 'opsi_tenor_pinjaman')->delete();
    }
}
