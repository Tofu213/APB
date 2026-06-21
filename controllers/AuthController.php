<?php
// controllers/AuthController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        // Inisialisasi DB, Repo, dan Service secara berantai sesuai arsitektur [cite: 60]
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
                // Redirect dashboard secara dinamis berdasarkan peran aktor [cite: 42, 46, 70, 72]
                if ($result['peran'] === 'admin') {
                    header("Location: ../views/admin/dashboard.php");
                } elseif ($result['peran'] === 'owner') {
                    header("Location: ../views/owner/dashboard.php");
                } else {
                    header("Location: ../views/pelanggan/dashboard.php");
                }
                exit();
            } else {
                // Kirim pesan error kembali ke halaman login [cite: 73]
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
            $peran = 'pelanggan'; // Default pendaftar baru lewat aplikasi adalah pelanggan [cite: 63]

            $result = $this->authService->register($nama_lengkap, $email, $password, $no_hp, $peran);

            if ($result['status']) {
                header("Location: ../views/login.php?registration=success");
                exit();
            } else {
                return $result['message'];
            }
        }
    }
}