-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: pratikum
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `barang`
--

DROP TABLE IF EXISTS `barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `status_id` varchar(50) DEFAULT NULL,
  `penyimpanan_id` varchar(100) DEFAULT NULL,
  `harga_barang` int(11) DEFAULT NULL,
  `stok` int(11) NOT NULL,
  `limit_stok` int(11) NOT NULL DEFAULT 5,
  `vendor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang`
--

LOCK TABLES `barang` WRITE;
/*!40000 ALTER TABLE `barang` DISABLE KEYS */;
INSERT INTO `barang` VALUES (1,'Mouse Wireless Logitech M185','1','2',160000,35,8,5),(2,'Keyboard Gaming Mechanical ROG','1','2',450000,15,5,3),(3,'Monitor LED ASUS 24 Inch','1','1',1850000,8,3,3),(4,'RAM Corsair Vengeance DDR4 16GB','1','4',950000,20,5,6),(5,'SSD NVMe Samsung 980 1TB','1','4',1250000,12,4,1),(6,'Power Supply Corsair CV650','1','3',850000,4,5,6),(7,'Motherboard ASUS ROG B550','1','3',2450000,6,3,3),(8,'Headset Gaming Lenovo Legion','1','2',650000,10,3,4),(9,'Laptop ASUS ROG Zephyrus','1','1',21500000,3,2,3),(10,'Flashdisk Sandisk Cruzer 64GB','1','2',85000,45,10,2);
/*!40000 ALTER TABLE `barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `distribusi`
--

DROP TABLE IF EXISTS `distribusi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribusi` (
  `id_distribusi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jenis_distribusi` enum('Masuk','Keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_distribusi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribusi`
--

LOCK TABLES `distribusi` WRITE;
/*!40000 ALTER TABLE `distribusi` DISABLE KEYS */;
INSERT INTO `distribusi` VALUES (0,1,'Masuk',10,'2026-06-20 02:00:00','Stok Awal Supplier Corsair'),(0,2,'Masuk',5,'2026-06-20 03:30:00','Stok Awal Supplier Asus'),(0,1,'Keluar',2,'2026-06-21 07:15:00','Kirim ke Cabang Depok');
/*!40000 ALTER TABLE `distribusi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penyimpanan`
--

DROP TABLE IF EXISTS `penyimpanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penyimpanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_penyimpanan` varchar(100) NOT NULL,
  `lokasi` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penyimpanan`
--

LOCK TABLES `penyimpanan` WRITE;
/*!40000 ALTER TABLE `penyimpanan` DISABLE KEYS */;
INSERT INTO `penyimpanan` VALUES (1,'Gedung Utama (A)','Blok A Lantai 1'),(2,'Gedung Aksesoris (B)','Blok B Lantai 2'),(3,'Rak Komponen (C)','Blok C Lantai 1'),(4,'Loker SSD & RAM (D)','Blok D Lantai 2');
/*!40000 ALTER TABLE `penyimpanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_barang`
--

DROP TABLE IF EXISTS `status_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_barang`
--

LOCK TABLES `status_barang` WRITE;
/*!40000 ALTER TABLE `status_barang` DISABLE KEYS */;
INSERT INTO `status_barang` VALUES (1,'Tersedia'),(2,'Kosong'),(3,'Restok');
/*!40000 ALTER TABLE `status_barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('admin','pengguna') NOT NULL DEFAULT 'pengguna',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'user','ee11cbb19052e40b07aac0ca060c23ee','Pengguna Umum',NULL,NULL,'pengguna','2026-06-21 07:56:04'),(9,'admin','21232f297a57a5a743894a0e4a801fc3','admin',NULL,'profile_9_1782028430.png','admin','2026-05-01 07:56:04'),(10,'user1','24c9e15e52afc47c225b757e7bee1f9d','user1 tes',NULL,NULL,'pengguna','2026-06-21 08:05:35');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id_vendor` int(11) NOT NULL AUTO_INCREMENT,
  `nama_vendor` varchar(100) NOT NULL,
  `kontak_vendor` varchar(50) NOT NULL,
  `alamat_vendor` text NOT NULL,
  PRIMARY KEY (`id_vendor`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,'PT. Sinar Abadi','021-5551234','Jl. Perintis No. 123, Jakarta'),(2,'CV. Computindo Sukses','0812-3456-789','Jl. Maju Jaya No. 30, Bandung'),(3,'PT. Asus Indonesia','021-8884432','Kawasan Industri Sunter, Jakarta'),(4,'PT. Lenovo Technology','021-7773321','Sudirman Central Business District, Jakarta'),(5,'PT. Logi Logitech Indonesia','0821-9988-7766','Ruko Mangga Dua Square, Jakarta'),(6,'PT. Corsair Tech','0813-8877-6655','Gedung Cyber, Kuningan, Jakarta');
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-21 17:10:17
