-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Feb 25, 2025 at 10:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sampledb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adultmaleequivalent`
--

CREATE TABLE `adultmaleequivalent` (
  `AMEID` int(11) NOT NULL,
  `AME` decimal(10,2) NOT NULL,
  `GenderID` int(11) NOT NULL,
  `AgeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `adultmaleequivalent`
--

INSERT INTO `adultmaleequivalent` (`AMEID`, `AME`, `GenderID`, `AgeID`) VALUES
(1, 0.00, 2, 1),
(2, 0.00, 2, 2),
(3, 0.00, 2, 3),
(4, 1.00, 3, 1),
(5, 1.00, 3, 2),
(6, 1.00, 3, 3),
(7, 0.80, 4, 1),
(8, 0.80, 4, 2),
(9, 0.80, 4, 3),
(10, 0.95, 3, 3),
(11, 0.96, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `age`
--

CREATE TABLE `age` (
  `AgeID` int(11) NOT NULL,
  `AgeRange` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `age`
--

INSERT INTO `age` (`AgeID`, `AgeRange`) VALUES
(1, '0-14'),
(2, '15-64'),
(3, '65 Plus'),
(4, '0-5');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `BrandID` int(11) NOT NULL,
  `BrandName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`BrandID`, `BrandName`) VALUES
(1, 'N/A'),
(2, 'Rupchanda'),
(3, 'Fortune'),
(4, 'Kings'),
(5, 'Rupchanda'),
(6, 'Fortune'),
(7, 'Kings'),
(8, 'Veola'),
(9, 'Meizan'),
(10, 'Fresh'),
(11, 'Teer'),
(12, 'ACI'),
(13, 'Pusti'),
(14, 'Pran'),
(15, 'Kollany'),
(16, 'Lucky'),
(17, 'Bashundhara'),
(18, 'Century'),
(19, 'Maharaja'),
(20, 'Hilsa'),
(21, 'TEER Advanced'),
(22, 'SUN'),
(23, 'Natural'),
(24, 'Spondon'),
(25, 'Royal Chef'),
(26, 'Royal'),
(27, 'Bashmoti'),
(28, 'IFAD Solid Gold'),
(29, 'Natura'),
(30, 'Jasmir'),
(31, 'Health Care'),
(32, 'Saffola Active +'),
(33, 'PureGold'),
(34, 'MEER'),
(35, 'Super Fresh, No.1'),
(36, 'Super Pure'),
(37, 'Oleo'),
(38, 'Mostofa'),
(39, 'Foodela'),
(40, 'Shokti'),
(41, 'Krishani'),
(42, 'Shaad'),
(43, 'White Gold'),
(44, 'Delta'),
(45, 'Mach Marka'),
(46, 'Morog Marka'),
(47, 'SENA'),
(48, 'Deshbandhu'),
(49, 'Ranna'),
(50, 'Family'),
(51, 'Family Pusti'),
(52, 'Pusti crown'),
(53, 'Shakti'),
(54, 'Kitchena'),
(55, 'Butterfly'),
(56, 'Starship'),
(57, 'Starship Zahaj Marka'),
(58, 'Pusti Glory'),
(59, 'Hilsha'),
(60, 'Dhani'),
(61, 'Fresh Actifit');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `CompanyID` int(11) NOT NULL,
  `CompanyName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyID`, `CompanyName`) VALUES
(1, 'N/A'),
(2, 'Agrotech International Limited'),
(3, 'Ali Natural Oil Mills & Agro Industries Limited'),
(4, 'Bangladesh Edible Oil Limited'),
(5, 'Bashundhara Group of Industries'),
(6, 'TK Group of Industries'),
(7, 'City Group'),
(8, 'Globe Pharma Group of Companies'),
(9, 'Green Oil Poultry & Feed Industries'),
(10, 'Jamuna Industrial Agro Group'),
(11, 'Nurjahan Group'),
(12, 'Mahbub Group of Industries'),
(13, 'Majumder Group'),
(14, 'Meghna Group of Industries'),
(15, 'Mostafa Group of Industries'),
(16, 'MRT Group of Industries'),
(17, 'M & J Group'),
(18, 'Rashid Group of Industries'),
(19, 'SA Group of Industries'),
(20, 'TK Group'),
(21, 'Tamim Group'),
(22, 'PRAN'),
(23, 'ACI');

-- --------------------------------------------------------

--
-- Table structure for table `consumption`
--

CREATE TABLE `consumption` (
  `ConsumptionID` int(11) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `GL1ID` int(11) NOT NULL,
  `GL2ID` int(11) NOT NULL,
  `GL3ID` int(11) NOT NULL,
  `GenderID` int(11) NOT NULL,
  `AgeID` int(11) NOT NULL,
  `NumberOfPeople` int(11) NOT NULL,
  `SourceVolume` decimal(20,4) NOT NULL,
  `VolumeMTY` decimal(20,4) NOT NULL,
  `UCID` int(11) NOT NULL,
  `YearTypeID` int(11) NOT NULL,
  `StartYear` int(11) NOT NULL,
  `EndYear` int(11) NOT NULL,
  `ReferenceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `consumption`
--

INSERT INTO `consumption` (`ConsumptionID`, `VehicleID`, `GL1ID`, `GL2ID`, `GL3ID`, `GenderID`, `AgeID`, `NumberOfPeople`, `SourceVolume`, `VolumeMTY`, `UCID`, `YearTypeID`, `StartYear`, `EndYear`, `ReferenceID`) VALUES
(1, 2, 1, 1, 2, 3, 1, 2100000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(2, 2, 1, 1, 2, 4, 1, 2050000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(3, 2, 1, 1, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(4, 2, 1, 1, 2, 3, 1, 2000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(5, 2, 1, 1, 2, 4, 1, 1980000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(6, 2, 1, 1, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(7, 2, 2, 1, 3, 3, 1, 1000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(8, 2, 2, 1, 3, 4, 1, 1020000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(9, 2, 2, 1, 3, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(10, 2, 4, 1, 5, 3, 1, 500000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(11, 2, 4, 1, 5, 4, 1, 520000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(12, 2, 4, 1, 5, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(13, 2, 3, 1, 4, 3, 1, 450000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(14, 2, 3, 1, 4, 4, 1, 460000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(15, 2, 3, 1, 4, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(16, 2, 6, 1, 7, 3, 1, 600000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(17, 2, 6, 1, 7, 4, 1, 620000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(18, 2, 6, 1, 7, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(19, 2, 5, 1, 6, 3, 1, 250000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(20, 2, 5, 1, 6, 4, 1, 255000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(21, 2, 5, 1, 6, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(22, 2, 8, 1, 9, 3, 1, 1450000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(23, 2, 8, 1, 9, 4, 1, 1500000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(24, 2, 8, 1, 9, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(25, 2, 1, 1, 2, 3, 2, 6000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(26, 2, 1, 1, 2, 4, 2, 5900000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(27, 2, 1, 1, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(28, 2, 1, 1, 2, 3, 2, 5800000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(29, 2, 1, 1, 2, 4, 2, 5700000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(30, 2, 1, 1, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(31, 2, 2, 1, 3, 3, 2, 3100000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(32, 2, 2, 1, 3, 4, 2, 3200000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(33, 2, 2, 1, 3, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(34, 2, 4, 1, 5, 3, 2, 1500000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(35, 2, 4, 1, 5, 4, 2, 1520000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(36, 2, 4, 1, 5, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(37, 2, 3, 1, 4, 3, 2, 1300000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(38, 2, 3, 1, 4, 4, 2, 1350000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(39, 2, 3, 1, 4, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(40, 2, 6, 1, 7, 3, 2, 1700000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(41, 2, 6, 1, 7, 4, 2, 1750000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(42, 2, 6, 1, 7, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(43, 2, 5, 1, 6, 3, 2, 750000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(44, 2, 5, 1, 6, 4, 2, 760000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(45, 2, 5, 1, 6, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(46, 2, 7, 1, 8, 3, 2, 900000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(47, 2, 7, 1, 8, 4, 2, 920000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(48, 2, 7, 1, 8, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(49, 2, 8, 1, 9, 3, 2, 4000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(50, 2, 8, 1, 9, 4, 2, 4100000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(51, 2, 8, 1, 9, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(52, 2, 1, 1, 2, 3, 3, 400000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(53, 2, 1, 1, 2, 4, 3, 420000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(54, 2, 1, 1, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(55, 2, 1, 1, 2, 3, 3, 390000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(56, 2, 1, 1, 2, 4, 3, 400000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(57, 2, 1, 1, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(58, 2, 2, 1, 3, 3, 3, 200000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(59, 2, 2, 1, 3, 4, 3, 210000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(60, 2, 2, 1, 3, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(61, 2, 4, 1, 5, 3, 3, 100000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(62, 2, 4, 1, 5, 4, 3, 110000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(63, 2, 4, 1, 5, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(64, 2, 3, 1, 4, 3, 3, 90000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(65, 2, 3, 1, 4, 4, 3, 95000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(66, 2, 3, 1, 4, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(67, 2, 6, 1, 7, 3, 3, 120000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(68, 2, 6, 1, 7, 4, 3, 125000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(69, 2, 6, 1, 7, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(70, 2, 5, 1, 6, 3, 3, 50000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(71, 2, 5, 1, 6, 4, 3, 52000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(72, 2, 5, 1, 6, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(73, 2, 7, 1, 8, 3, 3, 60000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(74, 2, 7, 1, 8, 4, 3, 62000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(75, 2, 7, 1, 8, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(76, 2, 8, 1, 9, 3, 3, 180000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(77, 2, 8, 1, 9, 4, 3, 190000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(78, 2, 8, 1, 9, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `CountryID` int(11) NOT NULL,
  `CountryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`CountryID`, `CountryName`) VALUES
(1, 'N/A'),
(2, 'Bangladesh'),
(3, 'Germany'),
(4, 'USA'),
(5, 'India'),
(6, 'Malaysia'),
(7, 'Canada'),
(8, 'Spain'),
(9, 'Italy'),
(10, 'Turkey');

-- --------------------------------------------------------

--
-- Table structure for table `distribution`
--

CREATE TABLE `distribution` (
  `DistributionID` int(11) NOT NULL,
  `DistributionChannelID` int(11) DEFAULT NULL,
  `SubDistributionChannelID` int(11) DEFAULT NULL,
  `VehicleID` int(11) DEFAULT NULL,
  `UCID` int(11) DEFAULT NULL,
  `SourceVolume` decimal(10,2) DEFAULT NULL,
  `Volume_MT_Y` decimal(10,2) DEFAULT NULL,
  `CountryID` int(11) DEFAULT NULL,
  `YearTypeID` int(11) DEFAULT NULL,
  `StartYear` int(4) DEFAULT NULL,
  `EndYear` int(4) DEFAULT NULL,
  `ReferenceID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `distribution`
--

INSERT INTO `distribution` (`DistributionID`, `DistributionChannelID`, `SubDistributionChannelID`, `VehicleID`, `UCID`, `SourceVolume`, `Volume_MT_Y`, `CountryID`, `YearTypeID`, `StartYear`, `EndYear`, `ReferenceID`) VALUES
(1, 2, 2, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(2, 2, 3, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(3, 2, 4, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(4, 3, 5, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(5, 3, 6, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(6, 4, 7, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(7, 4, 8, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(8, 4, 9, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(9, 4, 10, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(10, 5, 11, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(11, 5, 12, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(12, 5, 7, 2, 1, 0.00, 0.00, 2, 2, 2015, 2015, 1),
(13, 5, 11, 3, 1, 0.00, 0.00, 2, 1, 2015, 2016, 1),
(14, 5, 12, 3, 1, 0.00, 0.00, 2, 1, 2015, 2016, 1),
(15, 5, 7, 3, 1, 0.00, 0.00, 2, 1, 2015, 2016, 1),
(16, 5, 7, 3, 1, 0.00, 0.00, 2, 1, 2015, 2016, 1),
(17, 1, 1, 1, 1, 0.00, 0.00, 2, 1, 2022, 2023, 1),
(18, 1, 1, 1, 1, 0.00, 0.00, 3, 2, 2022, 2022, 1),
(19, 1, 1, 1, 1, 0.00, 0.00, 4, 1, 2020, 2021, 1),
(20, 1, 1, 1, 1, 0.00, 0.00, 5, 2, 2020, 2020, 1),
(21, 1, 1, 1, 1, 0.00, 0.00, 6, 2, 2022, 2022, 1),
(22, 1, 1, 1, 1, 100.00, 100.00, 7, 2, 2022, 2022, 1),
(23, 1, 1, 1, 2, 20.00, 240.00, 8, 2, 2022, 2022, 1);

-- --------------------------------------------------------

--
-- Table structure for table `distributionchannel`
--

CREATE TABLE `distributionchannel` (
  `DistributionChannelID` int(11) NOT NULL,
  `DistributionChannelName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `distributionchannel`
--

INSERT INTO `distributionchannel` (`DistributionChannelID`, `DistributionChannelName`) VALUES
(1, 'N/A'),
(2, 'Retail'),
(3, 'Institutional'),
(4, 'Food Service'),
(5, 'B2B');

-- --------------------------------------------------------

--
-- Table structure for table `entity`
--

CREATE TABLE `entity` (
  `EntityID` int(11) NOT NULL,
  `ProducerProcessorName` varchar(255) NOT NULL,
  `CompanyID` int(11) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `GL1ID` int(11) DEFAULT NULL,
  `GL2ID` int(11) DEFAULT NULL,
  `GL3ID` int(11) DEFAULT NULL,
  `CountryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `entity`
--

INSERT INTO `entity` (`EntityID`, `ProducerProcessorName`, `CompanyID`, `VehicleID`, `GL1ID`, `GL2ID`, `GL3ID`, `CountryID`) VALUES
(1, 'N/A', 1, 1, 1, 1, 1, 1),
(2, 'Green Oil Poultry & Feed Limited', 9, 2, 8, 1, 1, 2),
(3, 'IFAD Multi Products Limited', 1, 2, 2, 1, 1, 2),
(4, 'Jamuna Edible Oil Industries Limited', 10, 2, 7, 1, 1, 2),
(5, 'VOTT Oil Refineries Limited', 7, 2, 4, 1, 1, 2),
(6, 'A Deshi Food Product Ltd', 1, 2, 1, 1, 1, 2),
(7, 'AMB Nosia Ltd', 1, 2, 1, 1, 1, 2),
(8, 'Ara Food & Beverage', 1, 2, 1, 1, 1, 2),
(9, 'Areej Veg Oils', 1, 2, 1, 1, 1, 2),
(10, 'Chamak Food Products', 1, 2, 1, 1, 1, 2),
(11, 'Confidence Edible Oil Ltd', 1, 2, 1, 1, 1, 2),
(12, 'Gazi NPKS', 1, 2, 1, 1, 1, 2),
(13, 'JB Foods Service', 1, 2, 1, 1, 1, 2),
(14, 'Jibon Food Products', 1, 2, 1, 1, 1, 2),
(15, 'Kanon Enterprise', 1, 2, 1, 1, 1, 2),
(16, 'MH Edible Products Ltd', 1, 2, 1, 1, 1, 2),
(17, 'Madina Oil Limited', 1, 2, 1, 1, 1, 2),
(18, 'Monjil Multi Food Product Ltd', 1, 2, 1, 1, 1, 2),
(19, 'MS Modina Oil Mills', 1, 2, 1, 1, 1, 2),
(20, 'Natore Agro Ltd', 1, 2, 1, 1, 1, 2),
(21, 'NM Food Product', 1, 2, 1, 1, 1, 2),
(22, 'Olitalia', 1, 2, 1, 1, 1, 2),
(23, 'Omi Food Product', 1, 2, 1, 1, 1, 2),
(24, 'Pran Agro Ltd', 1, 2, 1, 1, 1, 2),
(25, 'President Food Products', 1, 2, 1, 1, 1, 2),
(26, 'RB Edible Food Product', 1, 2, 1, 1, 1, 2),
(27, 'Rony Edible Oil & Food products Ltd', 1, 2, 1, 1, 1, 2),
(28, 'Saif Edible Food Packing', 1, 2, 1, 1, 1, 2),
(29, 'MM Vegetable Oil Industries', 1, 2, 2, 8, 1, 2),
(30, 'Deepa Food Products Ltd', 1, 2, 2, 8, 1, 2),
(31, 'Tanveer/United Edible Oil Mills', 1, 2, 2, 8, 1, 2),
(32, 'Shun Shing Edible oil Ltd', 1, 2, 5, 35, 156, 2),
(33, 'Marine Edible Oil Ltd', 1, 2, 1, 1, 1, 2),
(34, 'ACI Edible Oils Ltd', 1, 2, 7, 42, 1, 2),
(35, 'AM Bran Oil Company Ltd', 1, 2, 2, 1, 1, 2),
(36, 'Adani Wilmar Ltd', 1, 2, 10, 66, 1, 2),
(37, 'Lam Soon Edible Oil Industries Sdn Bhd', 1, 2, 1, 1, 1, 6),
(38, 'Green Oil Ltd', 1, 2, 8, 1, 1, 2),
(39, 'KBC Agro Products Ltd', 1, 2, 2, 1, 1, 2),
(40, 'Kadooglu yag san', 1, 2, 1, 1, 1, 2),
(41, 'Kucukbay Oil Industry Inc', 1, 2, 1, 1, 1, 2),
(42, 'Majumder Group of Industries', 1, 2, 5, 1, 1, 2),
(43, 'MRT Agro Product Ltd', 1, 2, 6, 1, 1, 2),
(44, 'Mateo SA', 1, 2, 1, 1, 1, 8),
(45, 'United Edible Oil Ltd', 1, 2, 2, 1, 1, 2),
(46, 'Chemical Oil Refineries', 1, 2, 1, 1, 1, 2),
(47, 'Borges Agricultural and Industrial Edible Oils', 1, 2, 1, 1, 1, 2),
(48, 'Rashid Oil Mills Ltd', 1, 2, 7, 1, 1, 2),
(49, 'Khan Food & Aquarcy Ltd', 1, 2, 7, 1, 1, 2),
(50, 'Shabnam Vegetable Oil Industries Ltd', 1, 2, 2, 1, 1, 2),
(51, 'Super Oil Refinery Ltd', 1, 2, 2, 1, 1, 2),
(52, 'Helvacidade Food, Pharma and Chemicals Incorporation', 1, 2, 1, 1, 1, 10),
(53, 'Nurjahan', 23, 2, 1, 1, 1, 1),
(54, 'IFAD', 1, 2, 2, 1, 1, 1),
(55, 'Sajeeb', 1, 2, 1, 1, 1, 1),
(56, 'Emerald Oil Industries Limited', 1, 2, 3, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `extractionconversion`
--

CREATE TABLE `extractionconversion` (
  `ExtractionID` int(11) NOT NULL,
  `ExtractionRate` decimal(10,2) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `FoodTypeID` int(11) NOT NULL,
  `ReferenceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `extractionconversion`
--

INSERT INTO `extractionconversion` (`ExtractionID`, `ExtractionRate`, `VehicleID`, `FoodTypeID`, `ReferenceID`) VALUES
(1, 0.00, 1, 1, 1),
(2, 17.50, 2, 6, 1),
(3, 17.50, 2, 7, 1),
(4, 17.50, 2, 8, 1),
(5, 17.50, 2, 2, 1),
(6, 17.50, 2, 5, 1),
(7, 17.50, 2, 4, 1),
(8, 17.50, 2, 13, 1),
(9, 17.50, 2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `foodtype`
--

CREATE TABLE `foodtype` (
  `FoodTypeID` int(11) NOT NULL,
  `FoodTypeName` varchar(100) NOT NULL,
  `VehicleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `foodtype`
--

INSERT INTO `foodtype` (`FoodTypeID`, `FoodTypeName`, `VehicleID`) VALUES
(1, 'N/A', 1),
(2, 'Soya Bean', 2),
(3, 'Palm', 2),
(4, 'Sunflower', 2),
(5, 'Coconut', 2),
(6, 'Sesame', 2),
(7, 'Rape & Mustard', 2),
(8, 'Ground Nut', 2),
(9, 'Cottonseed', 2),
(10, 'Rapeseed & Canola', 2),
(11, 'Rice Bran', 2),
(12, 'Super Palm', 2),
(13, 'Olive Oil', 2),
(14, 'Vegetable oil', 2),
(15, 'Palm Kernel', 2),
(16, 'Whole Wheat', 3),
(17, 'Atta', 3),
(18, 'Maida', 3),
(19, 'Wheat Crop', 3);

-- --------------------------------------------------------

--
-- Table structure for table `foodvehicle`
--

CREATE TABLE `foodvehicle` (
  `VehicleID` int(11) NOT NULL,
  `VehicleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `foodvehicle`
--

INSERT INTO `foodvehicle` (`VehicleID`, `VehicleName`) VALUES
(1, 'N/A'),
(2, 'Edible Oil'),
(3, 'Wheat Flour');

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE `gender` (
  `GenderID` int(11) NOT NULL,
  `GenderName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`GenderID`, `GenderName`) VALUES
(1, 'N/A'),
(2, 'Other'),
(3, 'Male'),
(4, 'Female');

-- --------------------------------------------------------

--
-- Table structure for table `geographylevel1`
--

CREATE TABLE `geographylevel1` (
  `GL1ID` int(11) NOT NULL,
  `AdminLevel1` varchar(50) NOT NULL,
  `CountryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `geographylevel1`
--

INSERT INTO `geographylevel1` (`GL1ID`, `AdminLevel1`, `CountryID`) VALUES
(1, 'N/A', 1),
(2, 'Dhaka', 2),
(3, 'Barishal', 2),
(4, 'Chattogram', 2),
(5, 'Khulna', 2),
(6, 'Mymensingh', 2),
(7, 'Rajshahi', 2),
(8, 'Rangpur', 2),
(9, 'Sylhet', 2),
(10, 'Gujarat', 5);

-- --------------------------------------------------------

--
-- Table structure for table `geographylevel2`
--

CREATE TABLE `geographylevel2` (
  `GL2ID` int(11) NOT NULL,
  `AdminLevel2` varchar(50) NOT NULL,
  `GL1ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `geographylevel2`
--

INSERT INTO `geographylevel2` (`GL2ID`, `AdminLevel2`, `GL1ID`) VALUES
(1, 'N/A', 1),
(2, 'Dhaka', 2),
(3, 'Gazipur', 2),
(4, 'Munshiganj', 2),
(5, 'Kishoreganj', 2),
(6, 'Shariatpur', 2),
(7, 'Gopalganj', 2),
(8, 'Narayanganj', 2),
(9, 'Manikganj', 2),
(10, 'Faridpur', 2),
(11, 'Narsingdi', 2),
(12, 'Rajbari', 2),
(13, 'Tangail', 2),
(14, 'Madaripur', 2),
(15, 'Mymensingh', 6),
(16, 'Sherpur', 6),
(17, 'Jamalpur', 6),
(18, 'Netrokona', 6),
(19, 'Chattogram', 4),
(20, 'Cox\\\'s Bazar', 4),
(21, 'Bandarban', 4),
(22, 'Comilla', 4),
(23, 'Bramanbaria', 4),
(24, 'Chandpur', 4),
(25, 'Feni', 4),
(26, 'Lakshmipur', 4),
(27, 'Noakhali', 4),
(28, 'Rangamati', 4),
(29, 'Khagrachari', 4),
(30, 'Khulna', 5),
(31, 'Jessor', 5),
(32, 'Satkhira', 5),
(33, 'Kushtia', 5),
(34, 'Chuadanga', 5),
(35, 'Bagerhat', 5),
(36, 'Jhenaidah', 5),
(37, 'Magura', 5),
(38, 'Meherpur', 5),
(39, 'Narail', 5),
(40, 'Rajshahi', 7),
(41, 'Naogaon', 7),
(42, 'Shirajganj', 7),
(43, 'Joypurhat', 7),
(44, 'Bogura', 7),
(45, 'Chapainawabganj', 7),
(46, 'Natore', 7),
(47, 'Pabna', 7),
(48, 'Nilphamari', 8),
(49, 'Dinajpur', 8),
(50, 'Panchagar', 8),
(51, 'Gaibandha', 8),
(52, 'Kurigram', 8),
(53, 'Lalmonirhat', 8),
(54, 'Rangpur', 8),
(55, 'Thakurgaon', 8),
(56, 'Bhola', 3),
(57, 'Barishal', 3),
(58, 'Pirojpur', 3),
(59, 'Barguna', 3),
(60, 'Jhalokathi', 3),
(61, 'Patuakhali', 3),
(62, 'Moulvibazar', 9),
(63, 'Sylhet', 9),
(64, 'Habiganj', 9),
(65, 'Sunamganj', 9),
(66, 'Gujarat', 10);

-- --------------------------------------------------------

--
-- Table structure for table `geographylevel3`
--

CREATE TABLE `geographylevel3` (
  `GL3ID` int(11) NOT NULL,
  `AdminLevel3` varchar(50) NOT NULL,
  `GL2ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `geographylevel3`
--

INSERT INTO `geographylevel3` (`GL3ID`, `AdminLevel3`, `GL2ID`) VALUES
(1, 'N/A', 1),
(2, 'Pirojpur', 58),
(3, 'Mathbaria', 58),
(4, 'Swarupkathi', 58),
(5, 'Jhalokathi', 60),
(6, 'Barguna', 59),
(7, 'Amtali', 59),
(8, 'Patuakhali', 61),
(9, 'Galachipa', 61),
(10, 'Kalapara', 61),
(11, 'Bhola', 56),
(12, 'Lalmohan', 56),
(13, 'Charfassion', 56),
(14, 'Burhanuddin', 56),
(15, 'Gouranadi', 57),
(16, 'Muladi', 57),
(17, 'Mehendiganj', 57),
(18, 'Bakerganj', 57),
(19, 'Banaripara', 57),
(20, 'Nalchhity', 60),
(21, 'Patharghata', 59),
(22, 'Bauphal', 61),
(23, 'Daulatkhan', 56),
(24, 'Kuakata', 61),
(25, 'Betagi', 59),
(26, 'Uzirpur', 57),
(27, 'Bhandaria', 58),
(28, 'Patiya', 19),
(29, 'Bariarhat', 19),
(30, 'Sitakunda', 19),
(31, 'Satkania', 19),
(32, 'Bashkhali', 19),
(33, 'Cox? Bazar', 20),
(34, 'Chakoria', 20),
(35, 'Rangamati', 28),
(36, 'Bandarban', 21),
(37, 'Khagrachhari', 29),
(38, 'Chandanaish', 19),
(39, 'Raojan', 19),
(40, 'Mirsharai', 19),
(41, 'Rangunia', 19),
(42, 'Sandwip', 19),
(43, 'Teknaf', 20),
(44, 'Moheskhali', 20),
(45, 'Ramgarh', 29),
(46, 'Lama', 21),
(47, 'Fatikchhari', 19),
(48, 'Hathazari', 19),
(49, 'Boalkhali', 19),
(50, 'Nazirhat', 19),
(51, 'Matiranga', 29),
(52, 'Baghaichhari', 28),
(53, 'Chandpur', 24),
(54, 'Haziganj', 24),
(55, 'Shahrasti', 24),
(56, 'Kachua', 24),
(57, 'Noakhali', 27),
(58, 'Choumuhani', 27),
(59, 'Chatkhil', 27),
(60, 'Basurhat', 27),
(61, 'Sonaimuri', 27),
(62, 'Laxmipur', 26),
(63, 'Ramganj', 26),
(64, 'Raipur', 26),
(65, 'Feni', 25),
(66, 'Daganbhuiyan', 25),
(67, 'Kabirhat', 27),
(68, 'Senbag', 27),
(69, 'Chhagalnaiya', 25),
(70, 'Sonagazi', 25),
(71, 'Parshuram', 25),
(72, 'Matlab', 24),
(73, 'Chengarchar', 24),
(74, 'Faridganj', 24),
(75, 'Narayanpur', 24),
(76, 'Hatiya', 27),
(77, 'Dohazari', 19),
(78, 'Tarabo', 8),
(79, 'Narsingdi', 11),
(80, 'Madhabdi', 11),
(81, 'Ghorashal', 11),
(82, 'Savar', 2),
(83, 'Dohar', 2),
(84, 'Tangail', 13),
(85, 'Kaliakoir', 3),
(86, 'Munshiganj', 4),
(87, 'Mirkadim', 4),
(88, 'Manikganj', 9),
(89, 'Gopalpur', 13),
(90, 'Bhuapur', 13),
(91, 'Ghatail', 13),
(92, 'Madhupur', 13),
(93, 'Mirzapur', 13),
(94, 'Dhanbari', 13),
(95, 'Kalihati', 13),
(96, 'Sakhipur', 13),
(97, 'Sonargaon', 8),
(98, 'Dhamrai', 2),
(99, 'Sreepur', 3),
(100, 'Kaliganj', 3),
(101, 'Singair', 9),
(102, 'Elenga', 13),
(103, 'Basail', 13),
(104, 'Monohardi', 11),
(105, 'Shibpur', 11),
(106, 'Raipura', 11),
(107, 'Kanchan', 8),
(108, 'Araihazar', 8),
(109, 'Gopaldi', 8),
(110, 'Faridpur', 10),
(111, 'Bhanga', 10),
(112, 'Boalmari', 10),
(113, 'Rajbari', 12),
(114, 'Madaripur', 14),
(115, 'Shibchar', 14),
(116, 'Shariatpur', 6),
(117, 'Gopalganj', 7),
(118, 'Muksudpur', 7),
(119, 'Tungipara', 7),
(120, 'Damuddya', 6),
(121, 'Naria', 6),
(122, 'Pangsha', 12),
(123, 'Goalanda', 12),
(124, 'Kalkini', 14),
(125, 'Nagarkanda', 10),
(126, 'Madhukhali', 10),
(127, 'Rajoir', 14),
(128, 'Zanjira', 6),
(129, 'Bhedarganj', 6),
(130, 'Goshairhat', 6),
(131, 'Kishoreganj', 5),
(132, 'Bhairab', 5),
(133, 'Bajitpur', 5),
(134, 'Karimganj', 5),
(135, 'Kuliarchar', 5),
(136, 'Pakundia', 5),
(137, 'Hossainpur', 5),
(138, 'Kotalipara', 7),
(139, 'Alfadanga', 10),
(140, 'Katiadi', 5),
(141, 'Lohagara', 39),
(142, 'Kalia', 39),
(143, 'Magura', 37),
(144, 'Narail', 39),
(145, 'Jhenaidaha', 36),
(146, 'Kotchandpur', 36),
(147, 'Maheshpur', 36),
(148, 'Kaliganj', 36),
(149, 'Shailkupa', 36),
(150, 'Chuadanga', 34),
(151, 'Alamdanga', 34),
(152, 'Satkhira', 32),
(153, 'Kushtia', 33),
(154, 'Kumarkhali', 33),
(155, 'Bagerhat', 35),
(156, 'Mongla', 35),
(157, 'Meherpur', 38),
(158, 'Paikgachha', 30),
(159, 'Chalna', 30),
(160, 'Bheramara', 33),
(161, 'Mirpur', 33),
(162, 'Gangni', 38),
(163, 'Kalaroa', 32),
(164, 'Jiban Nagar', 34),
(165, 'Darshana', 34),
(166, 'Morrelganj', 35),
(167, 'Khoksha', 33),
(168, 'Harinakunda', 36),
(169, 'Gafargaon', 15),
(170, 'Trishal', 15),
(171, 'Muktagacha', 15),
(172, 'Ishwarganj', 15),
(173, 'Bhaluka', 15),
(174, 'Gauripur', 15),
(175, 'Phulpur', 15),
(176, 'Jamalpur', 17),
(177, 'Sherpur', 16),
(178, 'Netrokona', 18),
(179, 'Sharishabari', 17),
(180, 'Islampur', 17),
(181, 'Melandaha', 17),
(182, 'Dewanganj', 17),
(183, 'Madarganj', 17),
(184, 'Fulbaria', 15),
(185, 'Mohanganj', 18),
(186, 'Nalitabari', 16),
(187, 'Durgapur', 18),
(188, 'Kendua', 18),
(189, 'Madan', 18),
(190, 'Nakla', 16),
(191, 'Sreebardi', 16),
(192, 'Nandail', 15),
(193, 'Haluaghat', 15),
(194, 'Bakshiganj', 17),
(195, 'Hazrabari', 17),
(196, 'Bogura', 44),
(197, 'Sherpur', 44),
(198, 'Nandigram', 44),
(199, 'Pabna', 47),
(200, 'Ishwardi', 47),
(201, 'Bera', 47),
(202, 'Suzanagar', 47),
(203, 'Santhia', 47),
(204, 'Joypurhat', 43),
(205, 'Panchbibi', 43),
(206, 'Natore', 46),
(207, 'Gurudaspur', 46),
(208, 'Singra', 46),
(209, 'Banpara', 46),
(210, 'Naogaon', 41),
(211, 'Godagari', 40),
(212, 'Taherpur', 40),
(213, 'Naohata', 40),
(214, 'Charghat', 40),
(215, 'Baraigram', 46),
(216, 'Dhamoirhat', 41),
(217, 'Nazipur', 41),
(218, 'Dhupchanchia', 44),
(219, 'Dhunat', 44),
(220, 'Kalai', 43),
(221, 'Akkelpur', 43),
(222, 'Khetlal', 43),
(223, 'Mondumala', 40),
(224, 'Bagha', 40),
(225, 'Keshorehat', 40),
(226, 'Kakonhat', 40),
(227, 'Arani', 40),
(228, 'Chatmohar', 47),
(229, 'Bhangura', 47),
(230, 'Faridpur', 47),
(231, 'Santahar', 44),
(232, 'Gopalpur', 46),
(233, 'Bagatipara', 46),
(234, 'Naldanga', 46),
(235, 'Atgharia', 47),
(236, 'Sariakandi', 44),
(237, 'Sonatola', 44),
(238, 'Shibganj', 44),
(239, 'Kahaloo', 44),
(240, 'Gabtali', 44),
(241, 'Talora', 44),
(242, 'Bhabaniganj', 40),
(243, 'Tanore', 40),
(244, 'Puthia', 40),
(245, 'Katakhali', 40),
(246, 'Durgapur', 40),
(247, 'Gaibandha', 51),
(248, 'Gobindaganj', 51),
(249, 'Dinajpur', 49),
(250, 'Setabganj', 49),
(251, 'Birampur', 49),
(252, 'Parbatipur', 49),
(253, 'Fulbari', 49),
(254, 'Thakurgaon', 55),
(255, 'Nilphamari', 48),
(256, 'Saidpur', 48),
(257, 'Kurigram', 52),
(258, 'Nageswari', 52),
(259, 'Lalmonirhat', 53),
(260, 'Patgram', 53),
(261, 'Birganj', 49),
(262, 'Sundarganj', 51),
(263, 'Ulipur', 52),
(264, 'Jaldhaka', 48),
(265, 'Badarganj', 54),
(266, 'Pirganj', 55),
(267, 'Domar', 48),
(268, 'Haragachh', 54),
(269, 'Pirganj', 54),
(270, 'Hakimpur', 49),
(271, 'Ghoraghat', 49),
(272, 'Biral', 49),
(273, 'Palashbari', 51),
(274, 'Ranisankail', 55),
(275, 'Habiganj', 64),
(276, 'Madhabpur', 64),
(277, 'Shayestaganj', 64),
(278, 'Laksham', 22),
(279, 'Chouddagram', 22),
(280, 'Panchagar', 50),
(281, 'Kazipur', 42),
(282, 'Raiganj', 42),
(283, 'Tarash', 42),
(284, 'Belkuchi', 42),
(285, 'Nachol', 45),
(286, 'Chapainawabganjanj', 45),
(287, 'Shibganj', 45),
(288, 'Rohanpur', 45),
(289, 'Shirajganj', 42),
(290, 'Shahjadpur', 42),
(291, 'Ullapara', 42),
(292, 'Daudkandi', 22),
(293, 'Chandina', 22),
(294, 'Debidwar', 22),
(295, 'Homna', 22),
(296, 'Barura', 22),
(297, 'Nangalkot', 22),
(298, 'Dhaka North City Corporation', 2),
(299, 'Dhaka South City Corporation', 2),
(300, 'Chattogram City Corporation', 19),
(301, 'Khulna City Corporation', 30),
(302, 'Rajshahi City Corporation', 40),
(303, 'Barishal City Corporation', 57),
(304, 'Rangpur City Corporation', 54),
(305, 'Mymensingh City Corporation', 15),
(306, 'Sylhet City Corporation', 63);

-- --------------------------------------------------------

--
-- Table structure for table `individualconsumption`
--

CREATE TABLE `individualconsumption` (
  `ConsumptionID` int(11) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `GenderID` int(11) NOT NULL,
  `AgeID` int(11) NOT NULL,
  `NumberOfPeople` int(11) NOT NULL,
  `SourceVolume` decimal(20,4) NOT NULL,
  `VolumeMTY` decimal(20,4) NOT NULL,
  `UCID` int(11) NOT NULL,
  `YearTypeID` int(11) NOT NULL,
  `StartYear` int(11) NOT NULL,
  `EndYear` int(11) NOT NULL,
  `ReferenceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `individualconsumption`
--

INSERT INTO `individualconsumption` (`ConsumptionID`, `VehicleID`, `GenderID`, `AgeID`, `NumberOfPeople`, `SourceVolume`, `VolumeMTY`, `UCID`, `YearTypeID`, `StartYear`, `EndYear`, `ReferenceID`) VALUES
(1, 2, 3, 1, 2100000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(2, 2, 4, 1, 2050000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(3, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(4, 2, 3, 1, 2000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(5, 2, 4, 1, 1980000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(6, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(7, 2, 3, 1, 1000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(8, 2, 4, 1, 1020000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(9, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(10, 2, 3, 1, 500000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(11, 2, 4, 1, 520000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(12, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(13, 2, 3, 1, 450000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(14, 2, 4, 1, 460000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(15, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(16, 2, 3, 1, 600000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(17, 2, 4, 1, 620000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(18, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(19, 2, 3, 1, 250000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(20, 2, 4, 1, 255000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(21, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(22, 2, 3, 1, 1450000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(23, 2, 4, 1, 1500000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(24, 2, 2, 1, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(25, 2, 3, 2, 6000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(26, 2, 4, 2, 5900000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(27, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(28, 2, 3, 2, 5800000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(29, 2, 4, 2, 5700000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(30, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(31, 2, 3, 2, 3100000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(32, 2, 4, 2, 3200000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(33, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(34, 2, 3, 2, 1500000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(35, 2, 4, 2, 1520000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(36, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(37, 2, 3, 2, 1300000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(38, 2, 4, 2, 1350000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(39, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(40, 2, 3, 2, 1700000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(41, 2, 4, 2, 1750000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(42, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(43, 2, 3, 2, 750000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(44, 2, 4, 2, 760000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(45, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(46, 2, 3, 2, 900000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(47, 2, 4, 2, 920000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(48, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(49, 2, 3, 2, 4000000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(50, 2, 4, 2, 4100000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(51, 2, 2, 2, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(52, 2, 3, 3, 400000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(53, 2, 4, 3, 420000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(54, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(55, 2, 3, 3, 390000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(56, 2, 4, 3, 400000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(57, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(58, 2, 3, 3, 200000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(59, 2, 4, 3, 210000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(60, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(61, 2, 3, 3, 100000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(62, 2, 4, 3, 110000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(63, 2, 2, 3, 0, 20.0000, 0.0073, 9, 2, 2022, 2022, 1),
(64, 2, 3, 3, 90000, 30.0000, 0.0110, 9, 2, 2022, 2022, 1),
(65, 2, 4, 3, 95000, 40.0000, 0.0146, 9, 2, 2022, 2022, 1),
(66, 2, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(67, 2, 3, 3, 120000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(68, 2, 4, 3, 125000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(69, 2, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(70, 2, 3, 3, 50000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(71, 2, 4, 3, 52000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(72, 2, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(73, 2, 3, 3, 60000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(74, 2, 4, 3, 62000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(75, 2, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(76, 2, 3, 3, 180000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(77, 2, 4, 3, 190000, 0.0000, 0.0000, 9, 2, 2022, 2022, 1),
(78, 2, 2, 3, 0, 0.0000, 0.0000, 9, 2, 2022, 2022, 1);

-- --------------------------------------------------------

--
-- Table structure for table `measurecurrency`
--

CREATE TABLE `measurecurrency` (
  `MCID` int(11) NOT NULL,
  `CurrencyName` varchar(50) NOT NULL,
  `CurrencyValue` decimal(20,12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `measurecurrency`
--

INSERT INTO `measurecurrency` (`MCID`, `CurrencyName`, `CurrencyValue`) VALUES
(1, 'N/A', 0.000000000000),
(2, 'USD', 1.000000000000),
(3, 'BDT', 119.440000000000),
(4, 'INR', 84.440000000000),
(5, 'GBP', 0.804200000000);

-- --------------------------------------------------------

--
-- Table structure for table `measureunit1`
--

CREATE TABLE `measureunit1` (
  `UCID` int(11) NOT NULL,
  `SupplyVolumeUnit` varchar(50) NOT NULL,
  `PeriodicalUnit` varchar(50) NOT NULL,
  `UnitValue` decimal(30,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `measureunit1`
--

INSERT INTO `measureunit1` (`UCID`, `SupplyVolumeUnit`, `PeriodicalUnit`, `UnitValue`) VALUES
(1, 'Metric Ton', 'Year', 1.00000000),
(2, 'Metric Ton', 'Month', 12.00000000),
(3, '1000 t', 'Year', 1000.00000000),
(4, '1000 t', 'Month', 12000.00000000),
(5, 'KG', 'Year', 0.00100000),
(6, 'KG', 'Month', 0.01200000),
(7, 'Gram', 'Year', 0.00000100),
(8, 'Gram', 'Month', 0.00001200),
(9, 'Gram', 'Day', 0.00036500);

-- --------------------------------------------------------

--
-- Table structure for table `packagingtype`
--

CREATE TABLE `packagingtype` (
  `PackagingTypeID` int(11) NOT NULL,
  `PackagingTypeName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `packagingtype`
--

INSERT INTO `packagingtype` (`PackagingTypeID`, `PackagingTypeName`) VALUES
(1, 'N/A'),
(2, 'Pet Bottle'),
(3, 'Poly');

-- --------------------------------------------------------

--
-- Table structure for table `processingstage`
--

CREATE TABLE `processingstage` (
  `PSID` int(11) NOT NULL,
  `ProcessingStageName` varchar(255) NOT NULL,
  `ExtractionRate` decimal(10,2) DEFAULT NULL,
  `VehicleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `processingstage`
--

INSERT INTO `processingstage` (`PSID`, `ProcessingStageName`, `ExtractionRate`, `VehicleID`) VALUES
(1, 'N/A', 0.00, 1),
(2, 'Oil Seeds & Oleaginous Fruits', 100.00, 2),
(3, 'Oil Seeds', 100.00, 2),
(4, 'Flours & Meals of Oil Seeds (Not Mustard)', 100.00, 2),
(5, 'Oil Seeds & Other Oily Fruit', 100.00, 2),
(6, 'Crude Oil', 15.00, 2),
(7, 'Oil without fortification', 100.00, 2),
(8, 'Refined Oil', 100.00, 2),
(9, 'Wheat Flour', 100.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `producerprocessor`
--

CREATE TABLE `producerprocessor` (
  `ProducerProcessorID` int(11) NOT NULL,
  `EntityID` int(11) NOT NULL,
  `TaskDoneByEntity` varchar(255) DEFAULT NULL,
  `ProductionCapacityVolumeMTY` decimal(20,3) DEFAULT NULL,
  `PercentageOfCapacityUsed` decimal(10,2) DEFAULT NULL,
  `AnnualProductionSupplyVolumeMTY` decimal(20,3) DEFAULT NULL,
  `ProducerReferenceID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `producerprocessor`
--

INSERT INTO `producerprocessor` (`ProducerProcessorID`, `EntityID`, `TaskDoneByEntity`, `ProductionCapacityVolumeMTY`, `PercentageOfCapacityUsed`, `AnnualProductionSupplyVolumeMTY`, `ProducerReferenceID`) VALUES
(1, 1, 'N/A', 0.000, 0.00, 0.000, 1),
(2, 2, 'Distributor', 0.000, 100.00, 0.000, 1),
(3, 3, 'Distributor', 0.000, 100.00, 0.000, 1),
(4, 4, 'Distributor', 0.000, 100.00, 0.000, 1),
(5, 5, 'Distributor', 0.000, 100.00, 0.000, 1),
(6, 6, 'Distributor', 0.000, 100.00, 0.000, 1),
(7, 7, 'Distributor', 0.000, 100.00, 0.000, 1),
(8, 8, 'Distributor', 0.000, 100.00, 0.000, 1),
(9, 9, 'Distributor', 0.000, 100.00, 0.000, 1),
(10, 10, 'Distributor', 0.000, 100.00, 0.000, 1),
(11, 11, 'Distributor', 0.000, 100.00, 0.000, 1),
(12, 12, 'Distributor', 0.000, 100.00, 0.000, 1),
(13, 13, 'Distributor', 0.000, 100.00, 0.000, 1),
(14, 14, 'Distributor', 0.000, 100.00, 0.000, 1),
(15, 15, 'Distributor', 0.000, 100.00, 0.000, 1),
(16, 16, 'Distributor', 0.000, 100.00, 0.000, 1),
(17, 17, 'Distributor', 0.000, 100.00, 0.000, 1),
(18, 18, 'Distributor', 0.000, 100.00, 0.000, 1),
(19, 19, 'Distributor', 0.000, 100.00, 0.000, 1),
(20, 20, 'Distributor', 0.000, 100.00, 0.000, 1),
(21, 21, 'Distributor', 0.000, 100.00, 0.000, 1),
(22, 22, 'Distributor', 0.000, 100.00, 0.000, 1),
(23, 23, 'Distributor', 0.000, 100.00, 0.000, 1),
(24, 24, 'Distributor', 0.000, 100.00, 0.000, 1),
(25, 25, 'Distributor', 0.000, 100.00, 0.000, 1),
(26, 26, 'Distributor', 0.000, 100.00, 0.000, 1),
(27, 27, 'Distributor', 0.000, 100.00, 0.000, 1),
(28, 28, 'Distributor', 0.000, 100.00, 0.000, 1),
(29, 29, 'Distributor', 0.000, 100.00, 0.000, 1),
(30, 31, 'Distributor', 0.000, 100.00, 0.000, 1),
(31, 32, 'Distributor', 0.000, 100.00, 0.000, 1),
(32, 33, 'Distributor', 0.000, 100.00, 0.000, 1),
(33, 34, 'Distributor', 0.000, 100.00, 0.000, 1),
(34, 35, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(35, 36, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(36, 37, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(37, 38, 'Refinery', 200000.000, 100.00, 200000.000, 1),
(38, 39, 'Refinery', 0.000, 100.00, 0.000, 1),
(39, 40, 'Refinery', 0.000, 100.00, 0.000, 1),
(40, 41, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(41, 42, 'Refinery', 0.000, 100.00, 0.000, 1),
(42, 43, 'Refinery', 0.000, 100.00, 0.000, 1),
(43, 44, 'Refinery', 0.000, 100.00, 0.000, 1),
(44, 45, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(45, 46, 'Refinery', 0.000, 100.00, 0.000, 1),
(46, 47, 'Refinery', 0.000, 100.00, 0.000, 1),
(47, 48, 'Refinery', 0.000, 100.00, 0.000, 1),
(48, 49, 'Refinery', 42000.000, 100.00, 42000.000, 1),
(49, 50, 'Refinery', 42000.000, 100.00, 42000.000, 1),
(50, 51, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(51, 52, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(52, 53, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(53, 54, 'Refinery', 150000.000, 100.00, 150000.000, 1),
(54, 55, 'Refinery', 600000.000, 100.00, 600000.000, 1),
(55, 56, 'Refinery', 150000.000, 100.00, 150000.000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `producerreference`
--

CREATE TABLE `producerreference` (
  `ProducerReferenceID` int(11) NOT NULL,
  `CompanyID` int(11) DEFAULT NULL,
  `IdentifierNumber` varchar(255) DEFAULT NULL,
  `IdentifierReferenceSystem` varchar(255) DEFAULT NULL,
  `CountryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `producerreference`
--

INSERT INTO `producerreference` (`ProducerReferenceID`, `CompanyID`, `IdentifierNumber`, `IdentifierReferenceSystem`, `CountryID`) VALUES
(1, 1, '1', 'N/A', 1),
(2, 14, 'DHK-12345', 'BSTI', 2);

-- --------------------------------------------------------

--
-- Table structure for table `producersku`
--

CREATE TABLE `producersku` (
  `SKUID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `CompanyID` int(11) NOT NULL,
  `SKU` int(11) DEFAULT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `PackagingTypeID` int(11) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
  `ReferenceID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `producersku`
--

INSERT INTO `producersku` (`SKUID`, `ProductID`, `CompanyID`, `SKU`, `Unit`, `PackagingTypeID`, `Price`, `CurrencyID`, `ReferenceID`) VALUES
(1, 1, 1, 0, 'Litre', 1, 0.00, 1, 1),
(2, 2, 4, 1, 'Litre', 2, 164.00, 3, 88),
(3, 2, 4, 1, 'Litre', 3, 167.00, 3, 89),
(4, 2, 4, 2, 'Litre', 3, 334.00, 3, 1),
(5, 2, 4, 5, 'Litre', 3, 818.00, 3, 1),
(6, 2, 4, 8, 'Litre', 3, 1320.00, 3, 1),
(7, 3, 4, 1, 'Litre', 3, 197.00, 3, 1),
(8, 3, 4, 2, 'Litre', 3, 390.00, 3, 1),
(9, 3, 4, 5, 'Litre', 3, 935.00, 3, 1),
(10, 4, 4, 1, 'Litre', 3, 350.00, 3, 1),
(11, 5, 4, 5, 'Litre', 3, 2100.00, 3, 1),
(12, 6, 4, 2, 'Litre', 3, 338.00, 3, 1),
(13, 7, 4, 5, 'Litre', 3, 860.00, 3, 1),
(14, 8, 14, 1, 'Litre', 2, 165.00, 3, 1),
(15, 8, 14, 2, 'Litre', 3, 334.00, 3, 1),
(16, 8, 14, 5, 'Litre', 3, 818.00, 3, 1),
(17, 8, 14, 8, 'Litre', 3, 1310.00, 3, 1),
(18, 9, 14, 500, 'Millilitre', 3, 180.00, 3, 1),
(19, 9, 14, 1, 'Litre', 3, 350.00, 3, 1),
(20, 10, 7, 1, 'Litre', 3, 167.00, 3, 1),
(21, 10, 7, 2, 'Litre', 3, 334.00, 3, 1),
(22, 10, 7, 5, 'Litre', 3, 818.00, 3, 1),
(23, 10, 7, 8, 'Litre', 3, 1320.00, 3, 1),
(24, 11, 22, 500, 'Millilitre', 3, 180.00, 3, 1),
(25, 11, 22, 1, 'Litre', 3, 355.00, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `BrandID` int(11) NOT NULL,
  `CompanyID` int(11) NOT NULL,
  `FoodTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `ProductName`, `BrandID`, `CompanyID`, `FoodTypeID`) VALUES
(1, 'N/A', 1, 1, 1),
(2, 'Rupchanda Soyabean Oil', 2, 4, 2),
(3, 'Fortune Fortified Rice Bran Oil', 3, 4, 11),
(4, 'Fortune Kachi Ghani Pure Mustard Oil', 3, 4, 7),
(5, 'Kings Sunflower Oil', 4, 4, 4),
(6, 'Veola Fortified Soyabean Oil', 5, 4, 7),
(7, 'Meizan Super Palm Oilein', 6, 4, 3),
(8, 'Fresh Fortified Soyabean Oil', 7, 14, 2),
(9, 'Fresh Actifit Mustard Oil', 58, 14, 7),
(10, 'Teer Fortified Soyabean Oil', 8, 7, 2),
(11, 'Pran Mustard Oil', 11, 22, 7);

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE `reference` (
  `ReferenceID` int(11) NOT NULL,
  `ReferenceNumber` int(11) NOT NULL,
  `Source` varchar(255) NOT NULL,
  `Link` varchar(255) DEFAULT NULL,
  `ProcessToObtainData` varchar(255) DEFAULT NULL,
  `AccessDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `reference`
--

INSERT INTO `reference` (`ReferenceID`, `ReferenceNumber`, `Source`, `Link`, `ProcessToObtainData`, `AccessDate`) VALUES
(1, 1, 'N/A', 'N/A', 'N/A', '0000-00-00'),
(2, 2, 'BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/b343a8b4_956b_45ca_872f_4cf9b2f1a6e0/2024-01-31-15-51-b53c55dd692233ae401ba013060b9cbb.pdf', 'Go to Link> Open File> GTo to Table 3.2.6', '0000-00-00'),
(3, 3, 'Faostat', 'Link C', 'N/A', '0000-00-00'),
(4, 3, 'BBS', 'Link D', 'N/A', '0000-00-00'),
(5, 4, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(6, 5, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(7, 6, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(8, 7, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(9, 8, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(10, 9, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(11, 10, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(12, 11, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nAnnual Import Quantity >\\nItems Filter: soybean oil> Year: 2015-2024', '2011-04-24'),
(13, 12, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(14, 13, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(15, 14, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(16, 15, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(17, 16, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(18, 17, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(19, 18, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(20, 19, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: sunflower oil> Year: 2015-2024', '2011-04-24'),
(21, 20, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: mustard oil> Year: 2015-2024', '2011-04-24'),
(22, 21, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(23, 22, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(24, 23, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(25, 24, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(26, 25, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(27, 26, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(28, 27, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: palm oil> Year: 2015-2024', '2011-04-24'),
(29, 28, 'Oilseeds and Products Annual GAIN Report - USDA', 'https://apps.fas.usda.gov/newgainapi/api/Report/DownloadReportByFileName?fileName=Oilseeds%20and%20Products%20Annual_Dhaka_Bangladesh_BG2023-0007', 'BBS Homepage > Publication > Foreign Trade Statistics > Yearly Report> Volume 2> Part 2', '2011-04-24'),
(30, 29, 'Oilseeds and Products Annual GAIN Report - USDA', 'https://apps.fas.usda.gov/newgainapi/api/Report/DownloadReportByFileName?fileName=Oilseeds%20and%20Products%20Annual_Dhaka_Bangladesh_BG2023-0007', 'BBS Homepage > Publication > Foreign Trade Statistics > Yearly Report> Volume 2> Part 2', '2011-04-24'),
(31, 30, 'Oilseeds and Products Annual GAIN Report - USDA', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/8643ec8b_27a3_41cd_bbd9_9be3479f578e/2023-05-28-05-50-77ecbebf784c7504c5fc839be6ab707e.pdf', 'BBS Homepage > Publication > Foreign Trade Statistics > Yearly Report> Volume 2> Part 2', '2011-04-24'),
(32, 31, 'Oilseeds and Products Annual GAIN Report - USDA', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/8643ec8b_27a3_41cd_bbd9_9be3479f578e/2023-05-28-05-50-77ecbebf784c7504c5fc839be6ab707e.pdf', 'BBS Homepage > Publication > Foreign Trade Statistics > Yearly Report> Volume 2> Part 2', '2011-04-24'),
(33, 32, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(34, 33, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(35, 34, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(36, 35, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(37, 36, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(38, 37, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(39, 38, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(40, 39, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: coconut oil> Year: 2015-2024', '2011-04-24'),
(41, 40, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(42, 41, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(43, 42, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(44, 43, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(45, 44, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(46, 45, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(47, 46, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(48, 47, 'Faostat', 'https://www.fao.org/faostat/en/#data/FBS', 'FAOSTAT Homepage > Data\\nRibbon > Food Balance Sheets > Country Filter:\\nBangladesh > Element Filter:\\nImport Quantity >\\nItems Filter: Olive oil > Year: 2015-2024', '2011-04-24'),
(49, 48, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(50, 49, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(51, 50, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(52, 51, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(53, 52, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(54, 53, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(55, 54, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(56, 55, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(57, 56, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(58, 57, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(59, 58, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(60, 59, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(61, 60, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(62, 61, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2018-10-24'),
(63, 62, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(64, 63, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(65, 64, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(66, 65, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(67, 66, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(68, 67, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(69, 68, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(70, 69, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(71, 70, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(72, 71, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(73, 72, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(74, 73, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(75, 74, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(76, 75, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(77, 76, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(78, 77, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2016-10-24'),
(79, 78, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2017-10-24'),
(80, 79, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2018-10-24'),
(81, 80, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(82, 81, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(83, 82, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(84, 83, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2019-10-24'),
(85, 84, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2020-10-24'),
(86, 85, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2021-10-24'),
(87, 86, 'Yearbook of Agriculture Statistics 2023 - BBS', 'https://bbs.portal.gov.bd/sites/default/files/files/bbs.portal.gov.bd/page/1b1eb817_9325_4354_a756_3d18412203e2/2024-06-13-05-41-8d348db80ecf814b6f1876432643639e.pdf', 'BBS Homepage > Publication > Regular Publication > Yearbook of Agriculture Statistics by Year > Go to \\\"National estimate of different Oilseeds\\\" section of the report.', '2022-10-24'),
(88, 87, 'N/A', 'https://www.gainhealth.org/sites/default/files/publications/documents/assessment-of-presence-of-edible-oil-brands-in-bangladesh-2017.pdf', 'Search prompt \\\"GAIN report on Assessment of presence of edible oil brands in Bangladesh\\\"', '2020-10-24'),
(89, 88, 'N/A', 'https://www.gainhealth.org/sites/default/files/publications/documents/assessment-of-presence-of-edible-oil-brands-in-bangladesh-2017.pdf', 'Search prompt \\\"GAIN report on Assessment of presence of edible oil brands in Bangladesh\\\"', '2020-10-24');

-- --------------------------------------------------------

--
-- Table structure for table `subdistributionchannel`
--

CREATE TABLE `subdistributionchannel` (
  `SubDistributionChannelID` int(11) NOT NULL,
  `SubDistributionChannelName` varchar(255) NOT NULL,
  `DistributionChannelID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subdistributionchannel`
--

INSERT INTO `subdistributionchannel` (`SubDistributionChannelID`, `SubDistributionChannelName`, `DistributionChannelID`) VALUES
(1, 'N/A', 1),
(2, 'Retail Shop', 2),
(3, 'Wholesale', 2),
(4, 'Supermarket', 2),
(5, 'Government Ration', 3),
(6, 'OMS', 3),
(7, 'Bakery', 4),
(8, 'Hotels', 4),
(9, 'Restaurants', 4),
(10, 'Fast Food', 4),
(11, 'Small 2nd level Processing Manufacturing', 5),
(12, 'Large Manufacturing', 5);

-- --------------------------------------------------------

--
-- Table structure for table `supply`
--

CREATE TABLE `supply` (
  `SupplyID` int(11) NOT NULL,
  `VehicleID` int(11) DEFAULT NULL,
  `CountryID` int(11) DEFAULT NULL,
  `FoodTypeID` int(11) DEFAULT NULL,
  `PSID` int(11) DEFAULT NULL,
  `Origin` varchar(255) DEFAULT NULL,
  `EntityID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `ProducerReferenceID` int(11) DEFAULT NULL,
  `UCID` int(11) DEFAULT NULL,
  `SourceVolume` decimal(20,3) DEFAULT NULL,
  `VolumeMTY` decimal(20,3) DEFAULT NULL,
  `CropToFirstProcessedFoodStageConvertedValue` decimal(20,3) DEFAULT NULL,
  `YearTypeID` int(11) DEFAULT NULL,
  `StartYear` int(4) DEFAULT NULL,
  `EndYear` int(4) DEFAULT NULL,
  `ReferenceID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supply`
--

INSERT INTO `supply` (`SupplyID`, `VehicleID`, `CountryID`, `FoodTypeID`, `PSID`, `Origin`, `EntityID`, `ProductID`, `ProducerReferenceID`, `UCID`, `SourceVolume`, `VolumeMTY`, `CropToFirstProcessedFoodStageConvertedValue`, `YearTypeID`, `StartYear`, `EndYear`, `ReferenceID`) VALUES
(1, 2, 2, 6, 3, '0', 1, 1, 1, 1, 3243408.000, 3243408.000, 567596.400, 3, 2020, 2021, 49),
(2, 2, 2, 6, 3, '0', 1, 1, 1, 1, 3106023.000, 3106023.000, 543554.025, 3, 2021, 2022, 50),
(3, 2, 2, 6, 3, '0', 1, 1, 1, 1, 3071468.000, 3071468.000, 537506.900, 3, 2022, 2023, 51),
(4, 2, 2, 7, 3, '0', 1, 1, 1, 1, 39659428.000, 39659428.000, 6940399.900, 3, 2020, 2021, 52),
(5, 2, 2, 7, 3, '0', 1, 1, 1, 1, 40965906.000, 40965906.000, 7169033.550, 3, 2021, 2022, 53),
(6, 2, 2, 7, 3, '0', 1, 1, 1, 1, 54742532.000, 54742532.000, 9579943.100, 3, 2022, 2023, 54),
(7, 2, 2, 8, 3, '0', 1, 1, 1, 1, 6674541.000, 6674541.000, 1168044.675, 3, 2020, 2021, 55),
(8, 2, 2, 8, 3, '0', 1, 1, 1, 1, 7474867.000, 7474867.000, 1308101.725, 3, 2021, 2022, 56),
(9, 2, 2, 8, 3, '0', 1, 1, 1, 1, 7503085.000, 7503085.000, 1313039.875, 3, 2022, 2023, 57),
(10, 2, 2, 2, 3, '0', 1, 1, 1, 1, 9117659.000, 9117659.000, 1595590.325, 3, 2020, 2021, 58),
(11, 2, 2, 2, 3, '0', 1, 1, 1, 1, 9864606.000, 9864606.000, 1726306.050, 3, 2021, 2022, 59),
(12, 2, 2, 2, 3, '0', 1, 1, 1, 1, 10730759.000, 10730759.000, 1877882.825, 3, 2022, 2023, 60),
(13, 2, 2, 5, 3, '0', 1, 1, 1, 1, 40285200.000, 40285200.000, 7049910.000, 3, 2021, 2022, 61),
(14, 2, 2, 5, 3, '0', 1, 1, 1, 1, 41196956.000, 41196956.000, 7209467.300, 3, 2022, 2023, 62),
(15, 2, 2, 5, 3, '0', 1, 1, 1, 1, 40365765.000, 40365765.000, 7064008.875, 3, 2022, 2023, 63),
(16, 2, 2, 4, 3, '0', 1, 1, 1, 1, 200620.000, 200620.000, 35108.500, 3, 2021, 2022, 64),
(17, 2, 2, 4, 3, '0', 1, 1, 1, 1, 272899.000, 272899.000, 47757.325, 3, 2022, 2023, 65),
(18, 2, 2, 4, 3, '0', 1, 1, 1, 1, 362211.000, 362211.000, 63386.925, 3, 2022, 2023, 66);

-- --------------------------------------------------------

--
-- Table structure for table `supply_in_chain_final`
--

CREATE TABLE `supply_in_chain_final` (
  `SupplyID` int(11) DEFAULT NULL,
  `SupplyCountry` varchar(255) DEFAULT NULL,
  `FoodTypeName` varchar(255) DEFAULT NULL,
  `ProcessingStageName` varchar(255) DEFAULT NULL,
  `Origin` varchar(255) DEFAULT NULL,
  `ProducerProcessorName` varchar(255) DEFAULT NULL,
  `ProductName` varchar(255) DEFAULT NULL,
  `IdentifierNumber` varchar(255) DEFAULT NULL,
  `SupplyVolumeUnit` varchar(50) DEFAULT NULL,
  `SupplyPeriodicalUnit` varchar(50) DEFAULT NULL,
  `SupplySourceVolume` decimal(20,3) DEFAULT NULL,
  `SupplyVolumeMTY` decimal(20,3) DEFAULT NULL,
  `CropToFirstProcessedFoodStageConvertedValue` decimal(20,3) DEFAULT NULL,
  `SupplyYearType` varchar(255) DEFAULT NULL,
  `SupplyStartYear` int(4) DEFAULT NULL,
  `SupplyEndYear` int(4) DEFAULT NULL,
  `SupplyReferenceNumber` int(11) DEFAULT NULL,
  `SupplySource` varchar(255) DEFAULT NULL,
  `SupplyLink` varchar(255) DEFAULT NULL,
  `SupplyProcessToObtainData` varchar(255) DEFAULT NULL,
  `SupplyAccessDate` date DEFAULT NULL,
  `DistributionID` int(11) DEFAULT NULL,
  `DistributionChannelName` varchar(255) DEFAULT NULL,
  `SubDistributionChannelName` varchar(255) DEFAULT NULL,
  `DistributionVehicleName` varchar(255) DEFAULT NULL,
  `DistributionVolumeUnit` varchar(50) DEFAULT NULL,
  `DistributionPeriodicalUnit` varchar(50) DEFAULT NULL,
  `DistributionSourceVolume` decimal(10,2) DEFAULT NULL,
  `DistributionVolumeMTY` decimal(10,2) DEFAULT NULL,
  `DistributionCountry` varchar(255) DEFAULT NULL,
  `DistributionYearType` varchar(255) DEFAULT NULL,
  `DistributionStartYear` int(4) DEFAULT NULL,
  `DistributionEndYear` int(4) DEFAULT NULL,
  `DistributionReferenceNumber` int(11) DEFAULT NULL,
  `DistributionSource` varchar(255) DEFAULT NULL,
  `DistributionLink` varchar(255) DEFAULT NULL,
  `DistributionProcessToObtainData` varchar(255) DEFAULT NULL,
  `DistributionAccessDate` date DEFAULT NULL,
  `ConsumptionID` int(11) DEFAULT NULL,
  `ConsumptionVehicleName` varchar(255) DEFAULT NULL,
  `AdminLevel1` varchar(50) DEFAULT NULL,
  `AdminLevel2` varchar(50) DEFAULT NULL,
  `AdminLevel3` varchar(50) DEFAULT NULL,
  `GenderName` varchar(50) DEFAULT NULL,
  `AgeRange` varchar(50) DEFAULT NULL,
  `NumberOfPeople` int(11) DEFAULT NULL,
  `ConsumptionVolumeUnit` varchar(50) DEFAULT NULL,
  `ConsumptionPeriodicalUnit` varchar(50) DEFAULT NULL,
  `ConsumptionSourceVolume` decimal(20,4) DEFAULT NULL,
  `ConsumptionVolumeMTY` decimal(20,4) DEFAULT NULL,
  `ConsumptionYearType` varchar(255) DEFAULT NULL,
  `ConsumptionStartYear` int(11) DEFAULT NULL,
  `ConsumptionEndYear` int(11) DEFAULT NULL,
  `ConsumptionReferenceNumber` int(11) DEFAULT NULL,
  `ConsumptionSource` varchar(255) DEFAULT NULL,
  `ConsumptionLink` varchar(255) DEFAULT NULL,
  `ConsumptionProcessToObtainData` varchar(255) DEFAULT NULL,
  `ConsumptionAccessDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `yeartype`
--

CREATE TABLE `yeartype` (
  `YearTypeID` int(11) NOT NULL,
  `YearTypeName` varchar(255) NOT NULL,
  `StartMonth` varchar(50) NOT NULL,
  `EndMonth` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `yeartype`
--

INSERT INTO `yeartype` (`YearTypeID`, `YearTypeName`, `StartMonth`, `EndMonth`) VALUES
(1, 'N/A', 'N/A', 'N/A'),
(2, 'Year (Jan-Dec)', 'January', 'December'),
(3, 'Year(Jun-July)', 'June', 'July'),
(4, 'Year (Mar-Apr)', 'March', 'April');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adultmaleequivalent`
--
ALTER TABLE `adultmaleequivalent`
  ADD PRIMARY KEY (`AMEID`),
  ADD KEY `GenderID` (`GenderID`),
  ADD KEY `AgeID` (`AgeID`);

--
-- Indexes for table `age`
--
ALTER TABLE `age`
  ADD PRIMARY KEY (`AgeID`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`BrandID`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`CompanyID`);

--
-- Indexes for table `consumption`
--
ALTER TABLE `consumption`
  ADD PRIMARY KEY (`ConsumptionID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `GL1ID` (`GL1ID`),
  ADD KEY `GL2ID` (`GL2ID`),
  ADD KEY `GL3ID` (`GL3ID`),
  ADD KEY `GenderID` (`GenderID`),
  ADD KEY `AgeID` (`AgeID`),
  ADD KEY `UCID` (`UCID`),
  ADD KEY `YearTypeID` (`YearTypeID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`CountryID`);

--
-- Indexes for table `distribution`
--
ALTER TABLE `distribution`
  ADD PRIMARY KEY (`DistributionID`),
  ADD KEY `DistributionChannelID` (`DistributionChannelID`),
  ADD KEY `SubDistributionChannelID` (`SubDistributionChannelID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `UCID` (`UCID`),
  ADD KEY `CountryID` (`CountryID`),
  ADD KEY `YearTypeID` (`YearTypeID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `distributionchannel`
--
ALTER TABLE `distributionchannel`
  ADD PRIMARY KEY (`DistributionChannelID`);

--
-- Indexes for table `entity`
--
ALTER TABLE `entity`
  ADD PRIMARY KEY (`EntityID`),
  ADD KEY `CompanyID` (`CompanyID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `GL1ID` (`GL1ID`),
  ADD KEY `GL2ID` (`GL2ID`),
  ADD KEY `GL3ID` (`GL3ID`),
  ADD KEY `CountryID` (`CountryID`);

--
-- Indexes for table `extractionconversion`
--
ALTER TABLE `extractionconversion`
  ADD PRIMARY KEY (`ExtractionID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `FoodTypeID` (`FoodTypeID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `foodtype`
--
ALTER TABLE `foodtype`
  ADD PRIMARY KEY (`FoodTypeID`),
  ADD KEY `VehicleID` (`VehicleID`);

--
-- Indexes for table `foodvehicle`
--
ALTER TABLE `foodvehicle`
  ADD PRIMARY KEY (`VehicleID`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`GenderID`);

--
-- Indexes for table `geographylevel1`
--
ALTER TABLE `geographylevel1`
  ADD PRIMARY KEY (`GL1ID`),
  ADD KEY `CountryID` (`CountryID`);

--
-- Indexes for table `geographylevel2`
--
ALTER TABLE `geographylevel2`
  ADD PRIMARY KEY (`GL2ID`),
  ADD KEY `GL1ID` (`GL1ID`);

--
-- Indexes for table `geographylevel3`
--
ALTER TABLE `geographylevel3`
  ADD PRIMARY KEY (`GL3ID`),
  ADD KEY `GL2ID` (`GL2ID`);

--
-- Indexes for table `individualconsumption`
--
ALTER TABLE `individualconsumption`
  ADD PRIMARY KEY (`ConsumptionID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `GenderID` (`GenderID`),
  ADD KEY `AgeID` (`AgeID`),
  ADD KEY `UCID` (`UCID`),
  ADD KEY `YearTypeID` (`YearTypeID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `measurecurrency`
--
ALTER TABLE `measurecurrency`
  ADD PRIMARY KEY (`MCID`);

--
-- Indexes for table `measureunit1`
--
ALTER TABLE `measureunit1`
  ADD PRIMARY KEY (`UCID`);

--
-- Indexes for table `packagingtype`
--
ALTER TABLE `packagingtype`
  ADD PRIMARY KEY (`PackagingTypeID`);

--
-- Indexes for table `processingstage`
--
ALTER TABLE `processingstage`
  ADD PRIMARY KEY (`PSID`),
  ADD KEY `VehicleID` (`VehicleID`);

--
-- Indexes for table `producerprocessor`
--
ALTER TABLE `producerprocessor`
  ADD PRIMARY KEY (`ProducerProcessorID`),
  ADD KEY `EntityID` (`EntityID`),
  ADD KEY `ProducerReferenceID` (`ProducerReferenceID`);

--
-- Indexes for table `producerreference`
--
ALTER TABLE `producerreference`
  ADD PRIMARY KEY (`ProducerReferenceID`),
  ADD KEY `CompanyID` (`CompanyID`),
  ADD KEY `CountryID` (`CountryID`);

--
-- Indexes for table `producersku`
--
ALTER TABLE `producersku`
  ADD PRIMARY KEY (`SKUID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `CompanyID` (`CompanyID`),
  ADD KEY `PackagingTypeID` (`PackagingTypeID`),
  ADD KEY `CurrencyID` (`CurrencyID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `BrandID` (`BrandID`),
  ADD KEY `CompanyID` (`CompanyID`),
  ADD KEY `FoodTypeID` (`FoodTypeID`);

--
-- Indexes for table `reference`
--
ALTER TABLE `reference`
  ADD PRIMARY KEY (`ReferenceID`);

--
-- Indexes for table `subdistributionchannel`
--
ALTER TABLE `subdistributionchannel`
  ADD PRIMARY KEY (`SubDistributionChannelID`),
  ADD KEY `DistributionChannelID` (`DistributionChannelID`);

--
-- Indexes for table `supply`
--
ALTER TABLE `supply`
  ADD PRIMARY KEY (`SupplyID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `CountryID` (`CountryID`),
  ADD KEY `FoodTypeID` (`FoodTypeID`),
  ADD KEY `PSID` (`PSID`),
  ADD KEY `EntityID` (`EntityID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `ProducerReferenceID` (`ProducerReferenceID`),
  ADD KEY `UCID` (`UCID`),
  ADD KEY `YearTypeID` (`YearTypeID`),
  ADD KEY `ReferenceID` (`ReferenceID`);

--
-- Indexes for table `yeartype`
--
ALTER TABLE `yeartype`
  ADD PRIMARY KEY (`YearTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adultmaleequivalent`
--
ALTER TABLE `adultmaleequivalent`
  MODIFY `AMEID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `age`
--
ALTER TABLE `age`
  MODIFY `AgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `BrandID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `CompanyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `consumption`
--
ALTER TABLE `consumption`
  MODIFY `ConsumptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `CountryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `distribution`
--
ALTER TABLE `distribution`
  MODIFY `DistributionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `distributionchannel`
--
ALTER TABLE `distributionchannel`
  MODIFY `DistributionChannelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `entity`
--
ALTER TABLE `entity`
  MODIFY `EntityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `extractionconversion`
--
ALTER TABLE `extractionconversion`
  MODIFY `ExtractionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `foodtype`
--
ALTER TABLE `foodtype`
  MODIFY `FoodTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `foodvehicle`
--
ALTER TABLE `foodvehicle`
  MODIFY `VehicleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gender`
--
ALTER TABLE `gender`
  MODIFY `GenderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `geographylevel1`
--
ALTER TABLE `geographylevel1`
  MODIFY `GL1ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `geographylevel2`
--
ALTER TABLE `geographylevel2`
  MODIFY `GL2ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `geographylevel3`
--
ALTER TABLE `geographylevel3`
  MODIFY `GL3ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT for table `individualconsumption`
--
ALTER TABLE `individualconsumption`
  MODIFY `ConsumptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `measurecurrency`
--
ALTER TABLE `measurecurrency`
  MODIFY `MCID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `measureunit1`
--
ALTER TABLE `measureunit1`
  MODIFY `UCID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `packagingtype`
--
ALTER TABLE `packagingtype`
  MODIFY `PackagingTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `processingstage`
--
ALTER TABLE `processingstage`
  MODIFY `PSID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `producerprocessor`
--
ALTER TABLE `producerprocessor`
  MODIFY `ProducerProcessorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `producerreference`
--
ALTER TABLE `producerreference`
  MODIFY `ProducerReferenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `producersku`
--
ALTER TABLE `producersku`
  MODIFY `SKUID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reference`
--
ALTER TABLE `reference`
  MODIFY `ReferenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `subdistributionchannel`
--
ALTER TABLE `subdistributionchannel`
  MODIFY `SubDistributionChannelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `supply`
--
ALTER TABLE `supply`
  MODIFY `SupplyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `yeartype`
--
ALTER TABLE `yeartype`
  MODIFY `YearTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adultmaleequivalent`
--
ALTER TABLE `adultmaleequivalent`
  ADD CONSTRAINT `adultmaleequivalent_ibfk_1` FOREIGN KEY (`GenderID`) REFERENCES `gender` (`GenderID`),
  ADD CONSTRAINT `adultmaleequivalent_ibfk_2` FOREIGN KEY (`AgeID`) REFERENCES `age` (`AgeID`);

--
-- Constraints for table `consumption`
--
ALTER TABLE `consumption`
  ADD CONSTRAINT `consumption_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `consumption_ibfk_2` FOREIGN KEY (`GL1ID`) REFERENCES `geographylevel1` (`GL1ID`),
  ADD CONSTRAINT `consumption_ibfk_3` FOREIGN KEY (`GL2ID`) REFERENCES `geographylevel2` (`GL2ID`),
  ADD CONSTRAINT `consumption_ibfk_4` FOREIGN KEY (`GL3ID`) REFERENCES `geographylevel3` (`GL3ID`),
  ADD CONSTRAINT `consumption_ibfk_5` FOREIGN KEY (`GenderID`) REFERENCES `gender` (`GenderID`),
  ADD CONSTRAINT `consumption_ibfk_6` FOREIGN KEY (`AgeID`) REFERENCES `age` (`AgeID`),
  ADD CONSTRAINT `consumption_ibfk_7` FOREIGN KEY (`UCID`) REFERENCES `measureunit1` (`UCID`),
  ADD CONSTRAINT `consumption_ibfk_8` FOREIGN KEY (`YearTypeID`) REFERENCES `yeartype` (`YearTypeID`),
  ADD CONSTRAINT `consumption_ibfk_9` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`);

--
-- Constraints for table `distribution`
--
ALTER TABLE `distribution`
  ADD CONSTRAINT `distribution_ibfk_1` FOREIGN KEY (`DistributionChannelID`) REFERENCES `distributionchannel` (`DistributionChannelID`),
  ADD CONSTRAINT `distribution_ibfk_2` FOREIGN KEY (`SubDistributionChannelID`) REFERENCES `subdistributionchannel` (`SubDistributionChannelID`),
  ADD CONSTRAINT `distribution_ibfk_3` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `distribution_ibfk_4` FOREIGN KEY (`UCID`) REFERENCES `measureunit1` (`UCID`),
  ADD CONSTRAINT `distribution_ibfk_5` FOREIGN KEY (`CountryID`) REFERENCES `country` (`CountryID`),
  ADD CONSTRAINT `distribution_ibfk_6` FOREIGN KEY (`YearTypeID`) REFERENCES `yeartype` (`YearTypeID`),
  ADD CONSTRAINT `distribution_ibfk_7` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`);

--
-- Constraints for table `entity`
--
ALTER TABLE `entity`
  ADD CONSTRAINT `entity_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`),
  ADD CONSTRAINT `entity_ibfk_2` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `entity_ibfk_3` FOREIGN KEY (`GL1ID`) REFERENCES `geographylevel1` (`GL1ID`),
  ADD CONSTRAINT `entity_ibfk_4` FOREIGN KEY (`GL2ID`) REFERENCES `geographylevel2` (`GL2ID`),
  ADD CONSTRAINT `entity_ibfk_5` FOREIGN KEY (`GL3ID`) REFERENCES `geographylevel3` (`GL3ID`),
  ADD CONSTRAINT `entity_ibfk_6` FOREIGN KEY (`CountryID`) REFERENCES `country` (`CountryID`);

--
-- Constraints for table `extractionconversion`
--
ALTER TABLE `extractionconversion`
  ADD CONSTRAINT `extractionconversion_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `extractionconversion_ibfk_2` FOREIGN KEY (`FoodTypeID`) REFERENCES `foodtype` (`FoodTypeID`),
  ADD CONSTRAINT `extractionconversion_ibfk_3` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`);

--
-- Constraints for table `foodtype`
--
ALTER TABLE `foodtype`
  ADD CONSTRAINT `foodtype_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`);

--
-- Constraints for table `geographylevel1`
--
ALTER TABLE `geographylevel1`
  ADD CONSTRAINT `geographylevel1_ibfk_1` FOREIGN KEY (`CountryID`) REFERENCES `country` (`CountryID`);

--
-- Constraints for table `geographylevel2`
--
ALTER TABLE `geographylevel2`
  ADD CONSTRAINT `geographylevel2_ibfk_1` FOREIGN KEY (`GL1ID`) REFERENCES `geographylevel1` (`GL1ID`);

--
-- Constraints for table `geographylevel3`
--
ALTER TABLE `geographylevel3`
  ADD CONSTRAINT `geographylevel3_ibfk_1` FOREIGN KEY (`GL2ID`) REFERENCES `geographylevel2` (`GL2ID`);

--
-- Constraints for table `individualconsumption`
--
ALTER TABLE `individualconsumption`
  ADD CONSTRAINT `individualconsumption_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `individualconsumption_ibfk_2` FOREIGN KEY (`GenderID`) REFERENCES `gender` (`GenderID`),
  ADD CONSTRAINT `individualconsumption_ibfk_3` FOREIGN KEY (`AgeID`) REFERENCES `age` (`AgeID`),
  ADD CONSTRAINT `individualconsumption_ibfk_4` FOREIGN KEY (`UCID`) REFERENCES `measureunit1` (`UCID`),
  ADD CONSTRAINT `individualconsumption_ibfk_5` FOREIGN KEY (`YearTypeID`) REFERENCES `yeartype` (`YearTypeID`),
  ADD CONSTRAINT `individualconsumption_ibfk_6` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`);

--
-- Constraints for table `processingstage`
--
ALTER TABLE `processingstage`
  ADD CONSTRAINT `processingstage_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`);

--
-- Constraints for table `producerprocessor`
--
ALTER TABLE `producerprocessor`
  ADD CONSTRAINT `producerprocessor_ibfk_1` FOREIGN KEY (`EntityID`) REFERENCES `entity` (`EntityID`),
  ADD CONSTRAINT `producerprocessor_ibfk_2` FOREIGN KEY (`ProducerReferenceID`) REFERENCES `producerreference` (`ProducerReferenceID`);

--
-- Constraints for table `producerreference`
--
ALTER TABLE `producerreference`
  ADD CONSTRAINT `producerreference_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`),
  ADD CONSTRAINT `producerreference_ibfk_2` FOREIGN KEY (`CountryID`) REFERENCES `country` (`CountryID`);

--
-- Constraints for table `producersku`
--
ALTER TABLE `producersku`
  ADD CONSTRAINT `producersku_ibfk_1` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`),
  ADD CONSTRAINT `producersku_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`),
  ADD CONSTRAINT `producersku_ibfk_3` FOREIGN KEY (`PackagingTypeID`) REFERENCES `packagingtype` (`PackagingTypeID`),
  ADD CONSTRAINT `producersku_ibfk_4` FOREIGN KEY (`CurrencyID`) REFERENCES `measurecurrency` (`MCID`),
  ADD CONSTRAINT `producersku_ibfk_5` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`BrandID`) REFERENCES `brand` (`BrandID`),
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`),
  ADD CONSTRAINT `product_ibfk_3` FOREIGN KEY (`FoodTypeID`) REFERENCES `foodtype` (`FoodTypeID`);

--
-- Constraints for table `subdistributionchannel`
--
ALTER TABLE `subdistributionchannel`
  ADD CONSTRAINT `subdistributionchannel_ibfk_1` FOREIGN KEY (`DistributionChannelID`) REFERENCES `distributionchannel` (`DistributionChannelID`);

--
-- Constraints for table `supply`
--
ALTER TABLE `supply`
  ADD CONSTRAINT `supply_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `foodvehicle` (`VehicleID`),
  ADD CONSTRAINT `supply_ibfk_10` FOREIGN KEY (`ReferenceID`) REFERENCES `reference` (`ReferenceID`),
  ADD CONSTRAINT `supply_ibfk_2` FOREIGN KEY (`CountryID`) REFERENCES `country` (`CountryID`),
  ADD CONSTRAINT `supply_ibfk_3` FOREIGN KEY (`FoodTypeID`) REFERENCES `foodtype` (`FoodTypeID`),
  ADD CONSTRAINT `supply_ibfk_4` FOREIGN KEY (`PSID`) REFERENCES `processingstage` (`PSID`),
  ADD CONSTRAINT `supply_ibfk_5` FOREIGN KEY (`EntityID`) REFERENCES `entity` (`EntityID`),
  ADD CONSTRAINT `supply_ibfk_6` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`),
  ADD CONSTRAINT `supply_ibfk_7` FOREIGN KEY (`ProducerReferenceID`) REFERENCES `producerreference` (`ProducerReferenceID`),
  ADD CONSTRAINT `supply_ibfk_8` FOREIGN KEY (`UCID`) REFERENCES `measureunit1` (`UCID`),
  ADD CONSTRAINT `supply_ibfk_9` FOREIGN KEY (`YearTypeID`) REFERENCES `yeartype` (`YearTypeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
