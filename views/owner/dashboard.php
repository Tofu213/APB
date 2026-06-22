<?php
// views/owner/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Proteksi halaman dashboard owner
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'owner') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../../controllers/LaporanController.php';

$bulanPilihan = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('m'));
$tahunPilihan = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));

$laporanController = new LaporanController();
$dataLaporan = $laporanController->renderDashboardOwner($bulanPilihan, $tahunPilihan);

$statistik = $dataLaporan['statistik'];
$grafik = $dataLaporan['grafik'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-gradient {
            background: linear-gradient(145deg, #0b3d0b, #1a6e1a);
        }
        .stat-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .filter-box {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .btn-print {
            background: #1f2937;
            transition: all 0.15s ease;
        }
        .btn-print:hover {
            background: #111827;
            transform: scale(1.02);
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
                    <h1 class="text-2xl font-extrabold tracking-tight">Dashboard Owner</h1>
                    <p class="text-sm opacity-80">Halo, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong> | <i class="fas fa-crown mr-1"></i>Owner</p>
                </div>
            </div>
            <a href="../logout.php" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2 backdrop-blur">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>

        <!-- Statistik cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="stat-icon bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pendapatan</span>
                    <p class="text-2xl font-bold text-green-600">Rp <?php echo number_format($statistik->total_omset ?? 0, 0, ',', '.'); ?></p>
                </div>
            </div>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Transaksi</span>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $statistik->total_transaksi ?? 0; ?></p>
                </div>
            </div>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="stat-icon bg-amber-100 text-amber-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Slot Terisi</span>
                    <p class="text-2xl font-bold text-amber-600"><?php echo $statistik->slot_terisi ?? 0; ?> jam</p>
                </div>
            </div>
        </div>

        <!-- Filter dan Cetak -->
        <div class="filter-box p-4 flex flex-wrap items-center justify-between gap-4 mb-6">
            <form method="GET" action="" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 mr-1"><i class="fas fa-calendar-alt text-green-600"></i> Bulan</label>
                    <select name="bulan" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-gray-700">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php if($m === $bulanPilihan) echo 'selected'; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 mr-1">Tahun</label>
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-gray-700">
                        <option value="2026" <?php if($tahunPilihan === 2026) echo 'selected'; ?>>2026</option>
                    </select>
                </div>
            </form>
            <button onclick="window.print()" class="btn-print text-white font-semibold px-5 py-2 rounded-xl shadow-sm flex items-center gap-2 text-sm">
                <i class="fas fa-print"></i> Cetak / PDF
            </button>
        </div>

        <!-- Grafik dan detail -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-2xl shadow-md lg:col-span-2">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                    <i class="fas fa-chart-bar text-green-600"></i> Grafik Tren Omset
                </h3>
                <div class="h-64">
                    <canvas id="chartPendapatan"></canvas>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-md">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                    <i class="fas fa-list text-green-600"></i> Rincian Pendapatan
                </h3>
                <div class="overflow-y-auto max-h-64 text-sm">
                    <?php if(empty($grafik['raw'])): ?>
                        <p class="text-center text-gray-400 py-8 text-xs"><i class="fas fa-info-circle"></i> Belum ada transaksi pada bulan ini.</p>
                    <?php else: ?>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-100 text-xs uppercase font-medium">
                                    <th class="pb-2">Tanggal</th>
                                    <th class="pb-2 text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-700 font-medium">
                                <?php foreach($grafik['raw'] as $row): ?>
                                    <tr>
                                        <td class="py-2.5"><?php echo date('d M Y', strtotime($row->tanggal)); ?></td>
                                        <td class="py-2.5 text-right text-green-600">Rp <?php echo number_format($row->total_pendapatan, 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('chartPendapatan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($grafik['labels']); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode($grafik['datasets']); ?>,
                    backgroundColor: '#16a34a',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>