-- MySQL dump 10.13  Distrib 5.7.29, for Linux (x86_64)
--
-- Host: localhost    Database: db_projet_2
-- ------------------------------------------------------
-- Server version	5.7.29-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `description` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='\n';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (1,'Les chambres meublées (FJT, 29 rue du colombiers)','FJT, 29 rue du colombiers','Formule 1/2 pension (petit-déjeuner + un forfait restauration)\nChambres meublées (lit, placard-penderie, bureau, chaise, commode, réfrigérateur)'),(2,'Les appartements (FJT, 50 rue d\'illiers)','FJT, 50 rue d\'illiers','\n    Appartements meublées (lit 1 ou 2 personnes, table, chaise, commode)\n    Cuisine équipée (plaques électriques, réfrigérateur, meuble cuisine)\n    Toutes les charges sont incluses (chauffage, électricité, eau)\n    Les impôts locaux sont à la charge du résident.\n'),(3,'appartements (Résidence Riobé, 2 boulevard Guy-Marie RIOBE)','Résidence Riobé, 2 boulevard Guy-Marie RIOBE','\n    Appartements meublées (lit 1 ou 2 personnes, table, chaise, tabouret, commode, meuble desserte)\n    Cuisine équipée (plaques électriques, réfrigérateur, meuble cuisine)\n    Toutes les charges sont incluses (chauffage, électricité, eau).\n    Possibilité de restauration au Colombier.\n');
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `guarantee` float NOT NULL,
  `equipment` varchar(100) DEFAULT NULL,
  `restoration` float DEFAULT NULL,
  `contribution` float DEFAULT NULL,
  `total` float NOT NULL,
  `breakfast` varchar(45) DEFAULT NULL,
  `equipment_contribution` float(4,2) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `address_id` int(11) NOT NULL,
  `area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`address_id`),
  KEY `fk_room_address_idx` (`address_id`),
  CONSTRAINT `fk_room_address` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room`
--

LOCK TABLES `room` WRITE;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
INSERT INTO `room` VALUES (1,'Anciennes chambres',238,'lavabo',99.36,1.55,344.35,'inclus',5.44,NULL,1,NULL),(2,'Nouvelles chambres',276,'sanitaire',99.36,1.55,401.16,'18.81',5.44,NULL,1,NULL),(3,'Nouvelles chambres',292,'sanitaire',99.36,1.55,417.16,'18.81',5.44,NULL,1,NULL),(4,'studio',324,NULL,23,1.55,348.55,NULL,NULL,NULL,2,20),(5,'T1',341,NULL,23,1.55,365.55,NULL,NULL,NULL,2,25),(6,'T1 bis',395,NULL,23,1.55,419.55,NULL,NULL,NULL,2,35),(7,'T1',293,NULL,NULL,6.99,303.1,NULL,3.11,NULL,3,20),(8,'T1',304,NULL,NULL,6.99,314.1,NULL,3.11,NULL,3,22),(9,'T1',317,NULL,NULL,6.99,327.1,NULL,3.11,NULL,3,24),(10,'T1 bis',349,NULL,NULL,6.99,359.1,NULL,3.11,NULL,3,32);
/*!40000 ALTER TABLE `room` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-15 14:19:46
