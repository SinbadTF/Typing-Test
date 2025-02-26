-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 26, 2025 at 05:33 AM
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
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` float NOT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `user_id`, `lesson_id`, `wpm`, `accuracy`, `status`, `completed_at`) VALUES
(1, 3, 1, 39, 100, 'completed', '2025-02-04 18:36:18'),
(2, 3, 2, 44, 95, 'completed', '2025-02-03 03:46:45'),
(3, 3, 3, 54, 100, 'completed', '2025-02-01 10:47:48'),
(4, 3, 4, 49, 95, 'completed', '2025-02-01 09:46:30'),
(5, 3, 16, 50, 91, 'completed', '2025-02-01 10:48:14'),
(6, 3, 22, 39, 82, 'completed', '2025-02-01 10:48:34'),
(7, 5, 1, 20, 89, 'completed', '2025-02-03 03:19:49'),
(8, 3, 11, 26, 100, 'completed', '2025-02-03 04:25:45'),
(9, 3, 5, 41, 100, 'completed', '2025-02-03 04:59:22'),
(10, 3, 21, 55, 100, 'completed', '2025-02-03 05:03:14'),
(11, 3, 56, 20, 100, 'completed', '2025-02-04 15:15:09'),
(12, 3, 46, 18, 100, 'completed', '2025-02-04 17:44:48'),
(13, 6, 1, 24, 100, 'completed', '2025-02-07 03:03:58'),
(14, 7, 1, 47, 100, 'completed', '2025-02-06 14:09:33'),
(15, 6, 11, 36, 100, 'completed', '2025-02-06 14:31:44'),
(16, 6, 12, 29, 90, 'completed', '2025-02-07 06:27:36'),
(17, 6, 46, 72, 100, 'completed', '2025-02-23 12:05:47'),
(18, 6, 66, 51, 100, 'completed', '2025-02-16 04:32:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
