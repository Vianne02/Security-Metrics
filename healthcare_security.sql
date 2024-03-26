-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2024 at 09:04 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthcare_security`
--

-- --------------------------------------------------------

--
-- Table structure for table `behaviour`
--

CREATE TABLE `behaviour` (
  `id` int(11) NOT NULL,
  `subject_matter` varchar(255) NOT NULL,
  `incidents_before` int(11) NOT NULL,
  `incidents_after` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  `performance` varchar(50) NOT NULL,
  `date_published` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `behaviour`
--

INSERT INTO `behaviour` (`id`, `subject_matter`, `incidents_before`, `incidents_after`, `result`, `performance`, `date_published`) VALUES
(11, 'Availability', 10, 5, -5, 'Improved', '2024-02-16 12:57:42'),
(12, 'Confidentiality', 10, 20, 10, 'Worsened', '2024-02-16 12:58:30'),
(13, 'Integrity', 10, 20, 10, 'Worsened', '2024-02-18 14:59:22');

-- --------------------------------------------------------

--
-- Table structure for table `business_impact_analysis`
--

CREATE TABLE `business_impact_analysis` (
  `id` int(11) NOT NULL,
  `incidents_before` varchar(100) NOT NULL,
  `incidents_after` varchar(100) NOT NULL,
  `incident_response_rating` varchar(100) DEFAULT NULL,
  `continuity_plan_effectiveness` int(11) DEFAULT NULL CHECK (`continuity_plan_effectiveness` between 1 and 5),
  `date_recorded` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_impact_analysis`
--

