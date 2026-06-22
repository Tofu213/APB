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
    <title>Kalender Jadwal - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .slot-card {
            transition: all 0.2s ease;
            border-radius: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .slot-card:hover:not(.cursor-not-allowed) {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        .slot-card.kosong {
            background: linear-gradient(145deg, #22c55e, #16a34a);
            color: white;
        }
        .slot-card.terkunci {
            background: linear-gradient(145deg, #fbbf24, #f59e0b);
            color: white;
            opacity: 0.8;
        }
        .slot-card.terisi {
            background: linear-gradient(145deg, #ef4444, #dc2626);
            color: white;
            opacity: 0.8;
        }
        .slot-card.selesai {
            background: #9ca3af;
            color: white;
            opacity: 0.6;
        }
        .header-gradient {
            background: linear-gradient(145deg, #0b3d0b, #1a6e1a);
        }
        .btn-logout {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="header-gradient rounded-2xl p-5 flex flex-wrap items-center justify-between shadow-xl mb-6 text-white">
            <div class="flex items-center gap-3">
                <i class="fas fa-futbol text-3xl"></i>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Jadwal Lapangan Futsal</h1>
                    <p class="text-sm opacity-80">Halo, <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pelanggan'); ?> 👋</p>
                </div>
            </div>
            <a href="logout.php" class="btn-logout px-5 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>

        <!-- Notifikasi sukses -->
        <?php if ($isSuccess): ?>
            <div class="bg-green-100 border-l-4 border-green-600 text-green-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <span>Pemesanan slot lapangan Anda berhasil diajukan! Menunggu verifikasi admin.</span>
            </div>
        <?php endif; ?>

        <!-- Filter tanggal & Legenda -->
        <div class="bg-white/80 backdrop-blur p-5 rounded-2xl shadow-md mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-calendar-alt text-green-600 mr-2"></i>Pilih Tanggal Bermain</label>
                <form method="GET" action="" id="formTanggal" class="inline">
                    <input type="date" name="tanggal" value="<?php echo $tanggalPilihan; ?>" onchange="document.getElementById('formTanggal').submit();"
                           class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-gray-800 font-medium bg-white">
                </form>
            </div>
            <div class="flex flex-wrap gap-3 text-xs font-medium">
                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-green-500 block"></span> Kosong</div>
                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-amber-400 block"></span> Terkunci</div>
                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-red-500 block"></span> Terisi</div>
                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-gray-400 block"></span> Selesai</div>
            </div>
        </div>

        <!-- Grid slot -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php if (empty($listSlot)): ?>
                <div class="col-span-full bg-white/80 backdrop-blur p-12 rounded-2xl text-center text-gray-500 shadow-md border-2 border-dashed border-gray-300">
                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-2 block"></i>
                    Belum ada definisi slot waktu untuk tanggal ini.
                </div>
            <?php else: ?>
                <?php foreach ($listSlot as $slot): 
                    $statusClass = 'kosong';
                    $statusLabel = 'Kosong';
                    $icon = 'fa-clock';
                    if ($slot->status_riil === 'terkunci') {
                        $statusClass = 'terkunci';
                        $statusLabel = 'Terkunci';
                        $icon = 'fa-lock';
                    } elseif ($slot->status_riil === 'terisi') {
                        $statusClass = 'terisi';
                        $statusLabel = 'Terisi';
                        $icon = 'fa-users';
                    } elseif ($slot->status_riil === 'selesai') {
                        $statusClass = 'selesai';
                        $statusLabel = 'Selesai';
                        $icon = 'fa-check-circle';
                    }
                    $onclick = ($slot->status_riil === 'kosong') ? "window.location.href='formulir_booking.php?id_slot=".$slot->id_slot."'" : "";
                ?>
                    <div <?php if($onclick) echo "onclick=\"$onclick\""; ?>
                         class="slot-card <?php echo $statusClass; ?> p-4 flex flex-col justify-between h-36 <?php echo $onclick ? 'cursor-pointer' : 'cursor-not-allowed'; ?>">
                        <div class="flex justify-between items-start">
                            <span class="text-xs uppercase tracking-wider opacity-80"><i class="far fa-clock mr-1"></i>Jam</span>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-bold uppercase"><?php echo $statusLabel; ?></span>
                        </div>
                        <div>
                            <p class="font-bold text-lg">
                                <?php echo date('H:i', strtotime($slot->jam_mulai)) . ' - ' . date('H:i', strtotime($slot->jam_selesai)); ?>
                            </p>
                            <div class="flex justify-between items-end mt-1">
                                <span class="text-sm font-semibold">Rp <?php echo number_format($slot->tarif, 0, ',', '.'); ?></span>
                                <i class="fas <?php echo $icon; ?> text-xs opacity-70"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>