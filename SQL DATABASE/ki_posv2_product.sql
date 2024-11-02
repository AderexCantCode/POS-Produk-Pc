-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: ki_posv2
-- ------------------------------------------------------
-- Server version	8.0.38

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
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (3,'GTX 1650',3,2500000.00,7,'2024-09-27 21:39:08','../../../../Industri/POS_ProjekAKhirV2/assets/img/gtx1650.jpg'),(4,'Aisurix AMD Radeon RX 580',3,1300000.00,0,'2024-09-27 21:40:53','../../../../Industri/POS_ProjekAKhirV2/assets/img/rx580.jpg'),(5,'Ryzen 5 5600x',2,2300000.00,0,'2024-09-27 21:41:15','../../../../Industri/POS_ProjekAKhirV2/assets/img/r5.jpeg'),(6,'NVME Samsung Evo 500GB',1,987000.00,8,'2024-10-01 20:48:47','../../../../Industri/POS_ProjekAKhirV2/assets/img/samsun.jpeg'),(7,'Gigabyte AORUS ELITE B550M',4,2000000.00,2,'2024-10-01 20:49:49','../../../../Industri/POS_ProjekAKhirV2/assets/img/b550m.jpeg'),(8,'Ryzen 5 3600',2,1300000.00,27,'2024-10-01 20:52:34','../../../../Industri/POS_ProjekAKhirV2/assets/img/r51.jpeg'),(9,'Logitech G PRO Wireless',11,1200000.00,12,'2024-10-01 20:52:58','../../../../Industri/POS_ProjekAKhirV2/assets/img/logitech.jpeg'),(10,'GAMEN TITAN ELITE ',11,200000.00,13,'2024-10-01 20:53:18','../../../../Industri/POS_ProjekAKhirV2/assets/img/gamen titan.jpeg'),(11,'Be Quiet! 500 WATT',5,700000.00,1,'2024-10-01 20:54:52','../../../../Industri/POS_ProjekAKhirV2/assets/img/bequiet.jpeg');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-05  9:32:51
