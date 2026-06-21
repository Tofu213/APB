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
    <title>Dashboard Owner - Laporan Pendapatan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Sistem Informasi Futsal — Panel Owner</h1>
                <p class="text-sm text-gray-500">Pemilik Sistem: <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></p>
            </div>
            <a href="../logout.php" class="text-red-500 text-sm font-medium hover:underline">Keluar</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pendapatan Bisnis</span>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp <?php echo number_format($statistik->total_omset ?? 0, 0, ',', '.'); ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Transaksi Valid</span>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $statistik->total_transaksi ?? 0; ?> Transaksi</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Slot Lapangan Terisi Aktif</span>
                <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo $statistik->slot_terisi ?? 0; ?> Jam Terjadwal</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center justify-between">
            <form method="GET" action="" class="flex gap-4 items-center">
                <div>
                    <select name="bulan" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php if($m === $bulanPilihan) echo 'selected'; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700">
                        <option value="2026" <?php if($tahunPilihan === 2026) echo 'selected'; ?>>2026</option>
                    </select>
                </div>
            </form>
            <button onclick="window.print()" class="text-xs font-semibold bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-xl transition">
                Cetak / Ekspor PDF 📥
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 md:col-span-2">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Grafik Tren Omset Periode Ini</h3>
                <div class="h-64">
                    <canvas id="chartPendapatan"></canvas>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Rincian Angka Pendapatan</h3>
                <div class="overflow-y-auto max-h-64 text-sm">
                    <?php if(empty($grafik['raw'])): ?>
                        <p class="text-center text-gray-400 py-8 text-xs">Belum ada transaksi pada bulan ini.</p>
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
                    backgroundColor: '#16a34a', // Tema warna hijau emerald Tailwind
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