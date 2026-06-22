<?php
// views/register.php
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();

// Mengamankan pemrosesan: Pastikan register hanya berjalan jika ada data POST yang masuk
$errorMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errorMessage = $authController->handleRegister();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Futsal Booking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0b3d0b 0%, #1a6e1a 50%, #2e8b2e 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .glass-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(4px);
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .btn-futsal {
            background: linear-gradient(145deg, #16a34a, #15803d);
            transition: all 0.2s ease;
        }
        .btn-futsal:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(22,163,74,0.4);
        }
        .futsal-icon {
            font-size: 3rem;
            color: #16a34a;
            background: white;
            border-radius: 50%;
            padding: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md p-8 md:p-10 relative overflow-hidden">
        <!-- Dekorasi -->
        <div class="absolute -top-10 -right-10 text-9xl opacity-10 select-none">⚽</div>
        <div class="absolute -bottom-10 -left-10 text-9xl opacity-10 select-none">⚽</div>

        <div class="text-center mb-6">
            <div class="flex justify-center mb-3">
                <span class="futsal-icon"><i class="fas fa-user-plus"></i></span>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-800">Daftar Akun</h2>
            <p class="text-sm text-gray-500 mt-1">Bergabunglah untuk menyewa lapangan futsal</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-100 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-user mr-2 text-green-600"></i>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-envelope mr-2 text-green-600"></i>Email</label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-lock mr-2 text-green-600"></i>Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-phone mr-2 text-green-600"></i>Nomor HP</label>
                <input type="text" name="no_hp" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <button type="submit" 
                    class="w-full btn-futsal text-white font-bold py-3 rounded-xl transition duration-200 shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Daftar Sekarang
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-6">
            Sudah punya akun? <a href="login.php" class="text-green-600 font-semibold hover:underline">Masuk di sini</a>
        </p>
        <div class="mt-4 text-center text-xs text-gray-400">
            <i class="fas fa-futbol mr-1"></i> Futsal Booking System v2.0
        </div>
    </div>

</body>
</html>