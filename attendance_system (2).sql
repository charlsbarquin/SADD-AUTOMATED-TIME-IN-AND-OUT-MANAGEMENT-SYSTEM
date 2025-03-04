-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Feb 25, 2025 at 04:19 AM
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
-- Database: `attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `auto_logout_time` datetime DEFAULT NULL,
  `work_duration` varchar(10) DEFAULT '0 hrs',
  `status` enum('Present','Absent','On Leave') NOT NULL,
  `face_scan_image` varchar(255) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `checkin_date` date NOT NULL DEFAULT curdate(),
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `auto_timeout` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `professor_id`, `check_in`, `check_out`, `auto_logout_time`, `work_duration`, `status`, `face_scan_image`, `recorded_at`, `checkin_date`, `latitude`, `longitude`, `accuracy`, `device_type`, `auto_timeout`) VALUES
(150, 5, '2025-02-24 10:29:08', '2025-02-24 10:29:16', NULL, '00:00:08', 'Present', 'checkin_1740364148.jpg', '2025-02-24 02:29:08', '2025-02-24', '14.2990183', '120.9589699', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `action`, `user`, `timestamp`) VALUES
(1, 'Professor John Doe checked in at 8:05 AM', 'John Doe', '2025-02-08 05:23:45'),
(2, 'Admin generated attendance report', 'Admin', '2025-02-08 05:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('check-in','check-out','settings') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `type`, `created_at`, `is_read`) VALUES
(1, 'Test notification: A professor checked in.', 'check-in', '2025-02-15 09:42:32', 1),
(2, 'Professor ID 2 just checked in.', 'check-in', '2025-02-16 06:37:18', 0),
(3, 'Professor ID 10 just checked in.', 'check-in', '2025-02-16 06:38:31', 0),
(4, 'Professor ID 1 just checked in.', 'check-in', '2025-02-16 06:40:37', 1),
(5, 'Professor ID 4 just checked in.', 'check-in', '2025-02-16 06:41:47', 0),
(6, 'Professor ID 5 just checked in.', 'check-in', '2025-02-16 06:45:44', 0),
(7, 'Professor ID 1 just checked in.', 'check-in', '2025-02-16 06:54:53', 1),
(8, 'Professor ID 10 just checked in.', 'check-in', '2025-02-16 07:04:48', 0),
(9, 'Professor ID 3 just checked in.', 'check-in', '2025-02-16 07:08:23', 1),
(10, 'Professor ID 11 just checked in.', 'check-in', '2025-02-16 07:16:23', 1),
(11, 'Professor ID 13 just checked in.', 'check-in', '2025-02-16 07:16:51', 0),
(12, 'Professor ID 5 just checked in.', 'check-in', '2025-02-16 07:19:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`id`, `name`, `email`, `designation`, `profile_image`, `created_at`) VALUES
(1, 'Dr. John Doe', 'johndoe@bup.edu.ph', 'Professor', '', '2025-02-08 05:22:23'),
(2, 'Dr. Jane Smith', 'janesmith@bup.edu.ph', 'Associate Professor', '', '2025-02-08 05:22:23'),
(3, 'Dr. Mark Dela Cruz', 'markdelacruz@bup.edu.ph', 'Assistant Professor', '', '2025-02-08 05:22:23'),
(4, 'Dr. Maria Santos', 'mariasantos@bup.edu.ph', 'Professor', '', '2025-02-08 06:17:30'),
(5, 'Dr. Rafael Cruz', 'rafaelcruz@bup.edu.ph', 'Associate Professor', '', '2025-02-08 06:17:30'),
(6, 'Dr. Angela Reyes', 'angelareyes@bup.edu.ph', 'Assistant Professor', '', '2025-02-08 06:17:30'),
(7, 'Dr. Michael Tan', 'michaeltan@bup.edu.ph', 'Professor', '', '2025-02-08 06:17:30'),
(8, 'Dr. Sophia Gomez', 'sophiagomez@bup.edu.ph', 'Professor', '', '2025-02-08 06:17:30'),
(9, 'Dr. Carlos Villanueva', 'carlosvillanueva@bup.edu.ph', 'Associate Professor', '', '2025-02-08 06:17:30'),
(10, 'Dr. Jessica Lim', 'jessicalim@bup.edu.ph', 'Assistant Professor', '', '2025-02-08 06:17:30'),
(11, 'Dr. Benedict Chua', 'benedictchua@bup.edu.ph', 'Professor', '', '2025-02-08 06:17:30'),
(12, 'Dr. Olivia Mendoza', 'oliviamendoza@bup.edu.ph', 'Professor', '', '2025-02-08 06:17:30'),
(13, 'Dr. Roberto Fernandez', 'robertofernandez@bup.edu.ph', 'Associate Professor', '', '2025-02-08 06:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `late_cutoff` time NOT NULL DEFAULT '08:00:00',
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Manila',
  `allow_auto_timeout` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `late_cutoff`, `timezone`, `allow_auto_timeout`) VALUES
(1, '08:00:00', 'Asia/Manila', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_checkin` (`professor_id`,`checkin_date`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_professor_attendance` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
