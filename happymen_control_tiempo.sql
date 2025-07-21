-- MariaDB dump 10.19  Distrib 10.6.17-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: happymen_control_tiempo
-- ------------------------------------------------------
-- Server version	10.6.17-MariaDB-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` (`id`, `nombre`, `telefono`) VALUES (1,'Empresa A',''),(2,'Empresa B',''),(3,'Empresa C','');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instalaciones`
--

DROP TABLE IF EXISTS `instalaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instalaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` text DEFAULT NULL,
  `codigo_unico` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_unico` (`codigo_unico`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instalaciones`
--

LOCK TABLES `instalaciones` WRITE;
/*!40000 ALTER TABLE `instalaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `instalaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registros`
--

DROP TABLE IF EXISTS `registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('inicio_jornada','fin_jornada','inicio_espera','fin_espera','inicio_descanso','fin_descanso','inicio_colacion','fin_colacion') NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `instalacion_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario` (`usuario_id`),
  KEY `instalacion_id` (`instalacion_id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registros`
--

LOCK TABLES `registros` WRITE;
/*!40000 ALTER TABLE `registros` DISABLE KEYS */;
INSERT INTO `registros` (`id`, `usuario_id`, `tipo`, `fecha_hora`, `latitud`, `longitud`, `instalacion_id`) VALUES (1,4,'inicio_jornada','2025-03-19 14:23:37',NULL,NULL,NULL),(2,4,'inicio_espera','2025-03-19 14:42:03',NULL,NULL,NULL),(3,4,'fin_espera','2025-03-19 14:53:38',NULL,NULL,NULL),(4,4,'fin_jornada','2025-03-19 14:57:44',NULL,NULL,NULL),(5,4,'inicio_jornada','2025-03-19 16:16:15',-33.00611352,-71.26684516,NULL),(6,4,'inicio_espera','2025-03-19 16:25:21',-33.00611352,-71.26684516,NULL),(7,4,'fin_jornada','2025-03-19 16:25:57',-33.00611352,-71.26684516,NULL),(8,4,'fin_espera','2025-03-19 16:25:58',-33.00611352,-71.26684516,NULL),(9,4,'fin_espera','2025-03-19 16:26:01',-33.00611352,-71.26684516,NULL),(10,4,'inicio_jornada','2025-03-19 13:31:02',-33.00611352,-71.26684516,NULL),(46,14,'inicio_espera','2025-05-13 21:27:07',-33.40619480,-70.57277270,NULL),(45,14,'inicio_espera','2025-05-13 21:26:37',-33.40619480,-70.57277270,NULL),(44,14,'inicio_espera','2025-05-13 21:26:37',-33.40619480,-70.57277270,NULL),(43,14,'inicio_espera','2025-05-13 21:26:37',-33.40619480,-70.57277270,NULL),(42,14,'fin_colacion','2025-05-13 21:26:03',-33.40621470,-70.57284960,NULL),(41,14,'inicio_colacion','2025-05-13 21:23:20',-33.40621470,-70.57284960,NULL),(40,14,'fin_espera','2025-05-13 21:09:33',-33.40621150,-70.57285910,NULL),(39,15,'fin_espera','2025-05-13 21:06:58',-33.40642000,-70.57275330,NULL),(36,14,'inicio_espera','2025-05-13 21:02:39',-33.40618990,-70.57286610,NULL),(35,15,'fin_espera','2025-05-13 21:02:19',-33.40620210,-70.57278790,NULL),(34,15,'inicio_espera','2025-05-13 21:01:47',-33.40620210,-70.57278790,NULL),(33,15,'inicio_espera','2025-05-13 21:01:17',-33.40620210,-70.57278790,NULL),(32,15,'inicio_jornada','2025-05-13 20:59:39',-33.40620210,-70.57278790,NULL),(31,15,'inicio_jornada','2025-05-13 20:59:39',-33.40620210,-70.57278790,NULL),(30,15,'inicio_jornada','2025-05-13 20:59:39',-33.40620210,-70.57278790,NULL),(29,14,'inicio_jornada','2025-05-13 20:54:54',-33.40692170,-70.57129990,NULL),(38,14,'fin_espera','2025-05-13 21:05:54',-33.40618990,-70.57286610,NULL),(37,15,'fin_espera','2025-05-13 21:03:53',-33.40642000,-70.57275330,NULL),(47,14,'inicio_jornada','2025-05-13 22:12:20',-33.40618600,-70.57282270,NULL),(48,14,'fin_espera','2025-05-13 22:14:03',-33.40621650,-70.57278710,NULL),(49,14,'inicio_espera','2025-05-13 22:14:43',-33.40621650,-70.57278710,NULL),(50,14,'fin_espera','2025-05-13 22:18:33',-33.40621650,-70.57278710,NULL),(51,14,'inicio_jornada','2025-05-13 22:19:10',-33.40618390,-70.57279120,NULL),(52,15,'inicio_jornada','2025-05-14 16:39:17',-33.40621100,-70.57282160,NULL),(53,15,'fin_espera','2025-05-14 18:28:24',-33.40619520,-70.57284230,NULL),(54,15,'fin_espera','2025-05-14 18:30:44',-33.40619240,-70.57278010,NULL),(55,15,'inicio_descanso','2025-05-14 18:34:18',-33.40619240,-70.57278010,NULL),(56,14,'fin_espera','2025-05-14 19:19:53',-33.40617080,-70.57276700,NULL),(57,15,'fin_descanso','2025-05-14 19:21:01',-33.40613660,-70.57278690,NULL),(58,15,'fin_descanso','2025-05-14 19:21:01',-33.40613660,-70.57278690,NULL),(59,14,'fin_descanso','2025-05-14 19:22:00',-33.40617080,-70.57276700,NULL),(60,14,'inicio_jornada','2025-05-14 19:23:05',-33.40617080,-70.57276700,NULL),(61,15,'inicio_espera','2025-05-14 19:30:00',-33.40620300,-70.57283180,NULL),(62,14,'inicio_colacion','2025-05-14 19:32:54',-33.40623360,-70.57286870,NULL),(63,14,'fin_colacion','2025-05-14 19:36:11',-33.40616300,-70.57277690,NULL),(64,14,'fin_colacion','2025-05-14 19:36:41',-33.40616300,-70.57277690,NULL),(65,15,'inicio_jornada','2025-05-16 17:58:42',-33.40698200,-70.57167170,NULL),(66,15,'inicio_espera','2025-05-16 18:01:11',-33.40618370,-70.57281210,NULL),(67,15,'fin_espera','2025-05-16 18:02:44',-33.40618370,-70.57281210,NULL),(68,15,'inicio_colacion','2025-05-16 18:08:35',-33.40619540,-70.57282710,NULL),(69,15,'fin_colacion','2025-05-16 18:14:09',-33.40620890,-70.57286330,NULL),(70,15,'fin_jornada','2025-05-16 18:22:55',-33.40617380,-70.57278690,NULL);
/*!40000 ALTER TABLE `registros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','empleado','superadmin') NOT NULL DEFAULT 'empleado',
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `rut` varchar(12) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `instalacion_id` int(11) DEFAULT NULL,
  `es_superadmin` tinyint(1) DEFAULT 0,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `instalacion_id` (`instalacion_id`),
  KEY `fk_empresa` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`id`, `username`, `password`, `rol`, `nombre`, `apellido`, `telefono`, `rut`, `edad`, `direccion`, `cargo`, `fecha_contratacion`, `instalacion_id`, `es_superadmin`, `empresa_id`) VALUES (3,'admin_kubo','$2y$10$AIAMsiPO3jwnCKyoZQ/./e00ZVulBr6K5gUhmv8vWvLWc/2CmwOx.','admin','','','',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),(6,'admin_pch','$2y$10$oGiO9tLJRaj6r.65hSZicuuJRefxGOyf6l9q2.yYNoz880oFHBEDy','admin','Rosa','Guzmán','987878787',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),(15,'poletsanhueza','$2y$10$XH3HjxH4a/pZqmTHmyzlwOSmTcaIbKDJk6pWy6fZ2gAOMjE4E9nhK','empleado','Polet','Sanhueza','456525633','17121777-6',NULL,'Los Militares 5620','Empleado',NULL,NULL,0,1),(14,'luiscid','$2y$10$wJvjRccngQktaYsjW/tfUe.an5.OU2jXLjgF5OKThh/N8cCHqW8Qu','empleado','Luis ','Cid','992488961','16.068.484-4',NULL,'Los Militares 5620','Empleado',NULL,NULL,0,2),(11,'admin_pchdigital','$2y$10$XQWDwq5iyPVuPszW6qQOV.ayakT4PovOwxxPhIHlC2/6Dyz5FYMQy','admin','','','',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'happymen_control_tiempo'
--

--
-- Dumping routines for database 'happymen_control_tiempo'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-20 17:58:58
