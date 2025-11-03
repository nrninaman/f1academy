-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 05:22 PM
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
-- Database: `f1academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `sponsor_name` varchar(100) DEFAULT NULL,
  `standing_position` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `biography` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `fullname`, `team_name`, `sponsor_name`, `standing_position`, `points`, `biography`, `image_path`) VALUES
(1, 'Max Verstappen', 'Red Bull Racing', 'Red Bull', 1, 50, 'Dominant world champion. Known for aggressive driving and speed.', 'image/MV.png'),
(2, 'Charles Leclerc', 'Ferrari', 'Ferrari', 3, 30, 'Ferrari\'s star driver. Excellent qualifying pace.', 'image/CLC.png'),
(3, 'Lando Norris', 'McLaren', 'McLaren', 2, 36, 'British star, known for consistency and humor.', 'image/Lando.png'),
(4, 'George Russell', 'Mercedes', 'Mercedes', 4, 12, 'A young talent leading the Mercedes charge.', 'image/George.png'),
(5, 'Fernando Alonso', 'Aston Martin', 'Aston Martin', 5, 10, 'The veteran champion, still showing incredible skill.', 'image/Alonso.png');

-- --------------------------------------------------------

--
-- Table structure for table `races`
--

CREATE TABLE `races` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `details` text DEFAULT NULL,
  `round_number` int(11) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `races`
--

INSERT INTO `races` (`id`, `name`, `date`, `details`, `round_number`, `is_completed`) VALUES
(1, 'Australian Grand Prix', '2025-03-16', 'Race held at Albert Park Circuit, Melbourne.', 1, 1),
(2, 'Miami Grand Prix', '2025-05-04', 'Race held at Miami International Autodrome.', 2, 1),
(3, 'Monaco Grand Prix', '2025-05-25', 'The legendary street race.', 3, 0),
(4, 'Singapore Grand Prix', '2025-03-20', 'Marina Bay Street', 4, 0),
(5, 'Sepang Grand Prix', '2025-06-21', 'Sepang International Circuit', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `race_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `race_id`, `driver_id`, `position`, `points`) VALUES
(1, 1, 1, 1, 25),
(2, 1, 3, 2, 18),
(3, 1, 2, 3, 15),
(4, 5, 1, 1, 25),
(5, 5, 3, 2, 18),
(6, 5, 2, 3, 15),
(7, 5, 4, 4, 12),
(8, 5, 5, 5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sector` varchar(100) NOT NULL,
  `contract_value` int(11) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsors`
--

INSERT INTO `sponsors` (`id`, `name`, `sector`, `contract_value`, `logo_path`, `details`) VALUES
(1, 'Red Bull', 'Energy Drink', 50000000, 'image/RedBull.png', 'Primary sponsor for Red Bull Racing and RB Cash App.'),
(2, 'Mercedes', 'Automotive', 40000000, 'image/Mercedes-Logo.png', 'Title sponsor of the Mercedes-AMG F1 Team.'),
(3, 'Ferrari', 'Automotive', 60000000, 'image/Ferrari.png', 'Title sponsor of the Scuderia Ferrari F1 Team.'),
(4, 'McLaren', 'Technology', 35000000, 'image/McLaren.png', 'The McLaren brand itself as a sponsor.');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `base_country` varchar(100) NOT NULL,
  `engine_supplier` varchar(100) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `car_image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `base_country`, `engine_supplier`, `logo_path`, `car_image_path`) VALUES
(1, 'Ferrari', 'Italy', 'Ferrari', 'image/Ferrari-removebg-preview.png', 'image/2025ferraricarright.avif'),
(2, 'Mercedes', 'UK', 'Mercedes', 'image/Mercedes.png', 'image/2025mercedescarright.avif'),
(3, 'Red Bull Racing', 'UK', 'Honda RBPT', 'image/RedBull-removebg-preview.png', 'image/2025redbullracingcarright.avif'),
(4, 'McLaren', 'UK', 'Mercedes', 'image/mclaren (1)-modified.png', 'image/2025mclarencarright.avif'),
(5, 'Aston Martin', 'UK', 'Mercedes', 'image/aston_martini-removebg-preview.png', 'image/2025astonmartincarright.avif'),
(6, 'Alpine', 'France', 'Renault', 'image/alpine-logo-a-1955-removebg-preview.png', 'image/2025alpinecarright.avif'),
(7, 'Williams', 'UK', 'Mercedes', 'image/Williams Racing Icon 2020.png', 'image/2025williamscarright.avif'),
(8, 'Kick Sauber', 'Switzerland', 'Ferrari', 'image/Kick-Logo-Logo--Streamline-Logos.png', 'image/2025kicksaubercarright.avif'),
(9, 'RB Cash App', 'Italy', 'Honda RBPT', 'image/cash app.png', 'image/2025racingbullscarright.avif');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `age` int(2) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `team` varchar(100) DEFAULT NULL,
  `sponsor` varchar(100) DEFAULT NULL,
  `team_request` varchar(100) DEFAULT NULL COMMENT 'Stores pending team request for admin approval',
  `sponsor_request` varchar(100) DEFAULT NULL COMMENT 'Stores pending sponsor request for admin approval',
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user' COMMENT 'Added role for Admin Dashboard access control'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `nationality`, `age`, `gender`, `team`, `sponsor`, `team_request`, `sponsor_request`, `password`, `role`) VALUES
(1, 'irbahdarin', 'darin@gmail.com', 'malay', 22, 'female', 'Ferrari', 'Red Bull', NULL, NULL, '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'user'),
(2, 'Admin User', 'admin@f1academy.com', 'Global', 30, 'other', NULL, NULL, NULL, NULL, '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_name` (`team_name`);

--
-- Indexes for table `races`
--
ALTER TABLE `races`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `round_number` (`round_number`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_result_per_driver_race` (`race_id`,`driver_id`),
  ADD UNIQUE KEY `unique_position_per_race` (`race_id`,`position`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `races`
--
ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`team_name`) REFERENCES `teams` (`name`) ON UPDATE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`race_id`) REFERENCES `races` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
