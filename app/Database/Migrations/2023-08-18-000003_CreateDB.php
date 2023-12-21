<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDB extends Migration
{
    public function up()
    {
        $this->forge->getConnection()->query("CREATE TABLE tb_kehadiran (
            id_kehadiran int(11) NOT NULL,
            kehadiran enum('Hadir','Sakit','Izin','Tanpa keterangan') NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("INSERT INTO tb_kehadiran (id_kehadiran, kehadiran) VALUES
            (1, 'Hadir'),
            (2, 'Sakit'),
            (3, 'Izin'),
            (4, 'Tanpa keterangan');");

        $this->forge->getConnection()->query("INSERT INTO tb_jurusan (jurusan) VALUES
            ('IPA1'),
            ('IPA2'),
            ('IPA3'),
            ('IPA4'),
            ('IPA5'),
            ('IPA6'),
            ('IPS1'),
            ('IPS2'),
            ('IPS3'),
            ('IPS4'),
            ('IPS5'),
            ('IPS6');");

        $this->forge->getConnection()->query("INSERT INTO tb_kelas (kelas, jurusan) VALUES
            ('X', 'IPA1'),
            ('X', 'IPA2'),
            ('X', 'IPA3'),
            ('X', 'IPA4'),
            ('X', 'IPA5'),
            ('X', 'IPA6'),
            ('X', 'IPS1'),
            ('X', 'IPS2'),
            ('X', 'IPS3'),
            ('X', 'IPS4'),
            ('X', 'IPS5'),
            ('X', 'IPS6'),
            ('XI', 'IPA1'),
            ('XI', 'IPA2'),
            ('XI', 'IPA3'),
            ('XI', 'IPA4'),
            ('XI', 'IPA5'),
            ('XI', 'IPA6'),
            ('XI', 'IPS1'),
            ('XI', 'IPS2'),
            ('XI', 'IPS3'),
            ('XI', 'IPS4'),
            ('XI', 'IPS5'),
            ('XI', 'IPS6'),
            ('XII', 'IPA1'),
            ('XII', 'IPA2'),
            ('XII', 'IPA3'),
            ('XII', 'IPA4'),
            ('XII', 'IPA5'),
            ('XII', 'IPA6'),
            ('XII', 'IPS1'),
            ('XII', 'IPS2'),
            ('XII', 'IPS3'),
            ('XII', 'IPS4'),
            ('XII', 'IPS5'),
            ('XII', 'IPS6');");

        $this->forge->getConnection()->query("CREATE TABLE tb_guru (
            nik varchar(32) NOT NULL,
            nama_guru varchar(255) NOT NULL,
            jenis_kelamin enum('Laki-laki','Perempuan') NOT NULL,
            alamat text NOT NULL,
            no_hp varchar(32) NOT NULL,
            unique_code varchar(64) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("CREATE TABLE tb_presensi_guru (
            id_presensi int(11) NOT NULL,
            nik varchar(32) NOT NULL,
            tanggal date NOT NULL,
            jam_masuk time DEFAULT NULL,
            jam_keluar time DEFAULT NULL,
            id_kehadiran int(11) NOT NULL,
            keterangan varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                        ");

        $this->forge->getConnection()->query("CREATE TABLE tb_siswa (
            nis varchar(32) NOT NULL,
            nama_siswa varchar(255) NOT NULL,
            id_kelas int(11) UNSIGNED NOT NULL,
            jenis_kelamin enum('Laki-laki','Perempuan') NOT NULL,
            no_hp varchar(32) NOT NULL,
            unique_code varchar(64) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("CREATE TABLE tb_presensi_siswa (
            id_presensi int(11) NOT NULL,
            nis varchar(32) NOT NULL,
            id_kelas int(11) UNSIGNED DEFAULT NULL,
            tanggal date NOT NULL,
            jam_masuk time DEFAULT NULL,
            jam_keluar time DEFAULT NULL,
            id_kehadiran int(11) NOT NULL,
            keterangan varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->forge->getConnection()->query("ALTER TABLE tb_guru
            ADD PRIMARY KEY (nik),
            ADD UNIQUE KEY unique_code (unique_code);");

        $this->forge->getConnection()->query("ALTER TABLE tb_kehadiran
            ADD PRIMARY KEY (id_kehadiran);");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            ADD PRIMARY KEY (id_presensi),
            ADD KEY id_kehadiran (id_kehadiran),
            ADD KEY nik (nik);");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            ADD PRIMARY KEY (id_presensi),
            ADD KEY nis (nis),
            ADD KEY id_kehadiran (id_kehadiran),
            ADD KEY id_kelas (id_kelas);");

        $this->forge->getConnection()->query("ALTER TABLE tb_siswa
            ADD PRIMARY KEY (nis),
            ADD UNIQUE KEY unique_code (unique_code),
            ADD KEY id_kelas (id_kelas);");

        $this->forge->getConnection()->query("ALTER TABLE tb_kehadiran
            MODIFY id_kehadiran int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            MODIFY id_presensi int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            MODIFY id_presensi int(11) NOT NULL AUTO_INCREMENT;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_guru
            ADD CONSTRAINT tb_presensi_guru_ibfk_2 FOREIGN KEY (id_kehadiran) REFERENCES tb_kehadiran (id_kehadiran),
            ADD CONSTRAINT tb_presensi_guru_ibfk_3 FOREIGN KEY (nik) REFERENCES tb_guru (nik) ON DELETE CASCADE;");

        $this->forge->getConnection()->query("ALTER TABLE tb_presensi_siswa
            ADD CONSTRAINT tb_presensi_siswa_ibfk_2 FOREIGN KEY (id_kehadiran) REFERENCES tb_kehadiran (id_kehadiran),
            ADD CONSTRAINT tb_presensi_siswa_ibfk_3 FOREIGN KEY (nis) REFERENCES tb_siswa (nis) ON DELETE CASCADE,
            ADD CONSTRAINT tb_presensi_siswa_ibfk_4 FOREIGN KEY (id_kelas) REFERENCES tb_kelas (id_kelas) ON DELETE SET NULL ON UPDATE CASCADE;");

        $this->forge->getConnection()->query("ALTER TABLE tb_siswa
            ADD CONSTRAINT tb_siswa_ibfk_1 FOREIGN KEY (id_kelas) REFERENCES tb_kelas (id_kelas);");
    }

    public function down()
    {
        $tables = [
            'tb_presensi_siswa',
            'tb_presensi_guru',
            'tb_siswa',
            'tb_guru',
            'tb_kehadiran',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table);
        }
    }
}
