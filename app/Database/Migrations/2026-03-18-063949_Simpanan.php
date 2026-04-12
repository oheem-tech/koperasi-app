<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Simpanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'anggota_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_simpanan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_transaksi' => ['type' => 'DATE'],
            'jumlah'            => ['type' => 'INT', 'constraint' => 11],
            'jenis_transaksi'   => ['type' => 'ENUM', 'constraint' => ['setor', 'tarik']],
            'keterangan'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // kas_id untuk referensi ke kas_koperasi (sudah include langsung)
            'kas_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jenis_simpanan_id', 'jenis_simpanan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('simpanan', true);
    }

    public function down()
    {
        $this->forge->dropTable('simpanan', true);
    }
}
