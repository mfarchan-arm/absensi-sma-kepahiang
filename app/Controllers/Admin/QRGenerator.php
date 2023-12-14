<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class QRGenerator extends BaseController
{
   private $qrCode;
   private $label;
   private $writer;
   private $relativePath;
   private $qrCodeFilePath;
   private $labelFont;
   private $foregroundColor;
   private $foregroundColor2;
   private $backgroundColor;
   private $logo;
   protected GuruModel $guruModel;

   public function __construct()
   {

      $this->guruModel = new GuruModel();
      $this->relativePath = ROOTPATH . '/';
      $this->qrCodeFilePath = 'public/uploads/';

      if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
         mkdir($this->relativePath . $this->qrCodeFilePath, 0755, true);
      }

      $this->writer = new PngWriter();
      $this->labelFont = new NotoSans(20);
      $this->foregroundColor = new Color(44, 73, 162);
      $this->backgroundColor = new Color(255, 255, 255);
      $this->logo = Logo::create(base_url('assets/img/apple-icon.png'))
         ->setResizeToWidth(50)
         ->setPunchoutBackground(true);
      $this->label = Label::create('')
         ->setFont($this->labelFont)
         ->setTextColor($this->foregroundColor);

      $this->qrCode = QrCode::create('')
         ->setEncoding(new Encoding('UTF-8'))
         ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
         ->setSize(300)
         ->setMargin(10)
         ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
         ->setForegroundColor($this->foregroundColor)
         ->setBackgroundColor($this->backgroundColor);
   }

   public function generateQrSiswa()
   {
      $kelas = url_title($this->request->getVar('kelas'), '-', true);

      $this->qrCodeFilePath .= 'qr-siswa/' . $kelas . '/';

      if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
         mkdir($this->relativePath . $this->qrCodeFilePath, recursive: true);
      }

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(['success' => true]);
   }
   public function ZipFolder($folderName, $fileName)
   {
      $DirFolder = str_replace('_', '/', $folderName);

      $this->qrCodeFilePath .= $DirFolder;
      // $zipFileName = str_replace('_', ' ', $fileName);
      $zipFileName =  $fileName;
      $folderToZip = $this->relativePath .  $this->qrCodeFilePath;
      // $folderToZip = base_url($DirFolder);
      // dd($folderToZip);
      $zip = new ZipArchive();
      if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
         exit("Tidak dapat membuka <$zipFileName>\n");
      }

      $this->addFolderToZip($folderToZip . '/', $zip);
      $zip->close();


      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename=' . basename($zipFileName));
      header('Content-Length: ' . filesize($zipFileName));
      readfile($zipFileName);


      // Opsional: Hapus file ZIP setelah diunduh
      unlink($zipFileName);

      $this->deleteDirectory($folderToZip);
   }

   public function generateQrGuru()
   {
      // Mengatur warna foreground untuk QR Code dan Label
      // $this->qrCode->setForegroundColor($this->foregroundColor2);
      // $this->label->setTextColor($this->foregroundColor2);

      // Menetapkan path tambahan untuk menyimpan QR Code
      $this->qrCodeFilePath .= 'qr-guru/';

      // Memeriksa dan membuat direktori jika belum ada
      if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
         mkdir($this->relativePath . $this->qrCodeFilePath, 0755, true); // Izin dan flag rekursif
      }

      // Memanggil metode generate

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(true);
   }


   protected function generate($nama, $nomor, $unique_code)
   {
      // Membuat nama file dengan format yang diinginkan
      $filename = url_title($nama, '-', true) . "_" . url_title($nomor, '-', true) . '.png';

      // Set data QR Code
      $this->qrCode->setData($unique_code);

      // Set text label
      $this->label->setText($nama);
      // $this->logo = Logo::create(base_url('assets/img/logo_sekolah.png'))->setResizeToWidth(75);
      // Menyimpan QR Code sebagai file
      $this->writer->write($this->qrCode, NULL, $this->label)
         ->saveToFile($this->relativePath . $this->qrCodeFilePath . $filename);
   }


   protected function deleteDirectory($dirPath)
   {
      if (!is_dir($dirPath)) {
         throw new InvalidArgumentException("$dirPath harus merupakan direktori");
      }
      if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
         $dirPath .= '/';
      }
      $files = glob($dirPath . '*', GLOB_MARK);
      foreach ($files as $file) {
         if (is_dir($file)) {
            $this->deleteDirectory($file);
         } else {
            unlink($file);
         }
      }
      rmdir($dirPath);
   }
   protected function addFolderToZip($dir, $zipArchive, $zipdir = '')
   {
      if (is_dir($dir)) {
         if ($dh = opendir($dir)) {
            // Menambahkan folder ke arsip
            if (!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);

            // Loop melalui file dan folder
            while (($file = readdir($dh)) !== false) {
               // Lewati file . dan ..
               if ($file != '.' && $file != '..') {
                  // Jika adalah folder, rekursi
                  if (is_dir($dir . $file)) {
                     $this->addFolderToZip($dir . $file . '/', $zipArchive, $zipdir . $file . '/');
                  } else {
                     // Jika adalah file, tambahkan ke arsip
                     $zipArchive->addFile($dir . $file, $zipdir . $file);
                  }
               }
            }
            closedir($dh);
         }
      }
   }
}
