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
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Selamat Datang</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Silakan masuk untuk menyewa lapangan</p>

        <?php if ($isRegistered): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                Registrasi berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage) && is_string($errorMessage)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <button type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition duration-200">
                Masuk
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-4">
            Belum punya akun? <a href="register.php" class="text-green-600 font-medium hover:underline">Daftar di sini</a>
        </p>
    </div>

</body>
</html>