INSERT INTO `business_impact_analysis` (`id`, `incidents_before`, `incidents_after`, `incident_response_rating`, `continuity_plan_effectiveness`, `date_recorded`) VALUES
(7, 'vejve', 'eve', 'er', 2, '2024-03-25 17:59:59'),
(8, 'vjfdvve', 'ejrvjeroe', 'ergerog', 5, '2024-03-25 18:00:14'),
(9, 'vejfe9fj', 'jnejifne', 'nein', 5, '2024-03-25 18:00:39'),
(10, 'vejfe9fj', 'jnejifne', 'nein', 5, '2024-03-25 18:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `feedback` int(11) NOT NULL,
  `date_submitted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `feedback`, `date_submitted`) VALUES
(1, 5, '2024-02-16 16:12:30'),
(2, 5, '2024-02-16 16:14:52'),
(3, 5, '2024-02-16 16:16:53'),
(4, 5, '2024-02-16 16:22:54'),
(5, 5, '2024-02-16 16:26:27'),
(6, 5, '2024-02-16 16:29:50'),
(7, 5, '2024-02-16 16:31:55'),
(8, 5, '2024-02-16 16:32:28'),
(9, 4, '2024-02-18 18:00:06'),
(10, 1, '2024-02-18 18:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge`
--

CREATE TABLE `knowledge` (
  `id` int(11) NOT NULL,
  `knowledge_before` int(11) NOT NULL,
  `knowledge_after` int(11) NOT NULL,
  `result` varchar(255) DEFAULT NULL,
  `performance` varchar(255) DEFAULT NULL,
  `date_published` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge`
--

INSERT INTO `knowledge` (`id`, `knowledge_before`, `knowledge_after`, `result`, `performance`, `date_published`) VALUES
(27, 1, 3, '2', 'Improved', '2024-02-16 16:33:28'),
(28, 3, 5, '2', 'Improved', '2024-02-16 16:33:39'),
(29, 5, 3, '-2', 'Depreciated', '2024-02-16 16:33:54'),
(30, 3, 3, '0', 'Stagnant', '2024-02-16 16:34:06'),
(31, 3, 1, '-2', 'Depreciated', '2024-02-16 16:35:08'),
(32, 1, 3, '2', 'Improved', '2024-02-16 16:36:39'),
(33, 3, 5, '2', 'Improved', '2024-02-18 17:53:20'),
(34, 5, 2, '-3', 'Depreciated', '2024-02-18 17:53:38'),
(35, 2, 5, '3', 'Improved', '2024-03-25 22:13:51');

-- --------------------------------------------------------

--
-- Table structure for table `risk_management`
--

CREATE TABLE `risk_management` (
  `id` int(11) NOT NULL,
  `type_of_risk` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `controls` text NOT NULL,
  `number_of_incidents` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_management`
--

INSERT INTO `risk_management` (`id`, `type_of_risk`, `description`, `controls`, `number_of_incidents`, `date_added`) VALUES
(1, 'Unauthorized access', 'neiofowefwmef', 'wcjwoj', 10, '2024-02-17 14:02:26'),
(2, 'dmsd', 'simos', 'smmfps', 10, '2024-02-17 14:06:13'),
(3, 'eifjeriofjeo', 'vmwioowfmow', '-newini\r\n-nrivnw', 20, '2024-02-18 18:01:11');

-- --------------------------------------------------------

--
-- Table structure for table `security_incidents`
--

CREATE TABLE `security_incidents` (
  `id` int(11) NOT NULL,
  `security_last` int(11) NOT NULL,
  `security_current` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  `performance` varchar(255) NOT NULL,
  `date_published` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_incidents`
--

INSERT INTO `security_incidents` (`id`, `security_last`, `security_current`, `result`, `performance`, `date_published`) VALUES
(8, 10, 10, 0, 'Stagnant', '2024-02-16 14:32:53'),
(9, 10, 20, 10, 'Reduced', '2024-02-16 14:33:09'),
(10, 20, 5, -15, 'Improved', '2024-02-16 14:33:25'),
(11, 5, 20, 15, 'Reduced', '2024-02-18 17:58:19'),
(12, 15, 10, -5, 'Improved', '2024-02-18 17:58:31'),
(13, 10, 20, 10, 'Reduced', '2024-03-25 21:30:07'),
(14, 20, 5, -15, 'Improved', '2024-03-25 21:34:43'),
(15, 5, 5, 0, 'Stagnant', '2024-03-25 21:39:16'),
(16, 5, 10, 5, 'Reduced', '2024-03-25 21:46:33'),
(17, 10, 5, -5, 'Improved', '2024-03-25 21:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `training_participants`
--

CREATE TABLE `training_participants` (
  `id` int(11) NOT NULL,
  `participants_last` int(11) NOT NULL,
  `participants_current` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  `performance` varchar(10) NOT NULL,
  `date_published` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_participants`
--

INSERT INTO `training_participants` (`id`, `participants_last`, `participants_current`, `result`, `performance`, `date_published`) VALUES
(15, 10, 20, 10, 'Improved', '2024-02-16 14:01:23'),
(17, 20, 10, -10, 'Reduced', '2024-02-16 14:02:17'),
(19, 10, 30, 20, 'Improved', '2024-02-16 14:03:25'),
(20, 30, 30, 0, 'Stagnant', '2024-02-16 14:03:39'),
(21, 30, 50, 20, 'Improved', '2024-02-18 17:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'collo', '$2y$10$aCUMCeY26TAqY9JIcvR8/OakCLOdu0aZ18WNLtM2HOyqUjVEK91NS'),
(2, 'Vianne', '$2y$10$yF//l1TYX73SM0.K2cG1Vut7wlAnOsRnCn.F1Q1ssq9I0HqqQPSSO'),
(3, 'kels', '$2y$10$ueYGeJ24bWSD6aJsISTXBet9V1.tKeipBNrAYDDGekufnbnLCg3p6'),
(4, 'fade', '$2y$10$M6/0RWRA2W3cjx1FpPmnReRwKaQ131.HT/rRpEt6B6Qy3v.1cSFTa'),
(5, 'Vee', '$2y$10$JXV4En90Ql2J4Y0NHPUjbeHtcqFoPL489M7XC9rZg8iiHEGT3eESe'),
(6, 'Vianne2', '$2y$10$9TtwW5vZv3jhi8u4NWQ9m.Nhm6aBwpTqmtE1YA6dg1e8LyoOqoHbW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `behaviour`
--
ALTER TABLE `behaviour`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_impact_analysis`
--
ALTER TABLE `business_impact_analysis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `knowledge`
--
ALTER TABLE `knowledge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risk_management`
--
ALTER TABLE `risk_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_incidents`
--
ALTER TABLE `security_incidents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_participants`
--
ALTER TABLE `training_participants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `behaviour`
--
ALTER TABLE `behaviour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `business_impact_analysis`
--
ALTER TABLE `business_impact_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `knowledge`
--
ALTER TABLE `knowledge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `risk_management`
--
ALTER TABLE `risk_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security_incidents`
--
ALTER TABLE `security_incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `training_participants`
--
ALTER TABLE `training_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
