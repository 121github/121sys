-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-02-2016 a las 12:27:38
-- Versión del servidor: 5.1.73-cll
-- Versión de PHP: 5.4.31

USE 121sys;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `one2one_lhs`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `access_log`
--

DROP TABLE IF EXISTS `access_log`;
CREATE TABLE IF NOT EXISTS `access_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL,
  `logon` datetime NOT NULL,
  `lastaction` datetime NOT NULL,
  `logoff` datetime DEFAULT NULL,
  `ip_address` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=163 ;

--
-- Truncar tablas antes de insertar `access_log`
--

TRUNCATE TABLE `access_log`;
--
-- Volcado de datos para la tabla `access_log`
--

INSERT INTO `access_log` (`log_id`, `user_id`, `logon`, `lastaction`, `logoff`, `ip_address`) VALUES
(1, 1, '2015-10-12 10:11:05', '2015-10-12 10:28:33', '2015-10-12 10:28:33', '84.19.44.186'),
(2, 2, '2015-10-12 11:27:05', '2015-10-12 11:44:28', '2015-10-12 11:51:57', '31.53.188.74'),
(3, 2, '2015-10-12 11:51:58', '2015-10-12 11:52:17', '2015-10-12 11:52:21', '31.53.188.74'),
(4, 2, '2015-10-13 09:43:59', '2015-10-13 09:44:11', '2015-10-13 09:44:16', '46.233.116.166'),
(5, 2, '2015-10-13 11:10:11', '2015-10-13 12:03:37', '2015-10-13 12:03:37', '213.120.112.112'),
(6, 2, '2015-10-13 15:52:09', '2015-10-13 15:53:04', '2015-10-13 15:53:04', '31.53.188.74'),
(7, 1, '2015-10-14 11:26:23', '2015-10-14 12:02:07', '2015-10-14 12:02:07', '84.19.44.186'),
(8, 1, '2015-10-20 16:23:06', '2015-10-20 16:23:11', '2015-10-20 16:23:11', '84.19.44.186'),
(9, 2, '2015-10-22 08:29:14', '2015-10-22 08:29:34', '2015-10-22 08:29:34', '81.141.203.65'),
(10, 2, '2015-10-22 10:40:17', '2015-10-22 11:03:06', '2015-10-22 11:03:06', '81.141.203.65'),
(11, 2, '2015-10-22 14:05:42', '2015-10-22 14:07:16', '2015-10-22 14:07:16', '81.141.203.65'),
(12, 2, '2015-10-22 14:08:34', '2015-10-22 14:09:57', '2015-10-22 14:09:57', '81.141.203.65'),
(13, 2, '2015-10-23 08:46:42', '2015-10-23 09:13:42', '2015-10-23 09:13:42', '86.159.195.249'),
(14, 2, '2015-10-23 09:54:46', '2015-10-23 10:01:18', '2015-10-23 10:01:18', '86.159.195.249'),
(15, 1, '2015-10-23 11:00:22', '2015-10-23 11:00:40', '2015-10-23 11:00:40', '84.19.44.186'),
(16, 2, '2015-10-27 10:47:16', '2015-10-27 10:54:50', '2015-10-27 10:54:50', '31.52.118.197'),
(17, 1, '2015-10-27 15:59:40', '2015-10-28 09:22:38', '2015-10-28 09:23:15', '84.19.44.186'),
(18, 2, '2015-10-27 16:03:59', '2015-10-27 16:04:25', '2015-10-27 16:04:25', '31.52.118.197'),
(19, 2, '2015-10-28 09:16:09', '2015-10-28 09:26:12', '2015-10-28 10:04:54', '31.52.118.197'),
(20, 1, '2015-10-28 09:23:25', '2015-10-28 09:54:49', '2015-10-28 09:54:49', '84.19.44.186'),
(21, 2, '2015-10-28 10:04:55', '2015-10-28 10:07:47', '2015-10-28 10:07:47', '31.52.118.197'),
(22, 2, '2015-10-28 10:30:36', '2015-10-28 10:37:14', '2015-10-28 10:37:14', '84.19.44.186'),
(23, 1, '2015-11-04 15:10:25', '2015-11-04 15:10:28', '2015-11-04 15:10:28', '84.19.44.186'),
(24, 1, '2015-11-04 16:42:01', '2015-11-04 16:42:51', '2015-11-04 16:42:51', '84.19.44.186'),
(25, 2, '2015-11-04 16:48:46', '2015-11-04 16:53:44', '2015-11-04 16:53:44', '86.179.246.85'),
(26, 1, '2015-11-05 09:42:27', '2015-11-05 09:43:41', '2015-11-05 09:43:41', '84.19.44.186'),
(27, 2, '2015-11-05 17:02:11', '2015-11-05 17:09:16', '2015-11-05 17:09:16', '86.179.246.85'),
(28, 2, '2015-11-09 10:30:27', '2015-11-09 11:01:10', '2015-11-09 11:01:10', '86.179.246.85'),
(29, 1, '2015-11-09 11:52:47', '2015-11-09 11:57:07', '2015-11-09 11:57:15', '84.19.44.186'),
(30, 1, '2015-11-09 11:57:22', '2015-11-09 12:41:01', '2015-11-09 12:41:01', '84.19.44.186'),
(31, 1, '2015-11-09 13:26:01', '2015-11-09 13:37:43', '2015-11-09 13:39:12', '84.19.44.186'),
(32, 1, '2015-11-09 13:39:19', '2015-11-09 14:24:46', '2015-11-09 14:24:49', '84.19.44.186'),
(33, 2, '2015-11-09 14:18:23', '2015-11-09 14:19:27', '2015-11-09 14:19:34', '86.179.246.85'),
(34, 2, '2015-11-09 14:19:35', '2015-11-09 14:19:48', '2015-11-09 14:20:35', '86.179.246.85'),
(35, 2, '2015-11-09 14:20:36', '2015-11-09 14:21:03', '2015-11-09 14:21:31', '86.179.246.85'),
(36, 2, '2015-11-09 14:21:33', '2015-11-09 14:31:01', '2015-11-09 14:33:13', '86.179.246.85'),
(37, 2, '2015-11-09 14:25:00', '2015-11-09 14:25:39', '2015-11-09 14:26:02', '84.19.44.186'),
(38, 1, '2015-11-09 14:26:13', '2015-11-09 15:20:28', '2015-11-09 15:20:28', '84.19.44.186'),
(39, 2, '2015-11-09 14:33:15', '2015-11-09 14:47:59', '2015-11-09 14:50:46', '86.179.246.85'),
(40, 2, '2015-11-09 14:50:48', '2015-11-09 14:56:45', '2015-11-09 14:56:45', '86.179.246.85'),
(41, 2, '2015-11-09 15:21:37', '2015-11-09 15:27:36', '2015-11-09 15:27:47', '86.179.246.85'),
(42, 2, '2015-11-09 15:27:58', '2015-11-09 15:43:16', '2015-11-09 15:43:16', '86.179.246.85'),
(43, 2, '2015-11-09 16:23:05', '2015-11-09 16:25:17', '2015-11-09 16:25:40', '86.179.246.85'),
(44, 2, '2015-11-09 16:26:07', '2015-11-09 16:39:13', '2015-11-09 16:39:13', '86.179.246.85'),
(45, 1, '2015-11-09 16:27:52', '2015-11-09 16:37:27', '2015-11-09 16:37:27', '84.19.44.186'),
(46, 2, '2015-11-10 10:32:29', '2015-11-10 11:10:02', '2015-11-10 11:10:02', '213.123.133.249'),
(47, 2, '2015-11-17 15:40:17', '2015-11-17 15:42:02', '2015-11-17 15:42:02', '85.255.233.49'),
(48, 2, '2015-11-18 09:29:47', '2015-11-18 09:35:14', '2015-11-18 09:35:14', '80.229.189.129'),
(49, 2, '2015-11-23 09:10:41', '2015-11-23 09:33:19', '2015-11-23 10:45:15', '86.175.130.40'),
(50, 2, '2015-11-23 10:49:40', '2015-11-23 10:49:51', '2015-11-23 10:53:53', '86.175.130.40'),
(51, 2, '2015-11-23 10:53:57', '2015-11-23 10:54:47', '2015-11-23 10:54:57', '86.175.130.40'),
(52, 2, '2015-11-23 10:55:02', '2015-11-23 10:59:30', '2015-11-23 10:59:30', '86.175.130.40'),
(53, 1, '2015-11-23 14:55:55', '2015-11-23 15:00:18', '2015-11-23 15:00:18', '84.19.44.186'),
(54, 2, '2015-11-24 09:42:05', '2015-11-24 09:42:23', '2015-11-24 09:42:23', '85.255.232.65'),
(55, 2, '2015-11-24 12:12:02', '2015-11-24 12:26:02', '2015-11-24 12:27:22', '31.205.255.241'),
(56, 2, '2015-11-24 12:31:37', '2015-11-24 12:33:35', '2015-11-24 12:33:35', '31.205.255.241'),
(57, 2, '2015-11-25 09:03:02', '2015-11-25 09:18:09', '2015-11-25 09:18:09', '5.148.139.143'),
(58, 2, '2015-11-26 08:39:27', '2015-11-26 09:11:30', '2015-11-26 09:11:30', '86.138.82.56'),
(59, 2, '2015-12-07 15:16:24', '2015-12-07 15:16:33', '2015-12-07 15:16:33', '5.81.242.160'),
(60, 2, '2015-12-09 12:19:40', '2015-12-09 12:19:48', '2015-12-09 12:19:48', '86.137.253.197'),
(61, 2, '2015-12-09 12:32:41', '2015-12-09 12:42:47', '2015-12-09 12:42:51', '84.19.44.186'),
(62, 2, '2015-12-09 12:42:57', '2015-12-09 12:44:57', '2015-12-09 13:10:32', '84.19.44.186'),
(63, 2, '2015-12-09 13:10:35', '2015-12-09 13:11:34', '2015-12-09 13:11:34', '86.137.253.197'),
(64, 2, '2015-12-09 13:11:34', '2015-12-09 13:12:21', '2015-12-09 13:12:39', '86.137.253.197'),
(65, 2, '2015-12-09 13:12:56', '2015-12-09 13:12:56', '2015-12-09 13:12:56', '86.137.253.197'),
(66, 2, '2015-12-09 13:13:32', '2015-12-09 13:13:36', '2015-12-09 13:13:36', '86.137.253.197'),
(67, 2, '2015-12-10 10:58:37', '2015-12-10 10:59:35', '2015-12-10 10:59:35', '84.19.44.186'),
(68, 2, '2015-12-10 11:00:57', '2015-12-10 11:04:24', '2015-12-10 11:04:24', '84.19.44.186'),
(69, 2, '2015-12-10 11:44:33', '2015-12-10 11:46:38', '2015-12-10 11:46:38', '31.48.194.53'),
(70, 2, '2015-12-16 10:53:30', '2015-12-16 11:00:47', '2015-12-16 11:00:47', '213.120.99.55'),
(71, 2, '2015-12-17 08:16:22', '2015-12-17 08:17:44', '2015-12-17 08:17:44', '31.53.64.214'),
(72, 2, '2015-12-17 09:00:33', '2015-12-17 09:00:35', '2015-12-17 09:00:35', '31.53.64.214'),
(73, 2, '2015-12-17 10:55:32', '2015-12-17 11:06:17', '2015-12-17 11:06:17', '31.53.64.214'),
(74, 1, '2015-12-17 12:37:36', '2015-12-17 12:37:36', '2015-12-17 12:37:37', '84.19.44.186'),
(75, 1, '2015-12-17 12:37:58', '2015-12-17 13:06:19', '2015-12-17 13:06:19', '84.19.44.186'),
(76, 2, '2015-12-17 13:58:50', '2015-12-17 14:04:56', '2015-12-17 14:04:56', '31.53.64.214'),
(77, 1, '2015-12-18 08:28:27', '2015-12-18 09:23:18', '2015-12-18 09:23:18', '84.19.44.186'),
(78, 1, '2015-12-18 09:23:58', '2015-12-18 09:24:05', '2015-12-18 09:24:06', '84.19.44.186'),
(79, 1, '2015-12-18 09:31:36', '2015-12-18 10:13:04', '2015-12-18 10:13:04', '::1'),
(80, 1, '2015-12-18 10:46:26', '2015-12-18 11:28:45', '2015-12-18 11:28:45', '::1'),
(81, 1, '2015-12-18 14:29:20', '2015-12-18 14:33:15', '2015-12-18 14:33:15', '::1'),
(82, 1, '2015-12-18 15:22:36', '2015-12-18 15:29:29', '2015-12-18 15:29:29', '84.19.44.186'),
(83, 1, '2015-12-18 15:53:30', '2015-12-18 15:56:53', '2015-12-18 15:56:53', '::1'),
(84, 1, '2015-12-18 16:02:48', '2015-12-18 16:46:19', '2015-12-18 16:46:19', '::1'),
(85, 1, '2015-12-21 09:26:43', '2015-12-21 10:04:10', '2015-12-21 10:04:10', '::1'),
(86, 1, '2015-12-21 11:27:20', '2015-12-21 12:04:03', '2015-12-21 12:04:03', '::1'),
(87, 1, '2015-12-21 16:21:26', '2015-12-21 16:40:42', '2015-12-21 16:40:42', '::1'),
(88, 1, '2015-12-22 09:45:00', '2015-12-22 09:53:13', '2015-12-22 09:53:13', '84.19.44.186'),
(89, 1, '2015-12-22 10:44:03', '2015-12-22 10:44:06', '2015-12-22 10:44:06', '::1'),
(90, 1, '2015-12-22 10:52:25', '2015-12-22 11:30:14', '2015-12-22 11:30:14', '84.19.44.186'),
(91, 2, '2015-12-22 11:15:35', '2015-12-22 11:21:03', '2015-12-22 11:21:03', '85.255.234.185'),
(92, 1, '2015-12-22 11:30:12', '2015-12-22 12:43:45', '2015-12-22 12:43:45', '::1'),
(93, 2, '2015-12-22 13:07:07', '2015-12-22 13:11:03', '2015-12-22 13:16:24', '85.255.235.109'),
(94, 2, '2015-12-22 13:17:20', '2015-12-22 14:31:39', '2015-12-22 14:31:39', '85.255.235.109'),
(95, 2, '2015-12-22 14:34:57', '2015-12-22 14:47:00', '2015-12-22 14:47:00', '85.255.235.109'),
(96, 2, '2015-12-22 14:48:29', '2015-12-22 14:51:55', '2015-12-22 15:00:45', '85.255.235.109'),
(97, 2, '2015-12-22 14:55:49', '2015-12-22 14:57:25', '2015-12-22 14:57:47', '84.19.44.186'),
(98, 1, '2015-12-22 14:57:51', '2015-12-22 15:05:05', '2015-12-22 15:05:05', '84.19.44.186'),
(99, 2, '2015-12-22 15:01:36', '2015-12-22 15:05:47', '2015-12-22 15:05:47', '85.255.235.109'),
(100, 1, '2015-12-22 15:07:24', '2015-12-22 15:07:27', '2015-12-22 15:07:33', '::1'),
(101, 1, '2015-12-22 16:18:11', '2015-12-22 16:19:30', '2015-12-22 16:19:30', '84.19.44.186'),
(102, 1, '2015-12-22 16:20:14', '2015-12-22 16:41:32', '2015-12-22 16:41:32', '::1'),
(103, 1, '2015-12-23 09:09:18', '2015-12-23 09:09:57', '2015-12-23 09:09:57', '::1'),
(104, 1, '2015-12-23 09:26:19', '2015-12-23 10:26:25', '2015-12-23 10:26:25', '84.19.44.186'),
(105, 2, '2016-01-05 09:22:47', '2016-01-05 09:39:29', '2016-01-05 11:32:44', '86.183.5.101'),
(106, 1, '2016-01-05 10:29:40', '2016-01-05 10:45:36', '2016-01-05 10:45:36', '84.19.44.186'),
(107, 2, '2016-01-05 11:32:52', '2016-01-05 11:41:17', '2016-01-05 11:48:31', '86.183.5.101'),
(108, 2, '2016-01-05 11:48:36', '2016-01-05 11:49:38', '2016-01-05 11:49:38', '86.183.5.101'),
(109, 2, '2016-01-05 13:31:07', '2016-01-05 13:32:04', '2016-01-05 13:32:46', '86.183.5.101'),
(110, 2, '2016-01-05 13:32:51', '2016-01-05 13:36:42', '2016-01-05 13:36:42', '86.183.5.101'),
(111, 2, '2016-01-05 14:05:59', '2016-01-05 14:09:10', '2016-01-05 14:17:16', '84.19.44.186'),
(112, 2, '2016-01-05 14:17:19', '2016-01-05 14:18:48', '2016-01-05 14:18:48', '86.183.5.101'),
(113, 2, '2016-01-06 12:40:42', '2016-01-06 13:01:09', '2016-01-06 13:01:09', '84.19.44.186'),
(114, 2, '2016-01-06 13:10:10', '2016-01-06 13:12:39', '2016-01-06 13:12:39', '84.19.44.186'),
(115, 2, '2016-01-06 14:04:47', '2016-01-06 14:21:22', '2016-01-06 14:21:22', '84.19.44.186'),
(116, 2, '2016-01-06 15:08:11', '2016-01-06 15:39:25', '2016-01-06 15:39:25', '92.40.249.22'),
(117, 2, '2016-01-06 18:42:34', '2016-01-06 18:49:39', '2016-01-06 18:49:39', '185.31.154.141'),
(118, 2, '2016-01-07 09:00:32', '2016-01-07 09:29:40', '2016-01-07 11:04:19', '84.19.44.186'),
(119, 2, '2016-01-07 11:04:23', '2016-01-07 11:04:45', '2016-01-07 11:42:41', '84.19.44.186'),
(120, 2, '2016-01-07 11:42:54', '2016-01-07 11:44:16', '2016-01-07 12:25:56', '84.19.44.186'),
(121, 2, '2016-01-07 12:27:16', '2016-01-07 13:02:33', '2016-01-07 14:13:55', '84.19.44.186'),
(122, 2, '2016-01-08 14:59:40', '2016-01-08 15:00:52', '2016-01-08 15:06:46', '86.185.37.179'),
(123, 2, '2016-01-08 15:07:14', '2016-01-08 15:12:07', '2016-01-08 15:21:48', '86.185.37.179'),
(124, 2, '2016-01-08 15:22:40', '2016-01-08 15:32:26', '2016-01-08 17:06:41', '86.185.37.179'),
(125, 2, '2016-01-11 16:48:20', '2016-01-11 17:09:50', '2016-01-11 17:09:50', '85.189.165.150'),
(126, 2, '2016-01-12 11:10:20', '2016-01-12 11:10:44', '2016-01-12 11:30:46', '92.26.54.136'),
(127, 2, '2016-01-12 11:30:51', '2016-01-12 11:37:59', '2016-01-12 11:37:59', '92.26.54.136'),
(128, 2, '2016-01-12 15:14:06', '2016-01-12 15:36:44', '2016-01-12 15:36:44', '185.69.145.216'),
(129, 2, '2016-01-13 11:36:54', '2016-01-13 11:42:12', '2016-01-13 11:42:12', '84.19.44.186'),
(130, 2, '2016-01-13 11:42:25', '2016-01-13 12:03:02', '2016-01-13 12:03:02', '::1'),
(131, 2, '2016-01-13 15:50:31', '2016-01-13 15:56:58', '2016-01-13 15:56:58', '86.185.37.53'),
(132, 2, '2016-01-15 15:24:28', '2016-01-15 15:24:28', '2016-01-15 15:24:28', '::1'),
(133, 2, '2016-01-15 15:24:36', '2016-01-15 15:24:36', '2016-01-15 15:24:36', '::1'),
(134, 2, '2016-01-15 15:25:14', '2016-01-15 15:25:14', '2016-01-15 15:25:14', '::1'),
(135, 2, '2016-01-15 15:26:14', '2016-01-15 15:26:14', '2016-01-15 15:26:39', '::1'),
(136, 2, '2016-01-15 15:26:55', '2016-01-15 16:03:27', '2016-01-15 16:03:27', '::1'),
(137, 1, '2016-01-18 08:36:38', '2016-01-18 09:15:48', '2016-01-18 09:15:48', '::1'),
(138, 1, '2016-01-18 10:32:32', '2016-01-18 10:33:25', '2016-01-18 10:33:26', '192.168.1.57'),
(139, 1, '2016-01-18 10:33:38', '2016-01-18 10:33:42', '2016-01-18 10:36:58', '::1'),
(140, 1, '2016-01-18 10:37:10', '2016-01-18 12:25:37', '2016-01-19 14:43:51', '192.168.1.57'),
(141, 2, '2016-01-19 08:56:23', '2016-01-19 09:09:05', '2016-01-19 09:09:05', '86.180.34.205'),
(142, 1, '2016-01-19 09:22:32', '2016-01-19 09:23:52', '2016-01-19 09:24:34', '::1'),
(143, 2, '2016-01-19 09:24:39', '2016-01-19 09:31:48', '2016-01-19 09:31:55', '::1'),
(144, 2, '2016-01-19 09:32:01', '2016-01-19 09:53:22', '2016-01-19 10:14:02', '86.180.34.205'),
(145, 2, '2016-01-19 14:41:33', '2016-01-19 15:16:42', '2016-01-19 15:44:18', '92.27.93.71'),
(146, 1, '2016-01-19 14:43:57', '2016-01-19 14:44:33', '2016-01-19 14:45:03', '::1'),
(147, 2, '2016-01-19 15:44:22', '2016-01-19 15:45:21', '2016-01-19 15:45:21', '92.27.93.71'),
(148, 2, '2016-01-20 09:30:53', '2016-01-20 09:31:01', '2016-01-20 09:31:01', '86.187.48.52'),
(149, 2, '2016-01-20 12:07:24', '2016-01-20 12:07:55', '2016-01-20 12:07:55', '85.255.233.2'),
(150, 2, '2016-01-20 15:08:48', '2016-01-20 15:09:04', '2016-01-20 15:09:04', '212.183.140.11'),
(151, 1, '2016-01-21 08:45:50', '2016-01-21 08:46:01', '2016-01-21 08:46:01', '::1'),
(152, 2, '2016-01-21 09:51:55', '2016-01-21 09:51:55', '2016-01-21 09:52:03', '212.42.168.18'),
(153, 2, '2016-01-21 10:37:03', '2016-01-21 11:49:57', '2016-01-21 12:05:06', '212.42.168.18'),
(154, 2, '2016-01-21 12:05:10', '2016-01-21 13:48:33', '2016-01-21 13:48:33', '212.42.168.18'),
(155, 2, '2016-01-21 14:26:25', '2016-01-21 14:39:26', NULL, '212.42.168.18'),
(156, 2, '2016-01-22 08:56:29', '2016-01-22 08:57:23', NULL, '86.180.34.205'),
(157, 2, '2016-01-22 15:04:17', '2016-01-22 15:04:17', NULL, '86.180.34.205'),
(158, 2, '2016-01-25 15:21:54', '2016-01-25 15:22:39', NULL, '85.255.234.141'),
(159, 1, '2016-02-08 17:09:47', '2016-02-08 17:12:53', NULL, '84.19.44.186'),
(160, 1, '2016-02-09 09:30:57', '2016-02-09 10:04:32', '2016-02-09 10:04:37', '84.19.44.186'),
(161, 1, '2016-02-09 10:04:42', '2016-02-09 11:02:19', NULL, '::1'),
(162, 1, '2016-02-09 11:11:43', '2016-02-09 11:25:43', NULL, '84.19.44.186');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `answers_to_options`
--

DROP TABLE IF EXISTS `answers_to_options`;
CREATE TABLE IF NOT EXISTS `answers_to_options` (
  `answer_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  UNIQUE KEY `answer_id2` (`answer_id`,`option_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `answers_to_options`
--

TRUNCATE TABLE `answers_to_options`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `answer_notes`
--

DROP TABLE IF EXISTS `answer_notes`;
CREATE TABLE IF NOT EXISTS `answer_notes` (
  `answer_id` int(11) NOT NULL,
  `notes` mediumtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `answer_notes`
--

TRUNCATE TABLE `answer_notes`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apis`
--

DROP TABLE IF EXISTS `apis`;
CREATE TABLE IF NOT EXISTS `apis` (
  `api_id` int(11) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `api_token` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`api_id`),
  UNIQUE KEY `api_name` (`api_name`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `apis`
--

TRUNCATE TABLE `apis`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `text` mediumtext CHARACTER SET utf8 NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `urn` int(11) NOT NULL,
  `postcode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `cancellation_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `appointment_type_id` int(11) DEFAULT '1',
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `contact_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`appointment_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`),
  KEY `contact_id` (`contact_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `appointments`
--

TRUNCATE TABLE `appointments`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointments_ics`
--

DROP TABLE IF EXISTS `appointments_ics`;
CREATE TABLE IF NOT EXISTS `appointments_ics` (
  `appointments_ics_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'REQUEST',
  `uid` varchar(50) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '0',
  `send_to` varchar(255) NOT NULL,
  `send_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `send_from` varchar(255) NOT NULL,
  PRIMARY KEY (`appointments_ics_id`),
  KEY `FK_appointments_ics_appointment_id` (`appointment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Truncar tablas antes de insertar `appointments_ics`
--

TRUNCATE TABLE `appointments_ics`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_attendees`
--

DROP TABLE IF EXISTS `appointment_attendees`;
CREATE TABLE IF NOT EXISTS `appointment_attendees` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `appointment_id_2` (`appointment_id`,`user_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `appointment_attendees`
--

TRUNCATE TABLE `appointment_attendees`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_slots`
--

DROP TABLE IF EXISTS `appointment_slots`;
CREATE TABLE IF NOT EXISTS `appointment_slots` (
  `appointment_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_group_id` int(11) DEFAULT NULL,
  `slot_start` time DEFAULT NULL COMMENT '24h time of day',
  `slot_end` time DEFAULT NULL COMMENT '24h time of day',
  `slot_name` varchar(15) NOT NULL,
  `slot_description` varchar(100) NOT NULL COMMENT '1-7 monday - friday',
  PRIMARY KEY (`appointment_slot_id`),
  KEY `slot_group_id` (`slot_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `appointment_slots`
--

TRUNCATE TABLE `appointment_slots`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_slot_assignment`
--

DROP TABLE IF EXISTS `appointment_slot_assignment`;
CREATE TABLE IF NOT EXISTS `appointment_slot_assignment` (
  `slot_assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_slot_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `max_slots` int(11) NOT NULL,
  `day` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`slot_assignment_id`),
  KEY `appointment_slot_id` (`appointment_slot_id`,`campaign_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `appointment_slot_assignment`
--

TRUNCATE TABLE `appointment_slot_assignment`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_slot_groups`
--

DROP TABLE IF EXISTS `appointment_slot_groups`;
CREATE TABLE IF NOT EXISTS `appointment_slot_groups` (
  `slot_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`slot_group_id`),
  UNIQUE KEY `slot_group_name` (`slot_group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `appointment_slot_groups`
--

TRUNCATE TABLE `appointment_slot_groups`;
--
-- Volcado de datos para la tabla `appointment_slot_groups`
--

INSERT INTO `appointment_slot_groups` (`slot_group_id`, `slot_group_name`) VALUES
(1, 'AM/PM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_slot_override`
--

DROP TABLE IF EXISTS `appointment_slot_override`;
CREATE TABLE IF NOT EXISTS `appointment_slot_override` (
  `slot_override_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_slot_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `max_slots` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `notes` varchar(255) NOT NULL,
  PRIMARY KEY (`slot_override_id`),
  UNIQUE KEY `appointment_slot_id` (`appointment_slot_id`,`user_id`,`date`),
  KEY `slot_override_id` (`slot_override_id`,`campaign_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `appointment_slot_override`
--

TRUNCATE TABLE `appointment_slot_override`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_types`
--

DROP TABLE IF EXISTS `appointment_types`;
CREATE TABLE IF NOT EXISTS `appointment_types` (
  `appointment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_type` varchar(100) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(50) NOT NULL,
  PRIMARY KEY (`appointment_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `appointment_types`
--

TRUNCATE TABLE `appointment_types`;
--
-- Volcado de datos para la tabla `appointment_types`
--

INSERT INTO `appointment_types` (`appointment_type_id`, `appointment_type`, `is_default`, `icon`) VALUES
(1, 'Face to face', 1, ''),
(2, 'Telephone', 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `FK_urn` (`urn`),
  KEY `FK_users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Record attachments' AUTO_INCREMENT=7 ;

--
-- Truncar tablas antes de insertar `attachments`
--

TRUNCATE TABLE `attachments`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit`
--

DROP TABLE IF EXISTS `audit`;
CREATE TABLE IF NOT EXISTS `audit` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `change_type` varchar(20) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`audit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `audit`
--

TRUNCATE TABLE `audit`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_values`
--

DROP TABLE IF EXISTS `audit_values`;
CREATE TABLE IF NOT EXISTS `audit_values` (
  `audit_id` int(11) NOT NULL,
  `column_name` varchar(100) DEFAULT NULL,
  `oldval` varchar(255) DEFAULT NULL,
  `newval` varchar(255) DEFAULT NULL,
  UNIQUE KEY `audit_id_2` (`audit_id`,`column_name`),
  KEY `audit_id` (`audit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `audit_values`
--

TRUNCATE TABLE `audit_values`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup_by_campaign`
--

DROP TABLE IF EXISTS `backup_by_campaign`;
CREATE TABLE IF NOT EXISTS `backup_by_campaign` (
  `backup__by_campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `months_ago` int(11) NOT NULL DEFAULT '0',
  `months_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`backup__by_campaign_id`),
  KEY `FK_backup_by_campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Backup by campaign settings' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `backup_by_campaign`
--

TRUNCATE TABLE `backup_by_campaign`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup_campaign_history`
--

DROP TABLE IF EXISTS `backup_campaign_history`;
CREATE TABLE IF NOT EXISTS `backup_campaign_history` (
  `backup_campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_date` datetime NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `update_date_from` date DEFAULT NULL,
  `update_date_to` date DEFAULT NULL,
  `renewal_date_from` date DEFAULT NULL,
  `renewal_date_to` date DEFAULT NULL,
  `num_records` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `restored` tinyint(4) NOT NULL DEFAULT '0',
  `restored_date` datetime DEFAULT NULL,
  PRIMARY KEY (`backup_campaign_id`),
  KEY `FK_backup_campaign_id` (`campaign_id`),
  KEY `FK_backup_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Backup history for the campaigns' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `backup_campaign_history`
--

TRUNCATE TABLE `backup_campaign_history`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch`
--

DROP TABLE IF EXISTS `branch`;
CREATE TABLE IF NOT EXISTS `branch` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(100) NOT NULL,
  `branch_email` varchar(250) DEFAULT NULL,
  `branch_status` tinyint(1) NOT NULL DEFAULT '1',
  `region_id` int(11) DEFAULT NULL,
  `map_icon` varchar(50) DEFAULT NULL,
  `ics` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `branch`
--

TRUNCATE TABLE `branch`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch_addresses`
--

DROP TABLE IF EXISTS `branch_addresses`;
CREATE TABLE IF NOT EXISTS `branch_addresses` (
  `branch_id` int(11) NOT NULL,
  `add1` varchar(150) NOT NULL,
  `add2` varchar(150) NOT NULL,
  `add3` varchar(150) NOT NULL,
  `add4` varchar(150) NOT NULL,
  `county` varchar(50) NOT NULL,
  `postcode` varchar(50) NOT NULL,
  `location_id` int(11) NOT NULL,
  `covered_area` int(11) DEFAULT NULL,
  PRIMARY KEY (`branch_id`),
  KEY `FK_branch_area_location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `branch_addresses`
--

TRUNCATE TABLE `branch_addresses`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch_campaigns`
--

DROP TABLE IF EXISTS `branch_campaigns`;
CREATE TABLE IF NOT EXISTS `branch_campaigns` (
  `branch_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `branch_id_2` (`branch_id`,`campaign_id`),
  KEY `branch_id` (`branch_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `branch_campaigns`
--

TRUNCATE TABLE `branch_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch_regions`
--

DROP TABLE IF EXISTS `branch_regions`;
CREATE TABLE IF NOT EXISTS `branch_regions` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `region_name` varchar(100) NOT NULL,
  `region_email` varchar(250) DEFAULT NULL,
  `ics` tinyint(4) NOT NULL DEFAULT '0',
  `default_branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`region_id`),
  KEY `branch_regions_ibfk_4` (`default_branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `branch_regions`
--

TRUNCATE TABLE `branch_regions`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch_region_users`
--

DROP TABLE IF EXISTS `branch_region_users`;
CREATE TABLE IF NOT EXISTS `branch_region_users` (
  `region_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_manager` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `region_id` (`region_id`,`user_id`),
  KEY `region_id_2` (`region_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `branch_region_users`
--

TRUNCATE TABLE `branch_region_users`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branch_user`
--

DROP TABLE IF EXISTS `branch_user`;
CREATE TABLE IF NOT EXISTS `branch_user` (
  `branch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_manager` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`branch_id`,`user_id`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `branch_user`
--

TRUNCATE TABLE `branch_user`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `call_log`
--

DROP TABLE IF EXISTS `call_log`;
CREATE TABLE IF NOT EXISTS `call_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `call_date` datetime NOT NULL,
  `duration` time NOT NULL,
  `ring_time` int(11) NOT NULL,
  `call_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `call_from` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name_from` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_from` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `call_to` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `call_to_ext` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name_to` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_to` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `inbound` tinyint(1) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D663C42E93CB796C` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `call_log`
--

TRUNCATE TABLE `call_log`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `call_log_file`
--

DROP TABLE IF EXISTS `call_log_file`;
CREATE TABLE IF NOT EXISTS `call_log_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_date` date NOT NULL,
  `unit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `call_log_file`
--

TRUNCATE TABLE `call_log_file`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE IF NOT EXISTS `campaigns` (
  `campaign_id` int(5) NOT NULL AUTO_INCREMENT,
  `campaign_group_id` int(11) DEFAULT NULL,
  `campaign_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `record_layout` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '2col.php',
  `logo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_type_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `client_id` tinyint(4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `campaign_status` int(1) NOT NULL,
  `email_recipients` mediumtext CHARACTER SET utf8,
  `reassign_to` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `custom_panel_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `custom_panel_format` tinyint(4) NOT NULL DEFAULT '1',
  `min_quote_days` int(11) DEFAULT NULL,
  `max_quote_days` int(11) DEFAULT NULL,
  `daily_data` int(11) NOT NULL DEFAULT '0',
  `map_icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'fa-map-marker',
  `max_dials` int(11) DEFAULT NULL,
  `virgin_order_1` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `virgin_order_2` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `virgin_order_string` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `virgin_order_join` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `telephone_protocol` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'callto:',
  `telephone_prefix` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `timeout` int(11) DEFAULT NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `client_id` (`client_id`),
  KEY `campaign_group_id` (`campaign_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Truncar tablas antes de insertar `campaigns`
--

TRUNCATE TABLE `campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaigns_to_features`
--

DROP TABLE IF EXISTS `campaigns_to_features`;
CREATE TABLE IF NOT EXISTS `campaigns_to_features` (
  `campaign_id` int(3) NOT NULL,
  `feature_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id_2` (`campaign_id`,`feature_id`),
  KEY `campaign_id` (`campaign_id`,`feature_id`),
  KEY `feature_id` (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `campaigns_to_features`
--

TRUNCATE TABLE `campaigns_to_features`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_appointment_types`
--

DROP TABLE IF EXISTS `campaign_appointment_types`;
CREATE TABLE IF NOT EXISTS `campaign_appointment_types` (
  `campaign_id` int(11) NOT NULL,
  `appointment_type_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`appointment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `campaign_appointment_types`
--

TRUNCATE TABLE `campaign_appointment_types`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_features`
--

DROP TABLE IF EXISTS `campaign_features`;
CREATE TABLE IF NOT EXISTS `campaign_features` (
  `feature_id` int(3) NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `panel_path` varchar(50) CHARACTER SET utf8 NOT NULL,
  `permission_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`feature_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Truncar tablas antes de insertar `campaign_features`
--

TRUNCATE TABLE `campaign_features`;
--
-- Volcado de datos para la tabla `campaign_features`
--

INSERT INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES
(1, 'Contacts', 'contacts.php', NULL),
(2, 'Company', 'company.php', NULL),
(3, 'Update Record', 'record_update.php', NULL),
(4, 'Sticky Note', 'sticky.php', NULL),
(5, 'Ownership Changer', 'ownership.php', NULL),
(6, 'Scripts', 'scripts.php', NULL),
(7, 'History', 'history.php', NULL),
(8, 'Custom Info', 'custom_info.php', NULL),
(9, 'Emails', 'emails.php', NULL),
(10, 'Appointment Setting', 'appointments.php', NULL),
(11, 'Surveys', 'survey.php', NULL),
(12, 'Recordings', 'recordings.php', NULL),
(13, 'Attachments', 'attachments.php', NULL),
(14, 'Webform', 'webform.php', NULL),
(15, 'Related', 'related.php', NULL),
(16, 'SMS', 'sms.php', NULL),
(17, 'Slot Availability', 'availability.php', NULL),
(18, 'Branches', 'branch_info.php', NULL),
(19, 'Orders', 'orders.php', NULL),
(20, 'Journey Simulator', 'quick_planner.php', NULL),
(21, 'Tasks', 'tasks.php', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_groups`
--

DROP TABLE IF EXISTS `campaign_groups`;
CREATE TABLE IF NOT EXISTS `campaign_groups` (
  `campaign_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_group_name` varchar(100) NOT NULL,
  PRIMARY KEY (`campaign_group_id`),
  UNIQUE KEY `campaign_group_name` (`campaign_group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `campaign_groups`
--

TRUNCATE TABLE `campaign_groups`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_managers`
--

DROP TABLE IF EXISTS `campaign_managers`;
CREATE TABLE IF NOT EXISTS `campaign_managers` (
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `campaign_managers`
--

TRUNCATE TABLE `campaign_managers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_permissions`
--

DROP TABLE IF EXISTS `campaign_permissions`;
CREATE TABLE IF NOT EXISTS `campaign_permissions` (
  `campaign_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `permission_state` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `campaign_id` (`campaign_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `campaign_permissions`
--

TRUNCATE TABLE `campaign_permissions`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_tasks`
--

DROP TABLE IF EXISTS `campaign_tasks`;
CREATE TABLE IF NOT EXISTS `campaign_tasks` (
  `campaign_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `campaign_tasks`
--

TRUNCATE TABLE `campaign_tasks`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_triggers`
--

DROP TABLE IF EXISTS `campaign_triggers`;
CREATE TABLE IF NOT EXISTS `campaign_triggers` (
  `trigger_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `camp_triggers_Campaign` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `campaign_triggers`
--

TRUNCATE TABLE `campaign_triggers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_types`
--

DROP TABLE IF EXISTS `campaign_types`;
CREATE TABLE IF NOT EXISTS `campaign_types` (
  `campaign_type_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `campaign_type_desc` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`campaign_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Truncar tablas antes de insertar `campaign_types`
--

TRUNCATE TABLE `campaign_types`;
--
-- Volcado de datos para la tabla `campaign_types`
--

INSERT INTO `campaign_types` (`campaign_type_id`, `campaign_type_desc`) VALUES
(3, 'B2C'),
(4, 'B2B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign_xfers`
--

DROP TABLE IF EXISTS `campaign_xfers`;
CREATE TABLE IF NOT EXISTS `campaign_xfers` (
  `campaign_id` int(3) NOT NULL,
  `xfer_campaign` int(3) NOT NULL,
  UNIQUE KEY `campxfer` (`campaign_id`,`xfer_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `campaign_xfers`
--

TRUNCATE TABLE `campaign_xfers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `ci_sessions`
--

TRUNCATE TABLE `ci_sessions`;
--
-- Volcado de datos para la tabla `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('1d00d97193cf50027f331c01eda810ac', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.103 Safari/537.36', 1455016939, ''),
('291bc4e6b91d07a7545abc80c98f79f5', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 1455013837, ''),
('498bcb534e63b9dee091c4eebd99f320', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 1455013837, ''),
('6ab283a325080fa34975e745e9321aac', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 1455017062, ''),
('e933e34bd1dbb932d7d0edf17837018b', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 1455017071, ''),
('ef1163baf46325cb2554d01ac6cd89c6', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0', 1455015794, ''),
('f53f67c6cb43b7f3578600896092249e', '84.19.44.186', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 1455017071, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `clients`
--

TRUNCATE TABLE `clients`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client_refs`
--

DROP TABLE IF EXISTS `client_refs`;
CREATE TABLE IF NOT EXISTS `client_refs` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `client_ref` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`urn`),
  UNIQUE KEY `client_ref2` (`urn`,`client_ref`),
  KEY `client_ref` (`client_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `client_refs`
--

TRUNCATE TABLE `client_refs`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` varchar(290) CHARACTER SET utf8 DEFAULT NULL,
  `conumber` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `turnover` int(11) DEFAULT NULL,
  `employees` int(11) DEFAULT NULL,
  `website` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_of_creation` date DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `companies`
--

TRUNCATE TABLE `companies`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_addresses`
--

DROP TABLE IF EXISTS `company_addresses`;
CREATE TABLE IF NOT EXISTS `company_addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `add1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `county` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `postcode` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  `add4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locality` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  UNIQUE KEY `company_id_2` (`company_id`,`add1`,`add2`,`postcode`),
  KEY `company_id` (`company_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `company_addresses`
--

TRUNCATE TABLE `company_addresses`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_subsectors`
--

DROP TABLE IF EXISTS `company_subsectors`;
CREATE TABLE IF NOT EXISTS `company_subsectors` (
  `company_id` int(11) NOT NULL,
  `subsector_id` int(11) NOT NULL,
  UNIQUE KEY `company_id_2` (`company_id`,`subsector_id`),
  KEY `company_id` (`company_id`),
  KEY `subsector_id` (`subsector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `company_subsectors`
--

TRUNCATE TABLE `company_subsectors`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_telephone`
--

DROP TABLE IF EXISTS `company_telephone`;
CREATE TABLE IF NOT EXISTS `company_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `ctps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  UNIQUE KEY `company_id_2` (`company_id`,`telephone_number`),
  KEY `company_id` (`company_id`),
  KEY `telephone_number` (`telephone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `company_telephone`
--

TRUNCATE TABLE `company_telephone`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuration`
--

DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `use_fullname` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `configuration`
--

TRUNCATE TABLE `configuration`;
--
-- Volcado de datos para la tabla `configuration`
--

INSERT INTO `configuration` (`use_fullname`) VALUES
(1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `fullname` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `gender` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `email_optout` tinyint(1) DEFAULT NULL,
  `website` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `linkedin` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `facebook` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `notes` varchar(350) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  `sort` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  UNIQUE KEY `urn_2` (`urn`,`fullname`,`dob`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `contacts`
--

TRUNCATE TABLE `contacts`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_addresses`
--

DROP TABLE IF EXISTS `contact_addresses`;
CREATE TABLE IF NOT EXISTS `contact_addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `add1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `county` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `postcode` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  `add4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locality` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `contact_id` (`contact_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `contact_addresses`
--

TRUNCATE TABLE `contact_addresses`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_status`
--

DROP TABLE IF EXISTS `contact_status`;
CREATE TABLE IF NOT EXISTS `contact_status` (
  `contact_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_status_name` mediumtext CHARACTER SET utf8,
  `score_threshold` int(11) DEFAULT NULL,
  `colour` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`contact_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `contact_status`
--

TRUNCATE TABLE `contact_status`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_telephone`
--

DROP TABLE IF EXISTS `contact_telephone`;
CREATE TABLE IF NOT EXISTS `contact_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `tps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  UNIQUE KEY `contact_id_2` (`contact_id`,`telephone_number`),
  KEY `contact_id` (`contact_id`),
  KEY `telephone_number` (`telephone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `contact_telephone`
--

TRUNCATE TABLE `contact_telephone`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cross_transfers`
--

DROP TABLE IF EXISTS `cross_transfers`;
CREATE TABLE IF NOT EXISTS `cross_transfers` (
  `history_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `history_id` (`history_id`,`campaign_id`),
  KEY `history_id_2` (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncar tablas antes de insertar `cross_transfers`
--

TRUNCATE TABLE `cross_transfers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dashboards`
--

DROP TABLE IF EXISTS `dashboards`;
CREATE TABLE IF NOT EXISTS `dashboards` (
  `dashboard_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`dashboard_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `dashboards`
--

TRUNCATE TABLE `dashboards`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dashboard_by_user`
--

DROP TABLE IF EXISTS `dashboard_by_user`;
CREATE TABLE IF NOT EXISTS `dashboard_by_user` (
  `dashboard_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `dashboard_id` (`dashboard_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `dashboard_by_user`
--

TRUNCATE TABLE `dashboard_by_user`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dashboard_reports`
--

DROP TABLE IF EXISTS `dashboard_reports`;
CREATE TABLE IF NOT EXISTS `dashboard_reports` (
  `dashboard_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `column_size` int(11) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`dashboard_id`,`report_id`),
  UNIQUE KEY `dashboard_reports_order_uindex` (`position`),
  KEY `report_id` (`report_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `dashboard_reports`
--

TRUNCATE TABLE `dashboard_reports`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_columns`
--

DROP TABLE IF EXISTS `datatables_columns`;
CREATE TABLE IF NOT EXISTS `datatables_columns` (
  `column_id` int(11) NOT NULL AUTO_INCREMENT,
  `column_title` varchar(100) NOT NULL,
  `column_alias` varchar(50) NOT NULL,
  `column_select` varchar(255) NOT NULL,
  `column_order` varchar(255) NOT NULL,
  `column_group` varchar(50) NOT NULL,
  `column_table` varchar(50) NOT NULL,
  PRIMARY KEY (`column_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=85 ;

--
-- Truncar tablas antes de insertar `datatables_columns`
--

TRUNCATE TABLE `datatables_columns`;
--
-- Volcado de datos para la tabla `datatables_columns`
--

INSERT INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(1, 'URN', 'urn', 'r.urn', 'r.urn', 'Record', 'records'),
(2, 'Outcome', '', 'outcome', 'outcome', 'Record', 'outcomes'),
(3, 'Company Name', 'company_name', 'com.name', 'com.name', 'Company', 'companies'),
(4, 'Contact Name', 'contact_name', 'fullname', 'fullname', 'Contact', 'contacts'),
(5, 'Record Status', '', 'status_name', 'status_name', 'Record', 'status_list'),
(6, 'Parked Status', '', 'park_reason', 'park_reason', 'Record', 'park_codes'),
(7, 'Next Action', 'nextcall', 'date_format(r.nextcall,''%d/%m/%Y %H:%i'')', 'r.nextcall', 'Record', 'records'),
(8, 'Last Action', 'lastcall', 'date_format(r.date_updated,''%d/%m/%Y %H:%i'')', 'r.date_updated', 'Record', 'records'),
(9, 'Date Added', 'date_added', 'date_format(r.date_added,''%d/%m/%y %H:%i'')', 'r.date_added', 'Record', 'records'),
(10, 'Sector', '', 'sector_name', 'sector_name', 'Company', 'sectors'),
(11, 'Subector Name', '', 'subsector_name', 'subsector_name', 'Company', 'subsectors'),
(12, 'Company Phone', 'company_telephone', 'comt.telephone_number', 'comt.telephone_number', 'Company', 'company_telephone'),
(13, 'Contact Phone', 'contact_telephone', 'cont.telephone_number', 'cont.telephone_number', 'Contact', 'contact_telephone'),
(14, 'Company Postcode', 'company_postcode', 'coma.postcode', 'coma.postcode', 'Company', 'company_addresses'),
(15, 'Client Ref', '', 'client_ref', 'client_ref', 'Record', 'client_refs'),
(16, 'Icon', 'color_icon', 'CONCAT(IFNULL(r.map_icon,''''),IFNULL(camp.map_icon,''''))', 'r.map_icon', 'Record', 'records'),
(17, 'Campaign', '', 'campaign_name', 'campaign_name', 'Campaign', 'campaigns'),
(18, 'Campaign Type', '', 'campaign_type_desc', 'campaign_type_desc', 'Campaign', 'campaign_types'),
(19, 'Client', '', 'client_name', 'client_name', 'Campaign', 'clients'),
(20, 'Data Source', 'source_name', 'source_name', 'source_name', 'Record', 'data_sources'),
(21, 'c1', '', 'c1', 'c1', 'Extra Fields', 'record_details'),
(23, 'c2', '', 'c2', 'c2', 'Extra Fields', 'record_details'),
(25, 'c3', '', 'c3', 'c3', 'Extra Fields', 'record_details'),
(27, 'c4', '', 'c4', 'c4', 'Extra Fields', 'record_details'),
(29, 'c5', '', 'c5', 'c5', 'Extra Fields', 'record_details'),
(31, 'c6', '', 'c6', 'c6', 'Extra Fields', 'record_details'),
(33, 'n1', '', 'n1', 'n1', 'Extra Fields', 'record_details'),
(34, 'n2', '', 'n2', 'n2', 'Extra Fields', 'record_details'),
(35, 'd1', '', 'd1', 'd1', 'Extra Fields', 'record_details'),
(36, 'd2', '', 'd2', 'd2', 'Extra Fields', 'record_details'),
(37, 'dt1', '', 'dt1', 'dt1', 'Extra Fields', 'record_details'),
(38, 'dt2', '', 'dt2', 'dt2', 'Extra Fields', 'record_details'),
(39, 'Dials', '', 'r.dials', 'r.dials', 'Record', 'records'),
(41, 'Color', 'color_dot', 'r.record_color', 'r.record_color', 'Record', 'records'),
(44, 'Start Date', 'start', 'date_format(a.start,''%d/%m/%Y'')', 'a.start', 'Appointment', 'appointments'),
(45, 'End Time', 'end_time', 'date_format(a.end,''%l:%i %p'')', 'time(a.end)', 'Appointment', 'appointments'),
(46, 'App. Postcode', 'appointment_postcode', 'a.postcode', 'a.postcode', 'Appointment', 'appointments'),
(47, 'App. Created', 'appointment_created', 'date_format(a.date_added,''%d/%m/%Y %H:%i'')', 'a.date_added', 'Appointment', 'appointments'),
(48, 'App. Title', 'appointment_title', 'a.title', 'a.title', 'Appointment', 'appointments'),
(49, 'App. Notes', 'appointment_notes', 'if(char_length(a.text)>100,concat(substring(a.text,1,100),''...''),substring(a.text,1,100))', 'a.text', 'Appointment', 'appointments'),
(50, 'Start Time', 'start_time', 'date_format(a.start,''%l:%i %p'')', 'time(a.start)', 'Appointment', 'appointments'),
(51, 'Created By', 'created_by', 'appointment_users.name', 'appointment_users.name', 'Appointment', 'appointments'),
(52, 'Attendees', 'attendees', 'group_concat(au.name)', 'au.name', 'Appointment', 'appointments'),
(53, 'Appointment Type', 'appointment_type', 'appointment_type', 'appointment_type', 'Appointment', 'appointment_types'),
(58, 'Last Comment', 'last_comment', 'record_comments.last_comment', 'record_comments.last_comment', 'Record', 'record_comments'),
(59, 'Contact Email', 'contact_email', 'con.email', 'con.email', 'Contact', 'contacts'),
(60, 'c7', '', 'c7', 'c7', 'Extra Fields', 'record_details'),
(61, 'c8', '', 'c8', 'c8', 'Extra Fields', 'record_details'),
(62, 'c9', '', 'c9', 'c9', 'Extra Fields', 'record_details'),
(63, 'c10', '', 'c10', 'c10', 'Extra Fields', 'record_details'),
(64, 'd4', '', 'd4', 'd4', 'Extra Fields', 'record_details'),
(65, 'd5', '', 'd5', 'd5', 'Extra Fields', 'record_details'),
(66, 'd6', '', 'd6', 'd6', 'Extra Fields', 'record_details'),
(67, 'd7', '', 'd7', 'd7', 'Extra Fields', 'record_details'),
(68, 'd8', '', 'd8', 'd8', 'Extra Fields', 'record_details'),
(69, 'd9', '', 'd9', 'd9', 'Extra Fields', 'record_details'),
(70, 'd10', '', 'd10', 'd10', 'Extra Fields', 'record_details'),
(71, 'dt4', '', 'dt4', 'dt4', 'Extra Fields', 'record_details'),
(72, 'dt5', '', 'dt5', 'dt5', 'Extra Fields', 'record_details'),
(73, 'dt6', '', 'dt6', 'dt6', 'Extra Fields', 'record_details'),
(74, 'dt7', '', 'dt7', 'dt7', 'Extra Fields', 'record_details'),
(75, 'dt8', '', 'dt8', 'dt8', 'Extra Fields', 'record_details'),
(76, 'dt9', '', 'dt9', 'dt9', 'Extra Fields', 'record_details'),
(77, 'dt10', '', 'dt10', 'dt10', 'Extra Fields', 'record_details'),
(78, 'n4', '', 'n4', 'n4', 'Extra Fields', 'record_details'),
(79, 'n5', '', 'n5', 'n5', 'Extra Fields', 'record_details'),
(80, 'n6', '', 'n6', 'n6', 'Extra Fields', 'record_details'),
(81, 'n7', '', 'n7', 'n7', 'Extra Fields', 'record_details'),
(82, 'n8', '', 'n8', 'n8', 'Extra Fields', 'record_details'),
(83, 'n9', '', 'n9', 'n9', 'Extra Fields', 'record_details'),
(84, 'n10', '', 'n10', 'n10', 'Extra Fields', 'record_details');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_table_columns`
--

DROP TABLE IF EXISTS `datatables_table_columns`;
CREATE TABLE IF NOT EXISTS `datatables_table_columns` (
  `table_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  UNIQUE KEY `table_id` (`table_id`,`column_id`),
  KEY `column_id` (`column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `datatables_table_columns`
--

TRUNCATE TABLE `datatables_table_columns`;
--
-- Volcado de datos para la tabla `datatables_table_columns`
--

INSERT INTO `datatables_table_columns` (`table_id`, `column_id`) VALUES
(1, 1),
(3, 1),
(1, 2),
(3, 2),
(1, 3),
(3, 3),
(1, 4),
(3, 4),
(1, 5),
(3, 5),
(1, 6),
(3, 6),
(1, 7),
(3, 7),
(1, 8),
(3, 8),
(1, 9),
(3, 9),
(1, 10),
(1, 11),
(1, 12),
(3, 12),
(1, 13),
(3, 13),
(1, 14),
(1, 15),
(1, 16),
(3, 16),
(1, 17),
(3, 17),
(1, 18),
(3, 18),
(1, 19),
(3, 19),
(1, 20),
(3, 20),
(1, 21),
(3, 21),
(1, 23),
(3, 23),
(1, 25),
(3, 25),
(1, 27),
(3, 27),
(1, 29),
(3, 29),
(1, 31),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 41),
(3, 41),
(3, 44),
(3, 45),
(3, 46),
(3, 47),
(3, 48),
(3, 49),
(3, 50),
(3, 51),
(3, 52),
(3, 53),
(1, 58),
(3, 58),
(1, 59),
(3, 59);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_table_names`
--

DROP TABLE IF EXISTS `datatables_table_names`;
CREATE TABLE IF NOT EXISTS `datatables_table_names` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Truncar tablas antes de insertar `datatables_table_names`
--

TRUNCATE TABLE `datatables_table_names`;
--
-- Volcado de datos para la tabla `datatables_table_names`
--

INSERT INTO `datatables_table_names` (`table_id`, `table_name`) VALUES
(1, 'Records'),
(2, 'History'),
(3, 'Appointments'),
(4, 'Tasks');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_user_columns`
--

DROP TABLE IF EXISTS `datatables_user_columns`;
CREATE TABLE IF NOT EXISTS `datatables_user_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`column_id`,`table_id`),
  KEY `column_id` (`column_id`),
  KEY `table_id` (`table_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `datatables_user_columns`
--

TRUNCATE TABLE `datatables_user_columns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_views`
--

DROP TABLE IF EXISTS `datatables_views`;
CREATE TABLE IF NOT EXISTS `datatables_views` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `view_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `view_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `selected` tinyint(1) NOT NULL,
  PRIMARY KEY (`view_id`),
  KEY `user_id` (`user_id`),
  KEY `table_id` (`table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `datatables_views`
--

TRUNCATE TABLE `datatables_views`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datatables_view_columns`
--

DROP TABLE IF EXISTS `datatables_view_columns`;
CREATE TABLE IF NOT EXISTS `datatables_view_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `view_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `view_id` (`view_id`,`column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `datatables_view_columns`
--

TRUNCATE TABLE `datatables_view_columns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data_pots`
--

DROP TABLE IF EXISTS `data_pots`;
CREATE TABLE IF NOT EXISTS `data_pots` (
  `pot_id` int(11) NOT NULL AUTO_INCREMENT,
  `pot_name` varchar(50) NOT NULL,
  PRIMARY KEY (`pot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `data_pots`
--

TRUNCATE TABLE `data_pots`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data_sources`
--

DROP TABLE IF EXISTS `data_sources`;
CREATE TABLE IF NOT EXISTS `data_sources` (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `cost_per_record` float DEFAULT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `data_sources`
--

TRUNCATE TABLE `data_sources`;
--
-- Volcado de datos para la tabla `data_sources`
--

INSERT INTO `data_sources` (`source_id`, `source_name`, `cost_per_record`) VALUES
(1, 'Manual', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `default_hours`
--

DROP TABLE IF EXISTS `default_hours`;
CREATE TABLE IF NOT EXISTS `default_hours` (
  `default_hours_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL DEFAULT '0',
  `campaign_id` int(5) NOT NULL DEFAULT '0',
  `duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
  PRIMARY KEY (`default_hours_id`),
  KEY `FK_default_hours_users` (`user_id`),
  KEY `FK_default_hours_campaigns_` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Default working time by agent for a particular campaign' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `default_hours`
--

TRUNCATE TABLE `default_hours`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `default_time`
--

DROP TABLE IF EXISTS `default_time`;
CREATE TABLE IF NOT EXISTS `default_time` (
  `default_time_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL DEFAULT '0',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '230:59:59',
  PRIMARY KEY (`default_time_id`),
  KEY `FK__users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Default time defined for an agent' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `default_time`
--

TRUNCATE TABLE `default_time`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_history`
--

DROP TABLE IF EXISTS `email_history`;
CREATE TABLE IF NOT EXISTS `email_history` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `body` mediumtext CHARACTER SET utf8 NOT NULL,
  `send_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `send_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bcc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `read_confirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if the user read the email',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `read_confirmed_date` timestamp NULL DEFAULT NULL,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `pending` tinyint(4) NOT NULL DEFAULT '0',
  `cron_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `FK2_user_id` (`user_id`),
  KEY `FK3_record_urn` (`urn`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `email_history`
--

TRUNCATE TABLE `email_history`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_history_attachments`
--

DROP TABLE IF EXISTS `email_history_attachments`;
CREATE TABLE IF NOT EXISTS `email_history_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_email_id` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `email_history_attachments`
--

TRUNCATE TABLE `email_history_attachments`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE IF NOT EXISTS `email_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `template_subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `template_body` mediumtext CHARACTER SET utf8 NOT NULL,
  `template_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_cc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `template_bcc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `template_hostname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `template_port` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `template_username` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `template_password` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `template_encryption` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `email_templates`
--

TRUNCATE TABLE `email_templates`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_template_attachments`
--

DROP TABLE IF EXISTS `email_template_attachments`;
CREATE TABLE IF NOT EXISTS `email_template_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `email_template_attachments`
--

TRUNCATE TABLE `email_template_attachments`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_template_to_campaigns`
--

DROP TABLE IF EXISTS `email_template_to_campaigns`;
CREATE TABLE IF NOT EXISTS `email_template_to_campaigns` (
  `template_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  UNIQUE KEY `tempcamp` (`template_id`,`campaign_id`),
  KEY `template_id` (`template_id`),
  KEY `campanign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `email_template_to_campaigns`
--

TRUNCATE TABLE `email_template_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_triggers`
--

DROP TABLE IF EXISTS `email_triggers`;
CREATE TABLE IF NOT EXISTS `email_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `email_triggers`
--

TRUNCATE TABLE `email_triggers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_trigger_recipients`
--

DROP TABLE IF EXISTS `email_trigger_recipients`;
CREATE TABLE IF NOT EXISTS `email_trigger_recipients` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `email_trigger_recipients`
--

TRUNCATE TABLE `email_trigger_recipients`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_unsubscribe`
--

DROP TABLE IF EXISTS `email_unsubscribe`;
CREATE TABLE IF NOT EXISTS `email_unsubscribe` (
  `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_address` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(20) NOT NULL,
  PRIMARY KEY (`unsubscribe_id`),
  UNIQUE KEY `email_address` (`email_address`,`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `email_unsubscribe`
--

TRUNCATE TABLE `email_unsubscribe`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `export_forms`
--

DROP TABLE IF EXISTS `export_forms`;
CREATE TABLE IF NOT EXISTS `export_forms` (
  `export_forms_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `header` text COLLATE utf8_unicode_ci NOT NULL,
  `query` text COLLATE utf8_unicode_ci NOT NULL,
  `order_by` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_filter` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_filter` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pot_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`export_forms_id`),
  KEY `pot_filter` (`pot_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `export_forms`
--

TRUNCATE TABLE `export_forms`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `export_to_users`
--

DROP TABLE IF EXISTS `export_to_users`;
CREATE TABLE IF NOT EXISTS `export_to_users` (
  `export_forms_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`export_forms_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `export_to_users`
--

TRUNCATE TABLE `export_to_users`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `urn` int(11) NOT NULL,
  `user_id` tinyint(3) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `favorites`
--

TRUNCATE TABLE `favorites`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `filesize` int(11) DEFAULT NULL,
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_sent` varchar(50) DEFAULT NULL,
  `doc_hash` varchar(50) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_on` timestamp NULL DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `files`
--

TRUNCATE TABLE `files`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_cart_config`
--

DROP TABLE IF EXISTS `flexicart_cart_config`;
CREATE TABLE IF NOT EXISTS `flexicart_cart_config` (
  `config_id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `config_order_number_prefix` varchar(50) NOT NULL DEFAULT '',
  `config_order_number_suffix` varchar(50) NOT NULL DEFAULT '',
  `config_increment_order_number` tinyint(1) NOT NULL DEFAULT '0',
  `config_min_order` smallint(5) NOT NULL DEFAULT '0',
  `config_quantity_decimals` tinyint(1) NOT NULL DEFAULT '0',
  `config_quantity_limited_by_stock` tinyint(1) NOT NULL DEFAULT '0',
  `config_increment_duplicate_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_remove_no_stock_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_auto_allocate_stock` tinyint(1) NOT NULL DEFAULT '0',
  `config_save_ban_shipping_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_weight_type` varchar(25) NOT NULL DEFAULT '',
  `config_weight_decimals` tinyint(1) NOT NULL DEFAULT '0',
  `config_display_tax_prices` tinyint(1) NOT NULL DEFAULT '0',
  `config_price_inc_tax` tinyint(1) NOT NULL DEFAULT '0',
  `config_multi_row_duplicate_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_dynamic_reward_points` tinyint(1) NOT NULL DEFAULT '0',
  `config_reward_point_multiplier` double(8,4) NOT NULL DEFAULT '0.0000',
  `config_reward_voucher_multiplier` double(8,4) NOT NULL DEFAULT '0.0000',
  `config_reward_voucher_ratio` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_point_days_pending` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_point_days_valid` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_voucher_days_valid` smallint(5) NOT NULL DEFAULT '0',
  `config_custom_status_1` varchar(50) NOT NULL DEFAULT '',
  `config_custom_status_2` varchar(50) NOT NULL DEFAULT '',
  `config_custom_status_3` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  KEY `config_id` (`config_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `flexicart_cart_config`
--

TRUNCATE TABLE `flexicart_cart_config`;
--
-- Volcado de datos para la tabla `flexicart_cart_config`
--

INSERT INTO `flexicart_cart_config` (`config_id`, `config_order_number_prefix`, `config_order_number_suffix`, `config_increment_order_number`, `config_min_order`, `config_quantity_decimals`, `config_quantity_limited_by_stock`, `config_increment_duplicate_items`, `config_remove_no_stock_items`, `config_auto_allocate_stock`, `config_save_ban_shipping_items`, `config_weight_type`, `config_weight_decimals`, `config_display_tax_prices`, `config_price_inc_tax`, `config_multi_row_duplicate_items`, `config_dynamic_reward_points`, `config_reward_point_multiplier`, `config_reward_voucher_multiplier`, `config_reward_voucher_ratio`, `config_reward_point_days_pending`, `config_reward_point_days_valid`, `config_reward_voucher_days_valid`, `config_custom_status_1`, `config_custom_status_2`, `config_custom_status_3`) VALUES
(1, '', '', 1, 0, 0, 1, 1, 0, 1, 0, 'gram', 0, 1, 1, 0, 1, 10.0000, 0.0100, 250, 14, 365, 365, '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_cart_data`
--

DROP TABLE IF EXISTS `flexicart_cart_data`;
CREATE TABLE IF NOT EXISTS `flexicart_cart_data` (
  `cart_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'user_id',
  `cart_data_array` text NOT NULL,
  `cart_data_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cart_data_readonly_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cart_data_id`),
  UNIQUE KEY `cart_data_id` (`cart_data_id`) USING BTREE,
  KEY `cart_data_user_fk` (`user_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Truncar tablas antes de insertar `flexicart_cart_data`
--

TRUNCATE TABLE `flexicart_cart_data`;
--
-- Volcado de datos para la tabla `flexicart_cart_data`
--

INSERT INTO `flexicart_cart_data` (`cart_data_id`, `user_id`, `cart_data_array`, `cart_data_date`, `cart_data_readonly_status`) VALUES
(20, 1, 'a:3:{s:5:"items";a:2:{s:32:"c4ca4238a0b923820dcc509a6f75849b";a:15:{s:6:"row_id";s:32:"c4ca4238a0b923820dcc509a6f75849b";s:2:"id";s:1:"1";s:4:"name";s:15:"Cream of tomato";s:8:"quantity";d:2;s:5:"price";d:1;s:14:"stock_quantity";s:2:"95";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}s:32:"c81e728d9d4c2f636f067f89cc14862c";a:15:{s:6:"row_id";s:32:"c81e728d9d4c2f636f067f89cc14862c";s:2:"id";s:1:"2";s:4:"name";s:16:"Cream of chicken";s:8:"quantity";d:5;s:5:"price";d:1;s:14:"stock_quantity";s:2:"98";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}}s:7:"summary";a:9:{s:10:"total_rows";i:2;s:11:"total_items";d:7;s:12:"total_weight";d:0;s:19:"total_reward_points";d:70;s:18:"item_summary_total";d:7;s:14:"shipping_total";d:3.9500000000000002;s:9:"tax_total";d:1.8279999999999998;s:15:"surcharge_total";d:0;s:5:"total";d:10.949999999999999;}s:8:"settings";a:6:{s:8:"currency";a:7:{s:4:"name";s:3:"GBP";s:13:"exchange_rate";s:6:"1.0000";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";s:7:"default";a:5:{s:4:"name";s:3:"GBP";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";}}s:8:"shipping";a:7:{s:2:"id";s:1:"1";s:4:"name";s:20:"UK Standard Shipping";s:11:"description";s:8:"2-3 Days";s:5:"value";s:4:"3.95";s:8:"tax_rate";N;s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"0";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:11:{s:9:"surcharge";d:0;s:23:"separate_shipping_value";i:0;s:14:"separate_items";i:0;s:14:"separate_value";i:0;s:15:"separate_weight";i:0;s:10:"free_items";i:0;s:10:"free_value";i:0;s:11:"free_weight";i:0;s:21:"banned_shipping_items";a:0:{}s:23:"separate_shipping_items";a:0:{}s:19:"item_shipping_rates";a:0:{}}}s:3:"tax";a:5:{s:4:"name";s:3:"VAT";s:4:"rate";s:7:"20.0000";s:13:"internal_rate";s:7:"20.0000";s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"4";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:9:{s:14:"item_total_tax";d:1.1666000000000001;s:12:"shipping_tax";d:0.65800000000000003;s:17:"item_discount_tax";d:0.16669999999999999;s:20:"summary_discount_tax";i:0;s:18:"reward_voucher_tax";i:0;s:13:"surcharge_tax";i:0;s:8:"cart_tax";d:1.6579000000000002;s:18:"cart_taxable_value";d:8.2897999999999996;s:22:"cart_non_taxable_value";d:0;}}s:9:"discounts";a:6:{s:5:"codes";a:0:{}s:6:"manual";a:0:{}s:12:"active_items";a:1:{s:32:"c4ca4238a0b923820dcc509a6f75849b";a:7:{s:2:"id";i:32;s:11:"description";s:36:"Database Item #1: Buy 2, Get 1 Free.";s:17:"discount_quantity";d:1;s:21:"non_discount_quantity";d:1;s:9:"tax_value";d:0;s:5:"value";d:1;s:17:"shipping_discount";b:0;}}s:14:"active_summary";a:0:{}s:15:"reward_vouchers";a:0:{}s:4:"data";a:5:{s:21:"item_discount_savings";d:1;s:24:"summary_discount_savings";i:0;s:15:"reward_vouchers";i:0;s:23:"void_reward_point_items";a:0:{}s:18:"excluded_discounts";a:0:{}}}s:10:"surcharges";a:0:{}s:13:"configuration";a:28:{s:2:"id";b:1;s:19:"order_number_prefix";s:0:"";s:19:"order_number_suffix";s:0:"";s:22:"increment_order_number";b:1;s:13:"minimum_order";s:1:"0";s:17:"quantity_decimals";s:1:"0";s:33:"increment_duplicate_item_quantity";b:1;s:25:"quantity_limited_by_stock";b:1;s:21:"remove_no_stock_items";b:0;s:19:"auto_allocate_stock";b:1;s:26:"save_banned_shipping_items";b:0;s:11:"weight_type";s:4:"gram";s:15:"weight_decimals";s:1:"0";s:18:"display_tax_prices";b:1;s:13:"price_inc_tax";b:1;s:25:"multi_row_duplicate_items";b:0;s:21:"dynamic_reward_points";b:1;s:23:"reward_point_multiplier";s:7:"10.0000";s:25:"reward_voucher_multiplier";s:6:"0.0100";s:29:"reward_point_to_voucher_ratio";s:3:"250";s:25:"reward_point_days_pending";s:2:"14";s:23:"reward_point_days_valid";s:3:"365";s:25:"reward_voucher_days_valid";s:3:"365";s:15:"custom_status_1";b:0;s:15:"custom_status_2";b:0;s:15:"custom_status_3";b:0;s:12:"cart_data_id";i:20;s:12:"order_number";s:8:"00000001";}}}', '2015-11-23 14:59:23', 1),
(21, 2, 'a:3:{s:5:"items";a:4:{s:32:"c4ca4238a0b923820dcc509a6f75849b";a:15:{s:6:"row_id";s:32:"c4ca4238a0b923820dcc509a6f75849b";s:2:"id";s:1:"1";s:4:"name";s:15:"Cream of tomato";s:8:"quantity";d:5;s:5:"price";d:1;s:14:"stock_quantity";s:2:"93";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}s:32:"c81e728d9d4c2f636f067f89cc14862c";a:15:{s:6:"row_id";s:32:"c81e728d9d4c2f636f067f89cc14862c";s:2:"id";s:1:"2";s:4:"name";s:16:"Cream of chicken";s:8:"quantity";d:1;s:5:"price";d:1;s:14:"stock_quantity";s:2:"93";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}s:32:"eccbc87e4b5ce2fe28308fd9f2a7baf3";a:15:{s:6:"row_id";s:32:"eccbc87e4b5ce2fe28308fd9f2a7baf3";s:2:"id";s:1:"3";s:4:"name";s:17:"Cream of muchroom";s:8:"quantity";d:1;s:5:"price";d:1;s:14:"stock_quantity";s:3:"100";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}s:32:"a87ff679a2f3e71d9181a67b7542122c";a:15:{s:6:"row_id";s:32:"a87ff679a2f3e71d9181a67b7542122c";s:2:"id";s:1:"4";s:4:"name";s:6:"Oxtail";s:8:"quantity";d:1;s:5:"price";d:1;s:14:"stock_quantity";s:3:"100";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}}s:7:"summary";a:9:{s:10:"total_rows";i:4;s:11:"total_items";d:8;s:12:"total_weight";d:0;s:19:"total_reward_points";i:0;s:18:"item_summary_total";d:8;s:14:"shipping_total";d:3.9500000000000002;s:9:"tax_total";d:1.988;s:15:"surcharge_total";d:0;s:5:"total";d:11.949999999999999;}s:8:"settings";a:6:{s:8:"currency";a:7:{s:4:"name";s:3:"GBP";s:13:"exchange_rate";s:6:"1.0000";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";s:7:"default";a:5:{s:4:"name";s:3:"GBP";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";}}s:8:"shipping";a:7:{s:2:"id";s:1:"1";s:4:"name";s:20:"UK Standard Shipping";s:11:"description";s:8:"2-3 Days";s:5:"value";s:4:"3.95";s:8:"tax_rate";N;s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"0";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:11:{s:9:"surcharge";d:0;s:23:"separate_shipping_value";i:0;s:14:"separate_items";i:0;s:14:"separate_value";i:0;s:15:"separate_weight";i:0;s:10:"free_items";i:0;s:10:"free_value";i:0;s:11:"free_weight";i:0;s:21:"banned_shipping_items";a:0:{}s:23:"separate_shipping_items";a:0:{}s:19:"item_shipping_rates";a:0:{}}}s:3:"tax";a:5:{s:4:"name";s:3:"VAT";s:4:"rate";s:7:"20.0000";s:13:"internal_rate";s:7:"20.0000";s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"4";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:9:{s:14:"item_total_tax";d:1.3333999999999999;s:12:"shipping_tax";d:0;s:17:"item_discount_tax";d:0;s:20:"summary_discount_tax";d:0.66000000000000014;s:18:"reward_voucher_tax";i:0;s:13:"surcharge_tax";i:0;s:8:"cart_tax";d:1.3333999999999999;s:18:"cart_taxable_value";d:6.6664000000000003;s:22:"cart_non_taxable_value";d:0;}}s:9:"discounts";a:6:{s:5:"codes";a:1:{s:16:"FREE-UK-SHIPPING";a:3:{s:2:"id";s:1:"1";s:4:"code";s:16:"FREE-UK-SHIPPING";s:11:"description";s:52:"Discount Code "FREE-UK-SHIPPING" - Free UK shipping.";}}s:6:"manual";a:0:{}s:12:"active_items";a:0:{}s:14:"active_summary";a:1:{s:14:"shipping_total";a:5:{s:2:"id";s:1:"1";s:4:"code";s:16:"FREE-UK-SHIPPING";s:11:"description";s:52:"Discount Code "FREE-UK-SHIPPING" - Free UK shipping.";s:9:"tax_value";d:0.66000000000000014;s:5:"value";d:3.9500000000000002;}}s:15:"reward_vouchers";a:0:{}s:4:"data";a:5:{s:21:"item_discount_savings";d:0;s:24:"summary_discount_savings";d:3.9500000000000002;s:15:"reward_vouchers";i:0;s:23:"void_reward_point_items";a:0:{}s:18:"excluded_discounts";a:0:{}}}s:10:"surcharges";a:0:{}s:13:"configuration";a:28:{s:2:"id";b:1;s:19:"order_number_prefix";s:0:"";s:19:"order_number_suffix";s:0:"";s:22:"increment_order_number";b:1;s:13:"minimum_order";s:1:"0";s:17:"quantity_decimals";s:1:"0";s:33:"increment_duplicate_item_quantity";b:1;s:25:"quantity_limited_by_stock";b:1;s:21:"remove_no_stock_items";b:0;s:19:"auto_allocate_stock";b:1;s:26:"save_banned_shipping_items";b:0;s:11:"weight_type";s:4:"gram";s:15:"weight_decimals";s:1:"0";s:18:"display_tax_prices";b:1;s:13:"price_inc_tax";b:1;s:25:"multi_row_duplicate_items";b:0;s:21:"dynamic_reward_points";b:1;s:23:"reward_point_multiplier";s:7:"10.0000";s:25:"reward_voucher_multiplier";s:6:"0.0100";s:29:"reward_point_to_voucher_ratio";s:3:"250";s:25:"reward_point_days_pending";s:2:"14";s:23:"reward_point_days_valid";s:3:"365";s:25:"reward_voucher_days_valid";s:3:"365";s:15:"custom_status_1";b:0;s:15:"custom_status_2";b:0;s:15:"custom_status_3";b:0;s:12:"cart_data_id";i:21;s:12:"order_number";s:8:"00000002";}}}', '2015-12-16 10:58:41', 1),
(22, 2, 'a:3:{s:5:"items";a:2:{s:32:"c4ca4238a0b923820dcc509a6f75849b";a:15:{s:6:"row_id";s:32:"c4ca4238a0b923820dcc509a6f75849b";s:2:"id";s:1:"1";s:4:"name";s:15:"Cream of tomato";s:8:"quantity";d:20;s:5:"price";d:1;s:14:"stock_quantity";s:2:"88";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}s:32:"c81e728d9d4c2f636f067f89cc14862c";a:15:{s:6:"row_id";s:32:"c81e728d9d4c2f636f067f89cc14862c";s:2:"id";s:1:"2";s:4:"name";s:16:"Cream of chicken";s:8:"quantity";d:10;s:5:"price";d:1;s:14:"stock_quantity";s:2:"92";s:14:"internal_price";d:1;s:6:"weight";i:0;s:8:"tax_rate";b:0;s:13:"shipping_rate";b:0;s:17:"separate_shipping";b:0;s:13:"reward_points";b:0;s:9:"user_note";N;s:14:"status_message";a:0:{}s:3:"tax";d:0.16669999999999999;}}s:7:"summary";a:9:{s:10:"total_rows";i:2;s:11:"total_items";d:30;s:12:"total_weight";d:0;s:19:"total_reward_points";d:300;s:18:"item_summary_total";d:30;s:14:"shipping_total";d:3.9500000000000002;s:9:"tax_total";d:5.6580000000000004;s:15:"surcharge_total";d:0;s:5:"total";d:33.950000000000003;}s:8:"settings";a:6:{s:8:"currency";a:7:{s:4:"name";s:3:"GBP";s:13:"exchange_rate";s:6:"1.0000";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";s:7:"default";a:5:{s:4:"name";s:3:"GBP";s:6:"symbol";s:7:"&pound;";s:13:"symbol_suffix";b:0;s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";}}s:8:"shipping";a:7:{s:2:"id";s:1:"1";s:4:"name";s:20:"UK Standard Shipping";s:11:"description";s:8:"2-3 Days";s:5:"value";s:4:"3.95";s:8:"tax_rate";N;s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"0";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:11:{s:9:"surcharge";d:0;s:23:"separate_shipping_value";i:0;s:14:"separate_items";i:0;s:14:"separate_value";i:0;s:15:"separate_weight";i:0;s:10:"free_items";i:0;s:10:"free_value";i:0;s:11:"free_weight";i:0;s:21:"banned_shipping_items";a:0:{}s:23:"separate_shipping_items";a:0:{}s:19:"item_shipping_rates";a:0:{}}}s:3:"tax";a:5:{s:4:"name";s:3:"VAT";s:4:"rate";s:7:"20.0000";s:13:"internal_rate";s:7:"20.0000";s:8:"location";a:1:{i:0;a:5:{s:11:"location_id";s:1:"1";s:7:"zone_id";s:1:"4";s:7:"type_id";s:1:"1";s:9:"parent_id";s:1:"0";s:4:"name";s:19:"United Kingdom (EU)";}}s:4:"data";a:9:{s:14:"item_total_tax";d:5;s:12:"shipping_tax";d:0.65800000000000003;s:17:"item_discount_tax";d:0;s:20:"summary_discount_tax";i:0;s:18:"reward_voucher_tax";i:0;s:13:"surcharge_tax";i:0;s:8:"cart_tax";d:5.6580000000000004;s:18:"cart_taxable_value";d:28.288999999999998;s:22:"cart_non_taxable_value";d:0;}}s:9:"discounts";a:6:{s:5:"codes";a:0:{}s:6:"manual";a:0:{}s:12:"active_items";a:0:{}s:14:"active_summary";a:0:{}s:15:"reward_vouchers";a:0:{}s:4:"data";a:5:{s:21:"item_discount_savings";d:0;s:24:"summary_discount_savings";i:0;s:15:"reward_vouchers";i:0;s:23:"void_reward_point_items";a:0:{}s:18:"excluded_discounts";a:0:{}}}s:10:"surcharges";a:0:{}s:13:"configuration";a:28:{s:2:"id";b:1;s:19:"order_number_prefix";s:0:"";s:19:"order_number_suffix";s:0:"";s:22:"increment_order_number";b:1;s:13:"minimum_order";s:1:"0";s:17:"quantity_decimals";s:1:"0";s:33:"increment_duplicate_item_quantity";b:1;s:25:"quantity_limited_by_stock";b:1;s:21:"remove_no_stock_items";b:0;s:19:"auto_allocate_stock";b:1;s:26:"save_banned_shipping_items";b:0;s:11:"weight_type";s:4:"gram";s:15:"weight_decimals";s:1:"0";s:18:"display_tax_prices";b:1;s:13:"price_inc_tax";b:1;s:25:"multi_row_duplicate_items";b:0;s:21:"dynamic_reward_points";b:1;s:23:"reward_point_multiplier";s:7:"10.0000";s:25:"reward_voucher_multiplier";s:6:"0.0100";s:29:"reward_point_to_voucher_ratio";s:3:"250";s:25:"reward_point_days_pending";s:2:"14";s:23:"reward_point_days_valid";s:3:"365";s:25:"reward_voucher_days_valid";s:3:"365";s:15:"custom_status_1";b:0;s:15:"custom_status_2";b:0;s:15:"custom_status_3";b:0;s:12:"cart_data_id";i:22;s:12:"order_number";s:8:"00000003";}}}', '2015-12-17 10:59:20', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_currency`
--

DROP TABLE IF EXISTS `flexicart_currency`;
CREATE TABLE IF NOT EXISTS `flexicart_currency` (
  `curr_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `curr_name` varchar(50) NOT NULL DEFAULT '',
  `curr_exchange_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `curr_symbol` varchar(25) NOT NULL DEFAULT '',
  `curr_symbol_suffix` tinyint(1) NOT NULL DEFAULT '0',
  `curr_thousand_separator` varchar(10) NOT NULL DEFAULT '',
  `curr_decimal_separator` varchar(10) NOT NULL DEFAULT '',
  `curr_status` tinyint(1) NOT NULL DEFAULT '0',
  `curr_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`curr_id`),
  KEY `curr_id` (`curr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Truncar tablas antes de insertar `flexicart_currency`
--

TRUNCATE TABLE `flexicart_currency`;
--
-- Volcado de datos para la tabla `flexicart_currency`
--

INSERT INTO `flexicart_currency` (`curr_id`, `curr_name`, `curr_exchange_rate`, `curr_symbol`, `curr_symbol_suffix`, `curr_thousand_separator`, `curr_decimal_separator`, `curr_status`, `curr_default`) VALUES
(1, 'AUD', 2.0000, 'AU $', 0, ',', '.', 1, 0),
(2, 'EUR', 1.1500, '&euro;', 1, '.', ',', 1, 0),
(3, 'GBP', 1.0000, '&pound;', 0, ',', '.', 1, 1),
(4, 'USD', 1.6000, 'US $', 0, ',', '.', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_customers`
--

DROP TABLE IF EXISTS `flexicart_customers`;
CREATE TABLE IF NOT EXISTS `flexicart_customers` (
  `user_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_group_fk` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  KEY `user_group_fk` (`user_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: This is a custom demo table for users.' AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `flexicart_customers`
--

TRUNCATE TABLE `flexicart_customers`;
--
-- Volcado de datos para la tabla `flexicart_customers`
--

INSERT INTO `flexicart_customers` (`user_id`, `user_name`, `user_group_fk`) VALUES
(1, 'Customer #1', 1),
(2, 'Customer #2', 1),
(3, 'Customer #3', 2),
(4, 'Customer #4', 1),
(5, 'Customer #5', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discounts`
--

DROP TABLE IF EXISTS `flexicart_discounts`;
CREATE TABLE IF NOT EXISTS `flexicart_discounts` (
  `disc_id` int(11) NOT NULL AUTO_INCREMENT,
  `disc_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_tax_method_fk` tinyint(1) NOT NULL DEFAULT '0',
  `disc_user_acc_fk` int(11) NOT NULL DEFAULT '0',
  `disc_item_fk` int(11) NOT NULL DEFAULT '0' COMMENT 'Item / Product Id',
  `disc_group_fk` int(11) NOT NULL DEFAULT '0',
  `disc_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_code` varchar(50) NOT NULL DEFAULT '' COMMENT 'Discount Code',
  `disc_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name shown in cart when active',
  `disc_quantity_required` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Quantity required for offer',
  `disc_quantity_discounted` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Quantity affected by offer',
  `disc_value_required` double(8,2) NOT NULL DEFAULT '0.00',
  `disc_value_discounted` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '% discount, flat fee discount, new set price - specified via calculation_fk',
  `disc_recursive` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Discount is repeatable multiple times on one item',
  `disc_non_combinable_discount` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cannot be applied if any other discount is applied',
  `disc_void_reward_points` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Voids any current reward points',
  `disc_force_ship_discount` tinyint(1) NOT NULL DEFAULT '0',
  `disc_custom_status_1` varchar(50) NOT NULL DEFAULT '',
  `disc_custom_status_2` varchar(50) NOT NULL DEFAULT '',
  `disc_custom_status_3` varchar(50) NOT NULL DEFAULT '',
  `disc_usage_limit` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Number of offers available',
  `disc_valid_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disc_expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disc_status` tinyint(1) NOT NULL DEFAULT '0',
  `disc_order_by` smallint(1) NOT NULL DEFAULT '100' COMMENT 'Default value of 100 to ensure non set ''order by'' values of zero are not before 1,2,3 etc.',
  PRIMARY KEY (`disc_id`),
  UNIQUE KEY `disc_id` (`disc_id`) USING BTREE,
  KEY `disc_item_fk` (`disc_item_fk`),
  KEY `disc_location_fk` (`disc_location_fk`),
  KEY `disc_zone_fk` (`disc_zone_fk`),
  KEY `disc_method_fk` (`disc_method_fk`) USING BTREE,
  KEY `disc_type_fk` (`disc_type_fk`),
  KEY `disc_group_fk` (`disc_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Truncar tablas antes de insertar `flexicart_discounts`
--

TRUNCATE TABLE `flexicart_discounts`;
--
-- Volcado de datos para la tabla `flexicart_discounts`
--

INSERT INTO `flexicart_discounts` (`disc_id`, `disc_type_fk`, `disc_method_fk`, `disc_tax_method_fk`, `disc_user_acc_fk`, `disc_item_fk`, `disc_group_fk`, `disc_location_fk`, `disc_zone_fk`, `disc_code`, `disc_description`, `disc_quantity_required`, `disc_quantity_discounted`, `disc_value_required`, `disc_value_discounted`, `disc_recursive`, `disc_non_combinable_discount`, `disc_void_reward_points`, `disc_force_ship_discount`, `disc_custom_status_1`, `disc_custom_status_2`, `disc_custom_status_3`, `disc_usage_limit`, `disc_valid_date`, `disc_expire_date`, `disc_status`, `disc_order_by`) VALUES
(1, 1, 11, 1, 0, 0, 0, 1, 0, 'FREE-UK-SHIPPING', 'Discount Code "FREE-UK-SHIPPING" - Free UK shipping.', 0, 0, 0.00, 0.00, 0, 0, 1, 1, '', '', '', 9998, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(2, 2, 12, 1, 0, 0, 0, 0, 0, '10-PERCENT', 'Discount Code "10-PERCENT" - 10% off grand total.', 0, 0, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(3, 2, 13, 1, 0, 0, 0, 0, 0, '10-FIXED-RATE', 'Discount Code "10-FIXED-RATE" - &pound;10 off grand total.', 0, 0, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(4, 2, 13, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Spend over &pound;1,000, get &pound;100 off.', 1, 1, 1000.00, 100.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(5, 2, 11, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Spend over &pound;500, get free worldwide shipping.', 0, 0, 500.00, 0.00, 0, 0, 0, 1, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(6, 2, 12, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Logged in users get 5% off total.', 0, 0, 0.00, 5.00, 0, 0, 0, 0, '1', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(7, 1, 1, 1, 0, 301, 0, 0, 0, '', 'Discount Item #301, 10% off original price (&pound;24.99).', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9997, '2015-11-06 11:56:09', '2016-01-06 11:56:09', 1, 1),
(8, 1, 2, 1, 0, 302, 0, 0, 0, '', 'Discount Item #302, Fixed price of &pound;5.00 off original price of &pound;12.50.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(9, 1, 3, 1, 0, 303, 0, 0, 0, '', 'Discount Item #303, New price of &pound;15.00, item was &pound;25.00.', 1, 1, 0.00, 15.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(10, 1, 3, 1, 0, 304, 0, 0, 0, '', 'Discount Item #304, Buy 2, get 1 free.', 3, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(11, 1, 1, 1, 0, 305, 0, 0, 0, '', 'Discount Item #305, Buy 1, get 1 @ 50% off.', 2, 1, 0.00, 50.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(12, 1, 2, 1, 0, 306, 0, 0, 0, '', 'Discount Item #306, Buy 2 @ &pound;15.00, get 1 with &pound;5.00 off.', 3, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(13, 1, 3, 1, 0, 307, 0, 0, 0, '', 'Discount Item #307, Buy 5 @ &pound;10.00, get 2 for &pound;7.00.', 7, 2, 0.00, 7.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(14, 1, 6, 1, 0, 308, 0, 1, 0, '', 'Discount Item #308, Buy 3, get free UK shipping on these items (Other items still charged).', 3, 3, 0.00, 0.00, 1, 0, 0, 1, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(15, 1, 1, 1, 0, 309, 0, 0, 0, '', 'Discount Item #309, Spend over &pound;75.00 on this item, get 10% off this items total.', 1, 1, 75.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(16, 1, 2, 1, 0, 310, 0, 0, 0, '', 'Discount Item #310, Spend over &pound;100.00 on this item, get &pound;10.00 off this items total.', 1, 1, 100.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(17, 1, 3, 1, 0, 0, 1, 0, 0, '', 'Discount Group: Discount Items #311, #312 and #313 - buy 3, get cheapest item free.', 3, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(18, 1, 1, 1, 0, 314, 0, 0, 0, '', 'Discount Item #314, 10% off original price - but for logged in users only.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '1', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(19, 1, 1, 1, 0, 315, 0, 0, 0, '', 'Discount Item #315, 10% off original price - but removes the items reward points (Normally 200 points).', 1, 1, 0.00, 10.00, 1, 0, 1, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(20, 1, 1, 1, 0, 316, 0, 0, 0, '', 'Discount Item #316, 10% off original price - but applies to first item only (Non recursive quantity discount).', 1, 1, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(21, 1, 1, 1, 0, 317, 0, 1, 0, '', 'Discount Item #317, 10% off original price - but applies to orders being shipped to the UK only.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(22, 1, 1, 1, 0, 318, 0, 0, 0, '', 'Discount Item #318, 10% off original price - but cannot be applied if other discounts exist.', 1, 1, 0.00, 10.00, 1, 1, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(23, 1, 1, 1, 0, 401, 0, 0, 0, '', 'Discount Tax #401, get 10% off original price (&pound;10.00) - Method #1.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(24, 1, 1, 2, 0, 402, 0, 0, 0, '', 'Discount Tax #402, get 10% off original price (&pound;10.00) - Method #2.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(25, 1, 1, 3, 0, 403, 0, 0, 0, '', 'Discount Tax #403, get 10% off original price (&pound;10.00) - Method #3.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(26, 1, 2, 1, 0, 404, 0, 0, 0, '', 'Discount Tax #404, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #1.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(27, 1, 2, 2, 0, 405, 0, 0, 0, '', 'Discount Tax #405, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #2.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(28, 1, 2, 3, 0, 406, 0, 0, 0, '', 'Discount Tax #406, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #3.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(29, 1, 3, 1, 0, 407, 0, 0, 0, '', 'Discount Tax #407, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #1.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(30, 1, 3, 2, 0, 408, 0, 0, 0, '', 'Discount Tax #408, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #2.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(31, 1, 3, 3, 0, 409, 0, 0, 0, '', 'Discount Tax #409, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #3.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(32, 1, 3, 0, 0, 1, 0, 0, 0, '', 'Database Item #1: Buy 2, Get 1 Free.', 2, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 8, '2015-11-04 11:56:10', '2015-12-04 11:56:10', 1, 1),
(33, 1, 1, 0, 0, 3, 0, 0, 0, '', 'Database Item #3: 10% off original price.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9, '2015-11-04 11:56:10', '2015-11-30 11:56:10', 1, 1),
(34, 1, 2, 0, 0, 5, 0, 0, 0, '', 'Database Item #5: Get &pound;5.00 off original price.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9, '2015-11-04 11:56:10', '2015-11-27 11:56:10', 1, 1),
(35, 3, 14, 0, 1, 0, 0, 0, 0, '2AC2AE9AEF923F4', 'Reward Voucher: 2AC2AE9AEF923F4', 0, 0, 0.00, 5.00, 0, 0, 1, 0, '', '', '', 1, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 100),
(36, 3, 14, 0, 4, 0, 0, 0, 0, '088F148041B66A9', 'Reward Voucher: 088F148041B66A9', 0, 0, 0.00, 10.00, 0, 0, 1, 0, '', '', '', 0, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_calculation`
--

DROP TABLE IF EXISTS `flexicart_discount_calculation`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_calculation` (
  `disc_calculation_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_calculation` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_calculation_id`),
  UNIQUE KEY `disc_calculation_id` (`disc_calculation_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_calculation`
--

TRUNCATE TABLE `flexicart_discount_calculation`;
--
-- Volcado de datos para la tabla `flexicart_discount_calculation`
--

INSERT INTO `flexicart_discount_calculation` (`disc_calculation_id`, `disc_calculation`) VALUES
(1, 'Percentage Based'),
(2, 'Flat Fee'),
(3, 'New Value');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_columns`
--

DROP TABLE IF EXISTS `flexicart_discount_columns`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_columns` (
  `disc_column_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_column` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_column_id`),
  UNIQUE KEY `disc_column_id` (`disc_column_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=7 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_columns`
--

TRUNCATE TABLE `flexicart_discount_columns`;
--
-- Volcado de datos para la tabla `flexicart_discount_columns`
--

INSERT INTO `flexicart_discount_columns` (`disc_column_id`, `disc_column`) VALUES
(1, 'Item Price'),
(2, 'Item Shipping'),
(3, 'Summary Item Total'),
(4, 'Summary Shipping Total'),
(5, 'Summary Total'),
(6, 'Summary Total (Voucher)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_groups`
--

DROP TABLE IF EXISTS `flexicart_discount_groups`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_groups` (
  `disc_group_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_group` varchar(255) NOT NULL DEFAULT '',
  `disc_group_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`disc_group_id`),
  UNIQUE KEY `disc_group_id` (`disc_group_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_groups`
--

TRUNCATE TABLE `flexicart_discount_groups`;
--
-- Volcado de datos para la tabla `flexicart_discount_groups`
--

INSERT INTO `flexicart_discount_groups` (`disc_group_id`, `disc_group`, `disc_group_status`) VALUES
(1, 'Demo Group : Items #311, #312 and #313', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_group_items`
--

DROP TABLE IF EXISTS `flexicart_discount_group_items`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_group_items` (
  `disc_group_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `disc_group_item_group_fk` int(11) NOT NULL DEFAULT '0',
  `disc_group_item_item_fk` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`disc_group_item_id`),
  UNIQUE KEY `disc_group_item_id` (`disc_group_item_id`) USING BTREE,
  KEY `disc_group_item_group_fk` (`disc_group_item_group_fk`) USING BTREE,
  KEY `disc_group_item_item_fk` (`disc_group_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_group_items`
--

TRUNCATE TABLE `flexicart_discount_group_items`;
--
-- Volcado de datos para la tabla `flexicart_discount_group_items`
--

INSERT INTO `flexicart_discount_group_items` (`disc_group_item_id`, `disc_group_item_group_fk`, `disc_group_item_item_fk`) VALUES
(1, 1, 311),
(2, 1, 312),
(3, 1, 313);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_methods`
--

DROP TABLE IF EXISTS `flexicart_discount_methods`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_methods` (
  `disc_method_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_method_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_column_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_calculation_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_method_id`),
  UNIQUE KEY `disc_method_id` (`disc_method_id`) USING BTREE,
  KEY `disc_method_column_fk` (`disc_method_column_fk`) USING BTREE,
  KEY `disc_method_calculation_fk` (`disc_method_calculation_fk`) USING BTREE,
  KEY `disc_method_type_fk` (`disc_method_type_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=15 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_methods`
--

TRUNCATE TABLE `flexicart_discount_methods`;
--
-- Volcado de datos para la tabla `flexicart_discount_methods`
--

INSERT INTO `flexicart_discount_methods` (`disc_method_id`, `disc_method_type_fk`, `disc_method_column_fk`, `disc_method_calculation_fk`, `disc_method`) VALUES
(1, 1, 1, 1, 'Item Price - Percentage Based'),
(2, 1, 1, 2, 'Item Price - Flat Fee'),
(3, 1, 1, 3, 'Item Price - New Value'),
(4, 1, 2, 1, 'Item Shipping - Percentage Based'),
(5, 1, 2, 2, 'Item Shipping - Flat Fee'),
(6, 1, 2, 3, 'Item Shipping - New Value'),
(7, 2, 3, 1, 'Summary Item Total - Percentage Based'),
(8, 2, 3, 2, 'Summary Item Total - Flat Fee'),
(9, 2, 4, 1, 'Summary Shipping Total - Percentage Based'),
(10, 2, 4, 2, 'Summary Shipping Total - Flat Fee'),
(11, 2, 4, 3, 'Summary Shipping Total - New Value'),
(12, 2, 5, 1, 'Summary Total - Percentage Based'),
(13, 2, 5, 2, 'Summary Total - Flat Fee'),
(14, 3, 6, 2, 'Summary Total - Flat Fee (Voucher)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_tax_methods`
--

DROP TABLE IF EXISTS `flexicart_discount_tax_methods`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_tax_methods` (
  `disc_tax_method_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_tax_method` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_tax_method_id`),
  UNIQUE KEY `disc_tax_method_id` (`disc_tax_method_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_tax_methods`
--

TRUNCATE TABLE `flexicart_discount_tax_methods`;
--
-- Volcado de datos para la tabla `flexicart_discount_tax_methods`
--

INSERT INTO `flexicart_discount_tax_methods` (`disc_tax_method_id`, `disc_tax_method`) VALUES
(1, 'Apply Tax Before Discount '),
(2, 'Apply Discount Before Tax'),
(3, 'Apply Discount Before Tax, Add Original Tax');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_discount_types`
--

DROP TABLE IF EXISTS `flexicart_discount_types`;
CREATE TABLE IF NOT EXISTS `flexicart_discount_types` (
  `disc_type_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_type_id`),
  UNIQUE KEY `disc_type_id` (`disc_type_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_discount_types`
--

TRUNCATE TABLE `flexicart_discount_types`;
--
-- Volcado de datos para la tabla `flexicart_discount_types`
--

INSERT INTO `flexicart_discount_types` (`disc_type_id`, `disc_type`) VALUES
(1, 'Item Discount'),
(2, 'Summary Discount'),
(3, 'Reward Voucher');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_items`
--

DROP TABLE IF EXISTS `flexicart_items`;
CREATE TABLE IF NOT EXISTS `flexicart_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_category_id` smallint(6) DEFAULT NULL,
  `item_subcategory_id` smallint(6) DEFAULT NULL,
  `item_name` varchar(50) NOT NULL DEFAULT '',
  `item_short_description` varchar(255) NOT NULL,
  `item_full_description` text NOT NULL,
  `item_price` double(6,2) NOT NULL DEFAULT '0.00',
  `item_weight` double(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_id` (`item_id`),
  KEY `item_cat_fk` (`item_category_id`) USING BTREE,
  KEY `item_subcategory_id` (`item_subcategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: This is a custom demo table for items.' AUTO_INCREMENT=13 ;

--
-- Truncar tablas antes de insertar `flexicart_items`
--

TRUNCATE TABLE `flexicart_items`;
--
-- Volcado de datos para la tabla `flexicart_items`
--

INSERT INTO `flexicart_items` (`item_id`, `item_category_id`, `item_subcategory_id`, `item_name`, `item_short_description`, `item_full_description`, `item_price`, `item_weight`) VALUES
(1, 1, 1, 'Cream of tomato', '', '', 1.00, 0.32),
(2, 1, 1, 'Cream of chicken', '', '', 1.00, 0.32),
(3, 1, 1, 'Cream of muchroom', '', '', 1.00, 0.32),
(4, 1, 1, 'Oxtail', '', '', 1.00, 0.32),
(5, 1, 2, 'Tomato with chilli', '', '', 1.00, 0.32),
(6, 1, 2, 'Chicken with thai spices', '', '', 1.00, 0.32),
(7, 1, 2, 'Beef and paprika', '', '', 1.00, 0.32),
(8, 1, 4, 'Beef and veg', '', '', 1.00, 0.32),
(9, 1, 4, 'Chicken and veg', '', '', 1.00, 0.32),
(10, 1, 3, 'Tomato', '', '', 1.00, 0.32),
(11, 1, 3, 'Chicken', '', '', 1.00, 0.32),
(12, 1, 3, 'Chicken noodle', '', '', 1.00, 0.32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_item_categories`
--

DROP TABLE IF EXISTS `flexicart_item_categories`;
CREATE TABLE IF NOT EXISTS `flexicart_item_categories` (
  `item_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_category_name` varchar(255) NOT NULL,
  `item_catgory_description` varchar(255) NOT NULL,
  `item_category_image` varchar(255) NOT NULL,
  PRIMARY KEY (`item_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `flexicart_item_categories`
--

TRUNCATE TABLE `flexicart_item_categories`;
--
-- Volcado de datos para la tabla `flexicart_item_categories`
--

INSERT INTO `flexicart_item_categories` (`item_category_id`, `item_category_name`, `item_catgory_description`, `item_category_image`) VALUES
(1, 'Soup', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_item_stock`
--

DROP TABLE IF EXISTS `flexicart_item_stock`;
CREATE TABLE IF NOT EXISTS `flexicart_item_stock` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_item_fk` int(11) NOT NULL DEFAULT '0',
  `stock_quantity` smallint(5) NOT NULL DEFAULT '0',
  `stock_auto_allocate_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `stock_id` (`stock_id`) USING BTREE,
  KEY `stock_item_fk` (`stock_item_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Truncar tablas antes de insertar `flexicart_item_stock`
--

TRUNCATE TABLE `flexicart_item_stock`;
--
-- Volcado de datos para la tabla `flexicart_item_stock`
--

INSERT INTO `flexicart_item_stock` (`stock_id`, `stock_item_fk`, `stock_quantity`, `stock_auto_allocate_status`) VALUES
(1, 112, 20, 1),
(2, 113, 0, 1),
(3, 1, 68, 1),
(4, 2, 82, 1),
(5, 3, 99, 1),
(6, 4, 99, 1),
(7, 5, 99, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_item_subcategories`
--

DROP TABLE IF EXISTS `flexicart_item_subcategories`;
CREATE TABLE IF NOT EXISTS `flexicart_item_subcategories` (
  `item_subcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_subcategory_name` varchar(255) NOT NULL,
  `item_subcategory_description` varchar(255) NOT NULL,
  `item_subcategory_image` varchar(255) NOT NULL,
  `item_category_id` int(11) NOT NULL,
  PRIMARY KEY (`item_subcategory_id`),
  KEY `item_category_id` (`item_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Truncar tablas antes de insertar `flexicart_item_subcategories`
--

TRUNCATE TABLE `flexicart_item_subcategories`;
--
-- Volcado de datos para la tabla `flexicart_item_subcategories`
--

INSERT INTO `flexicart_item_subcategories` (`item_subcategory_id`, `item_subcategory_name`, `item_subcategory_description`, `item_subcategory_image`, `item_category_id`) VALUES
(1, 'Classics', '', '', 1),
(2, 'Black Label', '', '', 1),
(3, 'Cup Soups', '', '', 1),
(4, 'Farmers Market', '', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_locations`
--

DROP TABLE IF EXISTS `flexicart_locations`;
CREATE TABLE IF NOT EXISTS `flexicart_locations` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_parent_fk` int(11) NOT NULL DEFAULT '0',
  `loc_ship_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_tax_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_name` varchar(50) NOT NULL DEFAULT '',
  `loc_status` tinyint(1) NOT NULL DEFAULT '0',
  `loc_ship_default` tinyint(1) NOT NULL DEFAULT '0',
  `loc_tax_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loc_id`),
  UNIQUE KEY `loc_id` (`loc_id`) USING BTREE,
  KEY `loc_type_fk` (`loc_type_fk`) USING BTREE,
  KEY `loc_tax_zone_fk` (`loc_tax_zone_fk`),
  KEY `loc_ship_zone_fk` (`loc_ship_zone_fk`),
  KEY `loc_parent_fk` (`loc_parent_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Truncar tablas antes de insertar `flexicart_locations`
--

TRUNCATE TABLE `flexicart_locations`;
--
-- Volcado de datos para la tabla `flexicart_locations`
--

INSERT INTO `flexicart_locations` (`loc_id`, `loc_type_fk`, `loc_parent_fk`, `loc_ship_zone_fk`, `loc_tax_zone_fk`, `loc_name`, `loc_status`, `loc_ship_default`, `loc_tax_default`) VALUES
(1, 1, 0, 0, 4, 'United Kingdom (EU)', 1, 1, 1),
(2, 1, 0, 1, 4, 'France (EU)', 1, 0, 0),
(3, 1, 0, 1, 4, 'Germany (EU)', 1, 0, 0),
(4, 1, 0, 2, 4, 'Portugal (EU)', 1, 0, 0),
(5, 1, 0, 2, 4, 'Spain (EU)', 1, 0, 0),
(6, 1, 0, 3, 5, 'Norway (Non EU)', 1, 0, 0),
(7, 1, 0, 3, 5, 'Switzerland (Non EU)', 1, 0, 0),
(8, 1, 0, 0, 0, 'Australia', 1, 0, 0),
(9, 1, 0, 0, 0, 'Canada', 1, 0, 0),
(10, 1, 0, 0, 0, 'United States', 1, 0, 0),
(11, 2, 8, 0, 0, 'NSW', 1, 0, 0),
(12, 2, 8, 0, 0, 'Queensland', 1, 0, 0),
(13, 2, 8, 0, 0, 'Victoria', 1, 0, 0),
(14, 2, 10, 0, 0, 'California', 1, 0, 0),
(15, 2, 10, 0, 0, 'Florida', 1, 0, 0),
(16, 2, 10, 0, 0, 'New York', 1, 0, 0),
(17, 2, 10, 0, 0, 'Pennsylvania', 1, 0, 0),
(18, 3, 16, 0, 0, '10101', 1, 0, 0),
(19, 3, 16, 0, 0, '10102', 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_location_type`
--

DROP TABLE IF EXISTS `flexicart_location_type`;
CREATE TABLE IF NOT EXISTS `flexicart_location_type` (
  `loc_type_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `loc_type_parent_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_type_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`loc_type_id`),
  UNIQUE KEY `loc_type_id` (`loc_type_id`),
  KEY `loc_type_parent_fk` (`loc_type_parent_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_location_type`
--

TRUNCATE TABLE `flexicart_location_type`;
--
-- Volcado de datos para la tabla `flexicart_location_type`
--

INSERT INTO `flexicart_location_type` (`loc_type_id`, `loc_type_parent_fk`, `loc_type_name`) VALUES
(1, 0, 'Country'),
(2, 1, 'State'),
(3, 2, 'Post / Zip Code');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_location_zones`
--

DROP TABLE IF EXISTS `flexicart_location_zones`;
CREATE TABLE IF NOT EXISTS `flexicart_location_zones` (
  `lzone_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `lzone_name` varchar(50) NOT NULL DEFAULT '',
  `lzone_description` longtext NOT NULL,
  `lzone_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lzone_id`),
  UNIQUE KEY `lzone_id` (`lzone_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `flexicart_location_zones`
--

TRUNCATE TABLE `flexicart_location_zones`;
--
-- Volcado de datos para la tabla `flexicart_location_zones`
--

INSERT INTO `flexicart_location_zones` (`lzone_id`, `lzone_name`, `lzone_description`, `lzone_status`) VALUES
(1, 'Shipping Europe Zone 1', 'Example Zone 1 includes France and Germany', 1),
(2, 'Shipping Europe Zone 2', 'Example Zone 2 includes Portugal and Spain', 1),
(3, 'Shipping Europe Zone 3', 'Example Zone 3 includes Norway and Switzerland', 1),
(4, 'Tax EU Zone', 'Example Tax Zone for EU countries', 1),
(5, 'Tax Non EU Zone', 'Example Tax Zone for Non EU European countries', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_order_customers`
--

DROP TABLE IF EXISTS `flexicart_order_customers`;
CREATE TABLE IF NOT EXISTS `flexicart_order_customers` (
  `user_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_group_fk` smallint(5) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  KEY `user_group_fk` (`user_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='lets contacts login to view their order details and status' AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `flexicart_order_customers`
--

TRUNCATE TABLE `flexicart_order_customers`;
--
-- Volcado de datos para la tabla `flexicart_order_customers`
--

INSERT INTO `flexicart_order_customers` (`user_id`, `user_name`, `user_group_fk`, `password`, `email`) VALUES
(1, 'Customer #1', 1, '', ''),
(2, 'Customer #2', 1, '', ''),
(3, 'Customer #3', 2, '', ''),
(4, 'Customer #4', 1, '', ''),
(5, 'Customer #5', 2, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_order_details`
--

DROP TABLE IF EXISTS `flexicart_order_details`;
CREATE TABLE IF NOT EXISTS `flexicart_order_details` (
  `ord_det_id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_det_order_number_fk` varchar(25) NOT NULL DEFAULT '',
  `ord_det_cart_row_id` varchar(32) NOT NULL DEFAULT '',
  `ord_det_item_fk` int(11) NOT NULL DEFAULT '0',
  `ord_det_item_name` varchar(255) NOT NULL DEFAULT '',
  `ord_det_item_option` varchar(255) NOT NULL DEFAULT '',
  `ord_det_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_non_discount_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_stock_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_price` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_price_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_price` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_price_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_description` varchar(255) NOT NULL DEFAULT '',
  `ord_det_tax_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `ord_det_tax` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_tax_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_shipping_rate` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_weight` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_weight_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_reward_points` int(10) NOT NULL DEFAULT '0',
  `ord_det_reward_points_total` int(10) NOT NULL DEFAULT '0',
  `ord_det_status_message` varchar(255) NOT NULL DEFAULT '',
  `ord_det_quantity_shipped` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_quantity_cancelled` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_shipped_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ord_det_demo_user_note` varchar(255) NOT NULL DEFAULT '',
  `ord_det_demo_sku` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ord_det_id`),
  UNIQUE KEY `ord_det_id` (`ord_det_id`) USING BTREE,
  KEY `ord_det_order_number_fk` (`ord_det_order_number_fk`) USING BTREE,
  KEY `ord_det_item_fk` (`ord_det_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Truncar tablas antes de insertar `flexicart_order_details`
--

TRUNCATE TABLE `flexicart_order_details`;
--
-- Volcado de datos para la tabla `flexicart_order_details`
--

INSERT INTO `flexicart_order_details` (`ord_det_id`, `ord_det_order_number_fk`, `ord_det_cart_row_id`, `ord_det_item_fk`, `ord_det_item_name`, `ord_det_item_option`, `ord_det_quantity`, `ord_det_non_discount_quantity`, `ord_det_discount_quantity`, `ord_det_stock_quantity`, `ord_det_price`, `ord_det_price_total`, `ord_det_discount_price`, `ord_det_discount_price_total`, `ord_det_discount_description`, `ord_det_tax_rate`, `ord_det_tax`, `ord_det_tax_total`, `ord_det_shipping_rate`, `ord_det_weight`, `ord_det_weight_total`, `ord_det_reward_points`, `ord_det_reward_points_total`, `ord_det_status_message`, `ord_det_quantity_shipped`, `ord_det_quantity_cancelled`, `ord_det_shipped_date`, `ord_det_demo_user_note`, `ord_det_demo_sku`) VALUES
(1, '00000001', 'c4ca4238a0b923820dcc509a6f75849b', 1, 'Cream of tomato', '', 2.00, 1.00, 1.00, 95.00, 1.00, 2.00, 0.00, 1.00, 'Database Item #1: Buy 2, Get 1 Free.', 20.0000, 0.17, 0.34, 0.00, 0.00, 0.00, 10, 20, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(2, '00000001', 'c81e728d9d4c2f636f067f89cc14862c', 2, 'Cream of chicken', '', 5.00, 5.00, 0.00, 98.00, 1.00, 5.00, 1.00, 5.00, '', 20.0000, 0.17, 0.85, 0.00, 0.00, 0.00, 10, 50, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(3, '00000002', 'c4ca4238a0b923820dcc509a6f75849b', 1, 'Cream of tomato', '', 5.00, 5.00, 0.00, 93.00, 1.00, 5.00, 1.00, 5.00, '', 20.0000, 0.17, 0.85, 0.00, 0.00, 0.00, 0, 0, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(4, '00000002', 'c81e728d9d4c2f636f067f89cc14862c', 2, 'Cream of chicken', '', 1.00, 1.00, 0.00, 93.00, 1.00, 1.00, 1.00, 1.00, '', 20.0000, 0.17, 0.17, 0.00, 0.00, 0.00, 0, 0, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(5, '00000002', 'eccbc87e4b5ce2fe28308fd9f2a7baf3', 3, 'Cream of muchroom', '', 1.00, 1.00, 0.00, 100.00, 1.00, 1.00, 1.00, 1.00, '', 20.0000, 0.17, 0.17, 0.00, 0.00, 0.00, 0, 0, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(6, '00000002', 'a87ff679a2f3e71d9181a67b7542122c', 4, 'Oxtail', '', 1.00, 1.00, 0.00, 100.00, 1.00, 1.00, 1.00, 1.00, '', 20.0000, 0.17, 0.17, 0.00, 0.00, 0.00, 0, 0, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(7, '00000003', 'c4ca4238a0b923820dcc509a6f75849b', 1, 'Cream of tomato', '', 20.00, 20.00, 0.00, 88.00, 1.00, 20.00, 1.00, 20.00, '', 20.0000, 0.17, 3.40, 0.00, 0.00, 0.00, 10, 200, '', 0.00, 0.00, '0000-00-00 00:00:00', '', ''),
(8, '00000003', 'c81e728d9d4c2f636f067f89cc14862c', 2, 'Cream of chicken', '', 10.00, 10.00, 0.00, 92.00, 1.00, 10.00, 1.00, 10.00, '', 20.0000, 0.17, 1.70, 0.00, 0.00, 0.00, 10, 100, '', 0.00, 0.00, '0000-00-00 00:00:00', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_order_status`
--

DROP TABLE IF EXISTS `flexicart_order_status`;
CREATE TABLE IF NOT EXISTS `flexicart_order_status` (
  `ord_status_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ord_status_description` varchar(50) NOT NULL DEFAULT '',
  `ord_status_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `ord_status_save_default` tinyint(1) NOT NULL DEFAULT '0',
  `ord_status_resave_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ord_status_id`),
  KEY `ord_status_id` (`ord_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `flexicart_order_status`
--

TRUNCATE TABLE `flexicart_order_status`;
--
-- Volcado de datos para la tabla `flexicart_order_status`
--

INSERT INTO `flexicart_order_status` (`ord_status_id`, `ord_status_description`, `ord_status_cancelled`, `ord_status_save_default`, `ord_status_resave_default`) VALUES
(1, 'Awaiting Payment', 0, 1, 0),
(2, 'New Order', 0, 0, 1),
(3, 'Processing Order', 0, 0, 0),
(4, 'Order Complete', 0, 0, 0),
(5, 'Order Cancelled', 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_order_summary`
--

DROP TABLE IF EXISTS `flexicart_order_summary`;
CREATE TABLE IF NOT EXISTS `flexicart_order_summary` (
  `ord_order_number` varchar(25) NOT NULL DEFAULT '',
  `ord_cart_data_fk` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `urn` int(11) DEFAULT NULL,
  `ord_item_summary_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_item_summary_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_shipping` varchar(100) NOT NULL DEFAULT '',
  `ord_shipping_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_item_shipping_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_summary_discount_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_summary_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_surcharge_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_surcharge_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_reward_voucher_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_reward_voucher_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_tax_rate` varchar(25) NOT NULL DEFAULT '',
  `ord_tax_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_sub_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_rows` int(10) NOT NULL DEFAULT '0',
  `ord_total_items` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_weight` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_reward_points` int(10) NOT NULL DEFAULT '0',
  `ord_currency` varchar(25) NOT NULL DEFAULT '',
  `ord_exchange_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `ord_status` tinyint(1) NOT NULL DEFAULT '0',
  `ord_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ord_demo_bill_name` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_company` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_address_01` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_address_02` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_city` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_bill_state` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_bill_post_code` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_bill_country` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_name` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_company` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_address_01` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_address_02` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_city` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_state` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_post_code` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_ship_country` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_email` varchar(255) NOT NULL DEFAULT '',
  `ord_demo_phone` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_comments` longtext NOT NULL,
  PRIMARY KEY (`ord_order_number`),
  UNIQUE KEY `ord_order_number` (`ord_order_number`) USING BTREE,
  KEY `ord_cart_data_fk` (`ord_cart_data_fk`) USING BTREE,
  KEY `ord_user_fk` (`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `flexicart_order_summary`
--

TRUNCATE TABLE `flexicart_order_summary`;
--
-- Volcado de datos para la tabla `flexicart_order_summary`
--

INSERT INTO `flexicart_order_summary` (`ord_order_number`, `ord_cart_data_fk`, `user_id`, `urn`, `ord_item_summary_total`, `ord_item_summary_savings_total`, `ord_shipping`, `ord_shipping_total`, `ord_item_shipping_total`, `ord_summary_discount_desc`, `ord_summary_savings_total`, `ord_savings_total`, `ord_surcharge_desc`, `ord_surcharge_total`, `ord_reward_voucher_desc`, `ord_reward_voucher_total`, `ord_tax_rate`, `ord_tax_total`, `ord_sub_total`, `ord_total`, `ord_total_rows`, `ord_total_items`, `ord_total_weight`, `ord_total_reward_points`, `ord_currency`, `ord_exchange_rate`, `ord_status`, `ord_date`, `ord_demo_bill_name`, `ord_demo_bill_company`, `ord_demo_bill_address_01`, `ord_demo_bill_address_02`, `ord_demo_bill_city`, `ord_demo_bill_state`, `ord_demo_bill_post_code`, `ord_demo_bill_country`, `ord_demo_ship_name`, `ord_demo_ship_company`, `ord_demo_ship_address_01`, `ord_demo_ship_address_02`, `ord_demo_ship_city`, `ord_demo_ship_state`, `ord_demo_ship_post_code`, `ord_demo_ship_country`, `ord_demo_email`, `ord_demo_phone`, `ord_demo_comments`) VALUES
('00000001', 20, 1, 2, 6.00, 1.00, 'UK Standard Shipping', 3.95, 9.95, '', 0.00, 1.00, '', 0.00, '', 0.00, '20', 1.66, 0.00, 9.95, 2, 7.00, 0.00, 70, 'GBP', 1.0000, 2, '2015-11-23 14:59:23', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
('00000002', 21, 2, 1, 8.00, 0.00, 'UK Standard Shipping', 0.00, 8.00, 'Discount Code "FREE-UK-SHIPPING" - Free UK shipping.', 3.95, 3.95, '', 0.00, '', 0.00, '20', 1.33, 0.00, 8.00, 4, 8.00, 0.00, 0, 'GBP', 1.0000, 2, '2015-12-16 10:58:41', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
('00000003', 22, 2, 1, 30.00, 0.00, 'UK Standard Shipping', 3.95, 33.95, '', 0.00, 0.00, '', 0.00, '', 0.00, '20', 5.66, 0.00, 33.95, 2, 30.00, 0.00, 300, 'GBP', 1.0000, 2, '2015-12-17 10:59:20', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_reward_points_converted`
--

DROP TABLE IF EXISTS `flexicart_reward_points_converted`;
CREATE TABLE IF NOT EXISTS `flexicart_reward_points_converted` (
  `rew_convert_id` int(10) NOT NULL AUTO_INCREMENT,
  `rew_convert_ord_detail_fk` int(10) NOT NULL DEFAULT '10',
  `rew_convert_discount_fk` varchar(50) NOT NULL DEFAULT '',
  `rew_convert_points` int(10) NOT NULL DEFAULT '10',
  `rew_convert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rew_convert_id`),
  UNIQUE KEY `rew_convert_id` (`rew_convert_id`) USING BTREE,
  KEY `rew_convert_discount_fk` (`rew_convert_discount_fk`),
  KEY `rew_convert_ord_detail_fk` (`rew_convert_ord_detail_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `flexicart_reward_points_converted`
--

TRUNCATE TABLE `flexicart_reward_points_converted`;
--
-- Volcado de datos para la tabla `flexicart_reward_points_converted`
--

INSERT INTO `flexicart_reward_points_converted` (`rew_convert_id`, `rew_convert_ord_detail_fk`, `rew_convert_discount_fk`, `rew_convert_points`, `rew_convert_date`) VALUES
(1, 1, '35', 400, '2015-11-01 20:49:48'),
(2, 2, '35', 100, '2015-11-03 00:36:28'),
(3, 7, '36', 1000, '2015-11-04 04:23:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_shipping_item_rules`
--

DROP TABLE IF EXISTS `flexicart_shipping_item_rules`;
CREATE TABLE IF NOT EXISTS `flexicart_shipping_item_rules` (
  `ship_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_item_item_fk` int(11) NOT NULL DEFAULT '0',
  `ship_item_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_item_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_item_value` double(8,4) DEFAULT NULL,
  `ship_item_separate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicate if item should have a shipping rate calculated specifically for it.',
  `ship_item_banned` tinyint(1) NOT NULL DEFAULT '0',
  `ship_item_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_item_id`),
  UNIQUE KEY `ship_item_id` (`ship_item_id`) USING BTREE,
  KEY `ship_item_zone_fk` (`ship_item_zone_fk`) USING BTREE,
  KEY `ship_item_location_fk` (`ship_item_location_fk`) USING BTREE,
  KEY `ship_item_item_fk` (`ship_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `flexicart_shipping_item_rules`
--

TRUNCATE TABLE `flexicart_shipping_item_rules`;
--
-- Volcado de datos para la tabla `flexicart_shipping_item_rules`
--

INSERT INTO `flexicart_shipping_item_rules` (`ship_item_id`, `ship_item_item_fk`, `ship_item_location_fk`, `ship_item_zone_fk`, `ship_item_value`, `ship_item_separate`, `ship_item_banned`, `ship_item_status`) VALUES
(1, 104, 1, 0, 0.0000, 0, 0, 1),
(2, 106, 0, 0, NULL, 1, 0, 1),
(3, 107, 1, 0, NULL, 0, 1, 1),
(4, 108, 1, 0, NULL, 0, 2, 1),
(5, 108, 2, 0, NULL, 0, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_shipping_options`
--

DROP TABLE IF EXISTS `flexicart_shipping_options`;
CREATE TABLE IF NOT EXISTS `flexicart_shipping_options` (
  `ship_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(50) NOT NULL DEFAULT '',
  `ship_description` varchar(50) NOT NULL DEFAULT '',
  `ship_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_inc_sub_locations` tinyint(1) NOT NULL DEFAULT '0',
  `ship_tax_rate` double(7,4) DEFAULT NULL,
  `ship_discount_inclusion` tinyint(1) NOT NULL DEFAULT '0',
  `ship_status` tinyint(1) NOT NULL DEFAULT '0',
  `ship_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_id`),
  UNIQUE KEY `ship_id` (`ship_id`) USING BTREE,
  KEY `ship_zone_fk` (`ship_zone_fk`) USING BTREE,
  KEY `ship_location_fk` (`ship_location_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Truncar tablas antes de insertar `flexicart_shipping_options`
--

TRUNCATE TABLE `flexicart_shipping_options`;
--
-- Volcado de datos para la tabla `flexicart_shipping_options`
--

INSERT INTO `flexicart_shipping_options` (`ship_id`, `ship_name`, `ship_description`, `ship_location_fk`, `ship_zone_fk`, `ship_inc_sub_locations`, `ship_tax_rate`, `ship_discount_inclusion`, `ship_status`, `ship_default`) VALUES
(1, 'UK Standard Shipping', '2-3 Days', 1, 0, 0, NULL, 1, 1, 1),
(2, 'UK Recorded Shipping', '2-3 Days', 1, 0, 0, NULL, 0, 1, 0),
(3, 'UK Special Shipping', 'Next Day', 1, 0, 0, NULL, 0, 1, 0),
(4, 'UK Collection', 'Available Next Day', 1, 0, 0, NULL, 0, 1, 0),
(5, 'EU Zone 1: Standard Shipping', '3-4 Days', 0, 1, 0, NULL, 0, 1, 0),
(6, 'EU Zone 1: Special Shipping', '1-2 Days', 0, 1, 0, NULL, 0, 1, 0),
(7, 'EU Zone 2: Standard Shipping', '4-6 Days', 0, 2, 0, NULL, 0, 1, 0),
(8, 'EU Zone 2: Special Shipping', '2-4 Days', 0, 2, 0, NULL, 0, 1, 0),
(9, 'EU Zone 3: Standard Shipping', '5-8 Days', 0, 3, 0, NULL, 0, 1, 0),
(10, 'EU Zone 3: Special Shipping', '3-5 Days', 0, 3, 0, NULL, 0, 1, 0),
(11, 'Australia (Non NSW) Shipping', '12 Days', 8, 0, 0, NULL, 0, 1, 0),
(12, 'Australia NSW Shipping', '10 Days', 11, 0, 0, NULL, 0, 1, 0),
(13, 'Canada Shipping', '8 Days', 9, 0, 0, NULL, 0, 1, 0),
(14, 'United States (Non CA or NY) Shipping', '8 Days', 10, 0, 0, NULL, 0, 1, 0),
(15, 'New York State Shipping', '6 Days', 16, 0, 1, NULL, 0, 1, 0),
(16, 'California State Shipping', '8 Days', 14, 0, 0, NULL, 0, 1, 0),
(17, 'New York City Shipping', '6 Days', 18, 0, 0, NULL, 0, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_shipping_rates`
--

DROP TABLE IF EXISTS `flexicart_shipping_rates`;
CREATE TABLE IF NOT EXISTS `flexicart_shipping_rates` (
  `ship_rate_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ship_rate_ship_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_rate_value` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_tare_wgt` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_min_wgt` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_max_wgt` double(8,2) NOT NULL DEFAULT '9999.00',
  `ship_rate_min_value` double(10,2) NOT NULL DEFAULT '0.00',
  `ship_rate_max_value` double(10,2) NOT NULL DEFAULT '9999.00',
  `ship_rate_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_rate_id`),
  UNIQUE KEY `ship_rate_id` (`ship_rate_id`) USING BTREE,
  KEY `ship_rate_ship_fk` (`ship_rate_ship_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Truncar tablas antes de insertar `flexicart_shipping_rates`
--

TRUNCATE TABLE `flexicart_shipping_rates`;
--
-- Volcado de datos para la tabla `flexicart_shipping_rates`
--

INSERT INTO `flexicart_shipping_rates` (`ship_rate_id`, `ship_rate_ship_fk`, `ship_rate_value`, `ship_rate_tare_wgt`, `ship_rate_min_wgt`, `ship_rate_max_wgt`, `ship_rate_min_value`, `ship_rate_max_value`, `ship_rate_status`) VALUES
(1, 1, 3.95, 2.00, 0.00, 50.00, 0.00, 500.00, 1),
(2, 1, 4.50, 2.00, 50.00, 150.00, 0.00, 500.00, 1),
(3, 1, 5.25, 2.00, 150.00, 500.00, 0.00, 500.00, 1),
(4, 2, 5.10, 2.00, 0.00, 50.00, 0.00, 500.00, 1),
(5, 2, 5.75, 2.00, 50.00, 150.00, 0.00, 500.00, 1),
(6, 2, 6.40, 2.00, 150.00, 500.00, 0.00, 500.00, 1),
(7, 3, 7.50, 10.00, 0.00, 500.00, 0.00, 1000.00, 1),
(8, 3, 10.95, 10.00, 500.00, 0.00, 0.00, 9999.00, 1),
(9, 4, 0.00, 10.00, 0.00, 0.00, 0.00, 9999.00, 1),
(10, 5, 7.25, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(11, 6, 15.75, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(12, 7, 7.75, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(13, 8, 16.25, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(14, 9, 8.50, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(15, 10, 20.10, 0.00, 0.00, 0.00, 0.00, 0.00, 1),
(16, 11, 16.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(17, 12, 14.90, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(18, 13, 14.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(19, 14, 14.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(20, 15, 13.25, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(21, 16, 15.30, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(22, 17, 10.55, 10.00, 0.00, 0.00, 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_tax`
--

DROP TABLE IF EXISTS `flexicart_tax`;
CREATE TABLE IF NOT EXISTS `flexicart_tax` (
  `tax_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `tax_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_name` varchar(25) NOT NULL DEFAULT '',
  `tax_rate` double(7,4) NOT NULL DEFAULT '0.0000',
  `tax_status` tinyint(1) NOT NULL DEFAULT '0',
  `tax_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tax_id`),
  UNIQUE KEY `tax_id` (`tax_id`),
  KEY `tax_zone_fk` (`tax_zone_fk`),
  KEY `tax_location_fk` (`tax_location_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Truncar tablas antes de insertar `flexicart_tax`
--

TRUNCATE TABLE `flexicart_tax`;
--
-- Volcado de datos para la tabla `flexicart_tax`
--

INSERT INTO `flexicart_tax` (`tax_id`, `tax_location_fk`, `tax_zone_fk`, `tax_name`, `tax_rate`, `tax_status`, `tax_default`) VALUES
(1, 0, 4, 'VAT', 20.0000, 1, 1),
(2, 0, 5, 'No Tax (Non EU)', 0.0000, 1, 0),
(3, 16, 0, 'Tax New York', 4.0000, 1, 0),
(4, 14, 0, 'Tax California', 8.2500, 1, 0),
(5, 10, 0, 'Tax (Other US)', 6.0000, 1, 0),
(6, 18, 0, 'Tax New York City', 8.3700, 1, 0),
(7, 8, 0, 'GST', 10.0000, 1, 0),
(8, 9, 0, 'HST', 8.0000, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flexicart_tax_item_rates`
--

DROP TABLE IF EXISTS `flexicart_tax_item_rates`;
CREATE TABLE IF NOT EXISTS `flexicart_tax_item_rates` (
  `tax_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_item_item_fk` int(11) NOT NULL DEFAULT '0',
  `tax_item_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_item_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_item_rate` double(7,4) NOT NULL DEFAULT '0.0000',
  `tax_item_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tax_item_id`),
  UNIQUE KEY `tax_item_id` (`tax_item_id`) USING BTREE,
  KEY `tax_item_zone_fk` (`tax_item_zone_fk`),
  KEY `tax_item_location_fk` (`tax_item_location_fk`),
  KEY `tax_item_item_fk` (`tax_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `flexicart_tax_item_rates`
--

TRUNCATE TABLE `flexicart_tax_item_rates`;
--
-- Volcado de datos para la tabla `flexicart_tax_item_rates`
--

INSERT INTO `flexicart_tax_item_rates` (`tax_item_id`, `tax_item_item_fk`, `tax_item_location_fk`, `tax_item_zone_fk`, `tax_item_rate`, `tax_item_status`) VALUES
(1, 110, 0, 0, 0.0000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(100) NOT NULL,
  `accepted_filetypes` varchar(200) NOT NULL,
  PRIMARY KEY (`folder_id`),
  UNIQUE KEY `folder_name` (`folder_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `folders`
--

TRUNCATE TABLE `folders`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folder_permissions`
--

DROP TABLE IF EXISTS `folder_permissions`;
CREATE TABLE IF NOT EXISTS `folder_permissions` (
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `read` tinyint(4) DEFAULT '1',
  `write` tinyint(4) DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`,`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `folder_permissions`
--

TRUNCATE TABLE `folder_permissions`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `freedata`
--

DROP TABLE IF EXISTS `freedata`;
CREATE TABLE IF NOT EXISTS `freedata` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `coname` varchar(150) DEFAULT NULL,
  `local` int(1) NOT NULL,
  `phone` varchar(17) DEFAULT NULL,
  `mobile` varchar(17) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `add1` varchar(100) DEFAULT NULL,
  `add2` varchar(100) DEFAULT NULL,
  `add3` varchar(100) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `user_id` int(3) DEFAULT NULL,
  `sector_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`data_id`),
  UNIQUE KEY `coname` (`coname`,`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `freedata`
--

TRUNCATE TABLE `freedata`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `function_triggers`
--

DROP TABLE IF EXISTS `function_triggers`;
CREATE TABLE IF NOT EXISTS `function_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `fk_FT_Campaign` (`campaign_id`),
  KEY `fk_FT_Outcome` (`outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `function_triggers`
--

TRUNCATE TABLE `function_triggers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE IF NOT EXISTS `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `urn` int(11) NOT NULL,
  `loaded` datetime DEFAULT NULL,
  `contact` datetime NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `outcome_id` int(11) DEFAULT NULL,
  `outcome_reason_id` int(11) DEFAULT NULL,
  `comments` longtext CHARACTER SET utf8,
  `nextcall` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `contact_id` int(11) DEFAULT NULL,
  `progress_id` int(1) DEFAULT NULL,
  `last_survey` int(11) DEFAULT NULL,
  `call_direction` tinyint(1) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `pot_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `urn` (`urn`),
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`),
  KEY `repgroup_id` (`group_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_reason_id` (`outcome_reason_id`),
  KEY `pot_id` (`pot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `history`
--

TRUNCATE TABLE `history`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `history_log`
--

DROP TABLE IF EXISTS `history_log`;
CREATE TABLE IF NOT EXISTS `history_log` (
  `id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `table_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `col_name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `old_val` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `new_val` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `history_log`
--

TRUNCATE TABLE `history_log`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hours`
--

DROP TABLE IF EXISTS `hours`;
CREATE TABLE IF NOT EXISTS `hours` (
  `hours_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL COMMENT 'The Agent',
  `campaign_id` int(5) NOT NULL,
  `duration` int(20) NOT NULL COMMENT 'in seconds',
  `time_logged` int(20) NOT NULL COMMENT 'in seconds',
  `date` date NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `updated_id` int(5) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`hours_id`),
  UNIQUE KEY `user_id_2` (`user_id`,`campaign_id`,`date`),
  KEY `user_id` (`user_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Agent duration by campaign and day' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `hours`
--

TRUNCATE TABLE `hours`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hours_logged`
--

DROP TABLE IF EXISTS `hours_logged`;
CREATE TABLE IF NOT EXISTS `hours_logged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `hours_logged`
--

TRUNCATE TABLE `hours_logged`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hour_exception`
--

DROP TABLE IF EXISTS `hour_exception`;
CREATE TABLE IF NOT EXISTS `hour_exception` (
  `exception_id` int(11) NOT NULL AUTO_INCREMENT,
  `hour_id` int(11) NOT NULL,
  `exception_type_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`exception_id`),
  KEY `hour_id` (`hour_id`),
  KEY `exception_type_id` (`exception_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `hour_exception`
--

TRUNCATE TABLE `hour_exception`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hour_exception_type`
--

DROP TABLE IF EXISTS `hour_exception_type`;
CREATE TABLE IF NOT EXISTS `hour_exception_type` (
  `exception_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `exception_name` varchar(50) DEFAULT NULL,
  `paid` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`exception_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `hour_exception_type`
--

TRUNCATE TABLE `hour_exception_type`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `importcsv`
--

DROP TABLE IF EXISTS `importcsv`;
CREATE TABLE IF NOT EXISTS `importcsv` (
  `contact_fullname` varchar(255) DEFAULT NULL,
  `contact_add1` varchar(255) DEFAULT NULL,
  `contact_add2` varchar(255) DEFAULT NULL,
  `contact_add3` varchar(255) DEFAULT NULL,
  `contact_county` varchar(255) DEFAULT NULL,
  `contact_postcode` varchar(255) DEFAULT NULL,
  `contact_tel_Telephone` varchar(255) DEFAULT NULL,
  `contact_tel_Mobile` varchar(255) DEFAULT NULL,
  `contact_tel_Landline` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `c1` varchar(255) DEFAULT NULL,
  `d1` varchar(255) DEFAULT NULL,
  `c2` varchar(255) DEFAULT NULL,
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`urn`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `importcsv`
--

TRUNCATE TABLE `importcsv`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(50) NOT NULL,
  PRIMARY KEY (`keyword_id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `keywords`
--

TRUNCATE TABLE `keywords`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `location_id` int(11) NOT NULL,
  `lat` decimal(18,12) DEFAULT NULL,
  `lng` decimal(18,12) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `lat` (`lat`,`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `locations`
--

TRUNCATE TABLE `locations`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `migrations`
--

TRUNCATE TABLE `migrations`;
--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(94);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `outcomes`
--

DROP TABLE IF EXISTS `outcomes`;
CREATE TABLE IF NOT EXISTS `outcomes` (
  `outcome_id` int(11) NOT NULL AUTO_INCREMENT,
  `outcome` varchar(35) CHARACTER SET utf8 NOT NULL,
  `set_status` int(1) DEFAULT NULL,
  `set_progress` tinyint(1) DEFAULT NULL,
  `set_parked_code` int(11) DEFAULT NULL,
  `positive` int(1) DEFAULT NULL,
  `dm_contact` int(1) DEFAULT NULL,
  `sort` int(2) DEFAULT NULL,
  `enable_select` int(1) DEFAULT NULL,
  `force_comment` int(1) DEFAULT NULL,
  `force_nextcall` int(1) DEFAULT NULL,
  `delay_hours` tinyint(4) DEFAULT NULL,
  `no_history` tinyint(1) DEFAULT NULL,
  `disabled` tinyint(4) DEFAULT NULL,
  `keep_record` int(11) DEFAULT NULL,
  `requires_callback` int(11) DEFAULT NULL,
  `contact_made` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`outcome_id`),
  KEY `set_parked_code` (`set_parked_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=102 ;

--
-- Truncar tablas antes de insertar `outcomes`
--

TRUNCATE TABLE `outcomes`;
--
-- Volcado de datos para la tabla `outcomes`
--

INSERT INTO `outcomes` (`outcome_id`, `outcome`, `set_status`, `set_progress`, `set_parked_code`, `positive`, `dm_contact`, `sort`, `enable_select`, `force_comment`, `force_nextcall`, `delay_hours`, `no_history`, `disabled`, `keep_record`, `requires_callback`, `contact_made`) VALUES
(1, 'Call Back', 1, NULL, NULL, NULL, NULL, 4, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0),
(2, 'Call Back DM', 1, NULL, NULL, NULL, 1, 1, 1, NULL, 1, NULL, NULL, NULL, 1, 1, 0),
(3, 'Answer Machine', 1, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL, NULL, 0),
(4, 'Dead Line', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(5, 'Engaged', 1, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL, NULL, 0),
(7, 'No Answer', 1, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL, NULL, 0),
(12, 'Not Interested', 3, NULL, NULL, NULL, 1, 9, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(13, 'Not Eligible', 3, NULL, NULL, NULL, NULL, 9, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(17, 'Unavailable', 1, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL, NULL, 0),
(30, 'Deceased', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(32, 'Moved', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(33, 'Slammer', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL, NULL, 0),
(60, 'Survey Complete', 4, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(63, 'Wrong Number', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(64, 'Duplicate', 3, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(65, 'Fax Machine', 3, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(66, 'Survey Refused', 3, NULL, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(67, 'Adding additional notes', NULL, NULL, NULL, NULL, NULL, 10, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(68, 'Changing next action date', NULL, NULL, NULL, NULL, NULL, 2, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0),
(69, 'No Number', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(70, 'Transfer', 4, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0),
(71, 'Cross Transfer', 4, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0),
(72, 'Appointment', 4, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0),
(73, 'Not in business', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(74, 'Remove from records', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(76, 'Not required', 3, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(78, 'Head Office Deals', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(79, 'Gatekeeper Refusal', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(80, 'Language Barrier', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(81, 'No Sale', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(82, 'Existing Customer', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(83, 'Sale', 4, NULL, NULL, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(84, 'Email Sent', 1, NULL, NULL, 1, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, 0),
(85, 'Interest Now', 1, NULL, NULL, NULL, 1, NULL, 1, 1, 1, NULL, NULL, NULL, NULL, 1, 0),
(86, 'Interest Future', 1, NULL, NULL, NULL, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, 0),
(87, 'Website enquiry', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(88, 'Research Required', 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 0),
(89, 'Data Captured', NULL, NULL, NULL, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0),
(90, 'Soft Email', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(91, 'Under Warrenty', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0),
(92, 'Not Eligible:Rented Property', 3, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(93, 'Not Eligible:Helplink', 3, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(94, 'Meeting booked - face to face', 4, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(95, 'Meeting booked - remote', 4, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(96, 'Telephone Appointment – Consultant', 4, 1, NULL, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(97, 'No Contact', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(98, 'Prequalify', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(99, 'Qualified', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(100, 'Quoted', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(101, 'Proposal Accepted', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `outcomes_to_campaigns`
--

DROP TABLE IF EXISTS `outcomes_to_campaigns`;
CREATE TABLE IF NOT EXISTS `outcomes_to_campaigns` (
  `outcome_id` int(3) NOT NULL,
  `campaign_id` int(3) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `outcomes_to_campaigns`
--

TRUNCATE TABLE `outcomes_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `outcomes_to_roles`
--

DROP TABLE IF EXISTS `outcomes_to_roles`;
CREATE TABLE IF NOT EXISTS `outcomes_to_roles` (
  `outcome_id` int(5) NOT NULL,
  `role_id` int(5) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`outcome_id`),
  KEY `outcome_id` (`outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `outcomes_to_roles`
--

TRUNCATE TABLE `outcomes_to_roles`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `outcome_reasons`
--

DROP TABLE IF EXISTS `outcome_reasons`;
CREATE TABLE IF NOT EXISTS `outcome_reasons` (
  `outcome_reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `outcome_reason` varchar(100) NOT NULL,
  PRIMARY KEY (`outcome_reason_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Truncar tablas antes de insertar `outcome_reasons`
--

TRUNCATE TABLE `outcome_reasons`;
--
-- Volcado de datos para la tabla `outcome_reasons`
--

INSERT INTO `outcome_reasons` (`outcome_reason_id`, `outcome_reason`) VALUES
(1, 'Answer machine'),
(2, 'Deceased'),
(3, 'Dead line'),
(4, 'Wrong number'),
(5, 'Already renewed'),
(6, 'Happy with current');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `outcome_reason_campaigns`
--

DROP TABLE IF EXISTS `outcome_reason_campaigns`;
CREATE TABLE IF NOT EXISTS `outcome_reason_campaigns` (
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `outcome_reason_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`outcome_id`,`outcome_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `outcome_reason_campaigns`
--

TRUNCATE TABLE `outcome_reason_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ownership`
--

DROP TABLE IF EXISTS `ownership`;
CREATE TABLE IF NOT EXISTS `ownership` (
  `urn` int(12) NOT NULL,
  `user_id` int(4) NOT NULL,
  UNIQUE KEY `urn_user` (`urn`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `ownership`
--

TRUNCATE TABLE `ownership`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ownership_triggers`
--

DROP TABLE IF EXISTS `ownership_triggers`;
CREATE TABLE IF NOT EXISTS `ownership_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  PRIMARY KEY (`trigger_id`),
  UNIQUE KEY `trigger_id2` (`campaign_id`,`outcome_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `ownership_triggers`
--

TRUNCATE TABLE `ownership_triggers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ownership_trigger_users`
--

DROP TABLE IF EXISTS `ownership_trigger_users`;
CREATE TABLE IF NOT EXISTS `ownership_trigger_users` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `ownership_trigger_users`
--

TRUNCATE TABLE `ownership_trigger_users`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `park_codes`
--

DROP TABLE IF EXISTS `park_codes`;
CREATE TABLE IF NOT EXISTS `park_codes` (
  `parked_code` int(11) NOT NULL AUTO_INCREMENT,
  `park_reason` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`parked_code`),
  UNIQUE KEY `park_reason` (`park_reason`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Truncar tablas antes de insertar `park_codes`
--

TRUNCATE TABLE `park_codes`;
--
-- Volcado de datos para la tabla `park_codes`
--

INSERT INTO `park_codes` (`parked_code`, `park_reason`) VALUES
(5, 'Duplicated'),
(8, 'No Numbers'),
(6, 'Not in date'),
(2, 'Not Working'),
(10, 'Onion'),
(7, 'Out of Area'),
(1, 'Rationing'),
(9, 'Reached max dials'),
(4, 'Suppressed'),
(11, 'Unity'),
(3, 'Unknown');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `permission_group` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=165 ;

--
-- Truncar tablas antes de insertar `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES
(1, 'set call outcomes', 'Records', 'The user can set a call outcome on a record. ie. Answer machine, Sale etc'),
(2, 'set progress', 'Records', 'The user can set a progress status on a record. ie. Pending, In progress, Completed'),
(3, 'add surveys', 'Surveys', 'The user can add surveys to a record'),
(4, 'view surveys', 'Surveys', 'The user can view surveys on a record'),
(5, 'edit surveys', 'Surveys', 'The user can edit surveys on a record'),
(6, 'delete surveys', 'Surveys', 'The user can delete surveys from a record'),
(7, 'add contacts', 'Contacts', 'The user can add contacts to a record'),
(8, 'edit contacts', 'Contacts', 'The user can edit contacts on a  record'),
(9, 'delete contacts', 'Contacts', 'The user can delete contacts from a record'),
(10, 'add companies', 'Companies', 'The user can add companies to a record'),
(11, 'edit companies', 'Companies', 'The user can edit companies on a record'),
(12, 'add records', 'Records', 'The user can add new recors to the system'),
(13, 'reset records', 'Records', 'The can reset a record. Bringing a dead record back in for dialling'),
(14, 'park records', 'Records', 'The user can park/unpark a record'),
(15, 'view ownership', 'Ownership', 'The user can view the ownership panel'),
(16, 'change ownership', 'Ownership', 'The user can change the ownership from within a  record'),
(17, 'view appointments', 'Appointments', 'The user can view appointments on the system'),
(18, 'add appointments', 'Appointments', 'The user can add appointments to the system'),
(19, 'edit appointments', 'Appointments', 'The user can edit appointments on the system'),
(20, 'delete appointments', 'Appointments', 'The user can cancel appointments on the system'),
(21, 'view history', 'History', 'The user can view the history panel on a record'),
(22, 'delete history', 'History', 'The user can delete the history on a record'),
(23, 'edit history', 'History', 'The user can edit the history on a record'),
(24, 'view recordings', 'Recordings', 'The user can vie wthe records associated to a record'),
(25, 'delete recordings', 'Recordings', 'The user can delete recordings (n/a)'),
(26, 'search records', 'Search', 'The user has access to the search page'),
(27, 'send email', 'Email', 'The user can send emails from the record page'),
(28, 'view email', 'Email', 'The user can see the email panel on the record page'),
(29, 'all campaigns', 'System', 'The user can access any and all campaigns on the system'),
(30, 'agent dash', 'Dashboards', 'The user has access to the agent dashboard'),
(31, 'client dash', 'Dashboards', 'The user has access to the dlient dashboard'),
(32, 'management dash', 'Dashboards', 'The user has access to the management dashboard'),
(34, 'search campaigns', 'Search', 'The user can search records by campaign'),
(35, 'search surveys', 'Search', 'The view the survey list page with filters'),
(36, 'log hours', 'System', 'The user can add/edit agent hours'),
(37, 'edit scripts', 'Admin', 'The user can add/edit campaign scripts'),
(38, 'edit templates', 'Admin', 'The user can add/edit email templates'),
(39, 'reassign data', 'Data', 'The user has access to the data management page allowing them to reallocate data between users'),
(40, 'view logs', 'Admin', 'The user has access to various system logs'),
(41, 'view hours', 'Admin', 'The user can view agent hours'),
(42, 'show footer', 'System', 'The user can see the footer info display showing basic stats.'),
(43, 'admin menu', 'Admin', 'The user has access to the admin submenu'),
(44, 'campaign menu', 'Admin', 'The user has access to the campaign submenu'),
(45, 'view attachments', 'Attachments', 'The user can view attachements on a record'),
(46, 'add attachment', 'Attachments', 'The user can add attachments to a record'),
(47, 'full calendar', 'Calendar', 'The user has access to the full page calendar'),
(48, 'mini calendar', 'Calendar', 'The user can view the mini-calendar from the appointments panel'),
(49, 'delete email', 'Email', 'The user can delete emails on a record'),
(52, 'by agent', 'Reports', 'The user can filter reports by agent'),
(56, 'nbf dash', 'Dashboards', 'The user has access to the NBF Dashboard'),
(57, 'mix campaigns', 'System', 'The user can view records in multiple campaigns from the list views'),
(59, 'search parked', 'Search', 'The user can search parked records'),
(60, 'search unassigned', 'Search', 'The user can search for records unassigned records'),
(61, 'search any owner', 'Search', 'The user can search for records assigned to any other user'),
(62, 'search groups', 'Search', 'The user can search for records assigned to any group'),
(63, 'search dead', 'Search', 'The user can search dead records'),
(64, 'view own records', 'Default view', 'The user can only see data assigend to them by default'),
(65, 'view own group', 'Default view', 'The user can only see data assigned to their own group by default'),
(66, 'search teams', 'Search', 'The user can search records assigned to any team'),
(67, 'view own team', 'Default view', 'The user can only report on their own team members'),
(69, 'by group', 'Reports', 'The user can filter reports by group'),
(70, 'by team', 'Reports', 'The user can filter reports by team'),
(71, 'email', 'Reports', 'The user has access to the email reports'),
(72, 'outcomes', 'Reports', 'The user has access to the outcomes reports'),
(73, 'activity', 'Reports', 'The user has access to the activity reports'),
(74, 'Transfers', 'Reports', 'The user has access to the transfer reports'),
(75, 'survey answers', 'Reports', 'The user has access to the survey answers report'),
(76, 'urgent flag', 'Records', 'The user can set a record as urgent by clicking the flag on the record update panel'),
(77, 'urgent dropdown', 'Records', 'The user can see the urgent dropdown menu on the record update panel'),
(78, 'search recordings', 'Recordings', 'The user has access to the recordings panel within a campaign'),
(79, 'reports menu', 'Reports', 'The user has access to the reports submenu'),
(80, 'data menu', 'Data', 'The user has access to the data submenu'),
(81, 'import data', 'Data', 'The user can import records using a CSV file from the import menu'),
(82, 'export data', 'Data', 'The user has access to the data export page'),
(83, 'archive data', 'Data', 'The user has access to the data archiving features'),
(84, 'ration data', 'Data', 'The user can manage the daily data rations'),
(85, 'use callpot', 'System', 'The user has access to the "Start dialling" feature'),
(86, 'view unassigned', 'Default view', 'Unassigned records will be included in the list view by default'),
(87, 'view parked', 'Default view', 'Parked records will be included in the list view by default'),
(88, 'view dead', 'Default view', 'Dead records will be included in the list view by default'),
(89, 'view completed', 'Default view', 'Completed records will be included in the list view by default'),
(90, 'view live', 'Default view', 'Live records will be included in the list view by default'),
(91, 'view pending', 'Default view', 'Records with tasks pending will be included in the list view by default'),
(92, 'keep records', 'System', 'The user will automatically be assigned to a record they view if nobody else already owns it'),
(93, 'files menu', 'Files', 'The user has access to the files submenu'),
(94, 'view files', 'Files', 'The user can view and download files from folders they have access to'),
(98, 'admin files', 'Admin', 'The user can manage file and folder permissions on the system'),
(99, 'list records', 'Records', 'The user can view the list records page'),
(102, 'delete files', 'Files', 'The user can delete files from folders they have access to'),
(103, 'add files', 'Files', 'The user can add new files to folders they have access to'),
(104, 'search files', 'Files', 'The user can search for files they have access to'),
(108, 'view dashboard', 'Dashboards', 'The user can view the dashboard'),
(110, 'search actions', 'Search', 'The user can access the actions menu from the search page. Used to bulk park and bulk email based on search criteria.'),
(111, 'edit export', 'Data', 'The user can add/edit CSV exports'),
(112, 'edit outcomes', 'Data', 'The user can add/edit outcomes on the system'),
(113, 'triggers', 'Data', 'The user can add/edit outcome triggers for a campaign'),
(114, 'duplicates', 'Data', 'The user can search and managae duplicate records'),
(115, 'suppression', 'Data', 'The user can manage record suppression within the system'),
(116, 'parkcodes', 'Data', 'The user can add/edit park codes within the system'),
(117, 'productivity', 'Reports', 'The user has access to the productivity report'),
(118, 'database', 'Admin', 'The user has access to various database features. IE. Migrate/Reset/Backup'),
(119, 'campaign access', 'Admin', 'The user can control which users have access to a campaign'),
(120, 'campaign setup', 'Admin', 'The user can add/edit campaigns on the system'),
(121, 'planner', 'Records', 'The user has access to the planner'),
(122, 'admin groups', 'Admin', 'The user can add/edit groups on the system'),
(123, 'campaign fields', 'Admin', 'The user can add/edit custom fields linked to a campaign'),
(124, 'admin teams', 'Admin', 'The user can add/edit teams on the system'),
(125, 'admin roles', 'Admin', 'The user can manage permissions and roles on the system'),
(126, 'admin users', 'Admin', 'The user can add/edit other users on the system'),
(127, 'use timer', 'System', 'The user will be shown a timer when they dial a telephone number'),
(130, 'sms', 'Reports', 'The user has access to the SMS report'),
(134, 'send sms', 'SMS', 'The user is able to send SMS messages from with a record'),
(135, 'system menu', 'Admin', 'The user has access to various system related options. This is required to access any pages within this submenu'),
(136, 'files only', 'Files', 'The user only has access to the files and folders feature. Can be used for client accounts that need to send and retrieve files'),
(137, 'survey only', 'Survey', 'The user only has access to complete a survey'),
(138, 'take ownership', 'System', 'The user will automatically take ownership of a record when they update it'),
(139, 'admin planner', 'Planner', 'The user can view and amend the planner of other users'),
(140, 'set call direction', 'Records', 'The user must set a call direction (inbound/outbound) when they update a record'),
(142, 'apps to planner', 'Appointments', 'Records are automatically added to the attendees planner when an appointment is made for them'),
(143, 'import ics', 'Appointments', 'The user can import a google/outlook ICS file into the system'),
(144, 'export ics', 'Appointments', 'The user can export appointments to an ICS file used to import to other calendars such as google/outlook'),
(145, 'add custom items', 'System', 'The user can add multiple entries to the additional info panel'),
(146, 'report on', 'Reports', 'The user will be displayed in reports. Managers probably don''t need this options'),
(148, 'edit outcome', 'History', 'The user can edit the history of a record'),
(149, 'get address', 'Address', 'The user can find a full address by using just a postcode. This feature uses a 3rd party API and may cost credits'),
(150, 'record options', 'Record Options', 'The user can park, unpark and remove records on the system'),
(151, 'change color', 'Record Options', 'The user can change the color of a record'),
(152, 'change pot', 'Record Options', 'The user can move the record to another data pot'),
(153, 'change source', 'Record Options', 'The user can change the source of the record'),
(154, 'change icon', 'Record Options', 'The user can change a records icon'),
(155, 'change campaign', 'Record Options', 'The user can move a record to another campaign'),
(156, 'admin shop', 'Admin', 'The user can manage the shop/cart/order configuration'),
(157, 'data counts', 'Reports', 'The user can view the data counts report'),
(158, 'slot config', 'Admin', 'The user can create and edit appointment attendee slots and availability'),
(159, 'client report', 'Reports', 'The user can view the client reports'),
(160, 'edit recent history', 'History', 'The user can edit the recent history entry (created on the same day) '),
(161, 'view record', 'Records', 'Can the user access the record details page to update and edit the record'),
(163, 'slot availability', 'Admin', 'Allow the user to manage the availability of attendees for appointments'),
(164, 'dashboard viewers', 'Dashboards', 'Can the user set the access for the custom dashboards');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progress_description`
--

DROP TABLE IF EXISTS `progress_description`;
CREATE TABLE IF NOT EXISTS `progress_description` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `progress_color` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`progress_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `progress_description`
--

TRUNCATE TABLE `progress_description`;
--
-- Volcado de datos para la tabla `progress_description`
--

INSERT INTO `progress_description` (`progress_id`, `description`, `progress_color`) VALUES
(1, 'Pending', 'red'),
(2, 'In Progress', 'orange'),
(3, 'Complete', 'green');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `question_script` varchar(300) CHARACTER SET utf8 NOT NULL,
  `question_guide` varchar(300) CHARACTER SET utf8 NOT NULL,
  `other` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `question_cat_id` int(11) DEFAULT NULL,
  `sort` int(2) NOT NULL DEFAULT '5',
  `nps_question` tinyint(1) DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `survey_info_id` tinyint(3) DEFAULT NULL,
  `trigger_score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  KEY `survey_info_id` (`survey_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `questions`
--

TRUNCATE TABLE `questions`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `questions_to_categories`
--

DROP TABLE IF EXISTS `questions_to_categories`;
CREATE TABLE IF NOT EXISTS `questions_to_categories` (
  `question_cat_id` int(3) NOT NULL AUTO_INCREMENT,
  `question_cat_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`question_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `questions_to_categories`
--

TRUNCATE TABLE `questions_to_categories`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `question_options`
--

DROP TABLE IF EXISTS `question_options`;
CREATE TABLE IF NOT EXISTS `question_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `question_id` int(11) NOT NULL,
  `trigger_email` tinyint(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `question_options`
--

TRUNCATE TABLE `question_options`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `records`
--

DROP TABLE IF EXISTS `records`;
CREATE TABLE IF NOT EXISTS `records` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `outcome_id` int(3) DEFAULT NULL,
  `outcome_reason_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `nextcall` datetime DEFAULT NULL,
  `dials` int(2) DEFAULT '0',
  `record_status` tinyint(1) NOT NULL DEFAULT '1',
  `parked_code` int(11) DEFAULT NULL,
  `parked_date` timestamp NULL DEFAULT NULL,
  `progress_id` tinyint(1) DEFAULT NULL,
  `urgent` int(11) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `reset_date` date DEFAULT NULL,
  `last_survey_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `pot_id` int(11) DEFAULT NULL,
  `urn_copied` int(11) DEFAULT NULL,
  `company_copied` int(11) DEFAULT NULL,
  `contact_copied` int(11) DEFAULT NULL,
  `record_color` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `map_icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`urn`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_id` (`outcome_id`),
  KEY `group_id` (`team_id`),
  KEY `progress_id` (`progress_id`),
  KEY `last_survey_id` (`last_survey_id`),
  KEY `outcome_reason_id` (`outcome_reason_id`),
  KEY `pot_id` (`pot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `records`
--

TRUNCATE TABLE `records`;
--
-- Disparadores `records`
--
DROP TRIGGER IF EXISTS `records_before_update`;
DELIMITER //
CREATE TRIGGER `records_before_update` BEFORE UPDATE ON `records`
 FOR EACH ROW BEGIN 

      if new.parked_code or new.parked_code is null then

        SET NEW.parked_date = CURRENT_TIMESTAMP;

       end if;

		END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_comments`
--

DROP TABLE IF EXISTS `record_comments`;
CREATE TABLE IF NOT EXISTS `record_comments` (
  `urn` int(11) NOT NULL,
  `last_comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `record_comments`
--

TRUNCATE TABLE `record_comments`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_details`
--

DROP TABLE IF EXISTS `record_details`;
CREATE TABLE IF NOT EXISTS `record_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `c1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c4` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c5` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c6` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c7` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c8` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c9` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c10` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d1` date DEFAULT NULL,
  `d2` date DEFAULT NULL,
  `d3` date DEFAULT NULL,
  `d4` date DEFAULT NULL,
  `d5` date DEFAULT NULL,
  `d6` date DEFAULT NULL,
  `d7` date DEFAULT NULL,
  `d8` date DEFAULT NULL,
  `d9` date DEFAULT NULL,
  `d10` date DEFAULT NULL,
  `dt1` datetime DEFAULT NULL,
  `dt2` datetime DEFAULT NULL,
  `dt3` datetime DEFAULT NULL,
  `dt4` datetime DEFAULT NULL,
  `dt5` datetime DEFAULT NULL,
  `dt6` datetime DEFAULT NULL,
  `dt7` datetime DEFAULT NULL,
  `dt8` datetime DEFAULT NULL,
  `dt9` datetime DEFAULT NULL,
  `dt10` datetime DEFAULT NULL,
  `n1` decimal(20,2) DEFAULT NULL,
  `n2` decimal(20,2) DEFAULT NULL,
  `n3` decimal(20,2) DEFAULT NULL,
  `n4` decimal(20,2) DEFAULT NULL,
  `n5` decimal(20,2) DEFAULT NULL,
  `n6` decimal(20,2) DEFAULT NULL,
  `n7` decimal(20,2) DEFAULT NULL,
  `n8` decimal(20,2) DEFAULT NULL,
  `n9` decimal(20,2) DEFAULT NULL,
  `n10` decimal(20,2) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `urn` (`urn`),
  KEY `added_by` (`added_by`,`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `record_details`
--

TRUNCATE TABLE `record_details`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_details_fields`
--

DROP TABLE IF EXISTS `record_details_fields`;
CREATE TABLE IF NOT EXISTS `record_details_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `field_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `is_select` int(1) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  `is_visible` int(11) DEFAULT NULL,
  `is_renewal` int(11) DEFAULT NULL,
  `format` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(4) NOT NULL DEFAULT '1',
  `is_color` tinyint(1) DEFAULT NULL,
  `is_owner` tinyint(1) DEFAULT NULL,
  `is_client_ref` tinyint(1) DEFAULT NULL,
  `is_radio` tinyint(1) DEFAULT NULL,
  `is_decimal` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `record_details_fields`
--

TRUNCATE TABLE `record_details_fields`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_details_options`
--

DROP TABLE IF EXISTS `record_details_options`;
CREATE TABLE IF NOT EXISTS `record_details_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `option` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `record_details_options`
--

TRUNCATE TABLE `record_details_options`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_keywords`
--

DROP TABLE IF EXISTS `record_keywords`;
CREATE TABLE IF NOT EXISTS `record_keywords` (
  `urn` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  UNIQUE KEY `urn` (`urn`,`keyword_id`),
  KEY `urn_2` (`urn`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `record_keywords`
--

TRUNCATE TABLE `record_keywords`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_planner`
--

DROP TABLE IF EXISTS `record_planner`;
CREATE TABLE IF NOT EXISTS `record_planner` (
  `record_planner_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `urn` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `order_num` int(11) DEFAULT NULL,
  `planner_status` tinyint(1) NOT NULL DEFAULT '1',
  `planner_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`record_planner_id`),
  UNIQUE KEY `user_id_2` (`user_id`,`urn`),
  KEY `user_id` (`user_id`),
  KEY `urn` (`urn`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `record_planner`
--

TRUNCATE TABLE `record_planner`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_planner_route`
--

DROP TABLE IF EXISTS `record_planner_route`;
CREATE TABLE IF NOT EXISTS `record_planner_route` (
  `record_planner_route_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_planner_id` int(11) NOT NULL,
  `start_add` varchar(255) NOT NULL,
  `start_lat` double NOT NULL,
  `start_lng` double NOT NULL,
  `end_add` varchar(255) NOT NULL,
  `end_lat` double NOT NULL,
  `end_lng` double NOT NULL,
  `distance` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `travel_mode` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`record_planner_route_id`),
  KEY `record_planner_id` (`record_planner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `record_planner_route`
--

TRUNCATE TABLE `record_planner_route`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record_tasks`
--

DROP TABLE IF EXISTS `record_tasks`;
CREATE TABLE IF NOT EXISTS `record_tasks` (
  `record_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  PRIMARY KEY (`record_task_id`),
  UNIQUE KEY `urn_2` (`urn`,`task_id`),
  UNIQUE KEY `urn_3` (`urn`,`task_id`),
  KEY `urn` (`urn`,`task_id`,`task_status_id`),
  KEY `task_status_id` (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Truncar tablas antes de insertar `record_tasks`
--

TRUNCATE TABLE `record_tasks`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `related_records`
--

DROP TABLE IF EXISTS `related_records`;
CREATE TABLE IF NOT EXISTS `related_records` (
  `source` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `matched_on` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `source` (`source`,`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `related_records`
--

TRUNCATE TABLE `related_records`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reminders`
--

DROP TABLE IF EXISTS `reminders`;
CREATE TABLE IF NOT EXISTS `reminders` (
  `urn` int(11) NOT NULL,
  `ignore` int(1) NOT NULL DEFAULT '0',
  `snooze` int(1) NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `reminders`
--

TRUNCATE TABLE `reminders`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_outcomes`
--

DROP TABLE IF EXISTS `role_outcomes`;
CREATE TABLE IF NOT EXISTS `role_outcomes` (
  `role_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `role_id_2` (`role_id`,`outcome_id`,`campaign_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_id` (`outcome_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `role_outcomes`
--

TRUNCATE TABLE `role_outcomes`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `role_permissions`
--

TRUNCATE TABLE `role_permissions`;
--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 34),
(1, 35),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 52),
(1, 56),
(1, 57),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 66),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 79),
(1, 80),
(1, 81),
(1, 82),
(1, 83),
(1, 84),
(1, 86),
(1, 90),
(1, 93),
(1, 94),
(1, 98),
(1, 99),
(1, 102),
(1, 103),
(1, 104),
(1, 108),
(1, 110),
(1, 111),
(1, 112),
(1, 113),
(1, 114),
(1, 115),
(1, 116),
(1, 117),
(1, 118),
(1, 119),
(1, 120),
(1, 121),
(1, 122),
(1, 123),
(1, 124),
(1, 125),
(1, 126),
(1, 127),
(1, 130),
(1, 134),
(1, 135),
(1, 139),
(1, 142),
(1, 143),
(1, 144),
(1, 145),
(1, 149),
(1, 150),
(1, 151),
(1, 152),
(1, 153),
(1, 154),
(1, 155),
(1, 156),
(1, 157),
(1, 158),
(1, 159),
(1, 161),
(1, 163),
(1, 164),
(2, 1),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 32),
(2, 34),
(2, 35),
(2, 38),
(2, 39),
(2, 40),
(2, 41),
(2, 43),
(2, 44),
(2, 47),
(2, 48),
(2, 49),
(2, 52),
(2, 60),
(2, 61),
(2, 63),
(2, 67),
(2, 69),
(2, 70),
(2, 71),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 79),
(2, 92),
(2, 99),
(2, 108),
(2, 119),
(2, 120),
(2, 157),
(2, 158),
(2, 161),
(3, 1),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 27),
(3, 28),
(3, 30),
(3, 32),
(3, 35),
(3, 36),
(3, 40),
(3, 41),
(3, 42),
(3, 43),
(3, 45),
(3, 46),
(3, 47),
(3, 48),
(3, 61),
(3, 63),
(3, 64),
(3, 66),
(3, 71),
(3, 72),
(3, 73),
(3, 79),
(3, 85),
(3, 90),
(3, 108),
(3, 157),
(3, 161),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 7),
(4, 8),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 15),
(4, 16),
(4, 17),
(4, 18),
(4, 19),
(4, 21),
(4, 23),
(4, 26),
(4, 27),
(4, 28),
(4, 31),
(4, 45),
(4, 47),
(4, 60),
(4, 63),
(4, 64),
(4, 72),
(4, 73),
(4, 75),
(4, 76),
(4, 79),
(4, 89),
(4, 108),
(4, 157),
(4, 161),
(5, 1),
(5, 3),
(5, 4),
(5, 5),
(5, 7),
(5, 8),
(5, 10),
(5, 11),
(5, 12),
(5, 13),
(5, 15),
(5, 17),
(5, 18),
(5, 19),
(5, 21),
(5, 27),
(5, 28),
(5, 30),
(5, 35),
(5, 36),
(5, 42),
(5, 45),
(5, 46),
(5, 47),
(5, 48),
(5, 60),
(5, 64),
(5, 71),
(5, 72),
(5, 73),
(5, 79),
(5, 85),
(5, 86),
(5, 90),
(5, 99),
(5, 108),
(5, 127),
(5, 157),
(5, 160),
(5, 161),
(6, 2),
(6, 4),
(6, 5),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 14),
(6, 15),
(6, 16),
(6, 17),
(6, 18),
(6, 19),
(6, 20),
(6, 21),
(6, 22),
(6, 23),
(6, 24),
(6, 25),
(6, 26),
(6, 27),
(6, 28),
(6, 29),
(6, 30),
(6, 31),
(6, 32),
(6, 34),
(6, 35),
(6, 37),
(6, 38),
(6, 39),
(6, 40),
(6, 41),
(6, 43),
(6, 44),
(6, 45),
(6, 46),
(6, 47),
(6, 48),
(6, 52),
(6, 57),
(6, 59),
(6, 60),
(6, 61),
(6, 62),
(6, 63),
(6, 66),
(6, 69),
(6, 70),
(6, 71),
(6, 72),
(6, 73),
(6, 74),
(6, 75),
(6, 78),
(6, 79),
(6, 89),
(6, 90),
(6, 108),
(6, 157),
(6, 161),
(7, 142),
(11, 108),
(11, 161);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scripts`
--

DROP TABLE IF EXISTS `scripts`;
CREATE TABLE IF NOT EXISTS `scripts` (
  `script_id` int(11) NOT NULL AUTO_INCREMENT,
  `expandable` tinyint(4) DEFAULT NULL,
  `script_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `script` mediumtext CHARACTER SET utf8 NOT NULL,
  `sort` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`script_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `scripts`
--

TRUNCATE TABLE `scripts`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scripts_to_campaigns`
--

DROP TABLE IF EXISTS `scripts_to_campaigns`;
CREATE TABLE IF NOT EXISTS `scripts_to_campaigns` (
  `script_id` tinyint(4) NOT NULL,
  `campaign_id` tinyint(4) NOT NULL,
  UNIQUE KEY `script_id2` (`script_id`,`campaign_id`),
  KEY `script_id` (`script_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `scripts_to_campaigns`
--

TRUNCATE TABLE `scripts_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectors`
--

DROP TABLE IF EXISTS `sectors`;
CREATE TABLE IF NOT EXISTS `sectors` (
  `sector_id` int(11) NOT NULL AUTO_INCREMENT,
  `sector_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `section` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sector_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=34 ;

--
-- Truncar tablas antes de insertar `sectors`
--

TRUNCATE TABLE `sectors`;
--
-- Volcado de datos para la tabla `sectors`
--

INSERT INTO `sectors` (`sector_id`, `sector_name`, `section`) VALUES
(13, ' Agriculture, Forestry and Fishing', 'A'),
(14, ' Mining and Quarrying', 'B'),
(15, ' Manufacturing', 'C'),
(16, ' Electricity, gas, steam and air conditioning supply', 'D'),
(17, ' Water supply, sewerage, waste management and remediation activities', 'E'),
(18, ' Construction', 'F'),
(19, ' Wholesale and retail trade; repair of motor vehicles and motorcycles', 'G'),
(20, ' Transportation and storage', 'H'),
(21, ' Accommodation and food service activities', 'I'),
(22, ' Information and communication', 'J'),
(23, ' Financial and insurance activities', 'K'),
(24, ' Real estate activities', 'L'),
(25, ' Professional, scientific and technical activities', 'M'),
(26, ' Administrative and support service activities', 'N'),
(27, ' Public administration and defence; compulsory social security', 'O'),
(28, ' Education', 'P'),
(29, ' Human health and social work activities', 'Q'),
(30, ' Arts, entertainment and recreation', 'R'),
(31, ' Other service activities', 'S'),
(32, ' Activities of households as employers; undifferentiated goods and services producing activities of households for own use', 'T'),
(33, ' Activities of extraterritorial organisations and bodies', 'U');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_history`
--

DROP TABLE IF EXISTS `sms_history`;
CREATE TABLE IF NOT EXISTS `sms_history` (
  `sms_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` longtext COLLATE utf8_unicode_ci,
  `sender_id` int(11) NOT NULL,
  `send_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `urn` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `text_local_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sms_id`),
  KEY `FK2_user_id` (`user_id`),
  KEY `FK3_record_urn` (`urn`),
  KEY `template_id` (`template_id`),
  KEY `sms_history_statusfk_1` (`status_id`),
  KEY `sms_history_senderfk_1` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `sms_history`
--

TRUNCATE TABLE `sms_history`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_sender`
--

DROP TABLE IF EXISTS `sms_sender`;
CREATE TABLE IF NOT EXISTS `sms_sender` (
  `sender_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `sms_sender`
--

TRUNCATE TABLE `sms_sender`;
--
-- Volcado de datos para la tabla `sms_sender`
--

INSERT INTO `sms_sender` (`sender_id`, `name`) VALUES
(0, 'Automatic'),
(1, 'one2one');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_status`
--

DROP TABLE IF EXISTS `sms_status`;
CREATE TABLE IF NOT EXISTS `sms_status` (
  `sms_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_reason` varchar(100) NOT NULL,
  PRIMARY KEY (`sms_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Truncar tablas antes de insertar `sms_status`
--

TRUNCATE TABLE `sms_status`;
--
-- Volcado de datos para la tabla `sms_status`
--

INSERT INTO `sms_status` (`sms_status_id`, `status_reason`) VALUES
(1, 'PENDING'),
(2, 'SENT'),
(3, 'UNKNOWN'),
(4, 'UNDELIVERED'),
(5, 'ERROR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_templates`
--

DROP TABLE IF EXISTS `sms_templates`;
CREATE TABLE IF NOT EXISTS `sms_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `template_text` longtext COLLATE utf8_unicode_ci,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `template_sender_id` int(11) NOT NULL,
  `custom_sender` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`template_id`),
  KEY `sms_template_senderfk_1` (`template_sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `sms_templates`
--

TRUNCATE TABLE `sms_templates`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_template_to_campaigns`
--

DROP TABLE IF EXISTS `sms_template_to_campaigns`;
CREATE TABLE IF NOT EXISTS `sms_template_to_campaigns` (
  `template_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  UNIQUE KEY `tempcamp` (`template_id`,`campaign_id`),
  KEY `template_id` (`template_id`),
  KEY `campanign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `sms_template_to_campaigns`
--

TRUNCATE TABLE `sms_template_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_triggers`
--

DROP TABLE IF EXISTS `sms_triggers`;
CREATE TABLE IF NOT EXISTS `sms_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `sms_triggers`
--

TRUNCATE TABLE `sms_triggers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_trigger_recipients`
--

DROP TABLE IF EXISTS `sms_trigger_recipients`;
CREATE TABLE IF NOT EXISTS `sms_trigger_recipients` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `sms_trigger_recipients`
--

TRUNCATE TABLE `sms_trigger_recipients`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_unsubscribe`
--

DROP TABLE IF EXISTS `sms_unsubscribe`;
CREATE TABLE IF NOT EXISTS `sms_unsubscribe` (
  `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_address` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(20) NOT NULL,
  PRIMARY KEY (`unsubscribe_id`),
  UNIQUE KEY `sms_address` (`sms_address`,`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `sms_unsubscribe`
--

TRUNCATE TABLE `sms_unsubscribe`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_list`
--

DROP TABLE IF EXISTS `status_list`;
CREATE TABLE IF NOT EXISTS `status_list` (
  `record_status_id` int(1) NOT NULL,
  `status_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`record_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `status_list`
--

TRUNCATE TABLE `status_list`;
--
-- Volcado de datos para la tabla `status_list`
--

INSERT INTO `status_list` (`record_status_id`, `status_name`) VALUES
(1, 'Live'),
(2, 'Parked'),
(3, 'Dead'),
(4, 'Completed');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sticky_notes`
--

DROP TABLE IF EXISTS `sticky_notes`;
CREATE TABLE IF NOT EXISTS `sticky_notes` (
  `urn` int(11) NOT NULL,
  `note` varchar(250) CHARACTER SET utf8 NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` tinyint(4) NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `sticky_notes`
--

TRUNCATE TABLE `sticky_notes`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subsectors`
--

DROP TABLE IF EXISTS `subsectors`;
CREATE TABLE IF NOT EXISTS `subsectors` (
  `subsector_id` int(11) NOT NULL AUTO_INCREMENT,
  `subsector_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `sector_id` int(11) NOT NULL,
  `section` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`subsector_id`),
  KEY `sector_id` (`sector_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=100000 ;

--
-- Truncar tablas antes de insertar `subsectors`
--

TRUNCATE TABLE `subsectors`;
--
-- Volcado de datos para la tabla `subsectors`
--

INSERT INTO `subsectors` (`subsector_id`, `subsector_name`, `sector_id`, `section`) VALUES
(1110, 'Growing of cereals (except rice), leguminous crops and oil seeds', 13, 'A'),
(1120, 'Growing of rice', 13, 'A'),
(1130, 'Growing of vegetables and melons, roots and tubers', 13, 'A'),
(1140, 'Growing of sugar cane', 13, 'A'),
(1150, 'Growing of tobacco', 13, 'A'),
(1160, 'Growing of fibre crops', 13, 'A'),
(1190, 'Growing of other non-perennial crops', 13, 'A'),
(1210, 'Growing of grapes', 13, 'A'),
(1220, 'Growing of tropical and subtropical fruits', 13, 'A'),
(1230, 'Growing of citrus fruits', 13, 'A'),
(1240, 'Growing of pome fruits and stone fruits', 13, 'A'),
(1250, 'Growing of other tree and bush fruits and nuts', 13, 'A'),
(1260, 'Growing of oleaginous fruits', 13, 'A'),
(1270, 'Growing of beverage crops', 13, 'A'),
(1280, 'Growing of spices, aromatic, drug and pharmaceutical crops', 13, 'A'),
(1290, 'Growing of other perennial crops', 13, 'A'),
(1300, 'Plant propagation', 13, 'A'),
(1410, 'Raising of dairy cattle', 13, 'A'),
(1420, 'Raising of other cattle and buffaloes', 13, 'A'),
(1430, 'Raising of horses and other equines', 13, 'A'),
(1440, 'Raising of camels and camelids', 13, 'A'),
(1450, 'Raising of sheep and goats', 13, 'A'),
(1460, 'Raising of swine/pigs', 13, 'A'),
(1470, 'Raising of poultry', 13, 'A'),
(1490, 'Raising of other animals', 13, 'A'),
(1500, 'Mixed farming', 13, 'A'),
(1610, 'Support activities for crop production', 13, 'A'),
(1621, 'Farm animal boarding and care', 13, 'A'),
(1629, 'Support activities for animal production (other than farm animal boarding and care) n.e.c.', 13, 'A'),
(1630, 'Post-harvest crop activities', 13, 'A'),
(1640, 'Seed processing for propagation', 13, 'A'),
(1700, 'Hunting, trapping and related service activities', 13, 'A'),
(2100, 'Silviculture and other forestry activities', 13, 'A'),
(2200, 'Logging', 13, 'A'),
(2300, 'Gathering of wild growing non-wood products', 13, 'A'),
(2400, 'Support services to forestry', 13, 'A'),
(3110, 'Marine fishing', 13, 'A'),
(3120, 'Freshwater fishing', 13, 'A'),
(3210, 'Marine aquaculture', 13, 'A'),
(3220, 'Freshwater aquaculture', 13, 'A'),
(5101, 'Deep coal mines', 14, 'B'),
(5102, 'Open cast coal working', 14, 'B'),
(5200, 'Mining of lignite', 14, 'B'),
(6100, 'Extraction of crude petroleum', 14, 'B'),
(6200, 'Extraction of natural gas', 14, 'B'),
(7100, 'Mining of iron ores', 14, 'B'),
(7210, 'Mining of uranium and thorium ores', 14, 'B'),
(7290, 'Mining of other non-ferrous metal ores', 14, 'B'),
(8110, 'Quarrying of ornamental and building stone, limestone, gypsum, chalk and slate', 14, 'B'),
(8120, 'Operation of gravel and sand pits; mining of clays and kaolin', 14, 'B'),
(8910, 'Mining of chemical and fertilizer minerals', 14, 'B'),
(8920, 'Extraction of peat', 14, 'B'),
(8930, 'Extraction of salt', 14, 'B'),
(8990, 'Other mining and quarrying n.e.c.', 14, 'B'),
(9100, 'Support activities for petroleum and natural gas mining', 14, 'B'),
(9900, 'Support activities for other mining and quarrying', 14, 'B'),
(10110, 'Processing and preserving of meat', 15, 'C'),
(10120, 'Processing and preserving of poultry meat', 15, 'C'),
(10130, 'Production of meat and poultry meat products', 15, 'C'),
(10200, 'Processing and preserving of fish, crustaceans and molluscs', 15, 'C'),
(10310, 'Processing and preserving of potatoes', 15, 'C'),
(10320, 'Manufacture of fruit and vegetable juice', 15, 'C'),
(10390, 'Other processing and preserving of fruit and vegetables', 15, 'C'),
(10410, 'Manufacture of oils and fats', 15, 'C'),
(10420, 'Manufacture of margarine and similar edible fats', 15, 'C'),
(10511, 'Liquid milk and cream production', 15, 'C'),
(10512, 'Butter and cheese production', 15, 'C'),
(10519, 'Manufacture of other milk products', 15, 'C'),
(10520, 'Manufacture of ice cream', 15, 'C'),
(10611, 'Grain milling', 15, 'C'),
(10612, 'Manufacture of breakfast cereals and cereals-based food', 15, 'C'),
(10620, 'Manufacture of starches and starch products', 15, 'C'),
(10710, 'Manufacture of bread; manufacture of fresh pastry goods and cakes', 15, 'C'),
(10720, 'Manufacture of rusks and biscuits; manufacture of preserved pastry goods and cakes', 15, 'C'),
(10730, 'Manufacture of macaroni, noodles, couscous and similar farinaceous products', 15, 'C'),
(10810, 'Manufacture of sugar', 15, 'C'),
(10821, 'Manufacture of cocoa and chocolate confectionery', 15, 'C'),
(10822, 'Manufacture of sugar confectionery', 15, 'C'),
(10831, 'Tea processing', 15, 'C'),
(10832, 'Production of coffee and coffee substitutes', 15, 'C'),
(10840, 'Manufacture of condiments and seasonings', 15, 'C'),
(10850, 'Manufacture of prepared meals and dishes', 15, 'C'),
(10860, 'Manufacture of homogenized food preparations and dietetic food', 15, 'C'),
(10890, 'Manufacture of other food products n.e.c.', 15, 'C'),
(10910, 'Manufacture of prepared feeds for farm animals', 15, 'C'),
(10920, 'Manufacture of prepared pet foods', 15, 'C'),
(11010, 'Distilling, rectifying and blending of spirits', 15, 'C'),
(11020, 'Manufacture of wine from grape', 15, 'C'),
(11030, 'Manufacture of cider and other fruit wines', 15, 'C'),
(11040, 'Manufacture of other non-distilled fermented beverages', 15, 'C'),
(11050, 'Manufacture of beer', 15, 'C'),
(11060, 'Manufacture of malt', 15, 'C'),
(11070, 'Manufacture of soft drinks; production of mineral waters and other bottled waters', 15, 'C'),
(12000, 'Manufacture of tobacco products', 15, 'C'),
(13100, 'Preparation and spinning of textile fibres', 15, 'C'),
(13200, 'Weaving of textiles', 15, 'C'),
(13300, 'Finishing of textiles', 15, 'C'),
(13910, 'Manufacture of knitted and crocheted fabrics', 15, 'C'),
(13921, 'Manufacture of soft furnishings', 15, 'C'),
(13922, 'manufacture of canvas goods, sacks, etc.', 15, 'C'),
(13923, 'manufacture of household textiles', 15, 'C'),
(13931, 'Manufacture of woven or tufted carpets and rugs', 15, 'C'),
(13939, 'Manufacture of other carpets and rugs', 15, 'C'),
(13940, 'Manufacture of cordage, rope, twine and netting', 15, 'C'),
(13950, 'Manufacture of non-wovens and articles made from non-wovens, except apparel', 15, 'C'),
(13960, 'Manufacture of other technical and industrial textiles', 15, 'C'),
(13990, 'Manufacture of other textiles n.e.c.', 15, 'C'),
(14110, 'Manufacture of leather clothes', 15, 'C'),
(14120, 'Manufacture of workwear', 15, 'C'),
(14131, 'Manufacture of other men''s outerwear', 15, 'C'),
(14132, 'Manufacture of other women''s outerwear', 15, 'C'),
(14141, 'Manufacture of men''s underwear', 15, 'C'),
(14142, 'Manufacture of women''s underwear', 15, 'C'),
(14190, 'Manufacture of other wearing apparel and accessories n.e.c.', 15, 'C'),
(14200, 'Manufacture of articles of fur', 15, 'C'),
(14310, 'Manufacture of knitted and crocheted hosiery', 15, 'C'),
(14390, 'Manufacture of other knitted and crocheted apparel', 15, 'C'),
(15110, 'Tanning and dressing of leather; dressing and dyeing of fur', 15, 'C'),
(15120, 'Manufacture of luggage, handbags and the like, saddlery and harness', 15, 'C'),
(15200, 'Manufacture of footwear', 15, 'C'),
(16100, 'Sawmilling and planing of wood', 15, 'C'),
(16210, 'Manufacture of veneer sheets and wood-based panels', 15, 'C'),
(16220, 'Manufacture of assembled parquet floors', 15, 'C'),
(16230, 'Manufacture of other builders'' carpentry and joinery', 15, 'C'),
(16240, 'Manufacture of wooden containers', 15, 'C'),
(16290, 'Manufacture of other products of wood; manufacture of articles of cork, straw and plaiting materials', 15, 'C'),
(17110, 'Manufacture of pulp', 15, 'C'),
(17120, 'Manufacture of paper and paperboard', 15, 'C'),
(17211, 'Manufacture of corrugated paper and paperboard, sacks and bags', 15, 'C'),
(17219, 'Manufacture of other paper and paperboard containers', 15, 'C'),
(17220, 'Manufacture of household and sanitary goods and of toilet requisites', 15, 'C'),
(17230, 'Manufacture of paper stationery', 15, 'C'),
(17240, 'Manufacture of wallpaper', 15, 'C'),
(17290, 'Manufacture of other articles of paper and paperboard n.e.c.', 15, 'C'),
(18110, 'Printing of newspapers', 15, 'C'),
(18121, 'Manufacture of printed labels', 15, 'C'),
(18129, 'Printing n.e.c.', 15, 'C'),
(18130, 'Pre-press and pre-media services', 15, 'C'),
(18140, 'Binding and related services', 15, 'C'),
(18201, 'Reproduction of sound recording', 15, 'C'),
(18202, 'Reproduction of video recording', 15, 'C'),
(18203, 'Reproduction of computer media', 15, 'C'),
(19100, 'Manufacture of coke oven products', 15, 'C'),
(19201, 'Mineral oil refining', 15, 'C'),
(19209, 'Other treatment of petroleum products (excluding petrochemicals manufacture)', 15, 'C'),
(20110, 'Manufacture of industrial gases', 15, 'C'),
(20120, 'Manufacture of dyes and pigments', 15, 'C'),
(20130, 'Manufacture of other inorganic basic chemicals', 15, 'C'),
(20140, 'Manufacture of other organic basic chemicals', 15, 'C'),
(20150, 'Manufacture of fertilizers and nitrogen compounds', 15, 'C'),
(20160, 'Manufacture of plastics in primary forms', 15, 'C'),
(20170, 'Manufacture of synthetic rubber in primary forms', 15, 'C'),
(20200, 'Manufacture of pesticides and other agrochemical products', 15, 'C'),
(20301, 'Manufacture of paints, varnishes and similar coatings, mastics and sealants', 15, 'C'),
(20302, 'Manufacture of printing ink', 15, 'C'),
(20411, 'Manufacture of soap and detergents', 15, 'C'),
(20412, 'Manufacture of cleaning and polishing preparations', 15, 'C'),
(20420, 'Manufacture of perfumes and toilet preparations', 15, 'C'),
(20510, 'Manufacture of explosives', 15, 'C'),
(20520, 'Manufacture of glues', 15, 'C'),
(20530, 'Manufacture of essential oils', 15, 'C'),
(20590, 'Manufacture of other chemical products n.e.c.', 15, 'C'),
(20600, 'Manufacture of man-made fibres', 15, 'C'),
(21100, 'Manufacture of basic pharmaceutical products', 15, 'C'),
(21200, 'Manufacture of pharmaceutical preparations', 15, 'C'),
(22110, 'Manufacture of rubber tyres and tubes; retreading and rebuilding of rubber tyres', 15, 'C'),
(22190, 'Manufacture of other rubber products', 15, 'C'),
(22210, 'Manufacture of plastic plates, sheets, tubes and profiles', 15, 'C'),
(22220, 'Manufacture of plastic packing goods', 15, 'C'),
(22230, 'Manufacture of builders ware of plastic', 15, 'C'),
(22290, 'Manufacture of other plastic products', 15, 'C'),
(23110, 'Manufacture of flat glass', 15, 'C'),
(23120, 'Shaping and processing of flat glass', 15, 'C'),
(23130, 'Manufacture of hollow glass', 15, 'C'),
(23140, 'Manufacture of glass fibres', 15, 'C'),
(23190, 'Manufacture and processing of other glass, including technical glassware', 15, 'C'),
(23200, 'Manufacture of refractory products', 15, 'C'),
(23310, 'Manufacture of ceramic tiles and flags', 15, 'C'),
(23320, 'Manufacture of bricks, tiles and construction products, in baked clay', 15, 'C'),
(23410, 'Manufacture of ceramic household and ornamental articles', 15, 'C'),
(23420, 'Manufacture of ceramic sanitary fixtures', 15, 'C'),
(23430, 'Manufacture of ceramic insulators and insulating fittings', 15, 'C'),
(23440, 'Manufacture of other technical ceramic products', 15, 'C'),
(23490, 'Manufacture of other ceramic products n.e.c.', 15, 'C'),
(23510, 'Manufacture of cement', 15, 'C'),
(23520, 'Manufacture of lime and plaster', 15, 'C'),
(23610, 'Manufacture of concrete products for construction purposes', 15, 'C'),
(23620, 'Manufacture of plaster products for construction purposes', 15, 'C'),
(23630, 'Manufacture of ready-mixed concrete', 15, 'C'),
(23640, 'Manufacture of mortars', 15, 'C'),
(23650, 'Manufacture of fibre cement', 15, 'C'),
(23690, 'Manufacture of other articles of concrete, plaster and cement', 15, 'C'),
(23700, 'Cutting, shaping and finishing of stone', 15, 'C'),
(23910, 'Production of abrasive products', 15, 'C'),
(23990, 'Manufacture of other non-metallic mineral products n.e.c.', 15, 'C'),
(24100, 'Manufacture of basic iron and steel and of ferro-alloys', 15, 'C'),
(24200, 'Manufacture of tubes, pipes, hollow profiles and related fittings, of steel', 15, 'C'),
(24310, 'Cold drawing of bars', 15, 'C'),
(24320, 'Cold rolling of narrow strip', 15, 'C'),
(24330, 'Cold forming or folding', 15, 'C'),
(24340, 'Cold drawing of wire', 15, 'C'),
(24410, 'Precious metals production', 15, 'C'),
(24420, 'Aluminium production', 15, 'C'),
(24430, 'Lead, zinc and tin production', 15, 'C'),
(24440, 'Copper production', 15, 'C'),
(24450, 'Other non-ferrous metal production', 15, 'C'),
(24460, 'Processing of nuclear fuel', 15, 'C'),
(24510, 'Casting of iron', 15, 'C'),
(24520, 'Casting of steel', 15, 'C'),
(24530, 'Casting of light metals', 15, 'C'),
(24540, 'Casting of other non-ferrous metals', 15, 'C'),
(25110, 'Manufacture of metal structures and parts of structures', 15, 'C'),
(25120, 'Manufacture of doors and windows of metal', 15, 'C'),
(25210, 'Manufacture of central heating radiators and boilers', 15, 'C'),
(25290, 'Manufacture of other tanks, reservoirs and containers of metal', 15, 'C'),
(25300, 'Manufacture of steam generators, except central heating hot water boilers', 15, 'C'),
(25400, 'Manufacture of weapons and ammunition', 15, 'C'),
(25500, 'Forging, pressing, stamping and roll-forming of metal; powder metallurgy', 15, 'C'),
(25610, 'Treatment and coating of metals', 15, 'C'),
(25620, 'Machining', 15, 'C'),
(25710, 'Manufacture of cutlery', 15, 'C'),
(25720, 'Manufacture of locks and hinges', 15, 'C'),
(25730, 'Manufacture of tools', 15, 'C'),
(25910, 'Manufacture of steel drums and similar containers', 15, 'C'),
(25920, 'Manufacture of light metal packaging', 15, 'C'),
(25930, 'Manufacture of wire products, chain and springs', 15, 'C'),
(25940, 'Manufacture of fasteners and screw machine products', 15, 'C'),
(25990, 'Manufacture of other fabricated metal products n.e.c.', 15, 'C'),
(26110, 'Manufacture of electronic components', 15, 'C'),
(26120, 'Manufacture of loaded electronic boards', 15, 'C'),
(26200, 'Manufacture of computers and peripheral equipment', 15, 'C'),
(26301, 'Manufacture of telegraph and telephone apparatus and equipment', 15, 'C'),
(26309, 'Manufacture of communication equipment other than telegraph, and telephone apparatus and equipment', 15, 'C'),
(26400, 'Manufacture of consumer electronics', 15, 'C'),
(26511, 'Manufacture of electronic measuring, testing etc. equipment, not for industrial process control', 15, 'C'),
(26512, 'Manufacture of electronic industrial process control equipment', 15, 'C'),
(26513, 'Manufacture of non-electronic measuring, testing etc. equipment, not for industrial process control', 15, 'C'),
(26514, 'Manufacture of non-electronic industrial process control equipment', 15, 'C'),
(26520, 'Manufacture of watches and clocks', 15, 'C'),
(26600, 'Manufacture of irradiation, electromedical and electrotherapeutic equipment', 15, 'C'),
(26701, 'Manufacture of optical precision instruments', 15, 'C'),
(26702, 'Manufacture of photographic and cinematographic equipment', 15, 'C'),
(26800, 'Manufacture of magnetic and optical media', 15, 'C'),
(27110, 'Manufacture of electric motors, generators and transformers', 15, 'C'),
(27120, 'Manufacture of electricity distribution and control apparatus', 15, 'C'),
(27200, 'Manufacture of batteries and accumulators', 15, 'C'),
(27310, 'Manufacture of fibre optic cables', 15, 'C'),
(27320, 'Manufacture of other electronic and electric wires and cables', 15, 'C'),
(27330, 'Manufacture of wiring devices', 15, 'C'),
(27400, 'Manufacture of electric lighting equipment', 15, 'C'),
(27510, 'Manufacture of electric domestic appliances', 15, 'C'),
(27520, 'Manufacture of non-electric domestic appliances', 15, 'C'),
(27900, 'Manufacture of other electrical equipment', 15, 'C'),
(28110, 'Manufacture of engines and turbines, except aircraft, vehicle and cycle engines', 15, 'C'),
(28120, 'Manufacture of fluid power equipment', 15, 'C'),
(28131, 'Manufacture of pumps', 15, 'C'),
(28132, 'Manufacture of compressors', 15, 'C'),
(28140, 'Manufacture of taps and valves', 15, 'C'),
(28150, 'Manufacture of bearings, gears, gearing and driving elements', 15, 'C'),
(28210, 'Manufacture of ovens, furnaces and furnace burners', 15, 'C'),
(28220, 'Manufacture of lifting and handling equipment', 15, 'C'),
(28230, 'Manufacture of office machinery and equipment (except computers and peripheral equipment)', 15, 'C'),
(28240, 'Manufacture of power-driven hand tools', 15, 'C'),
(28250, 'Manufacture of non-domestic cooling and ventilation equipment', 15, 'C'),
(28290, 'Manufacture of other general-purpose machinery n.e.c.', 15, 'C'),
(28301, 'Manufacture of agricultural tractors', 15, 'C'),
(28302, 'Manufacture of agricultural and forestry machinery other than tractors', 15, 'C'),
(28410, 'Manufacture of metal forming machinery', 15, 'C'),
(28490, 'Manufacture of other machine tools', 15, 'C'),
(28910, 'Manufacture of machinery for metallurgy', 15, 'C'),
(28921, 'Manufacture of machinery for mining', 15, 'C'),
(28922, 'Manufacture of earthmoving equipment', 15, 'C'),
(28923, 'Manufacture of equipment for concrete crushing and screening and roadworks', 15, 'C'),
(28930, 'Manufacture of machinery for food, beverage and tobacco processing', 15, 'C'),
(28940, 'Manufacture of machinery for textile, apparel and leather production', 15, 'C'),
(28950, 'Manufacture of machinery for paper and paperboard production', 15, 'C'),
(28960, 'Manufacture of plastics and rubber machinery', 15, 'C'),
(28990, 'Manufacture of other special-purpose machinery n.e.c.', 15, 'C'),
(29100, 'Manufacture of motor vehicles', 15, 'C'),
(29201, 'Manufacture of bodies (coachwork) for motor vehicles (except caravans)', 15, 'C'),
(29202, 'Manufacture of trailers and semi-trailers', 15, 'C'),
(29203, 'Manufacture of caravans', 15, 'C'),
(29310, 'Manufacture of electrical and electronic equipment for motor vehicles and their engines', 15, 'C'),
(29320, 'Manufacture of other parts and accessories for motor vehicles', 15, 'C'),
(30110, 'Building of ships and floating structures', 15, 'C'),
(30120, 'Building of pleasure and sporting boats', 15, 'C'),
(30200, 'Manufacture of railway locomotives and rolling stock', 15, 'C'),
(30300, 'Manufacture of air and spacecraft and related machinery', 15, 'C'),
(30400, 'Manufacture of military fighting vehicles', 15, 'C'),
(30910, 'Manufacture of motorcycles', 15, 'C'),
(30920, 'Manufacture of bicycles and invalid carriages', 15, 'C'),
(30990, 'Manufacture of other transport equipment n.e.c.', 15, 'C'),
(31010, 'Manufacture of office and shop furniture', 15, 'C'),
(31020, 'Manufacture of kitchen furniture', 15, 'C'),
(31030, 'Manufacture of mattresses', 15, 'C'),
(31090, 'Manufacture of other furniture', 15, 'C'),
(32110, 'Striking of coins', 15, 'C'),
(32120, 'Manufacture of jewellery and related articles', 15, 'C'),
(32130, 'Manufacture of imitation jewellery and related articles', 15, 'C'),
(32200, 'Manufacture of musical instruments', 15, 'C'),
(32300, 'Manufacture of sports goods', 15, 'C'),
(32401, 'Manufacture of professional and arcade games and toys', 15, 'C'),
(32409, 'Manufacture of other games and toys, n.e.c.', 15, 'C'),
(32500, 'Manufacture of medical and dental instruments and supplies', 15, 'C'),
(32910, 'Manufacture of brooms and brushes', 15, 'C'),
(32990, 'Other manufacturing n.e.c.', 15, 'C'),
(33110, 'Repair of fabricated metal products', 15, 'C'),
(33120, 'Repair of machinery', 15, 'C'),
(33130, 'Repair of electronic and optical equipment', 15, 'C'),
(33140, 'Repair of electrical equipment', 15, 'C'),
(33150, 'Repair and maintenance of ships and boats', 15, 'C'),
(33160, 'Repair and maintenance of aircraft and spacecraft', 15, 'C'),
(33170, 'Repair and maintenance of other transport equipment n.e.c.', 15, 'C'),
(33190, 'Repair of other equipment', 15, 'C'),
(33200, 'Installation of industrial machinery and equipment', 15, 'C'),
(35110, 'Production of electricity', 16, 'D'),
(35120, 'Transmission of electricity', 16, 'D'),
(35130, 'Distribution of electricity', 16, 'D'),
(35140, 'Trade of electricity', 16, 'D'),
(35210, 'Manufacture of gas', 16, 'D'),
(35220, 'Distribution of gaseous fuels through mains', 16, 'D'),
(35230, 'Trade of gas through mains', 16, 'D'),
(35300, 'Steam and air conditioning supply', 16, 'D'),
(36000, 'Water collection, treatment and supply', 17, 'E'),
(37000, 'Sewerage', 17, 'E'),
(38110, 'Collection of non-hazardous waste', 17, 'E'),
(38120, 'Collection of hazardous waste', 17, 'E'),
(38210, 'Treatment and disposal of non-hazardous waste', 17, 'E'),
(38220, 'Treatment and disposal of hazardous waste', 17, 'E'),
(38310, 'Dismantling of wrecks', 17, 'E'),
(38320, 'Recovery of sorted materials', 17, 'E'),
(39000, 'Remediation activities and other waste management services', 17, 'E'),
(41100, 'Development of building projects', 18, 'F'),
(41201, 'Construction of commercial buildings', 18, 'F'),
(41202, 'Construction of domestic buildings', 18, 'F'),
(42110, 'Construction of roads and motorways', 18, 'F'),
(42120, 'Construction of railways and underground railways', 18, 'F'),
(42130, 'Construction of bridges and tunnels', 18, 'F'),
(42210, 'Construction of utility projects for fluids', 18, 'F'),
(42220, 'Construction of utility projects for electricity and telecommunications', 18, 'F'),
(42910, 'Construction of water projects', 18, 'F'),
(42990, 'Construction of other civil engineering projects n.e.c.', 18, 'F'),
(43110, 'Demolition', 18, 'F'),
(43120, 'Site preparation', 18, 'F'),
(43130, 'Test drilling and boring', 18, 'F'),
(43210, 'Electrical installation', 18, 'F'),
(43220, 'Plumbing, heat and air-conditioning installation', 18, 'F'),
(43290, 'Other construction installation', 18, 'F'),
(43310, 'Plastering', 18, 'F'),
(43320, 'Joinery installation', 18, 'F'),
(43330, 'Floor and wall covering', 18, 'F'),
(43341, 'Painting', 18, 'F'),
(43342, 'Glazing', 18, 'F'),
(43390, 'Other building completion and finishing', 18, 'F'),
(43910, 'Roofing activities', 18, 'F'),
(43991, 'Scaffold erection', 18, 'F'),
(43999, 'Other specialised construction activities n.e.c.', 18, 'F'),
(45111, 'Sale of new cars and light motor vehicles', 19, 'G'),
(45112, 'Sale of used cars and light motor vehicles', 19, 'G'),
(45190, 'Sale of other motor vehicles', 19, 'G'),
(45200, 'Maintenance and repair of motor vehicles', 19, 'G'),
(45310, 'Wholesale trade of motor vehicle parts and accessories', 19, 'G'),
(45320, 'Retail trade of motor vehicle parts and accessories', 19, 'G'),
(45400, 'Sale, maintenance and repair of motorcycles and related parts and accessories', 19, 'G'),
(46110, 'Agents selling agricultural raw materials, livestock, textile raw materials and semi-finished goods', 19, 'G'),
(46120, 'Agents involved in the sale of fuels, ores, metals and industrial chemicals', 19, 'G'),
(46130, 'Agents involved in the sale of timber and building materials', 19, 'G'),
(46140, 'Agents involved in the sale of machinery, industrial equipment, ships and aircraft', 19, 'G'),
(46150, 'Agents involved in the sale of furniture, household goods, hardware and ironmongery', 19, 'G'),
(46160, 'Agents involved in the sale of textiles, clothing, fur, footwear and leather goods', 19, 'G'),
(46170, 'Agents involved in the sale of food, beverages and tobacco', 19, 'G'),
(46180, 'Agents specialised in the sale of other particular products', 19, 'G'),
(46190, 'Agents involved in the sale of a variety of goods', 19, 'G'),
(46210, 'Wholesale of grain, unmanufactured tobacco, seeds and animal feeds', 19, 'G'),
(46220, 'Wholesale of flowers and plants', 19, 'G'),
(46230, 'Wholesale of live animals', 19, 'G'),
(46240, 'Wholesale of hides, skins and leather', 19, 'G'),
(46310, 'Wholesale of fruit and vegetables', 19, 'G'),
(46320, 'Wholesale of meat and meat products', 19, 'G'),
(46330, 'Wholesale of dairy products, eggs and edible oils and fats', 19, 'G'),
(46341, 'Wholesale of fruit and vegetable juices, mineral water and soft drinks', 19, 'G'),
(46342, 'Wholesale of wine, beer, spirits and other alcoholic beverages', 19, 'G'),
(46350, 'Wholesale of tobacco products', 19, 'G'),
(46360, 'Wholesale of sugar and chocolate and sugar confectionery', 19, 'G'),
(46370, 'Wholesale of coffee, tea, cocoa and spices', 19, 'G'),
(46380, 'Wholesale of other food, including fish, crustaceans and molluscs', 19, 'G'),
(46390, 'Non-specialised wholesale of food, beverages and tobacco', 19, 'G'),
(46410, 'Wholesale of textiles', 19, 'G'),
(46420, 'Wholesale of clothing and footwear', 19, 'G'),
(46431, 'Wholesale of audio tapes, records, CDs and video tapes and the equipment on which these are played', 19, 'G'),
(46439, 'Wholesale of radio, television goods &amp; electrical household appliances', 19, 'G'),
(46440, 'Wholesale of china and glassware and cleaning materials', 19, 'G'),
(46450, 'Wholesale of perfume and cosmetics', 19, 'G'),
(46460, 'Wholesale of pharmaceutical goods', 19, 'G'),
(46470, 'Wholesale of furniture, carpets and lighting equipment', 19, 'G'),
(46480, 'Wholesale of watches and jewellery', 19, 'G'),
(46491, 'Wholesale of musical instruments', 19, 'G'),
(46499, 'Wholesale of household goods (other than musical instruments) n.e.c', 19, 'G'),
(46510, 'Wholesale of computers, computer peripheral equipment and software', 19, 'G'),
(46520, 'Wholesale of electronic and telecommunications equipment and parts', 19, 'G'),
(46610, 'Wholesale of agricultural machinery, equipment and supplies', 19, 'G'),
(46620, 'Wholesale of machine tools', 19, 'G'),
(46630, 'Wholesale of mining, construction and civil engineering machinery', 19, 'G'),
(46640, 'Wholesale of machinery for the textile industry and of sewing and knitting machines', 19, 'G'),
(46650, 'Wholesale of office furniture', 19, 'G'),
(46660, 'Wholesale of other office machinery and equipment', 19, 'G'),
(46690, 'Wholesale of other machinery and equipment', 19, 'G'),
(46711, 'Wholesale of petroleum and petroleum products', 19, 'G'),
(46719, 'Wholesale of other fuels and related products', 19, 'G'),
(46720, 'Wholesale of metals and metal ores', 19, 'G'),
(46730, 'Wholesale of wood, construction materials and sanitary equipment', 19, 'G'),
(46740, 'Wholesale of hardware, plumbing and heating equipment and supplies', 19, 'G'),
(46750, 'Wholesale of chemical products', 19, 'G'),
(46760, 'Wholesale of other intermediate products', 19, 'G'),
(46770, 'Wholesale of waste and scrap', 19, 'G'),
(46900, 'Non-specialised wholesale trade', 19, 'G'),
(47110, 'Retail sale in non-specialised stores with food, beverages or tobacco predominating', 19, 'G'),
(47190, 'Other retail sale in non-specialised stores', 19, 'G'),
(47210, 'Retail sale of fruit and vegetables in specialised stores', 19, 'G'),
(47220, 'Retail sale of meat and meat products in specialised stores', 19, 'G'),
(47230, 'Retail sale of fish, crustaceans and molluscs in specialised stores', 19, 'G'),
(47240, 'Retail sale of bread, cakes, flour confectionery and sugar confectionery in specialised stores', 19, 'G'),
(47250, 'Retail sale of beverages in specialised stores', 19, 'G'),
(47260, 'Retail sale of tobacco products in specialised stores', 19, 'G'),
(47290, 'Other retail sale of food in specialised stores', 19, 'G'),
(47300, 'Retail sale of automotive fuel in specialised stores', 19, 'G'),
(47410, 'Retail sale of computers, peripheral units and software in specialised stores', 19, 'G'),
(47421, 'Retail sale of mobile telephones', 19, 'G'),
(47429, 'Retail sale of telecommunications equipment other than mobile telephones', 19, 'G'),
(47430, 'Retail sale of audio and video equipment in specialised stores', 19, 'G'),
(47510, 'Retail sale of textiles in specialised stores', 19, 'G'),
(47520, 'Retail sale of hardware, paints and glass in specialised stores', 19, 'G'),
(47530, 'Retail sale of carpets, rugs, wall and floor coverings in specialised stores', 19, 'G'),
(47540, 'Retail sale of electrical household appliances in specialised stores', 19, 'G'),
(47591, 'Retail sale of musical instruments and scores', 19, 'G'),
(47599, 'Retail of furniture, lighting, and similar (not musical instruments or scores) in specialised store', 19, 'G'),
(47610, 'Retail sale of books in specialised stores', 19, 'G'),
(47620, 'Retail sale of newspapers and stationery in specialised stores', 19, 'G'),
(47630, 'Retail sale of music and video recordings in specialised stores', 19, 'G'),
(47640, 'Retail sale of sports goods, fishing gear, camping goods, boats and bicycles', 19, 'G'),
(47650, 'Retail sale of games and toys in specialised stores', 19, 'G'),
(47710, 'Retail sale of clothing in specialised stores', 19, 'G'),
(47721, 'Retail sale of footwear in specialised stores', 19, 'G'),
(47722, 'Retail sale of leather goods in specialised stores', 19, 'G'),
(47730, 'Dispensing chemist in specialised stores', 19, 'G'),
(47741, 'Retail sale of hearing aids', 19, 'G'),
(47749, 'Retail sale of medical and orthopaedic goods in specialised stores (not incl. hearing aids) n.e.c.', 19, 'G'),
(47750, 'Retail sale of cosmetic and toilet articles in specialised stores', 19, 'G'),
(47760, 'Retail sale of flowers, plants, seeds, fertilizers, pet animals and pet food in specialised stores', 19, 'G'),
(47770, 'Retail sale of watches and jewellery in specialised stores', 19, 'G'),
(47781, 'Retail sale in commercial art galleries', 19, 'G'),
(47782, 'Retail sale by opticians', 19, 'G'),
(47789, 'Other retail sale of new goods in specialised stores (not commercial art galleries and opticians)', 19, 'G'),
(47791, 'Retail sale of antiques including antique books in stores', 19, 'G'),
(47799, 'Retail sale of other second-hand goods in stores (not incl. antiques)', 19, 'G'),
(47810, 'Retail sale via stalls and markets of food, beverages and tobacco products', 19, 'G'),
(47820, 'Retail sale via stalls and markets of textiles, clothing and footwear', 19, 'G'),
(47890, 'Retail sale via stalls and markets of other goods', 19, 'G'),
(47910, 'Retail sale via mail order houses or via Internet', 19, 'G'),
(47990, 'Other retail sale not in stores, stalls or markets', 19, 'G'),
(49100, 'Passenger rail transport, interurban', 20, 'H'),
(49200, 'Freight rail transport', 20, 'H'),
(49311, 'Urban and suburban passenger railway transportation by underground, metro and similar systems', 20, 'H'),
(49319, 'Other urban, suburban or metropolitan passenger land transport (not underground, metro or similar)', 20, 'H'),
(49320, 'Taxi operation', 20, 'H'),
(49390, 'Other passenger land transport', 20, 'H'),
(49410, 'Freight transport by road', 20, 'H'),
(49420, 'Removal services', 20, 'H'),
(49500, 'Transport via pipeline', 20, 'H'),
(50100, 'Sea and coastal passenger water transport', 20, 'H'),
(50200, 'Sea and coastal freight water transport', 20, 'H'),
(50300, 'Inland passenger water transport', 20, 'H'),
(50400, 'Inland freight water transport', 20, 'H'),
(51101, 'Scheduled passenger air transport', 20, 'H'),
(51102, 'Non-scheduled passenger air transport', 20, 'H'),
(51210, 'Freight air transport', 20, 'H'),
(51220, 'Space transport', 20, 'H'),
(52101, 'Operation of warehousing and storage facilities for water transport activities', 20, 'H'),
(52102, 'Operation of warehousing and storage facilities for air transport activities', 20, 'H'),
(52103, 'Operation of warehousing and storage facilities for land transport activities', 20, 'H'),
(52211, 'Operation of rail freight terminals', 20, 'H'),
(52212, 'Operation of rail passenger facilities at railway stations', 20, 'H'),
(52213, 'Operation of bus and coach passenger facilities at bus and coach stations', 20, 'H'),
(52219, 'Other service activities incidental to land transportation, n.e.c.', 20, 'H'),
(52220, 'Service activities incidental to water transportation', 20, 'H'),
(52230, 'Service activities incidental to air transportation', 20, 'H'),
(52241, 'Cargo handling for water transport activities', 20, 'H'),
(52242, 'Cargo handling for air transport activities', 20, 'H'),
(52243, 'Cargo handling for land transport activities', 20, 'H'),
(52290, 'Other transportation support activities', 20, 'H'),
(53100, 'Postal activities under universal service obligation', 20, 'H'),
(53201, 'Licensed carriers', 20, 'H'),
(53202, 'Unlicensed carriers', 20, 'H'),
(55100, 'Hotels and similar accommodation', 21, 'I'),
(55201, 'Holiday centres and villages', 21, 'I'),
(55202, 'Youth hostels', 21, 'I'),
(55209, 'Other holiday and other collective accommodation', 21, 'I'),
(55300, 'Recreational vehicle parks, trailer parks and camping grounds', 21, 'I'),
(55900, 'Other accommodation', 21, 'I'),
(56101, 'Licenced restaurants', 21, 'I'),
(56102, 'Unlicenced restaurants and cafes', 21, 'I'),
(56103, 'Take-away food shops and mobile food stands', 21, 'I'),
(56210, 'Event catering activities', 21, 'I'),
(56290, 'Other food services', 21, 'I'),
(56301, 'Licenced clubs', 21, 'I'),
(56302, 'Public houses and bars', 21, 'I'),
(58110, 'Book publishing', 22, 'J'),
(58120, 'Publishing of directories and mailing lists', 22, 'J'),
(58130, 'Publishing of newspapers', 22, 'J'),
(58141, 'Publishing of learned journals', 22, 'J'),
(58142, 'Publishing of consumer and business journals and periodicals', 22, 'J'),
(58190, 'Other publishing activities', 22, 'J'),
(58210, 'Publishing of computer games', 22, 'J'),
(58290, 'Other software publishing', 22, 'J'),
(59111, 'Motion picture production activities', 22, 'J'),
(59112, 'Video production activities', 22, 'J'),
(59113, 'Television programme production activities', 22, 'J'),
(59120, 'Motion picture, video and television programme post-production activities', 22, 'J'),
(59131, 'Motion picture distribution activities', 22, 'J'),
(59132, 'Video distribution activities', 22, 'J'),
(59133, 'Television programme distribution activities', 22, 'J'),
(59140, 'Motion picture projection activities', 22, 'J'),
(59200, 'Sound recording and music publishing activities', 22, 'J'),
(60100, 'Radio broadcasting', 22, 'J'),
(60200, 'Television programming and broadcasting activities', 22, 'J'),
(61100, 'Wired telecommunications activities', 22, 'J'),
(61200, 'Wireless telecommunications activities', 22, 'J'),
(61300, 'Satellite telecommunications activities', 22, 'J'),
(61900, 'Other telecommunications activities', 22, 'J'),
(62011, 'Ready-made interactive leisure and entertainment software development', 22, 'J'),
(62012, 'Business and domestic software development', 22, 'J'),
(62020, 'Information technology consultancy activities', 22, 'J'),
(62030, 'Computer facilities management activities', 22, 'J'),
(62090, 'Other information technology service activities', 22, 'J'),
(63110, 'Data processing, hosting and related activities', 22, 'J'),
(63120, 'Web portals', 22, 'J'),
(63910, 'News agency activities', 22, 'J'),
(63990, 'Other information service activities n.e.c.', 22, 'J'),
(64110, 'Central banking', 23, 'K'),
(64191, 'Banks', 23, 'K'),
(64192, 'Building societies', 23, 'K'),
(64201, 'Activities of agricultural holding companies', 23, 'K'),
(64202, 'Activities of production holding companies', 23, 'K'),
(64203, 'Activities of construction holding companies', 23, 'K'),
(64204, 'Activities of distribution holding companies', 23, 'K'),
(64205, 'Activities of financial services holding companies', 23, 'K'),
(64209, 'Activities of other holding companies n.e.c.', 23, 'K'),
(64301, 'Activities of investment trusts', 23, 'K'),
(64302, 'Activities of unit trusts', 23, 'K'),
(64303, 'Activities of venture and development capital companies', 23, 'K'),
(64304, 'Activities of open-ended investment companies', 23, 'K'),
(64305, 'Activities of property unit trusts', 23, 'K'),
(64306, 'Activities of real estate investment trusts', 23, 'K'),
(64910, 'Financial leasing', 23, 'K'),
(64921, 'Credit granting by non-deposit taking finance houses and other specialist consumer credit grantors', 23, 'K'),
(64922, 'Activities of mortgage finance companies', 23, 'K'),
(64929, 'Other credit granting n.e.c.', 23, 'K'),
(64991, 'Security dealing on own account', 23, 'K'),
(64992, 'Factoring', 23, 'K'),
(64999, 'Financial intermediation not elsewhere classified', 23, 'K'),
(65110, 'Life insurance', 23, 'K'),
(65120, 'Non-life insurance', 23, 'K'),
(65201, 'Life reinsurance', 23, 'K'),
(65202, 'Non-life reinsurance', 23, 'K'),
(65300, 'Pension funding', 23, 'K'),
(66110, 'Administration of financial markets', 23, 'K'),
(66120, 'Security and commodity contracts dealing activities', 23, 'K'),
(66190, 'Activities auxiliary to financial intermediation n.e.c.', 23, 'K'),
(66210, 'Risk and damage evaluation', 23, 'K'),
(66220, 'Activities of insurance agents and brokers', 23, 'K'),
(66290, 'Other activities auxiliary to insurance and pension funding', 23, 'K'),
(66300, 'Fund management activities', 23, 'K'),
(68100, 'Buying and selling of own real estate', 24, 'L'),
(68201, 'Renting and operating of Housing Association real estate', 24, 'L'),
(68202, 'Letting and operating of conference and exhibition centres', 24, 'L'),
(68209, 'Other letting and operating of own or leased real estate', 24, 'L'),
(68310, 'Real estate agencies', 24, 'L'),
(68320, 'Management of real estate on a fee or contract basis', 24, 'L'),
(69101, 'Barristers at law', 25, 'M'),
(69102, 'Solicitors', 25, 'M'),
(69109, 'Activities of patent and copyright agents; other legal activities n.e.c.', 25, 'M'),
(69201, 'Accounting and auditing activities', 25, 'M'),
(69202, 'Bookkeeping activities', 25, 'M'),
(69203, 'Tax consultancy', 25, 'M'),
(70100, 'Activities of head offices', 25, 'M'),
(70210, 'Public relations and communications activities', 25, 'M'),
(70221, 'Financial management', 25, 'M'),
(70229, 'Management consultancy activities other than financial management', 25, 'M'),
(71111, 'Architectural activities', 25, 'M'),
(71112, 'Urban planning and landscape architectural activities', 25, 'M'),
(71121, 'Engineering design activities for industrial process and production', 25, 'M'),
(71122, 'Engineering related scientific and technical consulting activities', 25, 'M'),
(71129, 'Other engineering activities', 25, 'M'),
(71200, 'Technical testing and analysis', 25, 'M'),
(72110, 'Research and experimental development on biotechnology', 25, 'M'),
(72190, 'Other research and experimental development on natural sciences and engineering', 25, 'M'),
(72200, 'Research and experimental development on social sciences and humanities', 25, 'M'),
(73110, 'Advertising agencies', 25, 'M'),
(73120, 'Media representation services', 25, 'M'),
(73200, 'Market research and public opinion polling', 25, 'M'),
(74100, 'specialised design activities', 25, 'M'),
(74201, 'Portrait photographic activities', 25, 'M'),
(74202, 'Other specialist photography', 25, 'M'),
(74203, 'Film processing', 25, 'M'),
(74209, 'Photographic activities not elsewhere classified', 25, 'M'),
(74300, 'Translation and interpretation activities', 25, 'M'),
(74901, 'Environmental consulting activities', 25, 'M'),
(74902, 'Quantity surveying activities', 25, 'M'),
(74909, 'Other professional, scientific and technical activities n.e.c.', 25, 'M'),
(74990, 'Non-trading company', 25, 'M'),
(75000, 'Veterinary activities', 25, 'M'),
(77110, 'Renting and leasing of cars and light motor vehicles', 26, 'N'),
(77120, 'Renting and leasing of trucks and other heavy vehicles', 26, 'N'),
(77210, 'Renting and leasing of recreational and sports goods', 26, 'N'),
(77220, 'Renting of video tapes and disks', 26, 'N'),
(77291, 'Renting and leasing of media entertainment equipment', 26, 'N'),
(77299, 'Renting and leasing of other personal and household goods', 26, 'N'),
(77310, 'Renting and leasing of agricultural machinery and equipment', 26, 'N'),
(77320, 'Renting and leasing of construction and civil engineering machinery and equipment', 26, 'N'),
(77330, 'Renting and leasing of office machinery and equipment (including computers)', 26, 'N'),
(77341, 'Renting and leasing of passenger water transport equipment', 26, 'N'),
(77342, 'Renting and leasing of freight water transport equipment', 26, 'N'),
(77351, 'Renting and leasing of air passenger transport equipment', 26, 'N'),
(77352, 'Renting and leasing of freight air transport equipment', 26, 'N'),
(77390, 'Renting and leasing of other machinery, equipment and tangible goods n.e.c.', 26, 'N'),
(77400, 'Leasing of intellectual property and similar products, except copyright works', 26, 'N'),
(78101, 'Motion picture, television and other theatrical casting activities', 26, 'N'),
(78109, 'Other activities of employment placement agencies', 26, 'N'),
(78200, 'Temporary employment agency activities', 26, 'N'),
(78300, 'Human resources provision and management of human resources functions', 26, 'N'),
(79110, 'Travel agency activities', 26, 'N'),
(79120, 'Tour operator activities', 26, 'N'),
(79901, 'Activities of tourist guides', 26, 'N'),
(79909, 'Other reservation service activities n.e.c.', 26, 'N'),
(80100, 'Private security activities', 26, 'N'),
(80200, 'Security systems service activities', 26, 'N'),
(80300, 'Investigation activities', 26, 'N'),
(81100, 'Combined facilities support activities', 26, 'N'),
(81210, 'General cleaning of buildings', 26, 'N'),
(81221, 'Window cleaning services', 26, 'N'),
(81222, 'Specialised cleaning services', 26, 'N'),
(81223, 'Furnace and chimney cleaning services', 26, 'N'),
(81229, 'Other building and industrial cleaning activities', 26, 'N'),
(81291, 'Disinfecting and exterminating services', 26, 'N'),
(81299, 'Other cleaning services', 26, 'N'),
(81300, 'Landscape service activities', 26, 'N'),
(82110, 'Combined office administrative service activities', 26, 'N'),
(82190, 'Photocopying, document preparation and other specialised office support activities', 26, 'N'),
(82200, 'Activities of call centres', 26, 'N'),
(82301, 'Activities of exhibition and fair organisers', 26, 'N'),
(82302, 'Activities of conference organisers', 26, 'N'),
(82911, 'Activities of collection agencies', 26, 'N'),
(82912, 'Activities of credit bureaus', 26, 'N'),
(82920, 'Packaging activities', 26, 'N'),
(82990, 'Other business support service activities n.e.c.', 26, 'N'),
(84110, 'General public administration activities', 27, 'O'),
(84120, 'Regulation of health care, education, cultural and other social services, not incl. social security', 27, 'O'),
(84130, 'Regulation of and contribution to more efficient operation of businesses', 27, 'O'),
(84210, 'Foreign affairs', 27, 'O'),
(84220, 'Defence activities', 27, 'O'),
(84230, 'Justice and judicial activities', 27, 'O'),
(84240, 'Public order and safety activities', 27, 'O'),
(84250, 'Fire service activities', 27, 'O'),
(84300, 'Compulsory social security activities', 27, 'O'),
(85100, 'Pre-primary education', 28, 'P'),
(85200, 'Primary education', 28, 'P'),
(85310, 'General secondary education', 28, 'P'),
(85320, 'Technical and vocational secondary education', 28, 'P'),
(85410, 'Post-secondary non-tertiary education', 28, 'P'),
(85421, 'First-degree level higher education', 28, 'P'),
(85422, 'Post-graduate level higher education', 28, 'P'),
(85510, 'Sports and recreation education', 28, 'P'),
(85520, 'Cultural education', 28, 'P'),
(85530, 'Driving school activities', 28, 'P'),
(85590, 'Other education n.e.c.', 28, 'P'),
(85600, 'Educational support services', 28, 'P'),
(86101, 'Hospital activities', 29, 'Q'),
(86102, 'Medical nursing home activities', 29, 'Q'),
(86210, 'General medical practice activities', 29, 'Q'),
(86220, 'Specialists medical practice activities', 29, 'Q'),
(86230, 'Dental practice activities', 29, 'Q'),
(86900, 'Other human health activities', 29, 'Q'),
(87100, 'Residential nursing care facilities', 29, 'Q'),
(87200, 'Residential care activities for learning difficulties, mental health and substance abuse', 29, 'Q'),
(87300, 'Residential care activities for the elderly and disabled', 29, 'Q'),
(87900, 'Other residential care activities n.e.c.', 29, 'Q'),
(88100, 'Social work activities without accommodation for the elderly and disabled', 29, 'Q'),
(88910, 'Child day-care activities', 29, 'Q'),
(88990, 'Other social work activities without accommodation n.e.c.', 29, 'Q'),
(90010, 'Performing arts', 30, 'R'),
(90020, 'Support activities to performing arts', 30, 'R'),
(90030, 'Artistic creation', 30, 'R'),
(90040, 'Operation of arts facilities', 30, 'R'),
(91011, 'Library activities', 30, 'R'),
(91012, 'Archives activities', 30, 'R'),
(91020, 'Museums activities', 30, 'R'),
(91030, 'Operation of historical sites and buildings and similar visitor attractions', 30, 'R'),
(91040, 'Botanical and zoological gardens and nature reserves activities', 30, 'R'),
(92000, 'Gambling and betting activities', 30, 'R'),
(93110, 'Operation of sports facilities', 30, 'R'),
(93120, 'Activities of sport clubs', 30, 'R'),
(93130, 'Fitness facilities', 30, 'R'),
(93191, 'Activities of racehorse owners', 30, 'R'),
(93199, 'Other sports activities', 30, 'R'),
(93210, 'Activities of amusement parks and theme parks', 30, 'R'),
(93290, 'Other amusement and recreation activities n.e.c.', 30, 'R'),
(94110, 'Activities of business and employers membership organisations', 31, 'S'),
(94120, 'Activities of professional membership organisations', 31, 'S'),
(94200, 'Activities of trade unions', 31, 'S'),
(94910, 'Activities of religious organisations', 31, 'S'),
(94920, 'Activities of political organisations', 31, 'S'),
(94990, 'Activities of other membership organisations n.e.c.', 31, 'S'),
(95110, 'Repair of computers and peripheral equipment', 31, 'S'),
(95120, 'Repair of communication equipment', 31, 'S'),
(95210, 'Repair of consumer electronics', 31, 'S'),
(95220, 'Repair of household appliances and home and garden equipment', 31, 'S'),
(95230, 'Repair of footwear and leather goods', 31, 'S'),
(95240, 'Repair of furniture and home furnishings', 31, 'S'),
(95250, 'Repair of watches, clocks and jewellery', 31, 'S'),
(95290, 'Repair of personal and household goods n.e.c.', 31, 'S'),
(96010, 'Washing and (dry-)cleaning of textile and fur products', 31, 'S'),
(96020, 'Hairdressing and other beauty treatment', 31, 'S'),
(96030, 'Funeral and related activities', 31, 'S'),
(96040, 'Physical well-being activities', 31, 'S'),
(96090, 'Other service activities n.e.c.', 31, 'S'),
(97000, 'Activities of households as employers of domestic personnel', 32, 'T'),
(98000, ' Residents property management', 32, 'T'),
(98100, 'Undifferentiated goods-producing activities of private households for own use', 32, 'T'),
(98200, 'Undifferentiated service-producing activities of private households for own use', 32, 'T'),
(99000, 'Activities of extraterritorial organisations and bodies', 33, 'U'),
(99999, 'Dormant Company', 33, 'U');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppression`
--

DROP TABLE IF EXISTS `suppression`;
CREATE TABLE IF NOT EXISTS `suppression` (
  `suppression_id` int(11) NOT NULL AUTO_INCREMENT,
  `telephone_number` varchar(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL,
  `reason` text,
  PRIMARY KEY (`suppression_id`),
  KEY `telephone_number` (`telephone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `suppression`
--

TRUNCATE TABLE `suppression`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppression_by_campaign`
--

DROP TABLE IF EXISTS `suppression_by_campaign`;
CREATE TABLE IF NOT EXISTS `suppression_by_campaign` (
  `suppression_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  PRIMARY KEY (`suppression_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `suppression_by_campaign`
--

TRUNCATE TABLE `suppression_by_campaign`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `surveys`
--

DROP TABLE IF EXISTS `surveys`;
CREATE TABLE IF NOT EXISTS `surveys` (
  `survey_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `survey_updated` datetime DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `completed` int(1) NOT NULL DEFAULT '0',
  `contact_id` int(9) DEFAULT NULL,
  `user_id` int(5) NOT NULL,
  `survey_info_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`survey_id`),
  UNIQUE KEY `urn` (`urn`,`date_created`),
  UNIQUE KEY `urn_2` (`urn`,`completed_date`,`contact_id`),
  KEY `urn_3` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `surveys`
--

TRUNCATE TABLE `surveys`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `surveys_to_campaigns`
--

DROP TABLE IF EXISTS `surveys_to_campaigns`;
CREATE TABLE IF NOT EXISTS `surveys_to_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_info_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `default` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_info_id` (`survey_info_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `surveys_to_campaigns`
--

TRUNCATE TABLE `surveys_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `surveys_to_questions`
--

DROP TABLE IF EXISTS `surveys_to_questions`;
CREATE TABLE IF NOT EXISTS `surveys_to_questions` (
  `question_id` tinyint(1) NOT NULL,
  `survey_info_id` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `surveys_to_questions`
--

TRUNCATE TABLE `surveys_to_questions`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `survey_answers`
--

DROP TABLE IF EXISTS `survey_answers`;
CREATE TABLE IF NOT EXISTS `survey_answers` (
  `answer_id` int(5) NOT NULL AUTO_INCREMENT,
  `survey_id` int(5) DEFAULT NULL,
  `question_id` int(5) DEFAULT NULL,
  `answer` int(11) DEFAULT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `survey_answers`
--

TRUNCATE TABLE `survey_answers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `survey_info`
--

DROP TABLE IF EXISTS `survey_info`;
CREATE TABLE IF NOT EXISTS `survey_info` (
  `survey_info_id` int(11) NOT NULL,
  `survey_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `survey_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`survey_info_id`),
  UNIQUE KEY `survey_ref` (`survey_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `survey_info`
--

TRUNCATE TABLE `survey_info`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(250) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `tasks`
--

TRUNCATE TABLE `tasks`;
--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`task_id`, `task_name`) VALUES
(1, 'Complaint'),
(2, 'Query'),
(3, 'Review');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks_to_options`
--

DROP TABLE IF EXISTS `tasks_to_options`;
CREATE TABLE IF NOT EXISTS `tasks_to_options` (
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`task_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `tasks_to_options`
--

TRUNCATE TABLE `tasks_to_options`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_history`
--

DROP TABLE IF EXISTS `task_history`;
CREATE TABLE IF NOT EXISTS `task_history` (
  `task_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`task_history_id`),
  KEY `task_id` (`task_id`,`task_status_id`,`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `task_history`
--

TRUNCATE TABLE `task_history`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_status`
--

DROP TABLE IF EXISTS `task_status`;
CREATE TABLE IF NOT EXISTS `task_status` (
  `task_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_status` varchar(100) NOT NULL,
  PRIMARY KEY (`task_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `task_status`
--

TRUNCATE TABLE `task_status`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_status_options`
--

DROP TABLE IF EXISTS `task_status_options`;
CREATE TABLE IF NOT EXISTS `task_status_options` (
  `task_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_status` varchar(100) NOT NULL,
  PRIMARY KEY (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `task_status_options`
--

TRUNCATE TABLE `task_status_options`;
--
-- Volcado de datos para la tabla `task_status_options`
--

INSERT INTO `task_status_options` (`task_status_id`, `task_status`) VALUES
(1, 'n/a'),
(2, 'Pending'),
(3, 'Complete');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

DROP TABLE IF EXISTS `teams`;
CREATE TABLE IF NOT EXISTS `teams` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`team_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `teams`
--

TRUNCATE TABLE `teams`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team_managers`
--

DROP TABLE IF EXISTS `team_managers`;
CREATE TABLE IF NOT EXISTS `team_managers` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `team_id_2` (`team_id`,`user_id`),
  KEY `team_id` (`team_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncar tablas antes de insertar `team_managers`
--

TRUNCATE TABLE `team_managers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `time`
--

DROP TABLE IF EXISTS `time`;
CREATE TABLE IF NOT EXISTS `time` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL COMMENT 'The Agent',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '230:59:59',
  `date` datetime NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `updated_id` int(5) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`time_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Agent time by campaign and day' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `time`
--

TRUNCATE TABLE `time`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `time_exception`
--

DROP TABLE IF EXISTS `time_exception`;
CREATE TABLE IF NOT EXISTS `time_exception` (
  `exception_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_id` int(5) NOT NULL,
  `exception_type_id` int(5) NOT NULL,
  `duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
  PRIMARY KEY (`exception_id`),
  KEY `time_id` (`time_id`),
  KEY `exception_type_id` (`exception_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Exception time' AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `time_exception`
--

TRUNCATE TABLE `time_exception`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `time_exception_type`
--

DROP TABLE IF EXISTS `time_exception_type`;
CREATE TABLE IF NOT EXISTS `time_exception_type` (
  `exception_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `exception_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `paid` tinyint(1) NOT NULL,
  PRIMARY KEY (`exception_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Exception types' AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `time_exception_type`
--

TRUNCATE TABLE `time_exception_type`;
--
-- Volcado de datos para la tabla `time_exception_type`
--

INSERT INTO `time_exception_type` (`exception_type_id`, `exception_name`, `paid`) VALUES
(1, 'Lunch', 0),
(2, 'Break', 1),
(3, 'Training', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tps`
--

DROP TABLE IF EXISTS `tps`;
CREATE TABLE IF NOT EXISTS `tps` (
  `telephone` varchar(20) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tps` tinyint(4) NOT NULL DEFAULT '0',
  `ctps` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`telephone`),
  UNIQUE KEY `telephone` (`telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `tps`
--

TRUNCATE TABLE `tps`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(2) NOT NULL AUTO_INCREMENT,
  `role_id` int(2) NOT NULL,
  `group_id` int(2) NOT NULL DEFAULT '1',
  `team_id` int(11) DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `user_status` tinyint(1) DEFAULT NULL,
  `login_mode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `user_telephone` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `user_email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `phone_un` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_pw` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ext` int(3) DEFAULT NULL,
  `token` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pass_changed` datetime DEFAULT NULL,
  `failed_logins` tinyint(4) NOT NULL,
  `last_failed_login` datetime DEFAULT NULL,
  `reload_session` tinyint(1) NOT NULL DEFAULT '0',
  `attendee` tinyint(1) NOT NULL DEFAULT '0',
  `reset_pass_token` text COLLATE utf8_unicode_ci,
  `home_postcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `vehicle_reg` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `custom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ics` tinyint(4) NOT NULL DEFAULT '0',
  `theme_color` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `calendar` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `phone_un` (`phone_un`),
  KEY `group_id` (`role_id`),
  KEY `repgroup_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Truncar tablas antes de insertar `users`
--

TRUNCATE TABLE `users`;
--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `phone_un`, `phone_pw`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`, `reload_session`, `attendee`, `reset_pass_token`, `home_postcode`, `vehicle_reg`, `custom`, `ics`, `theme_color`, `calendar`) VALUES
(1, 1, 1, NULL, 'admin', '16a4ee2c9bfa8a8fd5a5062f132741ea', 'Administrator', 1, NULL, NULL, 'mark.bennett@121customerinsight.co.uk', '2016-02-09 11:11:43', NULL, NULL, NULL, NULL, NULL, 0, '2016-01-18 08:36:46', 0, 0, NULL, 'OL15 0HH', '', '', 0, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_to_campaigns`
--

DROP TABLE IF EXISTS `users_to_campaigns`;
CREATE TABLE IF NOT EXISTS `users_to_campaigns` (
  `user_id` int(5) NOT NULL,
  `campaign_id` int(5) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`campaign_id`),
  KEY `user_id_2` (`user_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncar tablas antes de insertar `users_to_campaigns`
--

TRUNCATE TABLE `users_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_address`
--

DROP TABLE IF EXISTS `user_address`;
CREATE TABLE IF NOT EXISTS `user_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `add1` varchar(100) DEFAULT NULL,
  `add2` varchar(100) DEFAULT NULL,
  `add3` varchar(100) DEFAULT NULL,
  `add4` varchar(100) DEFAULT NULL,
  `locality` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postcode` varchar(11) DEFAULT NULL,
  `county` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `primary` int(11) DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `user_address`
--

TRUNCATE TABLE `user_address`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
  `group_id` int(3) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `theme_images` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `theme_color` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Truncar tablas antes de insertar `user_groups`
--

TRUNCATE TABLE `user_groups`;
--
-- Volcado de datos para la tabla `user_groups`
--

INSERT INTO `user_groups` (`group_id`, `group_name`, `theme_images`, `theme_color`) VALUES
(1, '121', 'smartprospector', 'smartprospector');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` int(3) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `landing_page` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'dashboard',
  `timeout` int(11) DEFAULT '120',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Truncar tablas antes de insertar `user_roles`
--

TRUNCATE TABLE `user_roles`;
--
-- Volcado de datos para la tabla `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `landing_page`, `timeout`) VALUES
(1, 'Administrator', 'dashboard', 120),
(2, 'Team Leader', 'dashboard', 120),
(3, 'Team Senior', 'dashboard', 120),
(4, 'Client', 'dashboard', 120),
(5, 'Agent', 'dashboard', 120),
(6, 'Client Services', 'dashboard', 120),
(11, 'demo', 'dashboard', 120);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `webforms`
--

DROP TABLE IF EXISTS `webforms`;
CREATE TABLE IF NOT EXISTS `webforms` (
  `webform_id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_path` varchar(100) DEFAULT NULL,
  `webform_name` varchar(100) DEFAULT NULL,
  `btn_text` varchar(50) NOT NULL,
  `appointment_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`webform_id`),
  KEY `appointment_type_id` (`appointment_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Truncar tablas antes de insertar `webforms`
--

TRUNCATE TABLE `webforms`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `webforms_to_campaigns`
--

DROP TABLE IF EXISTS `webforms_to_campaigns`;
CREATE TABLE IF NOT EXISTS `webforms_to_campaigns` (
  `webform_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `webform_id_2` (`webform_id`,`campaign_id`),
  KEY `webform_id` (`webform_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `webforms_to_campaigns`
--

TRUNCATE TABLE `webforms_to_campaigns`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `webform_answers`
--

DROP TABLE IF EXISTS `webform_answers`;
CREATE TABLE IF NOT EXISTS `webform_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `completed_on` datetime DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL,
  `a1` varchar(255) NOT NULL,
  `a2` varchar(255) NOT NULL,
  `a3` varchar(255) NOT NULL,
  `a4` varchar(255) NOT NULL,
  `a5` varchar(255) NOT NULL,
  `a6` varchar(255) NOT NULL,
  `a7` varchar(255) NOT NULL,
  `a8` varchar(255) NOT NULL,
  `a9` varchar(255) NOT NULL,
  `a10` varchar(255) NOT NULL,
  `a11` varchar(255) NOT NULL,
  `a12` varchar(255) NOT NULL,
  `a13` varchar(255) NOT NULL,
  `a14` varchar(255) NOT NULL,
  `a15` varchar(255) NOT NULL,
  `a16` varchar(255) NOT NULL,
  `a17` varchar(255) NOT NULL,
  `a18` varchar(255) NOT NULL,
  `a19` varchar(255) NOT NULL,
  `a20` varchar(255) NOT NULL,
  `a21` varchar(255) NOT NULL,
  `a22` varchar(255) NOT NULL,
  `a23` varchar(255) NOT NULL,
  `a24` varchar(255) NOT NULL,
  `a25` varchar(255) NOT NULL,
  `a26` varchar(255) NOT NULL,
  `a27` varchar(255) NOT NULL,
  `a28` varchar(255) NOT NULL,
  `a29` varchar(255) NOT NULL,
  `a30` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webform_id_2` (`webform_id`,`urn`),
  KEY `urn` (`urn`),
  KEY `webform_id` (`webform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `webform_answers`
--

TRUNCATE TABLE `webform_answers`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `webform_questions`
--

DROP TABLE IF EXISTS `webform_questions`;
CREATE TABLE IF NOT EXISTS `webform_questions` (
  `webform_question_id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_id` int(11) NOT NULL,
  `q1` varchar(255) DEFAULT NULL,
  `q2` varchar(255) DEFAULT NULL,
  `q3` varchar(255) DEFAULT NULL,
  `q4` varchar(255) DEFAULT NULL,
  `q5` varchar(255) DEFAULT NULL,
  `q6` varchar(255) DEFAULT NULL,
  `q7` varchar(255) DEFAULT NULL,
  `q8` varchar(255) DEFAULT NULL,
  `q9` varchar(255) DEFAULT NULL,
  `q10` varchar(255) DEFAULT NULL,
  `q11` varchar(255) DEFAULT NULL,
  `q12` varchar(255) DEFAULT NULL,
  `q13` varchar(255) DEFAULT NULL,
  `q14` varchar(255) DEFAULT NULL,
  `q15` varchar(255) DEFAULT NULL,
  `q16` varchar(255) DEFAULT NULL,
  `q17` varchar(255) DEFAULT NULL,
  `q18` varchar(255) DEFAULT NULL,
  `q19` varchar(255) DEFAULT NULL,
  `q20` varchar(255) DEFAULT NULL,
  `q21` varchar(255) DEFAULT NULL,
  `q22` varchar(255) DEFAULT NULL,
  `q23` varchar(255) DEFAULT NULL,
  `q24` varchar(255) DEFAULT NULL,
  `q25` varchar(255) DEFAULT NULL,
  `q26` varchar(255) DEFAULT NULL,
  `q27` varchar(255) DEFAULT NULL,
  `q28` varchar(255) DEFAULT NULL,
  `q29` varchar(255) DEFAULT NULL,
  `q30` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`webform_question_id`),
  KEY `FK_webform_questions_id` (`webform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Truncar tablas antes de insertar `webform_questions`
--

TRUNCATE TABLE `webform_questions`;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `branch_regions`
--
ALTER TABLE `branch_regions`
  ADD CONSTRAINT `branch_regions_ibfk_4` FOREIGN KEY (`default_branch_id`) REFERENCES `branch` (`branch_id`);

--
-- Filtros para la tabla `datatables_view_columns`
--
ALTER TABLE `datatables_view_columns`
  ADD CONSTRAINT `datatables_view_columns_ibfk_1` FOREIGN KEY (`view_id`) REFERENCES `datatables_views` (`view_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `hour_exception`
--
ALTER TABLE `hour_exception`
  ADD CONSTRAINT `hour_exception_ibfk_1` FOREIGN KEY (`hour_id`) REFERENCES `hours` (`hours_id`),
  ADD CONSTRAINT `hour_exception_ibfk_2` FOREIGN KEY (`exception_type_id`) REFERENCES `hour_exception_type` (`exception_type_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
