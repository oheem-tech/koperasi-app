<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJasaPelunasanBebasToPengaturan extends Migration
{
    public function up()
    {
        $data = [
            'pengaturan_key'   => 'pelunasan_jasa_bebas_persen',
            'pengaturan_value' => '100',
            'keterangan'       => 'Persentase Jasa yang Dibebankan Saat Pelunasan Setelah Memenuhi Min. Tenor (0=Gratis, 100=1 Bulan Penuh, 50=Setengah Bulan, dll.)'
        ];
        $this->db->table('pengaturan')->insert($data);
    }

    public function down()
    {
        $this->db->table('pengaturan')->where('pengaturan_key', 'pelunasan_jasa_bebas_persen')->delete();
    }
}
