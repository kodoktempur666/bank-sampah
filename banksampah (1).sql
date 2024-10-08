-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 04:53 PM
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
(4, 'kawat', 1000.00),
(5, 'botol', 1000.00);

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
  `status` enum('pending','selesai') DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_penarikan`
--

INSERT INTO `riwayat_penarikan` (`id`, `id_warung_mitra`, `jumlah`, `status`, `tanggal`) VALUES
(1, 5, 2000.00, 'selesai', '2024-09-14 07:07:57'),
(2, 5, 1000.00, 'selesai', '2024-09-14 07:09:57'),
(3, 4, 500000.00, 'selesai', '2024-09-14 07:13:35'),
(4, 4, 100000.00, 'selesai', '2024-09-14 07:15:41'),
(5, 5, 10000.00, 'selesai', '2024-09-14 13:03:59'),
(6, 5, 5000.00, 'selesai', '2024-09-14 13:06:14'),
(7, 5, 2000.00, 'selesai', '2024-09-14 13:10:00'),
(8, 5, 1000.00, 'selesai', '2024-09-14 13:11:37'),
(9, 5, 200.00, 'selesai', '2024-09-14 13:13:37'),
(10, 5, 111.00, 'selesai', '2024-09-14 13:23:28'),
(11, 5, 3324.00, 'selesai', '2024-09-14 13:24:21'),
(12, 5, 434.00, 'selesai', '2024-09-14 13:25:25'),
(13, 4, 10000.00, 'selesai', '2024-09-21 03:46:06'),
(14, 4, 10000.00, 'selesai', '2024-09-24 13:38:48'),
(15, 4, 6000.00, 'selesai', '2024-10-07 13:42:19'),
(16, 4, 5000.00, 'selesai', '2024-10-07 13:42:24');

-- --------------------------------------------------------

--
-- Table structure for table `rumah_tangga`
--

CREATE TABLE `rumah_tangga` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kontak` varchar(15) DEFAULT NULL,
  `rw` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'rumah_tangga',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `saldo` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rumah_tangga`
--

INSERT INTO `rumah_tangga` (`id`, `nama`, `alamat`, `kontak`, `rw`, `role`, `username`, `password`, `is_verified`, `saldo`) VALUES
(1, 'koi', 'RT 25 RW 2', '21451245', 4, 'rumah_tangga', 'piok', '12345', 1, 63845.00),
(2, 'kois', 'jepara ', '214124', 2, 'rumah_tangga', 'kois23', '12345', 1, 94000.00),
(3, 'ahmad', 'karangkosa 25 no.14', '31245235', 4, 'rumah_tangga', 'jiji', '12345', 1, -13500.00),
(60, 'asd', 'asd', '124124', 0, 'rumah_tangga', '1234124', '123', 1, 4000.00),
(61, 'jonatan', 'jalan puteran berkoh ', '4294425', 1, 'rumah_tangga', 'john24', '123', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id` int(11) NOT NULL,
  `id_rumah_tangga` int(11) DEFAULT NULL,
  `berat` decimal(10,2) DEFAULT NULL,
  `status` enum('siap hitung','selesai') DEFAULT 'siap hitung',
  `total_harga` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_jenis_sampah` int(11) DEFAULT NULL,
  `confirmed_by_pengelola` enum('belum diterima','diterima') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'belum diterima',
  `confirmed_by_rumah_tangga` enum('belum diterima','diterima') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'belum diterima'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`id`, `id_rumah_tangga`, `berat`, `status`, `total_harga`, `created_at`, `id_jenis_sampah`, `confirmed_by_pengelola`, `confirmed_by_rumah_tangga`) VALUES
(15, 1, 4.00, 'selesai', 4000.00, '2024-09-10 18:21:33', 2, '', ''),
(16, 2, 2.00, 'selesai', 2000.00, '2024-09-10 18:23:29', 2, '', ''),
(17, 2, 5.00, 'selesai', 2500.00, '2024-09-10 18:28:19', 1, '', ''),
(23, 2, 7.00, 'selesai', 14000.00, '2024-09-10 18:56:27', 3, '', ''),
(24, 2, 7.00, 'selesai', 3500.00, '2024-09-10 19:01:37', 1, '', ''),
(25, 2, 4.00, 'selesai', 8000.00, '2024-09-10 19:47:21', 3, '', ''),
(26, 3, 66.00, 'selesai', 66000.00, '2024-09-10 19:48:47', 2, '', ''),
(27, 3, 6.00, 'selesai', 3000.00, '2024-09-10 20:34:24', 1, '', ''),
(28, 3, 9.00, 'selesai', 4500.00, '2024-09-10 20:34:31', 1, '', ''),
(30, 60, 2.00, 'selesai', 4000.00, '2024-09-12 17:19:07', 3, '', ''),
(31, 2, 3.00, 'selesai', 1000.00, '2024-09-13 12:48:01', 1, '', ''),
(32, 2, 1.00, 'selesai', 0.00, '2024-09-13 14:13:38', 1, '', ''),
(33, 2, 6.00, 'selesai', 12000.00, '2024-09-13 14:48:10', 3, '', ''),
(34, 1, 0.00, 'selesai', 0.00, '2024-09-21 03:35:23', 4, '', ''),
(35, 1, 0.00, 'selesai', 0.00, '2024-09-21 03:47:10', 1, '', ''),
(37, 1, 2.00, 'selesai', 1000.00, '2024-09-24 12:49:22', 1, '', ''),
(38, 2, 0.00, 'selesai', 0.00, '2024-09-24 13:25:16', 4, '', ''),
(39, 1, 5.00, 'selesai', 2500.00, '2024-09-26 06:29:04', 1, '', ''),
(40, 1, 700.00, '', 350000.00, '2024-10-01 14:35:03', 1, 'diterima', ''),
(41, 1, 6.00, '', 3000.00, '2024-10-01 15:14:58', 1, 'diterima', 'belum diterima'),
(42, 1, 0.00, '', 0.00, '2024-10-01 15:17:38', 1, 'diterima', 'diterima'),
(44, 1, 6.00, 'selesai', 3000.00, '2024-10-01 16:06:42', 1, 'diterima', 'diterima'),
(45, 1, 7.00, 'selesai', 3500.00, '2024-10-01 16:08:39', 1, 'diterima', 'diterima'),
(46, 1, 44.00, 'selesai', 44000.00, '2024-10-01 16:12:49', 5, 'diterima', 'diterima'),
(47, 1, 7.00, 'selesai', 14000.00, '2024-10-01 16:14:58', 3, 'diterima', 'diterima');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_rumah_tangga` int(11) DEFAULT NULL,
  `id_warung_mitra` int(11) DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `keterangan` varchar(255) NOT NULL,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_rumah_tangga`, `id_warung_mitra`, `jumlah_pembayaran`, `keterangan`, `status`, `tanggal`) VALUES
(1, 2, 1, 10000.00, '', 'selesai', '2024-09-10 19:23:50'),
(2, 2, 1, 10000.00, '', 'selesai', '2024-09-10 19:24:27'),
(3, 2, 1, 1000.00, '', 'selesai', '2024-09-10 19:24:53'),
(4, 1, 1, 5000.00, '', 'selesai', '2024-09-10 19:39:49'),
(5, 1, 1, 111.00, '', 'selesai', '2024-09-10 19:40:14'),
(6, 1, 1, 5000.00, '', 'selesai', '2024-09-10 19:40:31'),
(7, 1, 1, 60.00, '', 'selesai', '2024-09-10 19:41:58'),
(8, 3, 1, 6000.00, '', 'selesai', '2024-09-10 19:49:08'),
(9, 3, 3, 2000.00, '', 'pending', '2024-09-10 20:09:06'),
(10, 3, 1, 30000.00, '', 'selesai', '2024-09-10 20:32:01'),
(11, 3, 1, 5000.00, '', 'selesai', '2024-09-10 20:35:16'),
(12, 3, 3, 3000.00, '', 'pending', '2024-09-10 20:35:38'),
(13, 1, 1, 1110.00, '', 'selesai', '2024-09-24 13:00:45'),
(14, 1, 1, 500.00, '', 'selesai', '2024-09-24 13:02:15'),
(15, 1, 1, 1000.00, '', 'selesai', '2024-09-24 13:04:49'),
(16, 1, 1, 1000.00, '', 'selesai', '2024-09-24 13:05:30'),
(17, 1, 1, 2.00, '', 'selesai', '2024-09-24 13:13:57'),
(18, 1, 5, 2000.00, '', 'pending', '2024-10-07 13:59:53'),
(19, 1, 4, 2000.00, 'rokok', 'selesai', '2024-10-07 14:05:49'),
(20, 1, 1, 4000.00, 'rokok', 'selesai', '2024-10-07 14:24:53'),
(21, 1, 1, 5000.00, 'sabun', 'selesai', '2024-10-07 14:34:13'),
(22, 1, 1, 2000.00, 'mie', 'selesai', '2024-10-07 14:34:52'),
(23, 1, 1, 1000.00, 'kok', 'pending', '2024-10-07 14:35:57'),
(24, 1, 1, 6000.00, 'indomie', 'selesai', '2024-10-07 14:36:11'),
(25, 1, 4, 7000.00, 'mie yam', 'selesai', '2024-10-07 15:31:45'),
(28, 1, 4, 7000.00, 'kentang', 'selesai', '2024-10-07 15:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pencairan`
--

