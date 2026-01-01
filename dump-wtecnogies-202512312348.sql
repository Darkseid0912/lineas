-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: wtecnogies
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
-- Table structure for table `cat_companies`
--

DROP TABLE IF EXISTS `cat_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tax_id` varchar(20) DEFAULT NULL,
  `business_name` varchar(200) NOT NULL,
  `trade_name` varchar(200) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tax_id` (`tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_companies`
--

LOCK TABLES `cat_companies` WRITE;
/*!40000 ALTER TABLE `cat_companies` DISABLE KEYS */;
INSERT INTO `cat_companies` VALUES (5,'tex123','Comercio','wTecnologies','jesus.velasco091294@gmail.com','9191564508','ave 1a sur poninete','rayon','México',1,'2025-12-28 04:32:26','2025-12-27 23:05:41',NULL),(6,'12345','ventas','angel tecnologies','angel@gmail.com',NULL,NULL,NULL,NULL,1,'2025-12-28 05:07:53','2025-12-28 05:07:53',NULL);
/*!40000 ALTER TABLE `cat_companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_modules`
--

DROP TABLE IF EXISTS `cat_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) NOT NULL,
  `description` text,
  `slug` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_modules`
--

LOCK TABLES `cat_modules` WRITE;
/*!40000 ALTER TABLE `cat_modules` DISABLE KEYS */;
INSERT INTO `cat_modules` VALUES (1,'Usuarios','Este modulo esta relacionado con temas de administración de usuarios','Uusuarios',1,'2025-12-28 16:01:24','2025-12-31 23:00:38',NULL),(2,'Roles','En este modulo se tratan temas de los roles','Rol',1,'2025-12-28 16:02:31',NULL,NULL);
/*!40000 ALTER TABLE `cat_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_permissions`
--

DROP TABLE IF EXISTS `cat_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) NOT NULL,
  `key` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_permissions`
--

LOCK TABLES `cat_permissions` WRITE;
/*!40000 ALTER TABLE `cat_permissions` DISABLE KEYS */;
INSERT INTO `cat_permissions` VALUES (3,'Ver','view','2026-01-01 03:34:25','2026-01-01 03:34:25',NULL),(4,'Editar','edit','2026-01-01 03:34:33','2026-01-01 03:34:33',NULL),(5,'Eliminar','delete','2026-01-01 03:34:43','2026-01-01 03:34:43',NULL),(6,'Agregar','create','2026-01-01 03:34:51','2026-01-01 03:34:51',NULL),(8,'Detalles','details','2026-01-01 05:29:29',NULL,NULL);
/*!40000 ALTER TABLE `cat_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_users`
--

DROP TABLE IF EXISTS `cat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_companies` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_users_cat_companies_FK` (`id_companies`),
  CONSTRAINT `cat_users_cat_companies_FK` FOREIGN KEY (`id_companies`) REFERENCES `cat_companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_users`
--

LOCK TABLES `cat_users` WRITE;
/*!40000 ALTER TABLE `cat_users` DISABLE KEYS */;
INSERT INTO `cat_users` VALUES (17,'Administrador','2025-12-28 04:32:26','2025-12-28 04:32:26',NULL,5),(19,'Administrador','2025-12-28 05:07:53','2025-12-28 05:07:53',NULL,6),(22,'Secretari@','2026-01-01 04:27:17','2026-01-01 04:27:17',NULL,5);
/*!40000 ALTER TABLE `cat_users` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1);
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (5,'App\\Models\\User',1,'api','334a801755b0fac5963128809f34a052c99caa6fa30035ae3e8fe0fd77a2bd09','[\"*\"]',NULL,NULL,'2025-12-26 23:33:58','2025-12-26 23:33:58'),(14,'App\\Models\\User',2,'api','6e5323ab3b79b628793be8387ebdc94c33109e4bd2f211c9b5787fd44c9ab826','[\"*\"]','2025-12-28 03:07:43',NULL,'2025-12-28 02:47:54','2025-12-28 03:07:43'),(15,'App\\Models\\User',3,'api','eb5a054dd6f28333da544749eef3d85c51b28fd6a1ec126e666979c10dea39e9','[\"*\"]','2025-12-28 03:48:37',NULL,'2025-12-28 03:47:26','2025-12-28 03:48:37'),(17,'App\\Models\\User',4,'api','5559cf62d3cbe02faba32922ce4e531e4f25e15a35a3d34b228ae5aa6f8c5778','[\"*\"]','2025-12-28 04:30:19',NULL,'2025-12-28 04:04:54','2025-12-28 04:30:19');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rel_role_permissions`
--

DROP TABLE IF EXISTS `rel_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rel_role_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_role` int NOT NULL,
  `id_module` int NOT NULL,
  `id_permission` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_role` (`id_role`),
  KEY `id_module` (`id_module`),
  KEY `id_permission` (`id_permission`),
  CONSTRAINT `rel_role_permissions_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `cat_users` (`id`),
  CONSTRAINT `rel_role_permissions_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `cat_modules` (`id`),
  CONSTRAINT `rel_role_permissions_ibfk_3` FOREIGN KEY (`id_permission`) REFERENCES `cat_permissions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rel_role_permissions`
--

LOCK TABLES `rel_role_permissions` WRITE;
/*!40000 ALTER TABLE `rel_role_permissions` DISABLE KEYS */;
INSERT INTO `rel_role_permissions` VALUES (62,22,1,3,'2026-01-01 11:33:18','2026-01-01 11:33:18',NULL),(63,22,1,8,'2026-01-01 11:33:59','2026-01-01 11:33:59',NULL),(64,22,1,4,'2026-01-01 11:33:59','2026-01-01 11:33:59',NULL),(65,22,1,5,'2026-01-01 11:33:59','2026-01-01 11:33:59',NULL),(66,22,1,6,'2026-01-01 11:35:28','2026-01-01 11:35:28',NULL);
/*!40000 ALTER TABLE `rel_role_permissions` ENABLE KEYS */;
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
  `type` int NOT NULL,
  `id_companies` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_cat_users_FK` (`type`),
  KEY `users_cat_companies_FK` (`id_companies`),
  CONSTRAINT `users_cat_companies_FK` FOREIGN KEY (`id_companies`) REFERENCES `cat_companies` (`id`),
  CONSTRAINT `users_cat_users_FK` FOREIGN KEY (`type`) REFERENCES `cat_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'Jesus Velasco Bautista','jesus.velasco091294@gmail.com',NULL,'$2y$12$dUpB5KVjzOBlNukn.SKEreVQUGIoQ2Kz4RDXQuQhVURaQf1ea3YbO',NULL,'2025-12-28 04:32:27','2025-12-28 04:32:27',17,5),(6,'Angel velasco','angel@gmail.com',NULL,'$2y$12$dUpB5KVjzOBlNukn.SKEreVQUGIoQ2Kz4RDXQuQhVURaQf1ea3YbO',NULL,'2025-12-28 05:07:53','2025-12-28 05:07:53',19,6),(8,'Edgar Jesus Velasco Vazquez','edgar@gmail.com',NULL,'$2y$12$bmi/UVYSoDkY5e.ZamvZOemKKTT1osKYdbvWJLaR/reOOclmfoRsu',NULL,'2026-01-01 04:54:06','2026-01-01 04:54:06',22,5);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'wtecnogies'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-31 23:48:02
