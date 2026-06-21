<?php
// services/BookingService.php

class BookingService {
    private $slotRepo;
    private $bookingRepo;

    public function __construct($slotRepository, $bookingRepository) {
        $this->slotRepo = $slotRepository;
        $this->bookingRepo = $bookingRepository;
    }

    // Mendapatkan jadwal slot lapangan
    public function getJadwalPerTanggal($tanggal) {
        return $this->slotRepo->findByTanggal($tanggal);
    }

    // Logika Alur Transaksi Booking + Auto Lock 15 Menit (UC-03)
    public function tempatkanBooking($id_user, $id_slot, $nama_tim, $no_hp, $total_bayar, $foto_bukti, $metode_bayar) {
        
        // 1. Coba amankan dan kunci slot selama 15 menit terlebih dahulu
        $isLocked = $this->slotRepo->lockSlot($id_slot);
        
        if (!$isLocked) {
            return ["status" => false, "message" => "Maaf, slot waktu ini sudah dibooking atau dikunci oleh tim lain."];
        }

        // 2. Simpan data transaksi ke tabel booking jika slot berhasil dikunci
        $idBooking = $this->bookingRepo->save($id_user, $id_slot, $nama_tim, $no_hp, $total_bayar);
        
        if (!$idBooking) {
            // Jika pencatatan booking gagal, bebaskan kembali slotnya
            $this->slotRepo->releaseSlot($id_slot);
            return ["status" => false, "message" => "Gagal membuat transaksi booking."];
        }

        // 3. Simpan berkas bukti transfer ke database pembayaran
        $simpanBayar = $this->bookingRepo->savePembayaran($idBooking, $foto_bukti, $metode_bayar, $total_bayar);

        if ($simpanBayar) {
            return ["status" => true, "message" => "Booking berhasil ditempatkan! Status: Menunggu Verifikasi Admin."];
        }

        return ["status" => false, "message" => "Gagal mengunggah data bukti pembayaran."];
    }
}