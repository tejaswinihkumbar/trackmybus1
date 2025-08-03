-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 09:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trackmybus`
--

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `vehicle_number`, `route_id`, `driver_id`) VALUES
(1, 'KA-01 AB-1234', 7, 1),
(3, 'KA-01 F-3975', 8, 9);

-- --------------------------------------------------------

--
-- Table structure for table `bus_assignments`
--

CREATE TABLE `bus_assignments` (
  `id` int(11) NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `bus_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_us_users_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(256) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`id`, `contact_us_users_id`, `full_name`, `email`, `message`, `created_at`) VALUES
(1, 17, 'Sahana Huded', 'hudedshana@gmail.com', 'i want know the fees structure', '2025-06-02 16:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `trip_status` varchar(20) DEFAULT NULL,
  `trip_time` datetime DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `email`, `password`, `phone`, `trip_status`, `trip_time`, `user_id`) VALUES
(1, 'Pooja kumbar', 'poojakumbar2004@gmail.com', '$2y$10$H474OHoQ/b.nkEOyh/Y83.SRCxMn74cih2Qd8Aqv6djoQXyGuR7B2', '7483116691', NULL, NULL, 18),
(9, NULL, NULL, NULL, NULL, NULL, NULL, 20),
(10, NULL, NULL, NULL, NULL, NULL, NULL, 23);

-- --------------------------------------------------------

--
-- Table structure for table `location_updates`
--

CREATE TABLE `location_updates` (
  `id` int(11) NOT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `stop_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` enum('ARRIVED','DELAYED','ON_ROUTE') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'ADMIN'),
(3, 'DRIVER'),
(1, 'USER');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `name`) VALUES
(7, 'Old Bus Stand'),
(8, 'CBT');

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

CREATE TABLE `route_stops` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `stop_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_stops`
--

INSERT INTO `route_stops` (`id`, `route_id`, `stop_id`) VALUES
(31, 8, 39),
(32, 8, 40),
(33, 8, 41),
(34, 8, 42),
(35, 8, 43),
(36, 8, 44),
(37, 8, 45),
(38, 8, 46),
(39, 8, 47),
(40, 8, 48);

-- --------------------------------------------------------

--
-- Table structure for table `stops`
--

CREATE TABLE `stops` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `stop_order` int(11) DEFAULT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stops`
--

INSERT INTO `stops` (`id`, `name`, `route_id`, `stop_order`, `bus_id`, `latitude`, `longitude`) VALUES
(1, 'IBMR College', 7, 0, 1, 15.36450000, 75.12400000),
(2, 'Sidheshwar Park', 7, 1, 1, 15.36550000, 75.12700000),
(3, 'Shirur Park', 7, 2, 1, 15.36750000, 75.12900000),
(4, 'Vidhyanagar', 7, 3, 1, 15.36800000, 75.13050000),
(5, 'KIMS', 7, 4, 1, 15.36950000, 75.13200000),
(6, 'Hosur Bus Stand', 7, 5, 1, 15.37100000, 75.13350000),
(7, 'Hosur Cross', 7, 6, 1, 15.37300000, 75.13500000),
(8, 'OBS', 7, 7, 1, 15.37450000, 75.13650000),
(39, 'IBMR College', 8, 0, 3, 15.35800500, 75.10992500),
(40, 'Siddeshwar Park', 8, 1, 3, 15.36256500, 75.11498300),
(41, 'Shirur Park', 8, 2, 3, 15.36350500, 75.12062400),
(42, 'Vidhyanagar', 8, 3, 3, 15.36460500, 75.12391800),
(43, 'Hosur Bus Stand', 8, 4, 3, 15.35711100, 75.12881600),
(44, 'OBS', 8, 5, 3, 15.35085900, 75.13638000),
(45, 'Corporation', 8, 6, 3, 15.35094200, 75.13974400),
(46, 'Railway Station', 8, 7, 3, 15.34982400, 75.14879900),
(47, 'Chandrakala Talkies', 8, 8, 3, 15.34886400, 75.14587400),
(48, 'CBT', 8, 9, 3, 15.34450500, 75.14568700);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `status` enum('not_started','started','ended') DEFAULT 'not_started',
  `current_stop` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `stop_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `bus_id`, `status`, `current_stop`, `start_time`, `end_time`, `stop_order`) VALUES
