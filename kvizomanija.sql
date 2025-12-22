-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2025 at 11:54 PM
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
(3, 'murat', 'murat@kviz.ba', '$2y$10$7qC.uY1TJ0K6pWJHbuvSgeKo7gGcd8HxjXtS8g5s3ZF/Af1Uv8gJ.', 'I', '2025-12-19 22:33:08'),
(6, 'haris', 'haris@kviz.ba', '$2y$10$OgFnabSjsaVmyph8YKpESutJ6cxHSGkoOx.YOBhfb84W5Iay5yiua', 'I', '2025-12-21 20:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `kvizovi`
--

CREATE TABLE `kvizovi` (
  `kviz_id` int(11) NOT NULL,
  `naziv_kviza` varchar(100) DEFAULT NULL,
  `slika` varchar(255) DEFAULT 'default.jpg',
  `admin_id` int(11) DEFAULT NULL,
  `pocetak` datetime DEFAULT NULL,
  `rok_predaje` datetime DEFAULT NULL,
  `vremensko_ogranicenje` int(11) NOT NULL DEFAULT 15,
  `broj_pitanja` int(11) NOT NULL DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kvizovi`
--

INSERT INTO `kvizovi` (`kviz_id`, `naziv_kviza`, `slika`, `admin_id`, `pocetak`, `rok_predaje`, `vremensko_ogranicenje`, `broj_pitanja`) VALUES
(1, 'Opće znanje', 'default.jpg', 1, NULL, NULL, 15, 15),
(2, 'IT & Tehnologija', 'default.jpg', 1, NULL, NULL, 15, 15),
(3, 'Sport', 'default.jpg', 1, NULL, NULL, 15, 15),
(9, 'Geografija', 'slika_1766441088_6949c080aa565.jpg', NULL, NULL, NULL, 15, 2),
(10, 'Geografija', 'slika_1766442400_6949c5a01bafd.jpg', NULL, NULL, NULL, 15, 3);

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

--
-- Dumping data for table `pitanja`
--

INSERT INTO `pitanja` (`pitanje_id`, `kviz_id`, `tekst_pitanja`, `opcija_a`, `opcija_b`, `opcija_c`, `tacan_odgovor`) VALUES
(13, 9, 'eef', 'as', 'asd', 'asd', 'as'),
(14, 9, 'asd', 'asd', 'asd', 'a', 'a');

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
(38, 9, 'emel', 'Započeto', 'Učen', NULL, '2025-12-22 22:22:46', 0);

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
  MODIFY `korisnik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kvizovi`
--
ALTER TABLE `kvizovi`
  MODIFY `kviz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `odgovori`
--
ALTER TABLE `odgovori`
  MODIFY `odgovor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pitanja`
--
ALTER TABLE `pitanja`
  MODIFY `pitanje_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rezultati`
--
ALTER TABLE `rezultati`
  MODIFY `rezultat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
