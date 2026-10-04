-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: klinik_gudang_
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `klinik_gudang_`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `klinik_gudang_` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `klinik_gudang_`;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'add_poli','Super Admin menambahkan 5 Master Poli baru (KIA, Mata, THT, Penyakit Dalam, Fisioterapi)','2026-10-04 18:09:37'),(2,1,'add_category','Super Admin menambahkan 5 Master Kategori inventaris baru','2026-10-04 18:23:24'),(3,1,'create_user','Super Admin mendaftarkan akun petugas Bidan Ratna Sari (Poli KIA)','2026-10-04 18:25:01'),(4,2,'update_stock','Admin Poli Gigi memperbarui batas minimum dan stok Dental Needle','2026-10-04 18:25:01'),(5,3,'take_item','Perawat Poli Umum mengambil 2 box Antasida untuk darurat pasien pagi','2026-10-04 18:25:01'),(6,3,'login','User logged in','2026-10-04 18:26:31'),(7,1,'login','User logged in','2026-10-04 18:30:21'),(8,4,'login','User logged in','2026-10-04 18:30:21'),(9,3,'login','User logged in','2026-10-04 18:30:21'),(10,1,'login','User logged in','2026-10-04 18:31:00'),(11,4,'login','User logged in','2026-10-04 18:31:00'),(12,3,'login','User logged in','2026-10-04 18:31:00');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Obat','Kategori untuk jenis-jenis obatobatan','2026-10-04 18:07:47','2026-10-04 18:07:47'),(2,'Alkes','Kategori untuk alat kesehatan','2026-10-04 18:07:47','2026-10-04 18:07:47'),(3,'Habis Pakai','Bahan habis pakai medis','2026-10-04 18:07:47','2026-10-04 18:07:47'),(4,'Reagen & Lab','Cairan reagen, strip tes gula/kolesterol/asam urat, dan perlengkapan uji lab sederhana','2026-10-04 18:29:27','2026-10-04 18:29:27'),(5,'Desinfektan & Sanitasi','Cairan antiseptik, alkohol 70%, hand sanitizer, dan desinfektan permukaan steril','2026-10-04 18:29:27','2026-10-04 18:29:27'),(6,'Vitamin & Suplemen','Suplemen multivitamin, vitamin C dosis tinggi, zinc, dan suplemen daya tahan tubuh','2026-10-04 18:29:27','2026-10-04 18:29:27'),(7,'Bahan Bedah Minor','Benang jahit bedah (silk/chromic), bisturi pisau bedah steril, kassa steril, dan plester fiksasi','2026-10-04 18:29:27','2026-10-04 18:29:27'),(8,'Peralatan Diagnostik','Alat diagnosa vital seperti tensimeter digital, stetoskop, termometer inframerah, dan pulse oximeter','2026-10-04 18:29:27','2026-10-04 18:29:27');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poli_id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `sku` varchar(30) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `ambang_minimum` int NOT NULL DEFAULT '15',
  `satuan` varchar(20) NOT NULL,
  `status` enum('available','low') NOT NULL DEFAULT 'available',
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_poli` (`sku`,`poli_id`),
  KEY `idx_poli_status` (`poli_id`,`status`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,1,'Paracetamol 500mg Kaplet','UM-OB-001','Obat',45,10,'box','available','Stok utama obat antipiretik dan analgesik','2026-10-04 18:29:27','2026-10-04 18:29:27'),(2,1,'Amoxicillin 500mg Kapsul','UM-OB-002','Obat',28,10,'box','available','Antibiotik spektrum luas resep dokter','2026-10-04 18:29:27','2026-10-04 18:29:27'),(3,1,'Antasida Doen Tablet Kunyah','UM-OB-003','Obat',6,15,'box','low','Stok menipis, segera ajukan pengadaan baru','2026-10-04 18:29:27','2026-10-04 18:29:27'),(4,1,'Kassa Hidrofil Steril 16x16','UM-HP-001','Habis Pakai',50,15,'pack','available','Untuk perawatan luka bersih dan balut','2026-10-04 18:29:27','2026-10-04 18:29:27'),(5,1,'Tensimeter Digital Omron HEM-7120','UM-AL-001','Peralatan Diagnostik',4,2,'unit','available','Alat ukur tekanan darah digital ruang periksa','2026-10-04 18:29:27','2026-10-04 18:29:27'),(6,2,'Dental Needle 27G Short Terumo','GG-AL-001','Alkes',120,30,'pcs','available','Jarum anestesi lokal pencabutan gigi','2026-10-04 18:29:27','2026-10-04 18:29:27'),(7,2,'Eugenol Liquid 10ml Pure','GG-OB-001','Obat',5,2,'botol','available','Campuran zinc oxide untuk tambal sementara','2026-10-04 18:29:27','2026-10-04 18:29:27'),(8,2,'Cotton Roll Dental Absorben','GG-HP-001','Habis Pakai',8,10,'box','low','Gulungan kapas isolasi saliva area kerja gigi','2026-10-04 18:29:27','2026-10-04 18:29:27'),(9,2,'Kaca Mulut No. 4 Handle Stainless','GG-AL-002','Alkes',25,5,'pcs','available','Peralatan diagnostik intraoral','2026-10-04 18:29:27','2026-10-04 18:29:27'),(10,2,'Pasta Profilaksis Fluoride 50g','GG-OB-002','Obat',7,3,'tube','available','Pasta pembersih karang gigi (scaling)','2026-10-04 18:29:27','2026-10-04 18:29:27'),(11,3,'Asam Folat 400mcg Strip','KIA-OB-001','Vitamin & Suplemen',35,10,'strip','available','Suplemen pra-nikah dan nutrisi awal kehamilan','2026-10-04 18:29:27','2026-10-04 18:29:27'),(12,3,'Fetal Doppler Heart Rate LCD','KIA-AL-001','Peralatan Diagnostik',3,1,'unit','available','Monitor detak jantung janin portabel','2026-10-04 18:29:27','2026-10-04 18:29:27'),(13,4,'Cendo Lyteers Tetes Mata 15ml','MT-OB-001','Obat',18,5,'botol','available','Obat tetes mata kering dan iritasi debu','2026-10-04 18:29:27','2026-10-04 18:29:27'),(14,4,'Snellen Chart Huruf Lampu LED','MT-AL-001','Peralatan Diagnostik',2,1,'unit','available','Papan tes tajam penglihatan visus mata','2026-10-04 18:29:27','2026-10-04 18:29:27'),(15,5,'Ear Speculum Metal Set 3 Size','THT-AL-001','Alkes',12,3,'set','available','Spekulum telinga pemeriksaan liang telinga','2026-10-04 18:29:27','2026-10-04 18:29:27');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polis`
