<?php
// controllers/KelolaJadwalController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/SlotRepository.php';

class KelolaJadwalController {
    private $slotRepo;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection(); // Kita butuh DB untuk update tabel booking
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
            $id_admin = $_SESSION['user_id'] ?? 0;

            // AKSI 1: Buka Jadwal
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

            // AKSI 2: Walk-in Kasir
            if ($action === 'walk_in') {
                $id_slot = intval($_POST['id_slot']);

                $stmt = $this->db->prepare("SELECT tarif FROM slot_waktu WHERE id_slot = :id");
                $stmt->execute([':id' => $id_slot]);
                $slot = $stmt->fetch();
                $tarif = $slot ? $slot->tarif : 150000;

                $queryBooking = "INSERT INTO booking (id_pengguna, id_slot, id_admin, nama_tim, no_hp_pemesan, total_bayar, status_booking, jenis_booking, created_at) 
                                 VALUES (:id_user, :id_slot, :id_admin, 'Pelanggan Walk-in Kasir', '-', :tarif, 'confirmed', 'walk_in', NOW())";
                $stmtBooking = $this->db->prepare($queryBooking);
                $stmtBooking->execute([
                    ':id_user' => $id_admin, 
                    ':id_slot' => $id_slot,
                    ':id_admin' => $id_admin,
                    ':tarif' => $tarif
                ]);

                $this->slotRepo->lockPermanent($id_slot);
                header("Location: kelola_jadwal.php?tanggal=$tanggal&status=walkin_success");
                exit();
            }

            // AKSI 3: Batalkan Booking (Manual oleh Admin)
            if ($action === 'batal_manual') {
                $id_slot = intval($_POST['id_slot']);
                
                // 1. Bebaskan slot agar kembali hijau di kalender
                $this->slotRepo->releaseSlot($id_slot);
                
                // 2. Ubah status transaksi di tabel booking menjadi dibatalkan agar uangnya ditarik dari dashboard Owner
                $queryBatal = "UPDATE booking SET status_booking = 'dibatalkan', updated_at = NOW() 
                               WHERE id_slot = :id_slot AND status_booking IN ('confirmed', 'menunggu_verifikasi')";
                $stmtBatal = $this->db->prepare($queryBatal);
                $stmtBatal->execute([':id_slot' => $id_slot]);
                
                header("Location: kelola_jadwal.php?tanggal=$tanggal&status=cancelled");
                exit();
            }
        }
    }
}