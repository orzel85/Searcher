-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Wersja serwera:               5.6.24 - MySQL Community Server (GPL)
-- Serwer OS:                    Win32
-- HeidiSQL Wersja:              9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela torrent.query
CREATE TABLE IF NOT EXISTS `query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `value_sha1` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `provider` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `page` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Data exporting was unselected.


-- Zrzut struktury tabela torrent.query_torrents
CREATE TABLE IF NOT EXISTS `query_torrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_value_sha1` varchar(300) COLLATE utf8_bin NOT NULL,
  `torrents_link_sha1` varchar(400) COLLATE utf8_bin NOT NULL,
  `page` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `provider` varchar(400) COLLATE utf8_bin NOT NULL,
  `order_on_list` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `query_query_sha1` (`query_value_sha1`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Data exporting was unselected.


-- Zrzut struktury tabela torrent.torrents
CREATE TABLE IF NOT EXISTS `torrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(400) COLLATE utf8_bin NOT NULL,
  `link` varchar(400) COLLATE utf8_bin NOT NULL,
  `link_sha1` varchar(400) COLLATE utf8_bin NOT NULL,
  `seeds` int(11) NOT NULL,
  `peers` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `provider` varchar(50) COLLATE utf8_bin NOT NULL,
  `size` double NOT NULL,
  `sizeOriginal` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link_sha1` (`link_sha1`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
