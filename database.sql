-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 06:07 PM
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
-- Database: `nestph_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_access_logs`
--

CREATE TABLE `admin_access_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action` enum('granted','privileges_updated','revoked') NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_access_logs`
--

INSERT INTO `admin_access_logs` (`id`, `user_id`, `performed_by`, `action`, `note`, `created_at`) VALUES
(1, 124, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms, manage_contracts, manage_billing, view_reports', '2026-04-06 16:00:00'),
(2, 125, 3, 'granted', 'Admin access granted: manage_billing, view_reports', '2026-04-15 16:00:00'),
(3, 126, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms', '2026-04-24 16:00:00'),
(4, 127, 3, 'granted', 'Admin access granted: manage_tenants, view_reports', '2026-05-03 16:00:00'),
(5, 125, 3, 'privileges_updated', 'Privileges updated: manage_billing, view_reports', '2026-08-19 16:00:00'),
(6, 127, 3, 'revoked', 'Admin access revoked. Reason: No longer employed at the dormitory.', '2026-09-07 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_sessions`
--

CREATE TABLE `admin_login_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `logged_in_at` datetime NOT NULL,
  `logged_out_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_login_sessions`
--

INSERT INTO `admin_login_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `logged_in_at`, `logged_out_at`) VALUES
(1, 3, '6f48b4c1c7e059e011ee09ea08d7210483c6623c', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 09:13:00', '2026-09-28 12:25:00'),
(2, 3, 'a880e9c6d2c517caf3e62a27ca110452c7d50a9d', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 10:16:00', '2026-09-27 13:28:00'),
(3, 3, '55d735efa7c3a336e3175a729db7a38fb2a98294', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 12:22:00', '2026-09-25 15:34:00'),
(4, 3, 'fe2be0930830e405dc1a5bd64931b559786994bd', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-23 09:28:00', '2026-09-23 12:40:00'),
(5, 3, 'a9a9d0e311f7e0a6910e53e577a9ad4b47472930', '192.168.1.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-20 12:37:00', '2026-09-20 15:49:00'),
(6, 3, '1b99d65eba5acd0ed947fa21572b1ae94011f8f8', '192.168.1.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-16 11:49:00', '2026-09-16 15:01:00'),
(7, 124, '77139c0fd23959d6e63725e3b2043b8bbbb22e89', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 08:10:00', NULL),
(8, 124, '1ab8c61dd58363dc85d8a6fde3b0fc0c809dfa84', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 09:13:00', '2026-09-28 12:25:00'),
(9, 124, '886d77437aa1d33e92ee168c042da8c5942c3580', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 10:16:00', '2026-09-27 13:28:00'),
(10, 124, '61dafe95d7bd86d2cf11511da627d5d74818a4ce', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-26 11:19:00', '2026-09-26 14:31:00'),
(11, 124, 'eb978e017bd8912b824279016139cbbe045149da', '192.168.1.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-24 08:25:00', '2026-09-24 11:37:00'),
(12, 124, '74bca56e08cdead82e4a432b0282675a9e300080', '192.168.1.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-22 10:31:00', '2026-09-22 13:43:00'),
(13, 124, '150f24609e20256a6a261c8a4cfb0a81db2e2135', '192.168.1.28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-21 11:34:00', '2026-09-21 14:46:00'),
(14, 125, 'e96297accd758f24e0bbe8ffc41efe28851b56f0', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 09:13:00', '2026-09-28 12:25:00'),
(15, 125, '7e8ad862e977f077049ea961f2468cbf5531393b', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-26 11:19:00', '2026-09-26 14:31:00'),
(16, 125, '66314511d0af8a15b13f1e97c269dfcc84b38273', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-23 09:28:00', '2026-09-23 12:40:00'),
(17, 125, '8920cbd27b466896817a97a27236286b0675d9c6', '192.168.1.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-19 08:40:00', '2026-09-19 11:52:00'),
(18, 126, 'a55984525f9e44bea122b6ddaec3d8b76b50842e', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 08:10:00', NULL),
(19, 126, '7965bd3715717b69b07a8be6871017df15753914', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 10:16:00', '2026-09-27 13:28:00'),
(20, 126, '8404b875831be93fe68d6843bbac5795310ac9bf', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 12:22:00', '2026-09-25 15:34:00'),
(21, 126, '784646905d7694508c2c9ae1d24487673d13dfe6', '192.168.1.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-18 09:43:00', '2026-09-18 12:55:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_privileges`
--

CREATE TABLE `admin_privileges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `granted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `privilege_name` enum('manage_tenants','manage_rooms','manage_contracts','manage_billing','manage_users','view_reports') NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_privileges`
--

INSERT INTO `admin_privileges` (`id`, `user_id`, `granted_by`, `privilege_name`, `granted_at`) VALUES
(1, 3, NULL, 'manage_tenants', '2026-01-28 16:00:00'),
(2, 3, NULL, 'manage_rooms', '2026-01-28 16:00:00'),
(3, 3, NULL, 'manage_contracts', '2026-01-28 16:00:00'),
(4, 3, NULL, 'manage_billing', '2026-01-28 16:00:00'),
(5, 3, NULL, 'manage_users', '2026-01-28 16:00:00'),
(6, 3, NULL, 'view_reports', '2026-01-28 16:00:00'),
(40, 124, 3, 'manage_tenants', '2026-04-06 16:00:00'),
(41, 124, 3, 'manage_rooms', '2026-04-06 16:00:00'),
(42, 124, 3, 'manage_contracts', '2026-04-06 16:00:00'),
(43, 124, 3, 'manage_billing', '2026-04-06 16:00:00'),
(44, 124, 3, 'view_reports', '2026-04-06 16:00:00'),
(45, 125, 3, 'manage_billing', '2026-04-15 16:00:00'),
(46, 125, 3, 'view_reports', '2026-04-15 16:00:00'),
(47, 126, 3, 'manage_tenants', '2026-04-24 16:00:00'),
(48, 126, 3, 'manage_rooms', '2026-04-24 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `comments_restricted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `user_id`, `body`, `comments_restricted`, `created_at`, `updated_at`) VALUES
(1, 3, '📢 SCHEDULED WATER INTERRUPTION\n\nMaynilad will have a water interruption this Saturday from 9:00 AM to 4:00 PM. Please store enough water the night before. Salamat po sa pag-unawa!', 0, '2026-09-28 00:30:00', '2026-09-28 00:30:00'),
(2, 124, 'Reminder: Rent for this month is due on your billing due date. You may pay via Cash (admin office), GCash, or BDO. Please upload your proof of payment in the portal so we can verify it right away.', 0, '2026-09-23 00:30:00', '2026-09-23 00:30:00'),
(3, 3, 'Fire drill and building inspection next Wednesday, 3:00 PM. Attendance is required for all tenants who are in the building. Comments are turned off for this post.', 1, '2026-09-19 00:30:00', '2026-09-19 00:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_comments`
--

CREATE TABLE `announcement_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `announcement_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement_comments`
--

INSERT INTO `announcement_comments` (`id`, `announcement_id`, `user_id`, `tenant_id`, `body`, `created_at`) VALUES
(1, 1, NULL, 1, 'Noted po, thank you sa heads up!', '2026-09-28 01:30:00'),
(2, 1, NULL, 7, 'Buong araw po ba walang tubig sa lahat ng floors?', '2026-09-28 03:30:00'),
(3, 1, 124, NULL, 'Opo, lahat ng floors po. May drum ng tubig sa ground floor na pwede gamitin.', '2026-09-28 05:30:00'),
(4, 2, NULL, 8, 'Pwede po ba partial muna ngayong week?', '2026-09-23 01:30:00'),
(5, 2, 125, NULL, 'Pwede po, pero may 10% late fee kung lumampas sa 3-day grace period ang natitirang balance.', '2026-09-23 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `nationality` varchar(60) DEFAULT NULL,
  `medical_condition` varchar(255) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `school_company` varchar(150) DEFAULT NULL,
  `school_company_address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `landline` varchar(20) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `emergency_contact_email` varchar(150) DEFAULT NULL,
  `emergency_contact_landline` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `bed_id` bigint(20) UNSIGNED NOT NULL,
  `preferred_start_date` date DEFAULT NULL,
  `tenant_end_date` date DEFAULT NULL,
  `type_of_tenant` varchar(30) DEFAULT NULL,
  `id_document_path` varchar(255) DEFAULT NULL,
  `signed_contract_path` varchar(255) DEFAULT NULL,
  `dpa_consent` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected','re_application_requested','cancelled') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `re_application_note` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `inquiry_id`, `tenant_id`, `first_name`, `last_name`, `birthdate`, `gender`, `nationality`, `medical_condition`, `occupation`, `school_company`, `school_company_address`, `contact_number`, `email`, `landline`, `home_address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_email`, `emergency_contact_landline`, `emergency_contact_relation`, `bed_id`, `preferred_start_date`, `tenant_end_date`, `type_of_tenant`, `id_document_path`, `signed_contract_path`, `dpa_consent`, `status`, `rejection_reason`, `re_application_note`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Maria Angelica', 'Santos', '2004-03-14', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09181242486', 'maria.santos@gmail.com', NULL, 'Brgy. San Isidro, Angono, Rizal', 'Rodelio Santos', '09182034386', 'rodelio.santos.parent@gmail.com', NULL, 'Father', 1, '2026-04-19', '2027-03-18', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-04-08 02:15:00', '2026-04-10 02:15:00'),
(2, NULL, 2, 'Kimberly Anne', 'Dela Cruz', '2005-07-02', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09271250405', 'kimberly.delacruz@gmail.com', NULL, '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'Marites Dela Cruz', '09272042305', 'marites.delacruz.parent@gmail.com', NULL, 'Mother', 2, '2026-06-27', '2027-03-26', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-15 03:15:00', '2026-06-17 03:15:00'),
(3, NULL, 3, 'Patricia Mae', 'Gonzales', '2003-11-21', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09391258324', 'patricia.gonzales@gmail.com', NULL, 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'Lorna Gonzales', '09392050224', 'lorna.gonzales.parent@gmail.com', NULL, 'Mother', 3, '2026-07-25', '2027-03-24', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-07-12 04:15:00', '2026-07-14 04:15:00'),
(4, NULL, 4, 'Nicole Joy', 'Ramos', '2004-01-09', 'female', 'Filipino', 'None', 'Student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila', '09451266243', 'nicole.ramos@gmail.com', NULL, 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'Ernesto Ramos', '09452058143', 'ernesto.ramos.parent@gmail.com', NULL, 'Father', 28, '2026-07-17', '2026-10-19', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-07-03 05:15:00', '2026-07-05 05:15:00'),
(5, NULL, 5, 'Juan Miguel', 'Reyes', '1999-05-30', 'male', 'Filipino', 'None', 'Employee', 'Accenture Philippines', 'Cyber One Bldg., Eastwood City, Quezon City', '09561274162', 'juan.reyes@gmail.com', NULL, '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'Carmelita Reyes', '09562066062', 'carmelita.reyes.parent@gmail.com', NULL, 'Mother', 5, '2026-03-14', '2027-03-13', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-03-04 06:15:00', '2026-03-06 06:15:00'),
(6, NULL, 6, 'John Paul', 'Mendoza', '2004-08-17', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Mapúa University', 'Muralla St., Intramuros, Manila', '09212408565', 'john.mendoza@gmail.com', NULL, 'Brgy. Bagong Silang, Lucena City, Quezon', 'Rosalie Mendoza', '09212408565', 'rosalie.mendoza.parent@gmail.com', NULL, 'Mother', 34, '2026-06-15', '2027-03-14', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-04 07:15:00', '2026-06-06 07:15:00'),
(7, NULL, 7, 'Mark Joseph', 'Aquino', '2005-02-11', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09981290000', 'mark.aquino@gmail.com', NULL, 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'Josefina Aquino', '09982081900', 'josefina.aquino.parent@gmail.com', NULL, 'Mother', 7, '2026-08-24', '2027-03-23', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-08-12 08:15:00', '2026-08-14 08:15:00'),
(8, NULL, 8, 'Christian Dave', 'Torres', '2002-12-03', 'male', 'Filipino', 'None', 'Employee', 'Jollibee Foods Corp. (V. Mapa branch)', 'V. Mapa St., Sta. Mesa, Manila', '09081297919', 'christian.torres@gmail.com', NULL, '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'Dante Torres', '09082089819', 'dante.torres.parent@gmail.com', NULL, 'Father', 8, '2026-05-26', '2027-03-25', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-05-13 01:15:00', '2026-05-15 01:15:00'),
(9, NULL, 9, 'Benjamin', 'Robles', '2004-06-25', 'male', 'Filipino', 'None', 'Student', 'National University', 'M.F. Jhocson St., Sampaloc, Manila', '09212408565', 'benjamin.robles@gmail.com', NULL, 'Brgy. Santo Cristo, San Fernando, Pampanga', 'Evelyn Robles', '09212408565', 'evelyn.robles.parent@gmail.com', NULL, 'Mother', 9, '2026-06-21', '2027-03-20', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-07 02:15:00', '2026-06-09 02:15:00'),
(10, NULL, 10, 'Rafael Luis', 'Navarro', '2001-09-14', 'male', 'Filipino', 'None', 'Employee', 'BDO Unibank, Sta. Mesa Branch', 'Ramon Magsaysay Blvd., Sta. Mesa, Manila', '09171313757', 'rafael.navarro@gmail.com', NULL, '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'Gloria Navarro', '09172105657', 'gloria.navarro.parent@gmail.com', NULL, 'Mother', 37, '2026-04-17', '2026-09-26', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-04-07 03:15:00', '2026-04-09 03:15:00'),
(11, NULL, 11, 'Angela Marie', 'Villanueva', '1998-04-19', 'female', 'Filipino', 'None', 'Employee', 'Philippine General Hospital', 'Taft Ave., Ermita, Manila', '09181321676', 'angela.villanueva@gmail.com', NULL, 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'Arnel Villanueva', '09182113576', 'arnel.villanueva.parent@gmail.com', NULL, 'Father', 12, '2026-01-09', '2027-03-08', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2025-12-29 04:15:00', '2025-12-31 04:15:00'),
(12, NULL, 12, 'Jasmine Rose', 'Garcia', '2005-10-05', 'female', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Centro Escolar University', 'Mendiola St., San Miguel, Manila', '09271329595', 'jasmine.garcia@gmail.com', NULL, 'Purok 2, Brgy. Talon, Las Piñas City', 'Ramil Garcia', '09272121495', 'ramil.garcia.parent@gmail.com', NULL, 'Father', 13, '2026-07-28', '2027-03-27', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-07-16 05:15:00', '2026-07-18 05:15:00'),
(13, NULL, 13, 'Camille Louise', 'Flores', '2004-12-28', 'female', 'Filipino', 'None', 'Student', 'Lyceum of the Philippines University', 'Muralla St., Intramuros, Manila', '09212408565', 'camille.flores@gmail.com', NULL, 'Brgy. Mabini, Batangas City, Batangas', 'Susan Flores', '09212408565', 'susan.flores.parent@gmail.com', NULL, 'Mother', 14, '2026-06-18', '2027-03-17', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-05 06:15:00', '2026-06-07 06:15:00'),
(14, NULL, 14, 'Bea Katrina', 'Pascual', '2003-05-16', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09451345433', 'bea.pascual@gmail.com', NULL, 'Brgy. Sta. Rita, Olongapo City, Zambales', 'Gerardo Pascual', '09452137333', 'gerardo.pascual.parent@gmail.com', NULL, 'Father', 15, '2026-06-29', '2027-03-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-15 07:15:00', '2026-06-17 07:15:00'),
(15, NULL, 15, 'Princess Joy', 'Manalo', '2002-08-08', 'female', 'Filipino', 'None', 'Student', 'Pamantasan ng Lungsod ng Maynila', 'General Luna St., Intramuros, Manila', '09561353352', 'princess.manalo@gmail.com', NULL, 'Brgy. Parian, Calamba City, Laguna', 'Cristina Manalo', '09562145252', 'cristina.manalo.parent@gmail.com', NULL, 'Mother', 16, '2026-06-22', '2027-03-21', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-12 08:15:00', '2026-06-14 08:15:00'),
(16, NULL, 16, 'Kathleen Mae', 'Salazar', '2006-01-30', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09661361271', 'kathleen.salazar@gmail.com', NULL, 'Brgy. Poblacion, Tagum City, Davao del Norte', 'Rogelio Salazar', '09662153171', 'rogelio.salazar.parent@gmail.com', NULL, 'Father', 17, '2026-10-03', '2027-04-02', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-09-22 01:15:00', '2026-09-24 01:15:00'),
(17, NULL, 17, 'Carlo Miguel', 'Bautista', '2000-03-03', 'male', 'Filipino', 'None', 'Employee', 'Globe Telecom', 'The Globe Tower, BGC, Taguig City', '09981369190', 'carlo.bautista@gmail.com', NULL, 'San Pablo City, Laguna', 'Nenita Bautista', '09982161090', 'nenita.bautista.parent@gmail.com', NULL, 'Mother', 18, '2026-07-20', '2027-03-19', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-07-08 02:15:00', '2026-07-10 02:15:00'),
(18, NULL, 18, 'Joshua Emmanuel', 'Lim', '2004-11-11', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'De La Salle University', 'Taft Ave., Malate, Manila', '09081377109', 'joshua.lim@gmail.com', NULL, 'Sta. Cruz, Laguna', 'Wilson Lim', '09082169009', 'wilson.lim.parent@gmail.com', NULL, 'Father', 20, '2026-05-11', '2027-03-10', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-04-28 03:15:00', '2026-04-30 03:15:00'),
(19, NULL, 19, 'Paolo Andres', 'Ocampo', '2005-04-22', 'male', 'Filipino', 'None', 'Student', 'Adamson University', 'San Marcelino St., Ermita, Manila', '09951385028', 'paolo.ocampo@gmail.com', NULL, 'Brgy. Poblacion, Malolos City, Bulacan', 'Amelia Ocampo', '09952176928', 'amelia.ocampo.parent@gmail.com', NULL, 'Mother', 21, '2026-10-03', '2027-04-02', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-09-19 04:15:00', '2026-09-21 04:15:00'),
(20, NULL, 20, 'Andrea Nicole', 'Tan', '1997-09-09', 'female', 'Filipino', 'None', 'Employee', 'SM Supermalls Corporate Office', 'Mall of Asia Complex, Pasay City', '09171392947', 'andrea.tan@gmail.com', NULL, 'Brgy. Kauswagan, Cagayan de Oro City', 'Rebecca Tan', '09172184847', 'rebecca.tan.parent@gmail.com', NULL, 'Mother', 24, '2026-04-23', '2027-03-22', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-04-13 05:15:00', '2026-04-15 05:15:00'),
(21, NULL, 21, 'Erika Jane', 'Morales', '2004-07-07', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09181400866', 'erika.morales@gmail.com', NULL, 'Brgy. Pantal, Dagupan City, Pangasinan', 'Ronaldo Morales', '09182192766', 'ronaldo.morales.parent@gmail.com', NULL, 'Father', 25, '2026-06-16', '2027-03-15', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-06-05 06:15:00', '2026-06-07 06:15:00'),
(22, NULL, 22, 'Hannah Grace', 'Soriano', '2006-02-14', 'female', 'Filipino', 'None', 'Student', 'Philippine Normal University', 'Taft Ave., Ermita, Manila', '09271408785', 'hannah.soriano@gmail.com', NULL, 'Brgy. Dolores, Taytay, Rizal', 'Marilou Soriano', '09272200685', 'marilou.soriano.parent@gmail.com', NULL, 'Mother', 26, '2026-08-25', '2027-03-24', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-08-13 07:15:00', '2026-08-15 07:15:00'),
(23, NULL, 23, 'Gabriel Jose', 'Rivera', '2001-06-01', 'male', 'Filipino', 'None', 'Employee', 'Meralco', 'Ortigas Ave., Pasig City', '09391416704', 'gabriel.rivera@gmail.com', NULL, 'Brgy. San Antonio, Biñan, Laguna', 'Imelda Rivera', '09392208604', 'imelda.rivera.parent@gmail.com', NULL, 'Mother', 29, '2026-03-21', '2026-08-20', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-03-08 08:15:00', '2026-03-10 08:15:00'),
(24, NULL, 24, 'Joseph Allan', 'Cruz', '2003-03-27', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Emilio Aguinaldo College', 'Gen. Malvar St., Malate, Manila', '09212408565', 'joseph.cruz@gmail.com', NULL, 'Brgy. Tabing Ilog, Marilao, Bulacan', 'Nora Cruz', '09212408565', 'nora.cruz.parent@gmail.com', NULL, 'Mother', 31, '2026-04-10', '2027-02-09', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 124, '2026-03-27 01:15:00', '2026-03-29 01:15:00'),
(25, NULL, NULL, 'Ana Beatriz', 'Salonga', '2006-03-08', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09212408565', 'ana.salonga@gmail.com', NULL, 'Brgy. Poblacion, Bontoc, Mountain Province', 'Ramon Salonga', '09173610267', NULL, NULL, 'Father', 19, '2026-10-02', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-27 05:05:00', '2026-09-27 05:05:00'),
(26, NULL, NULL, 'Ryan Christopher', 'Santiago', '2005-09-18', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09182826286', 'ryan.santiago@gmail.com', NULL, 'Brgy. Bagumbayan, Taguig City', 'Rommel Santiago', '09183618186', NULL, NULL, 'Mother', 22, '2026-10-05', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-28 06:05:00', '2026-09-28 06:05:00'),
(27, NULL, NULL, 'Alyssa Mae', 'Mercado', '2006-04-03', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09272834205', 'alyssa.mercado@gmail.com', NULL, 'Brgy. Malinta, Valenzuela City', 'Liza Mercado', '09273626105', NULL, NULL, 'Father', 27, '2026-10-08', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-29 07:05:00', '2026-09-29 07:05:00'),
(28, NULL, NULL, 'Kevin James', 'Dizon', '2000-10-10', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Philippines', 'Manila', '09392842124', 'kevin.dizon@gmail.com', NULL, 'Brgy. San Isidro, Cainta, Rizal', 'Jun Dizon', '09393634024', NULL, NULL, 'Mother', 30, '2026-10-11', '2027-03-29', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-27 08:05:00', '2026-09-27 08:05:00'),
(29, NULL, NULL, 'Sophia Isabel', 'Lopez', '2004-05-25', 'female', 'Filipino', 'None', 'Student', 'San Beda University', 'Manila', '09452850043', 'sophia.lopez@gmail.com', NULL, 'Brgy. Poblacion, Muntinlupa City', 'Maricel Lopez', '09453641943', NULL, NULL, 'Father', 32, '2026-10-14', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'rejected', 'Requested stay is only 1 month; the dormitory requires a minimum 3-month contract.', NULL, NULL, NULL, '2026-09-23 09:05:00', '2026-09-24 09:05:00'),
(30, NULL, NULL, 'Daniel Lorenzo', 'Cruz', '2005-01-15', 'male', 'Filipino', 'None', 'Student', 'Mapúa University', 'Manila', '09562857962', 'daniel.cruz@gmail.com', NULL, 'Brgy. Tambo, Parañaque City', 'Bong Cruz', '09563649862', NULL, NULL, 'Mother', 33, '2026-10-17', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 're_application_requested', NULL, 'Uploaded ID is blurry and the name cannot be read. Please re-apply with a clear photo of a valid school or government ID.', NULL, NULL, '2026-09-25 10:05:00', '2026-09-26 10:05:00'),
(31, NULL, NULL, 'Mika Ella', 'Santos', '2006-08-12', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09662865881', 'mika.santos@gmail.com', NULL, 'Brgy. Sto. Niño, Marikina City', 'Tess Santos', '09663657781', NULL, NULL, 'Father', 35, '2026-10-20', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'cancelled', NULL, NULL, NULL, NULL, '2026-09-20 11:05:00', '2026-09-21 11:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `bed_label` varchar(20) NOT NULL,
  `status` enum('vacant','reserved','occupied','maintenance') NOT NULL DEFAULT 'vacant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `room_id`, `bed_label`, `status`, `created_at`, `updated_at`) VALUES
(1, 16, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(2, 16, 'Bed 2', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(3, 16, 'Bed 3', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(4, 16, 'Bed 4', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(5, 17, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(6, 17, 'Bed 2', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(7, 18, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(8, 18, 'Bed 2', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(9, 18, 'Bed 3', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(10, 18, 'Bed 4', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(11, 44, 'Bed 1', 'maintenance', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(12, 19, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(13, 19, 'Bed 2', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(14, 19, 'Bed 3', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(15, 19, 'Bed 4', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(16, 19, 'Bed 5', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(17, 19, 'Bed 6', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(18, 45, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(19, 45, 'Bed 2', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(20, 46, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(21, 46, 'Bed 2', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(22, 46, 'Bed 3', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(23, 46, 'Bed 4', 'maintenance', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(24, 47, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(25, 48, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(26, 48, 'Bed 2', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(27, 48, 'Bed 3', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(28, 48, 'Bed 4', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(29, 49, 'Bed 1', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(30, 49, 'Bed 2', 'reserved', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(31, 50, 'Bed 1', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(32, 50, 'Bed 2', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(33, 50, 'Bed 3', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(34, 50, 'Bed 4', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(35, 50, 'Bed 5', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(36, 50, 'Bed 6', 'vacant', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(37, 51, 'Bed 1', 'occupied', '2026-01-28 16:00:00', '2026-09-29 15:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `billing_statements`
--

CREATE TABLE `billing_statements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('move_in','monthly') NOT NULL DEFAULT 'monthly',
  `billing_period_start` date NOT NULL,
  `billing_period_end` date NOT NULL,
  `due_date` date NOT NULL,
  `base_rent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `utilities_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wifi_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalty_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','partial','paid','overdue') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing_statements`
--

INSERT INTO `billing_statements` (`id`, `contract_id`, `tenant_id`, `type`, `billing_period_start`, `billing_period_end`, `due_date`, `base_rent`, `utilities_amount`, `wifi_amount`, `penalty_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'move_in', '2026-04-19', '2026-04-19', '2026-04-19', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-04-10 02:15:00', '2026-09-29 15:45:53'),
(2, 1, 1, 'monthly', '2026-04-19', '2026-05-18', '2026-04-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-18 16:05:00', '2026-09-29 15:45:53'),
(3, 1, 1, 'monthly', '2026-05-19', '2026-06-18', '2026-05-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-18 16:05:00', '2026-09-29 15:45:53'),
(4, 1, 1, 'monthly', '2026-06-19', '2026-07-18', '2026-06-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-18 16:05:00', '2026-09-29 15:45:53'),
(5, 1, 1, 'monthly', '2026-07-19', '2026-08-18', '2026-07-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-18 16:05:00', '2026-09-29 15:45:53'),
(6, 1, 1, 'monthly', '2026-08-19', '2026-09-18', '2026-08-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-18 16:05:00', '2026-09-29 15:45:53'),
(7, 1, 1, 'monthly', '2026-09-19', '2026-10-18', '2026-09-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-18 16:05:00', '2026-09-29 15:45:53'),
(8, 2, 2, 'move_in', '2026-06-27', '2026-06-27', '2026-06-27', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-06-17 03:15:00', '2026-09-29 15:45:53'),
(9, 2, 2, 'monthly', '2026-06-27', '2026-07-26', '2026-07-02', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-26 16:05:00', '2026-09-29 15:45:53'),
(10, 2, 2, 'monthly', '2026-07-27', '2026-08-26', '2026-08-01', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-26 16:05:00', '2026-09-29 15:45:53'),
(11, 2, 2, 'monthly', '2026-08-27', '2026-09-26', '2026-09-01', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-26 16:05:00', '2026-09-29 15:45:53'),
(12, 2, 2, 'monthly', '2026-09-27', '2026-10-26', '2026-10-02', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'unpaid', '2026-09-26 16:05:00', '2026-09-29 15:45:53'),
(13, 3, 3, 'move_in', '2026-07-25', '2026-07-25', '2026-07-25', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-07-14 04:15:00', '2026-09-29 15:45:53'),
(14, 3, 3, 'monthly', '2026-07-25', '2026-08-24', '2026-07-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-24 16:05:00', '2026-09-29 15:45:53'),
(15, 3, 3, 'monthly', '2026-08-25', '2026-09-24', '2026-08-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-24 16:05:00', '2026-09-29 15:45:53'),
(16, 3, 3, 'monthly', '2026-09-25', '2026-10-24', '2026-09-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'unpaid', '2026-09-24 16:05:00', '2026-09-29 15:45:53'),
(17, 4, 4, 'move_in', '2026-07-17', '2026-07-17', '2026-07-17', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-07-05 05:15:00', '2026-09-29 15:45:53'),
(18, 4, 4, 'monthly', '2026-07-17', '2026-08-16', '2026-07-22', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-07-16 16:05:00', '2026-09-29 15:45:53'),
(19, 4, 4, 'monthly', '2026-08-17', '2026-09-16', '2026-08-22', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-16 16:05:00', '2026-09-29 15:45:53'),
(20, 4, 4, 'monthly', '2026-09-17', '2026-10-16', '2026-09-22', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-16 16:05:00', '2026-09-29 15:45:53'),
(21, 5, 5, 'move_in', '2026-03-14', '2026-03-14', '2026-03-14', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-06 06:15:00', '2026-09-29 15:45:53'),
(22, 5, 5, 'monthly', '2026-03-14', '2026-04-13', '2026-03-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-13 16:05:00', '2026-09-29 15:45:53'),
(23, 5, 5, 'monthly', '2026-04-14', '2026-05-13', '2026-04-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-04-13 16:05:00', '2026-09-29 15:45:53'),
(24, 5, 5, 'monthly', '2026-05-14', '2026-06-13', '2026-05-19', 4500.00, 600.00, 300.00, 450.00, 5850.00, 'paid', '2026-05-13 16:05:00', '2026-09-29 15:45:53'),
(25, 5, 5, 'monthly', '2026-06-14', '2026-07-13', '2026-06-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-13 16:05:00', '2026-09-29 15:45:53'),
(26, 5, 5, 'monthly', '2026-07-14', '2026-08-13', '2026-07-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-13 16:05:00', '2026-09-29 15:45:53'),
(27, 5, 5, 'monthly', '2026-08-14', '2026-09-13', '2026-08-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-08-13 16:05:00', '2026-09-29 15:45:53'),
(28, 5, 5, 'monthly', '2026-09-14', '2026-10-13', '2026-09-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-09-13 16:05:00', '2026-09-29 15:45:53'),
(29, 6, 6, 'move_in', '2026-06-15', '2026-06-15', '2026-06-15', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-06-06 07:15:00', '2026-09-29 15:45:53'),
(30, 6, 6, 'monthly', '2026-06-15', '2026-07-14', '2026-06-20', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-14 16:05:00', '2026-09-29 15:45:53'),
(31, 6, 6, 'monthly', '2026-07-15', '2026-08-14', '2026-07-20', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-14 16:05:00', '2026-09-29 15:45:53'),
(32, 6, 6, 'monthly', '2026-08-15', '2026-09-14', '2026-08-20', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-08-14 16:05:00', '2026-09-29 15:45:53'),
(33, 6, 6, 'monthly', '2026-09-15', '2026-10-14', '2026-09-20', 2600.00, 400.00, 200.00, 260.00, 3460.00, 'overdue', '2026-09-14 16:05:00', '2026-09-29 15:45:53'),
(34, 7, 7, 'move_in', '2026-08-24', '2026-08-24', '2026-08-24', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-08-14 08:15:00', '2026-09-29 15:45:53'),
(35, 7, 7, 'monthly', '2026-08-24', '2026-09-23', '2026-08-29', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-23 16:05:00', '2026-09-29 15:45:53'),
(36, 7, 7, 'monthly', '2026-09-24', '2026-10-23', '2026-09-29', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-09-23 16:05:00', '2026-09-29 15:45:53'),
(37, 8, 8, 'move_in', '2026-05-26', '2026-05-26', '2026-05-26', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-05-15 01:15:00', '2026-09-29 15:45:53'),
(38, 8, 8, 'monthly', '2026-05-26', '2026-06-25', '2026-05-31', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-05-25 16:05:00', '2026-09-29 15:45:53'),
(39, 8, 8, 'monthly', '2026-06-26', '2026-07-25', '2026-07-01', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-06-25 16:05:00', '2026-09-29 15:45:53'),
(40, 8, 8, 'monthly', '2026-07-26', '2026-08-25', '2026-07-31', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-25 16:05:00', '2026-09-29 15:45:53'),
(41, 8, 8, 'monthly', '2026-08-26', '2026-09-25', '2026-08-31', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-25 16:05:00', '2026-09-29 15:45:53'),
(42, 8, 8, 'monthly', '2026-09-26', '2026-10-25', '2026-10-01', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'partial', '2026-09-25 16:05:00', '2026-09-29 15:45:53'),
(43, 9, 9, 'move_in', '2026-06-21', '2026-06-21', '2026-06-21', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-06-09 02:15:00', '2026-09-29 15:45:53'),
(44, 9, 9, 'monthly', '2026-06-21', '2026-07-20', '2026-06-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-06-20 16:05:00', '2026-09-29 15:45:53'),
(45, 9, 9, 'monthly', '2026-07-21', '2026-08-20', '2026-07-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-20 16:05:00', '2026-09-29 15:45:53'),
(46, 9, 9, 'monthly', '2026-08-21', '2026-09-20', '2026-08-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-20 16:05:00', '2026-09-29 15:45:53'),
(47, 9, 9, 'monthly', '2026-09-21', '2026-10-20', '2026-09-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'overdue', '2026-09-20 16:05:00', '2026-09-29 15:45:53'),
(48, 10, 10, 'move_in', '2026-04-17', '2026-04-17', '2026-04-17', 15600.00, 0.00, 0.00, 0.00, 15600.00, 'paid', '2026-04-09 03:15:00', '2026-09-29 15:45:53'),
(49, 10, 10, 'monthly', '2026-04-17', '2026-05-16', '2026-04-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-04-16 16:05:00', '2026-09-29 15:45:53'),
(50, 10, 10, 'monthly', '2026-05-17', '2026-06-16', '2026-05-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-05-16 16:05:00', '2026-09-29 15:45:53'),
(51, 10, 10, 'monthly', '2026-06-17', '2026-07-16', '2026-06-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-06-16 16:05:00', '2026-09-29 15:45:53'),
(52, 10, 10, 'monthly', '2026-07-17', '2026-08-16', '2026-07-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-07-16 16:05:00', '2026-09-29 15:45:53'),
(53, 10, 10, 'monthly', '2026-08-17', '2026-09-16', '2026-08-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-08-16 16:05:00', '2026-09-29 15:45:53'),
(54, 10, 10, 'monthly', '2026-09-17', '2026-10-16', '2026-09-22', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-09-16 16:05:00', '2026-09-29 15:45:53'),
(55, 11, 11, 'move_in', '2026-01-09', '2026-01-09', '2026-01-09', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 04:15:00', '2026-09-29 15:45:53'),
(56, 11, 11, 'monthly', '2026-01-09', '2026-02-08', '2026-01-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-01-08 16:05:00', '2026-09-29 15:45:53'),
(57, 11, 11, 'monthly', '2026-02-09', '2026-03-08', '2026-02-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-02-08 16:05:00', '2026-09-29 15:45:53'),
(58, 11, 11, 'monthly', '2026-03-09', '2026-04-08', '2026-03-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-03-08 16:05:00', '2026-09-29 15:45:53'),
(59, 11, 11, 'monthly', '2026-04-09', '2026-05-08', '2026-04-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-04-08 16:05:00', '2026-09-29 15:45:53'),
(60, 11, 11, 'monthly', '2026-05-09', '2026-06-08', '2026-05-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-05-08 16:05:00', '2026-09-29 15:45:53'),
(61, 11, 11, 'monthly', '2026-06-09', '2026-07-08', '2026-06-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-06-08 16:05:00', '2026-09-29 15:45:53'),
(62, 11, 11, 'monthly', '2026-07-09', '2026-08-08', '2026-07-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-07-08 16:05:00', '2026-09-29 15:45:53'),
(63, 11, 11, 'monthly', '2026-08-09', '2026-09-08', '2026-08-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-08-08 16:05:00', '2026-09-29 15:45:53'),
(64, 11, 11, 'monthly', '2026-09-09', '2026-10-08', '2026-09-14', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-09-08 16:05:00', '2026-09-29 15:45:53'),
(65, 12, 12, 'move_in', '2026-07-28', '2026-07-28', '2026-07-28', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-07-18 05:15:00', '2026-09-29 15:45:53'),
(66, 12, 12, 'monthly', '2026-07-28', '2026-08-27', '2026-08-02', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-27 16:05:00', '2026-09-29 15:45:53'),
(67, 12, 12, 'monthly', '2026-08-28', '2026-09-27', '2026-09-02', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-27 16:05:00', '2026-09-29 15:45:53'),
(68, 12, 12, 'monthly', '2026-09-28', '2026-10-27', '2026-10-03', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'unpaid', '2026-09-27 16:05:00', '2026-09-29 15:45:53'),
(69, 13, 13, 'move_in', '2026-06-18', '2026-06-18', '2026-06-18', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(70, 13, 13, 'monthly', '2026-06-18', '2026-07-17', '2026-06-23', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-17 16:05:00', '2026-09-29 15:45:53'),
(71, 13, 13, 'monthly', '2026-07-18', '2026-08-17', '2026-07-23', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-17 16:05:00', '2026-09-29 15:45:53'),
(72, 13, 13, 'monthly', '2026-08-18', '2026-09-17', '2026-08-23', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-17 16:05:00', '2026-09-29 15:45:53'),
(73, 13, 13, 'monthly', '2026-09-18', '2026-10-17', '2026-09-23', 2500.00, 400.00, 200.00, 250.00, 3350.00, 'overdue', '2026-09-17 16:05:00', '2026-09-29 15:45:53'),
(74, 14, 14, 'move_in', '2026-06-29', '2026-06-29', '2026-06-29', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-17 07:15:00', '2026-09-29 15:45:53'),
(75, 14, 14, 'monthly', '2026-06-29', '2026-07-28', '2026-07-04', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-28 16:05:00', '2026-09-29 15:45:53'),
(76, 14, 14, 'monthly', '2026-07-29', '2026-08-28', '2026-08-03', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-28 16:05:00', '2026-09-29 15:45:53'),
(77, 14, 14, 'monthly', '2026-08-29', '2026-09-28', '2026-09-03', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-28 16:05:00', '2026-09-29 15:45:53'),
(78, 14, 14, 'monthly', '2026-09-29', '2026-10-28', '2026-10-04', 2500.00, 400.00, 200.00, 800.00, 3900.00, 'unpaid', '2026-09-28 16:05:00', '2026-09-29 15:45:53'),
(79, 15, 15, 'move_in', '2026-06-22', '2026-06-22', '2026-06-22', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-14 08:15:00', '2026-09-29 15:45:53'),
(80, 15, 15, 'monthly', '2026-06-22', '2026-07-21', '2026-06-27', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-21 16:05:00', '2026-09-29 15:45:53'),
(81, 15, 15, 'monthly', '2026-07-22', '2026-08-21', '2026-07-27', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-21 16:05:00', '2026-09-29 15:45:53'),
(82, 15, 15, 'monthly', '2026-08-22', '2026-09-21', '2026-08-27', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-21 16:05:00', '2026-09-29 15:45:53'),
(83, 15, 15, 'monthly', '2026-09-22', '2026-10-21', '2026-09-27', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-09-21 16:05:00', '2026-09-29 15:45:53'),
(84, 16, 16, 'move_in', '2026-10-03', '2026-10-03', '2026-10-03', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'unpaid', '2026-09-24 01:15:00', '2026-09-29 15:45:53'),
(85, 17, 17, 'move_in', '2026-07-20', '2026-07-20', '2026-07-20', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2026-07-10 02:15:00', '2026-09-29 15:45:53'),
(86, 17, 17, 'monthly', '2026-07-20', '2026-08-19', '2026-07-25', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-07-19 16:05:00', '2026-09-29 15:45:53'),
(87, 17, 17, 'monthly', '2026-08-20', '2026-09-19', '2026-08-25', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-08-19 16:05:00', '2026-09-29 15:45:53'),
(88, 17, 17, 'monthly', '2026-09-20', '2026-10-19', '2026-09-25', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-09-19 16:05:00', '2026-09-29 15:45:53'),
(89, 18, 18, 'move_in', '2026-05-11', '2026-05-11', '2026-05-11', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-04-30 03:15:00', '2026-09-29 15:45:53'),
(90, 18, 18, 'monthly', '2026-05-11', '2026-06-10', '2026-05-16', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-10 16:05:00', '2026-09-29 15:45:53'),
(91, 18, 18, 'monthly', '2026-06-11', '2026-07-10', '2026-06-16', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-10 16:05:00', '2026-09-29 15:45:53'),
(92, 18, 18, 'monthly', '2026-07-11', '2026-08-10', '2026-07-16', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-10 16:05:00', '2026-09-29 15:45:53'),
(93, 18, 18, 'monthly', '2026-08-11', '2026-09-10', '2026-08-16', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-10 16:05:00', '2026-09-29 15:45:53'),
(94, 18, 18, 'monthly', '2026-09-11', '2026-10-10', '2026-09-16', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-10 16:05:00', '2026-09-29 15:45:53'),
(95, 19, 19, 'move_in', '2026-10-03', '2026-10-03', '2026-10-03', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'unpaid', '2026-09-21 04:15:00', '2026-09-29 15:45:53'),
(96, 20, 20, 'move_in', '2026-04-23', '2026-04-23', '2026-04-23', 16000.00, 0.00, 0.00, 0.00, 16000.00, 'paid', '2026-04-15 05:15:00', '2026-09-29 15:45:53'),
(97, 20, 20, 'monthly', '2026-04-23', '2026-05-22', '2026-04-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-04-22 16:05:00', '2026-09-29 15:45:53'),
(98, 20, 20, 'monthly', '2026-05-23', '2026-06-22', '2026-05-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-05-22 16:05:00', '2026-09-29 15:45:53'),
(99, 20, 20, 'monthly', '2026-06-23', '2026-07-22', '2026-06-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-06-22 16:05:00', '2026-09-29 15:45:53'),
(100, 20, 20, 'monthly', '2026-07-23', '2026-08-22', '2026-07-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-07-22 16:05:00', '2026-09-29 15:45:53'),
(101, 20, 20, 'monthly', '2026-08-23', '2026-09-22', '2026-08-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-08-22 16:05:00', '2026-09-29 15:45:53'),
(102, 20, 20, 'monthly', '2026-09-23', '2026-10-22', '2026-09-28', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-09-22 16:05:00', '2026-09-29 15:45:53'),
(103, 21, 21, 'move_in', '2026-06-16', '2026-06-16', '2026-06-16', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(104, 21, 21, 'monthly', '2026-06-16', '2026-07-15', '2026-06-21', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-06-15 16:05:00', '2026-09-29 15:45:53'),
(105, 21, 21, 'monthly', '2026-07-16', '2026-08-15', '2026-07-21', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-07-15 16:05:00', '2026-09-29 15:45:53'),
(106, 21, 21, 'monthly', '2026-08-16', '2026-09-15', '2026-08-21', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-15 16:05:00', '2026-09-29 15:45:53'),
(107, 21, 21, 'monthly', '2026-09-16', '2026-10-15', '2026-09-21', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-15 16:05:00', '2026-09-29 15:45:53'),
(108, 22, 22, 'move_in', '2026-08-25', '2026-08-25', '2026-08-25', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-08-15 07:15:00', '2026-09-29 15:45:53'),
(109, 22, 22, 'monthly', '2026-08-25', '2026-09-24', '2026-08-30', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-24 16:05:00', '2026-09-29 15:45:53'),
(110, 22, 22, 'monthly', '2026-09-25', '2026-10-24', '2026-09-30', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-24 16:05:00', '2026-09-29 15:45:53'),
(111, 23, 23, 'move_in', '2026-03-21', '2026-03-21', '2026-03-21', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-10 08:15:00', '2026-09-29 15:45:53'),
(112, 23, 23, 'monthly', '2026-03-21', '2026-04-20', '2026-03-26', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-20 16:05:00', '2026-09-29 15:45:53'),
(113, 23, 23, 'monthly', '2026-04-21', '2026-05-20', '2026-04-26', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-04-20 16:05:00', '2026-09-29 15:45:53'),
(114, 23, 23, 'monthly', '2026-05-21', '2026-06-20', '2026-05-26', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-05-20 16:05:00', '2026-09-29 15:45:53'),
(115, 23, 23, 'monthly', '2026-06-21', '2026-07-20', '2026-06-26', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-20 16:05:00', '2026-09-29 15:45:53'),
(116, 23, 23, 'monthly', '2026-07-21', '2026-08-20', '2026-07-26', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-20 16:05:00', '2026-09-29 15:45:53'),
(117, 24, 24, 'move_in', '2026-04-10', '2026-04-10', '2026-04-10', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-03-29 01:15:00', '2026-09-29 15:45:53'),
(118, 24, 24, 'monthly', '2026-04-10', '2026-05-09', '2026-04-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-09 16:05:00', '2026-09-29 15:45:53'),
(119, 24, 24, 'monthly', '2026-05-10', '2026-06-09', '2026-05-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-09 16:05:00', '2026-09-29 15:45:53'),
(120, 24, 24, 'monthly', '2026-06-10', '2026-07-09', '2026-06-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-09 16:05:00', '2026-09-29 15:45:53'),
(121, 24, 24, 'monthly', '2026-07-10', '2026-08-09', '2026-07-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-09 16:05:00', '2026-09-29 15:45:53'),
(122, 24, 24, 'monthly', '2026-08-10', '2026-09-09', '2026-08-15', 2600.00, 400.00, 200.00, 260.00, 3460.00, 'overdue', '2026-08-09 16:05:00', '2026-09-29 15:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `damages`
--

CREATE TABLE `damages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `date_incurred` date NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `damages`
--

INSERT INTO `damages` (`id`, `tenant_id`, `room_id`, `bed_id`, `description`, `cost`, `date_incurred`, `photo_path`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 14, 19, 15, 'Broken cabinet door hinge (bed-side locker)', 800.00, '2026-09-17', NULL, 126, '2026-09-16 16:00:00', '2026-09-16 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_amenities`
--

CREATE TABLE `dormitory_amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(40) NOT NULL,
  `label` varchar(60) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_amenities`
--

INSERT INTO `dormitory_amenities` (`id`, `key`, `label`, `is_enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'wifi', 'WiFi', 1, 1, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(2, 'kitchen', 'Kitchen', 0, 2, '2026-09-07 06:08:17', '2026-09-07 07:03:57'),
(3, 'study_area', 'Study Area', 1, 3, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(4, 'parking_area', 'Parking Area', 1, 4, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(5, 'cctv', 'CCTV', 1, 5, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(6, 'hot_cold_shower', 'Hot & Cold Shower', 1, 6, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(7, 'laundry_area', 'Laundry Area', 1, 7, '2026-09-07 06:08:17', '2026-09-07 06:08:17'),
(8, '24_7_security', '24/7 Security', 1, 8, '2026-09-07 06:08:17', '2026-09-07 06:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_house_rules`
--

CREATE TABLE `dormitory_house_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule_text` varchar(500) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_house_rules`
--

INSERT INTO `dormitory_house_rules` (`id`, `rule_text`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Curfew at 11 PM', 1, '2026-09-07 06:59:14', '2026-09-07 06:59:14'),
(2, 'House Rule 2', 2, '2026-09-07 07:10:57', '2026-09-07 07:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_profile`
--

CREATE TABLE `dormitory_profile` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dorm_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `brand_logo_path` varchar(255) DEFAULT NULL,
  `policies_file_path` varchar(255) DEFAULT NULL,
  `contract_template_path` varchar(255) DEFAULT NULL,
  `business_permit_path` varchar(255) DEFAULT NULL,
  `bir_registration_path` varchar(255) DEFAULT NULL,
  `gcash_number` varchar(30) DEFAULT NULL,
  `bdo_account_number` varchar(30) DEFAULT NULL,
  `payments_and_fees` longtext DEFAULT NULL,
  `house_rules` longtext DEFAULT NULL,
  `checkout_procedures` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_profile`
--

INSERT INTO `dormitory_profile` (`id`, `dorm_name`, `description`, `address`, `contact_number`, `contact_email`, `logo_path`, `brand_logo_path`, `policies_file_path`, `contract_template_path`, `business_permit_path`, `bir_registration_path`, `gcash_number`, `bdo_account_number`, `payments_and_fees`, `house_rules`, `checkout_procedures`, `created_at`, `updated_at`) VALUES
(1, 'Pureza Station Dormitory', 'Pureza Station Dormitory has been a trusted home for students and young professionals since 1996. Just a 5-minute walk from PUP and the Pureza LRT Station, we offer clean, secure, and affordable rooms designed for easy, comfortable living close to school and work.', 'Pureza Station, Manila', '0917-893-2970', 'dormitorypurezastation@gmail.com', 'dormitory-profile/yYzcKk7O16Id6prPIyO47BJJlqGqSHs3EY1fNhes.jpg', NULL, 'dormitory-profile/JU08CTcqjUS5XJqlZKb7DemhpAryCiQObz8Xy0vp.pdf', 'contracts/dormitory-contract.pdf', 'dormitory-profile/legitimacy/SLgvrZYlX5qCvu1idRlgwXiWcWFJuSqy2Ecb2GgP.pdf', 'dormitory-profile/legitimacy/5sPbbKzi1OrwF8CRR2KFtur4qQtUgSCjbdJuJ6sr.png', NULL, NULL, 'Rent may be paid in cash, GCash, or bank deposit (BDO).\n\nTenancy is subject to a three-month minimum. Tenants must provide the start\nand end date of their stay upon registration.\n\nUpon registration, new tenants pay a reservation fee composed of a security\ndeposit (one month, refundable for a 3-month contract) and one month advance\nrent. The reservation fee is non-refundable if the tenant cancels or checks\nout earlier than the three-month minimum. The deposit is returned within\n2–3 weeks after the check-out date.\n\nRent is due every 1st day of the month. For GCash or bank deposit, payment\nconfirmation must be sent to the dormitory\'s official contact channels.\n\nTenants are granted a 3-day grace period for late rent payments. Beyond the\ngrace period, a 10% penalty fee applies. Failure to pay within one month\nresults in a notice of eviction for non-payment.\n\nTenants wishing to extend their stay must give at least 1 month notice.\nMove-out requires at least 2 weeks notice; move-out schedule is end of month.', 'Alcoholic beverages, smoking, and vaping are not allowed on dormitory premises.\n\nWashing of clothes is not allowed; a laundry service is available outside.\n\nTenants are responsible for keeping common areas clean after use, and must\npromptly report any damages or issues to maintenance staff.\n\nTenants must pay for any loss or damage to dormitory property caused by\nthemselves or their guests, at the cost of the damage (minimum ₱500).\n\nOnly registered tenants may enter the rooms. Visitors may be entertained at\nthe receiving area.\n\nHazardous goods (gas, cooking stoves, flammable fuels, firearms) are strictly\nprohibited; violation carries a ₱500 fine and may be reported to authorities.\nDrugs and illegal substances are strictly prohibited and will be reported.\n\nSilence should be observed at all times out of consideration for other tenants.\nTreat fellow tenants and staff with respect — harassment, discrimination, or\nbullying will not be tolerated.\n\nManagement is not responsible for losses or injuries occurring on the premises.\nTenants should exercise care and diligence at all times.\n\nDoors and windows must be closed when using the air-conditioner. When leaving,\nturn off all faucets, showers, lights, air conditioners, and appliances, and\nlock the door. Lost or damaged keys cost ₱50 to replace.\n\nA strict NO PETS policy is enforced.\n\nCurfew hours: 11PM – 4AM. Aircon schedule: 10PM – 5AM.', 'Advanced notice: Residents planning to check out must give written notice at\nleast two weeks before their intended departure date.\n\nRoom inspection: A staff member will inspect the room/bed before check-out to\nassess damages or cleanliness issues. Rooms should be clean before inspection.\n\nDamages and repairs: Residents are responsible for damage beyond normal wear\nand tear, and will be charged for repairs or replacements.\n\nFurniture and equipment: All dormitory-provided furniture and equipment must\nbe present and in good condition. Missing or damaged items incur charges.\n\nCleanliness: Rooms must be left in move-in condition, with all personal\nbelongings removed and shared areas cleaned.\n\nTrash disposal: Dispose of all trash and recyclables in designated bins.\n\nKey return: Room keys must be returned upon check-out. Failure to return keys\nmay result in a fine.\n\nCheck-out time: Residents must vacate by 2:00 PM on the check-out date.\n\nFinal settlement: After inspection, the security deposit is returned minus any\ndeductions for damages or outstanding charges, within two weeks of check-out.', '2026-08-27 12:47:03', '2026-09-25 12:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `escalation_logs`
--

CREATE TABLE `escalation_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `billing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `action_type` varchar(50) DEFAULT NULL,
  `message_content` text DEFAULT NULL,
  `status` enum('pending','sent','resolved') NOT NULL DEFAULT 'pending',
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `escalation_logs`
--

INSERT INTO `escalation_logs` (`id`, `tenant_id`, `billing_id`, `stage`, `action_type`, `message_content`, `status`, `performed_by`, `created_at`, `updated_at`) VALUES
(1, 6, 33, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-19 22:00:00', '2026-09-19 22:00:00'),
(2, 6, 33, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-21 22:00:00', '2026-09-21 22:00:00'),
(3, 6, 33, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-23 22:00:00', '2026-09-23 22:00:00'),
(4, 6, 33, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST.PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-26 22:00:00', '2026-09-26 22:00:00'),
(5, 6, 33, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST.PH', 'sent', NULL, '2026-09-27 22:00:00', '2026-09-27 22:00:00'),
(6, 6, 33, 4, 'emergency_contact_notified', 'This is to inform you that John Paul Mendoza\'s account at NEST.PH is 9 days overdue, balance PHP 3,460.00. Please encourage them to settle it as soon as possible.', 'sent', NULL, '2026-09-28 22:00:00', '2026-09-28 22:00:00'),
(7, 9, 47, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-25 22:00:00', '2026-09-25 22:00:00'),
(8, 9, 47, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,000.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-27 22:00:00', '2026-09-27 22:00:00'),
(9, 13, 73, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-22 22:00:00', '2026-09-22 22:00:00'),
(10, 13, 73, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,350.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-24 22:00:00', '2026-09-24 22:00:00'),
(11, 13, 73, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,350.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-26 22:00:00', '2026-09-26 22:00:00'),
(12, 13, NULL, 2, 'admin_override_pause', 'Paused by admin: tenant agreed to a payment plan (half on the 15th, half on the 30th).', 'resolved', 124, '2026-09-28 06:20:00', '2026-09-28 06:20:00'),
(13, 24, 122, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-08-14 22:00:00', '2026-08-14 22:00:00'),
(14, 24, 122, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-16 22:00:00', '2026-08-16 22:00:00'),
(15, 24, 122, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-18 22:00:00', '2026-08-18 22:00:00'),
(16, 24, 122, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST.PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-21 22:00:00', '2026-08-21 22:00:00'),
(17, 24, 122, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST.PH', 'sent', NULL, '2026-08-22 22:00:00', '2026-08-22 22:00:00'),
(18, 24, 122, 4, 'emergency_contact_notified', 'This is to inform you that Joseph Allan Cruz\'s account at NEST.PH is 9 days overdue, balance PHP 3,460.00. Please encourage them to settle it as soon as possible.', 'sent', NULL, '2026-08-23 22:00:00', '2026-08-23 22:00:00'),
(19, 24, 122, 5, 'demand_letter_generated', 'demand-letters/24_122.pdf', 'sent', NULL, '2026-08-24 22:00:00', '2026-08-24 22:00:00'),
(20, 24, 122, 6, 'delinquent_blacklisted', NULL, 'resolved', NULL, '2026-08-25 22:00:00', '2026-08-25 22:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `floor_name` varchar(50) NOT NULL,
  `monthly_utility_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_wifi_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `floor_number` tinyint(3) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `floor_name`, `monthly_utility_cost`, `monthly_wifi_cost`, `floor_number`, `description`, `created_at`, `updated_at`) VALUES
(3, 'Ground Floor', 0.00, 0.00, 1, 'Lobby, receiving area and study hall. Mixed rooms near the entrance.', '2026-07-29 22:22:25', '2026-09-29 15:45:52'),
(11, 'Second Floor', 0.00, 0.00, 2, 'Shared rooms and quiet solo units for working tenants.', '2026-09-04 13:04:53', '2026-09-29 15:45:52'),
(12, 'Third Floor', 0.00, 0.00, 3, 'Newly renovated rooms with balcony access.', '2026-09-29 13:00:54', '2026-09-29 15:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text DEFAULT NULL,
  `reply_message` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `replied_by` bigint(20) UNSIGNED DEFAULT NULL,
  `preferred_room_type` varchar(50) DEFAULT NULL,
  `dpa_consent` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('new','contacted','converted','closed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `full_name`, `contact_number`, `email`, `room_id`, `message`, `reply_message`, `replied_at`, `replied_by`, `preferred_room_type`, `dpa_consent`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Aira Nicole Dimaculangan', '09174402167', 'aira.dimaculangan@gmail.com', 48, 'Hello po! May available pa po bang bedspace for female this November? Magkano po ang quad sharing?', NULL, NULL, NULL, 'Quad Sharing', 1, 'new', '2026-09-29 12:45:54', '2026-09-29 12:45:54'),
(2, 'Rhenz Adrian Pacheco', '09184410086', 'rhenz.pacheco@gmail.com', 51, 'Good day! Pwede po ba mag-ocular visit this Saturday? Working po ako sa Makati, looking for a solo room.', NULL, NULL, NULL, 'Solo Room', 1, 'new', '2026-09-28 03:12:00', '2026-09-28 03:12:00'),
(3, 'Janella Marie Quiambao', '09274418005', 'janella.quiambao@gmail.com', NULL, 'Kasama na po ba ang WiFi at kuryente sa monthly rate?', 'Hi Janella! Hiwalay po ang utilities at WiFi pero hati-hati ang room mates, around ₱900/month per bed para sa quad. Welcome po kayong mag-visit!', '2026-09-26 10:12:00', 124, 'Double Sharing', 1, 'contacted', '2026-09-26 05:12:00', '2026-09-26 10:12:00'),
(4, 'Luis Gabriel Soriano', '09394425924', 'luis.soriano@gmail.com', 50, 'Is there a curfew? I have night classes until 9 PM.', 'Hello Luis! Curfew is 11 PM to 4 AM, so 9 PM classes are no problem. Feel free to apply online through our website.', '2026-09-24 12:12:00', 124, '6-Bed Dormitory', 1, 'contacted', '2026-09-24 07:12:00', '2026-09-24 12:12:00'),
(5, 'Ryan Christopher Santiago', '09454433843', 'ryan.santiago@gmail.com', 46, 'Interested po ako sa Room 203, pwede po ba mag-apply online?', 'Yes po! Na-send na namin ang link. Paki-fill up lang po ang application form.', '2026-09-22 14:12:00', 124, 'Quad Sharing', 1, 'converted', '2026-09-22 09:12:00', '2026-09-22 14:12:00'),
(6, 'Mylene Castillo', '09564441762', 'mylene.castillo@gmail.com', NULL, 'Pwede po ba ang pets? May maliit po akong pusa.', 'Sorry po, strict NO PETS policy po kami. Salamat sa interest!', '2026-09-17 16:12:00', 124, NULL, 1, 'closed', '2026-09-17 11:12:00', '2026-09-17 16:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lease_contracts`
--

CREATE TABLE `lease_contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `bed_id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `monthly_rate` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `esign_status` enum('pending','signed','not_applicable') NOT NULL DEFAULT 'pending',
  `signed_document_url` varchar(255) DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','active','expiring_soon','expired','terminated') NOT NULL DEFAULT 'pending',
  `termination_reason` text DEFAULT NULL,
  `terminated_at` timestamp NULL DEFAULT NULL,
  `last_renewed_at` timestamp NULL DEFAULT NULL,
  `last_renewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lease_contracts`
--

INSERT INTO `lease_contracts` (`id`, `application_id`, `tenant_id`, `bed_id`, `inquiry_id`, `start_date`, `end_date`, `monthly_rate`, `discount_amount`, `esign_status`, `signed_document_url`, `signed_at`, `status`, `termination_reason`, `terminated_at`, `last_renewed_at`, `last_renewed_by`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, '2026-04-19', '2027-03-18', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-09 02:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-04-10 02:15:00', '2026-09-29 15:45:53'),
(2, 2, 2, 2, NULL, '2026-06-27', '2027-03-26', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-16 03:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-17 03:15:00', '2026-09-29 15:45:53'),
(3, 3, 3, 3, NULL, '2026-07-25', '2027-03-24', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-13 04:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-07-14 04:15:00', '2026-09-29 15:45:53'),
(4, 4, 4, 28, NULL, '2026-07-17', '2026-10-19', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-04 05:15:00', 'expiring_soon', NULL, NULL, NULL, NULL, 124, 124, '2026-07-05 05:15:00', '2026-09-29 15:45:53'),
(5, 5, 5, 5, NULL, '2026-03-14', '2027-03-13', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-05 06:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-03-06 06:15:00', '2026-09-29 15:45:53'),
(6, 6, 6, 34, NULL, '2026-06-15', '2027-03-14', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-05 07:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-06 07:15:00', '2026-09-29 15:45:53'),
(7, 7, 7, 7, NULL, '2026-08-24', '2027-03-23', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-08-13 08:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-08-14 08:15:00', '2026-09-29 15:45:53'),
(8, 8, 8, 8, NULL, '2026-05-26', '2027-03-25', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-05-14 01:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-05-15 01:15:00', '2026-09-29 15:45:53'),
(9, 9, 9, 9, NULL, '2026-06-21', '2027-03-20', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-08 02:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-09 02:15:00', '2026-09-29 15:45:53'),
(10, 10, 10, 37, NULL, '2026-04-17', '2026-09-26', 7800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-08 03:15:00', 'expired', NULL, NULL, NULL, NULL, 124, 124, '2026-04-09 03:15:00', '2026-09-29 15:45:53'),
(11, 11, 11, 12, NULL, '2026-01-09', '2027-03-08', 2250.00, 250.00, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-30 04:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2025-12-31 04:15:00', '2026-09-29 15:45:53'),
(12, 12, 12, 13, NULL, '2026-07-28', '2027-03-27', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-17 05:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-07-18 05:15:00', '2026-09-29 15:45:53'),
(13, 13, 13, 14, NULL, '2026-06-18', '2027-03-17', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-06 06:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(14, 14, 14, 15, NULL, '2026-06-29', '2027-03-28', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-16 07:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-17 07:15:00', '2026-09-29 15:45:53'),
(15, 15, 15, 16, NULL, '2026-06-22', '2027-03-21', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-13 08:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-14 08:15:00', '2026-09-29 15:45:53'),
(16, 16, 16, 17, NULL, '2026-10-03', '2027-04-02', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-23 01:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-09-24 01:15:00', '2026-09-29 15:45:53'),
(17, 17, 17, 18, NULL, '2026-07-20', '2027-03-19', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-09 02:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-07-10 02:15:00', '2026-09-29 15:45:53'),
(18, 18, 18, 20, NULL, '2026-05-11', '2027-03-10', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-29 03:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-04-30 03:15:00', '2026-09-29 15:45:53'),
(19, 19, 19, 21, NULL, '2026-10-03', '2027-04-02', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-20 04:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-09-21 04:15:00', '2026-09-29 15:45:53'),
(20, 20, 20, 24, NULL, '2026-04-23', '2027-03-22', 8000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-14 05:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-04-15 05:15:00', '2026-09-29 15:45:53'),
(21, 21, 21, 25, NULL, '2026-06-16', '2027-03-15', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-06 06:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(22, 22, 22, 26, NULL, '2026-08-25', '2027-03-24', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-08-14 07:15:00', 'active', NULL, NULL, NULL, NULL, 124, 124, '2026-08-15 07:15:00', '2026-09-29 15:45:53'),
(23, 23, 23, 29, NULL, '2026-03-21', '2026-08-20', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-09 08:15:00', 'expired', NULL, NULL, NULL, NULL, 124, 124, '2026-03-10 08:15:00', '2026-09-29 15:45:53'),
(24, 24, 24, 31, NULL, '2026-04-10', '2027-02-09', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-28 01:15:00', 'terminated', 'Terminated for non-payment after full delinquency escalation (Stage 6).', '2026-08-29 16:00:00', NULL, NULL, 124, 124, '2026-03-29 01:15:00', '2026-09-29 15:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_tickets`
--

CREATE TABLE `maintenance_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `bed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `category` enum('billing_payment_concern','electrical_issue','plumbing_water_emergency','security_concern','structural_damage','safety_security','fire_safety_hazard','maintenance_repairs','facilities_amenities','administrative_leasing_concern','account_access_issue','noise_roommate_concern','suggestion_feedback') NOT NULL,
  `description` text NOT NULL,
  `attachment_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachment_paths`)),
  `priority` enum('urgent','non_urgent') DEFAULT NULL,
  `status` enum('open','seen','in_progress','resolved','rejected') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_tickets`
--

INSERT INTO `maintenance_tickets` (`id`, `tenant_id`, `bed_id`, `title`, `category`, `description`, `attachment_paths`, `priority`, `status`, `assigned_to`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Aircon not cooling', 'maintenance_repairs', 'The aircon in Room 101 blows air but it is not cold anymore since last night.', NULL, 'non_urgent', 'resolved', 126, '2026-09-11 03:30:00', '2026-09-09 03:30:00', '2026-09-09 07:30:00'),
(2, 2, 2, 'Sparking outlet near Bed 2', 'electrical_issue', 'The outlet beside my bed sparked when I plugged in my charger. I stopped using it.', NULL, 'urgent', 'open', NULL, NULL, '2026-09-29 13:45:53', '2026-09-29 14:45:53'),
(3, 5, 5, 'Low water pressure in CR', 'plumbing_water_emergency', 'Mahina po ang tulo ng tubig sa shower tuwing 6-7 AM.', NULL, 'non_urgent', 'in_progress', 126, NULL, '2026-09-25 07:30:00', '2026-09-25 09:30:00'),
(4, 8, 8, 'Noisy neighbors after curfew', 'noise_roommate_concern', 'May maingay po sa kabilang room (104?) past 12 midnight, 3 nights na.', NULL, 'non_urgent', 'seen', NULL, NULL, '2026-09-27 02:30:00', '2026-09-27 03:30:00'),
(5, 11, 12, 'Request for a bigger study table in the lobby', 'suggestion_feedback', 'Suggestion lang po: sana may mas malaking study table sa lobby for group study.', NULL, 'non_urgent', 'rejected', 124, NULL, '2026-09-14 05:30:00', '2026-09-14 07:30:00'),
(6, 12, 13, 'Question about my rejected GCash payment', 'billing_payment_concern', 'Bakit po na-reject yung payment ko? Nagbayad naman po ako.', NULL, 'non_urgent', 'in_progress', 125, NULL, '2026-09-29 13:45:53', '2026-09-29 15:45:53'),
(7, 15, 16, 'Broken door lock', 'security_concern', 'Hindi po nagla-lock nang maayos ang pinto ng Room 201, kailangan pang itulak.', NULL, 'urgent', 'resolved', 126, '2026-09-22 09:30:00', '2026-09-20 09:30:00', '2026-09-20 11:30:00'),
(8, 18, 20, 'WiFi keeps disconnecting', 'facilities_amenities', 'The WiFi on the 2nd floor drops every 10-15 minutes, hard to attend online classes.', NULL, 'non_urgent', 'open', NULL, NULL, '2026-09-28 04:30:00', '2026-09-28 05:30:00'),
(9, 20, 24, 'Ceiling leak in solo room', 'structural_damage', 'May tumutulo po sa kisame tuwing malakas ang ulan, malapit sa bintana.', NULL, 'urgent', 'in_progress', 126, NULL, '2026-09-26 06:30:00', '2026-09-26 09:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_31_000001_create_roles_table', 1),
(5, '2024_02_01_000001_add_role_and_status_to_users_table', 1),
(6, '2024_02_01_000002_create_tenants_table', 1),
(7, '2024_02_01_000003_create_rooms_table', 1),
(8, '2024_02_01_000004_create_beds_table', 1),
(9, '2024_02_01_000005_create_inquiries_table', 1),
(10, '2024_02_01_000006_create_lease_contracts_table', 1),
(11, '2024_02_01_000007_create_billing_statements_table', 1),
(12, '2024_02_01_000008_create_payments_table', 1),
(13, '2024_02_03_000001_create_admin_privileges_table', 1),
(14, '2024_02_04_000001_create_maintenance_tickets_table', 1),
(15, '2024_02_05_000001_create_escalation_logs_table', 1),
(16, '2026_07_15_181628_create_personal_access_tokens_table', 1),
(17, '2026_07_30_055832_create_floors_table', 2),
(18, '2026_07_30_060030_add_floor_id_to_rooms_table', 2),
(19, '2026_07_31_054830_add_vr_asset_path_to_rooms_table', 3),
(20, '2026_08_01_053125_add_granted_by_to_admin_privileges_table', 4),
(21, '2026_08_01_053150_add_status_index_to_rooms_table', 4),
(22, '2026_08_25_000001_create_applications_table', 5),
(23, '2026_08_25_000002_add_application_and_esign_fields_to_lease_contracts_table', 5),
(24, '2026_08_25_000003_add_dpa_consent_and_room_to_inquiries_table', 6),
(25, '2026_08_25_000010_create_damages_table', 7),
(26, '2026_08_25_000011_create_penalties_table', 7),
(27, '2026_08_25_000012_create_penalty_audit_logs_table', 7),
(28, '2026_08_25_000020_add_proof_review_fields_to_payments_table', 8),
(29, '2026_08_26_000001_add_utility_costs_to_floors_table', 9),
(30, '2026_08_26_000002_add_utility_fields_and_payment_notes', 9),
(31, '2026_08_26_000003_add_vr_caption_and_visibility_to_rooms_table', 10),
(32, '2026_08_26_000004_create_dormitory_profile_table', 11),
(33, '2026_08_28_000001_create_password_reset_codes_table', 12),
(34, '2026_08_29_000001_add_full_fields_to_applications_table', 13),
(35, '2026_08_29_000002_add_policies_file_path_to_dormitory_profile_table', 14),
(36, '2026_08_29_000003_add_amenities_and_room_photos', 15),
(37, '2026_08_29_000004_create_vr_scenes_and_hotspots', 16),
(38, '2026_08_29_000005_add_fov_to_vr_scenes', 17),
(39, '2026_08_29_000006_add_reply_fields_to_inquiries_table', 18),
(40, '2026_08_30_000001_add_reserved_status_and_application_workflow_fields', 19),
(41, '2026_08_30_000002_add_lease_lifecycle_fields', 20),
(42, '2026_08_30_000003_add_contract_template_to_dormitory_profile', 21),
(43, '2026_08_30_000004_add_status_to_tenants_table', 22),
(44, '2026_08_30_000005_add_type_to_billing_statements', 23),
(45, '2026_08_30_000006_add_payment_numbers_to_dormitory_profile', 24),
(46, '2026_09_01_000001_add_date_incurred_to_penalties_table', 25),
(47, '2026_09_02_000001_add_tenant_id_status_index_to_billing_statements_table', 26),
(48, '2026_09_07_000001_add_updated_at_to_escalation_logs_table', 27),
(49, '2026_09_08_000001_add_portal_restricted_to_tenants_table', 28),
(50, '2026_09_08_000002_add_billing_id_action_type_index_to_escalation_logs_table', 29),
(51, '2026_09_08_000003_add_status_due_date_index_to_billing_statements_table', 29),
(52, '2026_09_08_000004_add_escalation_paused_to_tenants_table', 30),
(53, '2026_09_04_134402_add_profile_fields_to_tenants_table', 31),
(54, '2026_09_04_140701_change_tenant_type_to_string_on_tenants_table', 32),
(55, '2026_09_04_153627_add_deactivated_by_to_tenants_table', 33),
(56, '2026_09_04_185202_rename_archived_to_inactive_on_tenants_table', 34),
(57, '2026_09_09_000001_create_dormitory_amenities_table', 35),
(58, '2026_09_09_000002_create_dormitory_house_rules_table', 35),
(59, '2026_09_09_000003_add_business_documents_to_dormitory_profile_table', 35),
(60, '2026_09_07_050000_create_admin_access_logs_table', 36),
(61, '2026_09_07_000003_rebuild_maintenance_tickets_table', 37),
(62, '2026_09_07_000004_create_ticket_replies_table', 37),
(63, '2026_09_08_000005_add_tenant_id_to_ticket_replies_table', 38),
(64, '2026_09_08_000006_convert_ticket_attachment_to_multiple', 39),
(65, '2026_09_10_135502_split_full_name_on_applications_table', 40),
(66, '2026_09_10_135507_split_full_name_on_tenants_table', 40),
(67, '2026_09_10_135740_drop_full_name_columns', 41),
(68, '2026_09_10_150536_create_reviews_table', 42),
(69, '2026_09_11_211439_remove_parent_fields_add_emergency_relation_to_applications', 43),
(71, '2026_09_16_210055_create_announcements_tables', 44),
(72, '2026_09_24_130732_add_moderation_columns_to_reviews_table', 45),
(73, '2026_09_25_000001_add_utility_costs_to_rooms_table', 46),
(74, '2026_09_25_000002_create_payment_methods_table', 47),
(75, '2026_09_25_000003_add_default_cash_payment_method', 48),
(77, '2026_09_28_000001_create_admin_login_sessions_table', 49),
(78, '2026_09_29_000001_add_brand_logo_path_to_dormitory_profile_table', 50),
(79, '2026_09_29_000001_add_filled_path_to_vr_scenes', 51),
(80, '2026_09_29_000002_add_photo_updated_at_to_vr_scenes', 52);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_codes`
--

CREATE TABLE `password_reset_codes` (
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billing_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','gcash','bank_transfer','other') NOT NULL DEFAULT 'cash',
  `payment_method_label` varchar(60) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `proof_path` varchar(255) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `review_notes` varchar(500) DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `payment_method_label`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(1, 1, 1, 7000.00, 'cash', 'Cash Payment', NULL, '2026-04-18', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-04-18 02:15:00'),
(2, 2, 1, 4250.00, 'gcash', 'GCash', '1000048319271', '2026-04-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-04-24 02:00:00', NULL, '2026-04-23 03:15:00'),
(3, 3, 1, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1002', '2026-05-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-05-23 02:00:00', NULL, '2026-05-22 04:15:00'),
(4, 4, 1, 4250.00, 'cash', 'Cash Payment', NULL, '2026-06-21', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-21 05:15:00'),
(5, 5, 1, 4250.00, 'gcash', 'GCash', '1000048415813', '2026-07-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-21 02:00:00', NULL, '2026-07-20 06:15:00'),
(6, 6, 1, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1004', '2026-08-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-25 02:00:00', NULL, '2026-08-24 07:15:00'),
(7, 7, 1, 4250.00, 'cash', 'Cash Payment', NULL, '2026-09-23', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-09-23 08:15:00'),
(8, 8, 2, 7000.00, 'cash', 'Cash Payment', NULL, '2026-06-26', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-26 09:15:00'),
(9, 9, 2, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1005', '2026-06-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-01 02:00:00', NULL, '2026-06-30 01:15:00'),
(10, 10, 2, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-29', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-29 02:15:00'),
(11, 11, 2, 4250.00, 'gcash', 'GCash', '1000048560626', '2026-08-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-29 02:00:00', NULL, '2026-08-28 03:15:00'),
(12, 13, 3, 7000.00, 'cash', 'Cash Payment', NULL, '2026-07-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-07-24 05:15:00'),
(13, 14, 3, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-27', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-27 06:15:00'),
(14, 15, 3, 4250.00, 'gcash', 'GCash', '1000048608897', '2026-08-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-27 02:00:00', NULL, '2026-08-26 07:15:00'),
(15, 16, 3, 4250.00, 'gcash', 'GCash', '1000048657168', '2026-09-29', 'pending', 'demo/sample-payment-proof.png', 'Time of payment: 08:42. Full payment for this month po.', NULL, NULL, NULL, NULL, '2026-09-29 08:15:00'),
(16, 17, 4, 6800.00, 'cash', 'Cash Payment', NULL, '2026-07-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-07-16 09:15:00'),
(17, 18, 4, 4150.00, 'gcash', 'GCash', '1000048705439', '2026-07-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-19 02:00:00', NULL, '2026-07-18 01:15:00'),
(18, 19, 4, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1010', '2026-08-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-23 02:00:00', NULL, '2026-08-22 02:15:00'),
(19, 20, 4, 4150.00, 'cash', 'Cash Payment', NULL, '2026-09-21', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-09-21 03:15:00'),
(20, 21, 5, 9000.00, 'cash', 'Cash Payment', NULL, '2026-03-13', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-03-13 04:15:00'),
(21, 22, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1011', '2026-03-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-03-20 02:00:00', NULL, '2026-03-19 05:15:00'),
(22, 23, 5, 5400.00, 'cash', 'Cash Payment', NULL, '2026-04-18', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-04-18 06:15:00'),
(23, 24, 5, 5850.00, 'gcash', 'GCash', '1000048850252', '2026-05-25', 'approved', 'demo/sample-payment-proof.png', 'Sorry po late, na-delay sweldo.', NULL, 125, '2026-05-26 02:00:00', NULL, '2026-05-25 07:15:00'),
(24, 25, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1013', '2026-06-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-17 02:00:00', NULL, '2026-06-16 08:15:00'),
(25, 26, 5, 5400.00, 'cash', 'Cash Payment', NULL, '2026-07-15', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-15 09:15:00'),
(26, 27, 5, 5400.00, 'gcash', 'GCash', '1000048946794', '2026-08-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-20 02:00:00', NULL, '2026-08-19 01:15:00'),
(27, 28, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1015', '2026-09-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-19 02:00:00', NULL, '2026-09-18 02:15:00'),
(28, 29, 6, 5200.00, 'cash', 'Cash Payment', NULL, '2026-06-14', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-14 03:15:00'),
(29, 30, 6, 3200.00, 'cash', 'Cash Payment', NULL, '2026-06-19', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-19 04:15:00'),
(30, 31, 6, 3200.00, 'gcash', 'GCash', '1000049043336', '2026-07-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-19 02:00:00', NULL, '2026-07-18 05:15:00'),
(31, 32, 6, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1017', '2026-08-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-18 02:00:00', NULL, '2026-08-17 06:15:00'),
(32, 34, 7, 6500.00, 'cash', 'Cash Payment', NULL, '2026-08-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-08-23 08:15:00'),
(33, 35, 7, 4000.00, 'gcash', 'GCash', '1000049139878', '2026-08-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-28 02:00:00', NULL, '2026-08-27 09:15:00'),
(34, 36, 7, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1019', '2026-09-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-27 02:00:00', NULL, '2026-09-26 01:15:00'),
(35, 37, 8, 6500.00, 'cash', 'Cash Payment', NULL, '2026-05-25', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-05-25 02:15:00'),
(36, 38, 8, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1020', '2026-05-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-05-29 02:00:00', NULL, '2026-05-28 03:15:00'),
(37, 39, 8, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-27', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-27 04:15:00'),
(38, 40, 8, 4000.00, 'gcash', 'GCash', '1000049284691', '2026-07-31', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-01 02:00:00', NULL, '2026-07-31 05:15:00'),
(39, 41, 8, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1022', '2026-08-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-31 02:00:00', NULL, '2026-08-30 06:15:00'),
(40, 42, 8, 2000.00, 'cash', 'Cash Payment', NULL, '2026-09-28', 'approved', NULL, 'Partial muna po, babayaran ko yung natitira sa sweldo.', NULL, NULL, NULL, 125, '2026-09-28 07:15:00'),
(41, 43, 9, 6500.00, 'cash', 'Cash Payment', NULL, '2026-06-20', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-20 08:15:00'),
(42, 44, 9, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-22', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-22 09:15:00'),
(43, 45, 9, 4000.00, 'gcash', 'GCash', '1000049381233', '2026-07-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-27 02:00:00', NULL, '2026-07-26 01:15:00'),
(44, 46, 9, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1024', '2026-08-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-26 02:00:00', NULL, '2026-08-25 02:15:00'),
(45, 48, 10, 15600.00, 'cash', 'Cash Payment', NULL, '2026-04-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-04-16 04:15:00'),
(46, 49, 10, 9100.00, 'gcash', 'GCash', '1000049477775', '2026-04-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-04-23 02:00:00', NULL, '2026-04-22 05:15:00'),
(47, 50, 10, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1026', '2026-05-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-05-22 02:00:00', NULL, '2026-05-21 06:15:00'),
(48, 51, 10, 9100.00, 'cash', 'Cash Payment', NULL, '2026-06-20', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-20 07:15:00'),
(49, 52, 10, 9100.00, 'gcash', 'GCash', '1000049574317', '2026-07-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-20 02:00:00', NULL, '2026-07-19 08:15:00'),
(50, 53, 10, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1028', '2026-08-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-19 02:00:00', NULL, '2026-08-18 09:15:00'),
(51, 54, 10, 9100.00, 'cash', 'Cash Payment', NULL, '2026-09-22', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-09-22 01:15:00'),
(52, 55, 11, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-08', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-01-08 02:15:00'),
(53, 56, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1029', '2026-01-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-01-14 02:00:00', NULL, '2026-01-13 03:15:00'),
(54, 57, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-02-12', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-02-12 04:15:00'),
(55, 58, 11, 2850.00, 'gcash', 'GCash', '1000049719130', '2026-03-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-03-12 02:00:00', NULL, '2026-03-11 05:15:00'),
(56, 59, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1031', '2026-04-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-04-11 02:00:00', NULL, '2026-04-10 06:15:00'),
(57, 60, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-05-14', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-05-14 07:15:00'),
(58, 61, 11, 2850.00, 'gcash', 'GCash', '1000049815672', '2026-06-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-14 02:00:00', NULL, '2026-06-13 08:15:00'),
(59, 62, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1033', '2026-07-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-13 02:00:00', NULL, '2026-07-12 09:15:00'),
(60, 63, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-08-11', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-11 01:15:00'),
(61, 64, 11, 2850.00, 'gcash', 'GCash', '1000049912214', '2026-09-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-11 02:00:00', NULL, '2026-09-10 02:15:00'),
(62, 65, 12, 5000.00, 'cash', 'Cash Payment', NULL, '2026-07-27', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-07-27 03:15:00'),
(63, 66, 12, 3100.00, 'cash', 'Cash Payment', NULL, '2026-07-31', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-31 04:15:00'),
(64, 67, 12, 3100.00, 'gcash', 'GCash', '1000049960485', '2026-08-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-31 02:00:00', NULL, '2026-08-30 05:15:00'),
(65, 68, 12, 3100.00, 'gcash', 'GCash', '1000050008756', '2026-09-28', 'rejected', 'demo/sample-payment-proof.png', 'Bayad ko po for this month.', 'Screenshot is cropped -- the reference number and amount are not visible. Please upload the full receipt.', 125, '2026-09-29 02:00:00', NULL, '2026-09-28 06:15:00'),
(66, 69, 13, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-17', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-17 07:15:00'),
(67, 70, 13, 3100.00, 'gcash', 'GCash', '1000050057027', '2026-06-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-21 02:00:00', NULL, '2026-06-20 08:15:00'),
(68, 71, 13, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1038', '2026-07-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-20 02:00:00', NULL, '2026-07-19 09:15:00'),
(69, 72, 13, 3100.00, 'cash', 'Cash Payment', NULL, '2026-08-23', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-23 01:15:00'),
(70, 74, 14, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-28', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-28 03:15:00'),
(71, 75, 14, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1039', '2026-06-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-01 02:00:00', NULL, '2026-06-30 04:15:00'),
(72, 76, 14, 3100.00, 'cash', 'Cash Payment', NULL, '2026-08-03', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-03 05:15:00'),
(73, 77, 14, 3100.00, 'gcash', 'GCash', '1000050201840', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-03 02:00:00', NULL, '2026-09-02 06:15:00'),
(74, 79, 15, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-21', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-21 08:15:00'),
(75, 80, 15, 3100.00, 'cash', 'Cash Payment', NULL, '2026-06-27', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-27 09:15:00'),
(76, 81, 15, 3100.00, 'gcash', 'GCash', '1000050250111', '2026-07-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-27 02:00:00', NULL, '2026-07-26 01:15:00'),
(77, 82, 15, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1042', '2026-08-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-26 02:00:00', NULL, '2026-08-25 02:15:00'),
(78, 83, 15, 3100.00, 'cash', 'Cash Payment', NULL, '2026-09-24', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-09-24 03:15:00'),
(79, 85, 17, 9500.00, 'cash', 'Cash Payment', NULL, '2026-07-19', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-07-19 05:15:00'),
(80, 86, 17, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1043', '2026-07-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-24 02:00:00', NULL, '2026-07-23 06:15:00'),
(81, 87, 17, 5650.00, 'cash', 'Cash Payment', NULL, '2026-08-22', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-22 07:15:00'),
(82, 88, 17, 5650.00, 'gcash', 'GCash', '1000050394924', '2026-09-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-22 02:00:00', NULL, '2026-09-21 08:15:00'),
(83, 89, 18, 7000.00, 'cash', 'Cash Payment', NULL, '2026-05-10', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-05-10 09:15:00'),
(84, 90, 18, 4250.00, 'cash', 'Cash Payment', NULL, '2026-05-13', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-05-13 01:15:00'),
(85, 91, 18, 4250.00, 'gcash', 'GCash', '1000050443195', '2026-06-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-13 02:00:00', NULL, '2026-06-12 02:15:00'),
(86, 92, 18, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1046', '2026-07-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-17 02:00:00', NULL, '2026-07-16 03:15:00'),
(87, 93, 18, 4250.00, 'cash', 'Cash Payment', NULL, '2026-08-15', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-15 04:15:00'),
(88, 94, 18, 4250.00, 'gcash', 'GCash', '1000050539737', '2026-09-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-15 02:00:00', NULL, '2026-09-14 05:15:00'),
(89, 95, 19, 7000.00, 'gcash', 'GCash', '1000050588008', '2026-09-28', 'pending', 'demo/sample-payment-proof.png', 'Move-in fee (deposit + advance). Sent via GCash po.', NULL, NULL, NULL, NULL, '2026-09-28 06:15:00'),
(90, 96, 20, 16000.00, 'cash', 'Cash Payment', NULL, '2026-04-22', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-04-22 07:15:00'),
(91, 97, 20, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1049', '2026-04-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-04-29 02:00:00', NULL, '2026-04-28 08:15:00'),
(92, 98, 20, 9300.00, 'cash', 'Cash Payment', NULL, '2026-05-27', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-05-27 09:15:00'),
(93, 99, 20, 9300.00, 'gcash', 'GCash', '1000050684550', '2026-06-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-27 02:00:00', NULL, '2026-06-26 01:15:00'),
(94, 100, 20, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1051', '2026-07-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-26 02:00:00', NULL, '2026-07-25 02:15:00'),
(95, 101, 20, 9300.00, 'cash', 'Cash Payment', NULL, '2026-08-24', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-08-24 03:15:00'),
(96, 102, 20, 9300.00, 'gcash', 'GCash', '1000050781092', '2026-09-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-29 02:00:00', NULL, '2026-09-28 04:15:00'),
(97, 103, 21, 6800.00, 'cash', 'Cash Payment', NULL, '2026-06-15', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-06-15 05:15:00'),
(98, 104, 21, 4150.00, 'cash', 'Cash Payment', NULL, '2026-06-20', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-06-20 06:15:00'),
(99, 105, 21, 4150.00, 'gcash', 'GCash', '1000050829363', '2026-07-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-07-20 02:00:00', NULL, '2026-07-19 07:15:00'),
(100, 106, 21, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1054', '2026-08-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-19 02:00:00', NULL, '2026-08-18 08:15:00'),
(101, 107, 21, 4150.00, 'cash', 'Cash Payment', NULL, '2026-09-17', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-09-17 09:15:00'),
(102, 108, 22, 6800.00, 'cash', 'Cash Payment', NULL, '2026-08-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-08-24 01:15:00'),
(103, 109, 22, 4150.00, 'gcash', 'GCash', '1000050925905', '2026-08-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-08-29 02:00:00', NULL, '2026-08-28 02:15:00'),
(104, 110, 22, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1056', '2026-09-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-09-28 02:00:00', NULL, '2026-09-27 03:15:00'),
(105, 111, 23, 9000.00, 'cash', 'Cash Payment', NULL, '2026-03-20', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-03-20 04:15:00'),
(106, 112, 23, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1057', '2026-03-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-03-24 02:00:00', NULL, '2026-03-23 05:15:00'),
(107, 113, 23, 5400.00, 'cash', 'Cash Payment', NULL, '2026-04-22', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-04-22 06:15:00'),
(108, 114, 23, 5400.00, 'gcash', 'GCash', '1000051070718', '2026-05-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-05-27 02:00:00', NULL, '2026-05-26 07:15:00'),
(109, 115, 23, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1059', '2026-06-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-26 02:00:00', NULL, '2026-06-25 08:15:00'),
(110, 116, 23, 5400.00, 'cash', 'Cash Payment', NULL, '2026-07-24', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-24 09:15:00'),
(111, 117, 24, 5200.00, 'cash', 'Cash Payment', NULL, '2026-04-09', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 125, '2026-04-09 01:15:00'),
(112, 118, 24, 3200.00, 'cash', 'Cash Payment', NULL, '2026-04-11', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-04-11 02:15:00'),
(113, 119, 24, 3200.00, 'gcash', 'GCash', '1000051167260', '2026-05-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-05-16 02:00:00', NULL, '2026-05-15 03:15:00'),
(114, 120, 24, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-260929-1061', '2026-06-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 125, '2026-06-15 02:00:00', NULL, '2026-06-14 04:15:00'),
(115, 121, 24, 3200.00, 'cash', 'Cash Payment', NULL, '2026-07-13', 'approved', NULL, NULL, NULL, NULL, NULL, 125, '2026-07-13 05:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(20) NOT NULL,
  `name` varchar(60) NOT NULL,
  `account_name` varchar(120) DEFAULT NULL,
  `account_number` varchar(60) DEFAULT NULL,
  `qr_path` varchar(255) DEFAULT NULL,
  `instructions` varchar(500) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `type`, `name`, `account_name`, `account_number`, `qr_path`, `instructions`, `sort_order`, `created_at`, `updated_at`) VALUES
(6, 'cash', 'Cash Payment', NULL, NULL, NULL, 'Pay in person at the lobby / admin office.', 0, '2026-09-25 06:52:59', '2026-09-25 06:52:59'),
(7, 'ewallet', 'GCash', 'NEST PH', '09289811405', 'payment-qr/zIm8bN3C4sd90ApR2B6uQ3mzP3TC1W1AuLXYa98H.jpg', NULL, 1, '2026-09-25 06:56:42', '2026-09-25 06:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `damage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `billing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('damage','manual','other') NOT NULL DEFAULT 'manual',
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date_incurred` date DEFAULT NULL,
  `status` enum('active','waived') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penalties`
--

INSERT INTO `penalties` (`id`, `tenant_id`, `damage_id`, `billing_id`, `type`, `description`, `amount`, `date_incurred`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, 24, 'manual', 'Late payment fee (10% of monthly rent)', 450.00, '2026-05-23', 'active', 124, '2026-05-22 16:00:00', '2026-05-22 16:00:00'),
(2, 6, NULL, 33, 'manual', 'Late payment fee (10% of monthly rent)', 260.00, '2026-09-24', 'active', 124, '2026-09-23 16:00:00', '2026-09-23 16:00:00'),
(3, 13, NULL, 73, 'manual', 'Late payment fee (10% of monthly rent)', 250.00, '2026-09-27', 'active', 124, '2026-09-26 16:00:00', '2026-09-26 16:00:00'),
(4, 14, 1, 78, 'damage', 'Damage: Broken cabinet door hinge (bed-side locker)', 800.00, '2026-09-17', 'active', 124, '2026-09-16 16:00:00', '2026-09-16 16:00:00'),
(5, 14, NULL, NULL, 'manual', 'Lost room key replacement', 50.00, '2026-08-20', 'waived', 124, '2026-08-19 16:00:00', '2026-08-19 16:00:00'),
(6, 15, NULL, NULL, 'manual', 'House rule violation: butane stove found in room (hazardous goods)', 500.00, '2026-09-27', 'active', 124, '2026-09-26 16:00:00', '2026-09-26 16:00:00'),
(7, 24, NULL, 122, 'manual', 'Late payment fee (10% of monthly rent)', 260.00, '2026-08-19', 'active', 124, '2026-08-18 16:00:00', '2026-08-18 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `penalty_audit_logs`
--

CREATE TABLE `penalty_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penalty_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('created','waived','reinstated') NOT NULL DEFAULT 'created',
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penalty_audit_logs`
--

INSERT INTO `penalty_audit_logs` (`id`, `penalty_id`, `action`, `performed_by`, `reason`, `created_at`) VALUES
(1, 1, 'created', 124, NULL, '2026-05-22 16:00:00'),
(2, 2, 'created', 124, NULL, '2026-09-23 16:00:00'),
(3, 3, 'created', 124, NULL, '2026-09-26 16:00:00'),
(4, 4, 'created', 124, NULL, '2026-09-16 16:00:00'),
(5, 5, 'created', 124, NULL, '2026-08-19 16:00:00'),
(6, 5, 'waived', 3, 'Key was found by the guard the next day. First offense -- waived.', '2026-08-20 16:00:00'),
(7, 6, 'created', 124, NULL, '2026-09-26 16:00:00'),
(8, 7, 'created', 124, NULL, '2026-08-18 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `flag_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`flag_reasons`)),
  `moderated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `tenant_id`, `rating`, `comment`, `is_approved`, `status`, `flag_reasons`, `moderated_by`, `moderated_at`, `moderation_note`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'Sobrang linis ng rooms and very accommodating ang staff. Malapit pa sa UST, walking distance lang. Highly recommended!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-22 16:00:00', '2026-09-22 16:00:00'),
(2, 5, 4, 'Maayos ang WiFi at tahimik sa gabi. Minsan lang medyo mahina ang tubig sa umaga pero agad naman inaayos.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-18 16:00:00', '2026-09-18 16:00:00'),
(3, 11, 5, 'Almost a year na ako dito. Safe, may curfew, at mabait si Ma\'am Tess. Perfect para sa mga nurse na shifting.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-12 16:00:00', '2026-09-12 16:00:00'),
(4, 15, 3, 'Okay naman overall. Sana lang may kitchen na pwedeng gamitin kasi bawal magluto sa room.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-08 16:00:00', '2026-09-08 16:00:00'),
(5, 18, 4, 'Good value for money. Malinis ang CR at laging may tubig. Medyo strict sa visitors pero understandable.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-05 16:00:00', '2026-09-05 16:00:00'),
(6, 23, 5, 'Nag-move out na ako kasi lumipat ang work ko, pero sobrang saya ng stay ko dito. Salamat NEST.PH!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-20 16:00:00', '2026-09-20 16:00:00'),
(7, 12, 1, 'Mas mura sa amin! Visit www.cheapdorms-manila.com for promo, message 0917-000-0000', 0, 'hidden', '[\"Contains a link\",\"Contains a phone number\"]', NULL, NULL, NULL, '2026-09-25 16:00:00', '2026-09-25 16:00:00'),
(8, 17, 2, 'Pangit ugali ng roommate ko, [name removed] sobrang ingay.', 0, 'removed', NULL, 124, '2026-09-22 16:00:00', 'Removed: names another tenant. Concern was redirected to a support ticket instead.', '2026-09-21 16:00:00', '2026-09-22 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(2, 'admin'),
(1, 'tenant');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_no` varchar(20) NOT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `monthly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_utility_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_wifi_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('available','full','maintenance') NOT NULL DEFAULT 'available',
  `vr_asset_path` varchar(255) DEFAULT NULL,
  `vr_caption` varchar(255) DEFAULT NULL,
  `vr_visibility` enum('public','locked','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `floor_id`, `room_no`, `room_type`, `amenities`, `monthly_rate`, `monthly_utility_cost`, `monthly_wifi_cost`, `status`, `vr_asset_path`, `vr_caption`, `vr_visibility`, `created_at`, `updated_at`) VALUES
(16, 3, '101', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Cabinet per bed\",\"Shared CR\"]', 14000.00, 2000.00, 1000.00, 'available', NULL, 'Bright quad room beside the study hall', 'public', '2026-08-30 18:57:43', '2026-09-29 15:45:52'),
(17, 3, '102', 'Double Sharing', '[\"Air-conditioned\",\"Study table\",\"Private CR\"]', 9000.00, 1200.00, 600.00, 'available', NULL, 'Cozy double room with private CR', 'public', '2026-08-31 10:49:31', '2026-09-29 15:45:52'),
(18, 3, '103', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Shared CR\"]', 13000.00, 2000.00, 1000.00, 'available', NULL, 'Spacious quad room with computer corner', 'public', '2026-09-03 06:27:16', '2026-09-29 15:45:52'),
(19, 11, '201', '6-Bed Dormitory', '[\"Air-conditioned\",\"Double-deck beds\",\"Lockers\",\"Shared CR\"]', 15000.00, 2400.00, 1200.00, 'full', NULL, NULL, 'draft', '2026-09-04 13:04:53', '2026-09-29 15:45:52'),
(44, 3, '104', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Mini fridge\"]', 7500.00, 800.00, 500.00, 'maintenance', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(45, 11, '202', 'Double Sharing', '[\"Air-conditioned\",\"Study table\",\"Private CR\"]', 9500.00, 1200.00, 600.00, 'full', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(46, 11, '203', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Cabinet per bed\"]', 14000.00, 2000.00, 1000.00, 'full', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(47, 11, '204', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Balcony\"]', 8000.00, 800.00, 500.00, 'full', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(48, 12, '301', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Balcony access\"]', 13600.00, 2000.00, 1000.00, 'full', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(49, 12, '302', 'Double Sharing', '[\"Air-conditioned\",\"Private CR\"]', 9000.00, 1200.00, 600.00, 'available', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(50, 12, '303', '6-Bed Dormitory', '[\"Air-conditioned\",\"Double-deck beds\",\"Lockers\"]', 15600.00, 2400.00, 1200.00, 'available', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52'),
(51, 12, '304', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Work desk\"]', 7800.00, 800.00, 500.00, 'full', NULL, NULL, 'draft', '2026-01-28 16:00:00', '2026-09-29 15:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `room_photos`
--

CREATE TABLE `room_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `tenant_type` varchar(30) DEFAULT NULL,
  `id_document_path` varchar(255) DEFAULT NULL,
  `signed_contract_path` varchar(255) DEFAULT NULL,
  `status` enum('pending_move_in_payment','active','inactive') NOT NULL DEFAULT 'pending_move_in_payment',
  `deactivation_reason` varchar(500) DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `deactivated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `portal_restricted` tinyint(1) NOT NULL DEFAULT 0,
  `escalation_paused` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `user_id`, `first_name`, `last_name`, `contact_number`, `email`, `emergency_contact_name`, `emergency_contact_number`, `date_of_birth`, `home_address`, `tenant_type`, `id_document_path`, `signed_contract_path`, `status`, `deactivation_reason`, `deactivated_at`, `deactivated_by`, `is_blacklisted`, `portal_restricted`, `escalation_paused`, `created_at`, `updated_at`) VALUES
(1, 128, 'Maria Angelica', 'Santos', '09181242486', 'maria.santos@gmail.com', 'Rodelio Santos', '09182034386', '2004-03-14', 'Brgy. San Isidro, Angono, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-10 02:15:00', '2026-09-29 15:45:53'),
(2, 129, 'Kimberly Anne', 'Dela Cruz', '09271250405', 'kimberly.delacruz@gmail.com', 'Marites Dela Cruz', '09272042305', '2005-07-02', '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-17 03:15:00', '2026-09-29 15:45:53'),
(3, 130, 'Patricia Mae', 'Gonzales', '09391258324', 'patricia.gonzales@gmail.com', 'Lorna Gonzales', '09392050224', '2003-11-21', 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-14 04:15:00', '2026-09-29 15:45:53'),
(4, 131, 'Nicole Joy', 'Ramos', '09451266243', 'nicole.ramos@gmail.com', 'Ernesto Ramos', '09452058143', '2004-01-09', 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-05 05:15:00', '2026-09-29 15:45:53'),
(5, 132, 'Juan Miguel', 'Reyes', '09561274162', 'juan.reyes@gmail.com', 'Carmelita Reyes', '09562066062', '1999-05-30', '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-06 06:15:00', '2026-09-29 15:45:53'),
(6, 133, 'John Paul', 'Mendoza', '09212408565', 'john.mendoza@gmail.com', 'Rosalie Mendoza', '09212408565', '2004-08-17', 'Brgy. Bagong Silang, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 1, 0, '2026-06-06 07:15:00', '2026-09-29 15:45:53'),
(7, 134, 'Mark Joseph', 'Aquino', '09981290000', 'mark.aquino@gmail.com', 'Josefina Aquino', '09982081900', '2005-02-11', 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-14 08:15:00', '2026-09-29 15:45:53'),
(8, 135, 'Christian Dave', 'Torres', '09081297919', 'christian.torres@gmail.com', 'Dante Torres', '09082089819', '2002-12-03', '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-15 01:15:00', '2026-09-29 15:45:53'),
(9, 136, 'Benjamin', 'Robles', '09212408565', 'benjamin.robles@gmail.com', 'Evelyn Robles', '09212408565', '2004-06-25', 'Brgy. Santo Cristo, San Fernando, Pampanga', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-09 02:15:00', '2026-09-29 15:45:53'),
(10, 137, 'Rafael Luis', 'Navarro', '09171313757', 'rafael.navarro@gmail.com', 'Gloria Navarro', '09172105657', '2001-09-14', '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-09 03:15:00', '2026-09-29 15:45:53'),
(11, 138, 'Angela Marie', 'Villanueva', '09181321676', 'angela.villanueva@gmail.com', 'Arnel Villanueva', '09182113576', '1998-04-19', 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-31 04:15:00', '2026-09-29 15:45:53'),
(12, 139, 'Jasmine Rose', 'Garcia', '09271329595', 'jasmine.garcia@gmail.com', 'Ramil Garcia', '09272121495', '2005-10-05', 'Purok 2, Brgy. Talon, Las Piñas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-18 05:15:00', '2026-09-29 15:45:53'),
(13, 140, 'Camille Louise', 'Flores', '09212408565', 'camille.flores@gmail.com', 'Susan Flores', '09212408565', '2004-12-28', 'Brgy. Mabini, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 1, '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(14, 141, 'Bea Katrina', 'Pascual', '09451345433', 'bea.pascual@gmail.com', 'Gerardo Pascual', '09452137333', '2003-05-16', 'Brgy. Sta. Rita, Olongapo City, Zambales', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-17 07:15:00', '2026-09-29 15:45:53'),
(15, 142, 'Princess Joy', 'Manalo', '09561353352', 'princess.manalo@gmail.com', 'Cristina Manalo', '09562145252', '2002-08-08', 'Brgy. Parian, Calamba City, Laguna', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-14 08:15:00', '2026-09-29 15:45:53'),
(16, 143, 'Kathleen Mae', 'Salazar', '09661361271', 'kathleen.salazar@gmail.com', 'Rogelio Salazar', '09662153171', '2006-01-30', 'Brgy. Poblacion, Tagum City, Davao del Norte', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-24 01:15:00', '2026-09-29 15:45:53'),
(17, 144, 'Carlo Miguel', 'Bautista', '09981369190', 'carlo.bautista@gmail.com', 'Nenita Bautista', '09982161090', '2000-03-03', 'San Pablo City, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-10 02:15:00', '2026-09-29 15:45:53'),
(18, 145, 'Joshua Emmanuel', 'Lim', '09081377109', 'joshua.lim@gmail.com', 'Wilson Lim', '09082169009', '2004-11-11', 'Sta. Cruz, Laguna', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-30 03:15:00', '2026-09-29 15:45:53'),
(19, 146, 'Paolo Andres', 'Ocampo', '09951385028', 'paolo.ocampo@gmail.com', 'Amelia Ocampo', '09952176928', '2005-04-22', 'Brgy. Poblacion, Malolos City, Bulacan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-21 04:15:00', '2026-09-29 15:45:53'),
(20, 147, 'Andrea Nicole', 'Tan', '09171392947', 'andrea.tan@gmail.com', 'Rebecca Tan', '09172184847', '1997-09-09', 'Brgy. Kauswagan, Cagayan de Oro City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-15 05:15:00', '2026-09-29 15:45:53'),
(21, 148, 'Erika Jane', 'Morales', '09181400866', 'erika.morales@gmail.com', 'Ronaldo Morales', '09182192766', '2004-07-07', 'Brgy. Pantal, Dagupan City, Pangasinan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-07 06:15:00', '2026-09-29 15:45:53'),
(22, 149, 'Hannah Grace', 'Soriano', '09271408785', 'hannah.soriano@gmail.com', 'Marilou Soriano', '09272200685', '2006-02-14', 'Brgy. Dolores, Taytay, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-15 07:15:00', '2026-09-29 15:45:53'),
(23, 150, 'Gabriel Jose', 'Rivera', '09391416704', 'gabriel.rivera@gmail.com', 'Imelda Rivera', '09392208604', '2001-06-01', 'Brgy. San Antonio, Biñan, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract -- transferred to a job in Laguna.', '2026-08-20 16:00:00', 124, 0, 0, 0, '2026-03-10 08:15:00', '2026-09-29 15:45:53'),
(24, 151, 'Joseph Allan', 'Cruz', '09212408565', 'joseph.cruz@gmail.com', 'Nora Cruz', '09212408565', '2003-03-27', 'Brgy. Tabing Ilog, Marilao, Bulacan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 1, 1, 0, '2026-03-29 01:15:00', '2026-09-29 15:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_replies`
--

INSERT INTO `ticket_replies` (`id`, `ticket_id`, `user_id`, `tenant_id`, `message`, `created_at`) VALUES
(1, 1, 126, NULL, 'Noted po. Papupuntahin namin ang technician bukas ng umaga.', '2026-09-09 06:30:00'),
(2, 1, NULL, 1, 'Salamat po!', '2026-09-09 09:30:00'),
(3, 1, 126, NULL, 'Nalinis na po ang filter at na-recharge ang freon. Paki-check po kung okay na.', '2026-09-09 12:30:00'),
(4, 3, 126, NULL, 'Chine-check na po ng plumber ang main line. Update po kami mamaya.', '2026-09-25 10:30:00'),
(5, 5, 124, NULL, 'Thank you for the suggestion! Hindi po kasya sa space ng lobby sa ngayon, pero isasama namin sa renovation plan next year.', '2026-09-14 08:30:00'),
(6, 6, 125, NULL, 'Hi Jasmine, naka-crop po kasi yung screenshot kaya hindi makita ang reference number. Paki-upload po ulit yung buong receipt.', '2026-09-29 15:45:53'),
(7, 7, 126, NULL, 'Napalitan na po ang lock. Paki-kuha po ang bagong susi sa front desk.', '2026-09-20 12:30:00'),
(8, 9, 126, NULL, 'Na-inspect na po, may crack sa roof gutter. Schedule ang repair ngayong Sabado.', '2026-09-26 09:30:00'),
(9, 9, NULL, 20, 'Sige po, thank you. Ililipat ko muna yung gamit ko.', '2026-09-26 12:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'Teresita Mendoza', 'owner@nestph.test', NULL, '$2y$12$zQxT7KB4BbEa875oGtGnou7nmZhWdHr5vw5TBO8Qo9PWNH3Fb3g0S', 2, 1, NULL, '2026-07-24 22:12:00', '2026-09-29 15:45:52'),
(124, 'Kristine Joy Bautista', 'kristine.bautista@nestph.test', NULL, '$2y$12$zQxT7KB4BbEa875oGtGnou7nmZhWdHr5vw5TBO8Qo9PWNH3Fb3g0S', 2, 1, NULL, '2026-04-06 16:00:00', '2026-04-06 16:00:00'),
(125, 'Mark Anthony Villanueva', 'mark.villanueva@nestph.test', NULL, '$2y$12$zQxT7KB4BbEa875oGtGnou7nmZhWdHr5vw5TBO8Qo9PWNH3Fb3g0S', 2, 1, NULL, '2026-04-15 16:00:00', '2026-04-15 16:00:00'),
(126, 'Jerome Castillo', 'jerome.castillo@nestph.test', NULL, '$2y$12$zQxT7KB4BbEa875oGtGnou7nmZhWdHr5vw5TBO8Qo9PWNH3Fb3g0S', 2, 1, NULL, '2026-04-24 16:00:00', '2026-04-24 16:00:00'),
(127, 'Lea Mae Fernandez', 'lea.fernandez@nestph.test', NULL, '$2y$12$zQxT7KB4BbEa875oGtGnou7nmZhWdHr5vw5TBO8Qo9PWNH3Fb3g0S', 2, 0, NULL, '2026-05-03 16:00:00', '2026-05-03 16:00:00'),
(128, 'Maria Angelica Santos', 'maria.santos@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-04-10 02:15:00', '2026-04-10 02:15:00'),
(129, 'Kimberly Anne Dela Cruz', 'kimberly.delacruz@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-17 03:15:00', '2026-06-17 03:15:00'),
(130, 'Patricia Mae Gonzales', 'patricia.gonzales@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-07-14 04:15:00', '2026-07-14 04:15:00'),
(131, 'Nicole Joy Ramos', 'nicole.ramos@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-07-05 05:15:00', '2026-07-05 05:15:00'),
(132, 'Juan Miguel Reyes', 'juan.reyes@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-03-06 06:15:00', '2026-03-06 06:15:00'),
(133, 'John Paul Mendoza', 'john.mendoza@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-06 07:15:00', '2026-06-06 07:15:00'),
(134, 'Mark Joseph Aquino', 'mark.aquino@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-08-14 08:15:00', '2026-08-14 08:15:00'),
(135, 'Christian Dave Torres', 'christian.torres@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-05-15 01:15:00', '2026-05-15 01:15:00'),
(136, 'Benjamin Robles', 'benjamin.robles@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-09 02:15:00', '2026-06-09 02:15:00'),
(137, 'Rafael Luis Navarro', 'rafael.navarro@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-04-09 03:15:00', '2026-04-09 03:15:00'),
(138, 'Angela Marie Villanueva', 'angela.villanueva@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2025-12-31 04:15:00', '2025-12-31 04:15:00'),
(139, 'Jasmine Rose Garcia', 'jasmine.garcia@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-07-18 05:15:00', '2026-07-18 05:15:00'),
(140, 'Camille Louise Flores', 'camille.flores@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-07 06:15:00', '2026-06-07 06:15:00'),
(141, 'Bea Katrina Pascual', 'bea.pascual@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-17 07:15:00', '2026-06-17 07:15:00'),
(142, 'Princess Joy Manalo', 'princess.manalo@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-14 08:15:00', '2026-06-14 08:15:00'),
(143, 'Kathleen Mae Salazar', 'kathleen.salazar@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-09-24 01:15:00', '2026-09-24 01:15:00'),
(144, 'Carlo Miguel Bautista', 'carlo.bautista@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-07-10 02:15:00', '2026-07-10 02:15:00'),
(145, 'Joshua Emmanuel Lim', 'joshua.lim@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-04-30 03:15:00', '2026-04-30 03:15:00'),
(146, 'Paolo Andres Ocampo', 'paolo.ocampo@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-09-21 04:15:00', '2026-09-21 04:15:00'),
(147, 'Andrea Nicole Tan', 'andrea.tan@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-04-15 05:15:00', '2026-04-15 05:15:00'),
(148, 'Erika Jane Morales', 'erika.morales@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-06-07 06:15:00', '2026-06-07 06:15:00'),
(149, 'Hannah Grace Soriano', 'hannah.soriano@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-08-15 07:15:00', '2026-08-15 07:15:00'),
(150, 'Gabriel Jose Rivera', 'gabriel.rivera@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 0, NULL, '2026-03-10 08:15:00', '2026-03-10 08:15:00'),
(151, 'Joseph Allan Cruz', 'joseph.cruz@gmail.com', NULL, '$2y$12$PA9JM72C33wpR1cKAfonAeHBazTBR0eO8rYd4J/.461P4XzeZdgHe', 1, 1, NULL, '2026-03-29 01:15:00', '2026-03-29 01:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `vr_hotspots`
--

CREATE TABLE `vr_hotspots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vr_scene_id` bigint(20) UNSIGNED NOT NULL,
  `target_scene_id` bigint(20) UNSIGNED NOT NULL,
  `pitch` decimal(8,4) NOT NULL,
  `yaw` decimal(8,4) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vr_hotspots`
--

INSERT INTO `vr_hotspots` (`id`, `vr_scene_id`, `target_scene_id`, `pitch`, `yaw`, `label`, `created_at`, `updated_at`) VALUES
(3, 4, 5, -7.5450, -172.6935, 'Living Room', '2026-09-04 12:00:47', '2026-09-04 12:00:47'),
(4, 5, 4, -9.4008, 19.1198, 'Lobby', '2026-09-04 12:01:09', '2026-09-04 12:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `vr_scenes`
--

CREATE TABLE `vr_scenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `panorama_path` varchar(255) NOT NULL,
  `filled_path` varchar(255) DEFAULT NULL,
  `photo_updated_at` timestamp NULL DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `haov` decimal(6,2) NOT NULL DEFAULT 360.00,
  `vaov` decimal(6,2) NOT NULL DEFAULT 180.00,
  `v_offset` decimal(6,2) NOT NULL DEFAULT 0.00,
  `is_partial` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vr_scenes`
--

INSERT INTO `vr_scenes` (`id`, `room_id`, `title`, `panorama_path`, `filled_path`, `photo_updated_at`, `is_default`, `sort_order`, `created_at`, `updated_at`, `haov`, `vaov`, `v_offset`, `is_partial`) VALUES
(4, 16, 'Lobby', 'vr-scenes/O3TaGpevnZjt4UBPxWr0pJ1eXkNI652UeaLkegMg.jpg', 'vr-scenes/filled/84b8bb86-50b3-4d96-94fa-c037a15c65ee.jpg', '2026-09-04 11:58:02', 0, 0, '2026-09-04 11:58:02', '2026-09-29 07:01:53', 360.00, 53.73, 0.00, 0),
(5, 16, 'Living Room', 'vr-scenes/JQTAEtHNbTZtu63MKDrAT2WnaQtOr5jptx6uKvs3.jpg', 'vr-scenes/filled/4b98e0f8-a9d6-42d3-8c1f-5c02e07298c4.jpg', '2026-09-04 11:59:22', 0, 1, '2026-09-04 11:59:22', '2026-09-29 07:01:54', 360.00, 64.30, 0.00, 0),
(6, 17, 'Entrance', 'vr-scenes/fvwzAPPPNJ4u8LruXwnxA14x9FTFHsVwfcBv5rd4.jpg', 'vr-scenes/filled/69e4c5ee-ed47-4b16-9ef3-19c048ef5ace.jpg', '2026-09-04 13:06:27', 0, 0, '2026-09-04 13:06:27', '2026-09-29 07:01:55', 360.00, 53.73, 0.00, 1),
(7, 17, 'TV area', 'vr-scenes/Acj8y3hVLZ32YredRJavA32SEafC3oSgMbvv9n3c.jpg', 'vr-scenes/filled/3de7ba45-9bdd-43de-839b-8113706ec4e9.jpg', '2026-09-04 13:06:53', 0, 1, '2026-09-04 13:06:53', '2026-09-29 07:01:56', 335.91, 60.00, 0.00, 1),
(8, 18, 'Computer Room', 'vr-scenes/lMdjSMn8A122wbntaKbN868Fm7zATkwqiwu0GwOT.jpg', 'vr-scenes/filled/e522f4b9-f751-4777-9568-f832200ce33e.jpg', '2026-09-29 07:20:06', 1, 0, '2026-09-29 07:20:06', '2026-09-29 07:25:44', 360.00, 180.00, 0.00, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_access_logs_user_id_foreign` (`user_id`),
  ADD KEY `admin_access_logs_performed_by_foreign` (`performed_by`);

--
-- Indexes for table `admin_login_sessions`
--
ALTER TABLE `admin_login_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_login_sessions_user_id_logged_in_at_index` (`user_id`,`logged_in_at`);

--
-- Indexes for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_privilege` (`user_id`,`privilege_name`),
  ADD KEY `admin_privileges_granted_by_foreign` (`granted_by`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_user_id_foreign` (`user_id`);

--
-- Indexes for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_comments_announcement_id_foreign` (`announcement_id`),
  ADD KEY `announcement_comments_user_id_foreign` (`user_id`),
  ADD KEY `announcement_comments_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applications_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `applications_tenant_id_foreign` (`tenant_id`),
  ADD KEY `applications_bed_id_foreign` (`bed_id`),
  ADD KEY `applications_created_by_foreign` (`created_by`),
  ADD KEY `applications_approved_by_foreign` (`approved_by`),
  ADD KEY `applications_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beds_room_id_foreign` (`room_id`);

--
-- Indexes for table `billing_statements`
--
ALTER TABLE `billing_statements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_statements_contract_id_foreign` (`contract_id`),
  ADD KEY `billing_statements_tenant_id_status_index` (`tenant_id`,`status`),
  ADD KEY `billing_statements_status_due_date_index` (`status`,`due_date`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `damages`
--
ALTER TABLE `damages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `damages_tenant_id_foreign` (`tenant_id`),
  ADD KEY `damages_room_id_foreign` (`room_id`),
  ADD KEY `damages_bed_id_foreign` (`bed_id`),
  ADD KEY `damages_created_by_foreign` (`created_by`);

--
-- Indexes for table `dormitory_amenities`
--
ALTER TABLE `dormitory_amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dormitory_amenities_key_unique` (`key`);

--
-- Indexes for table `dormitory_house_rules`
--
ALTER TABLE `dormitory_house_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dormitory_profile`
--
ALTER TABLE `dormitory_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `escalation_logs`
--
ALTER TABLE `escalation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `escalation_logs_tenant_id_foreign` (`tenant_id`),
  ADD KEY `escalation_logs_performed_by_foreign` (`performed_by`),
  ADD KEY `escalation_logs_billing_id_action_type_index` (`billing_id`,`action_type`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiries_room_id_foreign` (`room_id`),
  ADD KEY `inquiries_status_created_at_index` (`status`,`created_at`),
  ADD KEY `inquiries_replied_by_foreign` (`replied_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lease_contracts`
--
ALTER TABLE `lease_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lease_contracts_tenant_id_foreign` (`tenant_id`),
  ADD KEY `lease_contracts_bed_id_foreign` (`bed_id`),
  ADD KEY `lease_contracts_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `lease_contracts_application_id_foreign` (`application_id`),
  ADD KEY `lease_contracts_created_by_foreign` (`created_by`),
  ADD KEY `lease_contracts_approved_by_foreign` (`approved_by`),
  ADD KEY `lease_contracts_last_renewed_by_foreign` (`last_renewed_by`);

--
-- Indexes for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_tickets_tenant_id_foreign` (`tenant_id`),
  ADD KEY `maintenance_tickets_bed_id_foreign` (`bed_id`),
  ADD KEY `maintenance_tickets_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_billing_id_foreign` (`billing_id`),
  ADD KEY `payments_recorded_by_foreign` (`recorded_by`),
  ADD KEY `payments_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `payments_tenant_id_status_index` (`tenant_id`,`status`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penalties_damage_id_foreign` (`damage_id`),
  ADD KEY `penalties_billing_id_foreign` (`billing_id`),
  ADD KEY `penalties_created_by_foreign` (`created_by`),
  ADD KEY `penalties_tenant_id_status_index` (`tenant_id`,`status`);

--
-- Indexes for table `penalty_audit_logs`
--
ALTER TABLE `penalty_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penalty_audit_logs_penalty_id_foreign` (`penalty_id`),
  ADD KEY `penalty_audit_logs_performed_by_foreign` (`performed_by`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_tenant_id_unique` (`tenant_id`),
  ADD KEY `reviews_moderated_by_foreign` (`moderated_by`),
  ADD KEY `reviews_status_index` (`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_floor_id_foreign` (`floor_id`),
  ADD KEY `rooms_status_index` (`status`);

--
-- Indexes for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_photos_room_id_foreign` (`room_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenants_user_id_foreign` (`user_id`),
  ADD KEY `tenants_deactivated_by_foreign` (`deactivated_by`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_replies_ticket_id_foreign` (`ticket_id`),
  ADD KEY `ticket_replies_user_id_foreign` (`user_id`),
  ADD KEY `ticket_replies_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `vr_hotspots`
--
ALTER TABLE `vr_hotspots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vr_hotspots_vr_scene_id_foreign` (`vr_scene_id`),
  ADD KEY `vr_hotspots_target_scene_id_foreign` (`target_scene_id`);

--
-- Indexes for table `vr_scenes`
--
ALTER TABLE `vr_scenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vr_scenes_room_id_foreign` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_login_sessions`
--
ALTER TABLE `admin_login_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `billing_statements`
--
ALTER TABLE `billing_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `damages`
--
ALTER TABLE `damages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dormitory_amenities`
--
ALTER TABLE `dormitory_amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dormitory_house_rules`
--
ALTER TABLE `dormitory_house_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dormitory_profile`
--
ALTER TABLE `dormitory_profile`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `escalation_logs`
--
ALTER TABLE `escalation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lease_contracts`
--
ALTER TABLE `lease_contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penalty_audit_logs`
--
ALTER TABLE `penalty_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `vr_hotspots`
--
ALTER TABLE `vr_hotspots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vr_scenes`
--
ALTER TABLE `vr_scenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  ADD CONSTRAINT `admin_access_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admin_access_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_login_sessions`
--
ALTER TABLE `admin_login_sessions`
  ADD CONSTRAINT `admin_login_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  ADD CONSTRAINT `admin_privileges_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admin_privileges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  ADD CONSTRAINT `announcement_comments_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_comments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcement_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`),
  ADD CONSTRAINT `applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing_statements`
--
ALTER TABLE `billing_statements`
  ADD CONSTRAINT `billing_statements_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `lease_contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_statements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `damages`
--
ALTER TABLE `damages`
  ADD CONSTRAINT `damages_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `damages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `damages_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `damages_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `escalation_logs`
--
ALTER TABLE `escalation_logs`
  ADD CONSTRAINT `escalation_logs_billing_id_foreign` FOREIGN KEY (`billing_id`) REFERENCES `billing_statements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `escalation_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `escalation_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_replied_by_foreign` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lease_contracts`
--
ALTER TABLE `lease_contracts`
  ADD CONSTRAINT `lease_contracts_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `lease_contracts_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lease_contracts_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`),
  ADD CONSTRAINT `lease_contracts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lease_contracts_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lease_contracts_last_renewed_by_foreign` FOREIGN KEY (`last_renewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lease_contracts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  ADD CONSTRAINT `maintenance_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_tickets_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_tickets_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_billing_id_foreign` FOREIGN KEY (`billing_id`) REFERENCES `billing_statements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penalties`
--
ALTER TABLE `penalties`
  ADD CONSTRAINT `penalties_billing_id_foreign` FOREIGN KEY (`billing_id`) REFERENCES `billing_statements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penalties_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penalties_damage_id_foreign` FOREIGN KEY (`damage_id`) REFERENCES `damages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penalties_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penalty_audit_logs`
--
ALTER TABLE `penalty_audit_logs`
  ADD CONSTRAINT `penalty_audit_logs_penalty_id_foreign` FOREIGN KEY (`penalty_id`) REFERENCES `penalties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penalty_audit_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD CONSTRAINT `room_photos_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_deactivated_by_foreign` FOREIGN KEY (`deactivated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD CONSTRAINT `ticket_replies_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ticket_replies_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `maintenance_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `vr_hotspots`
--
ALTER TABLE `vr_hotspots`
  ADD CONSTRAINT `vr_hotspots_target_scene_id_foreign` FOREIGN KEY (`target_scene_id`) REFERENCES `vr_scenes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vr_hotspots_vr_scene_id_foreign` FOREIGN KEY (`vr_scene_id`) REFERENCES `vr_scenes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vr_scenes`
--
ALTER TABLE `vr_scenes`
  ADD CONSTRAINT `vr_scenes_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
