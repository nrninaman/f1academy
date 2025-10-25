-- F1 Academy Database Setup Script
-- This script drops tables if they exist, recreates them with the latest schema,
-- and inserts initial data for users, teams, and sponsors.

-- Set database context (replace `f1academy` if your database name is different)
CREATE DATABASE IF NOT EXISTS `f1academy`;
USE `f1academy`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Drop existing tables to ensure a clean setup
--
DROP TABLE IF EXISTS `results`;
DROP TABLE IF EXISTS `races`;
DROP TABLE IF EXISTS `drivers`;
DROP TABLE IF EXISTS `sponsors`;
DROP TABLE IF EXISTS `teams`;
DROP TABLE IF EXISTS `users`;

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
  `team_request` VARCHAR(100) DEFAULT NULL COMMENT 'Stores pending team request for admin approval',
  `sponsor_request` VARCHAR(100) DEFAULT NULL COMMENT 'Stores pending sponsor request for admin approval',
  `password` varchar(255) NOT NULL,
  -- Added role column for Admin Dashboard access control
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user' COMMENT 'Added role for Admin Dashboard access control'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
-- (Note: The password hashes below correspond to 'adminpass' for the admin user and a previous hash for darin@gmail.com)
--
INSERT INTO `users` (`id`, `fullname`, `email`, `nationality`, `age`, `gender`, `team`, `sponsor`, `team_request`, `sponsor_request`, `password`, `role`) VALUES
(1, 'irbahdarin', 'darin@gmail.com', 'malay', 22, 'female', 'Ferrari', 'Red Bull', NULL, NULL, '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'user'),
(2, 'Admin User', 'admin@f1academy.com', 'Global', 30, 'other', NULL, NULL, NULL, NULL, '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'admin'); 

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--
CREATE TABLE `teams` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `base_country` VARCHAR(100) NOT NULL,
  `engine_supplier` VARCHAR(100) NOT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `car_image_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--
INSERT INTO `teams` (`name`, `base_country`, `engine_supplier`, `logo_path`, `car_image_path`) VALUES
('Ferrari', 'Italy', 'Ferrari', 'image/Ferrari-removebg-preview.png', 'image/2025ferraricarright.avif'),
('Mercedes', 'UK', 'Mercedes', 'image/Mercedes.png', 'image/2025mercedescarright.avif'),
('Red Bull Racing', 'UK', 'Honda RBPT', 'image/RedBull-removebg-preview.png', 'image/2025redbullracingcarright.avif'),
('McLaren', 'UK', 'Mercedes', 'image/mclaren (1)-modified.png', 'image/2025mclarencarright.avif'),
('Aston Martin', 'UK', 'Mercedes', 'image/aston_martini-removebg-preview.png', 'image/2025astonmartincarright.avif'),
('Alpine', 'France', 'Renault', 'image/alpine-logo-a-1955-removebg-preview.png', 'image/2025alpinecarright.avif'),
('Williams', 'UK', 'Mercedes', 'image/Williams Racing Icon 2020.png', 'image/2025williamscarright.avif'),
('Kick Sauber', 'Switzerland', 'Ferrari', 'image/Kick-Logo-Logo--Streamline-Logos.png', 'image/2025kicksaubercarright.avif'),
('RB Cash App', 'Italy', 'Honda RBPT', 'image/cash app.png', 'image/2025racingbullscarright.avif');


-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--
CREATE TABLE `sponsors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `sector` VARCHAR(100) NOT NULL,
  `contract_value` INT(11) NOT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsors`
--
INSERT INTO `sponsors` (`name`, `sector`, `contract_value`, `logo_path`, `details`) VALUES
('Red Bull', 'Energy Drink', 50000000, 'image/RedBull.png', 'Primary sponsor for Red Bull Racing and RB Cash App.'),
('Mercedes', 'Automotive', 40000000, 'image/Mercedes-Logo.png', 'Title sponsor of the Mercedes-AMG F1 Team.'),
('Ferrari', 'Automotive', 60000000, 'image/Ferrari.png', 'Title sponsor of the Scuderia Ferrari F1 Team.'),
('McLaren', 'Technology', 35000000, 'image/McLaren.png', 'The McLaren brand itself as a sponsor.');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--
CREATE TABLE `drivers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NOT NULL,
  `team_name` VARCHAR(100) NOT NULL,
  `sponsor_name` VARCHAR(100) DEFAULT NULL,
  `standing_position` INT(11) DEFAULT NULL,
  `points` INT(11) DEFAULT 0,
  `biography` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`team_name`) REFERENCES `teams`(`name`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--
INSERT INTO `drivers` (`fullname`, `team_name`, `sponsor_name`, `standing_position`, `points`, `biography`, `image_path`) VALUES
('Max Verstappen', 'Red Bull Racing', 'Red Bull', 1, 350, 'Dominant world champion. Known for aggressive driving and speed.', 'image/MV.png'),
('Charles Leclerc', 'Ferrari', 'Ferrari', 2, 300, 'Ferrari\'s star driver. Excellent qualifying pace.', 'image/CLC.png'),
('Lando Norris', 'McLaren', 'McLaren', 3, 270, 'British star, known for consistency and humor.', 'image/Lando.png'),
('George Russell', 'Mercedes', 'Mercedes', 4, 250, 'A young talent leading the Mercedes charge.', 'image/George.png'),
('Fernando Alonso', 'Aston Martin', 'Aston Martin', 5, 200, 'The veteran champion, still showing incredible skill.', 'image/Alonso.png');

-- --------------------------------------------------------

--
-- Table structure for table `races`
--
CREATE TABLE `races` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `date` DATE NOT NULL,
  `details` TEXT DEFAULT NULL,
  `round_number` INT(11) NOT NULL UNIQUE,
  `is_completed` BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `races`
--
INSERT INTO `races` (`name`, `date`, `details`, `round_number`, `is_completed`) VALUES
('Australian Grand Prix', '2025-03-16', 'Race held at Albert Park Circuit, Melbourne.', 1, TRUE),
('Miami Grand Prix', '2025-05-04', 'Race held at Miami International Autodrome.', 2, TRUE),
('Monaco Grand Prix', '2025-05-25', 'The legendary street race.', 3, FALSE);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--
CREATE TABLE `results` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `race_id` INT(11) NOT NULL,
  `driver_id` INT(11) NOT NULL,
  `position` INT(11) NOT NULL,
  `points` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`race_id`) REFERENCES `races`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_result_per_driver_race` (`race_id`, `driver_id`),
  UNIQUE KEY `unique_position_per_race` (`race_id`, `position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
-- (Results for Australian Grand Prix - Round 1)
--
INSERT INTO `results` (`race_id`, `driver_id`, `position`, `points`) VALUES
(1, 1, 1, 25), -- Max Verstappen
(1, 3, 2, 18), -- Lando Norris
(1, 2, 3, 15); -- Charles Leclerc


--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `races`
--
ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;