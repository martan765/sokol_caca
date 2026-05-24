-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2026 at 06:13 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sokol_caca`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_statuses`
--

CREATE TABLE `attendance_statuses` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance_statuses`
--

INSERT INTO `attendance_statuses` (`id`, `name`) VALUES
(1, 'Budu'),
(2, 'Nebudu');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `match_type_id` int NOT NULL,
  `home_team` varchar(255) NOT NULL,
  `away_team` varchar(255) NOT NULL,
  `game_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `result` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `match_type_id`, `home_team`, `away_team`, `game_date`, `location`, `result`) VALUES
(1, 1, 'Částkov', 'Havřice', '2026-05-10 10:15:00', 'Částkov', NULL),
(2, 1, 'Částkov', 'Drslavice', '2026-05-24 10:15:00', 'Částkov', NULL),
(4, 1, 'Hradčovice', 'Částkov', '2026-04-05 15:30:00', 'Hradčovice', '6:0'),
(5, 1, 'Částkov', 'Popovice', '2026-04-12 10:15:00', 'Částkov', '0:3'),
(6, 1, 'Javořina B', 'Částkov', '2026-04-19 16:00:00', 'Horní Němčí', '1:1'),
(7, 1, 'Částkov', 'Březolupy', '2026-04-26 10:15:00', 'Částkov', '2:3'),
(8, 1, 'Bílovice', 'Částkov', '2026-05-03 16:30:00', 'Bílovice', '3:1'),
(9, 1, 'Boršice u Bl./D. Němčí B', 'Částkov', '2026-05-17 17:00:00', 'Boršice u Blatnice', '4:2'),
(10, 1, 'Částkov', 'Uh. Ostroh B', '2026-05-31 10:15:00', 'Částkov', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `game_attendance`
--

CREATE TABLE `game_attendance` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `status_id` int NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_attendance`
--

INSERT INTO `game_attendance` (`id`, `user_id`, `game_id`, `status_id`, `note`) VALUES
(12, 4, 4, 1, NULL),
(13, 4, 5, 1, NULL),
(14, 4, 6, 1, NULL),
(15, 4, 7, 1, NULL),
(16, 4, 8, 1, NULL),
(17, 4, 1, 1, NULL),
(18, 4, 9, 1, NULL),
(19, 5, 4, 2, NULL),
(20, 5, 5, 2, NULL),
(21, 5, 6, 2, NULL),
(22, 5, 7, 2, NULL),
(23, 5, 8, 2, NULL),
(24, 5, 1, 2, NULL),
(25, 5, 9, 2, NULL),
(26, 5, 2, 2, NULL),
(27, 6, 4, 1, NULL),
(28, 6, 5, 1, NULL),
(29, 6, 6, 2, NULL),
(30, 6, 7, 1, NULL),
(31, 6, 8, 1, NULL),
(32, 6, 1, 1, NULL),
(33, 6, 9, 1, NULL),
(34, 6, 2, 1, NULL),
(35, 7, 4, 1, NULL),
(36, 7, 5, 2, NULL),
(37, 7, 6, 1, NULL),
(38, 7, 7, 2, NULL),
(39, 7, 8, 1, NULL),
(40, 7, 1, 2, NULL),
(41, 7, 9, 1, NULL),
(42, 7, 2, 1, NULL),
(43, 8, 4, 1, NULL),
(44, 8, 5, 1, NULL),
(45, 8, 6, 1, NULL),
(46, 8, 7, 1, NULL),
(47, 8, 8, 1, NULL),
(48, 8, 1, 1, NULL),
(49, 8, 9, 1, NULL),
(50, 8, 2, 1, NULL),
(51, 9, 4, 2, NULL),
(52, 9, 5, 1, NULL),
(53, 9, 6, 1, NULL),
(54, 9, 7, 2, NULL),
(55, 9, 8, 1, NULL),
(56, 9, 1, 1, NULL),
(57, 9, 9, 2, NULL),
(58, 9, 2, 1, NULL),
(59, 10, 4, 1, NULL),
(60, 10, 5, 2, NULL),
(61, 10, 6, 2, NULL),
(62, 10, 7, 1, NULL),
(63, 10, 8, 1, NULL),
(64, 10, 1, 2, NULL),
(65, 10, 9, 1, NULL),
(66, 10, 2, 2, NULL),
(67, 11, 5, 2, NULL),
(68, 11, 6, 2, NULL),
(69, 11, 7, 2, NULL),
(70, 11, 8, 2, NULL),
(71, 11, 1, 2, NULL),
(72, 11, 9, 1, NULL),
(73, 11, 2, 2, NULL),
(74, 12, 4, 2, NULL),
(75, 12, 5, 1, NULL),
(76, 12, 6, 2, NULL),
(77, 12, 7, 2, NULL),
(78, 12, 8, 2, NULL),
(79, 12, 1, 1, NULL),
(80, 12, 9, 2, NULL),
(81, 12, 2, 2, NULL),
(82, 4, 4, 1, NULL),
(83, 4, 5, 1, NULL),
(84, 4, 6, 1, NULL),
(85, 4, 7, 1, NULL),
(86, 4, 8, 1, NULL),
(87, 4, 1, 1, NULL),
(88, 4, 9, 1, NULL),
(89, 5, 4, 2, NULL),
(90, 5, 5, 2, NULL),
(91, 5, 6, 2, NULL),
(92, 5, 7, 2, NULL),
(93, 5, 8, 2, NULL),
(94, 5, 1, 2, NULL),
(95, 5, 9, 2, NULL),
(96, 5, 2, 2, NULL),
(97, 6, 4, 1, NULL),
(98, 6, 5, 1, NULL),
(99, 6, 6, 2, NULL),
(100, 6, 7, 1, NULL),
(101, 6, 8, 1, NULL),
(102, 6, 1, 1, NULL),
(103, 6, 9, 1, NULL),
(104, 6, 2, 1, NULL),
(105, 7, 4, 1, NULL),
(106, 7, 5, 2, NULL),
(107, 7, 6, 1, NULL),
(108, 7, 7, 2, NULL),
(109, 7, 8, 1, NULL),
(110, 7, 1, 2, NULL),
(111, 7, 9, 1, NULL),
(112, 7, 2, 1, NULL),
(113, 8, 4, 1, NULL),
(114, 8, 5, 1, NULL),
(115, 8, 6, 1, NULL),
(116, 8, 7, 1, NULL),
(117, 8, 8, 1, NULL),
(118, 8, 1, 1, NULL),
(119, 8, 9, 1, NULL),
(120, 8, 2, 1, NULL),
(121, 9, 4, 2, NULL),
(122, 9, 5, 1, NULL),
(123, 9, 6, 1, NULL),
(124, 9, 7, 2, NULL),
(125, 9, 8, 1, NULL),
(126, 9, 1, 1, NULL),
(127, 9, 9, 2, NULL),
(128, 9, 2, 1, NULL),
(129, 10, 4, 1, NULL),
(130, 10, 5, 2, NULL),
(131, 10, 6, 2, NULL),
(132, 10, 7, 1, NULL),
(133, 10, 8, 1, NULL),
(134, 10, 1, 2, NULL),
(135, 10, 9, 1, NULL),
(136, 10, 2, 2, NULL),
(137, 11, 5, 2, NULL),
(138, 11, 6, 2, NULL),
(139, 11, 7, 2, NULL),
(140, 11, 8, 2, NULL),
(141, 11, 1, 2, NULL),
(142, 11, 9, 1, NULL),
(143, 11, 2, 2, NULL),
(144, 12, 4, 2, NULL),
(145, 12, 5, 1, NULL),
(146, 12, 6, 2, NULL),
(147, 12, 7, 2, NULL),
(148, 12, 8, 2, NULL),
(149, 12, 1, 1, NULL),
(150, 12, 9, 2, NULL),
(151, 12, 2, 2, NULL),
(152, 3, 2, 1, NULL),
(153, 3, 10, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `game_types`
--

CREATE TABLE `game_types` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_types`
--

INSERT INTO `game_types` (`id`, `name`) VALUES
(1, 'Mistrovské utkání');

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `id` int NOT NULL,
  `training_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT 'Domácí hřiště',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`id`, `training_date`, `location`, `description`) VALUES
(1, '2026-02-17 17:30:00', 'Umělka Zlámanec', NULL),
(2, '2026-02-20 17:30:00', 'Umělka Zlámanec', NULL),
(16, '2026-03-06 17:00:00', 'Hřiště Částkov', NULL),
(17, '2026-03-13 17:00:00', 'Hřiště Částkov', NULL),
(18, '2026-03-20 17:00:00', 'Hřiště Částkov', NULL),
(19, '2026-03-27 17:00:00', 'Hřiště Částkov', NULL),
(20, '2026-04-03 17:30:00', 'Hřiště Částkov', NULL),
(21, '2026-04-10 17:30:00', 'Hřiště Částkov', NULL),
(22, '2026-04-17 17:30:00', 'Hřiště Částkov', NULL),
(23, '2026-04-24 17:30:00', 'Hřiště Částkov', NULL),
(24, '2026-05-01 18:00:00', 'Hřiště Částkov', NULL),
(25, '2026-05-08 18:00:00', 'Hřiště Částkov', NULL),
(26, '2026-05-15 18:00:00', 'Hřiště Částkov', NULL),
(27, '2026-05-22 18:00:00', 'Hřiště Částkov', NULL),
(28, '2026-05-29 18:00:00', 'Hřiště Částkov', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_attendance`
--

CREATE TABLE `training_attendance` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `training_id` int NOT NULL,
  `status_id` int NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `training_attendance`
--

INSERT INTO `training_attendance` (`id`, `user_id`, `training_id`, `status_id`, `note`) VALUES
(18, 4, 16, 1, NULL),
(19, 4, 17, 1, NULL),
(20, 4, 18, 1, NULL),
(21, 4, 19, 1, NULL),
(22, 4, 20, 1, NULL),
(23, 5, 16, 2, NULL),
(24, 5, 17, 2, NULL),
(25, 5, 18, 2, NULL),
(26, 5, 19, 2, NULL),
(27, 5, 20, 2, NULL),
(28, 6, 16, 1, NULL),
(29, 6, 17, 1, NULL),
(30, 6, 18, 2, NULL),
(31, 6, 19, 1, NULL),
(32, 6, 20, 1, NULL),
(33, 7, 16, 1, NULL),
(34, 7, 17, 2, NULL),
(35, 7, 18, 1, NULL),
(36, 7, 19, 1, NULL),
(37, 7, 20, 2, NULL),
(38, 8, 16, 1, NULL),
(39, 8, 17, 1, NULL),
(40, 8, 18, 1, NULL),
(41, 8, 19, 1, NULL),
(42, 8, 20, 1, NULL),
(43, 9, 16, 2, NULL),
(44, 9, 17, 1, NULL),
(45, 9, 18, 1, NULL),
(46, 9, 19, 2, NULL),
(47, 9, 20, 1, NULL),
(48, 10, 16, 1, NULL),
(49, 10, 17, 2, NULL),
(50, 10, 18, 1, NULL),
(51, 10, 19, 2, NULL),
(52, 10, 20, 1, NULL),
(53, 11, 16, 2, NULL),
(54, 11, 17, 2, NULL),
(55, 11, 18, 2, NULL),
(56, 11, 19, 1, NULL),
(57, 11, 20, 2, NULL),
(58, 12, 16, 2, NULL),
(59, 12, 17, 1, NULL),
(60, 12, 18, 2, NULL),
(61, 12, 19, 2, NULL),
(62, 12, 20, 2, NULL),
(63, 4, 1, 1, NULL),
(64, 4, 2, 1, NULL),
(65, 4, 21, 1, NULL),
(66, 4, 22, 1, NULL),
(67, 4, 23, 1, NULL),
(68, 4, 24, 1, NULL),
(69, 4, 25, 1, NULL),
(70, 4, 26, 1, NULL),
(71, 4, 27, 1, NULL),
(72, 5, 1, 2, NULL),
(73, 5, 2, 2, NULL),
(74, 5, 21, 2, NULL),
(75, 5, 22, 2, NULL),
(76, 5, 23, 2, NULL),
(77, 5, 24, 2, NULL),
(78, 5, 25, 2, NULL),
(79, 5, 26, 2, NULL),
(80, 5, 27, 2, NULL),
(81, 6, 1, 1, NULL),
(82, 6, 2, 1, NULL),
(83, 6, 21, 1, NULL),
(84, 6, 22, 1, NULL),
(85, 6, 23, 1, NULL),
(86, 6, 24, 1, NULL),
(87, 6, 25, 1, NULL),
(88, 6, 26, 2, NULL),
(89, 6, 27, 1, NULL),
(90, 7, 1, 1, NULL),
(91, 7, 2, 2, NULL),
(92, 7, 21, 1, NULL),
(93, 7, 22, 2, NULL),
(94, 7, 23, 1, NULL),
(95, 7, 24, 2, NULL),
(96, 7, 25, 1, NULL),
(97, 7, 26, 1, NULL),
(98, 7, 27, 2, NULL),
(99, 8, 1, 1, NULL),
(100, 8, 2, 1, NULL),
(101, 8, 21, 1, NULL),
(102, 8, 22, 1, NULL),
(103, 8, 23, 1, NULL),
(104, 8, 24, 1, NULL),
(105, 8, 25, 1, NULL),
(106, 8, 26, 1, NULL),
(107, 8, 27, 1, NULL),
(108, 9, 1, 2, NULL),
(109, 9, 2, 1, NULL),
(110, 9, 21, 1, NULL),
(111, 9, 22, 2, NULL),
(112, 9, 23, 1, NULL),
(113, 9, 24, 1, NULL),
(114, 9, 25, 2, NULL),
(115, 9, 26, 1, NULL),
(116, 9, 27, 1, NULL),
(117, 10, 1, 1, NULL),
(118, 10, 2, 2, NULL),
(119, 10, 21, 2, NULL),
(120, 10, 22, 1, NULL),
(121, 10, 23, 2, NULL),
(122, 10, 24, 1, NULL),
(123, 10, 25, 2, NULL),
(124, 10, 26, 1, NULL),
(125, 10, 27, 2, NULL),
(126, 11, 1, 2, NULL),
(127, 11, 2, 2, NULL),
(128, 11, 21, 2, NULL),
(129, 11, 22, 2, NULL),
(130, 11, 23, 1, NULL),
(131, 11, 24, 2, NULL),
(132, 11, 25, 2, NULL),
(133, 11, 26, 2, NULL),
(134, 11, 27, 2, NULL),
(135, 12, 1, 2, NULL),
(136, 12, 2, 1, NULL),
(137, 12, 21, 1, NULL),
(138, 12, 22, 2, NULL),
(139, 12, 23, 2, NULL),
(140, 12, 24, 2, NULL),
(141, 12, 25, 1, NULL),
(142, 12, 26, 2, NULL),
(143, 12, 27, 2, NULL),
(145, 3, 28, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','player') DEFAULT 'player'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(3, 'admin', 'admin@admin.cz', '$2y$12$m3tAsB5NRmZKz0HdUGrcc.Vp76ohW2rU9NTqvYQw8AdY3TgWWiNfm', 'admin'),
(4, 'Petr Novák', 'novak@caca.cz', '$2y$12$LXBut0KFXWAT1l8bUFLwsOlWtLBkrrcbt/xwKLdMfnoXHzv4cYOia', 'player'),
(5, 'Jaroslav Kučera', 'kucera@caca.cz', '$2y$12$elUgJBczb92lpLE7d2NLWe36LoiC..K647occCJb.AUCs3N0zWNnK', 'player'),
(6, 'Martin Dvořák', 'dvorak@caca.cz', '$2y$12$JJAMrWZmsGphk6AjgGVNJuoyuprhnARmCwHqSReG9OIt/de5q6u9i', 'player'),
(7, 'Michal Svoboda', 'svoboda@caca.cz', '$2y$12$WePuRwef9.j3/4/yBL6AM.ULEhWjZ2lr/TCvOBAYS28ET/0NIDHRy', 'player'),
(8, 'Tomáš Marek', 'marek@caca.cz', '$2y$12$2Sj3GfahuJffujkYwbFo7e7RaVoUKgLvSY5T0ww9HCcXj6AHiH4/a', 'player'),
(9, 'Pavel Beneš', 'benes@caca.cz', '$2y$12$NW55L6s3vETJ0TfqXuGL5.mny3mXt3OqjDJwFU004ygvix4hhXqQW', 'player'),
(10, 'Lukáš Černý', 'cerny@caca.cz', '$2y$12$uRkfFTXm9iJomalLItYPU.coiaCSAHSTZTaoL7nokuIRm9U0Xvyc6', 'player'),
(11, 'Zdeněk Procházka', 'prochazka@caca.cz', '$2y$12$eWhvQIgDYXGdfIPwfAn6HOM90Xedli9MtY3kGS3580JbwmuaYoDry', 'player'),
(12, 'Jiří Veselý', 'vesely@caca.cz', '$2y$12$jDsu3rEQVnMnYKBkUUC14ebqV8C409KSCL99AgzwnDkK.d3UIvFsa', 'player');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_statuses`
--
ALTER TABLE `attendance_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_type_id` (`match_type_id`);

--
-- Indexes for table `game_attendance`
--
ALTER TABLE `game_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `match_id` (`game_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `game_types`
--
ALTER TABLE `game_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_id` (`training_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `training_attendance_ibfk_1` (`user_id`);

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
-- AUTO_INCREMENT for table `attendance_statuses`
--
ALTER TABLE `attendance_statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `game_attendance`
--
ALTER TABLE `game_attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `game_types`
--
ALTER TABLE `game_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `training_attendance`
--
ALTER TABLE `training_attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`match_type_id`) REFERENCES `game_types` (`id`);

--
-- Constraints for table `game_attendance`
--
ALTER TABLE `game_attendance`
  ADD CONSTRAINT `game_attendance_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_attendance_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `attendance_statuses` (`id`);

--
-- Constraints for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD CONSTRAINT `training_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `training_attendance_ibfk_2` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_attendance_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `attendance_statuses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
