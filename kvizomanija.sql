-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2025 at 12:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kvizomanija`
--

-- --------------------------------------------------------

--
-- Table structure for table `admini`
--

CREATE TABLE `admini` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `sifra` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admini`
--

INSERT INTO `admini` (`admin_id`, `email`, `sifra`) VALUES
(1, 'muslicj.007@gmail.com', '$2y$10$mPPNW9pFijOXIElAqaVyau0AH4MY1bp4yaz4js9ed5snIz.Do5FP.');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `korisnik_id` int(11) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifra` varchar(255) NOT NULL,
  `razred` varchar(10) NOT NULL,
  `datum_registracije` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`korisnik_id`, `korisnicko_ime`, `email`, `sifra`, `razred`, `datum_registracije`) VALUES
(1, 'emel', 'emel@kviz.ba', '$2y$10$ih1UkH6ZT72KKTOZwR0pWuJXd7UPSfMSzB3aj5X40lGDqy3KO3SlC', 'IV', '2025-12-19 21:16:04'),
(3, 'murat', 'murat@kviz.ba', '$2y$10$7qC.uY1TJ0K6pWJHbuvSgeKo7gGcd8HxjXtS8g5s3ZF/Af1Uv8gJ.', 'I', '2025-12-19 22:33:08');

-- --------------------------------------------------------

--
-- Table structure for table `kvizovi`
--

CREATE TABLE `kvizovi` (
  `kviz_id` int(11) NOT NULL,
  `naziv_kviza` varchar(100) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `pocetak` datetime DEFAULT NULL,
  `rok_predaje` datetime DEFAULT NULL,
  `vremensko_ogranicenje` int(11) NOT NULL DEFAULT 15,
  `broj_pitanja` int(11) NOT NULL DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kvizovi`
--

INSERT INTO `kvizovi` (`kviz_id`, `naziv_kviza`, `admin_id`, `pocetak`, `rok_predaje`, `vremensko_ogranicenje`, `broj_pitanja`) VALUES
(1, 'Opće znanje', 1, NULL, NULL, 15, 15),
(2, 'IT & Tehnologija', 1, NULL, NULL, 15, 15),
(3, 'Sport', 1, NULL, NULL, 15, 15);

-- --------------------------------------------------------

--
-- Table structure for table `odgovori`
--

