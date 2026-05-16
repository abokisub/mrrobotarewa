-- MySQL dump 10.13  Distrib 9.6.0, for macos26.3 (arm64)
--
-- Host: localhost    Database: mrrobot
-- ------------------------------------------------------
-- Server version	9.6.0

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'f8606786-471a-11f1-b738-d55f5eba887c:1-3393';

--
-- Table structure for table `bot_logs`
--

DROP TABLE IF EXISTS `bot_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bot_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bot_logs`
--

LOCK TABLES `bot_logs` WRITE;
/*!40000 ALTER TABLE `bot_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `bot_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
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
  `expiration` bigint NOT NULL,
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
-- Table structure for table `daily_statistics`
--

DROP TABLE IF EXISTS `daily_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_statistics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_trades` int NOT NULL DEFAULT '0',
  `wins` int NOT NULL DEFAULT '0',
  `losses` int NOT NULL DEFAULT '0',
  `daily_pnl` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `drawdown_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `volume_traded` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_statistics_date_unique` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_statistics`
--

LOCK TABLES `daily_statistics` WRITE;
/*!40000 ALTER TABLE `daily_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_statistics` ENABLE KEYS */;
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
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
  `attempts` smallint unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_16_210736_create_bot_logs_table',2),(5,'2026_05_16_210736_create_positions_table',2),(6,'2026_05_16_210736_create_signals_table',2),(7,'2026_05_16_210736_create_trades_table',2),(8,'2026_05_16_210736_create_user_settings_table',2),(9,'2026_05_16_211657_create_daily_statistics_table',3),(10,'2026_05_16_211657_create_risk_events_table',3),(11,'2026_05_16_213000_add_tags_to_trades_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `side` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` decimal(20,8) NOT NULL,
  `entry_price` decimal(20,8) NOT NULL,
  `mark_price` decimal(20,8) DEFAULT NULL,
  `liquidation_price` decimal(20,8) DEFAULT NULL,
  `unrealized_pnl` decimal(20,8) DEFAULT NULL,
  `leverage` decimal(8,2) NOT NULL DEFAULT '1.00',
  `margin_used` decimal(20,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_events`
--

DROP TABLE IF EXISTS `risk_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risk_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_events`
--

