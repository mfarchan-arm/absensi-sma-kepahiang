<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
   protected $DBGroup          = 'default';
   protected $useAutoIncrement = true;
   protected $returnType       = 'array';
   protected $useSoftDeletes   = true;
   protected $protectFields    = true;
   protected $allowedFields    = ['kelas', 'jurusan'];

   protected $table = 'tb_kelas';

   protected $primaryKey = 'id_kelas';

   public function getAllKelas()
   {
      return $this->join('tb_jurusan', 'tb_kelas.jurusan = tb_jurusan.jurusan', 'left')->findAll();
   }

   public function tambahKelas($kelas, $jurusan)
   {
      return $this->db->table($this->table)->insert([
         'kelas' => $kelas,
         'jurusan' => $jurusan
      ]);
   }
}
