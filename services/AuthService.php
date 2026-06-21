<?php
// services/AuthService.php

class AuthService {
    private $userRepo;

    // Memasukkan UserRepository ke dalam Service (Dependency Injection)
    public function __construct($userRepository) {
        $this->userRepo = $userRepository;
    }

    // Logika Bisnis Registrasi (UC-01)
    public function register($nama_lengkap, $email, $password, $no_hp, $peran) {
        // 1. Cek apakah email sudah terdaftar di database
        $existingUser = $this->userRepo->findByEmail($email);
        if ($existingUser) {
            return ["status" => false, "message" => "Email sudah terdaftar sebelumnya."]; // Sesuai alternatif alur dokumen [cite: 74]
        }

        // 2. Enkripsi password demi keamanan data (tidak boleh plain text)
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 3. Simpan ke database via Repository
        $userId = $this->userRepo->save($nama_lengkap, $email, $password_hash, $no_hp, $peran);

        if ($userId) {
            return ["status" => true, "message" => "Registrasi berhasil!", "user_id" => $userId];
        }

        return ["status" => false, "message" => "Gagal menyimpan data ke sistem."];
    }

    // Logika Bisnis Login (UC-01)
    public function login($email, $password) {
        // 1. Cari user berdasarkan email
        $user = $this->userRepo->findByEmail($email);
        if (!$user) {
            return ["status" => false, "message" => "Email atau password salah."]; // Sesuai alternatif alur dokumen [cite: 73]
        }

        // 2. Verifikasi kecocokan password dengan hash di database
        if (!password_verify($password, $user->password_hash)) {
            return ["status" => false, "message" => "Email atau password salah."]; // Sesuai alternatif alur dokumen [cite: 73]
        }

        // 3. Jika valid, buat session login aktif di server
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user->id_pengguna;
        $_SESSION['nama']    = $user->nama_lengkap;
        $_SESSION['peran']   = $user->peran; // 'pelanggan', 'admin', atau 'owner' [cite: 158]

        return [
            "status" => true, 
            "message" => "Login berhasil!", 
            "peran" => $user->peran
        ];
    }

    // Logika Bisnis Logout
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return ["status" => true, "message" => "Berhasil logout."];
    }
}