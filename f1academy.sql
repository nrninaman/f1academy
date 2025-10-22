-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 11:16 AM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `passwrod` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `team` varchar(100) DEFAULT NULL,
  `sponsor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `nationality`, `age`, `gender`, `passwrod`, `password`, `team`, `sponsor`) VALUES
(5, 'Dahlia Inara', 'samwukong2908@gmail.com', 'Malaysia', 18, 'female', NULL, '$2y$10$yASpL1/igCxk6nekA38iBOBINWGbaDtra9Mh1CIYbT73wpwA.iPQi', NULL, NULL),
(8, 'Antoine Griezmann', 'griezmann@gmail.com', 'France', 20, 'male', NULL, '$2y$10$LjzALZsvdt464g00niM4lu3EykmvV.pfETQETatUcZL9oZBySES0a', NULL, NULL),
(9, 'Florian Wirtz', 'florianwirtz@gmail.com', 'Germany', 23, 'male', NULL, '$2y$10$B/lX3hW3PBkGxJzkNvH18ePlCANLz1K0GWYRlCYji8FgdF.SPD/r.', 'Ferrari', 'Ferrari');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
