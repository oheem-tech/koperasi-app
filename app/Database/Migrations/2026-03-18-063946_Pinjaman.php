<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pinjaman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'anggota_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_pengajuan'   => ['type' => 'DATE'],
            'jumlah_pinjaman'     => ['type' => 'INT', 'constraint' => 11],
            'lama_tenor'          => ['type' => 'INT', 'constraint' => 11, 'comment' => 'Bulan'],
            'bunga_persen'        => ['type' => 'FLOAT', 'constraint' => '5,2'],
            'tanggal_jatuh_tempo' => ['type' => 'DATE', 'null' => true],
            'status'              => ['type' => 'ENUM', 'constraint' => ['pending', 'disetujui', 'ditolak', 'lunas'], 'default' => 'pending'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pinjaman', true);
    }

    public function down()
    {
        $this->forge->dropTable('pinjaman', true);
    }
}