(67, 1, 'ended', NULL, '2025-05-29 15:40:35', '2025-05-29 16:10:47', 7),
(68, 1, 'ended', NULL, '2025-05-29 16:10:57', '2025-05-29 16:11:04', 0),
(69, 1, 'ended', NULL, '2025-05-29 16:14:35', '2025-05-29 16:14:53', 7),
(70, 1, 'ended', NULL, '2025-05-29 16:31:10', '2025-05-29 16:38:08', 0),
(71, 1, 'ended', NULL, '2025-05-29 16:38:00', '2025-05-29 16:38:08', 0),
(72, 1, 'ended', NULL, '2025-05-29 16:38:12', '2025-05-29 16:56:35', 0),
(73, 1, 'ended', NULL, '2025-05-29 16:38:58', '2025-05-29 16:56:35', 0),
(74, 1, 'ended', NULL, '2025-05-29 16:45:25', '2025-05-29 16:56:35', 0),
(75, 1, 'ended', NULL, '2025-05-29 16:45:31', '2025-05-29 16:56:35', 0),
(76, 1, 'ended', NULL, '2025-05-29 16:56:45', '2025-05-30 16:25:34', 2),
(78, 3, 'ended', NULL, '2025-05-30 16:23:25', '2025-05-30 16:38:59', 0),
(79, 3, 'ended', NULL, '2025-05-30 16:42:11', '2025-05-30 16:42:23', 0),
(80, 3, 'ended', NULL, '2025-06-01 14:00:07', '2025-06-01 14:04:45', 1),
(81, 3, 'ended', NULL, '2025-06-01 14:07:09', '2025-06-01 14:18:13', 2),
(82, 3, 'ended', NULL, '2025-06-01 14:08:34', '2025-06-01 14:18:13', 2),
(83, 3, 'ended', NULL, '2025-06-01 14:08:41', '2025-06-01 14:18:13', 2),
(84, 1, 'ended', NULL, '2025-06-02 09:24:33', '2025-06-02 09:34:21', 1),
(85, 1, 'ended', NULL, '2025-06-02 09:35:10', '2025-06-02 09:36:20', 0),
(86, 3, 'ended', NULL, '2025-06-02 09:36:47', '2025-06-02 09:37:36', 0),
(87, 1, 'started', NULL, '2025-06-02 16:29:29', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trip_current_stop`
--

CREATE TABLE `trip_current_stop` (
  `bus_id` int(11) NOT NULL,
  `stop_order` int(11) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_current_stop`
--

INSERT INTO `trip_current_stop` (`bus_id`, `stop_order`, `updated_at`) VALUES
(1, 7, '2025-05-29 14:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bus_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `email`, `password`, `first_name`, `last_name`, `created_at`, `bus_id`) VALUES
(17, '9980635318', 'tejukumbar2004@gmail.com', '$2y$10$93GOxDnVFoBgta55TOK9XOEuOw6ibE0y.N1kEArAbeVsOBI6N27ba', 'Tejaswini', 'Kumbar', '2025-05-20 10:56:38', NULL),
(18, '8971544093', 'poojakumbar2004@gmail.com', '$2y$10$Z75/O3fKhKeoWaHTZWyNReMe.7L4NIbCOZcbYwoiUO9NLUYwHLhhe', 'Pooja', 'Kumbar', '2025-05-20 14:19:54', 1),
(19, '8618097480', 'hudedshana@gmail.com', '$2y$10$ShwmYkDWuul8l.YxUZ/8u.jfY1gNmFmmye74rHiiYInBkKcASxZwe', 'Sahana', 'Huded', '2025-05-22 10:01:51', 3),
(20, '8867664939', 'lacchipatil2004@gmail.com', '$2y$10$S2dsrIv7R/lPEQg4GLRlOehxRSEpIChm7ReV8euamy5uP2HJGBD4y', 'Nagalaxmi', 'Patil', '2025-05-23 18:17:31', NULL),
(21, '7349377715', 'nayanakodihal2004@gmail.com', '$2y$10$QgJt4gBD6EQEbrNiZvv0wOSwhWQIDx7Mx55A9iv8s3IqqARXMcecO', 'Nayana', 'Kodihal', '2025-05-23 18:20:22', 3),
(22, '8317339818', 'ankitaupper@gmail.com', '$2y$10$ozgaNLWc7qHW/u/ky4PqIeTY2n9OYtRWPPMmkKUBmOie.CbXLrUna', 'Ankita', 'upper', '2025-05-30 10:33:03', 1),
(23, '7795519485', 'apsannadaf83@gmail.com', '$2y$10$CJZKSFPoIgQlTD6eCboQ7eXjC23dxOx42oKYhTvcZYsu7Ky7qhFo2', 'Apsana', 'Nadaf', '2025-05-30 10:46:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(17, 2),
(18, 3),
(19, 1),
(20, 3),
(21, 1),
(22, 1),
(23, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `bus_assignments`
--
ALTER TABLE `bus_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD UNIQUE KEY `bus_id` (`bus_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_us_users_id` (`contact_us_users_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_driver_user` (`user_id`);

--
-- Indexes for table `location_updates`
--
ALTER TABLE `location_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`),
  ADD KEY `stop_id` (`stop_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `stop_id` (`stop_id`);

--
-- Indexes for table `stops`
--
ALTER TABLE `stops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bus_id` (`bus_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `trip_current_stop`
--
ALTER TABLE `trip_current_stop`
  ADD PRIMARY KEY (`bus_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `uq_users_phone_number` (`phone_number`),
  ADD KEY `fk_users_bus_id` (`bus_id`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bus_assignments`
--
ALTER TABLE `bus_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `location_updates`
--
ALTER TABLE `location_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `stops`
--
ALTER TABLE `stops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buses`
--
ALTER TABLE `buses`
  ADD CONSTRAINT `buses_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bus_assignments`
--
ALTER TABLE `bus_assignments`
  ADD CONSTRAINT `bus_assignments_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bus_assignments_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD CONSTRAINT `fk_contact_us_users_id` FOREIGN KEY (`contact_us_users_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `fk_driver_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `location_updates`
--
ALTER TABLE `location_updates`
  ADD CONSTRAINT `location_updates_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_updates_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `route_stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `route_stops_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stops`
--
ALTER TABLE `stops`
  ADD CONSTRAINT `fk_bus_id` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`),
  ADD CONSTRAINT `stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_bus_id` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
