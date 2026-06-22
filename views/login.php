<?php
// 1. Paksa PHP memunculkan semua error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Lacak path absolut controller
$controllerPath = dirname(__DIR__) . '/controllers/AuthController.php';

// 3. Cek apakah file benar-benar ada di folder tersebut
if (!file_exists($controllerPath)) {
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'>
            <b>ERROR FATAL:</b><br>
            Sistem tidak bisa menemukan file AuthController.php.<br>
            Sistem mencarinya di jalur: <i>{$controllerPath}</i><br>
            Pastikan nama folder dan filenya benar!
         </div>");
}

// 4. Jika ada, jalankan file-nya
require_once $controllerPath;

try {
    $authController = new AuthController();
    $errorMessage = $authController->handleLogin();
} catch (Throwable $e) {
    // Tangkap error jika terjadi masalah di dalam Controller atau Database
    die("<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'>
            <b>ERROR INTERNAL:</b><br>" . $e->getMessage() . "
         </div>");
}

$isRegistered = isset($_GET['registration']) && $_GET['registration'] === 'success';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Futsal Booking</title>
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
        <!-- Dekorasi bola -->
        <div class="absolute -top-10 -right-10 text-9xl opacity-10 select-none">⚽</div>
        <div class="absolute -bottom-10 -left-10 text-9xl opacity-10 select-none">⚽</div>

        <div class="text-center mb-6">
            <div class="flex justify-center mb-3">
                <span class="futsal-icon"><i class="fas fa-futbol"></i></span>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-800">Selamat Datang</h2>
            <p class="text-sm text-gray-500 mt-1">Masuk ke sistem penyewaan lapangan futsal</p>
        </div>

        <?php if ($isRegistered): ?>
            <div class="bg-green-100 border-l-4 border-green-600 text-green-800 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i> Registrasi berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage) && is_string($errorMessage)): ?>
            <div class="bg-red-100 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-5">
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

            <button type="submit" 
                    class="w-full btn-futsal text-white font-bold py-3 rounded-xl transition duration-200 shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-6">
            Belum punya akun? <a href="register.php" class="text-green-600 font-semibold hover:underline">Daftar di sini</a>
        </p>
        <div class="mt-4 text-center text-xs text-gray-400">
            <i class="fas fa-futbol mr-1"></i> Futsal Booking System v2.0
        </div>
    </div>

</body>
</html>