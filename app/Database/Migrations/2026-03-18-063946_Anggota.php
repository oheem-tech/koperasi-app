<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Anggota extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'no_anggota'        => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'nama_lengkap'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'            => ['type' => 'TEXT'],
            'no_telp'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'tanggal_bergabung' => ['type' => 'DATE'],
            'status'            => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('anggota', true);
    }

    public function down()
    {
        $this->forge->dropTable('anggota', true);
    }
}
