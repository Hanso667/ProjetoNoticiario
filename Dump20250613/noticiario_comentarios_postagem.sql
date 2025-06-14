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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios_postagem`
--

LOCK TABLES `comentarios_postagem` WRITE;
/*!40000 ALTER TABLE `comentarios_postagem` DISABLE KEYS */;
INSERT INTO `comentarios_postagem` VALUES (12,10,3,'<p>primeiro comentario</p>','2025-06-13 20:22:29',NULL),(13,10,4,'<p>não gostei</p>','2025-06-13 20:59:12',NULL),(14,10,5,'<p>ah eu curti</p>','2025-06-13 21:04:36',NULL),(15,11,5,'<p>Lorem ipsum dolor sit amet. Et molestiae fugiat sit sint cumque quo impedit nostrum est saepe numquam hic architecto dolorem est adipisci voluptatum qui harum consequatur. Aut voluptatem voluptate et quisquam accusamus est velit inventore. Aut pariatur obcaecati in voluptatem magni est magni quae non explicabo voluptas et iusto ipsa. A voluptas officia aut dolorem magni sit corrupti obcaecati cum enim internos ut quia illo qui suscipit sint aut quis nisi!</p><p>Vel labore voluptatem ut quidem fugit quo fugiat impedit! Et fugit labore et fugiat laborum sit aspernatur illo. Et totam minus in nostrum aspernatur sed saepe asperiores ut magnam blanditiis ut distinctio rerum hic sequi repellendus? Ab fugit numquam sit placeat esse ab unde illum et sequi amet et omnis distinctio ut officiis doloremque sit voluptatem Quis.</p><p>Qui repellendus quidem quo ratione nostrum rem voluptas omnis! Id assumenda pariatur sit inventore dolores est magnam vero non debitis nisi? Eos corporis dignissimos At voluptatem velit qui obcaecati incidunt est eligendi error a adipisci voluptas a nisi totam! Ex tenetur dolorum aut enim saepe qui quisquam odit aut atque minima quo unde repellat et autem ipsa.</p>','2025-06-13 21:07:20',NULL),(16,11,3,'<p>Lorem ipsum dolor sit amet. Et molestiae fugiat sit sint cumque quo impedit nostrum est saepe numquam hic architecto dolorem est adipisci voluptatum qui harum consequatur. Aut voluptatem voluptate et quisquam accusamus est velit inventore. Aut pariatur obcaecati in voluptatem magni est magni quae non explicabo voluptas et iusto ipsa. A voluptas officia aut dolorem magni sit corrupti obcaecati cum enim internos ut quia illo qui suscipit sint aut quis nisi!</p><p>Vel labore voluptatem ut quidem fugit quo fugiat impedit! Et fugit labore et fugiat laborum sit aspernatur illo. Et totam minus in nostrum aspernatur sed saepe asperiores ut magnam blanditiis ut distinctio rerum hic sequi repellendus? Ab fugit numquam sit placeat esse ab unde illum et sequi amet et omnis distinctio ut officiis doloremque sit voluptatem Quis.</p><p>Qui repellendus quidem quo ratione nostrum rem voluptas omnis! Id assumenda pariatur sit inventore dolores est magnam vero non debitis nisi? Eos corporis dignissimos At voluptatem velit qui obcaecati incidunt est eligendi error a adipisci voluptas a nisi totam! Ex tenetur dolorum aut enim saepe qui quisquam odit aut atque minima quo unde repellat et autem ipsa.</p>','2025-06-13 21:07:39',NULL);
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

-- Dump completed on 2025-06-13 21:50:08
