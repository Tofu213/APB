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
    <title>Formulir Pembayaran - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-card {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .btn-futsal {
            background: linear-gradient(145deg, #16a34a, #15803d);
            transition: all 0.2s ease;
        }
        .btn-futsal:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(22,163,74,0.4);
        }
        .detail-slot {
            background: linear-gradient(145deg, #f0fdf4, #dcfce7);
            border-left: 4px solid #16a34a;
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 flex items-center justify-center">

    <div class="form-card w-full max-w-xl p-6 md:p-8 relative overflow-hidden">
        <div class="absolute -top-8 -right-8 text-8xl opacity-10 select-none">⚽</div>

        <div class="flex items-center gap-3 mb-6">
            <a href="jadwal.php" class="text-green-600 hover:underline text-sm flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-gray-800 flex-1 text-center">Formulir Pemesanan</h2>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-100 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Detail slot -->
        <div class="detail-slot p-4 rounded-xl mb-6 space-y-1 text-sm text-gray-700">
            <p><i class="far fa-calendar-alt text-green-600 mr-2"></i><strong>Tanggal Main:</strong> <?php echo date('d M Y', strtotime($slotDetail->tanggal)); ?></p>
            <p><i class="far fa-clock text-green-600 mr-2"></i><strong>Durasi Waktu:</strong> <?php echo date('H:i', strtotime($slotDetail->jam_mulai)) . ' - ' . date('H:i', strtotime($slotDetail->jam_selesai)); ?> WIB</p>
            <p class="text-green-600 font-bold text-base"><i class="fas fa-money-bill-wave mr-2"></i><strong>Total Tarif:</strong> Rp <?php echo number_format($slotDetail->tarif, 0, ',', '.'); ?></p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id_slot" value="<?php echo $slotDetail->id_slot; ?>">
            <input type="hidden" name="total_bayar" value="<?php echo $slotDetail->tarif; ?>">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-users text-green-600 mr-2"></i>Nama Tim Futsal</label>
                <input type="text" name="nama_tim" required placeholder="Contoh: FC Nusantara"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-phone text-green-600 mr-2"></i>Nomor HP Pemesan</label>
                <input type="text" name="no_hp_pemesan" required placeholder="Contoh: 08123456789"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-credit-card text-green-600 mr-2"></i>Metode Pembayaran</label>
                <select name="metode_bayar" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-gray-700">
                    <option value="transfer_bank">Transfer Bank (Mandiri / BCA)</option>
                    <option value="e_wallet">E-Wallet (Dana / OVO / GoPay)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> Transfer sesuai nominal ke rekening: <strong class="text-green-700">123-456-7890 (Futsal Center)</strong></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-image text-green-600 mr-2"></i>Unggah Foto Bukti Transfer</label>
                <input type="file" name="bukti_bayar" required accept="image/png, image/jpeg, image/jpg"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG</p>
            </div>

            <button type="submit" 
                    class="w-full btn-futsal text-white font-bold py-3 rounded-xl transition duration-200 shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> Ajukan Sewa Lapangan
            </button>
        </form>
    </div>

</body>
</html>