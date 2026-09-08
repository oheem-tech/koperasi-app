<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelegramFeatures extends Migration
{
    public function up()
    {
        // Add telegram_chat_id to anggota
        $fields = [
            'telegram_chat_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'no_telp',
            ],
        ];
        $this->forge->addColumn('anggota', $fields);

        // Insert telegram_token to pengaturan if not exists
        $db = \Config\Database::connect();
        $builder = $db->table('pengaturan');
        
        $exists = $builder->where('pengaturan_key', 'telegram_token')->countAllResults();
        if ($exists == 0) {
            $builder->insert([
                'pengaturan_key'   => 'telegram_token',
                'pengaturan_value' => '',
                'keterangan'       => 'Token Bot Telegram dari @BotFather',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'telegram_chat_id');
        
        $db = \Config\Database::connect();
        $db->table('pengaturan')->where('pengaturan_key', 'telegram_token')->delete();
    }
}

