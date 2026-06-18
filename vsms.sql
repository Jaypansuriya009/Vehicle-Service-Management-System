-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 08:38 PM
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
-- Database: `vsms`
--

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `inquiry_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responce` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `user_id`, `inquiry_type`, `description`, `created_at`, `responce`) VALUES
(1, 2, 'Service Request', 'general service.', '2025-02-22 05:59:42', 'done'),
(2, 2, 'Service Request', 'general service', '2025-03-07 14:08:29', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `mechanics`
--

CREATE TABLE `mechanics` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanics`
--

INSERT INTO `mechanics` (`id`, `name`, `email`, `contact`, `address`, `created_at`) VALUES
(1, 'sanjay', 'sanjay789@gmail.com', '1234567891', 'navrangpura', '2025-02-22 05:54:50'),
(2, 'Vishal', 'vishalgamara@gmail.com', '8160534346', 'south bopal', '2025-03-19 20:43:27');

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_brand` varchar(100) NOT NULL,
  `vehicle_registration_number` varchar(50) NOT NULL,
  `service_date` date NOT NULL,
  `service_time` time NOT NULL,
  `delivery_type` enum('Pickup','Drop-off') NOT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','In Progress','Completed','Rejected') NOT NULL DEFAULT 'Pending',
  `admin_remark` text DEFAULT NULL,
  `admin_remark_date` datetime DEFAULT NULL,
  `service_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `parts_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mechanic_id` int(11) DEFAULT NULL,
  `assigned_mechanic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `user_id`, `category`, `vehicle_name`, `vehicle_model`, `vehicle_brand`, `vehicle_registration_number`, `service_date`, `service_time`, `delivery_type`, `terms_accepted`, `created_at`, `status`, `admin_remark`, `admin_remark_date`, `service_charge`, `parts_charge`, `other_charges`, `total_amount`, `mechanic_id`, `assigned_mechanic`) VALUES
(1, 2, '2', 'dream yuga', 'old', 'honda', 'GJ-01-LF-1140', '2025-01-22', '12:30:00', 'Drop-off', 1, '2025-02-22 05:58:32', 'Completed', 'general service done', '2025-04-05 20:27:59', 300.00, 50.00, 50.00, 400.00, 1, 'Vishal'),
(2, 2, '2', 'Shine', 'new', 'honda', 'GJ-01-LF-1140', '2025-03-09', '15:30:00', 'Drop-off', 1, '2025-03-07 14:06:50', 'Pending', '', '2025-04-05 21:08:21', 0.00, 0.00, 0.00, 0.00, NULL, 'sanjay'),
(4, 2, '1', 'activa', '2014', 'honda', 'gj-01-lf-1140', '2025-04-10', '10:30:00', 'Drop-off', 1, '2025-04-06 19:33:02', 'Completed', 'done', '2025-04-06 21:36:22', 300.00, 100.00, 50.00, 450.00, NULL, 'sanjay');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone_number`, `password`, `created_at`, `updated_at`, `profile_image`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '9925149988', '$2y$10$sjMn4J61bdDHKTh6Wqc7peiaE/pyvRq2SzXMP4Wiy5U/Yzrv6Yuhe', '2025-02-22 05:50:25', '2025-04-15 18:32:03', 'profile_1_1741021093.jpg', 'admin'),
(2, 'user', 'user@gmail.com', '9723741330', '$2y$10$rr0Kq/9W0BUb3VksJ/aI8.yc7XRVbXogOZDrCswM9kHzMiJM0DNmK', '2025-02-22 05:53:01', '2025-04-15 18:34:05', 'profile_2_1741356203.jpg', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_categories`
--

CREATE TABLE `vehicle_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_categories`
--

INSERT INTO `vehicle_categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'moped', '2025-02-22 05:55:48'),
(2, 'Bike', '2025-02-22 05:56:29'),
(3, 'Bullet', '2025-02-22 05:56:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mechanics`
--
ALTER TABLE `mechanics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicle_categories`
--
ALTER TABLE `vehicle_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mechanics`
--
ALTER TABLE `mechanics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicle_categories`
--
ALTER TABLE `vehicle_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
