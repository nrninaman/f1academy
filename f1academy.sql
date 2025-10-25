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
  `password` varchar(255) NOT NULL,
  -- Added role column for Admin Dashboard access control
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user' COMMENT 'Added role for Admin Dashboard access control'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
-- (Note: The password hashes below correspond to 'adminpass' for the admin user and a previous hash for darin@gmail.com)
--
INSERT INTO `users` (`id`, `fullname`, `email`, `nationality`, `age`, `gender`, `team`, `sponsor`, `password`, `role`) VALUES
(1, 'irbahdarin', 'darin@gmail.com', 'malay', 22, 'female', 'Ferrari', 'Red Bull', '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'user'),
(2, 'Admin User', 'admin@f1academy.com', 'Global', 30, 'other', NULL, NULL, '$2y$10$at7.9pv4RQMlkcdO9rzYLeakyjpBXzQpahVrLe9AxsxZ8T9smHabe', 'admin'); 

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--
CREATE TABLE `teams` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `base_country` VARCHAR(100) NOT NULL,
  `engine_supplier` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--
INSERT INTO `teams` (`name`, `base_country`, `engine_supplier`) VALUES
('Ferrari', 'Italy', 'Ferrari'),
('Mercedes', 'UK', 'Mercedes'),
('Red Bull Racing', 'UK', 'Honda RBPT'),
('McLaren', 'UK', 'Mercedes'),
('Aston Martin', 'UK', 'Mercedes'),
('Alpine', 'France', 'Renault'),
('Williams', 'UK', 'Mercedes'),
('Kick Sauber', 'Switzerland', 'Ferrari'),
('RB Cash App', 'Italy', 'Honda RBPT');


-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--
CREATE TABLE `sponsors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `sector` VARCHAR(100) NOT NULL,
  `contract_value` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsors`
--
INSERT INTO `sponsors` (`name`, `sector`, `contract_value`) VALUES
('Red Bull', 'Energy Drink', 50000000),
('Mercedes', 'Automotive', 40000000),
('Ferrari', 'Automotive', 60000000),
('McLaren', 'Technology', 35000000);

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

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
