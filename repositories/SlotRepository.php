<?php
// repositories/SlotRepository.php

class SlotRepository {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Menampilkan semua slot berdasarkan tanggal tertentu (UC-02)
    public function findByTanggal($tanggal) {
        // Query ini juga otomatis memeriksa apakah ada status 'terkunci' yang sudah hangus (lewat 15 menit)
        // Jika hangus, status di database dianggap 'kosong' kembali
        $query = "SELECT *, 
                  CASE 
                    WHEN status_slot = 'terkunci' AND lock_expired_at < NOW() THEN 'kosong'
                    ELSE status_slot 
                  END as status_riil
                  FROM slot_waktu 
                  WHERE tanggal = :tanggal 
                  ORDER BY jam_mulai ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":tanggal", $tanggal);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Mengunci slot waktu secara otomatis selama 15 menit (UC-03 / Auto-Lock)
    public function lockSlot($id_slot) {
        // Mengubah status menjadi terkunci dan memberikan waktu kedaluwarsa 15 menit dari sekarang
        $query = "UPDATE slot_waktu 
                  SET status_slot = 'terkunci', 
                      lock_expired_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                  WHERE id_slot = :id_slot AND (status_slot = 'kosong' OR (status_slot = 'terkunci' AND lock_expired_at < NOW()))";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_slot", $id_slot);
        $stmt->execute();
        
        // Mengembalikan true jika ada baris database yang berhasil diubah (berarti slot berhasil diamankan)
        return $stmt->rowCount() > 0;
    }

    // Mengunci slot secara permanen setelah pembayaran dikonfirmasi admin (UC-04)
    public function lockPermanent($id_slot) {
        $query = "UPDATE slot_waktu SET status_slot = 'terisi', lock_expired_at = NULL WHERE id_slot = :id_slot";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_slot", $id_slot);
        return $stmt->execute();
    }

    // Membebaskan slot kembali menjadi kosong (Alternative Flow / Pembatalan)
    public function releaseSlot($id_slot) {
        $query = "UPDATE slot_waktu SET status_slot = 'kosong', lock_expired_at = NULL WHERE id_slot = :id_slot";
        $stmt = $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_slot", $id_slot);
        return $stmt->execute();
    }
}