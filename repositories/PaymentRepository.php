<?php
// repositories/PaymentRepository.php
class PaymentRepository {
    private $db;
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function findBookingMenungguVerifikasi() {
        $query = "SELECT b.*, p.foto_bukti_bayar, p.metode_bayar, p.id_pembayaran 
                  FROM booking b 
                  JOIN pembayaran p ON b.id_booking = p.id_booking 
                  WHERE b.status_booking = 'menunggu_verifikasi' 
                  ORDER BY b.created_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateBookingStatus($id_booking, $status, $id_admin) {
        $query = "UPDATE booking SET status_booking = :status, id_admin = :id_admin, updated_at = NOW() WHERE id_booking = :id_booking";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id_admin", $id_admin);
        $stmt->bindParam(":id_booking", $id_booking);
        return $stmt->execute();
    }

    public function updatePaymentStatus($id_booking, $status, $catatan_admin) {
        $query = "UPDATE pembayaran SET status_verifikasi = :status, catatan_admin = :catatan_admin, verified_at = NOW() WHERE id_booking = :id_booking";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":catatan_admin", $catatan_admin);
        $stmt->bindParam(":id_booking", $id_booking);
        return $stmt->execute();
    }
}