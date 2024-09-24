-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2024 at 03:33 PM
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
-- Database: `banksampah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'admin2', '12345', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_sampah`
--

CREATE TABLE `jenis_sampah` (
  `id` int(11) NOT NULL,
  `nama_jenis` varchar(50) NOT NULL,
  `harga_per_kg` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_sampah`
--

INSERT INTO `jenis_sampah` (`id`, `nama_jenis`, `harga_per_kg`) VALUES
(1, 'plastik', 500.00),
(2, 'besi', 1000.00),
(3, 'kuningan', 2000.00),
(4, 'kawat', 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang_sampah`
--

CREATE TABLE `keranjang_sampah` (
  `id` int(11) NOT NULL,
  `id_rumah_tangga` int(11) DEFAULT NULL,
  `id_jenis_sampah` int(11) DEFAULT NULL,
  `berat` decimal(10,2) DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengelola_sampah`
--

CREATE TABLE `pengelola_sampah` (
  `id` int(11) NOT NULL,
  `nama_pengelola` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'pengelola',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengelola_sampah`
--

INSERT INTO `pengelola_sampah` (`id`, `nama_pengelola`, `kontak`, `email`, `role`, `username`, `password`, `verification_code`, `is_verified`) VALUES
(1, 'ud kita sejati', '01-38192', '', 'pengelola', 'udsejati', '12345', NULL, 0),
(2, 'jiohan', '14154', 'nohemi9596@esterace.com', 'pengelola', 'johan', '123', '122851', 1);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_penarikan`
--

CREATE TABLE `riwayat_penarikan` (
  `id` int(11) NOT NULL,
  `id_warung_mitra` int(11) DEFAULT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `tipe_penarikan` enum('cash','cashless') DEFAULT NULL,
  `status` enum('pending','selesai') DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_penarikan`
--

INSERT INTO `riwayat_penarikan` (`id`, `id_warung_mitra`, `jumlah`, `tipe_penarikan`, `status`, `no_rekening`, `bank`, `tanggal`) VALUES
(1, 5, 2000.00, 'cashless', 'selesai', '1412535', 'bca', '2024-09-14 07:07:57'),
(2, 5, 1000.00, 'cashless', 'selesai', '1412535', 'bca', '2024-09-14 07:09:57'),
(3, 4, 500000.00, 'cash', 'selesai', '431241', 'bca', '2024-09-14 07:13:35'),
(4, 4, 100000.00, 'cash', 'selesai', '431241', 'bca', '2024-09-14 07:15:41'),
(5, 5, 10000.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:03:59'),
(6, 5, 5000.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:06:14'),
(7, 5, 2000.00, 'cashless', 'selesai', '1412535', 'bca', '2024-09-14 13:10:00'),
(8, 5, 1000.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:11:37'),
(9, 5, 200.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:13:37'),
(10, 5, 111.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:23:28'),
(11, 5, 3324.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:24:21'),
(12, 5, 434.00, 'cash', 'selesai', '1412535', 'bca', '2024-09-14 13:25:25');

-- --------------------------------------------------------

--
-- Table structure for table `rumah_tangga`
--

CREATE TABLE `rumah_tangga` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kontak` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'rumah_tangga',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `saldo` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rumah_tangga`
--

INSERT INTO `rumah_tangga` (`id`, `nama`, `alamat`, `kontak`, `email`, `role`, `username`, `password`, `verification_code`, `is_verified`, `saldo`) VALUES
(1, 'koi', 'koi 25 lt', NULL, 'koi@gmail.com', 'rumah_tangga', 'piok', '12345', NULL, 1, 52129.00),
(2, 'kois', 'kois lt5', NULL, 'kois@gmail.com', 'rumah_tangga', 'kois23', '12345', '123451', 0, 94000.00),
(3, 'jiji', 'jiji 24 t', NULL, 'jiji@gmail.com', 'rumah_tangga', 'jiji', '12345', '123456', 0, 23000.00),
(56, 'asdadad', 'asdasd', NULL, 'adas@sdsda.ca', 'rumah_tangga', 'asdads', '123', '517493', 0, 0.00),
(60, 'asd', 'asd', '124124', 'nohemi9596@esterace.com', 'rumah_tangga', '1234124', '123', '640373', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id` int(11) NOT NULL,
  `id_rumah_tangga` int(11) DEFAULT NULL,
  `berat` decimal(10,2) DEFAULT NULL,
  `status` enum('menunggu_pickup','selesai') DEFAULT 'menunggu_pickup',
  `total_harga` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_jenis_sampah` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`id`, `id_rumah_tangga`, `berat`, `status`, `total_harga`, `created_at`, `id_jenis_sampah`) VALUES
(15, 1, 4.00, 'selesai', 4000.00, '2024-09-10 18:21:33', 2),
(16, 2, 2.00, 'selesai', 2000.00, '2024-09-10 18:23:29', 2),
(17, 2, 5.00, 'selesai', 2500.00, '2024-09-10 18:28:19', 1),
(23, 2, 7.00, 'selesai', 14000.00, '2024-09-10 18:56:27', 3),
(24, 2, 7.00, 'selesai', 3500.00, '2024-09-10 19:01:37', 1),
(25, 2, 4.00, 'selesai', 8000.00, '2024-09-10 19:47:21', 3),
(26, 3, 66.00, 'selesai', 66000.00, '2024-09-10 19:48:47', 2),
(27, 3, 6.00, 'selesai', 3000.00, '2024-09-10 20:34:24', 1),
(28, 3, 9.00, 'menunggu_pickup', 4500.00, '2024-09-10 20:34:31', 1),
(30, 60, 2.00, 'menunggu_pickup', 4000.00, '2024-09-12 17:19:07', 3),
(31, 2, 3.00, 'selesai', 1000.00, '2024-09-13 12:48:01', 1),
(32, 2, 1.00, 'selesai', 0.00, '2024-09-13 14:13:38', 1),
(33, 2, 6.00, 'selesai', 12000.00, '2024-09-13 14:48:10', 3);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_rumah_tangga` int(11) DEFAULT NULL,
  `id_warung_mitra` int(11) DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_rumah_tangga`, `id_warung_mitra`, `jumlah_pembayaran`, `tanggal`) VALUES
(1, 2, 1, 10000.00, '2024-09-10 19:23:50'),
(2, 2, 1, 10000.00, '2024-09-10 19:24:27'),
(3, 2, 1, 1000.00, '2024-09-10 19:24:53'),
(4, 1, 1, 5000.00, '2024-09-10 19:39:49'),
(5, 1, 1, 111.00, '2024-09-10 19:40:14'),
(6, 1, 1, 5000.00, '2024-09-10 19:40:31'),
(7, 1, 1, 60.00, '2024-09-10 19:41:58'),
(8, 3, 1, 6000.00, '2024-09-10 19:49:08'),
(9, 3, 3, 2000.00, '2024-09-10 20:09:06'),
(10, 3, 1, 30000.00, '2024-09-10 20:32:01'),
(11, 3, 1, 5000.00, '2024-09-10 20:35:16'),
(12, 3, 3, 3000.00, '2024-09-10 20:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pencairan`
--

CREATE TABLE `transaksi_pencairan` (
  `id` int(11) NOT NULL,
  `id_warung_mitra` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tipe_penarikan` enum('cash','cashless') NOT NULL,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `tanggal` datetime DEFAULT current_timestamp(),
  `no_rekening` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_pencairan`
--

INSERT INTO `transaksi_pencairan` (`id`, `id_warung_mitra`, `jumlah`, `tipe_penarikan`, `status`, `tanggal`, `no_rekening`, `bank`) VALUES
(1, 5, 5000.00, 'cashless', 'selesai', '2024-09-14 13:45:28', NULL, NULL),
(2, 5, 5000.00, 'cashless', 'selesai', '2024-09-14 13:45:39', NULL, NULL),
(3, 5, 5000.00, 'cash', 'selesai', '2024-09-14 14:00:57', '1412535', 'bca'),
(4, 5, 9000.00, 'cash', 'selesai', '2024-09-14 14:04:01', '1412535', 'bca');

-- --------------------------------------------------------

--
-- Table structure for table `warung_mitra`
--

CREATE TABLE `warung_mitra` (
  `id` int(11) NOT NULL,
  `nama_warung` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_rekening` varchar(100) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'warung_mitra',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warung_mitra`
--

INSERT INTO `warung_mitra` (`id`, `nama_warung`, `alamat`, `kontak`, `email`, `no_rekening`, `bank`, `role`, `username`, `password`, `verification_code`, `is_verified`, `saldo`) VALUES
(1, 'warseb', 'jln putren', '781327', NULL, NULL, NULL, 'warung_mitra', 'warungkita', '12345', NULL, 0, 37000.00),
(3, 'warjo', 'warjo 23 lt', '23124', NULL, NULL, NULL, 'warung_mitra', 'warjo', '12345', NULL, 0, 5000.00),
(4, 'alfa', 'hikafha', '142515', 'nohemi9596@esterace.com', '431241', 'bca', 'warung_mitra', 'alfa', '123', '840346', 1, 400000.00),
(5, 'kamoke', 'kamoke kos 23', '2314515', 'kamoke8682@asaud.com', '1412535', 'bca', 'warung_mitra', 'kamoke', '123', '294388', 1, 55931.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keranjang_sampah`
--
ALTER TABLE `keranjang_sampah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rumah_tangga` (`id_rumah_tangga`),
  ADD KEY `id_jenis_sampah` (`id_jenis_sampah`);

--
-- Indexes for table `pengelola_sampah`
--
ALTER TABLE `pengelola_sampah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `riwayat_penarikan`
--
ALTER TABLE `riwayat_penarikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_warung_mitra` (`id_warung_mitra`);

--
-- Indexes for table `rumah_tangga`
--
ALTER TABLE `rumah_tangga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `sampah`
--
ALTER TABLE `sampah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rumah_tangga` (`id_rumah_tangga`),
  ADD KEY `fk_jenis_sampah` (`id_jenis_sampah`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rumah_tangga` (`id_rumah_tangga`),
  ADD KEY `id_warung_mitra` (`id_warung_mitra`);

--
-- Indexes for table `transaksi_pencairan`
--
ALTER TABLE `transaksi_pencairan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_warung_mitra` (`id_warung_mitra`);

--
-- Indexes for table `warung_mitra`
--
ALTER TABLE `warung_mitra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang_sampah`
--
ALTER TABLE `keranjang_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengelola_sampah`
--
ALTER TABLE `pengelola_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `riwayat_penarikan`
--
ALTER TABLE `riwayat_penarikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rumah_tangga`
--
ALTER TABLE `rumah_tangga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaksi_pencairan`
--
ALTER TABLE `transaksi_pencairan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `warung_mitra`
--
ALTER TABLE `warung_mitra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang_sampah`
--
ALTER TABLE `keranjang_sampah`
  ADD CONSTRAINT `keranjang_sampah_ibfk_1` FOREIGN KEY (`id_rumah_tangga`) REFERENCES `rumah_tangga` (`id`),
  ADD CONSTRAINT `keranjang_sampah_ibfk_2` FOREIGN KEY (`id_jenis_sampah`) REFERENCES `jenis_sampah` (`id`);

--
-- Constraints for table `riwayat_penarikan`
--
ALTER TABLE `riwayat_penarikan`
  ADD CONSTRAINT `riwayat_penarikan_ibfk_1` FOREIGN KEY (`id_warung_mitra`) REFERENCES `warung_mitra` (`id`);

--
-- Constraints for table `sampah`
--
ALTER TABLE `sampah`
  ADD CONSTRAINT `fk_jenis_sampah` FOREIGN KEY (`id_jenis_sampah`) REFERENCES `jenis_sampah` (`id`),
  ADD CONSTRAINT `sampah_ibfk_1` FOREIGN KEY (`id_rumah_tangga`) REFERENCES `rumah_tangga` (`id`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_rumah_tangga`) REFERENCES `rumah_tangga` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_warung_mitra`) REFERENCES `warung_mitra` (`id`);

--
-- Constraints for table `transaksi_pencairan`
--
ALTER TABLE `transaksi_pencairan`
  ADD CONSTRAINT `transaksi_pencairan_ibfk_1` FOREIGN KEY (`id_warung_mitra`) REFERENCES `warung_mitra` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
