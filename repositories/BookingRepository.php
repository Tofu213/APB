<?php
// repositories/BookingRepository.php

class BookingRepository {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Menyimpan data booking baru ke database (UC-03)
    public function save($id_pengguna, $id_slot, $nama_tim, $no_hp_pemesan, $total_bayar) {
        $query = "INSERT INTO booking (id_pengguna, id_slot, nama_tim, no_hp_pemesan, total_bayar, status_booking, jenis_booking, created_at) 
                  VALUES (:id_user, :id_slot, :nama_tim, :no_hp, :total, 'menunggu_verifikasi', 'online', NOW())";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_user", $id_pengguna);
        $stmt->bindParam(":id_slot", $id_slot);
        $stmt->bindParam(":nama_tim", $nama_tim);
        $stmt->bindParam(":no_hp", $no_hp_pemesan);
        $stmt->bindParam(":total", $total_bayar);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    // Menyimpan data bukti pembayaran ke tabel pembayaran (UC-03)
    public function savePembayaran($id_booking, $foto_bukti_bayar, $metode_bayar, $jumlah_bayar) {
        $query = "INSERT INTO pembayaran (id_booking, foto_bukti_bayar, metode_bayar, jumlah_bayar, status_verifikasi, uploaded_at) 
                  VALUES (:id_booking, :foto, :metode, :jumlah, 'menunggu', NOW())";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_booking", $id_booking);
        $stmt->bindParam(":foto", $foto_bukti_bayar);
        $stmt->bindParam(":metode", $metode_bayar);
        $stmt->bindParam(":jumlah", $jumlah_bayar);
        
        return $stmt->execute();
    }
}