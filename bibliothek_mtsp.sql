-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2026 at 06:46 AM
-- Server version: 12.1.2-MariaDB
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bibliothek_mtsp`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_ausleih`
--

CREATE TABLE `t_ausleih` (
  `ausleihNr` int(11) NOT NULL,
  `vorname` varchar(100) DEFAULT NULL,
  `nachname` varchar(100) DEFAULT NULL,
  `personalNr` int(11) DEFAULT NULL,
  `buchNr` int(11) DEFAULT NULL,
  `ausleihdatum` date DEFAULT NULL,
  `rueckgabedatum` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `t_ausleih`
--

INSERT INTO `t_ausleih` (`ausleihNr`, `vorname`, `nachname`, `personalNr`, `buchNr`, `ausleihdatum`, `rueckgabedatum`) VALUES
(1, 'Manuel', 'Mayr', 1, 6, '2025-12-05', '2025-12-12'),
(2, 'David', 'Danninger', 2, 12, '2025-12-05', '2025-12-19'),
(3, 'Eric ', 'Höglinger', NULL, 6, '2026-01-08', '2026-01-23'),
(4, 'Eric ', 'Höglinger', NULL, 6, '2026-01-08', '2026-01-23'),
(5, 'Eric ', 'Höglinger', NULL, NULL, '2026-01-08', '2026-01-23'),
(6, 'Eric ', 'Höglinger', NULL, NULL, '2026-01-08', '2026-01-23'),
(7, 'Manuel', 'Mayr', NULL, NULL, '2026-01-08', '2026-01-23'),
(8, 'Manuel', 'Mayr', NULL, NULL, '2026-01-08', '2026-01-23');

-- --------------------------------------------------------

--
-- Table structure for table `t_bibliothekar`
--

CREATE TABLE `t_bibliothekar` (
  `personalNr` int(11) NOT NULL,
  `vorname` varchar(100) DEFAULT NULL,
  `nachname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `t_bibliothekar`
--

INSERT INTO `t_bibliothekar` (`personalNr`, `vorname`, `nachname`, `email`, `username`, `password`, `admin`) VALUES
(1, 'Dieter', 'Diesel', 'die.die@mail.at', 'DieselD', 'Test123!', NULL),
(2, 'Otto', 'Benzin', 'Ott.Benz@mail.at', 'OttoB', 'Test123!', NULL),
(3, 'Manuel', 'Mayr', 'manuel.mayr@tfs-haslach.at', 'Winterbua', '$2y$12$wAAqfdQCSv6yernXkOoGPOh2sQzbA12WzdhWAXpcH7SdCW60ljvmy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `t_buecher`
--

CREATE TABLE `t_buecher` (
  `buchNr` int(11) NOT NULL,
  `ISBN` varchar(50) DEFAULT NULL,
  `Titel` varchar(255) DEFAULT NULL,
  `Author` varchar(100) NOT NULL,
  `Verlag` varchar(255) DEFAULT NULL,
  `Kategorie` varchar(100) DEFAULT NULL,
  `Beschreibung` varchar(500) DEFAULT NULL,
  `Anschaffungskosten` decimal(10,2) DEFAULT NULL,
  `ausleih` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `t_buecher`
--

INSERT INTO `t_buecher` (`buchNr`, `ISBN`, `Titel`, `Author`, `Verlag`, `Kategorie`, `Beschreibung`, `Anschaffungskosten`, `ausleih`) VALUES
(5, '9780747532699', 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'An orphan discovers he is a wizard and begins his education at Hogwarts School of Witchcraft and Wizardry.', 16.00, 1),
(6, '9780747538493', 'Harry Potter and the Chamber of Secrets', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'Harry returns to Hogwarts and uncovers the mystery of the Chamber of Secrets.', 20.00, 0),
(7, '9780747542155', 'Harry Potter and the Prisoner of Azkaban', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'Harry learns more about his past and the mysterious prisoner Sirius Black.', 16.50, 0),
(8, '9780747546245', 'Harry Potter and the Goblet of Fire', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'Harry competes in a dangerous magical tournament.', 17.00, 1),
(9, '9780747551003', 'Harry Potter and the Order of the Phoenix', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'Harry fights against the Ministry and the return of Voldemort.', 18.00, 1),
(10, '9780747551010', 'Harry Potter and the Half-Blood Prince', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'Harry discovers dark secrets and prepares for the final battle.', 18.50, 0),
(11, '9780747591054', 'Harry Potter and the Deathly Hallows', 'J.K. Rowling', 'Bloomsbury', 'Fantasy, Young Adult', 'The final showdown: Harry’s last battle against Voldemort.', 19.00, 0),
(12, '9780316029186', 'The Last Wish', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'A collection of short stories introducing Geralt, the Witcher, a monster hunter with supernatural abilities.', 19.00, 0),
(13, '9780316035002', 'Sword of Destiny', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'Another collection of short stories that sets up key events and characters for the main Witcher saga.', 20.00, 0),
(14, '9780316013854', 'Blood of Elves', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'The first novel in the Witcher saga, focusing on Geralt, Ciri, and the growing political tensions in the Northern Kingdoms.', 15.00, 1),
(15, '9780316029845', 'Time of Contempt', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'The story continues with Ciri on the run, political intrigue, and Geralt caught in the conflicts between mages and kingdoms.', 21.50, 0),
(16, '9780316034166', 'Baptism of Fire', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'Geralt embarks on a dangerous journey to find Ciri, encountering new allies and facing deadly enemies.', 22.00, 0),
(17, '9780316034258', 'The Tower of Swallows', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'Ciri’s story continues as she faces both political schemes and personal trials.', 22.50, 0),
(18, '9780316034289', 'The Lady of the Lake', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'The epic conclusion of the Witcher saga, with Geralt, Ciri, and their friends confronting destiny and fate.', 23.00, 0),
(19, '9780316302030', 'Season of Storms', 'Andrzej Sapkowski', 'Orbit', 'Fantasy, Adult', 'A standalone Witcher novel set between the short stories of The Last Wish, providing additional adventures of Geralt.', 19.50, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_ausleih`
--
ALTER TABLE `t_ausleih`
  ADD PRIMARY KEY (`ausleihNr`),
  ADD KEY `fk_ausleih_personal` (`personalNr`),
  ADD KEY `fk_ausleih_buch` (`buchNr`);

--
-- Indexes for table `t_bibliothekar`
--
ALTER TABLE `t_bibliothekar`
  ADD PRIMARY KEY (`personalNr`);

--
-- Indexes for table `t_buecher`
--
ALTER TABLE `t_buecher`
  ADD PRIMARY KEY (`buchNr`),
  ADD UNIQUE KEY `ISBN` (`ISBN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_ausleih`
--
ALTER TABLE `t_ausleih`
  MODIFY `ausleihNr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `t_bibliothekar`
--
ALTER TABLE `t_bibliothekar`
  MODIFY `personalNr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `t_buecher`
--
ALTER TABLE `t_buecher`
  MODIFY `buchNr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_ausleih`
--
ALTER TABLE `t_ausleih`
  ADD CONSTRAINT `fk_ausleih_buch` FOREIGN KEY (`buchNr`) REFERENCES `t_buecher` (`buchNr`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ausleih_personal` FOREIGN KEY (`personalNr`) REFERENCES `t_bibliothekar` (`personalNr`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
