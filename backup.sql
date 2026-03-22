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
INSERT INTO `sessions` VALUES ('1gwTnyUtlqtgiLkOZALLunyDgjuES1eh4OQWTkcw',NULL,'64.62.156.212','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 OPR/94.0.0.0 (Edition Yx GX)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXY4VUFSa2g2Qm1aOE9qUkIzWDdCYjJ1Rlk1V0RRVk1Pb3VTMXdaaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774164005),('6CYTlPI96kDqxTDFeRg9IfJNi1ReMMRCgOMTibbm',NULL,'64.62.156.212','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFcya3REaXFpbDA5b0xVRlV4MGJrY1M1bTFha1drdlF5WlduVGVLciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774164530),('6YRKA4yt7MTFMD1WuiEKm0xPRuBCLGxtumiISA4u',NULL,'172.236.228.208','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWY1dGl6V2tKRnBQM3phZ0c5bDFPdDNhSk1rVHg3T0NpZ2V5N1hKayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774152842),('8h9x5FlleCpkQ1ZkosrUZd1rtSOnMOZ130ta6nvk',NULL,'217.76.52.30','Go-http-client/1.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoialdMbVVRTlBNSWxWNGJVcTBjTER6ZXZRSnUyUGg5alZYbk1jQ1d6VCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774168849),('8tjEsnRLut9xo2XcdPKUeNSEt8PI7NHfhDxQBajK',NULL,'74.249.129.23','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmE4cWNEcjRjd1A2dE8zTTFTSjNnSFIyQ2pLWWwxUGpHSlB1eHBUMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774149677),('9NXtsa4spcZPkNRfONPLIWsrdRG0NKh4LBIhmzkC',NULL,'81.29.142.100','Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWTFRcmdtUms0M3dpTE5yMTFrR0pNZXQ3NEpnUE12bXlmQjE4RnlZcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774164438),('BGL6cmN3gVKvy80qEA5nThrhmVpV1kmvcvFuiYD7',NULL,'172.236.228.208','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0FKMFFnUHBuc0RlVWNCNUd1RjUzc1BLV1IzbGo4Rk5VcEU3Z3ZGSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774152843),('cqIAeC0yG393QLhmlpqCKHXgKu48u16Y5QScqvYP',NULL,'64.62.156.213','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 OPR/94.0.0.0 (Edition Yx GX)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiblJ5T2Q5WEFVWHdFTW5jRW9qWU4yckVzOXEyVTV6Sk5IMk9tY0luZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774164466),('DH5eCS13x7aXajcRz7aMwYiPiLsyrtuIjfmWHKQ0',1,'182.5.247.204','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiSXZuN1QwWk9oUTBOcWxzQ0pHeW9jSUpqeU0zMFpVT1VYS0hkeUwzNyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU3OiJodHRwczovL2JpbGxpbmcuYWlybmV0cHMubXkuaWQvYWRtaW4vbWlrcm90aWsvMTIvc2Vzc2lvbnMiO3M6NToicm91dGUiO3M6MTc6Im1pa3JvdGlrLnNlc3Npb25zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NzQxNDM5MjY7fX0=',1774148387),('eAzMg60xFnDpSwJ2Mlcp7ldXKIl3wz50dVqqh5rL',NULL,'172.104.11.34','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSGd4RW5zNWlVNkx4dUlTd1pFRW1LM2x4REE4aHhXWmRMTkIySVlldyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774162452),('f3Uar4ADhljpcNtxoLP2evCiID5Lxnt03j5J0yHD',NULL,'103.22.242.5','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMGU3eXFEdjhuMlFnVmJHWDBoeU83SDdJS2RpYU9FSDhJQWtKSURHNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NToiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2FkbWluL21pa3JvdGlrL21vbml0b3Jpbmc/cm91dGVyPTMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNjoiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774143919),('GkVsBd0UImcJoBcBKPFwOD5DH3SSybLTdlNjW2VT',NULL,'64.181.211.198','Mozilla/5.0 (compatible; websiphon/0.2)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjlNc2kwQ3p3cG5DUFR6UEdiU3llU1FFRmdFSlROQ2FBU0ZFTzJtZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774163243),('HOZ3PtJJzp1SVFZxtkMDPtAidn5QBziVbCuLZPes',NULL,'34.61.82.184','Go-http-client/1.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOTJwNHlVVmRBUTNreDNzdGVSZm54YjN2Wm5YdHJjeDZWOUF6R1FpaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774160352),('I3bacIs2lRK38xrhcJpTeZviPSGHOzRaalxHMAmS',NULL,'80.82.77.202','fasthttp','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVRkTmh5Q1hZYnV0dzJmTGR0Y2hHQzViaGpzckY1cjFFSVRpRmVUaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774165544),('jE2Aw5B7warpVi4Zmp0zovduzZSYv73IOYLSr3o9',NULL,'172.104.11.34','Mozilla/5.0 (Macintosh; Intel Mac OS X 13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSU5kOTBxR1JDU3huY2JIaWh6S2lWdVQwVHhhVEphWFVlTUJtcHh2RyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774162453),('jKyCyUE43oVXsDjbrwh6dJwbIdHHF1eUCW1CwHvD',NULL,'217.76.52.30','Go-http-client/1.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDlvaTBXazNaV1ZDcjN4SVJERXhnekd4RmhqUXFSejY0QUhuVnJrQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774168848),('lto7dCQS4QHcR3IYuPkO3LLRaOv5bLm4FUrI4Lx8',NULL,'74.249.129.23','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoialdFUnB0VjIwNDh1YXpvY3d0SGdacTNDalhtejE5U0pCU1FnTWlxNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774149677),('MPadCrXYHf4gDb6jXX4Ov7OndCC4qtKs2ZZdjHK5',NULL,'142.93.35.114','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRW9TQ3V2RzRmYXpBZmZ4SDFsOXp4dzhYelhaN1l6bzlaNk9RYWxNQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774164147),('rbMOVPaxt3EnsqVPj994XZNBGHz8x56sWSBbfCrr',NULL,'64.62.156.212','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjIzRWVGNmt1ajZFd2VrdFpGaTJjT3poYlpkUWJWRzVTbVhpSGlCeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774164530),('RjOWl2P6AhPwV6Ct5rR6O0N9cR0vmZAZij9RNqVJ',NULL,'147.185.133.114','Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNkwwV0pNaDh1OW4wakFPOG1Ra0FjM0JjSUN5TEh5YVF3VjBTM2x3SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774145101),('T5fnq5YITCZfG4e8BBWvGHcYLcCHka6YHcD8I3zR',NULL,'103.175.82.69','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGZvZHpDa2pyRExzUTZHTVRwcDluWndzSDUwbzRUcWhzZzd2VGxkcyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NToiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2FkbWluL21pa3JvdGlrL21vbml0b3Jpbmc/cm91dGVyPTEiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNjoiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774143917),('Vod2ctutH8HJipk1UCNW3VDeJQbngdtnYoCTP3PQ',NULL,'64.181.211.198','Mozilla/5.0 (compatible; websiphon/0.2)','YToyOntzOjY6Il90b2tlbiI7czo0MDoibmVQdGhNTUxIajRzTVRhaWM0QzNFVDlvQTNMdDBrbVNvWXNLa2U4YiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774163242),('WXYmggeriM4RbH26rPtYI5HyaVZJO5ZqeaQTXPRZ',NULL,'34.61.82.184','Go-http-client/1.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoicmxCSnR1aXM5dHBoalFKalBEUndZdnJSWkpGcXdxUDlQOTJ6V2ZYZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vMTYzLjYxLjU4LjE3MiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774160352),('xSCz5evfs3QLDpmJVmpWTfhrUvHBTUZt558NKu9d',NULL,'64.62.156.212','Mozilla/5.0 (ZZ; Linux x86_64; rv:122.0) Gecko/20100101 Firefox/122.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXJmU3dRVFZtMjFZQUQxRlJXajRhaHRqazJQMzdORllrenYxM2lDeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774164573),('Y3yqnMAiB3AH3aq5OmoXD2A3dFLYRENl4aeZs5JC',NULL,'81.29.142.100','Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV2kwcEhqMXhlejNKTHN6UXEwV0pTMUdCRTg1VEhkYmhBeG5RSGo3SiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774164448),('YDCrVGOTu7b9kWU0oe6uPt6si7E0nF9LZ9pAf45t',NULL,'103.22.242.4','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVNkYndVdGV3bUhaMVI3Z3o4eE1vaWtnaFd5VTFWd0ZPdDBaQmszcSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2FkbWluL21pa3JvdGlrIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774143909),('YUD5JTenpfGGyDcoVbmvK7QTyRfdocww3wQTofCW',NULL,'141.148.153.213','Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaFQ4NHo3dXBuNXJhVlVjbFdKbG15dkJCaDVhUkoycUFNM3ltOXdzUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774149985),('Zi18lQKtSMiSgCbJtCAUBzQcMnObVCvEUtd7C4iJ',NULL,'103.175.82.68','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidm5XNnhCSXl6Qks4bFRWR3BxQTRNVzZEM3lsNm5kTGEzdlBRaVpsMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NjoiaHR0cHM6Ly9iaWxsaW5nLmFpcm5ldHBzLm15LmlkL2FkbWluL21pa3JvdGlrL21vbml0b3Jpbmc/cm91dGVyPTM3Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vYmlsbGluZy5haXJuZXRwcy5teS5pZC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774148363),('zrcDJLmxgN5s66jPt8p9vkj9ONE1wsBPzPkJTJ0R',NULL,'147.185.133.114','Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFV6TEpvVlNzQVZyaGdRbFRpWnZHVk9Ld05NQk9CUkRwaFRQRlppeCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTYzLjYxLjU4LjE3Mi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1774145102);
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

-- Dump completed on 2026-03-22 16:21:59
