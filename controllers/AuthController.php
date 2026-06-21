<?php
// controllers/AuthController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        // Inisialisasi DB, Repo, dan Service secara berantai sesuai arsitektur
        $database = new Database();
        $dbConn = $database->getConnection();
        $userRepo = new UserRepository($dbConn);
        $this->authService = new AuthService($userRepo);
    }

    // Menangani request dari form login HTML
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            $result = $this->authService->login($email, $password);

            if ($result['status']) {
                // Redirect dashboard secara dinamis berdasarkan peran aktor
                if ($result['peran'] === 'admin') {
                    header("Location: ../views/admin/dashboard.php");
                } elseif ($result['peran'] === 'owner') {
                    header("Location: ../views/owner/dashboard.php");
                } else {
                    // [PERBAIKAN]: Pelanggan diarahkan ke jadwal.php, bukan pelanggan/dashboard.php
                    header("Location: ../views/jadwal.php");
                }
                exit();
            } else {
                // Kirim pesan error kembali ke halaman login
                return $result['message'];
            }
        }
    }

    // Menangani request dari form registrasi HTML
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_lengkap = htmlspecialchars($_POST['nama_lengkap'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $no_hp = htmlspecialchars($_POST['no_hp'] ?? '');
            $peran = 'pelanggan'; // Default pendaftar baru lewat aplikasi adalah pelanggan

            $result = $this->authService->register($nama_lengkap, $email, $password, $no_hp, $peran);

            if ($result['status']) {
                // Karena dipanggil dari views/register.php, langsung arahkan ke login.php
                header("Location: login.php?registration=success");
                exit();
            } else {
                return $result['message'];
            }
        }
    }
}