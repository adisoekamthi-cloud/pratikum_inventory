-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 11:30 AM
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
-- Database: `pratikum`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `status_id` varchar(50) DEFAULT NULL,
  `penyimpanan_id` varchar(100) DEFAULT NULL,
  `harga_barang` int(11) DEFAULT NULL,
  `stok` int(11) NOT NULL,
  `limit_stok` int(11) NOT NULL DEFAULT 5,
  `vendor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `status_id`, `penyimpanan_id`, `harga_barang`, `stok`, `limit_stok`, `vendor_id`) VALUES
(8, 'Mouse', '1', '1', 150000, 11, 5, 2),
(9, 'Monitor', '1', '5', 200000, 20, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `distribusi`
--

CREATE TABLE `distribusi` (
  `id_distribusi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jenis_distribusi` enum('Masuk','Keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_distribusi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distribusi`
--

INSERT INTO `distribusi` (`id_distribusi`, `id_barang`, `jenis_distribusi`, `jumlah`, `tanggal_distribusi`, `keterangan`) VALUES
(0, 3, 'Masuk', 1, '2026-06-19 09:12:00', 'kirim ke cabang c'),
(0, 3, 'Masuk', 1, '2026-06-19 09:12:00', 'kirim ke cabang c'),
(0, 8, 'Keluar', 5, '2026-06-21 03:22:00', 'Kirim Cabang B');

-- --------------------------------------------------------

--
-- Table structure for table `penyimpanan`
--

CREATE TABLE `penyimpanan` (
  `id` int(11) NOT NULL,
  `nama_penyimpanan` varchar(100) NOT NULL,
  `lokasi` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyimpanan`
--

INSERT INTO `penyimpanan` (`id`, `nama_penyimpanan`, `lokasi`) VALUES
(1, 'Gedung A', ''),
(3, 'Gedung C', 'SEBELAH SELATAN'),
(4, 'Gedung B', 'SEBELAH SELATAN'),
(5, 'Box A', 'Lantai 1');

-- --------------------------------------------------------

--
-- Table structure for table `status_barang`
--

CREATE TABLE `status_barang` (
  `id` int(11) NOT NULL,
  `nama_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_barang`
--

INSERT INTO `status_barang` (`id`, `nama_status`) VALUES
(1, 'Tersedia'),
(2, 'Kosong'),
(3, 'Restok');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('admin','pengguna') NOT NULL DEFAULT 'pengguna',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `foto`, `role`, `created_at`) VALUES
(2, 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'Pengguna Umum', NULL, NULL, 'pengguna', '2026-06-21 07:56:04'),
(9, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', NULL, 'profile_9_1782028430.png', 'admin', '2026-05-01 07:56:04'),
(10, 'user1', '24c9e15e52afc47c225b757e7bee1f9d', 'user1 tes', NULL, NULL, 'pengguna', '2026-06-21 08:05:35');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id_vendor` int(11) NOT NULL,
  `nama_vendor` varchar(100) NOT NULL,
  `kontak_vendor` varchar(50) NOT NULL,
  `alamat_vendor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id_vendor`, `nama_vendor`, `kontak_vendor`, `alamat_vendor`) VALUES
(2, 'PT Sinar Abadi', '09812345678', 'Jl perintis no 123'),
(3, 'CV. Maju Jaya', '08123456789', 'Jl Maju jaya no.30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penyimpanan`
--
ALTER TABLE `penyimpanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_barang`
--
ALTER TABLE `status_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id_vendor`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `penyimpanan`
--
ALTER TABLE `penyimpanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `status_barang`
--
ALTER TABLE `status_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id_vendor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
