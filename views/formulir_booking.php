<?php
// views/formulir_booking.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/BookingController.php';

$id_slot = $_GET['id_slot'] ?? 0;

// Menarik data detail slot tunggal yang dipilih
$database = new Database();
$dbConn = $database->getConnection();
$stmt = $dbConn->prepare("SELECT * FROM slot_waktu WHERE id_slot = :id LIMIT 1");
$stmt->bindParam(":id", $id_slot);
$stmt->execute();
$slotDetail = $stmt->fetch();

if (!$slotDetail) {
    echo "Slot waktu tidak ditemukan.";
    exit();
}

$bookingController = new BookingController();
$errorMessage = $bookingController->handleTempatkanBooking();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pembayaran Sewa - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">

    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-md">
        <div class="mb-6">
            <a href="jadwal.php" class="text-sm text-green-600 hover:underline">← Kembali ke Kalender</a>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">Formulir Isian Booking Lapangan</h2>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="bg-gray-50 p-4 rounded-xl mb-6 space-y-1.5 border border-gray-200 text-sm text-gray-700">
            <p><strong>Tanggal Main:</strong> <?php echo date('d M Y', strtotime($slotDetail->tanggal)); ?></p>
            <p><strong>Durasi Waktu:</strong> <?php echo date('H:i', strtotime($slotDetail->jam_mulai)) . ' - ' . date('H:i', strtotime($slotDetail->jam_selesai)); ?> WIB</p>
            <p class="text-green-600 font-bold text-base"><strong>Total Tarif:</strong> Rp <?php echo number_format($slotDetail->tarif, 0, ',', '.'); ?></p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id_slot" value="<?php echo $slotDetail->id_slot; ?>">
            <input type="hidden" name="total_bayar" value="<?php echo $slotDetail->tarif; ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tim Futsal</label>
                <input type="text" name="nama_tim" required placeholder="Contoh: FC Nusantara"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP Pemesan</label>
                <input type="text" name="no_hp_pemesan" required placeholder="Contoh: 08123456789"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="metode_bayar" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none text-gray-700">
                    <option value="transfer_bank">Transfer Bank Mandiri / BCA</option>
                    <option value="e_wallet">E-Wallet (Dana / OVO / GoPay)</option>
                </select>
                <p class="text-[11px] text-gray-500 mt-1">Silakan transfer sesuai nominal di atas ke No. Rekening: <strong>123-456-7890 (Futsal Center)</strong></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto Bukti Transfer</label>
                <input type="file" name="bukti_bayar" required accept="image/png, image/jpeg, image/jpg"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-[11px] text-gray-400 mt-1">Format berkas yang didukung hanya JPG, JPEG, atau PNG.</p>
            </div>

            <button type="submit" 
                    class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition duration-200 shadow-sm">
                Ajukan Sewa Lapangan
            </button>
        </form>
    </div>

</body>
</html>