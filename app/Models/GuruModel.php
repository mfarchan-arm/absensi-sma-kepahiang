<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
   protected $allowedFields = [
      'nik',
      'nama_guru',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code'
   ];

   protected $table = 'tb_guru';

   protected $primaryKey = 'nik';

   public function cekGuru(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getAllGuru()
   {
      return $this->orderBy('nama_guru')->findAll();
   }

   public function getGuruById($nik)
   {
      return $this->where([$this->primaryKey => $nik])->first();
   }

   public function saveGuru($nik, $namaGuru, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         'nik' => $nik,
         'nama_guru' => $namaGuru,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaGuru . md5($nik . $namaGuru . $noHp)) . substr(sha1($nik . rand(0, 100)), 0, 24)
      ]);
   }
   public function insertGuru($nik, $namaGuru, $jenisKelamin, $alamat, $noHp)
   {
       $this->insert([
         'nik' => $nik,
         'nama_guru' => $namaGuru,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaGuru . md5($nik . $namaGuru . $noHp)) . substr(sha1($nik . rand(0, 100)), 0, 24)
      ]);
      return 1;
   }
}
