<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurusanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'jurusan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 16,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        // primary key
        $this->forge->addKey('jurusan', primary: TRUE);

        $this->forge->createTable('tb_jurusan', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('tb_jurusan');
    }
}
