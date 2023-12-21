<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kelas' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'kelas' => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
            ],
            'jurusan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 16,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        // primary key
        $this->forge->addKey('id_kelas', primary: TRUE);

        // id_jurusan foreign key
        $this->forge->addForeignKey('jurusan', 'tb_jurusan', 'jurusan', 'CASCADE', 'NO ACTION');

        $this->forge->createTable('tb_kelas', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('tb_kelas');
    }
}
