<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataGuru extends BaseController
{
   protected GuruModel $guruModel;

   protected $guruValidationRules = [
      'nik' => [
         'rules' => 'required|max_length[20]|min_length[16]',
         'errors' => [
            'required' => 'NIK harus diisi.',
            'is_unique' => 'NIK ini telah terdaftar.',
            'min_length[16]' => 'Panjang NIK minimal 16 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->guruModel = new GuruModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Guru',
         'ctx' => 'guru',
      ];

      return view('admin/data/data-guru', $data);
   }

   public function ambilDataGuru()
   {
      $result = $this->guruModel->getAllGuru();

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-guru', $data);
   }

   public function formTambahGuru()
   {
      $data = [
         'ctx' => 'guru',
         'title' => 'Tambah Data Guru'
      ];

      return view('admin/data/create/create-data-guru', $data);
   }

   public function saveGuru()
   {
      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'ctx' => 'guru',
            'title' => 'Tambah Data Guru',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-guru', $data);
      }

      $nik = $this->request->getVar('nik');
      $namaGuru = $this->request->getVar('nama');
      $jenisKelamin = $this->request->getVar('jk');
      $alamat = $this->request->getVar('alamat');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->guruModel->insertGuru($nik, $namaGuru, $jenisKelamin, $alamat, $noHp);
      // dd($result);
      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/create/');
   }

   public function formEditGuru($nik)
   {
      $guru = $this->guruModel->getGuruById($nik);

      if (empty($guru)) {
         throw new PageNotFoundException('Data guru dengan nik ' . $nik . ' tidak ditemukan');
      }

      $data = [
         'data' => $guru,
         'ctx' => 'guru',
         'title' => 'Edit Data Guru',
      ];

      return view('admin/data/edit/edit-data-guru', $data);
   }

   public function updateGuru()
   {
      $nik = $this->request->getVar('nik');

      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'data' => $this->guruModel->getGuruById($nik),
            'ctx' => 'guru',
            'title' => 'Edit Data Guru',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-guru', $data);
      }

      $nik = $this->request->getVar('nik');
      $namaGuru = $this->request->getVar('nama');
      $jenisKelamin = $this->request->getVar('jk');
      $alamat = $this->request->getVar('alamat');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->guruModel->saveGuru($nik, $namaGuru, $jenisKelamin, $alamat, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/edit/' . $nik);
   }

   public function delete($nik)
   {
      $result = $this->guruModel->delete($nik);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru');
   }
}
