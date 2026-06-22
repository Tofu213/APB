<?php
// views/admin/kelola_jadwal.php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../../controllers/KelolaJadwalController.php';

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
    <title>Kelola Jadwal - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-sm bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700 hover:bg-gray-200">← Kembali</a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Kelola Jadwal Lapangan (UC-05)</h1>
                </div>
            </div>
            <a href="../logout.php" class="text-red-500 text-sm font-medium hover:underline">Keluar</a>
        </div>

        <?php if ($status === 'generated'): ?>
            <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">Jadwal lapangan untuk tanggal tersebut berhasil dibuka!</div>
        <?php elseif ($status === 'walkin_success'): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">Booking Walk-in berhasil! Slot otomatis terkunci.</div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Tanggal</label>
                <form method="GET" action="" id="formTanggal">
                    <input type="date" name="tanggal" value="<?php echo $tanggalPilihan; ?>" onchange="document.getElementById('formTanggal').submit();"
                           class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </form>
            </div>
            
            <?php if (empty($listSlot)): ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate">
                <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl transition">
                    + Buka Jadwal Operasional (15:00 - 22:00)
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (empty($listSlot)): ?>
                <div class="col-span-full bg-white p-8 rounded-2xl text-center text-gray-500 shadow-sm border border-dashed border-gray-300">
                    Belum ada slot waktu dibuka untuk tanggal ini. Klik tombol biru di atas untuk meng-generate jadwal!
                </div>
            <?php else: ?>
                <?php foreach ($listSlot as $slot): ?>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col justify-between h-36">
                        <div class="flex justify-between">
                            <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded-md">
                                <?php echo date('H:i', strtotime($slot->jam_mulai)) . ' - ' . date('H:i', strtotime($slot->jam_selesai)); ?>
                            </span>
                            <span class="text-[10px] uppercase font-bold <?php echo $slot->status_riil === 'kosong' ? 'text-green-600' : 'text-red-500'; ?>">
                                <?php echo $slot->status_riil; ?>
                            </span>
                        </div>
                        
                        <div class="mt-auto">
                            <?php if ($slot->status_riil === 'kosong'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Proses pelanggan Walk-in di jam ini?');">
                                    <input type="hidden" name="action" value="walk_in">
                                    <input type="hidden" name="tanggal" value="<?php echo $tanggalPilihan; ?>">
                                    <input type="hidden" name="id_slot" value="<?php echo $slot->id_slot; ?>">
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white text-xs font-semibold py-2 rounded-xl transition">
                                        Isi via Walk-in 👤
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="w-full bg-gray-100 text-gray-400 text-xs font-semibold py-2 rounded-xl cursor-not-allowed">
                                    Slot Terkunci
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