CREATE TABLE `transaksi_pencairan` (
  `id` int(11) NOT NULL,
  `id_warung_mitra` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_pencairan`
--

INSERT INTO `transaksi_pencairan` (`id`, `id_warung_mitra`, `jumlah`, `status`, `tanggal`) VALUES
(1, 5, 5000.00, 'selesai', '2024-09-14 13:45:28'),
(2, 5, 5000.00, 'selesai', '2024-09-14 13:45:39'),
(3, 5, 5000.00, 'selesai', '2024-09-14 14:00:57'),
(4, 5, 9000.00, 'selesai', '2024-09-14 14:04:01'),
(19, 4, 10000.00, 'selesai', '2024-10-07 20:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `warung_mitra`
--

CREATE TABLE `warung_mitra` (
  `id` int(11) NOT NULL,
  `nama_warung` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'warung_mitra',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warung_mitra`
--

INSERT INTO `warung_mitra` (`id`, `nama_warung`, `alamat`, `kontak`, `role`, `username`, `password`, `is_verified`, `saldo`) VALUES
(1, 'warseb2', 'jln putren', '781327', 'warung_mitra', 'warungkita', '12345', 0, 103284.00),
(3, 'warjo', 'warjo 23 lt', '23124', 'warung_mitra', 'warjo', '12345', 0, 5000.00),
(4, 'alfa', 'hikafha', '142515', 'warung_mitra', 'alfa', '123', 1, 369000.00),
(5, 'kamoke', 'kamoke kos 23', '2314515', 'warung_mitra', 'kamoke', '123', 1, 57931.00),
(6, 'alfamidi', 'kedungwuluh rt 2 rw 1', '1241452151', 'warung_mitra', 'midi123', '123', 0, 0.00);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengelola_sampah`
--
ALTER TABLE `pengelola_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `riwayat_penarikan`
--
ALTER TABLE `riwayat_penarikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `rumah_tangga`
--
ALTER TABLE `rumah_tangga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `transaksi_pencairan`
--
ALTER TABLE `transaksi_pencairan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `warung_mitra`
--
ALTER TABLE `warung_mitra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

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
