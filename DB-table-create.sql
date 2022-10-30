-- MySQL dump 10.13  Distrib 8.0.27, for macos11 (x86_64)
--
-- Host: 127.0.0.1    Database: mms
-- ------------------------------------------------------
-- Server version	8.0.29

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
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genres` (
  `gnr_id` int NOT NULL AUTO_INCREMENT,
  `gnr_name` varchar(200) DEFAULT NULL,
  `gnr_featured` int DEFAULT '0',
  PRIMARY KEY (`gnr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `img_id` int NOT NULL AUTO_INCREMENT,
  `img_path` varchar(200) DEFAULT NULL,
  `img_ref_movie` int DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`img_id`),
  KEY `fk_img_ref_movie` (`img_ref_movie`),
  CONSTRAINT `fk_img_ref_movie` FOREIGN KEY (`img_ref_movie`) REFERENCES `movies` (`mv_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movies`
--

DROP TABLE IF EXISTS `movies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movies` (
  `mv_id` int NOT NULL AUTO_INCREMENT,
  `mv_title` varchar(200) DEFAULT NULL,
  `mv_year_released` date DEFAULT NULL,
  `mv_featured` int DEFAULT '0',
  `mv_synopsis` varchar(255) DEFAULT NULL,
  `mv_guid` varchar(45) NOT NULL,
  PRIMARY KEY (`mv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mv_genres`
--

DROP TABLE IF EXISTS `mv_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_genres` (
  `mvg_id` int NOT NULL AUTO_INCREMENT,
  `mvg_ref_genre` int DEFAULT NULL,
  `mvg_ref_movie` int DEFAULT NULL,
  PRIMARY KEY (`mvg_id`),
  KEY `fk_mvg_ref_genre` (`mvg_ref_genre`),
  KEY `fk_mvg_ref_movie` (`mvg_ref_movie`),
  CONSTRAINT `fk_mvg_ref_genre` FOREIGN KEY (`mvg_ref_genre`) REFERENCES `genres` (`gnr_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mvg_ref_movie` FOREIGN KEY (`mvg_ref_movie`) REFERENCES `movies` (`mv_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mv_ratings`
--

DROP TABLE IF EXISTS `mv_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_ratings` (
  `mvr_id` int NOT NULL AUTO_INCREMENT,
  `mvr_ref_rating` int DEFAULT NULL,
  `mvr_ref_movie` int DEFAULT NULL,
  PRIMARY KEY (`mvr_id`),
  KEY `fk_mvr_ref_rating` (`mvr_ref_rating`),
  KEY `fk_mvr_ref_movie` (`mvr_ref_movie`),
  CONSTRAINT `fk_mvr_ref_movie` FOREIGN KEY (`mvr_ref_movie`) REFERENCES `movies` (`mv_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mvr_ref_rating` FOREIGN KEY (`mvr_ref_rating`) REFERENCES `ratings` (`rating_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `rating_id` int NOT NULL AUTO_INCREMENT,
  `rating` varchar(45) DEFAULT NULL,
  `rating_desc` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `role` varchar(200) NOT NULL,
  `photo_img` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

