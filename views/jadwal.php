<?php
// views/jadwal.php

// 1. Paksa PHP menampilkan error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Lacak file BookingController
$controllerPath = dirname(__DIR__) . '/controllers/BookingController.php';

if (!file_exists($controllerPath)) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px;'><b>ERROR:</b> File BookingController.php tidak ditemukan di jalur: {$controllerPath}</div>");
}

// 3. Coba jalankan file dan tangkap error syntax jika ada
try {
    require_once $controllerPath;
    $bookingController = new BookingController();
    
    $tanggalPilihan = $_GET['tanggal'] ?? date('Y-m-d');
    $listSlot = $bookingController->lihatJadwal($tanggalPilihan);
    
    $isSuccess = isset($_GET['booking']) && $_GET['booking'] === 'success';

} catch (Throwable $e) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'>
            <b>ERROR INTERNAL TERTANGKAP:</b><br>" . $e->getMessage() . "
            <br><br><i>Petunjuk: Kemungkinan ada spasi tersembunyi (Non-Breaking Space) di file Controller, Service, atau Repository.</i>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Jadwal Lapangan - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Sistem Penyewaan Lapangan Futsal</h1>
                <p class="text-sm text-gray-500">Halo, <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pelanggan'); ?></p>
            </div>
            <a href="logout.php" class="text-red-500 text-sm font-medium hover:underline">Keluar</a>
        </div>

        <?php if ($isSuccess): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">
                Pemesanan slot lapangan Anda berhasil diajukan! Menunggu verifikasi admin.
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Bermain</label>
                <form method="GET" action="" id="formTanggal">
                    <input type="date" name="tanggal" value="<?php echo $tanggalPilihan; ?>" onchange="document.getElementById('formTanggal').submit();"
                           class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none text-gray-800">
                </form>
            </div>
            <div class="flex gap-4 text-xs">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-green-500 rounded-full block"></span> Kosong</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-amber-400 rounded-full block"></span> Terkunci (15 Menit)</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-500 rounded-full block"></span> Terisi</div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (empty($listSlot)): ?>
                <div class="col-span-full bg-white p-8 rounded-2xl text-center text-gray-500 shadow-sm">
                    Belum ada definisi slot waktu untuk tanggal ini di database.
                </div>
            <?php else: ?>
                <?php foreach ($listSlot as $slot): 
                    // Menentukan warna kartu berdasarkan status riil di database
                    $bgClass = "bg-green-500 hover:bg-green-600 text-white cursor-pointer";
                    if ($slot->status_riil === 'terkunci') {
                        $bgClass = "bg-amber-400 text-white cursor-not-allowed opacity-80";
                    } elseif ($slot->status_riil === 'terisi') {
                        $bgClass = "bg-red-500 text-white cursor-not-allowed opacity-80";
                    }
                ?>
                    <div <?php if($slot->status_riil === 'kosong') { echo "onclick=\"window.location.href='formulir_booking.php?id_slot=".$slot->id_slot."';\""; } ?>
                         class="<?php echo $bgClass; ?> p-4 rounded-2xl shadow-sm transition duration-150 flex flex-col justify-between h-32">
                        <div>
                            <span class="text-xs uppercase tracking-wide opacity-80">Slot Jam</span>
                            <p class="font-bold text-lg mt-0.5">
                                <?php echo date('H:i', strtotime($slot->jam_mulai)) . ' - ' . date('H:i', strtotime($slot->jam_selesai)); ?>
                            </p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-semibold">Rp <?php echo number_format($slot->tarif, 0, ',', '.'); ?></span>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full capitalize font-medium">
                                <?php echo $slot->status_riil; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>