-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 24, 2025 at 06:22 PM
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
-- Database: `typing_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificate_exams`
--

CREATE TABLE `certificate_exams` (
  `exam_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` float NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `exam_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_exams`
--

INSERT INTO `certificate_exams` (`exam_id`, `user_id`, `wpm`, `accuracy`, `passed`, `exam_date`) VALUES
(1, 7, 51, 95, 1, '2025-02-23 14:16:10'),
(2, 9, 46, 95, 1, '2025-02-23 14:35:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificate_exams`
--
ALTER TABLE `certificate_exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificate_exams`
--
ALTER TABLE `certificate_exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificate_exams`
--
ALTER TABLE `certificate_exams`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
