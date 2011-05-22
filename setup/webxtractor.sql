-- MySQL dump 10.13  Distrib 5.4.1-beta, for Win32 (ia32)
--
-- Host: localhost    Database: webxtractor
-- ------------------------------------------------------
-- Server version	6.0.11-alpha-community

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
-- Table structure for table `datasource`
--

DROP TABLE IF EXISTS `datasource`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `datasource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `idextractor` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Datasource_Url_Idx` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `datasource`
--

LOCK TABLES `datasource` WRITE;
/*!40000 ALTER TABLE `datasource` DISABLE KEYS */;
INSERT INTO `datasource` VALUES (24,'http://kopen.marktplaats.nl/opensearch.php?b=1&q=kuifje&g=201&u=227&ts=0&pa=1&s=10','www.marktplaats.nl/',2),(3,'http://thepiratebay.org/search/star%20trek/0/99/0','piratebay',1);
/*!40000 ALTER TABLE `datasource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datasourceattribute`
--

DROP TABLE IF EXISTS `datasourceattribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `datasourceattribute` (
  `iddatasource` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `val` varchar(255) NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`iddatasource`,`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `datasourceattribute`
--

LOCK TABLES `datasourceattribute` WRITE;
/*!40000 ALTER TABLE `datasourceattribute` DISABLE KEYS */;
INSERT INTO `datasourceattribute` VALUES (1,'AllowImageBlockOffers','0',1),(1,'AllowLinkBlockOffers','1',1),(1,'MinImageBlockOffers','10',2),(1,'MinLinkBlockOffers','10',2),(1,'OfferTitleFilter','kuifje',3),(1,'MaxLinksToFollow','1',2),(1,'LastRun','1275247723',2),(1,'RunEvery','2',2),(1,'RunStatus','1',2),(3,'MaxLinksToFollow','10',2),(3,'RunEvery','2',2),(4,'AllowImageBlockOffers','1',1),(4,'AllowLinkBlockOffers','0',1),(4,'MinImageBlockOffers','6',2),(4,'MinLinkBlockOffers','0',2),(4,'OfferTitleFilter','',3),(4,'MaxLinksToFollow','10',2),(4,'RunEvery','0',2),(4,'RunStatus','1',2),(26,'AllowImageBlockOffers','1',1),(26,'AllowLinkBlockOffers','0',1),(26,'MinImageBlockOffers','6',2),(26,'MinLinkBlockOffers','999',2),(26,'OfferTitleFilter','',3),(26,'MaxLinksToFollow','10',2),(26,'RunEvery','1',2);
/*!40000 ALTER TABLE `datasourceattribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extractor`
--

DROP TABLE IF EXISTS `extractor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `extractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `extractor`
--

LOCK TABLES `extractor` WRITE;
/*!40000 ALTER TABLE `extractor` DISABLE KEYS */;
INSERT INTO `extractor` VALUES (1,'HTML Extractor'),(2,'FEED Extractor');
/*!40000 ALTER TABLE `extractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extractorattribute`
--

DROP TABLE IF EXISTS `extractorattribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `extractorattribute` (
  `idextractor` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `val` varchar(255) NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`idextractor`,`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `extractorattribute`
--

LOCK TABLES `extractorattribute` WRITE;
/*!40000 ALTER TABLE `extractorattribute` DISABLE KEYS */;
INSERT INTO `extractorattribute` VALUES (1,'AllowImageBlockOffers','0',1),(1,'MinImageBlockOffers','6',2),(1,'AllowLinkBlockOffers','1',1),(1,'MinLinkBlockOffers','10',2),(1,'OfferTitleFilter','',3),(2,'OfferTitleFilter','',3);
/*!40000 ALTER TABLE `extractorattribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `idparent` int(10) unsigned NOT NULL,
  `iduser` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
INSERT INTO `item` VALUES (1,'Kuifje',0,1),(2,'Kuifje in Amerika',1,1),(3,'Kuifje in Afrika',1,1),(4,'Star Trek Movies',0,1),(5,'Wrath of Khan',4,1),(6,'De Zwarte Rotsen',1,1),(7,'De Zonnetempel',1,1),(12,'Search for Spock',4,1),(9,'Britney Spears Gallery',0,1),(10,'Mannen op de maan',1,1),(13,'De sigaren van de farao',1,1),(14,'De Blauwe Lotus',1,1),(15,'Het gebroken oor',1,1),(16,'De Zwarte Rotsen',1,1),(17,'De scepter van Ottokar',1,1),(18,'De krab met de gulden scharen',1,1),(19,'De geheimzinnige ster',1,1),(20,'Het geheim van de Eenhoorn',1,1),(21,'Motion Picture',4,1),(22,'The Voyage Home',4,1),(23,'The Final Frontier',4,1),(24,'The Undiscovered Country',4,1),(25,'Generations',4,1),(26,'First Contact',4,1),(27,'Insurrection',4,1),(28,'Nemesis',4,1);
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_datasource`
--

DROP TABLE IF EXISTS `item_datasource`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `item_datasource` (
  `iditem` int(10) unsigned NOT NULL,
  `iddatasource` int(10) unsigned NOT NULL,
  `interval` int(10) unsigned NOT NULL DEFAULT '0',
  `minmatchscore` int(10) unsigned NOT NULL DEFAULT '65',
  `createitemonnomatch` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`iditem`,`iddatasource`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `item_datasource`
--

LOCK TABLES `item_datasource` WRITE;
/*!40000 ALTER TABLE `item_datasource` DISABLE KEYS */;
INSERT INTO `item_datasource` VALUES (1,24,1,65,0),(4,3,0,65,0),(9,4,0,0,0),(9,26,1,65,0);
/*!40000 ALTER TABLE `item_datasource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_offer`
--

DROP TABLE IF EXISTS `item_offer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `item_offer` (
  `iditem` int(10) unsigned NOT NULL,
  `idoffer` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `confidence` int(10) unsigned NOT NULL,
  `shown` int(10) unsigned NOT NULL DEFAULT '0',
  `rank` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`iditem`,`idoffer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `item_offer`
--

LOCK TABLES `item_offer` WRITE;
/*!40000 ALTER TABLE `item_offer` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_offer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemattribute`
--

DROP TABLE IF EXISTS `itemattribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `itemattribute` (
  `iditem` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `val` varchar(255) NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`iditem`,`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `itemattribute`
--

LOCK TABLES `itemattribute` WRITE;
/*!40000 ALTER TABLE `itemattribute` DISABLE KEYS */;
INSERT INTO `itemattribute` VALUES (1,'author','herge',1);
/*!40000 ALTER TABLE `itemattribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer`
--

DROP TABLE IF EXISTS `offer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `offer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Offer_Url_idx` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=1725 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `offer`
--

LOCK TABLES `offer` WRITE;
/*!40000 ALTER TABLE `offer` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `passwd` varchar(64) NOT NULL,
  `fullname` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `quotum_offers` int(10) unsigned NOT NULL,
  `quotum_indexruns` int(10) unsigned NOT NULL,
  `current_offers` int(10) unsigned NOT NULL,
  `current_indexruns` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'demo','563412','Demo User','demo@nomail',100,10,0,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-18 20:51:41
