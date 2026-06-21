<?php
// repositories/UserRepository.php

class UserRepository {
    private $db;

    // Instance database dimasukkan lewat constructor (Dependency Injection)
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Menemukan user berdasarkan email (Untuk Skenario Login - UC-01)
    public function findByEmail($email) {
        $query = "SELECT * FROM pengguna WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->fetch(); // Mengembalikan data berupa objek user atau false
    }

    // Menyimpan akun baru ke database (Untuk Skenario Registrasi - UC-01)
    public function save($nama_lengkap, $email, $password_hash, $no_hp, $peran) {
        $query = "INSERT INTO pengguna (nama_lengkap, email, password_hash, no_hp, peran, status_akun, created_at) 
                  VALUES (:nama, :email, :password, :no_hp, :peran, 'aktif', NOW())";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":nama", $nama_lengkap);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":no_hp", $no_hp);
        $stmt->bindParam(":peran", $peran); // 'pelanggan', 'admin', atau 'owner'
        
        if($stmt->execute()) {
            return $this->db->lastInsertId(); // Mengembalikan ID user yang baru terdaftar
        }
        return false;
    }
}