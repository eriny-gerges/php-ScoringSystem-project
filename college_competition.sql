-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 10:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `college_competition`
--

-- --------------------------------------------------------

--
-- Table structure for table `individual_events`
--

CREATE TABLE `individual_events` (
  `event_id` int(5) NOT NULL,
  `event_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `individual_participation`
--

CREATE TABLE `individual_participation` (
  `participant_id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `Total_score` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `individual_participation`
--

INSERT INTO `individual_participation` (`participant_id`, `name`, `Total_score`) VALUES
(1, 'eriny ', 20);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` int(4) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `Member_1` varchar(255) NOT NULL,
  `Member_2` varchar(255) NOT NULL,
  `Member_3` varchar(255) NOT NULL,
  `Member_4` varchar(255) NOT NULL,
  `Member_5` varchar(255) NOT NULL,
  `Total_score` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `Member_1`, `Member_2`, `Member_3`, `Member_4`, `Member_5`, `Total_score`) VALUES
(6, 'erotoo', 'ero', 'bero', 'kero', 'mero', 'beso', 29);

-- --------------------------------------------------------

--
-- Table structure for table `team_events`
--

CREATE TABLE `team_events` (
  `event_id` int(5) NOT NULL,
  `event_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `individual_events`
--
ALTER TABLE `individual_events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `individual_participation`
--
ALTER TABLE `individual_participation`
  ADD PRIMARY KEY (`participant_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `team_events`
--
ALTER TABLE `team_events`
  ADD PRIMARY KEY (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `individual_events`
--
ALTER TABLE `individual_events`
  MODIFY `event_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `individual_participation`
--
ALTER TABLE `individual_participation`
  MODIFY `participant_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `team_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `team_events`
--
ALTER TABLE `team_events`
  MODIFY `event_id` int(5) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
