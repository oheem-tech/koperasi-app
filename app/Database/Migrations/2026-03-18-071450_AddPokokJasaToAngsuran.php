<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPokokJasaToAngsuran extends Migration
{
    public function up()
    {
        $fields = [
            'jumlah_pokok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'jumlah_bayar'
            ],
            'jumlah_jasa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'jumlah_pokok'
            ]
        ];
        
        $this->forge->addColumn('angsuran', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('angsuran', ['jumlah_pokok', 'jumlah_jasa']);
    }
}
