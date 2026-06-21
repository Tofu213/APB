<?php
// controllers/BookingController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/SlotRepository.php';
require_once __DIR__ . '/../repositories/BookingRepository.php';
require_once __DIR__ . '/../services/BookingService.php';

class BookingController {
    private $bookingService;

    public function __construct() {
        $database = new Database();
        $dbConn = $database->getConnection();
        
        $slotRepo = new SlotRepository($dbConn);
        $bookingRepo = new BookingRepository($dbConn);
        $this->bookingService = new BookingService($slotRepo, $bookingRepo);
    }

    // Mendapatkan daftar slot jadwal berdasarkan tanggal pilihan
    public function lihatJadwal($tanggal) {
        return $this->bookingService->getJadwalPerTanggal($tanggal);
    }

    // Menangani pengiriman form booking dan unggah berkas (UC-03)
    public function handleTempatkanBooking() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Pastikan pelanggan sudah masuk sesi login
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
            }

            $id_user = $_SESSION['user_id'];
            $id_slot = intval($_POST['id_slot'] ?? 0);
            $nama_tim = htmlspecialchars($_POST['nama_tim'] ?? '');
            $no_hp = htmlspecialchars($_POST['no_hp_pemesan'] ?? '');
            $total_bayar = floatval($_POST['total_bayar'] ?? 0);
            $metode_bayar = $_POST['metode_bayar'] ?? 'transfer_bank';

            // Proses Unggah Berkas Foto Bukti Pembayaran
            $foto_nama = "";
            if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['bukti_bayar']['tmp_name'];
                $fileName = $_FILES['bukti_bayar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Ekstensi yang diizinkan sesuai alternatif alur dokumen
                $extensions_allowed = ['jpg', 'jpeg', 'png'];
                
                if (in_array($fileExtension, $extensions_allowed)) {
                    // Berikan nama unik agar berkas tidak saling tertimpa
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../uploads/bukti_bayar/';
                    
                    // Buat folder uploads jika belum ada otomatis
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_path($fileTmpPath, $dest_path) || move_uploaded_file($fileTmpPath, $dest_path)) {
                        $foto_nama = $newFileName;
                    }
                }
            }

            if (empty($foto_nama)) {
                return "Gagal mengunggah berkas bukti pembayaran. Pastikan format berupa JPG/PNG.";
            }

            // Jalankan logika penempatan booking di tingkat Service
            $result = $this->bookingService->tempatkanBooking(
                $id_user, $id_slot, $nama_tim, $no_hp, $total_bayar, $foto_nama, $metode_bayar
            );

            if ($result['status']) {
                header("Location: jadwal.php?booking=success");
                exit();
            } else {
                return $result['message'];
            }
        }
    }
}