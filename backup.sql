-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: mybilling
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_28_043930_create_personal_access_tokens_table',1),(5,'2026_02_28_043942_create_permission_tables',1),(6,'2026_02_28_044056_create_paket_table',1),(7,'2026_02_28_044225_create_pelanggan_table',1),(8,'2026_02_28_044255_create_tagihan_table',1),(9,'2026_02_28_044327_create_pembayaran_setting_table',1),(10,'2026_02_28_164903_create_routers_table',1),(11,'2026_02_28_172634_add_router_id_to_pelanggan_table',1),(12,'2026_03_08_230051_add_portal_password_to_pelanggans_table',2),(13,'2026_03_08_230051_add_role_to_users_table',2),(14,'2026_03_08_230200_add_portal_password_to_pelanggans_table',3),(15,'2026_03_11_000001_add_bri_columns_to_tagihan',4),(16,'2026_03_14_091452_add_cascade_to_all_foreign_keys',5),(17,'2026_03_15_225445_add_koordinat_to_pelanggan_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket`
--

DROP TABLE IF EXISTS `paket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_paket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int NOT NULL,
  `kecepatan_download` int NOT NULL,
  `kecepatan_upload` int NOT NULL,
  `radius_profile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_aktif` int NOT NULL DEFAULT '30',
  `jenis` enum('pppoe','hotspot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pppoe',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket`
--

LOCK TABLES `paket` WRITE;
/*!40000 ALTER TABLE `paket` DISABLE KEYS */;
INSERT INTO `paket` VALUES (5,'110k',110000,10,10,'110k',30,'pppoe',NULL,1,'2026-03-06 17:48:52','2026-03-06 17:48:52'),(7,'20mb',150000,20,20,'paket20mb',30,'pppoe',NULL,1,'2026-03-14 02:18:51','2026-03-14 02:18:51');
/*!40000 ALTER TABLE `paket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pelanggan`
--

DROP TABLE IF EXISTS `pelanggan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pelanggan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_pppoe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portal_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `paket_id` bigint unsigned NOT NULL,
  `router_id` bigint unsigned DEFAULT NULL,
  `tgl_daftar` date NOT NULL,
  `tgl_expired` date DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `router_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','suspend','isolir','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `jenis_layanan` enum('pppoe','hotspot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pppoe',
  `wilayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pelanggan_id_pelanggan_unique` (`id_pelanggan`),
  UNIQUE KEY `pelanggan_username_unique` (`username`),
  KEY `pelanggan_router_id_foreign` (`router_id`),
  KEY `pelanggan_paket_id_foreign` (`paket_id`),
  CONSTRAINT `pelanggan_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pelanggan_router_id_foreign` FOREIGN KEY (`router_id`) REFERENCES `routers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pelanggan`
--

LOCK TABLES `pelanggan` WRITE;
/*!40000 ALTER TABLE `pelanggan` DISABLE KEYS */;
INSERT INTO `pelanggan` VALUES (9,'AR-20260004','laptop','laptop','$2y$12$W0U20t8Rpvx6oi8VI.OCKuk9H8la6aepOIImlVCVIhpp1ogy0QBjS',NULL,NULL,'081222','l@c.c','demuk',5,5,'2026-03-06','2026-05-05',NULL,NULL,'aktif','pppoe','demuk',NULL,NULL,'$2y$12$TRmw/QVL20.NhF2HG4rB0e6GbUvI5fhld0LrDQh6DfCglT0XbYCJu',NULL,'2026-03-06 06:21:06','2026-03-14 02:04:48','2026-03-14 02:04:48'),(10,'AR-20260005','topa-demuk','topa-demuk','$2y$12$DcYzOxPcg2oLjLws.Pur7.yySDpmr9fQehfTRx.hBUT4BjVUmIsRq','12345678',NULL,'81335','topa-demuk@a.c','demuk',5,5,'2026-03-06','2026-03-10',NULL,NULL,'isolir','pppoe','demuk',NULL,NULL,'$2y$12$Kw7yXGpAMRJlC0PgOmjK4uoCBNCnJk98yKq5Mbth09BOwHkBZJmW6',NULL,'2026-03-06 08:44:12','2026-03-09 18:00:03',NULL),(31,'AR-20260011','erkam','erkam','$2y$12$LBShpg7L2XjEguVwTpp1SunAAACXxFOYIB9XYaBfiW/6cuvVhG1/y','12345678',NULL,'08122','ervan@gmail.com','demuk',5,5,'2026-03-15','2026-04-17',NULL,'test','aktif','pppoe','demuk',-8.18836092,112.01752689,NULL,NULL,'2026-03-15 15:21:57','2026-03-15 19:10:50',NULL),(32,'AR-20260032','nia','nia','$2y$12$ofxCQ5PKZjXoSp/oNcjGfOM8e30Ni0tbMnzr.mtzpRTREkFVx8fjy','12345678',NULL,'81227','owzzay@gmail.com','demuk',5,5,'2026-03-15','2026-04-17',NULL,'test','aktif','pppoe','demuk',-8.18833794,112.01782599,NULL,NULL,'2026-03-15 15:21:57','2026-03-16 05:21:23',NULL),(33,'AR-20260033','home1','home1','$2y$12$xNHqMiu04yLOoeyWa5c9reK.wDIWvzdPuzK1KF1O2pbdDbSpjVoie','12345678',NULL,'081233','rdf@m.m','demuk',7,5,'2026-03-15','2026-04-14',NULL,NULL,'aktif','pppoe','demuk',-8.18849504,112.01820967,'$2y$12$sPZwCR4RkCAdAUAkhLWG6uumdczb7.LZSgvYl95htf2wFiEMdLWKO',NULL,'2026-03-15 15:24:36','2026-03-15 19:07:54',NULL);
/*!40000 ALTER TABLE `pelanggan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagihan_id` bigint unsigned NOT NULL,
  `pelanggan_id` bigint unsigned NOT NULL,
  `jumlah_bayar` int NOT NULL,
  `metode` enum('cash','transfer','midtrans','xendit','bri_qris','bri_va') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembayaran_no_pembayaran_unique` (`no_pembayaran`),
  KEY `pembayaran_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `pembayaran_tagihan_id_foreign` (`tagihan_id`),
  CONSTRAINT `pembayaran_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routers`
--

DROP TABLE IF EXISTS `routers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL DEFAULT '8728',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `local_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remote_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dns_server` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
INSERT INTO `routers` VALUES (1,'pule','157.15.67.51',28828,'admin22','bungaais03','10.10.127.1','ALL-POOL','157.15.67.7,157.15.66.9,157.15.66.10,1.1.1.1',1,'2026-03-02 23:57:19','2026-03-02 23:57:19'),(5,'test','10.10.10.2',8728,'admin','admin','10.10.5.2','pool-pppoe','8.8.8.8,8.8.4.4',1,'2026-03-06 03:58:21','2026-03-06 04:50:31'),(12,'gr3-sumberbendo','10.10.10.4',56988,'admin1','bunga040','10.10.10.1','POOL-1','157.15.67.7,157.15.66.10',1,'2026-03-11 04:51:32','2026-03-11 14:15:12');
/*!40000 ALTER TABLE `routers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0qN9N7WY1jLeurzw2xU0yS7EicM6Fm9YNsC7yFCL',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlRweHVVODdkdWhPdjVlMFhZUnhzSUpQOVVmM1lqMFc0bnZjUm9xSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi8/cGhwaW5mbz0xIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898728),('18AsBjb1XMOb4YgOqNJwYCLlrGLhnNfQr8i6dJuU',NULL,'157.15.67.57','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTG5ab2xwRW03d1dpSlJLVWpkeUw4TDlwMndFNHFtNDB1ejR3cXZOTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773920295),('1UQAQ7cXAq1E6lfhcOJ94LpMnNK1RlI82yaTSSXG',NULL,'45.156.129.189','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.86 Safari/537.36 BitSightBot/1.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVU9qejlsZWhSNGRKMDZPT0dlQ1JqamxTQlZZYVN6b3N2QUNheUxSbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773921950),('2pWgIX36OZFrSlrPDkbHfMBAgyyuheWQWdDnkVkX',NULL,'65.49.1.22','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjUxMEJ6S3hMeHhrTUNCMmZTTVVSMjN4MUZzVHlpVFI3Q1VQdEt3byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773913422),('5aOTaQdqeDzsImbMGoU6O8UGS97iqeGFQGDUShQJ',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVU9rY1JDczRTcHRBRmtoUUVudnJGOVBCcjF1OXZudGNhbHcwUVBHRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi8/cHA9ZW52IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898729),('5wJVNkctRMBh3yNF1Rf7EuY6QKdOdQiWkpwyIuTL',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWnJzTnlYWmlyQ0VMVHFmMnh1MVpzc2djTmt5MGNVNmtjRnp1RVI5NSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898647),('ArYII0ZhK3Drd0pKeZ9bn1DhcO6kcoSjxyBmSnaz',NULL,'18.218.118.203','visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkFidlZVN3FzeFdxSGhNb0VmRE9RV25CMUFoTUNGcjFGRzR6czZWViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773931875),('aUflAz789Bo8rsZTkVCdnNFiBKsVj0gHAzeMgzT4',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoidWVZOU9BVk43N3pocTdkMUpaWjRLaVFnbnRrdnlzVlV2ZGhxV0RWRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbj9wcD1lbnYiO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898763),('BmpTMa86Mv2Fp8KQ3NbzDN2Tuh03fxI8XPL9QLYz',NULL,'134.209.86.24','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2hzdUgweTJ6dGs0aVcxWEJ4Y3d0em13dzg5NVlycWFjVm15cXkwWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773905661),('BrK4iIIdDoruhoD3MjJD44egezrPMq0GV7hOKYca',NULL,'35.195.17.170','python-requests/2.32.5','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWRpaEpIWW1FRnc5SUF6N2sxNlJLUzBlSm1UajR0NzVWNmMzV2RlbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773921502),('dqfTFLnkZDMkjcNTqcvxWheCseFgQCPCGR0Qmdpj',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoicjI4RmVJM3N2d2VxZmNIUFF6NTRpWTJwZXdKbGZ0TTNHaDBuWTJlWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbj9wcD1lbnYiO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898705),('EuKIU48E7UmnewbgpVDasjBxQlbZ5OpCCE1qt9uT',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkRIeXpVbEZhQ2NBekc4dVI5Y3FJRk04dlEzRkpONFJDTW1yT3FWViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898672),('f4ImexTPVKz2sk7NKEXeRKRXFb41yFbCS3GQ2M3p',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0gwTFF0bzRSU3B5OW15Uzk4TE9pWjdSb0s5OXBTcTgyMEdUcWo4UiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898729),('Gmas917FzqPeuR9wCStUbFseqEQqZ2uLVZkGhT58',NULL,'18.218.118.203','visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXF5dVBIdXRlTzZHbUY5blpVZnFxZldPcXNmQlpKMHpOZ0tqempVWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773931874),('gsTlWLTuRmmusQRmYyzEw3J7mzmOCn77o5WCsmPK',NULL,'149.154.83.13','','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFpzUGU1ZlJCVUtIQlZVVlNkbFUzNHY0UnV0eGFZbzZjQ0xsWmFBWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773930886),('IJW2EvTcD42p7X6aUyYhoNfS8jstWSVZsfEjkmJ3',NULL,'206.168.34.53','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVA3RmFiQmNMRFdxUGVOVUxyRzhLMUIwenI1dlR3eGFrNGduU2RNNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773934672),('J8w8H8MW67WsrMzMQqI38z8wn2RxdAdg98hJqTmK',NULL,'81.29.142.6','Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoic210NHRlRWhyeTFDbVVrdzNPeHFzNzdDbWFmZDFvMkZMekNJSEZkciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773936685),('L1fKCmpxA0SbxEMXbFjNsMLTvMZo5pqCiOvF8QfB',NULL,'167.94.146.60','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOE5UV0RUcVROMEV4N3hmR2xaWUlWbkxaZnByT1Fkd2Z1VmZPYTI5bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773913220),('mqZLo9GDybVtikASBNy7tCVKsdyumuFHr40OcuyO',NULL,'167.94.146.60','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMURPd3BiaWtFTEpOMFBoYVFTSEJNTWhKbHMxb04zQmt4TnlCRDNXcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773913223),('nIe6ij6LqnszNIAiCM9XkImThYcC1Baq8N45pb3q',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3haeVpNZnhGQVNEWWxDWmpLZFJnUVByNDgzajNyYmZnYlpvZ0V6USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898648),('OdLUC6nLLKLtpzeKpV37xTKmj0V0Jhpp2OpZ10pW',NULL,'194.50.16.198','python-requests/2.6.0 CPython/2.7.5 Linux/3.10.0-1160.119.1.el7.x86_64','YTozOntzOjY6Il90b2tlbiI7czo0MDoidHpYUVpkSHRqMGV4TGhWckVkUWxXOEowd3ZSZDU1TFVONGVDR0QyWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773900521),('oJTU8dMqVgxPebuGiXSaYy3dIFEjHYMysDqCv9rU',NULL,'167.94.138.193','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoidnBtbkl0VTl2QzJoRUxrbHZDNTNTSHhXRmtFbERHdkg0d1RJS0VnSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773906890),('oO1qzdK7MzKpyaJd1eSB7Sf8e8sfLx111QtVasAf',NULL,'172.210.68.2','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoicHVUbDNBWkRGTDVEckxtWFlFUXFCY3kzb3NuVkxvUENMS3pFWEE1aCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773906858),('Ov3Nt9wi2hmyfpSbBtNxuAzOorVooiBbqG8NnjwW',NULL,'65.49.1.10','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.6.20','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmsyOUJlcUpsZVlBQWIyR2dKdVRTU1VBTE1jb09lRm5sdW1aTzZnMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773913472),('QftIqgJQmDs8OiH16iPXu2HRUewJIKEjnOnnYyvZ',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiejV4UWxzWjlYZmFiU2NyM3hiU3BTb3ZzMWtPYlM2VzdEVVJFSERGaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773898589),('rF8JGeqMTBZeYGtI57ayB8skfQ6tYlZwyWnRPzBV',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWVEM0Nlc3Rqc29nZHNwOVBGVE8xekNlRGdma2F3TjhqNjZLakR5ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898671),('rfoocaEV8Z1T5uWX7MgbB8J9qRyGwHHZueqdV3ng',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWE2SGk4WDg0UlNnQ1lheTZya2p6TXZGMDNoclhkYkIweHpoV0t2aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773898646),('rkCyvfcKH1BG1ctAxvMInHSNjWUnH2O3gilx18vU',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiczNSSEFqNVpTdEROTjdEUG1LNEd2a290Yzk3RDdBdDNNQjZJTUpJQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898707),('rwai3gLe49s1bi8sZFuTLw6nJbozPMqhPegDX9GP',NULL,'65.49.1.22','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjRXaGJFMFBCc3BNYzlvNFdBSzZSOVFYWVlOb0VDWXJIdVZjc3B1diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773913422),('sGRQTSRzgYzVKMsEkb8UVXKsxXoUyuUuYs5jvc3i',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUpKS2FtOGY0VXdkb3NCRk9JQ1ZuSkZ2bGdIdGt6TVdMZlkzeHd0SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898589),('SXzlPI5MsndtL41Y89QhYaCCAP3pSA96YzkHDzej',NULL,'65.49.1.12','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 OPR/94.0.0.0 (Edition Yx GX)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWdTVUx4aXpJNjUwZ2gwNW1rMno4bVR1bjFodFBseTZHV3JQNnNkRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773913357),('TMmEnc016DidXjUL94pKgoN3JJxGG9bz5ToaLEcT',NULL,'185.177.72.22','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzVUSjVkcVRDd1J6MDFYbnRrcGJvVFNoWkVtWEp3bHJvdGdHVWNxYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773898729),('tVG0HZBvDPvTFhHXNicRUfv9c8r7YfoRJX9qjQFE',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQThrTGRVMFZzZXZ3dE1wbUNTc1FmQ3cwdVhybDJpa2djRm1YWHIwZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC8/cHA9ZW52IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898672),('Twu0oI5gF1Pu1PBnHUnJPltcjomhjGor6NhuWDxV',NULL,'65.49.1.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 OPR/94.0.0.0 (Edition Yx GX)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN05pT1UxRUprSUVydE83U21raUxvZWprUDdaZ2NBdjd4dnp6MmN2OSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773912862),('VsXAPXm9qbGtxIyq7FxnCefQjK51l2Dx3xwcCpKZ',NULL,'167.94.138.193','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3JtY1BBbVl4S1NoY3NlRVp5QzRFcTlpeGNxcTlkOFRET0NvRkU2eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773906881),('W6KQKEkiR281kj7KSrnGi23TeYaPPVp8akuwgJzj',NULL,'206.168.34.53','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoibmhxd1kzOWxvWk00QXo5T3hVanNtT1FveDM4M2NpQ3ZBeEVQR0V0USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773934673),('XdEXDzEb9yG1obVeQBnx7t8xKpSobbohufAfXpZJ',NULL,'188.166.116.162','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUJuMjZQVXlXY3ZBQXNyQ1Y0ZnljNWtGeXJ6a1F4R1haeVYwa2k4TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773915034),('YGAlYb1zbyPi0xOA60bIYK5r0lj4zgWvbYFClMGI',NULL,'45.156.129.189','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.86 Safari/537.36 BitSightBot/1.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkVkUkFuUUtjYlQ2VHZxRWtaNFhuQ2haQjFyQmtQd3lsZ2pxYVZyYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773921950),('Zi7s05pPUIrbnq3L4nPPNgEQbcDo5PuP5pMvGdjK',NULL,'172.210.68.2','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoibHNCRTAxa1RLWFdaR1Z6cjM0cmVHNjR3dENXV3dJUlhqMWFYWHpkNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773906858),('zvrrAxnhcuHrAFEObxRrHDIo6jRGpnAh75neMOZJ',NULL,'81.29.142.6','Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYUVXa2RnWDQwWXZuYWt5RkxvWXBMRVZCWGt1QzFFNFFRVDdHbHczRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773936691),('zwsy2IOJxxegG4bpecX1WqU8VqLhAwZFnzicMEjY',NULL,'185.177.72.51','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEs1VWRJdTVFTkFlcjFWeGRnZDNMWkJiQ1hUamVUYkdKeTBpOEd2cyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC8/cGhwaW5mbz0xIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773898671);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setting` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting`
--

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagihan`
--

DROP TABLE IF EXISTS `tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_tagihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelanggan_id` bigint unsigned NOT NULL,
  `paket_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL,
  `denda` int NOT NULL DEFAULT '0',
  `diskon` int NOT NULL DEFAULT '0',
  `total` int NOT NULL,
  `periode_bulan` date NOT NULL,
  `tgl_tagihan` date NOT NULL,
  `tgl_jatuh_tempo` date NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `status` enum('unpaid','paid','overdue','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `metode_bayar` enum('cash','transfer','midtrans','xendit','bri_qris','bri_va') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bri_qris_data` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bri_va_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bri_ref_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bri_expired_at` timestamp NULL DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagihan_no_tagihan_unique` (`no_tagihan`),
  KEY `tagihan_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `tagihan_paket_id_foreign` (`paket_id`),
  CONSTRAINT `tagihan_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tagihan_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagihan`
--

LOCK TABLES `tagihan` WRITE;
/*!40000 ALTER TABLE `tagihan` DISABLE KEYS */;
INSERT INTO `tagihan` VALUES (6,'INV-TEST-BRI-001',10,5,500000,0,0,500000,'2026-04-01','2026-03-11','2026-03-21',NULL,'unpaid',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-11 16:08:00','2026-03-11 16:08:00');
/*!40000 ALTER TABLE `tagihan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','operator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operator',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin','admin@isp.com',NULL,'$2y$12$N4kVNM8/.dT5Q61dmdm0QefmIbpkWfdwscpWqbIxnovKKH04a6Zp6',NULL,'2026-03-02 23:55:51','2026-03-02 23:55:51'),(2,'huda','operator','huda@isp.com',NULL,'$2y$12$/Xh0quEA4hjkXqgwyalbvOg4cVpjdkadsCTBqAIs5w4byOlM8/wTW',NULL,'2026-03-08 16:22:29','2026-03-08 16:22:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_log`
--

DROP TABLE IF EXISTS `wa_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wa_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pelanggan_id` bigint unsigned NOT NULL,
  `no_tujuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('tagihan','jatuh_tempo','isolir','aktivasi','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wa_log_pelanggan_id_foreign` (`pelanggan_id`),
  CONSTRAINT `wa_log_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_log`
--

LOCK TABLES `wa_log` WRITE;
/*!40000 ALTER TABLE `wa_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `wa_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-19 23:56:45