LOCK TABLES `risk_events` WRITE;
/*!40000 ALTER TABLE `risk_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_events` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('puHvEfriDIAyfoEwrw3IefZEMr7ybpsbJr3P1Zq4',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnaWRFZU5lOG9lbG1paWJLOGg3WGFJc2lsYzJJNWk3NmtTNnNCTmk4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1778969177),('qAfuMc2muPz4MsrqB4pdKH3SI7pdCPLJuLXZcz9d',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI2b1JhR2o4SUdBWlFIOVJrU3dMdXlzMmU3azVvWk1XRWVPY29xOXMwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2xpdmUtcHJpY2VzIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1778970097);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signals`
--

DROP TABLE IF EXISTS `signals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signal_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rsi_value` decimal(8,4) DEFAULT NULL,
  `macd_value` decimal(20,8) DEFAULT NULL,
  `confidence_score` int NOT NULL DEFAULT '0',
  `market_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signals`
--

LOCK TABLES `signals` WRITE;
/*!40000 ALTER TABLE `signals` DISABLE KEYS */;
INSERT INTO `signals` VALUES (1,'BTCUSDT','WAIT',44.4355,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:13:16','2026-05-17 04:13:16'),(2,'ETHUSDT','WAIT',36.5389,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:13:18','2026-05-17 04:13:18'),(3,'SOLUSDT','WAIT',33.6000,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:13:19','2026-05-17 04:13:19'),(4,'XRPUSDT','WAIT',47.9675,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:13:19','2026-05-17 04:13:19'),(5,'HYPEUSDT','WAIT',45.4999,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:13:22','2026-05-17 04:13:22'),(6,'BTCUSDT','WAIT',45.1166,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:01','2026-05-17 04:14:01'),(7,'BTCUSDT','WAIT',45.1166,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:02','2026-05-17 04:14:02'),(8,'ETHUSDT','WAIT',36.3273,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:03','2026-05-17 04:14:03'),(9,'ETHUSDT','WAIT',36.3273,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:03','2026-05-17 04:14:03'),(10,'SOLUSDT','WAIT',33.8710,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:03','2026-05-17 04:14:03'),(11,'SOLUSDT','WAIT',33.8710,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:04','2026-05-17 04:14:04'),(12,'XRPUSDT','WAIT',47.8378,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:04','2026-05-17 04:14:04'),(13,'XRPUSDT','WAIT',47.8378,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:05','2026-05-17 04:14:05'),(14,'HYPEUSDT','WAIT',45.6336,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:05','2026-05-17 04:14:05'),(15,'HYPEUSDT','WAIT',45.5666,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:14:06','2026-05-17 04:14:06'),(16,'BTCUSDT','WAIT',45.9344,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:06','2026-05-17 04:16:06'),(17,'BTCUSDT','WAIT',45.9344,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:06','2026-05-17 04:16:06'),(18,'ETHUSDT','WAIT',35.8279,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:07','2026-05-17 04:16:07'),(19,'ETHUSDT','WAIT',35.8279,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:07','2026-05-17 04:16:07'),(20,'SOLUSDT','WAIT',33.6000,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:08','2026-05-17 04:16:08'),(21,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:08','2026-05-17 04:16:08'),(22,'SOLUSDT','WAIT',33.6000,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:08','2026-05-17 04:16:08'),(23,'HYPEUSDT','WAIT',46.0671,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:09','2026-05-17 04:16:09'),(24,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:09','2026-05-17 04:16:09'),(25,'HYPEUSDT','WAIT',46.1905,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:16:09','2026-05-17 04:16:09'),(26,'BTCUSDT','WAIT',46.2692,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:02','2026-05-17 04:18:02'),(27,'BTCUSDT','WAIT',46.2692,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:02','2026-05-17 04:18:02'),(28,'ETHUSDT','WAIT',36.5108,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:03','2026-05-17 04:18:03'),(29,'ETHUSDT','WAIT',36.5108,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:03','2026-05-17 04:18:03'),(30,'SOLUSDT','WAIT',34.2742,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:04','2026-05-17 04:18:04'),(31,'SOLUSDT','WAIT',34.2742,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:04','2026-05-17 04:18:04'),(32,'XRPUSDT','WAIT',47.7089,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:05','2026-05-17 04:18:05'),(33,'XRPUSDT','WAIT',47.7089,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:05','2026-05-17 04:18:05'),(34,'HYPEUSDT','WAIT',47.0588,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:05','2026-05-17 04:18:05'),(35,'HYPEUSDT','WAIT',47.0588,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:18:06','2026-05-17 04:18:06'),(36,'BTCUSDT','WAIT',44.4744,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:04','2026-05-17 04:20:04'),(37,'BTCUSDT','WAIT',44.4744,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:04','2026-05-17 04:20:04'),(38,'ETHUSDT','WAIT',36.0714,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:05','2026-05-17 04:20:05'),(39,'ETHUSDT','WAIT',36.0714,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:05','2026-05-17 04:20:05'),(40,'SOLUSDT','WAIT',34.0081,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:06','2026-05-17 04:20:06'),(41,'SOLUSDT','WAIT',34.0081,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:06','2026-05-17 04:20:06'),(42,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:06','2026-05-17 04:20:06'),(43,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:07','2026-05-17 04:20:07'),(44,'HYPEUSDT','WAIT',47.5054,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:07','2026-05-17 04:20:07'),(45,'HYPEUSDT','WAIT',47.5054,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:20:08','2026-05-17 04:20:08'),(46,'BTCUSDT','WAIT',45.6166,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:04','2026-05-17 04:22:04'),(47,'BTCUSDT','WAIT',45.6166,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:04','2026-05-17 04:22:04'),(48,'ETHUSDT','WAIT',36.0714,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:05','2026-05-17 04:22:05'),(49,'SOLUSDT','WAIT',33.7349,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:05','2026-05-17 04:22:05'),(50,'ETHUSDT','WAIT',36.0714,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:05','2026-05-17 04:22:05'),(51,'XRPUSDT','WAIT',48.0978,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:06','2026-05-17 04:22:06'),(52,'SOLUSDT','WAIT',33.7349,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:06','2026-05-17 04:22:06'),(53,'HYPEUSDT','WAIT',47.5636,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:06','2026-05-17 04:22:06'),(54,'XRPUSDT','WAIT',48.0978,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:06','2026-05-17 04:22:06'),(55,'HYPEUSDT','WAIT',47.5782,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:22:07','2026-05-17 04:22:07'),(56,'BTCUSDT','WAIT',46.3004,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:08','2026-05-17 04:24:08'),(57,'BTCUSDT','WAIT',46.3004,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:08','2026-05-17 04:24:08'),(58,'ETHUSDT','WAIT',36.2564,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:09','2026-05-17 04:24:09'),(59,'ETHUSDT','WAIT',36.2564,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:09','2026-05-17 04:24:09'),(60,'SOLUSDT','WAIT',34.2742,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:09','2026-05-17 04:24:09'),(61,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:11','2026-05-17 04:24:11'),(62,'SOLUSDT','WAIT',34.2742,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:11','2026-05-17 04:24:11'),(63,'HYPEUSDT','WAIT',49.4402,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:11','2026-05-17 04:24:11'),(64,'XRPUSDT','WAIT',47.5806,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:12','2026-05-17 04:24:12'),(65,'HYPEUSDT','WAIT',49.3022,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:24:13','2026-05-17 04:24:13'),(66,'BTCUSDT','WAIT',44.9316,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:01','2026-05-17 04:26:01'),(67,'BTCUSDT','WAIT',44.9316,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:01','2026-05-17 04:26:01'),(68,'ETHUSDT','WAIT',35.8279,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:02','2026-05-17 04:26:02'),(69,'ETHUSDT','WAIT',35.8279,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:02','2026-05-17 04:26:02'),(70,'BTCUSDT','WAIT',46.1180,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:32','2026-05-17 04:26:32'),(71,'ETHUSDT','WAIT',36.5108,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:26:33','2026-05-17 04:26:33'),(72,'BTCUSDT','WAIT',45.6539,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:28:02','2026-05-17 04:28:02'),(73,'BTCUSDT','WAIT',45.6539,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:28:02','2026-05-17 04:28:02'),(74,'ETHUSDT','WAIT',35.9427,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:28:03','2026-05-17 04:28:03'),(75,'ETHUSDT','WAIT',35.9427,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:28:03','2026-05-17 04:28:03'),(76,'BTCUSDT','WAIT',45.7336,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:30:01','2026-05-17 04:30:01'),(77,'BTCUSDT','WAIT',45.7336,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:30:01','2026-05-17 04:30:01'),(78,'ETHUSDT','WAIT',36.3131,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:30:02','2026-05-17 04:30:02'),(79,'ETHUSDT','WAIT',36.3131,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:30:02','2026-05-17 04:30:02'),(80,'BTCUSDT','WAIT',46.2692,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:02','2026-05-17 04:32:02'),(81,'BTCUSDT','WAIT',46.2692,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:02','2026-05-17 04:32:02'),(82,'ETHUSDT','WAIT',36.9163,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:02','2026-05-17 04:32:02'),(83,'ETHUSDT','WAIT',36.9163,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:02','2026-05-17 04:32:02'),(84,'BTCUSDT','WAIT',46.2692,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:18','2026-05-17 04:32:18'),(85,'ETHUSDT','WAIT',36.9302,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:32:19','2026-05-17 04:32:19'),(86,'BTCUSDT','WAIT',45.4403,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:34:02','2026-05-17 04:34:02'),(87,'ETHUSDT','WAIT',36.3980,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:34:02','2026-05-17 04:34:02'),(88,'BTCUSDT','WAIT',45.4403,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:34:02','2026-05-17 04:34:02'),(89,'ETHUSDT','WAIT',36.3980,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:34:07','2026-05-17 04:34:07'),(90,'BTCUSDT','WAIT',44.5798,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:36:01','2026-05-17 04:36:01'),(91,'ETHUSDT','WAIT',35.5681,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:36:01','2026-05-17 04:36:01'),(92,'BTCUSDT','WAIT',44.5742,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:36:01','2026-05-17 04:36:01'),(93,'ETHUSDT','WAIT',35.5681,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:36:02','2026-05-17 04:36:02'),(94,'BTCUSDT','WAIT',45.3328,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:38:01','2026-05-17 04:38:01'),(95,'BTCUSDT','WAIT',45.3328,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:38:01','2026-05-17 04:38:01'),(96,'ETHUSDT','WAIT',36.7631,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:38:02','2026-05-17 04:38:02'),(97,'ETHUSDT','WAIT',36.7631,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:38:02','2026-05-17 04:38:02'),(98,'BTCUSDT','WAIT',45.0243,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:40:02','2026-05-17 04:40:02'),(99,'ETHUSDT','WAIT',37.0273,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:40:02','2026-05-17 04:40:02'),(100,'BTCUSDT','WAIT',45.0243,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:40:02','2026-05-17 04:40:02'),(101,'ETHUSDT','WAIT',37.0273,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:40:03','2026-05-17 04:40:03'),(102,'BTCUSDT','WAIT',44.9698,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:42:02','2026-05-17 04:42:02'),(103,'BTCUSDT','WAIT',44.9698,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:42:03','2026-05-17 04:42:03'),(104,'ETHUSDT','WAIT',36.4262,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:42:03','2026-05-17 04:42:03'),(105,'ETHUSDT','WAIT',36.4262,0.00000000,20,'Market is neutral.',NULL,'2026-05-17 04:42:04','2026-05-17 04:42:04');
/*!40000 ALTER TABLE `signals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trades`
--

DROP TABLE IF EXISTS `trades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `side` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leverage` decimal(8,2) NOT NULL DEFAULT '1.00',
  `entry_price` decimal(20,8) NOT NULL,
  `take_profit` decimal(20,8) DEFAULT NULL,
  `stop_loss` decimal(20,8) DEFAULT NULL,
  `quantity` decimal(20,8) NOT NULL,
  `pnl` decimal(20,8) DEFAULT NULL,
  `fees` decimal(20,8) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPEN',
  `strategy_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tags` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trades`
--

LOCK TABLES `trades` WRITE;
/*!40000 ALTER TABLE `trades` DISABLE KEYS */;
/*!40000 ALTER TABLE `trades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `risk_percentage` decimal(5,2) NOT NULL DEFAULT '10.00',
  `default_leverage` decimal(5,2) NOT NULL DEFAULT '10.00',
  `strategy_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hybrid',
  `auto_trading_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `daily_loss_limit` decimal(5,2) NOT NULL DEFAULT '3.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_settings`
--

LOCK TABLES `user_settings` WRITE;
/*!40000 ALTER TABLE `user_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_settings` ENABLE KEYS */;
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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-16 15:21:44
