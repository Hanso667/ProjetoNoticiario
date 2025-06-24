CREATE DATABASE  IF NOT EXISTS `noticiario` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `noticiario`;
-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
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
-- Table structure for table `comentarios_postagem`
--

DROP TABLE IF EXISTS `comentarios_postagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios_postagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `data_comentario` datetime DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_post` (`id_post`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `comentarios_postagem_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `postagens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_postagem_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_postagem`
--

LOCK TABLES `comentarios_postagem` WRITE;
/*!40000 ALTER TABLE `comentarios_postagem` DISABLE KEYS */;
INSERT INTO `comentarios_postagem` VALUES (23,17,12,'<p class=\"ql-align-justify\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce a quam tristique, finibus massa at, elementum quam. Maecenas sodales magna quis commodo pretium. Fusce efficitur risus lorem, consequat ullamcorper tortor suscipit ut. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Sed luctus sagittis nunc, ac imperdiet neque gravida commodo. Curabitur ut enim eget nisl dapibus congue. Fusce purus risus, aliquam id imperdiet in, malesuada sollicitudin dolor. Etiam egestas ipsum tellus, non rhoncus eros mattis ut. Mauris sit amet efficitur purus. Nulla facilisis nibh tortor, id vulputate nisi scelerisque sed. Proin blandit, justo sit amet hendrerit rutrum, purus dui condimentum nunc, in luctus eros ligula at tortor. Duis interdum consequat leo eu elementum. Nullam eleifend dolor eget nisl auctor, nec ultrices augue rutrum.</p><p class=\"ql-align-justify\">Nulla nec eros mi. Maecenas quis libero ut dui dignissim consequat. Fusce nec magna dictum, dignissim metus non, vehicula dolor. Morbi et sapien ornare, dictum velit eget, tristique arcu. Duis sit amet elit id augue hendrerit facilisis et nec nulla. Donec sed pharetra purus. Donec fringilla felis efficitur bibendum congue. Suspendisse maximus finibus convallis. Phasellus dignissim lacus in felis pretium, quis efficitur eros consectetur. Duis ut magna aliquam, finibus metus pretium, tempus ipsum. Nam scelerisque vulputate dui, sit amet aliquet eros varius quis. Pellentesque condimentum sem libero, aliquet sodales eros suscipit ac.</p><p class=\"ql-align-justify\">Morbi rutrum molestie lacus, eget vestibulum diam vestibulum sit amet. Donec auctor dolor eu ligula placerat aliquam. Maecenas pellentesque arcu ac dapibus mollis. Vivamus congue orci vel elit ornare dapibus. Pellentesque quis tortor id nisl luctus rhoncus a id orci. Ut elit enim, semper at pellentesque et, viverra sit amet leo. Phasellus tincidunt augue quis dapibus aliquet. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur porta, risus iaculis venenatis pulvinar, purus diam finibus velit, nec faucibus tellus sem vel neque. Nulla hendrerit euismod mi ut cursus. Vivamus id finibus ex. Proin at nisl diam. Aliquam tincidunt convallis dolor vitae hendrerit. Phasellus non consectetur ipsum. Pellentesque varius nisl in nisi dapibus, a condimentum dolor rutrum. Sed hendrerit purus ac dolor placerat, ut fringilla dui pharetra.</p><p class=\"ql-align-justify\">Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla et mauris et arcu tincidunt pellentesque id et neque. Donec id erat id sem euismod pretium vel at massa. Praesent non orci finibus, condimentum sapien sed, pulvinar eros. Donec mollis nunc vitae facilisis laoreet. Fusce sed magna sed erat dapibus dignissim a eu nulla. Praesent eu nisi orci. Pellentesque efficitur enim risus, et blandit turpis semper a. Morbi nec aliquam risus, sed euismod risus. Duis condimentum varius turpis, et facilisis turpis fringilla ut. Integer non gravida enim. Phasellus quis velit condimentum, mattis nibh sed, egestas enim. Proin non porttitor lacus, quis feugiat est. Nam mattis nec turpis a fringilla.</p><p class=\"ql-align-justify\">Etiam consequat dictum nibh, at cursus tortor consequat sed. Morbi elementum erat eget cursus egestas. Proin porta hendrerit augue, non lobortis eros mollis eu. Vivamus sed molestie mauris, quis molestie sapien. Sed tempus id purus eu accumsan. Nulla eu lorem molestie, placerat tortor eu, porttitor velit. Quisque eu ante nibh. Phasellus eu massa laoreet, pulvinar tortor at, pretium mauris. Donec ut nunc nunc. Suspendisse a dapibus augue. Nulla rhoncus ac eros quis laoreet. Fusce egestas risus vitae quam bibendum euismod. Nunc consectetur egestas nisl, et rutrum felis ornare non.</p><p><br></p>','2025-06-23 21:46:09',NULL);
/*!40000 ALTER TABLE `comentarios_postagem` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-23 22:11:42
