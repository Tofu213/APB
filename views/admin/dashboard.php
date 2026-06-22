<?php
// views/admin/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Paksa PHP memunculkan semua error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Proteksi halaman dashboard admin
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$controllerPath = dirname(__DIR__, 2) . '/controllers/PaymentController.php';

if (!file_exists($controllerPath)) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px;'><b>ERROR:</b> File PaymentController.php tidak ditemukan di jalur: {$controllerPath}</div>");
}

require_once $controllerPath;

$paymentController = new PaymentController();
$paymentController->handleAksiAdmin();
$listVerifikasi = $paymentController->lihatPermintaanVerifikasi();

$msg = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Verifikasi Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Panel Utama Kelola Lapangan</h1>
                    <p class="text-sm text-gray-500">Masuk sebagai Admin: <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></p>
                </div>
                <a href="kelola_jadwal.php" class="ml-4 text-sm font-medium bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100">📅 Atur Jadwal (UC-05)</a>
            </div>
            <a href="../logout.php" class="text-red-500 text-sm font-medium hover:underline">Keluar</a>
        </div>

        <?php if ($msg === 'confirmed'): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">Transaksi berhasil dikonfirmasi dan slot dikunci permanen.</div>
        <?php elseif ($msg === 'rejected'): ?>
            <div class="bg-amber-100 text-amber-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">Transaksi ditolak, slot dikembalikan menjadi kosong.</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Tunggu Verifikasi Pembayaran</h3>
            </div>

            <?php if (empty($listVerifikasi)): ?>
                <div class="p-8 text-center text-gray-500 text-sm">Tidak ada permintaan booking baru yang memerlukan verifikasi saat ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase font-semibold text-[11px] tracking-wider border-b border-gray-100">
                                <th class="p-4">Nama Tim</th>
                                <th class="p-4">No. HP</th>
                                <th class="p-4">Total Bayar</th>
                                <th class="p-4">Metode</th>
                                <th class="p-4">Bukti Transfer</th>
                                <th class="p-4 text-center">Aksi Manajemen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <?php foreach ($listVerifikasi as $item): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-medium text-gray-900"><?php echo htmlspecialchars($item->nama_tim); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($item->no_hp_pemesan); ?></td>
                                    <td class="p-4 font-semibold text-green-600">Rp <?php echo number_format($item->total_bayar, 0, ',', '.'); ?></td>
                                    <td class="p-4 uppercase text-xs font-medium text-gray-500"><?php echo str_replace('_', ' ', $item->metode_bayar); ?></td>
                                    <td class="p-4">
                                        <a href="../../uploads/bukti_bayar/<?php echo $item->foto_bukti_bayar; ?>" target="_blank"
                                           class="text-green-600 hover:underline font-medium inline-flex items-center gap-1">
                                            Lihat Foto Bukti ↗
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <form action="" method="POST" class="flex items-center justify-center gap-2">
                                            <input type="hidden" name="id_booking" value="<?php echo $item->id_booking; ?>">
                                            <input type="hidden" name="id_slot" value="<?php echo $item->id_slot; ?>">
                                            
                                            <input type="text" name="catatan_admin" placeholder="Alasan jika ditolak..." 
                                                   class="px-2 py-1 text-xs border border-gray-300 rounded-lg focus:outline-none focus:border-red-400 w-36">

                                            <button type="submit" name="action" value="konfirmasi"
                                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                Konfirmasi
                                            </button>
                                            <button type="submit" name="action" value="tolak"
                                                    class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