CREATE TABLE `odgovori` (
  `odgovor_id` int(11) NOT NULL,
  `pitanje_id` int(11) DEFAULT NULL,
  `tekst_odgovora` text DEFAULT NULL,
  `tacan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pitanja`
--

CREATE TABLE `pitanja` (
  `pitanje_id` int(11) NOT NULL,
  `kviz_id` int(11) DEFAULT NULL,
  `tekst_pitanja` text DEFAULT NULL,
  `opcija_a` text DEFAULT NULL,
  `opcija_b` text DEFAULT NULL,
  `opcija_c` text DEFAULT NULL,
  `tacan_odgovor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezultati`
--

CREATE TABLE `rezultati` (
  `rezultat_id` int(11) NOT NULL,
  `kviz_id` int(11) DEFAULT NULL,
  `ime` varchar(20) DEFAULT NULL,
  `prezime` varchar(30) DEFAULT NULL,
  `razred_odjeljenje` varchar(4) DEFAULT NULL,
  `odgovori` text DEFAULT NULL,
  `vrijeme_zapoceto` timestamp NOT NULL DEFAULT current_timestamp(),
  `sekunde` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rezultati`
--

INSERT INTO `rezultati` (`rezultat_id`, `kviz_id`, `ime`, `prezime`, `razred_odjeljenje`, `odgovori`, `vrijeme_zapoceto`, `sekunde`) VALUES
(4, 1, 'emel', 'Započeto', 'Učen', NULL, '2025-12-19 22:13:25', 0),
(5, 1, 'emel', 'Započeto', 'Učen', NULL, '2025-12-19 22:13:25', 0),
(6, 1, 'haris', 'Započeto', 'Učen', NULL, '2025-12-19 22:16:32', 0),
(7, 1, 'emel', 'Započeto', 'Učen', NULL, '2025-12-19 22:42:56', 0),
(8, 1, 'emel', '6 / 15', 'Učen', NULL, '2025-12-19 22:57:25', 0),
(9, 2, 'emel', '6 / 15', 'Učen', NULL, '2025-12-19 23:00:21', 0),
(10, 3, 'emel', '3 / 15', 'Učen', NULL, '2025-12-19 23:01:07', 0),
(11, 1, 'murat', '10 / 15', 'Učen', NULL, '2025-12-20 13:43:19', 0),
(12, 2, 'murat', '2 / 15', 'Učen', NULL, '2025-12-20 13:47:46', 0),
(13, 2, 'murat', '6 / 15', 'Učen', NULL, '2025-12-20 14:16:15', 0),
(14, 2, 'murat', '8 / 15', 'Učen', NULL, '2025-12-20 14:19:16', 0),
(15, 2, 'murat', '6 / 15', 'Učen', NULL, '2025-12-20 14:19:54', 0),
(16, 2, 'murat', '6 / 15', 'Učen', NULL, '2025-12-20 14:22:20', 0),
(17, 2, 'murat', 'Započeto', 'Učen', NULL, '2025-12-20 14:27:35', 0),
(18, 2, 'murat', 'Započeto', 'Učen', NULL, '2025-12-20 14:28:09', 0),
(19, 2, 'murat', '6 / 15', 'Učen', NULL, '2025-12-20 14:29:30', 0),
(20, 2, 'murat', '5 / 15', 'Učen', NULL, '2025-12-20 14:31:12', 0),
(21, 2, 'murat', 'Započeto', 'Učen', NULL, '2025-12-20 14:33:08', 0),
(22, 2, 'murat', '0 / 3', 'Učen', NULL, '2025-12-20 14:34:17', 0),
(23, 2, 'murat', 'Započeto', 'Učen', NULL, '2025-12-20 14:34:26', 0),
(24, 2, 'emel', '2 / 5', 'Učen', NULL, '2025-12-20 21:59:04', 6),
(25, 2, 'emel', '3 / 5', 'Učen', NULL, '2025-12-20 21:59:16', 8),
(26, 2, 'emel', '3 / 5', 'Učen', NULL, '2025-12-20 21:59:54', 10),
(27, 2, 'emel', '3 / 5', 'Učen', NULL, '2025-12-20 22:00:28', 12),
(28, 2, 'emel', '0 / 5', 'Učen', NULL, '2025-12-20 22:06:11', 4),
(29, 2, 'emel', '2 / 5', 'Učen', NULL, '2025-12-20 22:07:11', 6),
(30, 2, 'emel', '3 / 5', 'Učen', NULL, '2025-12-20 22:09:00', 9),
(31, 2, 'ilija', '3 / 5', 'Učen', NULL, '2025-12-20 22:14:52', 8),
(32, 2, 'ilija', '9 / 15', 'Učen', NULL, '2025-12-20 22:17:08', 40),
(33, 1, 'ilija', '9 / 15', 'Učen', NULL, '2025-12-20 22:20:14', 41),
(34, 3, 'ilija', '7 / 15', 'Učen', NULL, '2025-12-20 22:22:13', 28),
(35, 2, 'ilija', '5 / 15', 'Učen', NULL, '2025-12-20 22:39:15', 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admini`
--
ALTER TABLE `admini`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`korisnik_id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `kvizovi`
--
ALTER TABLE `kvizovi`
  ADD PRIMARY KEY (`kviz_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `odgovori`
--
ALTER TABLE `odgovori`
  ADD PRIMARY KEY (`odgovor_id`),
  ADD KEY `pitanje_id` (`pitanje_id`);

--
-- Indexes for table `pitanja`
--
ALTER TABLE `pitanja`
  ADD PRIMARY KEY (`pitanje_id`),
  ADD KEY `kviz_id` (`kviz_id`);

--
-- Indexes for table `rezultati`
--
ALTER TABLE `rezultati`
  ADD PRIMARY KEY (`rezultat_id`),
  ADD KEY `kviz_id` (`kviz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admini`
--
ALTER TABLE `admini`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `korisnik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kvizovi`
--
ALTER TABLE `kvizovi`
  MODIFY `kviz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `odgovori`
--
ALTER TABLE `odgovori`
  MODIFY `odgovor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pitanja`
--
ALTER TABLE `pitanja`
  MODIFY `pitanje_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rezultati`
--
ALTER TABLE `rezultati`
  MODIFY `rezultat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kvizovi`
--
ALTER TABLE `kvizovi`
  ADD CONSTRAINT `kvizovi_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admini` (`admin_id`);

--
-- Constraints for table `odgovori`
--
ALTER TABLE `odgovori`
  ADD CONSTRAINT `odgovori_ibfk_1` FOREIGN KEY (`pitanje_id`) REFERENCES `pitanja` (`pitanje_id`);

--
-- Constraints for table `pitanja`
--
ALTER TABLE `pitanja`
  ADD CONSTRAINT `fk_pitanja_kviz` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pitanja_ibfk_1` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`);

--
-- Constraints for table `rezultati`
--
ALTER TABLE `rezultati`
  ADD CONSTRAINT `fk_rezultati_kviz` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezultati_ibfk_1` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
