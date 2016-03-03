-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 02-03-2016 a las 19:47:11
-- Versión del servidor: 5.5.35-1ubuntu1-log
-- Versión de PHP: 5.5.9-1ubuntu4.13

USE uk_postcodes;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `uk_postcodes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gov_postcodes_2009`
--

CREATE TABLE IF NOT EXISTS `gov_postcodes_2009` (
  `postcode` varchar(10) NOT NULL,
  `longitude` decimal(18,12) NOT NULL,
  `latitude` decimal(18,12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `OpenPostcode`
--

CREATE TABLE IF NOT EXISTS `OpenPostcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postcode` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locality` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `county` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` decimal(18,12) DEFAULT NULL,
  `longitude` decimal(18,12) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `postcodeIo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9B0230319367E049` (`postcodeIo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=64020 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PafPostcode`
--

CREATE TABLE IF NOT EXISTS `PafPostcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postcode` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_town` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dependent_locality` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `double_dependent_locality` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thoroughfare_and_descriptor` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dependent_thoroughfare_and_descriptor` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `building_number` int(11) DEFAULT NULL,
  `building_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_building_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `po_box` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `organisation_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `udprn` int(11) DEFAULT NULL,
  `postcode_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `su_organisation_indicator` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `delivery_point_suffix` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `postcodeIo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FD08E2869367E049` (`postcodeIo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PostcodeIo`
--

CREATE TABLE IF NOT EXISTS `PostcodeIo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postcode` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `quality` int(11) DEFAULT NULL,
  `eastings` int(11) DEFAULT NULL,
  `northings` int(11) DEFAULT NULL,
  `country` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nhs_ha` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` decimal(18,12) DEFAULT NULL,
  `longitude` decimal(18,12) DEFAULT NULL,
  `parliamentary_constituency` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `european_electoral_region` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_care_trust` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lsoa` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msoa` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `incode` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `outcode` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_district` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parish` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_county` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_ward` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ccg` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nuts` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `postcode` (`postcode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2014362 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postcode_areas`
--

CREATE TABLE IF NOT EXISTS `postcode_areas` (
  `postcode_prefix` varchar(10) NOT NULL,
  `postcode_area` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postcode_regions`
--

CREATE TABLE IF NOT EXISTS `postcode_regions` (
  `postcode_prefix` varchar(10) NOT NULL,
  `town` varchar(50) NOT NULL,
  `postcode_region` varchar(50) NOT NULL,
  UNIQUE KEY `postcode_prefix` (`postcode_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
