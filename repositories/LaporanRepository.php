<?php
// repositories/LaporanRepository.php

class LaporanRepository {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Mengambil total transaksi harian untuk grafik pendapatan (UC-06)
    public function getPendapatanHarian($bulan, $tahun) {
        $query = "SELECT DATE(created_at) as tanggal, SUM(total_bayar) as total_pendapatan, COUNT(id_booking) as jumlah_transaksi
                  FROM booking 
                  WHERE status_booking = 'confirmed' AND MONTH(created_at) = :bulan AND YEAR(created_at) = :tahun
                  GROUP BY DATE(created_at)
                  ORDER BY DATE(created_at) ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":bulan", $bulan, PDO::PARAM_INT);
        $stmt->bindParam(":tahun", $tahun, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Mengambil statistik ringkasan total (pendapatan, transaksi, slot terisi)
    public function getRingkasanStatistik() {
        $query = "SELECT 
                    (SELECT SUM(total_bayar) FROM booking WHERE status_booking = 'confirmed') as total_omset,
                    (SELECT COUNT(id_booking) FROM booking WHERE status_booking = 'confirmed') as total_transaksi,
                    (SELECT COUNT(id_slot) FROM slot_waktu WHERE status_slot = 'terisi') as slot_terisi";
                    
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}