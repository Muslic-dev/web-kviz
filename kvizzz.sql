-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2025 at 12:53 PM
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
  `broj_pitanja` int(11) NOT NULL DEFAULT 15,
  `slika` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kvizovi`
--

INSERT INTO `kvizovi` (`kviz_id`, `profesor_id`, `naziv_kviza`, `vrijeme_kreiranja`, `pocetak`, `rok_predaje`, `vremensko_ogranicenje`, `broj_pitanja`, `slika`) VALUES
(1, 1, 'Opće znanje', '2025-12-26 11:18:14', NULL, NULL, 15, 15, 'default.jpg'),
(2, 1, 'IT & Tehnologija', '2025-12-26 11:18:14', NULL, NULL, 15, 15, 'default.jpg'),
(3, 1, 'Sport', '2025-12-26 11:18:14', NULL, NULL, 15, 15, 'default.jpg'),
(9, NULL, 'Geografija', '2025-12-26 11:18:14', NULL, NULL, 15, 2, 'slika_1766441088_6949c080aa565.jpg'),
(10, NULL, 'Fizika', '2025-12-26 11:18:14', NULL, NULL, 15, 3, 'slika_1766442400_6949c5a01bafd.jpg');

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
  `datum_registracije` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nalozi`
--

INSERT INTO `nalozi` (`nalog_id`, `ime_prezime`, `pristup`, `aktiviran`, `razred_odjeljenje`, `email`, `sifra`, `datum_registracije`) VALUES
(1, 'Admin Nalog', 'admin', 1, NULL, 'admin@admin.com', '$2y$10$kEemvXJ3AMEgB3gEcsnhte1Yqyw7oOGT7nbEqg/pd/FWgLPul/im.', '2025-12-26 10:41:23'),
(2, 'Jasmin Muslić', 'ucenik', 0, 'I', 'muslicj.007@gmail.com', '$2y$10$Xv6wL0jBSd0vNoh2GY925OA1iNU0CwSyZCTk2yVvBjwW9mErlg62O', '2025-12-26 10:41:23'),
(4, 'Test Ucenik', 'ucenik', 0, 'I2', 'mikajlo367@gmail.com', '$2y$10$S7.qSH2lqwT.RrqIFM.szuHkeHd3f5jUHUItjAq04Bf15rQcSX6v6', '2025-12-26 10:44:05');

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
  `odgovori_json` text DEFAULT NULL,
  `bodovi` int(11) DEFAULT NULL,
  `vrijeme_izrade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rezultati`
--

INSERT INTO `rezultati` (`rezultat_id`, `ucenik_id`, `kviz_id`, `predano`, `odgovori_json`, `bodovi`, `vrijeme_izrade`) VALUES
(5, 4, 1, NULL, NULL, 1, 99),
(6, 4, 1, NULL, NULL, 9, 67),
(7, 4, 3, NULL, NULL, 3, 146),
(8, 4, 2, NULL, NULL, 7, 120),
(9, 4, 9, NULL, NULL, 14, 55),
(10, 4, 1, NULL, NULL, 2, 145);

-- --------------------------------------------------------

--
-- Table structure for table `verifikacije_naloga`
--

CREATE TABLE `verifikacije_naloga` (
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(6) DEFAULT NULL,
  `istek` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verifikacije_naloga`
--

INSERT INTO `verifikacije_naloga` (`email`, `token`, `istek`) VALUES
('aaa@aaa.com', '2WQORN', '2025-12-25 01:47:24'),
('aaa@aaa.com', 'WZUR68', '2025-12-25 02:09:00'),
('aaa@aaa.com', 'OBFBNS', '2025-12-25 02:09:07'),
('aaa@aaa.com', 'H9UD3W', '2025-12-25 02:45:15');

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
  MODIFY `kviz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nalozi`
--
ALTER TABLE `nalozi`
  MODIFY `nalog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `rezultat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `ocisti_istekle_verifikacije` ON SCHEDULE EVERY 15 MINUTE STARTS '2025-12-25 00:53:24' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM verifikacije_naloga WHERE iste < NOW()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
