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
    <title>Dashboard Admin - Futsal Booking</title>
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
        .card-table {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        }
        .btn-action {
            transition: all 0.15s ease;
        }
        .btn-action:hover {
            transform: scale(1.03);
        }
        .badge-status {
            background: rgba(255,255,255,0.15);
        }
        .table-row:hover {
            background: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="header-gradient rounded-2xl p-5 flex flex-wrap items-center justify-between shadow-xl mb-6 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-futbol text-3xl"></i>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Panel Admin</h1>
                    <p class="text-sm opacity-80">Halo, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong> | <i class="fas fa-shield-alt mr-1"></i>Admin</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="kelola_jadwal.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 backdrop-blur">
                    <i class="fas fa-calendar-plus"></i> Atur Jadwal
                </a>
                <a href="../logout.php" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 backdrop-blur">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if ($msg === 'confirmed'): ?>
            <div class="bg-green-100 border-l-4 border-green-600 text-green-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-green-600 text-xl"></i> Transaksi berhasil dikonfirmasi dan slot dikunci permanen.
            </div>
        <?php elseif ($msg === 'rejected'): ?>
            <div class="bg-amber-100 border-l-4 border-amber-600 text-amber-800 px-5 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-times-circle text-amber-600 text-xl"></i> Transaksi ditolak, slot dikembalikan menjadi kosong.
            </div>
        <?php endif; ?>

        <!-- Tabel daftar verifikasi -->
        <div class="card-table overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <i class="fas fa-list-ul text-green-600 text-xl"></i>
                <h3 class="font-bold text-gray-800 text-lg">Daftar Tunggu Verifikasi Pembayaran</h3>
                <span class="ml-auto bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full"><?php echo count($listVerifikasi); ?> baru</span>
            </div>

            <?php if (empty($listVerifikasi)): ?>
                <div class="p-10 text-center text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-200 mb-3 block"></i>
                    Tidak ada permintaan booking baru yang memerlukan verifikasi saat ini.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="p-4"><i class="fas fa-users mr-1"></i>Nama Tim</th>
                                <th class="p-4"><i class="fas fa-phone mr-1"></i>No. HP</th>
                                <th class="p-4"><i class="fas fa-money-bill-wave mr-1"></i>Total Bayar</th>
                                <th class="p-4"><i class="fas fa-credit-card mr-1"></i>Metode</th>
                                <th class="p-4"><i class="fas fa-image mr-1"></i>Bukti</th>
                                <th class="p-4 text-center"><i class="fas fa-cog mr-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <?php foreach ($listVerifikasi as $item): ?>
                                <tr class="table-row transition">
                                    <td class="p-4 font-medium text-gray-900"><?php echo htmlspecialchars($item->nama_tim); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($item->no_hp_pemesan); ?></td>
                                    <td class="p-4 font-semibold text-green-600">Rp <?php echo number_format($item->total_bayar, 0, ',', '.'); ?></td>
                                    <td class="p-4 uppercase text-xs font-medium text-gray-500"><?php echo str_replace('_', ' ', $item->metode_bayar); ?></td>
                                    <td class="p-4">
                                        <a href="../../uploads/bukti_bayar/<?php echo $item->foto_bukti_bayar; ?>" target="_blank"
                                           class="text-green-600 hover:underline font-medium inline-flex items-center gap-1">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <form action="" method="POST" class="flex flex-wrap items-center justify-center gap-2">
                                            <input type="hidden" name="id_booking" value="<?php echo $item->id_booking; ?>">
                                            <input type="hidden" name="id_slot" value="<?php echo $item->id_slot; ?>">
                                            
                                            <input type="text" name="catatan_admin" placeholder="Alasan tolak..." 
                                                   class="px-2 py-1 text-xs border border-gray-300 rounded-lg focus:outline-none focus:border-red-400 w-32">

                                            <button type="submit" name="action" value="konfirmasi"
                                                    class="btn-action bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1">
                                                <i class="fas fa-check"></i> Konfirmasi
                                            </button>
                                            <button type="submit" name="action" value="tolak"
                                                    class="btn-action bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1">
                                                <i class="fas fa-times"></i> Tolak
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