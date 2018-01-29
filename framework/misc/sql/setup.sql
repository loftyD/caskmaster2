-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.21-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for caskmaster
CREATE DATABASE IF NOT EXISTS `caskmaster` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `caskmaster`;


-- Dumping structure for table caskmaster.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `listing` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proprietor` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manager` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id_list` text COLLATE utf8_unicode_ci,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `samples_provided` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `flowchart_step_id` int(11) DEFAULT NULL,
  `highlight` int(11) DEFAULT NULL,
  `alert_date` date DEFAULT NULL,
  `status` enum('active','disabled','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`company_id`),
  KEY `idx_flowchart` (`flowchart_step_id`),
  KEY `FK_companies_users` (`user_id`),
  CONSTRAINT `FK_companies_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.entity_factory
CREATE TABLE IF NOT EXISTS `entity_factory` (
  `entity_factory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `class` varchar(255) NOT NULL,
  `table` text NOT NULL,
  `primary_key_field` text NOT NULL,
  `name_field` text NOT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`entity_factory_id`),
  UNIQUE KEY `Index 2` (`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `status` enum('active','disabled','deleted') NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.group_operations
CREATE TABLE IF NOT EXISTS `group_operations` (
  `group_id` int(11) NOT NULL,
  `operation_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`operation_id`),
  KEY `FK_group_operations_operations` (`operation_id`),
  CONSTRAINT `FK_group_operations_groups` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_group_operations_operations` FOREIGN KEY (`operation_id`) REFERENCES `operations` (`operation_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.operations
CREATE TABLE IF NOT EXISTS `operations` (
  `operation_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `create` int(11) DEFAULT NULL,
  `read` int(11) DEFAULT NULL,
  `update` int(11) DEFAULT NULL,
  `delete` int(11) DEFAULT NULL,
  PRIMARY KEY (`operation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `username` text NOT NULL,
  `dob` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table caskmaster.users_auth_status
CREATE TABLE IF NOT EXISTS `users_auth_status` (
  `user_id` int(11) NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(11),
  `status` enum('active','disabled','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  KEY `FK_users_auth_status_groups` (`group_id`),
  CONSTRAINT `FK_users_auth_status_groups` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_users_auth_status_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.21-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Dumping data for table caskmaster.entity_factory: ~5 rows (approximately)
/*!40000 ALTER TABLE `entity_factory` DISABLE KEYS */;
INSERT INTO `entity_factory` (`entity_factory_id`, `name`, `class`, `table`, `primary_key_field`, `name_field`, `status`, `added`, `updated`) VALUES
	(1, 'User', 'User', 'users', 'user_id', 'name', 'active', '2018-01-22 21:41:42', '2018-01-22 21:41:43'),
	(2, 'User Auth Status', 'UserAuthStatus', 'users_auth_status', 'user_id', 'user_id', 'active', '2018-01-26 11:42:20', '2018-01-26 11:42:20'),
	(3, 'Group', 'Group', 'groups', 'group_id', 'name', 'active', '2018-01-26 13:36:04', '2018-01-26 13:36:04'),
	(4, 'Operation', 'Operation', 'operations', 'operation_id', 'name', 'active', '2018-01-26 20:47:48', '2018-01-26 20:47:49'),
	(5, 'Group Operation', 'GroupOperation', 'group_operations', 'group_id,operation_id', 'group_id,operation_id', 'active', '2018-01-26 20:51:04', '2018-01-26 20:51:05'),
	(6, 'Company', 'Company', 'companies', 'company_id', 'name', 'active', '2018-01-26 22:27:32', '2018-01-26 22:27:32');
/*!40000 ALTER TABLE `entity_factory` ENABLE KEYS */;

-- Dumping data for table caskmaster.groups: ~2 rows (approximately)
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` (`group_id`, `name`, `status`, `added`, `updated`) VALUES
	(1, 'Admin', 'active', '2018-01-26 22:45:34', '2018-01-26 22:45:34'),
	(2, 'User', 'active', '2018-01-26 22:45:34', '2018-01-26 22:45:34');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;

-- Dumping data for table caskmaster.group_operations: ~0 rows (approximately)
/*!40000 ALTER TABLE `group_operations` DISABLE KEYS */;
INSERT INTO `group_operations` (`group_id`, `operation_id`) VALUES
	(1, 1);
/*!40000 ALTER TABLE `group_operations` ENABLE KEYS */;

-- Dumping data for table caskmaster.operations: ~0 rows (approximately)
/*!40000 ALTER TABLE `operations` DISABLE KEYS */;
INSERT INTO `operations` (`operation_id`, `entity_id`, `name`, `create`, `read`, `update`, `delete`) VALUES
	(1, 1, 'barrel.user', 1, 1, 1, 1);
/*!40000 ALTER TABLE `operations` ENABLE KEYS */;

-- Dumping data for table caskmaster.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user_id`, `name`, `username`, `dob`) VALUES
	(1, 'Caskmaster Admin', 'caskmaster.admin', '2018-01-26'),
	(2, 'John Jones', 'john.jones', '2018-01-26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping data for table caskmaster.users_auth_status: ~2 rows (approximately)
/*!40000 ALTER TABLE `users_auth_status` DISABLE KEYS */;
INSERT INTO `users_auth_status` (`user_id`, `password`, `group_id`, `status`) VALUES
	(1, '$2y$10$5SMIswinlGGaB88qCHwtpO1kZyU1POHKzwp6YgNmd2gFhDAtcUv0C', 1, 'active');
/*!40000 ALTER TABLE `users_auth_status` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
