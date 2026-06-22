<?php
// views/admin/kelola_jadwal.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$controllerPath = dirname(__DIR__, 2) . '/controllers/KelolaJadwalController.php';

if (!file_exists($controllerPath)) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'><b>ERROR:</b> File KelolaJadwalController.php tidak ditemukan di jalur: {$controllerPath}</div>");
}

require_once $controllerPath;

$jadwalController = new KelolaJadwalController();
$jadwalController->handleAksiAdmin();

$tanggalPilihan = $_GET['tanggal'] ?? date('Y-m-d');
$listSlot = $jadwalController->lihatJadwal($tanggalPilihan);
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal - Admin Futsal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-gradient {
            background: linear-gradient(145deg, #0b3d0b, #1a6e1a);
        }
        .slot-card-admin {
            border-radius: 1.25rem;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .slot-card-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .btn-walkin {
            background: linear-gradient(145deg, #16a34a, #15803d);
            transition: all 0.2s ease;
        }
        .btn-walkin:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(22,163,74,0.3);
        }
        .btn-batal {
            background: white;
            border: 1px solid #f87171;
            color: #dc2626;
            transition: all 0.2s ease;
        }
        .btn-batal:hover {
            background: #fef2f2;
            border-color: #dc2626;
        }
        .btn-generate {
            background: linear-gradient(145deg, #2563eb, #1d4ed8);
            transition: all 0.2s ease;
        }
        .btn-generate:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .btn-manual {
            background: linear-gradient(145deg, #8b5cf6, #7c3aed);
            transition: all 0.2s ease;
        }
        .btn-manual:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }
        .form-manual {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(4px);
            border-radius: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="header-gradient rounded-2xl p-5 flex flex-wrap items-center justify-between shadow-xl mb-6 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-futbol text-3xl"></i>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Kelola Jadwal Operasional</h1>
                    <p class="text-sm opacity-80"><i class="fas fa-calendar-alt mr-1"></i>Atur ketersediaan dan transaksi langsung</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="dashboard.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 backdrop-blur">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <a href="../logout.php" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 backdrop-blur">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if ($status === 'generated'): ?>
            <div class="bg-blue-100 border-l-4 border-blue-600 text-blue-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-blue-600 text-xl"></i> Sistem berhasil membuka slot jadwal operasional (15:00 - 22:00) untuk tanggal terpilih.
            </div>
        <?php elseif ($status === 'manual_added'): ?>
            <div class="bg-purple-100 border-l-4 border-purple-600 text-purple-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-purple-600 text-xl"></i> Slot berhasil ditambahkan secara manual!
            </div>
        <?php elseif ($status === 'manual_fail'): ?>
            <div class="bg-red-100 border-l-4 border-red-600 text-red-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-times-circle text-red-600 text-xl"></i> Gagal menambahkan slot. Periksa data input (tanggal, jam, tarif).
            </div>
        <?php elseif ($status === 'walkin_success'): ?>
            <div class="bg-green-100 border-l-4 border-green-600 text-green-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-green-600 text-xl"></i> Transaksi sewa langsung (Walk-in) berhasil dicatat! Slot terkunci permanen.
            </div>
        <?php elseif ($status === 'cancelled'): ?>
            <div class="bg-red-100 border-l-4 border-red-600 text-red-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-times-circle text-red-600 text-xl"></i> Pemesanan berhasil dibatalkan secara manual. Slot dibuka kembali.
            </div>
        <?php endif; ?>

        <!-- Form Tambah Manual & Generate -->
        <div class="form-manual p-5 rounded-2xl shadow-md mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <!-- Filter Tanggal (tetap) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-calendar-alt text-green-600 mr-2"></i>Pilih Tanggal</label>
                    <form method="GET" action="" id="formTanggal" class="inline">
                        <input type="date" name="tanggal" value="<?php echo $tanggalPilihan; ?>" onchange="document.getElementById('formTanggal').submit();"
                               class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-gray-800 font-medium bg-white">
                    </form>
                </div>

                <!-- Tombol Generate Otomatis -->
                <?php if (empty($listSlot)): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate">
                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                    <button type="submit" class="btn-generate text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Buka Slot Otomatis
                    </button>
                </form>
                <?php endif; ?>

                <!-- Form Tambah Manual -->
                <form method="POST" action="" class="flex flex-wrap items-end gap-3 ml-auto">
                    <input type="hidden" name="action" value="tambah_manual">
                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                    <input type="hidden" name="id_lapangan" value="1">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required class="px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Jam Selesai</label>
                        <input type="time" name="jam_selesai" required class="px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Tarif (Rp)</label>
                        <input type="number" name="tarif" required placeholder="150000" class="px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none text-sm w-32">
                    </div>
                    <button type="submit" class="btn-manual text-white font-semibold px-4 py-2 rounded-xl shadow-sm flex items-center gap-2 text-sm">
                        <i class="fas fa-pen"></i> Tambah Manual
                    </button>
                </form>
            </div>
        </div>

        <!-- Grid slot admin -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (empty($listSlot)): ?>
                <div class="col-span-full bg-white/80 backdrop-blur p-12 rounded-2xl text-center text-gray-500 shadow-md border-2 border-dashed border-gray-300">
                    <i class="fas fa-clock text-4xl text-gray-300 mb-2 block"></i>
                    Belum ada jam operasional yang dibuka untuk tanggal ini.<br>
                    <span class="text-xs text-gray-400">Gunakan tombol <strong>“Buka Slot Otomatis”</strong> atau <strong>“Tambah Manual”</strong> di atas.</span>
                </div>
            <?php else: ?>
                <?php foreach ($listSlot as $slot): 
                    $statusClass = 'border-gray-200 bg-white';
                    $statusLabel = 'Kosong';
                    $badgeColor = 'bg-green-100 text-green-700';
                    $disabled = false;
                    if ($slot->status_riil === 'terisi') {
                        $statusClass = 'border-red-300 bg-red-50/30';
                        $statusLabel = 'Terisi';
                        $badgeColor = 'bg-red-100 text-red-700';
                    } elseif ($slot->status_riil === 'selesai') {
                        $statusClass = 'border-gray-300 bg-gray-50 opacity-70';
                        $statusLabel = 'Selesai';
                        $badgeColor = 'bg-gray-100 text-gray-500';
                        $disabled = true;
                    } elseif ($slot->status_riil === 'terkunci') {
                        $statusClass = 'border-amber-300 bg-amber-50/30';
                        $statusLabel = 'Menunggu Bayar';
                        $badgeColor = 'bg-amber-100 text-amber-700';
                        $disabled = true;
                    }
                ?>
                    <div class="slot-card-admin p-4 border <?php echo $statusClass; ?> flex flex-col justify-between h-40">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg">
                                <i class="far fa-clock mr-1"></i><?php echo date('H:i', strtotime($slot->jam_mulai)) . ' - ' . date('H:i', strtotime($slot->jam_selesai)); ?>
                            </span>
                            <span class="text-[10px] uppercase font-bold <?php echo $badgeColor; ?> px-2 py-0.5 rounded-full"><?php echo $statusLabel; ?></span>
                        </div>
                        
                        <div class="mt-auto">
                            <?php if ($slot->status_riil === 'kosong'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Entri pelanggan Walk-in di jam ini?');">
                                    <input type="hidden" name="action" value="walk_in">
                                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                                    <input type="hidden" name="id_slot" value="<?php echo $slot->id_slot; ?>">
                                    <button type="submit" class="btn-walkin w-full text-white text-xs font-bold py-2 rounded-xl shadow-sm flex items-center justify-center gap-1">
                                        <i class="fas fa-user-plus"></i> Walk-in
                                    </button>
                                </form>
                            <?php elseif ($slot->status_riil === 'terisi'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Batalkan booking ini? Slot akan kosong kembali.');">
                                    <input type="hidden" name="action" value="batal_manual">
                                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                                    <input type="hidden" name="id_slot" value="<?php echo $slot->id_slot; ?>">
                                    <button type="submit" class="btn-batal w-full text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1">
                                        <i class="fas fa-ban"></i> Batalkan
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="w-full bg-gray-200 text-gray-400 text-xs font-medium py-2 rounded-xl cursor-not-allowed flex items-center justify-center gap-1">
                                    <i class="fas fa-lock"></i> <?php echo ($slot->status_riil === 'selesai') ? 'Selesai' : 'Terkunci'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>