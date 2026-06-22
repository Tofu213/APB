<?php
// services/PaymentService.php
class PaymentService {
    private $paymentRepo;
    private $slotRepo;

    public function __construct($paymentRepository, $slotRepository) {
        $this->paymentRepo = $paymentRepository;
        $this->slotRepo = $slotRepository;
    }

    public function getDaftarVerifikasi() {
        return $this->paymentRepo->findBookingMenungguVerifikasi();
    }

    public function konfirmasiPembayaran($id_booking, $id_slot, $id_admin) {
        $this->paymentRepo->updateBookingStatus($id_booking, 'confirmed', $id_admin);
        $this->paymentRepo->updatePaymentStatus($id_booking, 'diverifikasi', 'Pembayaran valid.');
        return $this->slotRepo->lockPermanent($id_slot);
    }

    public function tolakPembayaran($id_booking, $id_slot, $id_admin, $catatan) {
        $this->paymentRepo->updateBookingStatus($id_booking, 'ditolak', $id_admin);
        $this->paymentRepo->updatePaymentStatus($id_booking, 'ditolak', $catatan);
        return $this->slotRepo->releaseSlot($id_slot);
    }
}