--

DROP TABLE IF EXISTS `polis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polis`
--

LOCK TABLES `polis` WRITE;
/*!40000 ALTER TABLE `polis` DISABLE KEYS */;
INSERT INTO `polis` VALUES (1,'Poli Umum','Layanan kesehatan umum','2026-10-04 18:07:47','2026-10-04 18:07:47'),(2,'Poli Gigi','Layanan kesehatan gigi dan mulut','2026-10-04 18:07:47','2026-10-04 18:07:47'),(3,'Poli KIA & Kebidanan','Layanan kesehatan ibu, anak, imunisasi, dan kesehatan reproduksi','2026-10-04 18:29:27','2026-10-04 18:29:27'),(4,'Poli Mata','Layanan pemeriksaan kesehatan mata, refraksi, dan tajam penglihatan','2026-10-04 18:29:27','2026-10-04 18:29:27'),(5,'Poli THT','Layanan kesehatan telinga, hidung, dan tenggorokan serta ekstraksi serumen','2026-10-04 18:29:27','2026-10-04 18:29:27'),(6,'Poli Penyakit Dalam','Layanan spesialis organ dalam dan manajemen penyakit metabolik/kronis','2026-10-04 18:29:27','2026-10-04 18:29:27'),(7,'Poli Fisioterapi','Layanan rehabilitasi fisik, terapi cedera olahraga, dan pemulihan neuromuskular','2026-10-04 18:29:27','2026-10-04 18:29:27');
/*!40000 ALTER TABLE `polis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usage_logs`
--

DROP TABLE IF EXISTS `usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usage_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `user_id` int NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `usage_logs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usage_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usage_logs`
--

LOCK TABLES `usage_logs` WRITE;
/*!40000 ALTER TABLE `usage_logs` DISABLE KEYS */;
INSERT INTO `usage_logs` VALUES (1,1,3,3,'Distribusi obat ruang tindakan umum','Pemberian untuk 3 pasien demam ruang tunggu','2026-10-02 18:29:27'),(2,4,3,5,'Perawatan luka lecet kecelakaan mahasiswa','Pasien mahasiswa cidera saat praktikum lapangan','2026-10-03 18:29:27'),(3,6,2,8,'Tindakan ekstraksi gigi geraham','Pencabutan gigi molar mahasiswa semester 5','2026-10-04 00:29:27'),(4,8,2,2,'Perawatan tumpatan gigi karies','Pembersihan karang gigi dan penambalan komposit','2026-10-04 06:29:27'),(5,11,5,2,'Konseling gizi reproduksi mahasiswi','Pemberian multivitamin zat besi dan asam folat','2026-10-04 12:29:27'),(6,13,6,1,'Penanganan iritasi mata akibat debu praktikum','Pemberian tetes pelumas mata buatan','2026-10-04 14:29:27'),(7,15,7,1,'Pembersihan serumen obstipasi telinga','Irigasi telinga pasien mahasiswa','2026-10-04 16:29:27'),(8,3,3,2,'Pertolongan pertama maag akut / GERD','Pasien lemas sebelum ujian jam 08:00','2026-10-04 18:29:27');
/*!40000 ALTER TABLE `usage_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin_poli','perawat') NOT NULL,
  `poli_id` int DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `poli_id` (`poli_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'super.admin','$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG','super_admin',NULL,'Super Admin',NULL,NULL,'2026-10-04 18:07:47','2026-10-04 18:07:47'),(2,'admin.umum','$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG','admin_poli',1,'Admin Poli Umum',NULL,NULL,'2026-10-04 18:07:47','2026-10-04 18:07:47'),(3,'perawat.umum','$2y$10$Z9FR9O33t0g4PxgE6QIL6OGYJe5uveIAHt/kpgZGtilsR5XblWIPG','perawat',1,'Perawat Poli Umum',NULL,NULL,'2026-10-04 18:07:47','2026-10-04 18:07:47'),(4,'admin.kia','$2y$12$ZI4vDYmlOqMVLeB1RL/JMuXgcpyFVot8UIXJhaMP9L6eR/2uZShmW','admin_poli',3,'Bidan Ratna Sari, S.Tr.Keb',NULL,NULL,'2026-10-04 18:29:27','2026-10-04 18:29:27'),(5,'perawat.kia','$2y$12$ZI4vDYmlOqMVLeB1RL/JMuXgcpyFVot8UIXJhaMP9L6eR/2uZShmW','perawat',3,'Perawat Siti Aminah, A.Md.Kep',NULL,NULL,'2026-10-04 18:29:27','2026-10-04 18:29:27'),(6,'admin.mata','$2y$12$ZI4vDYmlOqMVLeB1RL/JMuXgcpyFVot8UIXJhaMP9L6eR/2uZShmW','admin_poli',4,'Hendra Wijaya, S.Kep (Admin Poli Mata)',NULL,NULL,'2026-10-04 18:29:27','2026-10-04 18:29:27'),(7,'perawat.tht','$2y$12$ZI4vDYmlOqMVLeB1RL/JMuXgcpyFVot8UIXJhaMP9L6eR/2uZShmW','perawat',5,'Dini Rahayu, A.Md.Kep (Perawat Poli THT)',NULL,NULL,'2026-10-04 18:29:27','2026-10-04 18:29:27'),(8,'admin.fisioterapi','$2y$12$ZI4vDYmlOqMVLeB1RL/JMuXgcpyFVot8UIXJhaMP9L6eR/2uZShmW','admin_poli',7,'Bagas Pratama, S.Ft (Fisioterapis)',NULL,NULL,'2026-10-04 18:29:27','2026-10-04 18:29:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-05  2:11:08
