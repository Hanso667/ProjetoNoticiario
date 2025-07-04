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
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(75) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (0,'Administrador','adm@adm.com','$2y$10$JF3B1RP.qesSsiVxc5iSiOixrzhpbMMXsR/eb42lR2hIMP/INOWx.','img_685beb9114be44.88959212.jpg'),(16,'teste2','teste2@teste2.com','$2y$10$L8IKo15sW0B0n3/P88ItReeVZY3YlqRpwzLdYniZbvwd1yiEr8HL2','img_685c5cf90e5e74.29522737.png'),(17,'teste3','teste3@teste3.com','$2y$10$frd5/s/aAICS5t9oyGKjnuMNQ9NWOYQmO9m/AF2g/rq2.sLR10z8.','img_685c5d463f64c7.95436744.png'),(18,'teste4','teste4@teste4.com','$2y$10$clTY.qCdCK5EJ57CBFZVguGH/6PxDdfOdYpGZiZ3UDV6pBZ4ZxmCG','img_685c5d56cb5241.30364595.png'),(19,'teste5','teste5@teste5.com','$2y$10$OSxwwJQdHozBaj/ODSc2O.w5BYyd3wqEu.c5WzHaphPm9G2wwH5K2','img_685c5d76c819c9.70409472.png'),(20,'teste6','teste6@teste6.com','$2y$10$lkqUr8afqxNlqJgbawkFZ.segIwdp3aiPNwpoeOdnHQcP5yGBgmP.','img_685c5d86496522.80381820.png'),(21,'teste7','teste7@teste7.com','$2y$10$jaBOqUREuCgQ3OVOzp1xN.kyuJJcQrC1Va/Bxomv73ArmOkCrLF3G','img_685c5d997d1980.03462438.png');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-25 17:49:46
