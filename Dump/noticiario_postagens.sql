CREATE DATABASE  IF NOT EXISTS `noticiario` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `noticiario`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: noticiario
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `texto` mediumtext NOT NULL,
  `data_post` datetime DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `postagens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postagens`
--

LOCK TABLES `postagens` WRITE;
/*!40000 ALTER TABLE `postagens` DISABLE KEYS */;
INSERT INTO `postagens` VALUES (18,0,'teste','<p>teste</p>','2025-06-25 09:53:06',NULL),(19,0,'teste','<p>teste</p>','2025-06-25 15:06:20',NULL),(20,0,'teste','<p>stet</p>','2025-06-25 15:06:58',NULL),(21,0,'tesetst','<p>setysetse</p>','2025-06-25 15:07:00',NULL),(22,0,'setsetsetes','<p>tsetsetse</p>','2025-06-25 15:07:03',NULL),(23,0,'estsetsetsetse','<p>tsetsetsetset</p>','2025-06-25 15:07:06',NULL),(24,0,'asfasdasd','<p>asdfagfasdfasdasd</p>','2025-06-25 15:07:29',NULL),(25,0,'asdasfadfas','<p>dasdasdafgasdas</p>','2025-06-25 15:07:32',NULL),(26,0,'dasdasdasd','<p>asdasdasdas</p>','2025-06-25 15:07:34',NULL),(27,0,'asdasdasdas','<p>dasdasdasdas</p>','2025-06-25 15:07:37',NULL),(28,0,'sdasdasdasda','<p>ssdasdasdas</p>','2025-06-25 15:07:39',NULL),(29,0,'asdasdasdas','<p>dasdasdasda</p>','2025-06-25 15:07:42',NULL),(30,0,'asdasdasd','<p>asdasdasdasd</p>','2025-06-25 15:07:45',NULL),(31,0,'asdasdasdasd','<p>asdasdasdasd</p>','2025-06-25 15:07:47',NULL),(32,0,'a','b','2025-06-25 15:11:24',NULL),(33,0,'a','b','2025-06-25 15:11:25',NULL),(34,0,'a','b','2025-06-25 15:11:25',NULL),(35,0,'a','b','2025-06-25 15:11:25',NULL),(36,0,'a','b','2025-06-25 15:11:26',NULL),(37,0,'a','b','2025-06-25 15:11:26',NULL),(38,0,'a','b','2025-06-25 15:11:26',NULL),(39,0,'a','b','2025-06-25 15:11:26',NULL),(40,0,'a','b','2025-06-25 15:11:26',NULL),(41,0,'a','b','2025-06-25 15:11:26',NULL),(42,0,'a','b','2025-06-25 15:11:27',NULL),(43,0,'a','b','2025-06-25 15:11:27',NULL),(44,0,'a','b','2025-06-25 15:11:27',NULL),(45,0,'a','b','2025-06-25 15:11:27',NULL),(46,0,'a','b','2025-06-25 15:11:27',NULL),(47,0,'a','b','2025-06-25 15:11:27',NULL),(48,0,'a','b','2025-06-25 15:11:28',NULL),(49,0,'a','b','2025-06-25 15:11:28',NULL),(50,0,'a','b','2025-06-25 15:11:28',NULL),(51,0,'a','b','2025-06-25 15:11:28',NULL),(52,0,'a','b','2025-06-25 15:11:28',NULL),(53,0,'a','b','2025-06-25 15:11:28',NULL),(54,0,'a','b','2025-06-25 15:11:29',NULL),(55,0,'a','b','2025-06-25 15:11:29',NULL),(56,0,'a','b','2025-06-25 15:11:29',NULL),(57,0,'a','b','2025-06-25 15:11:29',NULL),(58,0,'a','b','2025-06-25 15:11:29',NULL),(59,0,'a','b','2025-06-25 15:11:29',NULL),(60,0,'a','b','2025-06-25 15:11:30',NULL),(61,0,'a','b','2025-06-25 15:11:30',NULL),(62,0,'a','b','2025-06-25 15:11:30',NULL),(63,0,'a','b','2025-06-25 15:11:30',NULL),(64,0,'a','b','2025-06-25 15:11:30',NULL),(65,0,'a','b','2025-06-25 15:11:30',NULL),(66,0,'a','b','2025-06-25 15:11:30',NULL),(67,0,'a','b','2025-06-25 15:11:31',NULL),(68,0,'a','b','2025-06-25 15:11:31',NULL),(69,0,'a','b','2025-06-25 15:11:31',NULL),(70,0,'a','b','2025-06-25 15:11:31',NULL),(71,0,'a','b','2025-06-25 15:11:31',NULL),(72,0,'a','b','2025-06-25 15:11:31',NULL),(73,0,'a','b','2025-06-25 15:11:32',NULL),(74,0,'a','b','2025-06-25 15:11:32',NULL),(75,0,'a','b','2025-06-25 15:11:32',NULL),(76,0,'a','b','2025-06-25 15:11:32',NULL),(77,0,'a','b','2025-06-25 15:11:32',NULL),(78,0,'a','b','2025-06-25 15:11:33',NULL),(79,0,'a','b','2025-06-25 15:11:33',NULL),(80,0,'a','b','2025-06-25 15:11:33',NULL),(81,0,'a','b','2025-06-25 15:11:33',NULL),(82,0,'a','b','2025-06-25 15:11:33',NULL),(83,0,'a','b','2025-06-25 15:11:33',NULL),(84,0,'a','b','2025-06-25 15:11:34',NULL),(85,0,'a','b','2025-06-25 15:11:34',NULL),(86,0,'a','b','2025-06-25 15:11:34',NULL),(87,0,'a','b','2025-06-25 15:11:34',NULL),(88,0,'a','b','2025-06-25 15:11:34',NULL),(89,0,'a','b','2025-06-25 15:11:35',NULL),(90,0,'a','b','2025-06-25 15:11:35',NULL),(91,0,'a','b','2025-06-25 15:11:35',NULL),(92,0,'a','b','2025-06-25 15:11:35',NULL),(93,0,'a','b','2025-06-25 15:11:35',NULL),(94,0,'a','b','2025-06-25 15:11:35',NULL),(95,0,'a','b','2025-06-25 15:11:36',NULL),(96,0,'a','b','2025-06-25 15:11:36',NULL),(97,0,'a','b','2025-06-25 15:11:36',NULL),(98,0,'a','b','2025-06-25 15:11:36',NULL),(99,0,'a','b','2025-06-25 15:11:36',NULL),(100,0,'a','b','2025-06-25 15:11:37',NULL),(101,0,'a','b','2025-06-25 15:11:37',NULL),(102,0,'a','b','2025-06-25 15:11:37',NULL),(103,0,'a','b','2025-06-25 15:11:37',NULL),(104,0,'a','b','2025-06-25 15:11:37',NULL),(105,0,'a','b','2025-06-25 15:11:37',NULL),(106,0,'a','b','2025-06-25 15:11:38',NULL),(107,0,'a','b','2025-06-25 15:11:38',NULL),(108,0,'a','b','2025-06-25 15:11:38',NULL),(109,0,'a','b','2025-06-25 15:11:38',NULL),(110,0,'a','b','2025-06-25 15:11:38',NULL),(111,0,'a','b','2025-06-25 15:11:38',NULL),(112,0,'a','b','2025-06-25 15:11:39',NULL),(113,0,'a','b','2025-06-25 15:11:39',NULL),(114,0,'a','b','2025-06-25 15:11:39',NULL),(115,0,'a','b','2025-06-25 15:11:39',NULL),(116,0,'a','b','2025-06-25 15:11:39',NULL),(117,0,'a','b','2025-06-25 15:11:40',NULL),(118,0,'a','b','2025-06-25 15:11:40',NULL),(119,0,'a','b','2025-06-25 15:11:40',NULL),(120,0,'a','b','2025-06-25 15:11:40',NULL),(121,0,'a','b','2025-06-25 15:11:40',NULL),(122,0,'a','b','2025-06-25 15:11:40',NULL),(123,0,'a','b','2025-06-25 15:11:41',NULL),(124,0,'teste','<p>teste</p>','2025-06-25 15:13:28',NULL),(125,0,'teste','<p>teste</p>','2025-06-25 15:13:31',NULL),(126,0,'teste','<p>teste</p>','2025-06-25 15:13:33',NULL),(127,0,'teste','<p>teste</p>','2025-06-25 15:13:35',NULL),(128,0,'teste','<p>teste</p>','2025-06-25 15:13:54',NULL),(129,0,'teste','<p>teste</p>','2025-06-25 15:13:56',NULL),(130,0,'teste','<p>teste</p>','2025-06-25 15:13:59',NULL),(131,0,'teste','<p>teste</p>','2025-06-25 15:14:01',NULL),(134,18,'teste','<p>teste</p>','2025-07-09 09:50:04',NULL);
/*!40000 ALTER TABLE `postagens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-09 10:54:59
