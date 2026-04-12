<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailSHUToPengaturan extends Migration
{
    public function up()
    {
        // Hapus key lama shu_pengurus (akan diganti 3 key baru)
        $this->db->table('pengaturan')->where('pengaturan_key', 'shu_pengurus')->delete();

        $data = [
            [
                'pengaturan_key'   => 'shu_pengurus_anggota',
                'pengaturan_value' => '10',
                'keterangan'       => 'SHU Pengurus Anggota (%)'
            ],
            [
                'pengaturan_key'   => 'shu_pengawas',
                'pengaturan_value' => '5',
                'keterangan'       => 'SHU Pengawas (%)'
            ],
            [
                'pengaturan_key'   => 'shu_pembina',
                'pengaturan_value' => '5',
                'keterangan'       => 'SHU Pembina (%)'
            ],
            [
                'pengaturan_key'   => 'shu_dana_sosial',
                'pengaturan_value' => '5',
                'keterangan'       => 'SHU Dana Sosial (%)'
            ],
            [
                'pengaturan_key'   => 'shu_dana_cadangan',
                'pengaturan_value' => '10',
                'keterangan'       => 'SHU Dana Cadangan (%)'
            ],
        ];
        $this->db->table('pengaturan')->ignore(true)->insertBatch($data);
    }

    public function down()
    {
        $keys = ['shu_pengurus_anggota', 'shu_pengawas', 'shu_pembina', 'shu_dana_sosial', 'shu_dana_cadangan'];
        $this->db->table('pengaturan')->whereIn('pengaturan_key', $keys)->delete();
        // Kembalikan shu_pengurus lama
        $this->db->table('pengaturan')->insert([
            'pengaturan_key'   => 'shu_pengurus',
            'pengaturan_value' => '15',
            'keterangan'       => 'SHU untuk Pengurus (%)'
        ]);
    }
}
