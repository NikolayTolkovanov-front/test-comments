-- Adminer 4.8.1 MySQL 8.4.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `comments` (`id`, `name`, `date`, `text`) VALUES
(28,	'test@gmail.com',	'2024-05-27',	'ffffааааааааааа'),
(37,	'test@gmail.com',	'2024-05-27',	'ffff11111111111111'),
(38,	'test@gmail.com',	'2024-05-27',	'ffff22222222222222222222'),
(39,	'test@gmail.com',	'2024-05-27',	'ffff3333333333333333333333'),
(40,	'test@gmail.com',	'2024-05-27',	'ffff4444444444444444444444444');

-- 2024-05-27 11:02:37