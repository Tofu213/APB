-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 03:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `futsal_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_slot` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `nama_tim` varchar(100) NOT NULL,
  `no_hp_pemesan` varchar(20) NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `status_booking` enum('menunggu_verifikasi','confirmed','ditolak','selesai','dibatalkan') NOT NULL,
  `jenis_booking` enum('online','walk_in') DEFAULT 'online',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `id_pengguna`, `id_slot`, `id_admin`, `nama_tim`, `no_hp_pemesan`, `total_bayar`, `status_booking`, `jenis_booking`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 4, 9, 4, 'Pelanggan Walk-in Kasir', '-', 150000.00, 'confirmed', 'walk_in', NULL, '2026-06-22 19:23:06', '2026-06-22 19:23:06'),
(2, 3, 8, 4, 'FC indo', '08191919191', 150000.00, 'confirmed', 'online', NULL, '2026-06-22 19:33:39', '2026-06-22 19:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id_lapangan` int(11) NOT NULL,
  `nama_lapangan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id_lapangan`, `nama_lapangan`, `deskripsi`, `status`, `created_at`) VALUES
(1, 'Lapangan Istora Interlock', 'Rumput Interlock Standar Futsal Nasional, sejuk dan nyaman.', 'aktif', '2026-06-21 22:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_booking` int(11) DEFAULT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` enum('konfirmasi','penolakan','reminder','perubahan_jadwal') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `foto_bukti_bayar` varchar(255) NOT NULL,
  `metode_bayar` enum('transfer_bank','e_wallet') NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `status_verifikasi` enum('menunggu','diverifikasi','ditolak') DEFAULT 'menunggu',
  `catatan_admin` text DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_booking`, `foto_bukti_bayar`, `metode_bayar`, `jumlah_bayar`, `status_verifikasi`, `catatan_admin`, `uploaded_at`, `verified_at`) VALUES
(1, 2, '1204b016736f3d80a58bda026f50cacc.png', 'transfer_bank', 150000.00, 'diverifikasi', 'Pembayaran valid.', '2026-06-22 19:33:39', '2026-06-22 19:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `peran` enum('pelanggan','admin','owner') NOT NULL,
  `status_akun` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password_hash`, `no_hp`, `peran`, `status_akun`, `created_at`, `updated_at`) VALUES
(1, 'Raffi Admin', 'admin@futsal.com', '$2y$10$8C7RkFk1CqYI8V5V.eBlUebN23R3M1O9vGqE2L6G.gE3Yh7I6X7mS', '0811111111', 'admin', 'aktif', '2026-06-21 22:31:41', '2026-06-21 22:31:41'),
(2, 'Zaki Owner', 'owner@futsal.com', '$2y$10$8C7RkFk1CqYI8V5V.eBlUebN23R3M1O9vGqE2L6G.gE3Yh7I6X7mS', '0822222222', 'owner', 'aktif', '2026-06-21 22:31:41', '2026-06-21 22:31:41'),
(3, 'Laurensius Jovito', 'test@gmail.com', '$2y$10$O0usg3KUBlC4kXaNzitA4esevncmZQLp6wr6gOSnTR6.QFfUcnqTO', '081937772345', 'pelanggan', 'aktif', '2026-06-21 22:38:04', '2026-06-21 22:38:04'),
(4, 'admin', 'adminutama@gmail.com', '$2y$10$h5pyYBaQmdLT8WrnG5gGdOVQMLwCgbzCM5CemUt.v3JZg4v/PiEk2', '081010101', 'admin', 'aktif', '2026-06-22 19:06:30', '2026-06-22 19:06:54'),
(5, 'owner', 'owner@gmail.com', '$2y$10$iLJgOnBirYtowVEdid6vUe8QtABKpreaJyijFKDYBeH.KlIJo5hg.', '08999', 'owner', 'aktif', '2026-06-22 19:19:51', '2026-06-22 19:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `slot_waktu`
--

CREATE TABLE `slot_waktu` (
  `id_slot` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status_slot` enum('kosong','terkunci','terisi') DEFAULT 'kosong',
  `lock_expired_at` datetime DEFAULT NULL,
  `tarif` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slot_waktu`
--

INSERT INTO `slot_waktu` (`id_slot`, `id_lapangan`, `tanggal`, `jam_mulai`, `jam_selesai`, `status_slot`, `lock_expired_at`, `tarif`) VALUES
(1, 1, '2026-06-21', '15:00:00', '16:00:00', 'kosong', NULL, 150000.00),
(2, 1, '2026-06-21', '16:00:00', '17:00:00', 'kosong', NULL, 150000.00),
(3, 1, '2026-06-21', '17:00:00', '18:00:00', 'kosong', NULL, 170000.00),
(4, 1, '2026-06-21', '19:00:00', '20:00:00', 'kosong', NULL, 190000.00),
(5, 1, '2026-06-21', '20:00:00', '21:00:00', 'kosong', NULL, 190000.00),
(6, 1, '2026-06-22', '15:00:00', '16:00:00', 'kosong', NULL, 150000.00),
(7, 1, '2026-06-22', '16:00:00', '17:00:00', 'kosong', NULL, 150000.00),
(8, 1, '2026-06-22', '17:00:00', '18:00:00', 'terisi', NULL, 150000.00),
(9, 1, '2026-06-22', '18:00:00', '19:00:00', 'terisi', NULL, 150000.00),
(10, 1, '2026-06-22', '19:00:00', '20:00:00', 'kosong', NULL, 150000.00),
(11, 1, '2026-06-22', '20:00:00', '21:00:00', 'kosong', NULL, 150000.00),
(12, 1, '2026-06-22', '21:00:00', '22:00:00', 'kosong', NULL, 150000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tarif_promosi`
--

CREATE TABLE `tarif_promosi` (
  `id_tarif` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `nama_tarif` varchar(100) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `fk_booking_pengguna` (`id_pengguna`),
  ADD KEY `fk_booking_slot` (`id_slot`),
  ADD KEY `fk_booking_admin` (`id_admin`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id_lapangan`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `fk_notifikasi_pengguna` (`id_pengguna`),
  ADD KEY `fk_notifikasi_booking` (`id_booking`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `booking_unique` (`id_booking`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `slot_waktu`
--
ALTER TABLE `slot_waktu`
  ADD PRIMARY KEY (`id_slot`),
  ADD KEY `fk_slot_lapangan` (`id_lapangan`);

--
-- Indexes for table `tarif_promosi`
--
ALTER TABLE `tarif_promosi`
  ADD PRIMARY KEY (`id_tarif`),
  ADD KEY `fk_tarif_lapangan` (`id_lapangan`),
  ADD KEY `fk_tarif_owner` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id_lapangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `slot_waktu`
--
ALTER TABLE `slot_waktu`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tarif_promosi`
--
ALTER TABLE `tarif_promosi`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_admin` FOREIGN KEY (`id_admin`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_booking_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_slot` FOREIGN KEY (`id_slot`) REFERENCES `slot_waktu` (`id_slot`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notifikasi_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE;

--
-- Constraints for table `slot_waktu`
--
ALTER TABLE `slot_waktu`
  ADD CONSTRAINT `fk_slot_lapangan` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE;

--
-- Constraints for table `tarif_promosi`
--
ALTER TABLE `tarif_promosi`
  ADD CONSTRAINT `fk_tarif_lapangan` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tarif_owner` FOREIGN KEY (`created_by`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
