<?php
// controllers/KelolaJadwalController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/SlotRepository.php';

class KelolaJadwalController {
    private $slotRepo;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection(); // Kita panggil DB langsung untuk input ke tabel booking
        $this->slotRepo = new SlotRepository($this->db);
    }

    public function lihatJadwal($tanggal) {
        return $this->slotRepo->findByTanggal($tanggal);
    }

    public function handleAksiAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) { session_start(); }
            
            $action = $_POST['action'] ?? '';
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $id_admin = $_SESSION['user_id']; // Ambil ID Admin yang sedang bertugas

            // Fitur Generate Jadwal
            if ($action === 'generate') {
                $tarif = 150000;
                for ($jam = 15; $jam <= 21; $jam++) {
                    $jam_mulai = sprintf("%02d:00:00", $jam);
                    $jam_selesai = sprintf("%02d:00:00", $jam + 1);
                    $this->slotRepo->createSlot(1, $tanggal, $jam_mulai, $jam_selesai, $tarif);
                }
                header("Location: kelola_jadwal.php?tanggal=$tanggal&status=generated");
                exit();
            }

            // Fitur Walk-in (Diperbarui agar mencatat Pemasukan/Omset)
            if ($action === 'walk_in') {
                $id_slot = intval($_POST['id_slot']);

                // 1. Cek dulu berapa harga tarif lapangan di jam tersebut
                $stmt = $this->db->prepare("SELECT tarif FROM slot_waktu WHERE id_slot = :id");
                $stmt->execute([':id' => $id_slot]);
                $slot = $stmt->fetch();
                $tarif = $slot ? $slot->tarif : 150000;

                // 2. Suntikkan catatan transaksi kasir ke dalam tabel booking
                // Menggunakan ID Admin sebagai perwakilan pengguna
                $queryBooking = "INSERT INTO booking (id_pengguna, id_slot, id_admin, nama_tim, no_hp_pemesan, total_bayar, status_booking, jenis_booking, created_at) 
                                 VALUES (:id_user, :id_slot, :id_admin, 'Pelanggan Walk-in Kasir', '-', :tarif, 'confirmed', 'walk_in', NOW())";
                
                $stmtBooking = $this->db->prepare($queryBooking);
                $stmtBooking->execute([
                    ':id_user' => $id_admin, 
                    ':id_slot' => $id_slot,
                    ':id_admin' => $id_admin,
                    ':tarif' => $tarif
                ]);

                // 3. Kunci lapangan secara permanen
                $this->slotRepo->lockPermanent($id_slot);
                
                header("Location: kelola_jadwal.php?tanggal=$tanggal&status=walkin_success");
                exit();
            }
        }
    }
}