<?php
// views/logout.php

// 1. Mulai sesi jika belum berjalan
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Kosongkan semua data (variabel) di dalam sesi saat ini
session_unset();

// 3. Hancurkan sesi sepenuhnya dari server
session_destroy();

// 4. Arahkan pengguna kembali ke halaman login
header("Location: login.php");
exit();