<?php
// views/admin/kelola_jadwal.php

// 1. Paksa PHP menampilkan error jika terjadi kendala data
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

// 2. Proteksi keamanan halaman: Hanya aktor Admin yang boleh masuk
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 3. Lacak dan muat file KelolaJadwalController
$controllerPath = dirname(__DIR__, 2) . '/controllers/KelolaJadwalController.php';

if (!file_exists($controllerPath)) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'><b>ERROR:</b> File KelolaJadwalController.php tidak ditemukan di jalur: {$controllerPath}</div>");
}

require_once $controllerPath;

$jadwalController = new KelolaJadwalController();
$jadwalController->handleAksiAdmin();

// Mengambil parameter tanggal dari filter browser, default adalah tanggal hari ini
$tanggalPilihan = $_GET['tanggal'] ?? date('Y-m-d');
$listSlot = $jadwalController->lihatJadwal($tanggalPilihan);
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Operasional - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-sm bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700 hover:bg-gray-200 transition">← Dashboard</a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Kelola Jadwal Lapangan (UC-05)</h1>
                    <p class="text-xs text-gray-400">Atur ketersediaan jam bermain dan kelola transaksi langsung kasir</p>
                </div>
            </div>
            <a href="../logout.php" class="text-red-500 text-sm font-medium hover:underline">Keluar</a>
        </div>

        <?php if ($status === 'generated'): ?>
            <div class="bg-blue-100 border border-blue-300 text-blue-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">
                Sistem Berhasil membuka slot jadwal operasional (15:00 - 22:00) untuk tanggal terpilih.
            </div>
        <?php elseif ($status === 'walkin_success'): ?>
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">
                Transaksi sewa langsung (Walk-in) berhasil dicatat! Slot terkunci permanen dan omset masuk ke laporan.
            </div>
        <?php elseif ($status === 'cancelled'): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">
                Pemesanan berhasil dibatalkan secara manual. Status transaksi berubah menjadi 'dibatalkan' dan slot dibuka kembali.
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Kelola</label>
                <form method="GET" action="" id="formTanggal">
                    <input type="date" name="tanggal" value="<?php echo $tanggalPilihan; ?>" onchange="document.getElementById('formTanggal').submit();"
                           class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-800 font-medium">
                </form>
            </div>
            
            <?php if (empty($listSlot)): ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate">
                <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm text-sm">
                    + Buka Slot Operasional Otomatis (15:00 - 22:00)
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (empty($listSlot)): ?>
                <div class="col-span-full bg-white p-12 rounded-2xl text-center text-gray-400 shadow-sm border border-dashed border-gray-300 text-sm">
                    Belum ada jam operasional yang dibuka untuk tanggal ini.<br>
                    <span class="text-xs text-gray-400 mt-1 block">Silakan klik tombol biru **"Buka Slot Operasional Otomatis"** di atas untuk merilis jadwal baru.</span>
                </div>
            <?php else: ?>
                <?php foreach ($listSlot as $slot): 
                    // Menentukan visual batas border berdasarkan status
                    $borderClass = "border-gray-200";
                    if ($slot->status_riil === 'terisi') $borderClass = "border-red-300 bg-red-50/30";
                    if ($slot->status_riil === 'selesai') $borderClass = "border-gray-300 bg-gray-50 opacity-60";
                    if ($slot->status_riil === 'terkunci') $borderClass = "border-amber-300 bg-amber-50/20";
                ?>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border <?php echo $borderClass; ?> flex flex-col justify-between h-36 transition">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg">
                                <?php echo date('H:i', strtotime($slot->jam_mulai)) . ' - ' . date('H:i', strtotime($slot->jam_selesai)); ?>
                            </span>
                            
                            <?php if ($slot->status_riil === 'kosong'): ?>
                                <span class="text-[10px] uppercase font-bold text-green-600 tracking-wider">Kosong</span>
                            <?php elseif ($slot->status_riil === 'terkunci'): ?>
                                <span class="text-[10px] uppercase font-bold text-amber-500 tracking-wider">Booking Online</span>
                            <?php elseif ($slot->status_riil === 'terisi'): ?>
                                <span class="text-[10px] uppercase font-bold text-red-500 tracking-wider">Terisi</span>
                            <?php else: ?>
                                <span class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Selesai</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-auto">
                            <?php if ($slot->status_riil === 'kosong'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Proses entri pelanggan Walk-in di jam ini? (Pemasukan akan langsung dicatat ke Owner)');">
                                    <input type="hidden" name="action" value="walk_in">
                                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                                    <input type="hidden" name="id_slot" value="<?php echo $slot->id_slot; ?>">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 rounded-xl transition shadow-sm">
                                        Isi via Walk-in 👤
                                    </button>
                                </form>
                            <?php elseif ($slot->status_riil === 'terisi'): ?>
                                <form method="POST" action="" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan sewa jam ini secara manual? Lapangan akan dikosongkan kembali.');">
                                    <input type="hidden" name="action" value="batal_manual">
                                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                                    <input type="hidden" name="id_slot" value="<?php echo $slot->id_slot; ?>">
                                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-2 rounded-xl transition border border-red-200">
                                        Batalkan Booking ✖
                                    </button>
                                </form>
                            <?php elseif ($slot->status_riil === 'selesai'): ?>
                                <button disabled class="w-full bg-gray-200 text-gray-400 text-xs font-medium py-2 rounded-xl cursor-not-allowed text-center">
                                    Jam Selesai ✓
                                </button>
                            <?php else: ?>
                                <button disabled class="w-full bg-amber-50 text-amber-500 text-xs font-medium py-2 rounded-xl cursor-not-allowed border border-amber-100 text-center">
                                    Menunggu Bayar...
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