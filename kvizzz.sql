-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 11:37 PM
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
-- Database: `kvizzz`
--

-- --------------------------------------------------------

--
-- Table structure for table `kvizovi`
--

CREATE TABLE `kvizovi` (
  `kviz_id` int(11) NOT NULL,
  `profesor_id` int(11) DEFAULT NULL,
  `naziv_kviza` varchar(100) DEFAULT NULL,
  `vrijeme_kreiranja` datetime NOT NULL DEFAULT current_timestamp(),
  `pocetak` datetime DEFAULT NULL,
  `rok_predaje` datetime DEFAULT NULL,
  `vremensko_ogranicenje` int(11) NOT NULL DEFAULT 15,
  `broj_pitanja` int(11) NOT NULL DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nalozi`
--

CREATE TABLE `nalozi` (
  `nalog_id` int(11) NOT NULL,
  `ime_prezime` varchar(100) NOT NULL,
  `pristup` enum('admin','ucenik','profesor') DEFAULT 'ucenik',
  `aktiviran` tinyint(1) NOT NULL DEFAULT 0,
  `razred_odjeljenje` varchar(4) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `sifra` varchar(100) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_timeout` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nalozi`
--

INSERT INTO `nalozi` (`nalog_id`, `ime_prezime`, `pristup`, `aktiviran`, `razred_odjeljenje`, `email`, `sifra`, `token`, `token_timeout`) VALUES
(1, 'Admin Nalog', 'admin', 1, NULL, 'admin@admin.com', '$2y$10$kEemvXJ3AMEgB3gEcsnhte1Yqyw7oOGT7nbEqg/pd/FWgLPul/im.', NULL, '2025-12-24 20:49:12'),
(2, 'Jasmin Muslić', 'ucenik', 0, 'I', 'muslicj.007@gmail.com', '$2y$10$Xv6wL0jBSd0vNoh2GY925OA1iNU0CwSyZCTk2yVvBjwW9mErlg62O', NULL, '2025-12-24 20:49:12');

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
  `tekst_pitanja` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezultati`
--

CREATE TABLE `rezultati` (
  `rezultat_id` int(11) NOT NULL,
  `ucenik_id` int(11) DEFAULT NULL,
  `kviz_id` int(11) DEFAULT NULL,
  `predano` datetime DEFAULT NULL,
  `vrijeme_izrade` time DEFAULT NULL,
  `odgovori_json` text DEFAULT NULL,
  `bodovi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kvizovi`
--
ALTER TABLE `kvizovi`
  ADD PRIMARY KEY (`kviz_id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indexes for table `nalozi`
--
ALTER TABLE `nalozi`
  ADD PRIMARY KEY (`nalog_id`),
  ADD UNIQUE KEY `email` (`email`);

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
  ADD KEY `ucenik_id` (`ucenik_id`),
  ADD KEY `kviz_id` (`kviz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kvizovi`
--
ALTER TABLE `kvizovi`
  MODIFY `kviz_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nalozi`
--
ALTER TABLE `nalozi`
  MODIFY `nalog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `odgovori`
--
ALTER TABLE `odgovori`
  MODIFY `odgovor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pitanja`
--
ALTER TABLE `pitanja`
  MODIFY `pitanje_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rezultati`
--
ALTER TABLE `rezultati`
  MODIFY `rezultat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kvizovi`
--
ALTER TABLE `kvizovi`
  ADD CONSTRAINT `kvizovi_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `nalozi` (`nalog_id`);

--
-- Constraints for table `odgovori`
--
ALTER TABLE `odgovori`
  ADD CONSTRAINT `odgovori_ibfk_1` FOREIGN KEY (`pitanje_id`) REFERENCES `pitanja` (`pitanje_id`) ON DELETE CASCADE;

--
-- Constraints for table `pitanja`
--
ALTER TABLE `pitanja`
  ADD CONSTRAINT `pitanja_ibfk_1` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`) ON DELETE CASCADE;

--
-- Constraints for table `rezultati`
--
ALTER TABLE `rezultati`
  ADD CONSTRAINT `rezultati_ibfk_1` FOREIGN KEY (`ucenik_id`) REFERENCES `nalozi` (`nalog_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezultati_ibfk_2` FOREIGN KEY (`kviz_id`) REFERENCES `kvizovi` (`kviz_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
