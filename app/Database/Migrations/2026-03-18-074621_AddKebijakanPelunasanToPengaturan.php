<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKebijakanPelunasanToPengaturan extends Migration
{
    public function up()
    {
        $data = [
            [
                'pengaturan_key'   => 'pelunasan_min_tenor_persen',
                'pengaturan_value' => '50',
                'keterangan'       => 'Batas Minimum Tenor (%) sebelum pelunasan bebas biaya jasa. Jika cicilan ke- belum mencapai persen ini, total jasa tetap dibebankan penuh.'
            ],
            [
                'pengaturan_key'   => 'kebijakan_pelunasan_aktif',
                'pengaturan_value' => '1',
                'keterangan'       => 'Aktifkan Kebijakan Min. Tenor Pelunasan? (1=Ya, 0=Tidak)'
            ]
        ];
        $this->db->table('pengaturan')->ignore(true)->insertBatch($data);
    }

    public function down()
    {
        $this->db->table('pengaturan')->whereIn('pengaturan_key', ['pelunasan_min_tenor_persen', 'kebijakan_pelunasan_aktif'])->delete();
    }
}
