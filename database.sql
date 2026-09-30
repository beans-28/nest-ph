-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 06:53 PM
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
(1, 266, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms, manage_contracts, manage_billing, view_reports', '2026-04-09 16:00:00'),
(2, 267, 3, 'granted', 'Admin access granted: manage_billing, view_reports', '2026-04-18 16:00:00'),
(3, 268, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms', '2026-04-27 16:00:00'),
(4, 269, 3, 'granted', 'Admin access granted: manage_tenants, view_reports', '2026-05-06 16:00:00'),
(5, 267, 3, 'privileges_updated', 'Privileges updated: manage_billing, view_reports', '2026-08-21 16:00:00'),
(6, 269, 3, 'revoked', 'Admin access revoked. Reason: No longer employed at the dormitory.', '2026-09-09 16:00:00');

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
(1, 3, 'fe474efbbf5c131f40e3a032b80ff35c6d823149', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(2, 3, '05e6c46877d89ee351387f5513fa1c9021931993', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(3, 3, '71220b00c1fd26d555f09fc68561e49e78eb606c', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 12:22:00', '2026-09-27 15:34:00'),
(4, 3, '52189ab65e2cac30ad242d32beece6f91aa8f234', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 09:28:00', '2026-09-25 12:40:00'),
(5, 3, '7e4f4aad2fa53be4481ea318c5328e2b956fb780', '192.168.1.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-22 12:37:00', '2026-09-22 15:49:00'),
(6, 3, '45b4f69fb3db11be7092d74ddf25c1affe0802c8', '192.168.1.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-18 11:49:00', '2026-09-18 15:01:00'),
(7, 266, 'f32ea8ccf35af115c1a9e03cc56529d7d79fcd28', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-10-01 08:10:00', NULL),
(8, 266, 'eebe92000696a5744d131b61b20f68df0fbb1d0a', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(9, 266, 'e0f58a337bff2e63bf65e743d926fd19656dd7fa', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(10, 266, '310d40a9921a0fd4d2084d1d1e199d17e85ac97f', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 11:19:00', '2026-09-28 14:31:00'),
(11, 266, 'dd895e78fd4d56e3f466ce2f665a46d8cb93e52f', '192.168.1.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-26 08:25:00', '2026-09-26 11:37:00'),
(12, 266, '7628e1e1d92a66baee7993e04834c159f34c37bf', '192.168.1.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-24 10:31:00', '2026-09-24 13:43:00'),
(13, 266, '799bb23445d89d2dde8c83f44a43ef30bfdd2e4c', '192.168.1.28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-23 11:34:00', '2026-09-23 14:46:00'),
(14, 267, '0e060a39bc92a99ff40005d84bd302aa6aa48c6d', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(15, 267, '451582968501011d81119cc4d120ee4f755230b0', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 11:19:00', '2026-09-28 14:31:00'),
(16, 267, 'd8cd54e0a3bcecec3507d4dcc80c12928338df4a', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 09:28:00', '2026-09-25 12:40:00'),
(17, 267, '67d122ab43e4c5b509bb1bb6f11f9a1cd48ac10c', '192.168.1.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-21 08:40:00', '2026-09-21 11:52:00'),
(18, 268, '5c1940317ca07799c52b297c046ef656248a81c9', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-10-01 08:10:00', NULL),
(19, 268, '8403cb246ab28b3bd1d577b37c622e7f8f409b67', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(20, 268, 'db52293ee738f46cb6df46d1632a02e2680061be', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 12:22:00', '2026-09-27 15:34:00'),
(21, 268, '6784d92e0cc37f5c0c67a8e2dc451e8caf4fe054', '192.168.1.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-20 09:43:00', '2026-09-20 12:55:00'),
(22, 3, 'VKcuvOvQ8ann3uWr0p4CJJrRoIq2elFlHj6L3hiI', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 00:14:07', NULL);

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
(1, 3, NULL, 'manage_tenants', '2026-01-31 16:00:00'),
(2, 3, NULL, 'manage_rooms', '2026-01-31 16:00:00'),
(3, 3, NULL, 'manage_contracts', '2026-01-31 16:00:00'),
(4, 3, NULL, 'manage_billing', '2026-01-31 16:00:00'),
(5, 3, NULL, 'manage_users', '2026-01-31 16:00:00'),
(6, 3, NULL, 'view_reports', '2026-01-31 16:00:00'),
(85, 266, 3, 'manage_tenants', '2026-04-09 16:00:00'),
(86, 266, 3, 'manage_rooms', '2026-04-09 16:00:00'),
(87, 266, 3, 'manage_contracts', '2026-04-09 16:00:00'),
(88, 266, 3, 'manage_billing', '2026-04-09 16:00:00'),
(89, 266, 3, 'view_reports', '2026-04-09 16:00:00'),
(90, 267, 3, 'manage_billing', '2026-04-18 16:00:00'),
(91, 267, 3, 'view_reports', '2026-04-18 16:00:00'),
(92, 268, 3, 'manage_tenants', '2026-04-27 16:00:00'),
(93, 268, 3, 'manage_rooms', '2026-04-27 16:00:00');

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
(1, 3, '📢 SCHEDULED WATER INTERRUPTION\n\nMaynilad will have a water interruption this Saturday from 9:00 AM to 4:00 PM. Please store enough water the night before. Salamat po sa pag-unawa!', 0, '2026-09-30 00:30:00', '2026-09-30 00:30:00'),
(2, 266, 'Reminder: Rent for this month is due on your billing due date. You may pay via Cash (admin office), GCash, or BDO. Please upload your proof of payment in the portal so we can verify it right away.', 0, '2026-09-25 00:30:00', '2026-09-25 00:30:00'),
(3, 3, 'Fire drill and building inspection next Wednesday, 3:00 PM. Attendance is required for all tenants who are in the building. Comments are turned off for this post.', 1, '2026-09-21 00:30:00', '2026-09-21 00:30:00');

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
(1, 1, NULL, 1, 'Noted po, thank you sa heads up!', '2026-09-30 01:30:00'),
(2, 1, NULL, 7, 'Buong araw po ba walang tubig sa lahat ng floors?', '2026-09-30 03:30:00'),
(3, 1, 266, NULL, 'Opo, lahat ng floors po. May drum ng tubig sa ground floor na pwede gamitin.', '2026-09-30 05:30:00'),
(4, 2, NULL, 8, 'Pwede po ba partial muna ngayong week?', '2026-09-25 01:30:00'),
(5, 2, 267, NULL, 'Pwede po, pero may 10% late fee kung lumampas sa 3-day grace period ang natitirang balance.', '2026-09-25 03:30:00');

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
(1, NULL, 1, 'Maria Angelica', 'Santos', '2004-03-14', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09181242486', 'maria.santos@gmail.com', NULL, 'Brgy. San Isidro, Angono, Rizal', 'Rodelio Santos', '09182034386', 'rodelio.santos.parent@gmail.com', NULL, 'Father', 1, '2026-04-21', '2027-03-20', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-10 02:15:00', '2026-04-12 02:15:00'),
(2, NULL, 2, 'Kimberly Anne', 'Dela Cruz', '2005-07-02', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09271250405', 'kimberly.delacruz@gmail.com', NULL, '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'Marites Dela Cruz', '09272042305', 'marites.delacruz.parent@gmail.com', NULL, 'Mother', 2, '2026-06-29', '2027-03-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-17 03:15:00', '2026-06-19 03:15:00'),
(3, NULL, 3, 'Patricia Mae', 'Gonzales', '2003-11-21', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09391258324', 'patricia.gonzales@gmail.com', NULL, 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'Lorna Gonzales', '09392050224', 'lorna.gonzales.parent@gmail.com', NULL, 'Mother', 3, '2026-07-27', '2027-03-26', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-07-14 04:15:00', '2026-07-16 04:15:00'),
(4, NULL, 4, 'Nicole Joy', 'Ramos', '2004-01-09', 'female', 'Filipino', 'None', 'Student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila', '09451266243', 'nicole.ramos@gmail.com', NULL, 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'Ernesto Ramos', '09452058143', 'ernesto.ramos.parent@gmail.com', NULL, 'Father', 28, '2026-07-19', '2026-10-21', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-07-05 05:15:00', '2026-07-07 05:15:00'),
(5, NULL, 5, 'Juan Miguel', 'Reyes', '1999-05-30', 'male', 'Filipino', 'None', 'Employee', 'Accenture Philippines', 'Cyber One Bldg., Eastwood City, Quezon City', '09561274162', 'juan.reyes@gmail.com', NULL, '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'Carmelita Reyes', '09562066062', 'carmelita.reyes.parent@gmail.com', NULL, 'Mother', 5, '2026-03-16', '2027-03-15', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-06 06:15:00', '2026-03-08 06:15:00'),
(6, NULL, 6, 'John Paul', 'Mendoza', '2004-08-17', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Mapúa University', 'Muralla St., Intramuros, Manila', '09212408565', 'john.mendoza@gmail.com', NULL, 'Brgy. Bagong Silang, Lucena City, Quezon', 'Rosalie Mendoza', '09212408565', 'rosalie.mendoza.parent@gmail.com', NULL, 'Mother', 34, '2026-06-17', '2027-03-16', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-06 07:15:00', '2026-06-08 07:15:00'),
(7, NULL, 7, 'Mark Joseph', 'Aquino', '2005-02-11', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09981290000', 'mark.aquino@gmail.com', NULL, 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'Josefina Aquino', '09982081900', 'josefina.aquino.parent@gmail.com', NULL, 'Mother', 7, '2026-08-26', '2027-03-25', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-08-14 08:15:00', '2026-08-16 08:15:00'),
(8, NULL, 8, 'Christian Dave', 'Torres', '2002-12-03', 'male', 'Filipino', 'None', 'Employee', 'Jollibee Foods Corp. (V. Mapa branch)', 'V. Mapa St., Sta. Mesa, Manila', '09081297919', 'christian.torres@gmail.com', NULL, '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'Dante Torres', '09082089819', 'dante.torres.parent@gmail.com', NULL, 'Father', 8, '2026-05-28', '2027-03-27', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-05-15 01:15:00', '2026-05-17 01:15:00'),
(9, NULL, 9, 'Benjamin', 'Robles', '2004-06-25', 'male', 'Filipino', 'None', 'Student', 'National University', 'M.F. Jhocson St., Sampaloc, Manila', '09212408565', 'benjamin.robles@gmail.com', NULL, 'Brgy. Santo Cristo, San Fernando, Pampanga', 'Evelyn Robles', '09212408565', 'evelyn.robles.parent@gmail.com', NULL, 'Mother', 9, '2026-06-23', '2027-03-22', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-09 02:15:00', '2026-06-11 02:15:00'),
(10, NULL, 10, 'Rafael Luis', 'Navarro', '2001-09-14', 'male', 'Filipino', 'None', 'Employee', 'BDO Unibank, Sta. Mesa Branch', 'Ramon Magsaysay Blvd., Sta. Mesa, Manila', '09171313757', 'rafael.navarro@gmail.com', NULL, '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'Gloria Navarro', '09172105657', 'gloria.navarro.parent@gmail.com', NULL, 'Mother', 37, '2026-04-19', '2026-09-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-09 03:15:00', '2026-04-11 03:15:00'),
(11, NULL, 11, 'Angela Marie', 'Villanueva', '1998-04-19', 'female', 'Filipino', 'None', 'Employee', 'Philippine General Hospital', 'Taft Ave., Ermita, Manila', '09181321676', 'angela.villanueva@gmail.com', NULL, 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'Arnel Villanueva', '09182113576', 'arnel.villanueva.parent@gmail.com', NULL, 'Father', 12, '2026-01-11', '2027-03-10', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-12-31 04:15:00', '2026-01-02 04:15:00'),
(12, NULL, 12, 'Jasmine Rose', 'Garcia', '2005-10-05', 'female', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Centro Escolar University', 'Mendiola St., San Miguel, Manila', '09271329595', 'jasmine.garcia@gmail.com', NULL, 'Purok 2, Brgy. Talon, Las Piñas City', 'Ramil Garcia', '09272121495', 'ramil.garcia.parent@gmail.com', NULL, 'Father', 13, '2026-07-30', '2027-03-29', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-07-18 05:15:00', '2026-07-20 05:15:00'),
(13, NULL, 13, 'Camille Louise', 'Flores', '2004-12-28', 'female', 'Filipino', 'None', 'Student', 'Lyceum of the Philippines University', 'Muralla St., Intramuros, Manila', '09212408565', 'camille.flores@gmail.com', NULL, 'Brgy. Mabini, Batangas City, Batangas', 'Susan Flores', '09212408565', 'susan.flores.parent@gmail.com', NULL, 'Mother', 14, '2026-06-20', '2027-03-19', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-07 06:15:00', '2026-06-09 06:15:00'),
(14, NULL, 14, 'Bea Katrina', 'Pascual', '2003-05-16', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09451345433', 'bea.pascual@gmail.com', NULL, 'Brgy. Sta. Rita, Olongapo City, Zambales', 'Gerardo Pascual', '09452137333', 'gerardo.pascual.parent@gmail.com', NULL, 'Father', 15, '2026-07-01', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-17 07:15:00', '2026-06-19 07:15:00'),
(15, NULL, 15, 'Princess Joy', 'Manalo', '2002-08-08', 'female', 'Filipino', 'None', 'Student', 'Pamantasan ng Lungsod ng Maynila', 'General Luna St., Intramuros, Manila', '09561353352', 'princess.manalo@gmail.com', NULL, 'Brgy. Parian, Calamba City, Laguna', 'Cristina Manalo', '09562145252', 'cristina.manalo.parent@gmail.com', NULL, 'Mother', 16, '2026-06-24', '2027-03-23', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-14 08:15:00', '2026-06-16 08:15:00'),
(16, NULL, 16, 'Kathleen Mae', 'Salazar', '2006-01-30', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09661361271', 'kathleen.salazar@gmail.com', NULL, 'Brgy. Poblacion, Tagum City, Davao del Norte', 'Rogelio Salazar', '09662153171', 'rogelio.salazar.parent@gmail.com', NULL, 'Father', 17, '2026-10-05', '2027-04-04', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-09-24 01:15:00', '2026-09-26 01:15:00'),
(17, NULL, 17, 'Carlo Miguel', 'Bautista', '2000-03-03', 'male', 'Filipino', 'None', 'Employee', 'Globe Telecom', 'The Globe Tower, BGC, Taguig City', '09981369190', 'carlo.bautista@gmail.com', NULL, 'San Pablo City, Laguna', 'Nenita Bautista', '09982161090', 'nenita.bautista.parent@gmail.com', NULL, 'Mother', 18, '2026-07-22', '2027-03-21', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-07-10 02:15:00', '2026-07-12 02:15:00'),
(18, NULL, 18, 'Joshua Emmanuel', 'Lim', '2004-11-11', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'De La Salle University', 'Taft Ave., Malate, Manila', '09081377109', 'joshua.lim@gmail.com', NULL, 'Sta. Cruz, Laguna', 'Wilson Lim', '09082169009', 'wilson.lim.parent@gmail.com', NULL, 'Father', 20, '2026-05-13', '2027-03-12', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-30 03:15:00', '2026-05-02 03:15:00'),
(19, NULL, 19, 'Paolo Andres', 'Ocampo', '2005-04-22', 'male', 'Filipino', 'None', 'Student', 'Adamson University', 'San Marcelino St., Ermita, Manila', '09951385028', 'paolo.ocampo@gmail.com', NULL, 'Brgy. Poblacion, Malolos City, Bulacan', 'Amelia Ocampo', '09952176928', 'amelia.ocampo.parent@gmail.com', NULL, 'Mother', 21, '2026-10-05', '2027-04-04', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-09-21 04:15:00', '2026-09-23 04:15:00'),
(20, NULL, 20, 'Andrea Nicole', 'Tan', '1997-09-09', 'female', 'Filipino', 'None', 'Employee', 'SM Supermalls Corporate Office', 'Mall of Asia Complex, Pasay City', '09171392947', 'andrea.tan@gmail.com', NULL, 'Brgy. Kauswagan, Cagayan de Oro City', 'Rebecca Tan', '09172184847', 'rebecca.tan.parent@gmail.com', NULL, 'Mother', 24, '2026-04-25', '2027-03-24', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-15 05:15:00', '2026-04-17 05:15:00'),
(21, NULL, 21, 'Erika Jane', 'Morales', '2004-07-07', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09181400866', 'erika.morales@gmail.com', NULL, 'Brgy. Pantal, Dagupan City, Pangasinan', 'Ronaldo Morales', '09182192766', 'ronaldo.morales.parent@gmail.com', NULL, 'Father', 25, '2026-06-18', '2027-03-17', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-07 06:15:00', '2026-06-09 06:15:00'),
(22, NULL, 22, 'Hannah Grace', 'Soriano', '2006-02-14', 'female', 'Filipino', 'None', 'Student', 'Philippine Normal University', 'Taft Ave., Ermita, Manila', '09271408785', 'hannah.soriano@gmail.com', NULL, 'Brgy. Dolores, Taytay, Rizal', 'Marilou Soriano', '09272200685', 'marilou.soriano.parent@gmail.com', NULL, 'Mother', 26, '2026-08-27', '2027-03-26', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-08-15 07:15:00', '2026-08-17 07:15:00'),
(23, NULL, 23, 'Gabriel Jose', 'Rivera', '2001-06-01', 'male', 'Filipino', 'None', 'Employee', 'Meralco', 'Ortigas Ave., Pasig City', '09391416704', 'gabriel.rivera@gmail.com', NULL, 'Brgy. San Antonio, Biñan, Laguna', 'Imelda Rivera', '09392208604', 'imelda.rivera.parent@gmail.com', NULL, 'Mother', 29, '2026-03-23', '2026-08-22', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-10 08:15:00', '2026-03-12 08:15:00'),
(24, NULL, 24, 'Joseph Allan', 'Cruz', '2003-03-27', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Emilio Aguinaldo College', 'Gen. Malvar St., Malate, Manila', '09212408565', 'joseph.cruz@gmail.com', NULL, 'Brgy. Tabing Ilog, Marilao, Bulacan', 'Nora Cruz', '09212408565', 'nora.cruz.parent@gmail.com', NULL, 'Mother', 31, '2026-04-12', '2027-02-11', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-29 01:15:00', '2026-03-31 01:15:00'),
(25, NULL, 25, 'Stephanie Claire', 'Uy', '2004-09-02', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09561432542', 'stephanie.uy@gmail.com', NULL, 'Brgy. Lourdes, Dagupan City, Pangasinan', 'Henry Uy', '09562224442', 'henry.uy.parent@gmail.com', NULL, 'Father', 4, '2026-02-15', '2027-03-14', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-02-05 02:15:00', '2026-02-07 02:15:00'),
(26, NULL, 26, 'Adrian Paul', 'Castro', '1999-12-12', 'male', 'Filipino', 'None', 'Employee', 'Teleperformance Philippines', 'Robinsons Cybergate, Mandaluyong City', '09661440461', 'adrian.castro@gmail.com', NULL, 'Brgy. Sampaloc, Tanauan City, Batangas', 'Leticia Castro', '09662232361', 'leticia.castro.parent@gmail.com', NULL, 'Mother', 6, '2026-06-20', '2027-03-19', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-09 03:15:00', '2026-06-11 03:15:00'),
(27, NULL, 27, 'Vincent Ray', 'Magbanua', '2005-06-19', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09981448380', 'vincent.magbanua@gmail.com', NULL, 'Brgy. Poblacion, Roxas City, Capiz', 'Ramon Magbanua', '09982240280', 'ramon.magbanua.parent@gmail.com', NULL, 'Father', 10, '2026-07-22', '2027-03-21', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-07-10 04:15:00', '2026-07-12 04:15:00'),
(28, NULL, 28, 'Luis Antonio', 'Del Rosario', '2003-02-27', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09081456299', 'luis.delrosario@gmail.com', NULL, 'Brgy. Cutcut, Angeles City, Pampanga', 'Rowena Del Rosario', '09082248199', 'rowena.delrosario.parent@gmail.com', NULL, 'Mother', 29, '2026-09-10', '2027-03-09', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-08-28 05:15:00', '2026-08-30 05:15:00'),
(29, NULL, 29, 'Jerome Anthony', 'Pineda', '2004-10-30', 'male', 'Filipino', 'None', 'Student', 'Mapúa University', 'Muralla St., Intramuros, Manila', '09951464218', 'jerome.pineda@gmail.com', NULL, 'Brgy. Poblacion, Tarlac City, Tarlac', 'Grace Pineda', '09952256118', 'grace.pineda.parent@gmail.com', NULL, 'Mother', 31, '2026-09-17', '2027-03-16', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-09-03 06:15:00', '2026-09-05 06:15:00'),
(30, NULL, 30, 'Kenneth Bryan', 'Sy', '2006-01-08', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'National University', 'M.F. Jhocson St., Sampaloc, Manila', '09171472137', 'kenneth.sy@gmail.com', NULL, 'Brgy. Balibago, Sta. Rosa City, Laguna', 'Victor Sy', '09172264037', 'victor.sy.parent@gmail.com', NULL, 'Father', 35, '2026-09-29', '2027-03-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-09-19 07:15:00', '2026-09-21 07:15:00'),
(31, NULL, 31, 'Emmanuel Jose', 'Villareal', '2002-07-21', 'male', 'Filipino', 'None', 'Employee', '7-Eleven (Legarda branch)', 'Legarda St., Sampaloc, Manila', '09181480056', 'emmanuel.villareal@gmail.com', NULL, 'Brgy. San Vicente, Tacloban City, Leyte', 'Nelia Villareal', '09182271956', 'nelia.villareal.parent@gmail.com', NULL, 'Mother', 36, '2026-05-07', '2027-03-06', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-26 08:15:00', '2026-04-28 08:15:00'),
(32, NULL, 32, 'Janelle', 'Tolentino', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09182826286', 'janelle.tolentino@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Tolentino', '09183618186', 'ricardo.tolentino.parent@gmail.com', NULL, 'Father', 1, '2025-09-01', '2026-03-01', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-17 02:30:00', '2025-08-19 02:30:00'),
(33, NULL, 33, 'Oliver', 'Galang', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09272834205', 'oliver.galang@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Galang', '09273626105', 'elena.galang.parent@gmail.com', NULL, 'Mother', 2, '2025-09-07', '2026-02-07', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-22 03:30:00', '2025-08-24 03:30:00'),
(34, NULL, 34, 'Elijah', 'Sison', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09392842124', 'elijah.sison@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Sison', '09393634024', 'manuel.sison.parent@gmail.com', NULL, 'Father', 2, '2026-03-19', '2026-06-22', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-09 04:30:00', '2026-03-11 04:30:00'),
(35, NULL, 35, 'Irish', 'Umali', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09452850043', 'irish.umali@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Umali', '09453641943', 'teresa.umali.parent@gmail.com', NULL, 'Mother', 3, '2025-09-25', '2026-02-25', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-14 05:30:00', '2025-09-16 05:30:00'),
(36, NULL, 36, 'Nathan', 'Tolentino', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09562857962', 'nathan.tolentino@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Tolentino', '09563649862', 'roberto.tolentino.parent@gmail.com', NULL, 'Father', 3, '2026-04-18', '2026-07-20', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-06 06:30:00', '2026-04-08 06:30:00'),
(37, NULL, 37, 'Clarisse', 'Figueroa', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09662865881', 'clarisse.figueroa@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Figueroa', '09663657781', 'lourdes.figueroa.parent@gmail.com', NULL, 'Mother', 4, '2025-09-24', '2026-01-24', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-11 07:30:00', '2025-09-13 07:30:00'),
(38, NULL, 38, 'Jericho', 'Figueroa', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09982873800', 'jericho.figueroa@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Figueroa', '09983665700', 'ricardo.figueroa.parent@gmail.com', NULL, 'Father', 5, '2025-09-24', '2026-03-09', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-10 08:30:00', '2025-09-12 08:30:00'),
(39, NULL, 39, 'Alyssa', 'Ilagan', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09082881719', 'alyssa.ilagan@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Ilagan', '09083673619', 'elena.ilagan.parent@gmail.com', NULL, 'Mother', 6, '2025-09-13', '2026-04-13', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-29 01:30:00', '2025-08-31 01:30:00'),
(40, NULL, 40, 'Queenie', 'Zamora', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09952889638', 'queenie.zamora@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Zamora', '09953681538', 'manuel.zamora.parent@gmail.com', NULL, 'Father', 7, '2025-09-10', '2026-02-10', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-25 02:30:00', '2025-08-27 02:30:00'),
(41, NULL, 41, 'Ella', 'Ortega', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09172897557', 'ella.ortega@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Ortega', '09173689457', 'teresa.ortega.parent@gmail.com', NULL, 'Mother', 7, '2026-03-25', '2026-08-19', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-15 03:30:00', '2026-03-17 03:30:00'),
(42, NULL, 42, 'Irish', 'Galang', '2006-10-01', 'female', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09182905476', 'irish.galang@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Galang', '09183697376', 'roberto.galang.parent@gmail.com', NULL, 'Father', 8, '2025-09-23', '2026-03-23', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-12 04:30:00', '2025-09-14 04:30:00'),
(43, NULL, 43, 'Oliver', 'Figueroa', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09272913395', 'oliver.figueroa@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Figueroa', '09273705295', 'lourdes.figueroa.parent@gmail.com', NULL, 'Mother', 9, '2025-09-21', '2026-03-21', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-09 05:30:00', '2025-09-11 05:30:00'),
(44, NULL, 44, 'Queenie', 'Figueroa', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09392921314', 'queenie.figueroa@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Ricardo Figueroa', '09393713214', 'ricardo.figueroa.parent@gmail.com', NULL, 'Father', 10, '2025-10-12', '2026-05-12', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-29 06:30:00', '2025-10-01 06:30:00'),
(45, NULL, 45, 'Sofia', 'Zamora', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09452929233', 'sofia.zamora@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Elena Zamora', '09453721133', 'elena.zamora.parent@gmail.com', NULL, 'Mother', 12, '2025-09-21', '2025-12-21', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-07 07:30:00', '2025-09-09 07:30:00'),
(46, NULL, 46, 'Marco', 'Galang', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09562937152', 'marco.galang@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Manuel Galang', '09563729052', 'manuel.galang.parent@gmail.com', NULL, 'Father', 13, '2025-09-25', '2026-03-25', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-10 08:30:00', '2025-09-12 08:30:00'),
(47, NULL, 47, 'Bianca', 'Nepomuceno', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09662945071', 'bianca.nepomuceno@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Teresa Nepomuceno', '09663736971', 'teresa.nepomuceno.parent@gmail.com', NULL, 'Mother', 13, '2026-04-29', '2026-07-23', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-13 01:30:00', '2026-04-15 01:30:00'),
(48, NULL, 48, 'Warren', 'Macaraeg', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09982952990', 'warren.macaraeg@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Roberto Macaraeg', '09983744890', 'roberto.macaraeg.parent@gmail.com', NULL, 'Father', 14, '2025-10-13', '2026-03-13', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-10-03 02:30:00', '2025-10-05 02:30:00'),
(49, NULL, 49, 'Francine', 'Cordero', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09082960909', 'francine.cordero@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Lourdes Cordero', '09083752809', 'lourdes.cordero.parent@gmail.com', NULL, 'Mother', 15, '2025-09-20', '2026-05-20', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-09 03:30:00', '2025-09-11 03:30:00'),
(50, NULL, 50, 'Vanessa', 'Umali', '2006-10-01', 'female', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09952968828', 'vanessa.umali@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Ricardo Umali', '09953760728', 'ricardo.umali.parent@gmail.com', NULL, 'Father', 16, '2025-09-25', '2025-12-25', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-13 04:30:00', '2025-09-15 04:30:00'),
(51, NULL, 51, 'Ysabel', 'Figueroa', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09172976747', 'ysabel.figueroa@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Elena Figueroa', '09173768647', 'elena.figueroa.parent@gmail.com', NULL, 'Mother', 16, '2026-01-25', '2026-06-17', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-01-12 05:30:00', '2026-01-14 05:30:00'),
(52, NULL, 52, 'Elijah', 'Fernandez', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09182984666', 'elijah.fernandez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Manuel Fernandez', '09183776566', 'manuel.fernandez.parent@gmail.com', NULL, 'Father', 17, '2025-09-17', '2026-03-17', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-03 06:30:00', '2025-09-05 06:30:00'),
(53, NULL, 53, 'Harold', 'Hernandez', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09272992585', 'harold.hernandez@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Teresa Hernandez', '09273784485', 'teresa.hernandez.parent@gmail.com', NULL, 'Mother', 17, '2026-04-02', '2026-09-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-18 07:30:00', '2026-03-20 07:30:00'),
(54, NULL, 54, 'Francine', 'Nepomuceno', '2006-10-01', 'female', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09393000504', 'francine.nepomuceno@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Roberto Nepomuceno', '09393792404', 'roberto.nepomuceno.parent@gmail.com', NULL, 'Father', 18, '2025-09-18', '2026-01-18', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-02 08:30:00', '2025-09-04 08:30:00'),
(55, NULL, 55, 'Bryan', 'Macaraeg', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09453008423', 'bryan.macaraeg@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Lourdes Macaraeg', '09453800323', 'lourdes.macaraeg.parent@gmail.com', NULL, 'Mother', 18, '2026-03-04', '2026-07-15', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-02-22 01:30:00', '2026-02-24 01:30:00'),
(56, NULL, 56, 'Gwen', 'Abad', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09563016342', 'gwen.abad@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Ricardo Abad', '09563808242', 'ricardo.abad.parent@gmail.com', NULL, 'Father', 19, '2025-10-14', '2026-01-14', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-10-03 02:30:00', '2025-10-05 02:30:00'),
(57, NULL, 57, 'Ella', 'Yap', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09663024261', 'ella.yap@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Elena Yap', '09663816161', 'elena.yap.parent@gmail.com', NULL, 'Mother', 19, '2026-02-01', '2026-07-01', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-01-20 03:30:00', '2026-01-22 03:30:00'),
(58, NULL, 58, 'Gian', 'Hernandez', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09983032180', 'gian.hernandez@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Manuel Hernandez', '09983824080', 'manuel.hernandez.parent@gmail.com', NULL, 'Father', 20, '2025-09-19', '2026-01-19', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-06 04:30:00', '2025-09-08 04:30:00'),
(59, NULL, 59, 'Clarisse', 'Cordero', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09083040099', 'clarisse.cordero@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Teresa Cordero', '09083831999', 'teresa.cordero.parent@gmail.com', NULL, 'Mother', 20, '2026-02-16', '2026-05-06', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-02-02 05:30:00', '2026-02-04 05:30:00'),
(60, NULL, 60, 'Lianne', 'Quiambao', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09953048018', 'lianne.quiambao@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Roberto Quiambao', '09953839918', 'roberto.quiambao.parent@gmail.com', NULL, 'Father', 21, '2025-10-02', '2026-06-02', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-17 06:30:00', '2025-09-19 06:30:00'),
(61, NULL, 61, 'Dominic', 'Agustin', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09173055937', 'dominic.agustin@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Lourdes Agustin', '09173847837', 'lourdes.agustin.parent@gmail.com', NULL, 'Mother', 21, '2026-06-18', '2026-09-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-06-02 07:30:00', '2026-06-04 07:30:00'),
(62, NULL, 62, 'Marco', 'Rosales', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09183063856', 'marco.rosales@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Rosales', '09183855756', 'ricardo.rosales.parent@gmail.com', NULL, 'Father', 22, '2025-09-15', '2026-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-05 08:30:00', '2025-09-07 08:30:00'),
(63, NULL, 63, 'Aldrin', 'Dizon', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09273071775', 'aldrin.dizon@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Dizon', '09273863675', 'elena.dizon.parent@gmail.com', NULL, 'Mother', 24, '2025-09-04', '2025-12-04', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-24 01:30:00', '2025-08-26 01:30:00'),
(64, NULL, 64, 'Sofia', 'Umali', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09393079694', 'sofia.umali@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Umali', '09393871594', 'manuel.umali.parent@gmail.com', NULL, 'Father', 24, '2026-01-08', '2026-04-18', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-12-27 02:30:00', '2025-12-29 02:30:00'),
(65, NULL, 65, 'Bianca', 'Javier', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09453087613', 'bianca.javier@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Javier', '09453879513', 'teresa.javier.parent@gmail.com', NULL, 'Mother', 25, '2025-10-06', '2026-04-06', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-23 03:30:00', '2025-09-25 03:30:00'),
(66, NULL, 66, 'Renz', 'Javier', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09563095532', 'renz.javier@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Javier', '09563887432', 'roberto.javier.parent@gmail.com', NULL, 'Father', 26, '2025-10-01', '2026-02-01', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-17 04:30:00', '2025-09-19 04:30:00'),
(67, NULL, 67, 'Queenie', 'Fernandez', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09663103451', 'queenie.fernandez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Fernandez', '09663895351', 'lourdes.fernandez.parent@gmail.com', NULL, 'Mother', 26, '2026-03-10', '2026-07-10', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-02-23 05:30:00', '2026-02-25 05:30:00'),
(68, NULL, 68, 'Kevin', 'Macaraeg', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09983111370', 'kevin.macaraeg@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Macaraeg', '09983903270', 'ricardo.macaraeg.parent@gmail.com', NULL, 'Father', 27, '2025-10-05', '2026-05-05', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-19 06:30:00', '2025-09-21 06:30:00'),
(69, NULL, 69, 'Danica', 'Castillo', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09083119289', 'danica.castillo@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Castillo', '09083911189', 'elena.castillo.parent@gmail.com', NULL, 'Mother', 28, '2025-10-02', '2026-04-02', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-22 07:30:00', '2025-09-24 07:30:00'),
(70, NULL, 70, 'Oliver', 'Umali', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09953127208', 'oliver.umali@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Umali', '09953919108', 'manuel.umali.parent@gmail.com', NULL, 'Father', 28, '2026-04-25', '2026-07-12', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-04-14 08:30:00', '2026-04-16 08:30:00'),
(71, NULL, 71, 'Troy', 'Quiambao', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09173135127', 'troy.quiambao@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Quiambao', '09173927027', 'teresa.quiambao.parent@gmail.com', NULL, 'Mother', 29, '2025-09-12', '2026-03-16', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-31 01:30:00', '2025-09-02 01:30:00'),
(72, NULL, 72, 'Irish', 'Fernandez', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09183143046', 'irish.fernandez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Fernandez', '09183934946', 'roberto.fernandez.parent@gmail.com', NULL, 'Father', 30, '2025-10-14', '2026-03-14', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-10-01 02:30:00', '2025-10-03 02:30:00'),
(73, NULL, 73, 'Lance', 'Rosales', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09273150965', 'lance.rosales@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Rosales', '09273942865', 'lourdes.rosales.parent@gmail.com', NULL, 'Mother', 30, '2026-04-06', '2026-07-11', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-23 03:30:00', '2026-03-25 03:30:00'),
(74, NULL, 74, 'Gian', 'Ortega', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09393158884', 'gian.ortega@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Ricardo Ortega', '09393950784', 'ricardo.ortega.parent@gmail.com', NULL, 'Father', 31, '2025-10-04', '2026-04-05', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-19 04:30:00', '2025-09-21 04:30:00'),
(75, NULL, 75, 'Renz', 'Ortega', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09453166803', 'renz.ortega@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Elena Ortega', '09453958703', 'elena.ortega.parent@gmail.com', NULL, 'Mother', 32, '2025-09-24', '2026-02-27', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-08 05:30:00', '2025-09-10 05:30:00'),
(76, NULL, 76, 'Janelle', 'Agustin', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09563174722', 'janelle.agustin@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Manuel Agustin', '09563966622', 'manuel.agustin.parent@gmail.com', NULL, 'Father', 33, '2025-09-07', '2025-12-07', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-28 06:30:00', '2025-08-30 06:30:00'),
(77, NULL, 77, 'Troy', 'Panganiban', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09663182641', 'troy.panganiban@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Teresa Panganiban', '09663974541', 'teresa.panganiban.parent@gmail.com', NULL, 'Mother', 33, '2025-12-17', '2026-03-17', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-12-06 07:30:00', '2025-12-08 07:30:00'),
(78, NULL, 78, 'Lance', 'Umali', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09983190560', 'lance.umali@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Roberto Umali', '09983982460', 'roberto.umali.parent@gmail.com', NULL, 'Father', 33, '2026-04-02', '2026-08-02', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-21 08:30:00', '2026-03-23 08:30:00'),
(79, NULL, 79, 'Kyla', 'Belmonte', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09083198479', 'kyla.belmonte@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Lourdes Belmonte', '09083990379', 'lourdes.belmonte.parent@gmail.com', NULL, 'Mother', 34, '2025-09-02', '2026-03-02', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-20 01:30:00', '2025-08-22 01:30:00'),
(80, NULL, 80, 'Renz', 'Abad', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09953206398', 'renz.abad@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Ricardo Abad', '09953998298', 'ricardo.abad.parent@gmail.com', NULL, 'Father', 34, '2026-03-30', '2026-06-10', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-03-16 02:30:00', '2026-03-18 02:30:00'),
(81, NULL, 81, 'Gian', 'Macaraeg', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09173214317', 'gian.macaraeg@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Elena Macaraeg', '09174006217', 'elena.macaraeg.parent@gmail.com', NULL, 'Mother', 35, '2025-09-30', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-15 03:30:00', '2025-09-17 03:30:00'),
(82, NULL, 82, 'Ivan', 'Nepomuceno', '2006-10-01', 'male', 'Filipino', 'None', 'Employee', 'University of the East', 'Manila', '09183222236', 'ivan.nepomuceno@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Manuel Nepomuceno', '09184014136', 'manuel.nepomuceno.parent@gmail.com', NULL, 'Father', 35, '2026-03-10', '2026-09-22', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-02-22 04:30:00', '2026-02-24 04:30:00'),
(83, NULL, 83, 'Danica', 'Abad', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09273230155', 'danica.abad@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Teresa Abad', '09274022055', 'teresa.abad.parent@gmail.com', NULL, 'Mother', 36, '2025-09-02', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-08-23 05:30:00', '2025-08-25 05:30:00'),
(84, NULL, 84, 'Zach', 'Fernandez', '2006-10-01', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09393238074', 'zach.fernandez@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Roberto Fernandez', '09394029974', 'roberto.fernandez.parent@gmail.com', NULL, 'Father', 37, '2025-09-14', '2026-01-14', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2025-09-03 06:30:00', '2025-09-05 06:30:00'),
(85, NULL, 85, 'Clarisse', 'Dizon', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09453245993', 'clarisse.dizon@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Lourdes Dizon', '09454037893', 'lourdes.dizon.parent@gmail.com', NULL, 'Mother', 37, '2026-01-24', '2026-04-12', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, 266, '2026-01-12 07:30:00', '2026-01-14 07:30:00'),
(86, NULL, NULL, 'Ana Beatriz', 'Salonga', '2006-03-08', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09212408565', 'ana.salonga@gmail.com', NULL, 'Brgy. Poblacion, Bontoc, Mountain Province', 'Ramon Salonga', '09173610267', NULL, NULL, 'Father', 19, '2026-10-04', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-ana-beatriz-salonga.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-29 05:05:00', '2026-09-29 05:05:00'),
(87, NULL, NULL, 'Ryan Christopher', 'Santiago', '2005-09-18', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09182826286', 'ryan.santiago@gmail.com', NULL, 'Brgy. Bagumbayan, Taguig City', 'Rommel Santiago', '09183618186', NULL, NULL, 'Mother', 22, '2026-10-07', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-ryan-christopher-santiago.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-30 06:05:00', '2026-09-30 06:05:00'),
(88, NULL, NULL, 'Alyssa Mae', 'Mercado', '2006-04-03', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09272834205', 'alyssa.mercado@gmail.com', NULL, 'Brgy. Malinta, Valenzuela City', 'Liza Mercado', '09273626105', NULL, NULL, 'Father', 27, '2026-10-10', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-alyssa-mae-mercado.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-10-01 07:05:00', '2026-10-01 07:05:00'),
(89, NULL, NULL, 'Kevin James', 'Dizon', '2000-10-10', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Philippines', 'Manila', '09392842124', 'kevin.dizon@gmail.com', NULL, 'Brgy. San Isidro, Cainta, Rizal', 'Jun Dizon', '09393634024', NULL, NULL, 'Mother', 30, '2026-10-13', '2027-04-01', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kevin-james-dizon.pdf', 1, 'pending', NULL, NULL, NULL, NULL, '2026-09-29 08:05:00', '2026-09-29 08:05:00'),
(90, NULL, NULL, 'Sophia Isabel', 'Lopez', '2004-05-25', 'female', 'Filipino', 'None', 'Student', 'San Beda University', 'Manila', '09452850043', 'sophia.lopez@gmail.com', NULL, 'Brgy. Poblacion, Muntinlupa City', 'Maricel Lopez', '09453641943', NULL, NULL, 'Father', 32, '2026-10-16', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-sophia-isabel-lopez.pdf', 1, 'rejected', 'Requested stay is only 1 month; the dormitory requires a minimum 3-month contract.', NULL, NULL, NULL, '2026-09-25 09:05:00', '2026-09-26 09:05:00'),
(91, NULL, NULL, 'Daniel Lorenzo', 'Cruz', '2005-01-15', 'male', 'Filipino', 'None', 'Student', 'Mapúa University', 'Manila', '09562857962', 'daniel.cruz@gmail.com', NULL, 'Brgy. Tambo, Parañaque City', 'Bong Cruz', '09563649862', NULL, NULL, 'Mother', 33, '2026-10-19', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-daniel-lorenzo-cruz.pdf', 1, 're_application_requested', NULL, 'Uploaded ID is blurry and the name cannot be read. Please re-apply with a clear photo of a valid school or government ID.', NULL, NULL, '2026-09-27 10:05:00', '2026-09-28 10:05:00'),
(92, NULL, NULL, 'Mika Ella', 'Santos', '2006-08-12', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09662865881', 'mika.santos@gmail.com', NULL, 'Brgy. Sto. Niño, Marikina City', 'Tess Santos', '09663657781', NULL, NULL, 'Father', 35, '2026-10-22', '2027-04-01', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-mika-ella-santos.pdf', 1, 'cancelled', NULL, NULL, NULL, NULL, '2026-09-22 11:05:00', '2026-09-23 11:05:00');

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
(1, 16, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(2, 16, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(3, 16, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(4, 16, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(5, 17, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(6, 17, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(7, 18, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(8, 18, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(9, 18, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(10, 18, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(11, 84, 'Bed 1', 'maintenance', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(12, 19, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(13, 19, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(14, 19, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(15, 19, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(16, 19, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(17, 19, 'Bed 6', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(18, 85, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(19, 85, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(20, 86, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(21, 86, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(22, 86, 'Bed 3', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(23, 86, 'Bed 4', 'maintenance', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(24, 87, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(25, 88, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(26, 88, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(27, 88, 'Bed 3', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(28, 88, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(29, 89, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(30, 89, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(31, 90, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(32, 90, 'Bed 2', 'vacant', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(33, 90, 'Bed 3', 'vacant', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(34, 90, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(35, 90, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(36, 90, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(37, 91, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-09-30 16:13:55');

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
(1, 1, 1, 'move_in', '2026-04-21', '2026-04-21', '2026-04-21', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-04-12 02:15:00', '2026-09-30 16:13:55'),
(2, 1, 1, 'monthly', '2026-04-21', '2026-05-20', '2026-04-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-20 16:05:00', '2026-09-30 16:13:55'),
(3, 1, 1, 'monthly', '2026-05-21', '2026-06-20', '2026-05-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-20 16:05:00', '2026-09-30 16:13:55'),
(4, 1, 1, 'monthly', '2026-06-21', '2026-07-20', '2026-06-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-20 16:05:00', '2026-09-30 16:13:55'),
(5, 1, 1, 'monthly', '2026-07-21', '2026-08-20', '2026-07-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-20 16:05:00', '2026-09-30 16:13:55'),
(6, 1, 1, 'monthly', '2026-08-21', '2026-09-20', '2026-08-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-20 16:05:00', '2026-09-30 16:13:55'),
(7, 1, 1, 'monthly', '2026-09-21', '2026-10-20', '2026-09-26', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-20 16:05:00', '2026-09-30 16:13:55'),
(8, 2, 2, 'move_in', '2026-06-29', '2026-06-29', '2026-06-29', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-06-19 03:15:00', '2026-09-30 16:13:55'),
(9, 2, 2, 'monthly', '2026-06-29', '2026-07-28', '2026-07-04', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-28 16:05:00', '2026-09-30 16:13:55'),
(10, 2, 2, 'monthly', '2026-07-29', '2026-08-28', '2026-08-03', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-28 16:05:00', '2026-09-30 16:13:55'),
(11, 2, 2, 'monthly', '2026-08-29', '2026-09-28', '2026-09-03', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-28 16:05:00', '2026-09-30 16:13:55'),
(12, 2, 2, 'monthly', '2026-09-29', '2026-10-28', '2026-10-04', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'unpaid', '2026-09-28 16:05:00', '2026-09-30 16:13:55'),
(13, 3, 3, 'move_in', '2026-07-27', '2026-07-27', '2026-07-27', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-07-16 04:15:00', '2026-09-30 16:13:55'),
(14, 3, 3, 'monthly', '2026-07-27', '2026-08-26', '2026-08-01', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-26 16:05:00', '2026-09-30 16:13:55'),
(15, 3, 3, 'monthly', '2026-08-27', '2026-09-26', '2026-09-01', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-26 16:05:00', '2026-09-30 16:13:55'),
(16, 3, 3, 'monthly', '2026-09-27', '2026-10-26', '2026-10-02', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'unpaid', '2026-09-26 16:05:00', '2026-09-30 16:13:55'),
(17, 4, 4, 'move_in', '2026-07-19', '2026-07-19', '2026-07-19', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-07-07 05:15:00', '2026-09-30 16:13:55'),
(18, 4, 4, 'monthly', '2026-07-19', '2026-08-18', '2026-07-24', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-07-18 16:05:00', '2026-09-30 16:13:55'),
(19, 4, 4, 'monthly', '2026-08-19', '2026-09-18', '2026-08-24', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-18 16:05:00', '2026-09-30 16:13:55'),
(20, 4, 4, 'monthly', '2026-09-19', '2026-10-18', '2026-09-24', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-18 16:05:00', '2026-09-30 16:13:55'),
(21, 5, 5, 'move_in', '2026-03-16', '2026-03-16', '2026-03-16', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-08 06:15:00', '2026-09-30 16:13:55'),
(22, 5, 5, 'monthly', '2026-03-16', '2026-04-15', '2026-03-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-15 16:05:00', '2026-09-30 16:13:55'),
(23, 5, 5, 'monthly', '2026-04-16', '2026-05-15', '2026-04-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-04-15 16:05:00', '2026-09-30 16:13:55'),
(24, 5, 5, 'monthly', '2026-05-16', '2026-06-15', '2026-05-21', 4500.00, 600.00, 300.00, 450.00, 5850.00, 'paid', '2026-05-15 16:05:00', '2026-09-30 16:13:55'),
(25, 5, 5, 'monthly', '2026-06-16', '2026-07-15', '2026-06-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-15 16:05:00', '2026-09-30 16:13:55'),
(26, 5, 5, 'monthly', '2026-07-16', '2026-08-15', '2026-07-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-15 16:05:00', '2026-09-30 16:13:55'),
(27, 5, 5, 'monthly', '2026-08-16', '2026-09-15', '2026-08-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-08-15 16:05:00', '2026-09-30 16:13:55'),
(28, 5, 5, 'monthly', '2026-09-16', '2026-10-15', '2026-09-21', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-09-15 16:05:00', '2026-09-30 16:13:55'),
(29, 6, 6, 'move_in', '2026-06-17', '2026-06-17', '2026-06-17', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-06-08 07:15:00', '2026-09-30 16:13:55'),
(30, 6, 6, 'monthly', '2026-06-17', '2026-07-16', '2026-06-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-16 16:05:00', '2026-09-30 16:13:55'),
(31, 6, 6, 'monthly', '2026-07-17', '2026-08-16', '2026-07-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-16 16:05:00', '2026-09-30 16:13:55'),
(32, 6, 6, 'monthly', '2026-08-17', '2026-09-16', '2026-08-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-08-16 16:05:00', '2026-09-30 16:13:55'),
(33, 6, 6, 'monthly', '2026-09-17', '2026-10-16', '2026-09-22', 2600.00, 400.00, 200.00, 260.00, 3460.00, 'overdue', '2026-09-16 16:05:00', '2026-09-30 16:13:55'),
(34, 7, 7, 'move_in', '2026-08-26', '2026-08-26', '2026-08-26', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-08-16 08:15:00', '2026-09-30 16:13:55'),
(35, 7, 7, 'monthly', '2026-08-26', '2026-09-25', '2026-08-31', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-25 16:05:00', '2026-09-30 16:13:55'),
(36, 7, 7, 'monthly', '2026-09-26', '2026-10-25', '2026-10-01', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-09-25 16:05:00', '2026-09-30 16:13:55'),
(37, 8, 8, 'move_in', '2026-05-28', '2026-05-28', '2026-05-28', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-05-17 01:15:00', '2026-09-30 16:13:55'),
(38, 8, 8, 'monthly', '2026-05-28', '2026-06-27', '2026-06-02', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-05-27 16:05:00', '2026-09-30 16:13:55'),
(39, 8, 8, 'monthly', '2026-06-28', '2026-07-27', '2026-07-03', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-06-27 16:05:00', '2026-09-30 16:13:55'),
(40, 8, 8, 'monthly', '2026-07-28', '2026-08-27', '2026-08-02', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-27 16:05:00', '2026-09-30 16:13:55'),
(41, 8, 8, 'monthly', '2026-08-28', '2026-09-27', '2026-09-02', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-27 16:05:00', '2026-09-30 16:13:55'),
(42, 8, 8, 'monthly', '2026-09-28', '2026-10-27', '2026-10-03', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'partial', '2026-09-27 16:05:00', '2026-09-30 16:13:55'),
(43, 9, 9, 'move_in', '2026-06-23', '2026-06-23', '2026-06-23', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-06-11 02:15:00', '2026-09-30 16:13:55'),
(44, 9, 9, 'monthly', '2026-06-23', '2026-07-22', '2026-06-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-06-22 16:05:00', '2026-09-30 16:13:55'),
(45, 9, 9, 'monthly', '2026-07-23', '2026-08-22', '2026-07-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-22 16:05:00', '2026-09-30 16:13:55'),
(46, 9, 9, 'monthly', '2026-08-23', '2026-09-22', '2026-08-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-22 16:05:00', '2026-09-30 16:13:55'),
(47, 9, 9, 'monthly', '2026-09-23', '2026-10-22', '2026-09-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'overdue', '2026-09-22 16:05:00', '2026-09-30 16:13:55'),
(48, 10, 10, 'move_in', '2026-04-19', '2026-04-19', '2026-04-19', 15600.00, 0.00, 0.00, 0.00, 15600.00, 'paid', '2026-04-11 03:15:00', '2026-09-30 16:13:55'),
(49, 10, 10, 'monthly', '2026-04-19', '2026-05-18', '2026-04-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-04-18 16:05:00', '2026-09-30 16:13:55'),
(50, 10, 10, 'monthly', '2026-05-19', '2026-06-18', '2026-05-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-05-18 16:05:00', '2026-09-30 16:13:55'),
(51, 10, 10, 'monthly', '2026-06-19', '2026-07-18', '2026-06-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-06-18 16:05:00', '2026-09-30 16:13:55'),
(52, 10, 10, 'monthly', '2026-07-19', '2026-08-18', '2026-07-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-07-18 16:05:00', '2026-09-30 16:13:55'),
(53, 10, 10, 'monthly', '2026-08-19', '2026-09-18', '2026-08-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-08-18 16:05:00', '2026-09-30 16:13:55'),
(54, 10, 10, 'monthly', '2026-09-19', '2026-10-18', '2026-09-24', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-09-18 16:05:00', '2026-09-30 16:13:55'),
(55, 11, 11, 'move_in', '2026-01-11', '2026-01-11', '2026-01-11', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-02 04:15:00', '2026-09-30 16:13:55'),
(56, 11, 11, 'monthly', '2026-01-11', '2026-02-10', '2026-01-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-01-10 16:05:00', '2026-09-30 16:13:55'),
(57, 11, 11, 'monthly', '2026-02-11', '2026-03-10', '2026-02-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-02-10 16:05:00', '2026-09-30 16:13:55'),
(58, 11, 11, 'monthly', '2026-03-11', '2026-04-10', '2026-03-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-03-10 16:05:00', '2026-09-30 16:13:55'),
(59, 11, 11, 'monthly', '2026-04-11', '2026-05-10', '2026-04-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-04-10 16:05:00', '2026-09-30 16:13:55'),
(60, 11, 11, 'monthly', '2026-05-11', '2026-06-10', '2026-05-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-05-10 16:05:00', '2026-09-30 16:13:55'),
(61, 11, 11, 'monthly', '2026-06-11', '2026-07-10', '2026-06-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-06-10 16:05:00', '2026-09-30 16:13:55'),
(62, 11, 11, 'monthly', '2026-07-11', '2026-08-10', '2026-07-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-07-10 16:05:00', '2026-09-30 16:13:55'),
(63, 11, 11, 'monthly', '2026-08-11', '2026-09-10', '2026-08-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-08-10 16:05:00', '2026-09-30 16:13:55'),
(64, 11, 11, 'monthly', '2026-09-11', '2026-10-10', '2026-09-16', 2250.00, 400.00, 200.00, 0.00, 2850.00, 'paid', '2026-09-10 16:05:00', '2026-09-30 16:13:55'),
(65, 12, 12, 'move_in', '2026-07-30', '2026-07-30', '2026-07-30', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-07-20 05:15:00', '2026-09-30 16:13:55'),
(66, 12, 12, 'monthly', '2026-07-30', '2026-08-29', '2026-08-04', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-29 16:05:00', '2026-09-30 16:13:55'),
(67, 12, 12, 'monthly', '2026-08-30', '2026-09-29', '2026-09-04', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-29 16:05:00', '2026-09-30 16:13:55'),
(68, 12, 12, 'monthly', '2026-09-30', '2026-10-29', '2026-10-05', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'unpaid', '2026-09-29 16:05:00', '2026-09-30 16:13:55'),
(69, 13, 13, 'move_in', '2026-06-20', '2026-06-20', '2026-06-20', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(70, 13, 13, 'monthly', '2026-06-20', '2026-07-19', '2026-06-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-19 16:05:00', '2026-09-30 16:13:55'),
(71, 13, 13, 'monthly', '2026-07-20', '2026-08-19', '2026-07-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-19 16:05:00', '2026-09-30 16:13:55'),
(72, 13, 13, 'monthly', '2026-08-20', '2026-09-19', '2026-08-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-19 16:05:00', '2026-09-30 16:13:55'),
(73, 13, 13, 'monthly', '2026-09-20', '2026-10-19', '2026-09-25', 2500.00, 400.00, 200.00, 250.00, 3350.00, 'overdue', '2026-09-19 16:05:00', '2026-09-30 16:13:55'),
(74, 14, 14, 'move_in', '2026-07-01', '2026-07-01', '2026-07-01', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-19 07:15:00', '2026-09-30 16:13:55'),
(75, 14, 14, 'monthly', '2026-07-01', '2026-07-31', '2026-07-06', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-30 16:05:00', '2026-09-30 16:13:55'),
(76, 14, 14, 'monthly', '2026-08-01', '2026-08-31', '2026-08-06', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-31 16:05:00', '2026-09-30 16:13:55'),
(77, 14, 14, 'monthly', '2026-09-01', '2026-09-30', '2026-09-06', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-31 16:05:00', '2026-09-30 16:13:55'),
(78, 14, 14, 'monthly', '2026-10-01', '2026-10-31', '2026-10-06', 2500.00, 400.00, 200.00, 800.00, 3900.00, 'unpaid', '2026-09-30 16:05:00', '2026-09-30 16:13:55'),
(79, 15, 15, 'move_in', '2026-06-24', '2026-06-24', '2026-06-24', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-06-16 08:15:00', '2026-09-30 16:13:55'),
(80, 15, 15, 'monthly', '2026-06-24', '2026-07-23', '2026-06-29', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-23 16:05:00', '2026-09-30 16:13:55'),
(81, 15, 15, 'monthly', '2026-07-24', '2026-08-23', '2026-07-29', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-23 16:05:00', '2026-09-30 16:13:55'),
(82, 15, 15, 'monthly', '2026-08-24', '2026-09-23', '2026-08-29', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-23 16:05:00', '2026-09-30 16:13:55'),
(83, 15, 15, 'monthly', '2026-09-24', '2026-10-23', '2026-09-29', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-09-23 16:05:00', '2026-09-30 16:13:55'),
(84, 16, 16, 'move_in', '2026-10-05', '2026-10-05', '2026-10-05', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'unpaid', '2026-09-26 01:15:00', '2026-09-30 16:13:55'),
(85, 17, 17, 'move_in', '2026-07-22', '2026-07-22', '2026-07-22', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2026-07-12 02:15:00', '2026-09-30 16:13:55'),
(86, 17, 17, 'monthly', '2026-07-22', '2026-08-21', '2026-07-27', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-07-21 16:05:00', '2026-09-30 16:13:55'),
(87, 17, 17, 'monthly', '2026-08-22', '2026-09-21', '2026-08-27', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-08-21 16:05:00', '2026-09-30 16:13:55'),
(88, 17, 17, 'monthly', '2026-09-22', '2026-10-21', '2026-09-27', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-09-21 16:05:00', '2026-09-30 16:13:55'),
(89, 18, 18, 'move_in', '2026-05-13', '2026-05-13', '2026-05-13', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-05-02 03:15:00', '2026-09-30 16:13:55'),
(90, 18, 18, 'monthly', '2026-05-13', '2026-06-12', '2026-05-18', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-12 16:05:00', '2026-09-30 16:13:55'),
(91, 18, 18, 'monthly', '2026-06-13', '2026-07-12', '2026-06-18', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-12 16:05:00', '2026-09-30 16:13:55'),
(92, 18, 18, 'monthly', '2026-07-13', '2026-08-12', '2026-07-18', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-12 16:05:00', '2026-09-30 16:13:55'),
(93, 18, 18, 'monthly', '2026-08-13', '2026-09-12', '2026-08-18', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-12 16:05:00', '2026-09-30 16:13:55'),
(94, 18, 18, 'monthly', '2026-09-13', '2026-10-12', '2026-09-18', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-12 16:05:00', '2026-09-30 16:13:55'),
(95, 19, 19, 'move_in', '2026-10-05', '2026-10-05', '2026-10-05', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'unpaid', '2026-09-23 04:15:00', '2026-09-30 16:13:55'),
(96, 20, 20, 'move_in', '2026-04-25', '2026-04-25', '2026-04-25', 16000.00, 0.00, 0.00, 0.00, 16000.00, 'paid', '2026-04-17 05:15:00', '2026-09-30 16:13:55'),
(97, 20, 20, 'monthly', '2026-04-25', '2026-05-24', '2026-04-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-04-24 16:05:00', '2026-09-30 16:13:55'),
(98, 20, 20, 'monthly', '2026-05-25', '2026-06-24', '2026-05-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-05-24 16:05:00', '2026-09-30 16:13:55'),
(99, 20, 20, 'monthly', '2026-06-25', '2026-07-24', '2026-06-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-06-24 16:05:00', '2026-09-30 16:13:55'),
(100, 20, 20, 'monthly', '2026-07-25', '2026-08-24', '2026-07-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-07-24 16:05:00', '2026-09-30 16:13:55'),
(101, 20, 20, 'monthly', '2026-08-25', '2026-09-24', '2026-08-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-08-24 16:05:00', '2026-09-30 16:13:55'),
(102, 20, 20, 'monthly', '2026-09-25', '2026-10-24', '2026-09-30', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-09-24 16:05:00', '2026-09-30 16:13:55'),
(103, 21, 21, 'move_in', '2026-06-18', '2026-06-18', '2026-06-18', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(104, 21, 21, 'monthly', '2026-06-18', '2026-07-17', '2026-06-23', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-06-17 16:05:00', '2026-09-30 16:13:55'),
(105, 21, 21, 'monthly', '2026-07-18', '2026-08-17', '2026-07-23', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-07-17 16:05:00', '2026-09-30 16:13:55'),
(106, 21, 21, 'monthly', '2026-08-18', '2026-09-17', '2026-08-23', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-17 16:05:00', '2026-09-30 16:13:55'),
(107, 21, 21, 'monthly', '2026-09-18', '2026-10-17', '2026-09-23', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-17 16:05:00', '2026-09-30 16:13:55'),
(108, 22, 22, 'move_in', '2026-08-27', '2026-08-27', '2026-08-27', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-08-17 07:15:00', '2026-09-30 16:13:55'),
(109, 22, 22, 'monthly', '2026-08-27', '2026-09-26', '2026-09-01', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-08-26 16:05:00', '2026-09-30 16:13:55'),
(110, 22, 22, 'monthly', '2026-09-27', '2026-10-26', '2026-10-02', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-09-26 16:05:00', '2026-09-30 16:13:55'),
(111, 23, 23, 'move_in', '2026-03-23', '2026-03-23', '2026-03-23', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-12 08:15:00', '2026-09-30 16:13:55'),
(112, 23, 23, 'monthly', '2026-03-23', '2026-04-22', '2026-03-28', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-22 16:05:00', '2026-09-30 16:13:55'),
(113, 23, 23, 'monthly', '2026-04-23', '2026-05-22', '2026-04-28', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-04-22 16:05:00', '2026-09-30 16:13:55'),
(114, 23, 23, 'monthly', '2026-05-23', '2026-06-22', '2026-05-28', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-05-22 16:05:00', '2026-09-30 16:13:55'),
(115, 23, 23, 'monthly', '2026-06-23', '2026-07-22', '2026-06-28', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-22 16:05:00', '2026-09-30 16:13:55'),
(116, 23, 23, 'monthly', '2026-07-23', '2026-08-22', '2026-07-28', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-22 16:05:00', '2026-09-30 16:13:55'),
(117, 24, 24, 'move_in', '2026-04-12', '2026-04-12', '2026-04-12', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-03-31 01:15:00', '2026-09-30 16:13:55'),
(118, 24, 24, 'monthly', '2026-04-12', '2026-05-11', '2026-04-17', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-11 16:05:00', '2026-09-30 16:13:55'),
(119, 24, 24, 'monthly', '2026-05-12', '2026-06-11', '2026-05-17', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-11 16:05:00', '2026-09-30 16:13:55'),
(120, 24, 24, 'monthly', '2026-06-12', '2026-07-11', '2026-06-17', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-11 16:05:00', '2026-09-30 16:13:55'),
(121, 24, 24, 'monthly', '2026-07-12', '2026-08-11', '2026-07-17', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-11 16:05:00', '2026-09-30 16:13:55'),
(122, 24, 24, 'monthly', '2026-08-12', '2026-09-11', '2026-08-17', 2600.00, 400.00, 200.00, 260.00, 3460.00, 'overdue', '2026-08-11 16:05:00', '2026-09-30 16:13:55'),
(123, 25, 25, 'move_in', '2026-02-15', '2026-02-15', '2026-02-15', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-02-07 02:15:00', '2026-09-30 16:13:56'),
(124, 25, 25, 'monthly', '2026-02-15', '2026-03-14', '2026-02-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-02-14 16:05:00', '2026-09-30 16:13:56'),
(125, 25, 25, 'monthly', '2026-03-15', '2026-04-14', '2026-03-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-03-14 16:05:00', '2026-09-30 16:13:56'),
(126, 25, 25, 'monthly', '2026-04-15', '2026-05-14', '2026-04-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-14 16:05:00', '2026-09-30 16:13:56'),
(127, 25, 25, 'monthly', '2026-05-15', '2026-06-14', '2026-05-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-14 16:05:00', '2026-09-30 16:13:56'),
(128, 25, 25, 'monthly', '2026-06-15', '2026-07-14', '2026-06-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-14 16:05:00', '2026-09-30 16:13:56'),
(129, 25, 25, 'monthly', '2026-07-15', '2026-08-14', '2026-07-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-14 16:05:00', '2026-09-30 16:13:56'),
(130, 25, 25, 'monthly', '2026-08-15', '2026-09-14', '2026-08-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-14 16:05:00', '2026-09-30 16:13:56'),
(131, 25, 25, 'monthly', '2026-09-15', '2026-10-14', '2026-09-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-14 16:05:00', '2026-09-30 16:13:56'),
(132, 26, 26, 'move_in', '2026-06-20', '2026-06-20', '2026-06-20', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-06-11 03:15:00', '2026-09-30 16:13:56'),
(133, 26, 26, 'monthly', '2026-06-20', '2026-07-19', '2026-06-25', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-19 16:05:00', '2026-09-30 16:13:56'),
(134, 26, 26, 'monthly', '2026-07-20', '2026-08-19', '2026-07-25', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-19 16:05:00', '2026-09-30 16:13:56'),
(135, 26, 26, 'monthly', '2026-08-20', '2026-09-19', '2026-08-25', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-08-19 16:05:00', '2026-09-30 16:13:56'),
(136, 26, 26, 'monthly', '2026-09-20', '2026-10-19', '2026-09-25', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-09-19 16:05:00', '2026-09-30 16:13:56'),
(137, 27, 27, 'move_in', '2026-07-22', '2026-07-22', '2026-07-22', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-07-12 04:15:00', '2026-09-30 16:13:56'),
(138, 27, 27, 'monthly', '2026-07-22', '2026-08-21', '2026-07-27', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-21 16:05:00', '2026-09-30 16:13:56'),
(139, 27, 27, 'monthly', '2026-08-22', '2026-09-21', '2026-08-27', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-08-21 16:05:00', '2026-09-30 16:13:56'),
(140, 27, 27, 'monthly', '2026-09-22', '2026-10-21', '2026-09-27', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-09-21 16:05:00', '2026-09-30 16:13:56'),
(141, 28, 28, 'move_in', '2026-09-10', '2026-09-10', '2026-09-10', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-08-30 05:15:00', '2026-09-30 16:13:56'),
(142, 28, 28, 'monthly', '2026-09-10', '2026-10-09', '2026-09-15', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'overdue', '2026-09-09 16:05:00', '2026-09-30 16:14:07'),
(143, 29, 29, 'move_in', '2026-09-17', '2026-09-17', '2026-09-17', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-09-05 06:15:00', '2026-09-30 16:13:56'),
(144, 29, 29, 'monthly', '2026-09-17', '2026-10-16', '2026-09-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-09-16 16:05:00', '2026-09-30 16:13:56'),
(145, 30, 30, 'move_in', '2026-09-29', '2026-09-29', '2026-09-29', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-09-21 07:15:00', '2026-09-30 16:13:56'),
(146, 30, 30, 'monthly', '2026-09-29', '2026-10-28', '2026-10-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'unpaid', '2026-09-28 16:05:00', '2026-09-30 16:13:56'),
(147, 31, 31, 'move_in', '2026-05-07', '2026-05-07', '2026-05-07', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-04-28 08:15:00', '2026-09-30 16:13:56'),
(148, 31, 31, 'monthly', '2026-05-07', '2026-06-06', '2026-05-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-06 16:05:00', '2026-09-30 16:13:56'),
(149, 31, 31, 'monthly', '2026-06-07', '2026-07-06', '2026-06-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-06 16:05:00', '2026-09-30 16:13:56'),
(150, 31, 31, 'monthly', '2026-07-07', '2026-08-06', '2026-07-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-06 16:05:00', '2026-09-30 16:13:56'),
(151, 31, 31, 'monthly', '2026-08-07', '2026-09-06', '2026-08-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-08-06 16:05:00', '2026-09-30 16:13:56'),
(152, 31, 31, 'monthly', '2026-09-07', '2026-10-06', '2026-09-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-09-06 16:05:00', '2026-09-30 16:13:56'),
(153, 32, 32, 'move_in', '2025-09-01', '2025-09-01', '2025-09-01', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-08-19 02:30:00', '2025-08-31 16:00:00'),
(154, 32, 32, 'monthly', '2025-09-01', '2025-09-30', '2025-09-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-08-31 16:05:00', '2025-09-05 16:00:00'),
(155, 32, 32, 'monthly', '2025-10-01', '2025-10-31', '2025-10-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-30 16:05:00', '2025-10-05 16:00:00'),
(156, 32, 32, 'monthly', '2025-11-01', '2025-11-30', '2025-11-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-31 16:05:00', '2025-11-05 16:00:00'),
(157, 32, 32, 'monthly', '2025-12-01', '2025-12-31', '2025-12-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-30 16:05:00', '2025-12-05 16:00:00'),
(158, 32, 32, 'monthly', '2026-01-01', '2026-01-31', '2026-01-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-31 16:05:00', '2026-01-05 16:00:00'),
(159, 32, 32, 'monthly', '2026-02-01', '2026-02-28', '2026-02-06', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-01-31 16:05:00', '2026-02-05 16:00:00'),
(160, 33, 33, 'move_in', '2025-09-07', '2025-09-07', '2025-09-07', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-08-24 03:30:00', '2025-09-06 16:00:00'),
(161, 33, 33, 'monthly', '2025-09-07', '2025-10-06', '2025-09-12', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-06 16:05:00', '2025-09-11 16:00:00'),
(162, 33, 33, 'monthly', '2025-10-07', '2025-11-06', '2025-10-12', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-06 16:05:00', '2025-10-11 16:00:00'),
(163, 33, 33, 'monthly', '2025-11-07', '2025-12-06', '2025-11-12', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-06 16:05:00', '2025-11-11 16:00:00'),
(164, 33, 33, 'monthly', '2025-12-07', '2026-01-06', '2025-12-12', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-06 16:05:00', '2025-12-11 16:00:00'),
(165, 33, 33, 'monthly', '2026-01-07', '2026-02-06', '2026-01-12', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-01-06 16:05:00', '2026-01-11 16:00:00'),
(166, 34, 34, 'move_in', '2026-03-19', '2026-03-19', '2026-03-19', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-03-11 04:30:00', '2026-03-18 16:00:00'),
(167, 34, 34, 'monthly', '2026-03-19', '2026-04-18', '2026-03-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-03-18 16:05:00', '2026-03-23 16:00:00'),
(168, 34, 34, 'monthly', '2026-04-19', '2026-05-18', '2026-04-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-18 16:05:00', '2026-04-23 16:00:00'),
(169, 34, 34, 'monthly', '2026-05-19', '2026-06-18', '2026-05-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-18 16:05:00', '2026-05-23 16:00:00'),
(170, 34, 34, 'monthly', '2026-06-19', '2026-07-18', '2026-06-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-18 16:05:00', '2026-06-23 16:00:00'),
(171, 35, 35, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-09-16 05:30:00', '2025-09-24 16:00:00'),
(172, 35, 35, 'monthly', '2025-09-25', '2025-10-24', '2025-09-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-24 16:05:00', '2025-09-29 16:00:00'),
(173, 35, 35, 'monthly', '2025-10-25', '2025-11-24', '2025-10-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-24 16:05:00', '2025-10-29 16:00:00'),
(174, 35, 35, 'monthly', '2025-11-25', '2025-12-24', '2025-11-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-24 16:05:00', '2025-11-29 16:00:00'),
(175, 35, 35, 'monthly', '2025-12-25', '2026-01-24', '2025-12-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-24 16:05:00', '2025-12-29 16:00:00'),
(176, 35, 35, 'monthly', '2026-01-25', '2026-02-24', '2026-01-30', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-01-24 16:05:00', '2026-01-29 16:00:00'),
(177, 36, 36, 'move_in', '2026-04-18', '2026-04-18', '2026-04-18', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-04-08 06:30:00', '2026-04-17 16:00:00'),
(178, 36, 36, 'monthly', '2026-04-18', '2026-05-17', '2026-04-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-17 16:05:00', '2026-04-22 16:00:00'),
(179, 36, 36, 'monthly', '2026-05-18', '2026-06-17', '2026-05-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-17 16:05:00', '2026-05-22 16:00:00'),
(180, 36, 36, 'monthly', '2026-06-18', '2026-07-17', '2026-06-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-17 16:05:00', '2026-06-22 16:00:00'),
(181, 36, 36, 'monthly', '2026-07-18', '2026-08-17', '2026-07-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-17 16:05:00', '2026-07-22 16:00:00'),
(182, 37, 37, 'move_in', '2025-09-24', '2025-09-24', '2025-09-24', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-09-13 07:30:00', '2025-09-23 16:00:00'),
(183, 37, 37, 'monthly', '2025-09-24', '2025-10-23', '2025-09-29', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-23 16:05:00', '2025-09-28 16:00:00'),
(184, 37, 37, 'monthly', '2025-10-24', '2025-11-23', '2025-10-29', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-23 16:05:00', '2025-10-28 16:00:00'),
(185, 37, 37, 'monthly', '2025-11-24', '2025-12-23', '2025-11-29', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-23 16:05:00', '2025-11-28 16:00:00'),
(186, 37, 37, 'monthly', '2025-12-24', '2026-01-23', '2025-12-29', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-23 16:05:00', '2025-12-28 16:00:00'),
(187, 38, 38, 'move_in', '2025-09-24', '2025-09-24', '2025-09-24', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-12 08:30:00', '2025-09-23 16:00:00'),
(188, 38, 38, 'monthly', '2025-09-24', '2025-10-23', '2025-09-29', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-09-23 16:05:00', '2025-09-28 16:00:00'),
(189, 38, 38, 'monthly', '2025-10-24', '2025-11-23', '2025-10-29', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-10-23 16:05:00', '2025-10-28 16:00:00'),
(190, 38, 38, 'monthly', '2025-11-24', '2025-12-23', '2025-11-29', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-11-23 16:05:00', '2025-11-28 16:00:00'),
(191, 38, 38, 'monthly', '2025-12-24', '2026-01-23', '2025-12-29', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-12-23 16:05:00', '2025-12-28 16:00:00'),
(192, 38, 38, 'monthly', '2026-01-24', '2026-02-23', '2026-01-29', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-01-23 16:05:00', '2026-01-28 16:00:00'),
(193, 38, 38, 'monthly', '2026-02-24', '2026-03-23', '2026-03-01', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-02-23 16:05:00', '2026-02-28 16:00:00'),
(194, 39, 39, 'move_in', '2025-09-13', '2025-09-13', '2025-09-13', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-08-31 01:30:00', '2025-09-12 16:00:00'),
(195, 39, 39, 'monthly', '2025-09-13', '2025-10-12', '2025-09-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-09-12 16:05:00', '2025-09-17 16:00:00'),
(196, 39, 39, 'monthly', '2025-10-13', '2025-11-12', '2025-10-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-10-12 16:05:00', '2025-10-17 16:00:00'),
(197, 39, 39, 'monthly', '2025-11-13', '2025-12-12', '2025-11-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-11-12 16:05:00', '2025-11-17 16:00:00'),
(198, 39, 39, 'monthly', '2025-12-13', '2026-01-12', '2025-12-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-12-12 16:05:00', '2025-12-17 16:00:00'),
(199, 39, 39, 'monthly', '2026-01-13', '2026-02-12', '2026-01-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-01-12 16:05:00', '2026-01-17 16:00:00'),
(200, 39, 39, 'monthly', '2026-02-13', '2026-03-12', '2026-02-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-02-12 16:05:00', '2026-02-17 16:00:00'),
(201, 39, 39, 'monthly', '2026-03-13', '2026-04-12', '2026-03-18', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-12 16:05:00', '2026-03-17 16:00:00'),
(202, 40, 40, 'move_in', '2025-09-10', '2025-09-10', '2025-09-10', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2025-08-27 02:30:00', '2025-09-09 16:00:00'),
(203, 40, 40, 'monthly', '2025-09-10', '2025-10-09', '2025-09-15', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-09-09 16:05:00', '2025-09-14 16:00:00'),
(204, 40, 40, 'monthly', '2025-10-10', '2025-11-09', '2025-10-15', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-10-09 16:05:00', '2025-10-14 16:00:00'),
(205, 40, 40, 'monthly', '2025-11-10', '2025-12-09', '2025-11-15', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-11-09 16:05:00', '2025-11-14 16:00:00'),
(206, 40, 40, 'monthly', '2025-12-10', '2026-01-09', '2025-12-15', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-12-09 16:05:00', '2025-12-14 16:00:00'),
(207, 40, 40, 'monthly', '2026-01-10', '2026-02-09', '2026-01-15', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-01-09 16:05:00', '2026-01-14 16:00:00'),
(208, 41, 41, 'move_in', '2026-03-25', '2026-03-25', '2026-03-25', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2026-03-17 03:30:00', '2026-03-24 16:00:00'),
(209, 41, 41, 'monthly', '2026-03-25', '2026-04-24', '2026-03-30', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-03-24 16:05:00', '2026-03-29 16:00:00'),
(210, 41, 41, 'monthly', '2026-04-25', '2026-05-24', '2026-04-30', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-04-24 16:05:00', '2026-04-29 16:00:00'),
(211, 41, 41, 'monthly', '2026-05-25', '2026-06-24', '2026-05-30', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-05-24 16:05:00', '2026-05-29 16:00:00'),
(212, 41, 41, 'monthly', '2026-06-25', '2026-07-24', '2026-06-30', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-06-24 16:05:00', '2026-06-29 16:00:00'),
(213, 41, 41, 'monthly', '2026-07-25', '2026-08-24', '2026-07-30', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-07-24 16:05:00', '2026-07-29 16:00:00'),
(214, 42, 42, 'move_in', '2025-09-23', '2025-09-23', '2025-09-23', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2025-09-14 04:30:00', '2025-09-22 16:00:00'),
(215, 42, 42, 'monthly', '2025-09-23', '2025-10-22', '2025-09-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-09-22 16:05:00', '2025-09-27 16:00:00'),
(216, 42, 42, 'monthly', '2025-10-23', '2025-11-22', '2025-10-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-10-22 16:05:00', '2025-10-27 16:00:00'),
(217, 42, 42, 'monthly', '2025-11-23', '2025-12-22', '2025-11-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-11-22 16:05:00', '2025-11-27 16:00:00'),
(218, 42, 42, 'monthly', '2025-12-23', '2026-01-22', '2025-12-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-12-22 16:05:00', '2025-12-27 16:00:00'),
(219, 42, 42, 'monthly', '2026-01-23', '2026-02-22', '2026-01-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-01-22 16:05:00', '2026-01-27 16:00:00'),
(220, 42, 42, 'monthly', '2026-02-23', '2026-03-22', '2026-02-28', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-02-22 16:05:00', '2026-02-27 16:00:00'),
(221, 43, 43, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2025-09-11 05:30:00', '2025-09-20 16:00:00'),
(222, 43, 43, 'monthly', '2025-09-21', '2025-10-20', '2025-09-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-09-20 16:05:00', '2025-09-25 16:00:00'),
(223, 43, 43, 'monthly', '2025-10-21', '2025-11-20', '2025-10-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-10-20 16:05:00', '2025-10-25 16:00:00'),
(224, 43, 43, 'monthly', '2025-11-21', '2025-12-20', '2025-11-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-11-20 16:05:00', '2025-11-25 16:00:00'),
(225, 43, 43, 'monthly', '2025-12-21', '2026-01-20', '2025-12-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-12-20 16:05:00', '2025-12-25 16:00:00'),
(226, 43, 43, 'monthly', '2026-01-21', '2026-02-20', '2026-01-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-01-20 16:05:00', '2026-01-25 16:00:00'),
(227, 43, 43, 'monthly', '2026-02-21', '2026-03-20', '2026-02-26', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-02-20 16:05:00', '2026-02-25 16:00:00'),
(228, 44, 44, 'move_in', '2025-10-12', '2025-10-12', '2025-10-12', 6500.00, 0.00, 0.00, 0.00, 6500.00, 'paid', '2025-10-01 06:30:00', '2025-10-11 16:00:00'),
(229, 44, 44, 'monthly', '2025-10-12', '2025-11-11', '2025-10-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-10-11 16:05:00', '2025-10-16 16:00:00'),
(230, 44, 44, 'monthly', '2025-11-12', '2025-12-11', '2025-11-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-11-11 16:05:00', '2025-11-16 16:00:00'),
(231, 44, 44, 'monthly', '2025-12-12', '2026-01-11', '2025-12-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2025-12-11 16:05:00', '2025-12-16 16:00:00'),
(232, 44, 44, 'monthly', '2026-01-12', '2026-02-11', '2026-01-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-01-11 16:05:00', '2026-01-16 16:00:00'),
(233, 44, 44, 'monthly', '2026-02-12', '2026-03-11', '2026-02-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-02-11 16:05:00', '2026-02-16 16:00:00'),
(234, 44, 44, 'monthly', '2026-03-12', '2026-04-11', '2026-03-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-03-11 16:05:00', '2026-03-16 16:00:00'),
(235, 44, 44, 'monthly', '2026-04-12', '2026-05-11', '2026-04-17', 3250.00, 500.00, 250.00, 0.00, 4000.00, 'paid', '2026-04-11 16:05:00', '2026-04-16 16:00:00'),
(236, 45, 45, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-09-09 07:30:00', '2025-09-20 16:00:00'),
(237, 45, 45, 'monthly', '2025-09-21', '2025-10-20', '2025-09-26', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-09-20 16:05:00', '2025-09-25 16:00:00'),
(238, 45, 45, 'monthly', '2025-10-21', '2025-11-20', '2025-10-26', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-20 16:05:00', '2025-10-25 16:00:00'),
(239, 45, 45, 'monthly', '2025-11-21', '2025-12-20', '2025-11-26', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-20 16:05:00', '2025-11-25 16:00:00'),
(240, 46, 46, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-09-12 08:30:00', '2025-09-24 16:00:00'),
(241, 46, 46, 'monthly', '2025-09-25', '2025-10-24', '2025-09-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-09-24 16:05:00', '2025-09-29 16:00:00'),
(242, 46, 46, 'monthly', '2025-10-25', '2025-11-24', '2025-10-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-24 16:05:00', '2025-10-29 16:00:00'),
(243, 46, 46, 'monthly', '2025-11-25', '2025-12-24', '2025-11-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-24 16:05:00', '2025-11-29 16:00:00'),
(244, 46, 46, 'monthly', '2025-12-25', '2026-01-24', '2025-12-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-12-24 16:05:00', '2025-12-29 16:00:00'),
(245, 46, 46, 'monthly', '2026-01-25', '2026-02-24', '2026-01-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-01-24 16:05:00', '2026-01-29 16:00:00'),
(246, 46, 46, 'monthly', '2026-02-25', '2026-03-24', '2026-03-02', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-02-24 16:05:00', '2026-03-01 16:00:00'),
(247, 47, 47, 'move_in', '2026-04-29', '2026-04-29', '2026-04-29', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-04-15 01:30:00', '2026-04-28 16:00:00'),
(248, 47, 47, 'monthly', '2026-04-29', '2026-05-28', '2026-05-04', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-04-28 16:05:00', '2026-05-03 16:00:00'),
(249, 47, 47, 'monthly', '2026-05-29', '2026-06-28', '2026-06-03', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-05-28 16:05:00', '2026-06-02 16:00:00'),
(250, 47, 47, 'monthly', '2026-06-29', '2026-07-28', '2026-07-04', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-28 16:05:00', '2026-07-03 16:00:00'),
(251, 48, 48, 'move_in', '2025-10-13', '2025-10-13', '2025-10-13', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-10-05 02:30:00', '2025-10-12 16:00:00'),
(252, 48, 48, 'monthly', '2025-10-13', '2025-11-12', '2025-10-18', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-12 16:05:00', '2025-10-17 16:00:00'),
(253, 48, 48, 'monthly', '2025-11-13', '2025-12-12', '2025-11-18', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-12 16:05:00', '2025-11-17 16:00:00'),
(254, 48, 48, 'monthly', '2025-12-13', '2026-01-12', '2025-12-18', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-12-12 16:05:00', '2025-12-17 16:00:00'),
(255, 48, 48, 'monthly', '2026-01-13', '2026-02-12', '2026-01-18', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-01-12 16:05:00', '2026-01-17 16:00:00'),
(256, 48, 48, 'monthly', '2026-02-13', '2026-03-12', '2026-02-18', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-02-12 16:05:00', '2026-02-17 16:00:00'),
(257, 49, 49, 'move_in', '2025-09-20', '2025-09-20', '2025-09-20', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-09-11 03:30:00', '2025-09-19 16:00:00'),
(258, 49, 49, 'monthly', '2025-09-20', '2025-10-19', '2025-09-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-09-19 16:05:00', '2025-09-24 16:00:00'),
(259, 49, 49, 'monthly', '2025-10-20', '2025-11-19', '2025-10-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-19 16:05:00', '2025-10-24 16:00:00'),
(260, 49, 49, 'monthly', '2025-11-20', '2025-12-19', '2025-11-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-19 16:05:00', '2025-11-24 16:00:00'),
(261, 49, 49, 'monthly', '2025-12-20', '2026-01-19', '2025-12-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-12-19 16:05:00', '2025-12-24 16:00:00'),
(262, 49, 49, 'monthly', '2026-01-20', '2026-02-19', '2026-01-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-01-19 16:05:00', '2026-01-24 16:00:00'),
(263, 49, 49, 'monthly', '2026-02-20', '2026-03-19', '2026-02-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-02-19 16:05:00', '2026-02-24 16:00:00'),
(264, 49, 49, 'monthly', '2026-03-20', '2026-04-19', '2026-03-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-03-19 16:05:00', '2026-03-24 16:00:00'),
(265, 49, 49, 'monthly', '2026-04-20', '2026-05-19', '2026-04-25', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-04-19 16:05:00', '2026-04-24 16:00:00'),
(266, 50, 50, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-09-15 04:30:00', '2025-09-24 16:00:00'),
(267, 50, 50, 'monthly', '2025-09-25', '2025-10-24', '2025-09-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-09-24 16:05:00', '2025-09-29 16:00:00'),
(268, 50, 50, 'monthly', '2025-10-25', '2025-11-24', '2025-10-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-24 16:05:00', '2025-10-29 16:00:00'),
(269, 50, 50, 'monthly', '2025-11-25', '2025-12-24', '2025-11-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-24 16:05:00', '2025-11-29 16:00:00'),
(270, 51, 51, 'move_in', '2026-01-25', '2026-01-25', '2026-01-25', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-01-14 05:30:00', '2026-01-24 16:00:00'),
(271, 51, 51, 'monthly', '2026-01-25', '2026-02-24', '2026-01-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-01-24 16:05:00', '2026-01-29 16:00:00'),
(272, 51, 51, 'monthly', '2026-02-25', '2026-03-24', '2026-03-02', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-02-24 16:05:00', '2026-03-01 16:00:00'),
(273, 51, 51, 'monthly', '2026-03-25', '2026-04-24', '2026-03-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-03-24 16:05:00', '2026-03-29 16:00:00'),
(274, 51, 51, 'monthly', '2026-04-25', '2026-05-24', '2026-04-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-04-24 16:05:00', '2026-04-29 16:00:00'),
(275, 51, 51, 'monthly', '2026-05-25', '2026-06-24', '2026-05-30', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-05-24 16:05:00', '2026-05-29 16:00:00'),
(276, 52, 52, 'move_in', '2025-09-17', '2025-09-17', '2025-09-17', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2025-09-05 06:30:00', '2025-09-16 16:00:00'),
(277, 52, 52, 'monthly', '2025-09-17', '2025-10-16', '2025-09-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-09-16 16:05:00', '2025-09-21 16:00:00'),
(278, 52, 52, 'monthly', '2025-10-17', '2025-11-16', '2025-10-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-10-16 16:05:00', '2025-10-21 16:00:00'),
(279, 52, 52, 'monthly', '2025-11-17', '2025-12-16', '2025-11-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-11-16 16:05:00', '2025-11-21 16:00:00'),
(280, 52, 52, 'monthly', '2025-12-17', '2026-01-16', '2025-12-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2025-12-16 16:05:00', '2025-12-21 16:00:00'),
(281, 52, 52, 'monthly', '2026-01-17', '2026-02-16', '2026-01-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-01-16 16:05:00', '2026-01-21 16:00:00'),
(282, 52, 52, 'monthly', '2026-02-17', '2026-03-16', '2026-02-22', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-02-16 16:05:00', '2026-02-21 16:00:00'),
(283, 53, 53, 'move_in', '2026-04-02', '2026-04-02', '2026-04-02', 5000.00, 0.00, 0.00, 0.00, 5000.00, 'paid', '2026-03-20 07:30:00', '2026-04-01 16:00:00'),
(284, 53, 53, 'monthly', '2026-04-02', '2026-05-01', '2026-04-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-04-01 16:05:00', '2026-04-06 16:00:00'),
(285, 53, 53, 'monthly', '2026-05-02', '2026-06-01', '2026-05-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-05-01 16:05:00', '2026-05-06 16:00:00'),
(286, 53, 53, 'monthly', '2026-06-02', '2026-07-01', '2026-06-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-06-01 16:05:00', '2026-06-06 16:00:00'),
(287, 53, 53, 'monthly', '2026-07-02', '2026-08-01', '2026-07-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-07-01 16:05:00', '2026-07-06 16:00:00'),
(288, 53, 53, 'monthly', '2026-08-02', '2026-09-01', '2026-08-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-08-01 16:05:00', '2026-08-06 16:00:00'),
(289, 53, 53, 'monthly', '2026-09-02', '2026-10-01', '2026-09-07', 2500.00, 400.00, 200.00, 0.00, 3100.00, 'paid', '2026-09-01 16:05:00', '2026-09-06 16:00:00'),
(290, 54, 54, 'move_in', '2025-09-18', '2025-09-18', '2025-09-18', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2025-09-04 08:30:00', '2025-09-17 16:00:00'),
(291, 54, 54, 'monthly', '2025-09-18', '2025-10-17', '2025-09-23', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-09-17 16:05:00', '2025-09-22 16:00:00'),
(292, 54, 54, 'monthly', '2025-10-18', '2025-11-17', '2025-10-23', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-10-17 16:05:00', '2025-10-22 16:00:00'),
(293, 54, 54, 'monthly', '2025-11-18', '2025-12-17', '2025-11-23', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-11-17 16:05:00', '2025-11-22 16:00:00'),
(294, 54, 54, 'monthly', '2025-12-18', '2026-01-17', '2025-12-23', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-12-17 16:05:00', '2025-12-22 16:00:00'),
(295, 55, 55, 'move_in', '2026-03-04', '2026-03-04', '2026-03-04', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2026-02-24 01:30:00', '2026-03-03 16:00:00'),
(296, 55, 55, 'monthly', '2026-03-04', '2026-04-03', '2026-03-09', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-03-03 16:05:00', '2026-03-08 16:00:00'),
(297, 55, 55, 'monthly', '2026-04-04', '2026-05-03', '2026-04-09', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-04-03 16:05:00', '2026-04-08 16:00:00'),
(298, 55, 55, 'monthly', '2026-05-04', '2026-06-03', '2026-05-09', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-05-03 16:05:00', '2026-05-08 16:00:00'),
(299, 55, 55, 'monthly', '2026-06-04', '2026-07-03', '2026-06-09', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-06-03 16:05:00', '2026-06-08 16:00:00'),
(300, 55, 55, 'monthly', '2026-07-04', '2026-08-03', '2026-07-09', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-07-03 16:05:00', '2026-07-08 16:00:00'),
(301, 56, 56, 'move_in', '2025-10-14', '2025-10-14', '2025-10-14', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2025-10-05 02:30:00', '2025-10-13 16:00:00'),
(302, 56, 56, 'monthly', '2025-10-14', '2025-11-13', '2025-10-19', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-10-13 16:05:00', '2025-10-18 16:00:00'),
(303, 56, 56, 'monthly', '2025-11-14', '2025-12-13', '2025-11-19', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-11-13 16:05:00', '2025-11-18 16:00:00'),
(304, 56, 56, 'monthly', '2025-12-14', '2026-01-13', '2025-12-19', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2025-12-13 16:05:00', '2025-12-18 16:00:00'),
(305, 57, 57, 'move_in', '2026-02-01', '2026-02-01', '2026-02-01', 9500.00, 0.00, 0.00, 0.00, 9500.00, 'paid', '2026-01-22 03:30:00', '2026-01-31 16:00:00'),
(306, 57, 57, 'monthly', '2026-02-01', '2026-02-28', '2026-02-06', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-01-31 16:05:00', '2026-02-05 16:00:00'),
(307, 57, 57, 'monthly', '2026-03-01', '2026-03-31', '2026-03-06', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-02-28 16:05:00', '2026-03-05 16:00:00'),
(308, 57, 57, 'monthly', '2026-04-01', '2026-04-30', '2026-04-06', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-03-31 16:05:00', '2026-04-05 16:00:00'),
(309, 57, 57, 'monthly', '2026-05-01', '2026-05-31', '2026-05-06', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-04-30 16:05:00', '2026-05-05 16:00:00'),
(310, 57, 57, 'monthly', '2026-06-01', '2026-06-30', '2026-06-06', 4750.00, 600.00, 300.00, 0.00, 5650.00, 'paid', '2026-05-31 16:05:00', '2026-06-05 16:00:00'),
(311, 58, 58, 'move_in', '2025-09-19', '2025-09-19', '2025-09-19', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-09-08 04:30:00', '2025-09-18 16:00:00'),
(312, 58, 58, 'monthly', '2025-09-19', '2025-10-18', '2025-09-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-18 16:05:00', '2025-09-23 16:00:00'),
(313, 58, 58, 'monthly', '2025-10-19', '2025-11-18', '2025-10-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-18 16:05:00', '2025-10-23 16:00:00');
INSERT INTO `billing_statements` (`id`, `contract_id`, `tenant_id`, `type`, `billing_period_start`, `billing_period_end`, `due_date`, `base_rent`, `utilities_amount`, `wifi_amount`, `penalty_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(314, 58, 58, 'monthly', '2025-11-19', '2025-12-18', '2025-11-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-18 16:05:00', '2025-11-23 16:00:00'),
(315, 58, 58, 'monthly', '2025-12-19', '2026-01-18', '2025-12-24', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-18 16:05:00', '2025-12-23 16:00:00'),
(316, 59, 59, 'move_in', '2026-02-16', '2026-02-16', '2026-02-16', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-02-04 05:30:00', '2026-02-15 16:00:00'),
(317, 59, 59, 'monthly', '2026-02-16', '2026-03-15', '2026-02-21', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-02-15 16:05:00', '2026-02-20 16:00:00'),
(318, 59, 59, 'monthly', '2026-03-16', '2026-04-15', '2026-03-21', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-03-15 16:05:00', '2026-03-20 16:00:00'),
(319, 59, 59, 'monthly', '2026-04-16', '2026-05-15', '2026-04-21', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-15 16:05:00', '2026-04-20 16:00:00'),
(320, 60, 60, 'move_in', '2025-10-02', '2025-10-02', '2025-10-02', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-09-19 06:30:00', '2025-10-01 16:00:00'),
(321, 60, 60, 'monthly', '2025-10-02', '2025-11-01', '2025-10-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-01 16:05:00', '2025-10-06 16:00:00'),
(322, 60, 60, 'monthly', '2025-11-02', '2025-12-01', '2025-11-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-01 16:05:00', '2025-11-06 16:00:00'),
(323, 60, 60, 'monthly', '2025-12-02', '2026-01-01', '2025-12-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-01 16:05:00', '2025-12-06 16:00:00'),
(324, 60, 60, 'monthly', '2026-01-02', '2026-02-01', '2026-01-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-01-01 16:05:00', '2026-01-06 16:00:00'),
(325, 60, 60, 'monthly', '2026-02-02', '2026-03-01', '2026-02-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-02-01 16:05:00', '2026-02-06 16:00:00'),
(326, 60, 60, 'monthly', '2026-03-02', '2026-04-01', '2026-03-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-03-01 16:05:00', '2026-03-06 16:00:00'),
(327, 60, 60, 'monthly', '2026-04-02', '2026-05-01', '2026-04-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-04-01 16:05:00', '2026-04-06 16:00:00'),
(328, 60, 60, 'monthly', '2026-05-02', '2026-06-01', '2026-05-07', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-05-01 16:05:00', '2026-05-06 16:00:00'),
(329, 61, 61, 'move_in', '2026-06-18', '2026-06-18', '2026-06-18', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2026-06-04 07:30:00', '2026-06-17 16:00:00'),
(330, 61, 61, 'monthly', '2026-06-18', '2026-07-17', '2026-06-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-06-17 16:05:00', '2026-06-22 16:00:00'),
(331, 61, 61, 'monthly', '2026-07-18', '2026-08-17', '2026-07-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-07-17 16:05:00', '2026-07-22 16:00:00'),
(332, 61, 61, 'monthly', '2026-08-18', '2026-09-17', '2026-08-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-08-17 16:05:00', '2026-08-22 16:00:00'),
(333, 61, 61, 'monthly', '2026-09-18', '2026-10-17', '2026-09-23', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-09-17 16:05:00', '2026-09-22 16:00:00'),
(334, 62, 62, 'move_in', '2025-09-15', '2025-09-15', '2025-09-15', 7000.00, 0.00, 0.00, 0.00, 7000.00, 'paid', '2025-09-07 08:30:00', '2025-09-14 16:00:00'),
(335, 62, 62, 'monthly', '2025-09-15', '2025-10-14', '2025-09-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-09-14 16:05:00', '2025-09-19 16:00:00'),
(336, 62, 62, 'monthly', '2025-10-15', '2025-11-14', '2025-10-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-10-14 16:05:00', '2025-10-19 16:00:00'),
(337, 62, 62, 'monthly', '2025-11-15', '2025-12-14', '2025-11-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-11-14 16:05:00', '2025-11-19 16:00:00'),
(338, 62, 62, 'monthly', '2025-12-15', '2026-01-14', '2025-12-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2025-12-14 16:05:00', '2025-12-19 16:00:00'),
(339, 62, 62, 'monthly', '2026-01-15', '2026-02-14', '2026-01-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-01-14 16:05:00', '2026-01-19 16:00:00'),
(340, 62, 62, 'monthly', '2026-02-15', '2026-03-14', '2026-02-20', 3500.00, 500.00, 250.00, 0.00, 4250.00, 'paid', '2026-02-14 16:05:00', '2026-02-19 16:00:00'),
(341, 63, 63, 'move_in', '2025-09-04', '2025-09-04', '2025-09-04', 16000.00, 0.00, 0.00, 0.00, 16000.00, 'paid', '2025-08-26 01:30:00', '2025-09-03 16:00:00'),
(342, 63, 63, 'monthly', '2025-09-04', '2025-10-03', '2025-09-09', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2025-09-03 16:05:00', '2025-09-08 16:00:00'),
(343, 63, 63, 'monthly', '2025-10-04', '2025-11-03', '2025-10-09', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2025-10-03 16:05:00', '2025-10-08 16:00:00'),
(344, 63, 63, 'monthly', '2025-11-04', '2025-12-03', '2025-11-09', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2025-11-03 16:05:00', '2025-11-08 16:00:00'),
(345, 64, 64, 'move_in', '2026-01-08', '2026-01-08', '2026-01-08', 16000.00, 0.00, 0.00, 0.00, 16000.00, 'paid', '2025-12-29 02:30:00', '2026-01-07 16:00:00'),
(346, 64, 64, 'monthly', '2026-01-08', '2026-02-07', '2026-01-13', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-01-07 16:05:00', '2026-01-12 16:00:00'),
(347, 64, 64, 'monthly', '2026-02-08', '2026-03-07', '2026-02-13', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-02-07 16:05:00', '2026-02-12 16:00:00'),
(348, 64, 64, 'monthly', '2026-03-08', '2026-04-07', '2026-03-13', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-03-07 16:05:00', '2026-03-12 16:00:00'),
(349, 64, 64, 'monthly', '2026-04-08', '2026-05-07', '2026-04-13', 8000.00, 800.00, 500.00, 0.00, 9300.00, 'paid', '2026-04-07 16:05:00', '2026-04-12 16:00:00'),
(350, 65, 65, 'move_in', '2025-10-06', '2025-10-06', '2025-10-06', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2025-09-25 03:30:00', '2025-10-05 16:00:00'),
(351, 65, 65, 'monthly', '2025-10-06', '2025-11-05', '2025-10-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-10-05 16:05:00', '2025-10-10 16:00:00'),
(352, 65, 65, 'monthly', '2025-11-06', '2025-12-05', '2025-11-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-11-05 16:05:00', '2025-11-10 16:00:00'),
(353, 65, 65, 'monthly', '2025-12-06', '2026-01-05', '2025-12-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-12-05 16:05:00', '2025-12-10 16:00:00'),
(354, 65, 65, 'monthly', '2026-01-06', '2026-02-05', '2026-01-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-01-05 16:05:00', '2026-01-10 16:00:00'),
(355, 65, 65, 'monthly', '2026-02-06', '2026-03-05', '2026-02-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-02-05 16:05:00', '2026-02-10 16:00:00'),
(356, 65, 65, 'monthly', '2026-03-06', '2026-04-05', '2026-03-11', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-03-05 16:05:00', '2026-03-10 16:00:00'),
(357, 66, 66, 'move_in', '2025-10-01', '2025-10-01', '2025-10-01', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2025-09-19 04:30:00', '2025-09-30 16:00:00'),
(358, 66, 66, 'monthly', '2025-10-01', '2025-10-31', '2025-10-06', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-09-30 16:05:00', '2025-10-05 16:00:00'),
(359, 66, 66, 'monthly', '2025-11-01', '2025-11-30', '2025-11-06', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-10-31 16:05:00', '2025-11-05 16:00:00'),
(360, 66, 66, 'monthly', '2025-12-01', '2025-12-31', '2025-12-06', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-11-30 16:05:00', '2025-12-05 16:00:00'),
(361, 66, 66, 'monthly', '2026-01-01', '2026-01-31', '2026-01-06', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-12-31 16:05:00', '2026-01-05 16:00:00'),
(362, 67, 67, 'move_in', '2026-03-10', '2026-03-10', '2026-03-10', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-02-25 05:30:00', '2026-03-09 16:00:00'),
(363, 67, 67, 'monthly', '2026-03-10', '2026-04-09', '2026-03-15', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-03-09 16:05:00', '2026-03-14 16:00:00'),
(364, 67, 67, 'monthly', '2026-04-10', '2026-05-09', '2026-04-15', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-04-09 16:05:00', '2026-04-14 16:00:00'),
(365, 67, 67, 'monthly', '2026-05-10', '2026-06-09', '2026-05-15', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-05-09 16:05:00', '2026-05-14 16:00:00'),
(366, 67, 67, 'monthly', '2026-06-10', '2026-07-09', '2026-06-15', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-06-09 16:05:00', '2026-06-14 16:00:00'),
(367, 68, 68, 'move_in', '2025-10-05', '2025-10-05', '2025-10-05', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2025-09-21 06:30:00', '2025-10-04 16:00:00'),
(368, 68, 68, 'monthly', '2025-10-05', '2025-11-04', '2025-10-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-10-04 16:05:00', '2025-10-09 16:00:00'),
(369, 68, 68, 'monthly', '2025-11-05', '2025-12-04', '2025-11-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-11-04 16:05:00', '2025-11-09 16:00:00'),
(370, 68, 68, 'monthly', '2025-12-05', '2026-01-04', '2025-12-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-12-04 16:05:00', '2025-12-09 16:00:00'),
(371, 68, 68, 'monthly', '2026-01-05', '2026-02-04', '2026-01-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-01-04 16:05:00', '2026-01-09 16:00:00'),
(372, 68, 68, 'monthly', '2026-02-05', '2026-03-04', '2026-02-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-02-04 16:05:00', '2026-02-09 16:00:00'),
(373, 68, 68, 'monthly', '2026-03-05', '2026-04-04', '2026-03-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-03-04 16:05:00', '2026-03-09 16:00:00'),
(374, 68, 68, 'monthly', '2026-04-05', '2026-05-04', '2026-04-10', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-04-04 16:05:00', '2026-04-09 16:00:00'),
(375, 69, 69, 'move_in', '2025-10-02', '2025-10-02', '2025-10-02', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2025-09-24 07:30:00', '2025-10-01 16:00:00'),
(376, 69, 69, 'monthly', '2025-10-02', '2025-11-01', '2025-10-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-10-01 16:05:00', '2025-10-06 16:00:00'),
(377, 69, 69, 'monthly', '2025-11-02', '2025-12-01', '2025-11-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-11-01 16:05:00', '2025-11-06 16:00:00'),
(378, 69, 69, 'monthly', '2025-12-02', '2026-01-01', '2025-12-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2025-12-01 16:05:00', '2025-12-06 16:00:00'),
(379, 69, 69, 'monthly', '2026-01-02', '2026-02-01', '2026-01-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-01-01 16:05:00', '2026-01-06 16:00:00'),
(380, 69, 69, 'monthly', '2026-02-02', '2026-03-01', '2026-02-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-02-01 16:05:00', '2026-02-06 16:00:00'),
(381, 69, 69, 'monthly', '2026-03-02', '2026-04-01', '2026-03-07', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-03-01 16:05:00', '2026-03-06 16:00:00'),
(382, 70, 70, 'move_in', '2026-04-25', '2026-04-25', '2026-04-25', 6800.00, 0.00, 0.00, 0.00, 6800.00, 'paid', '2026-04-16 08:30:00', '2026-04-24 16:00:00'),
(383, 70, 70, 'monthly', '2026-04-25', '2026-05-24', '2026-04-30', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-04-24 16:05:00', '2026-04-29 16:00:00'),
(384, 70, 70, 'monthly', '2026-05-25', '2026-06-24', '2026-05-30', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-05-24 16:05:00', '2026-05-29 16:00:00'),
(385, 70, 70, 'monthly', '2026-06-25', '2026-07-24', '2026-06-30', 3400.00, 500.00, 250.00, 0.00, 4150.00, 'paid', '2026-06-24 16:05:00', '2026-06-29 16:00:00'),
(386, 71, 71, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-02 01:30:00', '2025-09-11 16:00:00'),
(387, 71, 71, 'monthly', '2025-09-12', '2025-10-11', '2025-09-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-09-11 16:05:00', '2025-09-16 16:00:00'),
(388, 71, 71, 'monthly', '2025-10-12', '2025-11-11', '2025-10-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-10-11 16:05:00', '2025-10-16 16:00:00'),
(389, 71, 71, 'monthly', '2025-11-12', '2025-12-11', '2025-11-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-11-11 16:05:00', '2025-11-16 16:00:00'),
(390, 71, 71, 'monthly', '2025-12-12', '2026-01-11', '2025-12-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-12-11 16:05:00', '2025-12-16 16:00:00'),
(391, 71, 71, 'monthly', '2026-01-12', '2026-02-11', '2026-01-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-01-11 16:05:00', '2026-01-16 16:00:00'),
(392, 71, 71, 'monthly', '2026-02-12', '2026-03-11', '2026-02-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-02-11 16:05:00', '2026-02-16 16:00:00'),
(393, 71, 71, 'monthly', '2026-03-12', '2026-04-11', '2026-03-17', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-03-11 16:05:00', '2026-03-16 16:00:00'),
(394, 72, 72, 'move_in', '2025-10-14', '2025-10-14', '2025-10-14', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-10-03 02:30:00', '2025-10-13 16:00:00'),
(395, 72, 72, 'monthly', '2025-10-14', '2025-11-13', '2025-10-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-10-13 16:05:00', '2025-10-18 16:00:00'),
(396, 72, 72, 'monthly', '2025-11-14', '2025-12-13', '2025-11-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-11-13 16:05:00', '2025-11-18 16:00:00'),
(397, 72, 72, 'monthly', '2025-12-14', '2026-01-13', '2025-12-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2025-12-13 16:05:00', '2025-12-18 16:00:00'),
(398, 72, 72, 'monthly', '2026-01-14', '2026-02-13', '2026-01-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-01-13 16:05:00', '2026-01-18 16:00:00'),
(399, 72, 72, 'monthly', '2026-02-14', '2026-03-13', '2026-02-19', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-02-13 16:05:00', '2026-02-18 16:00:00'),
(400, 73, 73, 'move_in', '2026-04-06', '2026-04-06', '2026-04-06', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-25 03:30:00', '2026-04-05 16:00:00'),
(401, 73, 73, 'monthly', '2026-04-06', '2026-05-05', '2026-04-11', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-04-05 16:05:00', '2026-04-10 16:00:00'),
(402, 73, 73, 'monthly', '2026-05-06', '2026-06-05', '2026-05-11', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-05-05 16:05:00', '2026-05-10 16:00:00'),
(403, 73, 73, 'monthly', '2026-06-06', '2026-07-05', '2026-06-11', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-06-05 16:05:00', '2026-06-10 16:00:00'),
(404, 73, 73, 'monthly', '2026-07-06', '2026-08-05', '2026-07-11', 4500.00, 600.00, 300.00, 0.00, 5400.00, 'paid', '2026-07-05 16:05:00', '2026-07-10 16:00:00'),
(405, 74, 74, 'move_in', '2025-10-04', '2025-10-04', '2025-10-04', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-09-21 04:30:00', '2025-10-03 16:00:00'),
(406, 74, 74, 'monthly', '2025-10-04', '2025-11-03', '2025-10-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-03 16:05:00', '2025-10-08 16:00:00'),
(407, 74, 74, 'monthly', '2025-11-04', '2025-12-03', '2025-11-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-03 16:05:00', '2025-11-08 16:00:00'),
(408, 74, 74, 'monthly', '2025-12-04', '2026-01-03', '2025-12-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-03 16:05:00', '2025-12-08 16:00:00'),
(409, 74, 74, 'monthly', '2026-01-04', '2026-02-03', '2026-01-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-03 16:05:00', '2026-01-08 16:00:00'),
(410, 74, 74, 'monthly', '2026-02-04', '2026-03-03', '2026-02-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-02-03 16:05:00', '2026-02-08 16:00:00'),
(411, 74, 74, 'monthly', '2026-03-04', '2026-04-03', '2026-03-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-03-03 16:05:00', '2026-03-08 16:00:00'),
(412, 74, 74, 'monthly', '2026-04-04', '2026-05-03', '2026-04-09', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-03 16:05:00', '2026-04-08 16:00:00'),
(413, 75, 75, 'move_in', '2025-09-24', '2025-09-24', '2025-09-24', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-09-10 05:30:00', '2025-09-23 16:00:00'),
(414, 75, 75, 'monthly', '2025-09-24', '2025-10-23', '2025-09-29', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-09-23 16:05:00', '2025-09-28 16:00:00'),
(415, 75, 75, 'monthly', '2025-10-24', '2025-11-23', '2025-10-29', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-23 16:05:00', '2025-10-28 16:00:00'),
(416, 75, 75, 'monthly', '2025-11-24', '2025-12-23', '2025-11-29', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-23 16:05:00', '2025-11-28 16:00:00'),
(417, 75, 75, 'monthly', '2025-12-24', '2026-01-23', '2025-12-29', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-23 16:05:00', '2025-12-28 16:00:00'),
(418, 75, 75, 'monthly', '2026-01-24', '2026-02-23', '2026-01-29', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-23 16:05:00', '2026-01-28 16:00:00'),
(419, 75, 75, 'monthly', '2026-02-24', '2026-03-23', '2026-03-01', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-02-23 16:05:00', '2026-02-28 16:00:00'),
(420, 76, 76, 'move_in', '2025-09-07', '2025-09-07', '2025-09-07', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-08-30 06:30:00', '2025-09-06 16:00:00'),
(421, 76, 76, 'monthly', '2025-09-07', '2025-10-06', '2025-09-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-09-06 16:05:00', '2025-09-11 16:00:00'),
(422, 76, 76, 'monthly', '2025-10-07', '2025-11-06', '2025-10-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-06 16:05:00', '2025-10-11 16:00:00'),
(423, 76, 76, 'monthly', '2025-11-07', '2025-12-06', '2025-11-12', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-06 16:05:00', '2025-11-11 16:00:00'),
(424, 77, 77, 'move_in', '2025-12-17', '2025-12-17', '2025-12-17', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-12-08 07:30:00', '2025-12-16 16:00:00'),
(425, 77, 77, 'monthly', '2025-12-17', '2026-01-16', '2025-12-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-16 16:05:00', '2025-12-21 16:00:00'),
(426, 77, 77, 'monthly', '2026-01-17', '2026-02-16', '2026-01-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-16 16:05:00', '2026-01-21 16:00:00'),
(427, 77, 77, 'monthly', '2026-02-17', '2026-03-16', '2026-02-22', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-02-16 16:05:00', '2026-02-21 16:00:00'),
(428, 78, 78, 'move_in', '2026-04-02', '2026-04-02', '2026-04-02', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-03-23 08:30:00', '2026-04-01 16:00:00'),
(429, 78, 78, 'monthly', '2026-04-02', '2026-05-01', '2026-04-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-01 16:05:00', '2026-04-06 16:00:00'),
(430, 78, 78, 'monthly', '2026-05-02', '2026-06-01', '2026-05-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-01 16:05:00', '2026-05-06 16:00:00'),
(431, 78, 78, 'monthly', '2026-06-02', '2026-07-01', '2026-06-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-01 16:05:00', '2026-06-06 16:00:00'),
(432, 78, 78, 'monthly', '2026-07-02', '2026-08-01', '2026-07-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-01 16:05:00', '2026-07-06 16:00:00'),
(433, 79, 79, 'move_in', '2025-09-02', '2025-09-02', '2025-09-02', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-08-22 01:30:00', '2025-09-01 16:00:00'),
(434, 79, 79, 'monthly', '2025-09-02', '2025-10-01', '2025-09-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-09-01 16:05:00', '2025-09-06 16:00:00'),
(435, 79, 79, 'monthly', '2025-10-02', '2025-11-01', '2025-10-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-01 16:05:00', '2025-10-06 16:00:00'),
(436, 79, 79, 'monthly', '2025-11-02', '2025-12-01', '2025-11-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-01 16:05:00', '2025-11-06 16:00:00'),
(437, 79, 79, 'monthly', '2025-12-02', '2026-01-01', '2025-12-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-01 16:05:00', '2025-12-06 16:00:00'),
(438, 79, 79, 'monthly', '2026-01-02', '2026-02-01', '2026-01-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-01 16:05:00', '2026-01-06 16:00:00'),
(439, 79, 79, 'monthly', '2026-02-02', '2026-03-01', '2026-02-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-02-01 16:05:00', '2026-02-06 16:00:00'),
(440, 80, 80, 'move_in', '2026-03-30', '2026-03-30', '2026-03-30', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-03-18 02:30:00', '2026-03-29 16:00:00'),
(441, 80, 80, 'monthly', '2026-03-30', '2026-04-29', '2026-04-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-03-29 16:05:00', '2026-04-03 16:00:00'),
(442, 80, 80, 'monthly', '2026-04-30', '2026-05-29', '2026-05-05', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-29 16:05:00', '2026-05-04 16:00:00'),
(443, 80, 80, 'monthly', '2026-05-30', '2026-06-29', '2026-06-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-29 16:05:00', '2026-06-03 16:00:00'),
(444, 81, 81, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-09-17 03:30:00', '2025-09-29 16:00:00'),
(445, 81, 81, 'monthly', '2025-09-30', '2025-10-29', '2025-10-05', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-09-29 16:05:00', '2025-10-04 16:00:00'),
(446, 81, 81, 'monthly', '2025-10-30', '2025-11-29', '2025-11-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-29 16:05:00', '2025-11-03 16:00:00'),
(447, 81, 81, 'monthly', '2025-11-30', '2025-12-29', '2025-12-05', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-29 16:05:00', '2025-12-04 16:00:00'),
(448, 81, 81, 'monthly', '2025-12-30', '2026-01-29', '2026-01-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-29 16:05:00', '2026-01-03 16:00:00'),
(449, 81, 81, 'monthly', '2026-01-30', '2026-02-27', '2026-02-04', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-29 16:05:00', '2026-02-03 16:00:00'),
(450, 82, 82, 'move_in', '2026-03-10', '2026-03-10', '2026-03-10', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2026-02-24 04:30:00', '2026-03-09 16:00:00'),
(451, 82, 82, 'monthly', '2026-03-10', '2026-04-09', '2026-03-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-03-09 16:05:00', '2026-03-14 16:00:00'),
(452, 82, 82, 'monthly', '2026-04-10', '2026-05-09', '2026-04-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-09 16:05:00', '2026-04-14 16:00:00'),
(453, 82, 82, 'monthly', '2026-05-10', '2026-06-09', '2026-05-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-05-09 16:05:00', '2026-05-14 16:00:00'),
(454, 82, 82, 'monthly', '2026-06-10', '2026-07-09', '2026-06-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-06-09 16:05:00', '2026-06-14 16:00:00'),
(455, 82, 82, 'monthly', '2026-07-10', '2026-08-09', '2026-07-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-07-09 16:05:00', '2026-07-14 16:00:00'),
(456, 82, 82, 'monthly', '2026-08-10', '2026-09-09', '2026-08-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-08-09 16:05:00', '2026-08-14 16:00:00'),
(457, 82, 82, 'monthly', '2026-09-10', '2026-10-09', '2026-09-15', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-09-09 16:05:00', '2026-09-14 16:00:00'),
(458, 83, 83, 'move_in', '2025-09-02', '2025-09-02', '2025-09-02', 5200.00, 0.00, 0.00, 0.00, 5200.00, 'paid', '2025-08-25 05:30:00', '2025-09-01 16:00:00'),
(459, 83, 83, 'monthly', '2025-09-02', '2025-10-01', '2025-09-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-09-01 16:05:00', '2025-09-06 16:00:00'),
(460, 83, 83, 'monthly', '2025-10-02', '2025-11-01', '2025-10-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-10-01 16:05:00', '2025-10-06 16:00:00'),
(461, 83, 83, 'monthly', '2025-11-02', '2025-12-01', '2025-11-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-11-01 16:05:00', '2025-11-06 16:00:00'),
(462, 83, 83, 'monthly', '2025-12-02', '2026-01-01', '2025-12-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2025-12-01 16:05:00', '2025-12-06 16:00:00'),
(463, 83, 83, 'monthly', '2026-01-02', '2026-02-01', '2026-01-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-01-01 16:05:00', '2026-01-06 16:00:00'),
(464, 83, 83, 'monthly', '2026-02-02', '2026-03-01', '2026-02-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-02-01 16:05:00', '2026-02-06 16:00:00'),
(465, 83, 83, 'monthly', '2026-03-02', '2026-04-01', '2026-03-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-03-01 16:05:00', '2026-03-06 16:00:00'),
(466, 83, 83, 'monthly', '2026-04-02', '2026-05-01', '2026-04-07', 2600.00, 400.00, 200.00, 0.00, 3200.00, 'paid', '2026-04-01 16:05:00', '2026-04-06 16:00:00'),
(467, 84, 84, 'move_in', '2025-09-14', '2025-09-14', '2025-09-14', 15600.00, 0.00, 0.00, 0.00, 15600.00, 'paid', '2025-09-05 06:30:00', '2025-09-13 16:00:00'),
(468, 84, 84, 'monthly', '2025-09-14', '2025-10-13', '2025-09-19', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2025-09-13 16:05:00', '2025-09-18 16:00:00'),
(469, 84, 84, 'monthly', '2025-10-14', '2025-11-13', '2025-10-19', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2025-10-13 16:05:00', '2025-10-18 16:00:00'),
(470, 84, 84, 'monthly', '2025-11-14', '2025-12-13', '2025-11-19', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2025-11-13 16:05:00', '2025-11-18 16:00:00'),
(471, 84, 84, 'monthly', '2025-12-14', '2026-01-13', '2025-12-19', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2025-12-13 16:05:00', '2025-12-18 16:00:00'),
(472, 85, 85, 'move_in', '2026-01-24', '2026-01-24', '2026-01-24', 15600.00, 0.00, 0.00, 0.00, 15600.00, 'paid', '2026-01-14 07:30:00', '2026-01-23 16:00:00'),
(473, 85, 85, 'monthly', '2026-01-24', '2026-02-23', '2026-01-29', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-01-23 16:05:00', '2026-01-28 16:00:00'),
(474, 85, 85, 'monthly', '2026-02-24', '2026-03-23', '2026-03-01', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-02-23 16:05:00', '2026-02-28 16:00:00'),
(475, 85, 85, 'monthly', '2026-03-24', '2026-04-23', '2026-03-29', 7800.00, 800.00, 500.00, 0.00, 9100.00, 'paid', '2026-03-23 16:05:00', '2026-03-28 16:00:00');

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
(1, 14, 19, 15, 'Broken cabinet door hinge (bed-side locker)', 800.00, '2026-09-19', NULL, 268, '2026-09-18 16:00:00', '2026-09-18 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_refunds`
--

CREATE TABLE `deposit_refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `deductions_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions_note` varchar(500) DEFAULT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_method` varchar(60) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `refunded_at` date NOT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deposit_refunds`
--

INSERT INTO `deposit_refunds` (`id`, `tenant_id`, `deposit_amount`, `deductions_amount`, `deductions_note`, `refund_amount`, `refund_method`, `reference_number`, `refunded_at`, `recorded_by`, `created_at`, `updated_at`) VALUES
(1, 32, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000052325764', '2026-03-04', 266, '2026-03-03 16:00:00', '2026-03-03 16:00:00'),
(2, 33, 3500.00, 0.00, NULL, 3500.00, 'Cash', NULL, '2026-02-10', 266, '2026-02-09 16:00:00', '2026-02-09 16:00:00'),
(3, 34, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000052711932', '2026-06-25', 266, '2026-06-24 16:00:00', '2026-06-24 16:00:00'),
(4, 35, 3500.00, 500.00, 'Replacement of lost room key.', 3000.00, 'Cash', NULL, '2026-02-28', 266, '2026-02-27 16:00:00', '2026-02-27 16:00:00'),
(5, 36, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000053049829', '2026-07-23', 266, '2026-07-22 16:00:00', '2026-07-22 16:00:00'),
(6, 37, 3500.00, 0.00, NULL, 3500.00, 'Cash', NULL, '2026-01-27', 266, '2026-01-26 16:00:00', '2026-01-26 16:00:00'),
(7, 38, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000053435997', '2026-03-12', 266, '2026-03-11 16:00:00', '2026-03-11 16:00:00'),
(8, 39, 4500.00, 500.00, 'Replacement of lost room key.', 4000.00, 'Cash', NULL, '2026-04-16', 266, '2026-04-15 16:00:00', '2026-04-15 16:00:00'),
(9, 40, 3250.00, 0.00, NULL, 3250.00, 'GCash', '1000053918707', '2026-02-13', 266, '2026-02-12 16:00:00', '2026-02-12 16:00:00'),
(10, 41, 3250.00, 0.00, NULL, 3250.00, 'Cash', NULL, '2026-08-22', 266, '2026-08-21 16:00:00', '2026-08-21 16:00:00'),
(11, 42, 3250.00, 0.00, NULL, 3250.00, 'GCash', '1000054353146', '2026-03-26', 266, '2026-03-25 16:00:00', '2026-03-25 16:00:00'),
(12, 43, 3250.00, 500.00, 'Replacement of lost room key.', 2750.00, 'Cash', NULL, '2026-03-24', 266, '2026-03-23 16:00:00', '2026-03-23 16:00:00'),
(13, 44, 3250.00, 0.00, NULL, 3250.00, 'GCash', '1000054884127', '2026-05-15', 266, '2026-05-14 16:00:00', '2026-05-14 16:00:00'),
(14, 45, 2500.00, 0.00, NULL, 2500.00, 'Cash', NULL, '2025-12-24', 266, '2025-12-23 16:00:00', '2025-12-23 16:00:00'),
(15, 46, 2500.00, 0.00, NULL, 2500.00, 'GCash', '1000055222024', '2026-03-28', 266, '2026-03-27 16:00:00', '2026-03-27 16:00:00'),
(16, 47, 2500.00, 500.00, 'Replacement of lost room key.', 2000.00, 'Cash', NULL, '2026-07-26', 266, '2026-07-25 16:00:00', '2026-07-25 16:00:00'),
(17, 48, 2500.00, 0.00, NULL, 2500.00, 'GCash', '1000055559921', '2026-03-16', 266, '2026-03-15 16:00:00', '2026-03-15 16:00:00'),
(18, 49, 2500.00, 0.00, NULL, 2500.00, 'Cash', NULL, '2026-05-23', 266, '2026-05-22 16:00:00', '2026-05-22 16:00:00'),
(19, 50, 2500.00, 0.00, NULL, 2500.00, 'GCash', '1000055994360', '2025-12-28', 266, '2025-12-27 16:00:00', '2025-12-27 16:00:00'),
(20, 51, 2500.00, 500.00, 'Replacement of lost room key.', 2000.00, 'Cash', NULL, '2026-06-20', 266, '2026-06-19 16:00:00', '2026-06-19 16:00:00'),
(21, 52, 2500.00, 0.00, NULL, 2500.00, 'GCash', '1000056428799', '2026-03-20', 266, '2026-03-19 16:00:00', '2026-03-19 16:00:00'),
(22, 53, 2500.00, 0.00, NULL, 2500.00, 'Cash', NULL, '2026-10-01', 266, '2026-09-30 16:00:00', '2026-09-30 16:00:00'),
(23, 54, 4750.00, 0.00, NULL, 4750.00, 'GCash', '1000056863238', '2026-01-21', 266, '2026-01-20 16:00:00', '2026-01-20 16:00:00'),
(24, 55, 4750.00, 500.00, 'Replacement of lost room key.', 4250.00, 'Cash', NULL, '2026-07-18', 266, '2026-07-17 16:00:00', '2026-07-17 16:00:00'),
(25, 56, 4750.00, 0.00, NULL, 4750.00, 'GCash', '1000057201135', '2026-01-17', 266, '2026-01-16 16:00:00', '2026-01-16 16:00:00'),
(26, 57, 4750.00, 0.00, NULL, 4750.00, 'Cash', NULL, '2026-07-04', 266, '2026-07-03 16:00:00', '2026-07-03 16:00:00'),
(27, 58, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000057587303', '2026-01-22', 266, '2026-01-21 16:00:00', '2026-01-21 16:00:00'),
(28, 59, 3500.00, 500.00, 'Replacement of lost room key.', 3000.00, 'Cash', NULL, '2026-05-09', 266, '2026-05-08 16:00:00', '2026-05-08 16:00:00'),
(29, 60, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000058021742', '2026-06-05', 266, '2026-06-04 16:00:00', '2026-06-04 16:00:00'),
(30, 61, 3500.00, 0.00, NULL, 3500.00, 'Cash', NULL, '2026-10-01', 266, '2026-09-30 16:00:00', '2026-09-30 16:00:00'),
(31, 62, 3500.00, 0.00, NULL, 3500.00, 'GCash', '1000058407910', '2026-03-03', 266, '2026-03-02 16:00:00', '2026-03-02 16:00:00'),
(32, 63, 8000.00, 500.00, 'Replacement of lost room key.', 7500.00, 'Cash', NULL, '2025-12-07', 266, '2025-12-06 16:00:00', '2025-12-06 16:00:00'),
(33, 64, 8000.00, 0.00, NULL, 8000.00, 'GCash', '1000058697536', '2026-04-21', 266, '2026-04-20 16:00:00', '2026-04-20 16:00:00'),
(34, 65, 3400.00, 0.00, NULL, 3400.00, 'Cash', NULL, '2026-04-09', 266, '2026-04-08 16:00:00', '2026-04-08 16:00:00'),
(35, 66, 3400.00, 0.00, NULL, 3400.00, 'GCash', '1000059131975', '2026-02-04', 266, '2026-02-03 16:00:00', '2026-02-03 16:00:00'),
(36, 67, 3400.00, 500.00, 'Replacement of lost room key.', 2900.00, 'Cash', NULL, '2026-07-13', 266, '2026-07-12 16:00:00', '2026-07-12 16:00:00'),
(37, 68, 3400.00, 0.00, NULL, 3400.00, 'GCash', '1000059614685', '2026-05-08', 266, '2026-05-07 16:00:00', '2026-05-07 16:00:00'),
(38, 69, 3400.00, 0.00, NULL, 3400.00, 'Cash', NULL, '2026-04-05', 266, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(39, 70, 3400.00, 0.00, NULL, 3400.00, 'GCash', '1000060000853', '2026-07-15', 266, '2026-07-14 16:00:00', '2026-07-14 16:00:00'),
(40, 71, 4500.00, 500.00, 'Replacement of lost room key.', 4000.00, 'Cash', NULL, '2026-03-19', 266, '2026-03-18 16:00:00', '2026-03-18 16:00:00'),
(41, 72, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000060483563', '2026-03-17', 266, '2026-03-16 16:00:00', '2026-03-16 16:00:00'),
(42, 73, 4500.00, 0.00, NULL, 4500.00, 'Cash', NULL, '2026-07-14', 266, '2026-07-13 16:00:00', '2026-07-13 16:00:00'),
(43, 74, 2600.00, 0.00, NULL, 2600.00, 'GCash', '1000060918002', '2026-04-08', 266, '2026-04-07 16:00:00', '2026-04-07 16:00:00'),
(44, 75, 2600.00, 500.00, 'Replacement of lost room key.', 2100.00, 'Cash', NULL, '2026-03-02', 266, '2026-03-01 16:00:00', '2026-03-01 16:00:00'),
(45, 76, 2600.00, 0.00, NULL, 2600.00, 'GCash', '1000061304170', '2025-12-10', 266, '2025-12-09 16:00:00', '2025-12-09 16:00:00'),
(46, 77, 2600.00, 0.00, NULL, 2600.00, 'Cash', NULL, '2026-03-20', 266, '2026-03-19 16:00:00', '2026-03-19 16:00:00'),
(47, 78, 2600.00, 0.00, NULL, 2600.00, 'GCash', '1000061593796', '2026-08-05', 266, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(48, 79, 2600.00, 500.00, 'Replacement of lost room key.', 2100.00, 'Cash', NULL, '2026-03-05', 266, '2026-03-04 16:00:00', '2026-03-04 16:00:00'),
(49, 80, 2600.00, 0.00, NULL, 2600.00, 'GCash', '1000061979964', '2026-06-13', 266, '2026-06-12 16:00:00', '2026-06-12 16:00:00'),
(50, 81, 2600.00, 0.00, NULL, 2600.00, 'Cash', NULL, '2026-03-03', 266, '2026-03-02 16:00:00', '2026-03-02 16:00:00'),
(51, 82, 2600.00, 0.00, NULL, 2600.00, 'GCash', '1000062462674', '2026-09-25', 266, '2026-09-24 16:00:00', '2026-09-24 16:00:00'),
(52, 83, 2600.00, 500.00, 'Replacement of lost room key.', 2100.00, 'Cash', NULL, '2026-05-03', 266, '2026-05-02 16:00:00', '2026-05-02 16:00:00'),
(53, 84, 7800.00, 0.00, NULL, 7800.00, 'GCash', '1000062945384', '2026-01-17', 266, '2026-01-16 16:00:00', '2026-01-16 16:00:00'),
(54, 85, 7800.00, 0.00, NULL, 7800.00, 'Cash', NULL, '2026-04-15', 266, '2026-04-14 16:00:00', '2026-04-14 16:00:00');

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
(1, 'Pureza Station Dormitory', 'Pureza Station Dormitory has been a trusted home for students and young professionals since 1996. Just a 5-minute walk from PUP and the Pureza LRT Station, we offer clean, secure, and affordable rooms designed for easy, comfortable living close to school and work.', 'Pureza Station, Manila', '09289811405', 'dormitorypurezastation@gmail.com', 'dormitory-profile/yYzcKk7O16Id6prPIyO47BJJlqGqSHs3EY1fNhes.jpg', NULL, 'dormitory-profile/JU08CTcqjUS5XJqlZKb7DemhpAryCiQObz8Xy0vp.pdf', 'contracts/dormitory-contract.pdf', 'dormitory-profile/legitimacy/SLgvrZYlX5qCvu1idRlgwXiWcWFJuSqy2Ecb2GgP.pdf', 'dormitory-profile/legitimacy/5sPbbKzi1OrwF8CRR2KFtur4qQtUgSCjbdJuJ6sr.png', NULL, NULL, 'Rent may be paid in cash, GCash, or bank deposit (BDO).\n\nTenancy is subject to a three-month minimum. Tenants must provide the start\nand end date of their stay upon registration.\n\nUpon registration, new tenants pay a reservation fee composed of a security\ndeposit (one month, refundable for a 3-month contract) and one month advance\nrent. The reservation fee is non-refundable if the tenant cancels or checks\nout earlier than the three-month minimum. The deposit is returned within\n2–3 weeks after the check-out date.\n\nRent is due every 1st day of the month. For GCash or bank deposit, payment\nconfirmation must be sent to the dormitory\'s official contact channels.\n\nTenants are granted a 3-day grace period for late rent payments. Beyond the\ngrace period, a 10% penalty fee applies. Failure to pay within one month\nresults in a notice of eviction for non-payment.\n\nTenants wishing to extend their stay must give at least 1 month notice.\nMove-out requires at least 2 weeks notice; move-out schedule is end of month.', 'Alcoholic beverages, smoking, and vaping are not allowed on dormitory premises.\n\nWashing of clothes is not allowed; a laundry service is available outside.\n\nTenants are responsible for keeping common areas clean after use, and must\npromptly report any damages or issues to maintenance staff.\n\nTenants must pay for any loss or damage to dormitory property caused by\nthemselves or their guests, at the cost of the damage (minimum ₱500).\n\nOnly registered tenants may enter the rooms. Visitors may be entertained at\nthe receiving area.\n\nHazardous goods (gas, cooking stoves, flammable fuels, firearms) are strictly\nprohibited; violation carries a ₱500 fine and may be reported to authorities.\nDrugs and illegal substances are strictly prohibited and will be reported.\n\nSilence should be observed at all times out of consideration for other tenants.\nTreat fellow tenants and staff with respect — harassment, discrimination, or\nbullying will not be tolerated.\n\nManagement is not responsible for losses or injuries occurring on the premises.\nTenants should exercise care and diligence at all times.\n\nDoors and windows must be closed when using the air-conditioner. When leaving,\nturn off all faucets, showers, lights, air conditioners, and appliances, and\nlock the door. Lost or damaged keys cost ₱50 to replace.\n\nA strict NO PETS policy is enforced.\n\nCurfew hours: 11PM – 4AM. Aircon schedule: 10PM – 5AM.', 'Advanced notice: Residents planning to check out must give written notice at\nleast two weeks before their intended departure date.\n\nRoom inspection: A staff member will inspect the room/bed before check-out to\nassess damages or cleanliness issues. Rooms should be clean before inspection.\n\nDamages and repairs: Residents are responsible for damage beyond normal wear\nand tear, and will be charged for repairs or replacements.\n\nFurniture and equipment: All dormitory-provided furniture and equipment must\nbe present and in good condition. Missing or damaged items incur charges.\n\nCleanliness: Rooms must be left in move-in condition, with all personal\nbelongings removed and shared areas cleaned.\n\nTrash disposal: Dispose of all trash and recyclables in designated bins.\n\nKey return: Room keys must be returned upon check-out. Failure to return keys\nmay result in a fine.\n\nCheck-out time: Residents must vacate by 2:00 PM on the check-out date.\n\nFinal settlement: After inspection, the security deposit is returned minus any\ndeductions for damages or outstanding charges, within two weeks of check-out.', '2026-08-27 12:47:03', '2026-09-30 14:42:04');

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
(1, 6, 33, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-21 22:00:00', '2026-09-21 22:00:00'),
(2, 6, 33, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-23 22:00:00', '2026-09-23 22:00:00'),
(3, 6, 33, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-25 22:00:00', '2026-09-25 22:00:00'),
(4, 6, 33, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST.PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-28 22:00:00', '2026-09-28 22:00:00'),
(5, 6, 33, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST.PH', 'sent', NULL, '2026-09-29 22:00:00', '2026-09-29 22:00:00'),
(6, 6, 33, 4, 'emergency_contact_notified', 'This is to inform you that John Paul Mendoza\'s account at NEST.PH is 9 days overdue, balance PHP 3,460.00. Please encourage them to settle it as soon as possible.', 'sent', NULL, '2026-09-30 22:00:00', '2026-09-30 22:00:00'),
(7, 9, 47, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-27 22:00:00', '2026-09-27 22:00:00'),
(8, 9, 47, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,000.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-29 22:00:00', '2026-09-29 22:00:00'),
(9, 13, 73, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-24 22:00:00', '2026-09-24 22:00:00'),
(10, 13, 73, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,350.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-26 22:00:00', '2026-09-26 22:00:00'),
(11, 13, 73, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,350.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-28 22:00:00', '2026-09-28 22:00:00'),
(12, 13, NULL, 2, 'admin_override_pause', 'Paused by admin: tenant agreed to a payment plan (half on the 15th, half on the 30th).', 'resolved', 266, '2026-09-30 06:20:00', '2026-09-30 06:20:00'),
(13, 24, 122, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-08-16 22:00:00', '2026-08-16 22:00:00'),
(14, 24, 122, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST.PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-18 22:00:00', '2026-08-18 22:00:00'),
(15, 24, 122, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST.PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-20 22:00:00', '2026-08-20 22:00:00'),
(16, 24, 122, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST.PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 3,460.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-23 22:00:00', '2026-08-23 22:00:00'),
(17, 24, 122, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST.PH', 'sent', NULL, '2026-08-24 22:00:00', '2026-08-24 22:00:00'),
(18, 24, 122, 4, 'emergency_contact_notified', 'This is to inform you that Joseph Allan Cruz\'s account at NEST.PH is 9 days overdue, balance PHP 3,460.00. Please encourage them to settle it as soon as possible.', 'sent', NULL, '2026-08-25 22:00:00', '2026-08-25 22:00:00'),
(19, 24, 122, 5, 'demand_letter_generated', 'demand-letters/24_122.pdf', 'sent', NULL, '2026-08-26 22:00:00', '2026-08-26 22:00:00'),
(20, 24, 122, 6, 'delinquent_blacklisted', NULL, 'resolved', NULL, '2026-08-27 22:00:00', '2026-08-27 22:00:00');

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
(3, 'Ground Floor', 0.00, 0.00, 1, 'Lobby, receiving area and study hall. Mixed rooms near the entrance.', '2026-07-29 22:22:25', '2026-09-30 16:13:55'),
(11, 'Second Floor', 0.00, 0.00, 2, 'Shared rooms and quiet solo units for working tenants.', '2026-09-04 13:04:53', '2026-09-30 16:13:55'),
(12, 'Third Floor', 0.00, 0.00, 3, 'Newly renovated rooms with balcony access.', '2026-09-29 13:00:54', '2026-09-30 16:13:55');

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
(1, 'Aira Nicole Dimaculangan', '09174402167', 'aira.dimaculangan@gmail.com', 88, 'Hello po! May available pa po bang bedspace for female this November? Magkano po ang quad sharing?', NULL, NULL, NULL, 'Quad Sharing', 1, 'new', '2026-09-30 13:13:58', '2026-09-30 13:13:58'),
(2, 'Rhenz Adrian Pacheco', '09184410086', 'rhenz.pacheco@gmail.com', 91, 'Good day! Pwede po ba mag-ocular visit this Saturday? Working po ako sa Makati, looking for a solo room.', NULL, NULL, NULL, 'Solo Room', 1, 'new', '2026-09-30 03:12:00', '2026-09-30 03:12:00'),
(3, 'Janella Marie Quiambao', '09274418005', 'janella.quiambao@gmail.com', NULL, 'Kasama na po ba ang WiFi at kuryente sa monthly rate?', 'Hi Janella! Hiwalay po ang utilities at WiFi pero hati-hati ang room mates, around ₱900/month per bed para sa quad. Welcome po kayong mag-visit!', '2026-09-28 10:12:00', 266, 'Double Sharing', 1, 'contacted', '2026-09-28 05:12:00', '2026-09-28 10:12:00'),
(4, 'Luis Gabriel Soriano', '09394425924', 'luis.soriano@gmail.com', 90, 'Is there a curfew? I have night classes until 9 PM.', 'Hello Luis! Curfew is 11 PM to 4 AM, so 9 PM classes are no problem. Feel free to apply online through our website.', '2026-09-26 12:12:00', 266, '6-Bed Dormitory', 1, 'contacted', '2026-09-26 07:12:00', '2026-09-26 12:12:00'),
(5, 'Ryan Christopher Santiago', '09454433843', 'ryan.santiago@gmail.com', 86, 'Interested po ako sa Room 203, pwede po ba mag-apply online?', 'Yes po! Na-send na namin ang link. Paki-fill up lang po ang application form.', '2026-09-24 14:12:00', 266, 'Quad Sharing', 1, 'converted', '2026-09-24 09:12:00', '2026-09-24 14:12:00'),
(6, 'Mylene Castillo', '09564441762', 'mylene.castillo@gmail.com', NULL, 'Pwede po ba ang pets? May maliit po akong pusa.', 'Sorry po, strict NO PETS policy po kami. Salamat sa interest!', '2026-09-19 16:12:00', 266, NULL, 1, 'closed', '2026-09-19 11:12:00', '2026-09-19 16:12:00');

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
(1, 1, 1, 1, NULL, '2026-04-21', '2027-03-20', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-11 02:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-04-12 02:15:00', '2026-09-30 16:13:55'),
(2, 2, 2, 2, NULL, '2026-06-29', '2027-03-28', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-18 03:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-19 03:15:00', '2026-09-30 16:13:55'),
(3, 3, 3, 3, NULL, '2026-07-27', '2027-03-26', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-15 04:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-07-16 04:15:00', '2026-09-30 16:13:55'),
(4, 4, 4, 28, NULL, '2026-07-19', '2026-10-21', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-06 05:15:00', 'expiring_soon', NULL, NULL, NULL, NULL, 266, 266, '2026-07-07 05:15:00', '2026-09-30 16:13:55'),
(5, 5, 5, 5, NULL, '2026-03-16', '2027-03-15', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-07 06:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-03-08 06:15:00', '2026-09-30 16:13:55'),
(6, 6, 6, 34, NULL, '2026-06-17', '2027-03-16', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-07 07:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-08 07:15:00', '2026-09-30 16:13:55'),
(7, 7, 7, 7, NULL, '2026-08-26', '2027-03-25', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-08-15 08:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-08-16 08:15:00', '2026-09-30 16:13:55'),
(8, 8, 8, 8, NULL, '2026-05-28', '2027-03-27', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-05-16 01:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-05-17 01:15:00', '2026-09-30 16:13:55'),
(9, 9, 9, 9, NULL, '2026-06-23', '2027-03-22', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-10 02:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-11 02:15:00', '2026-09-30 16:13:55'),
(10, 10, 10, 37, NULL, '2026-04-19', '2026-09-28', 7800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-10 03:15:00', 'expired', NULL, NULL, NULL, NULL, 266, 266, '2026-04-11 03:15:00', '2026-09-30 16:13:55'),
(11, 11, 11, 12, NULL, '2026-01-11', '2027-03-10', 2250.00, 250.00, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-01 04:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-01-02 04:15:00', '2026-09-30 16:13:55'),
(12, 12, 12, 13, NULL, '2026-07-30', '2027-03-29', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-19 05:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-07-20 05:15:00', '2026-09-30 16:13:55'),
(13, 13, 13, 14, NULL, '2026-06-20', '2027-03-19', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-08 06:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(14, 14, 14, 15, NULL, '2026-07-01', '2027-03-31', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-18 07:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-19 07:15:00', '2026-09-30 16:13:55'),
(15, 15, 15, 16, NULL, '2026-06-24', '2027-03-23', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-15 08:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-16 08:15:00', '2026-09-30 16:13:55'),
(16, 16, 16, 17, NULL, '2026-10-05', '2027-04-04', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-25 01:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-09-26 01:15:00', '2026-09-30 16:13:55'),
(17, 17, 17, 18, NULL, '2026-07-22', '2027-03-21', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-11 02:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-07-12 02:15:00', '2026-09-30 16:13:55'),
(18, 18, 18, 20, NULL, '2026-05-13', '2027-03-12', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-05-01 03:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-05-02 03:15:00', '2026-09-30 16:13:55'),
(19, 19, 19, 21, NULL, '2026-10-05', '2027-04-04', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-22 04:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-09-23 04:15:00', '2026-09-30 16:13:55'),
(20, 20, 20, 24, NULL, '2026-04-25', '2027-03-24', 8000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-16 05:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-04-17 05:15:00', '2026-09-30 16:13:55'),
(21, 21, 21, 25, NULL, '2026-06-18', '2027-03-17', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-08 06:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(22, 22, 22, 26, NULL, '2026-08-27', '2027-03-26', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-08-16 07:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-08-17 07:15:00', '2026-09-30 16:13:55'),
(23, 23, 23, 29, NULL, '2026-03-23', '2026-08-22', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-11 08:15:00', 'expired', NULL, NULL, NULL, NULL, 266, 266, '2026-03-12 08:15:00', '2026-09-30 16:13:55'),
(24, 24, 24, 31, NULL, '2026-04-12', '2027-02-11', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-30 01:15:00', 'terminated', 'Terminated for non-payment after full delinquency escalation (Stage 6).', '2026-08-31 16:00:00', NULL, NULL, 266, 266, '2026-03-31 01:15:00', '2026-09-30 16:13:55'),
(25, 25, 25, 4, NULL, '2026-02-15', '2027-03-14', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-06 02:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-02-07 02:15:00', '2026-09-30 16:13:56'),
(26, 26, 26, 6, NULL, '2026-06-20', '2027-03-19', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-10 03:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-06-11 03:15:00', '2026-09-30 16:13:56'),
(27, 27, 27, 10, NULL, '2026-07-22', '2027-03-21', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-07-11 04:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-07-12 04:15:00', '2026-09-30 16:13:56'),
(28, 28, 28, 29, NULL, '2026-09-10', '2027-03-09', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-08-29 05:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-08-30 05:15:00', '2026-09-30 16:13:56'),
(29, 29, 29, 31, NULL, '2026-09-17', '2027-03-16', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-04 06:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-09-05 06:15:00', '2026-09-30 16:13:56'),
(30, 30, 30, 35, NULL, '2026-09-29', '2027-03-28', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-09-20 07:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-09-21 07:15:00', '2026-09-30 16:13:56'),
(31, 31, 31, 36, NULL, '2026-05-07', '2027-03-06', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-27 08:15:00', 'active', NULL, NULL, NULL, NULL, 266, 266, '2026-04-28 08:15:00', '2026-09-30 16:13:56'),
(32, 32, 32, 1, NULL, '2025-09-01', '2026-03-01', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-18 02:30:00', 'terminated', 'Found a job in another city.', '2026-03-01 02:00:00', NULL, NULL, 266, 266, '2025-08-19 02:30:00', '2026-02-28 16:00:00'),
(33, 33, 33, 2, NULL, '2025-09-07', '2026-02-07', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-23 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-07 02:00:00', NULL, NULL, 266, 266, '2025-08-24 03:30:00', '2026-02-06 16:00:00'),
(34, 34, 34, 2, NULL, '2026-03-19', '2026-06-22', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-10 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-06-22 02:00:00', NULL, NULL, 266, 266, '2026-03-11 04:30:00', '2026-06-21 16:00:00'),
(35, 35, 35, 3, NULL, '2025-09-25', '2026-02-25', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-15 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-02-25 02:00:00', NULL, NULL, 266, 266, '2025-09-16 05:30:00', '2026-02-24 16:00:00'),
(36, 36, 36, 3, NULL, '2026-04-18', '2026-07-20', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-07 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-07-20 02:00:00', NULL, NULL, 266, 266, '2026-04-08 06:30:00', '2026-07-19 16:00:00'),
(37, 37, 37, 4, NULL, '2025-09-24', '2026-01-24', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-12 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-01-24 02:00:00', NULL, NULL, 266, 266, '2025-09-13 07:30:00', '2026-01-23 16:00:00'),
(38, 38, 38, 5, NULL, '2025-09-24', '2026-03-09', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-11 08:30:00', 'terminated', 'Found a job in another city.', '2026-03-09 02:00:00', NULL, NULL, 266, 266, '2025-09-12 08:30:00', '2026-03-08 16:00:00'),
(39, 39, 39, 6, NULL, '2025-09-13', '2026-04-13', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-30 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-04-13 02:00:00', NULL, NULL, 266, 266, '2025-08-31 01:30:00', '2026-04-12 16:00:00'),
(40, 40, 40, 7, NULL, '2025-09-10', '2026-02-10', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-26 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-02-10 02:00:00', NULL, NULL, 266, 266, '2025-08-27 02:30:00', '2026-02-09 16:00:00'),
(41, 41, 41, 7, NULL, '2026-03-25', '2026-08-19', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-16 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-08-19 02:00:00', NULL, NULL, 266, 266, '2026-03-17 03:30:00', '2026-08-18 16:00:00'),
(42, 42, 42, 8, NULL, '2025-09-23', '2026-03-23', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-13 04:30:00', 'terminated', 'Moved out at the end of contract.', '2026-03-23 02:00:00', NULL, NULL, 266, 266, '2025-09-14 04:30:00', '2026-03-22 16:00:00'),
(43, 43, 43, 9, NULL, '2025-09-21', '2026-03-21', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-10 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-03-21 02:00:00', NULL, NULL, 266, 266, '2025-09-11 05:30:00', '2026-03-20 16:00:00'),
(44, 44, 44, 10, NULL, '2025-10-12', '2026-05-12', 3250.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-30 06:30:00', 'terminated', 'Found a job in another city.', '2026-05-12 02:00:00', NULL, NULL, 266, 266, '2025-10-01 06:30:00', '2026-05-11 16:00:00'),
(45, 45, 45, 12, NULL, '2025-09-21', '2025-12-21', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-08 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2025-12-21 02:00:00', NULL, NULL, 266, 266, '2025-09-09 07:30:00', '2025-12-20 16:00:00'),
(46, 46, 46, 13, NULL, '2025-09-25', '2026-03-25', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-11 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-03-25 02:00:00', NULL, NULL, 266, 266, '2025-09-12 08:30:00', '2026-03-24 16:00:00'),
(47, 47, 47, 13, NULL, '2026-04-29', '2026-07-23', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-14 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-07-23 02:00:00', NULL, NULL, 266, 266, '2026-04-15 01:30:00', '2026-07-22 16:00:00'),
(48, 48, 48, 14, NULL, '2025-10-13', '2026-03-13', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-04 02:30:00', 'terminated', 'Moved out at the end of contract.', '2026-03-13 02:00:00', NULL, NULL, 266, 266, '2025-10-05 02:30:00', '2026-03-12 16:00:00'),
(49, 49, 49, 15, NULL, '2025-09-20', '2026-05-20', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-10 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-05-20 02:00:00', NULL, NULL, 266, 266, '2025-09-11 03:30:00', '2026-05-19 16:00:00'),
(50, 50, 50, 16, NULL, '2025-09-25', '2025-12-25', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-14 04:30:00', 'terminated', 'Found a job in another city.', '2025-12-25 02:00:00', NULL, NULL, 266, 266, '2025-09-15 04:30:00', '2025-12-24 16:00:00'),
(51, 51, 51, 16, NULL, '2026-01-25', '2026-06-17', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-13 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-06-17 02:00:00', NULL, NULL, 266, 266, '2026-01-14 05:30:00', '2026-06-16 16:00:00'),
(52, 52, 52, 17, NULL, '2025-09-17', '2026-03-17', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-04 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-03-17 02:00:00', NULL, NULL, 266, 266, '2025-09-05 06:30:00', '2026-03-16 16:00:00'),
(53, 53, 53, 17, NULL, '2026-04-02', '2026-09-28', 2500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-19 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-09-28 02:00:00', NULL, NULL, 266, 266, '2026-03-20 07:30:00', '2026-09-27 16:00:00'),
(54, 54, 54, 18, NULL, '2025-09-18', '2026-01-18', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-03 08:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-18 02:00:00', NULL, NULL, 266, 266, '2025-09-04 08:30:00', '2026-01-17 16:00:00'),
(55, 55, 55, 18, NULL, '2026-03-04', '2026-07-15', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-23 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-07-15 02:00:00', NULL, NULL, 266, 266, '2026-02-24 01:30:00', '2026-07-14 16:00:00'),
(56, 56, 56, 19, NULL, '2025-10-14', '2026-01-14', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-04 02:30:00', 'terminated', 'Found a job in another city.', '2026-01-14 02:00:00', NULL, NULL, 266, 266, '2025-10-05 02:30:00', '2026-01-13 16:00:00'),
(57, 57, 57, 19, NULL, '2026-02-01', '2026-07-01', 4750.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-21 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-07-01 02:00:00', NULL, NULL, 266, 266, '2026-01-22 03:30:00', '2026-06-30 16:00:00'),
(58, 58, 58, 20, NULL, '2025-09-19', '2026-01-19', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-07 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-01-19 02:00:00', NULL, NULL, 266, 266, '2025-09-08 04:30:00', '2026-01-18 16:00:00'),
(59, 59, 59, 20, NULL, '2026-02-16', '2026-05-06', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-03 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-05-06 02:00:00', NULL, NULL, 266, 266, '2026-02-04 05:30:00', '2026-05-05 16:00:00'),
(60, 60, 60, 21, NULL, '2025-10-02', '2026-06-02', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-18 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-06-02 02:00:00', NULL, NULL, 266, 266, '2025-09-19 06:30:00', '2026-06-01 16:00:00'),
(61, 61, 61, 21, NULL, '2026-06-18', '2026-09-28', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-06-03 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-09-28 02:00:00', NULL, NULL, 266, 266, '2026-06-04 07:30:00', '2026-09-27 16:00:00'),
(62, 62, 62, 22, NULL, '2025-09-15', '2026-02-28', 3500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-06 08:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 266, 266, '2025-09-07 08:30:00', '2026-02-27 16:00:00'),
(63, 63, 63, 24, NULL, '2025-09-04', '2025-12-04', 8000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-25 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2025-12-04 02:00:00', NULL, NULL, 266, 266, '2025-08-26 01:30:00', '2025-12-03 16:00:00'),
(64, 64, 64, 24, NULL, '2026-01-08', '2026-04-18', 8000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-28 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-04-18 02:00:00', NULL, NULL, 266, 266, '2025-12-29 02:30:00', '2026-04-17 16:00:00'),
(65, 65, 65, 25, NULL, '2025-10-06', '2026-04-06', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-24 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-06 02:00:00', NULL, NULL, 266, 266, '2025-09-25 03:30:00', '2026-04-05 16:00:00'),
(66, 66, 66, 26, NULL, '2025-10-01', '2026-02-01', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-18 04:30:00', 'terminated', 'Moved out at the end of contract.', '2026-02-01 02:00:00', NULL, NULL, 266, 266, '2025-09-19 04:30:00', '2026-01-31 16:00:00'),
(67, 67, 67, 26, NULL, '2026-03-10', '2026-07-10', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-24 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-07-10 02:00:00', NULL, NULL, 266, 266, '2026-02-25 05:30:00', '2026-07-09 16:00:00'),
(68, 68, 68, 27, NULL, '2025-10-05', '2026-05-05', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-20 06:30:00', 'terminated', 'Found a job in another city.', '2026-05-05 02:00:00', NULL, NULL, 266, 266, '2025-09-21 06:30:00', '2026-05-04 16:00:00'),
(69, 69, 69, 28, NULL, '2025-10-02', '2026-04-02', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-23 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-04-02 02:00:00', NULL, NULL, 266, 266, '2025-09-24 07:30:00', '2026-04-01 16:00:00'),
(70, 70, 70, 28, NULL, '2026-04-25', '2026-07-12', 3400.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-04-15 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-07-12 02:00:00', NULL, NULL, 266, 266, '2026-04-16 08:30:00', '2026-07-11 16:00:00'),
(71, 71, 71, 29, NULL, '2025-09-12', '2026-03-16', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-03-16 02:00:00', NULL, NULL, 266, 266, '2025-09-02 01:30:00', '2026-03-15 16:00:00'),
(72, 72, 72, 30, NULL, '2025-10-14', '2026-03-14', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-02 02:30:00', 'terminated', 'Moved out at the end of contract.', '2026-03-14 02:00:00', NULL, NULL, 266, 266, '2025-10-03 02:30:00', '2026-03-13 16:00:00'),
(73, 73, 73, 30, NULL, '2026-04-06', '2026-07-11', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-24 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-07-11 02:00:00', NULL, NULL, 266, 266, '2026-03-25 03:30:00', '2026-07-10 16:00:00'),
(74, 74, 74, 31, NULL, '2025-10-04', '2026-04-05', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-20 04:30:00', 'terminated', 'Found a job in another city.', '2026-04-05 02:00:00', NULL, NULL, 266, 266, '2025-09-21 04:30:00', '2026-04-04 16:00:00'),
(75, 75, 75, 32, NULL, '2025-09-24', '2026-02-27', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-09 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-27 02:00:00', NULL, NULL, 266, 266, '2025-09-10 05:30:00', '2026-02-26 16:00:00'),
(76, 76, 76, 33, NULL, '2025-09-07', '2025-12-07', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-29 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2025-12-07 02:00:00', NULL, NULL, 266, 266, '2025-08-30 06:30:00', '2025-12-06 16:00:00'),
(77, 77, 77, 33, NULL, '2025-12-17', '2026-03-17', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-07 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-03-17 02:00:00', NULL, NULL, 266, 266, '2025-12-08 07:30:00', '2026-03-16 16:00:00'),
(78, 78, 78, 33, NULL, '2026-04-02', '2026-08-02', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-22 08:30:00', 'terminated', 'Moved out at the end of contract.', '2026-08-02 02:00:00', NULL, NULL, 266, 266, '2026-03-23 08:30:00', '2026-08-01 16:00:00'),
(79, 79, 79, 34, NULL, '2025-09-02', '2026-03-02', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-21 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-03-02 02:00:00', NULL, NULL, 266, 266, '2025-08-22 01:30:00', '2026-03-01 16:00:00'),
(80, 80, 80, 34, NULL, '2026-03-30', '2026-06-10', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-17 02:30:00', 'terminated', 'Found a job in another city.', '2026-06-10 02:00:00', NULL, NULL, 266, 266, '2026-03-18 02:30:00', '2026-06-09 16:00:00'),
(81, 81, 81, 35, NULL, '2025-09-30', '2026-02-28', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-16 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 266, 266, '2025-09-17 03:30:00', '2026-02-27 16:00:00'),
(82, 82, 82, 35, NULL, '2026-03-10', '2026-09-22', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-23 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-09-22 02:00:00', NULL, NULL, 266, 266, '2026-02-24 04:30:00', '2026-09-21 16:00:00'),
(83, 83, 83, 36, NULL, '2025-09-02', '2026-04-30', 2600.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-24 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', NULL, NULL, 266, 266, '2025-08-25 05:30:00', '2026-04-29 16:00:00'),
(84, 84, 84, 37, NULL, '2025-09-14', '2026-01-14', 7800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-04 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-14 02:00:00', NULL, NULL, 266, 266, '2025-09-05 06:30:00', '2026-01-13 16:00:00'),
(85, 85, 85, 37, NULL, '2026-01-24', '2026-04-12', 7800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-13 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-04-12 02:00:00', NULL, NULL, 266, 266, '2026-01-14 07:30:00', '2026-04-11 16:00:00');

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
(1, 1, 1, 'Aircon not cooling', 'maintenance_repairs', 'The aircon in Room 101 blows air but it is not cold anymore since last night.', NULL, 'non_urgent', 'resolved', 268, '2026-09-13 03:30:00', '2026-09-11 03:30:00', '2026-09-11 07:30:00'),
(2, 2, 2, 'Sparking outlet near Bed 2', 'electrical_issue', 'The outlet beside my bed sparked when I plugged in my charger. I stopped using it.', NULL, 'urgent', 'open', NULL, NULL, '2026-09-30 14:13:55', '2026-09-30 15:13:55'),
(3, 5, 5, 'Low water pressure in CR', 'plumbing_water_emergency', 'Mahina po ang tulo ng tubig sa shower tuwing 6-7 AM.', NULL, 'non_urgent', 'in_progress', 268, NULL, '2026-09-27 07:30:00', '2026-09-27 09:30:00'),
(4, 8, 8, 'Noisy neighbors after curfew', 'noise_roommate_concern', 'May maingay po sa kabilang room (104?) past 12 midnight, 3 nights na.', NULL, 'non_urgent', 'seen', NULL, NULL, '2026-09-29 02:30:00', '2026-09-29 03:30:00'),
(5, 11, 12, 'Request for a bigger study table in the lobby', 'suggestion_feedback', 'Suggestion lang po: sana may mas malaking study table sa lobby for group study.', NULL, 'non_urgent', 'rejected', 266, NULL, '2026-09-16 05:30:00', '2026-09-16 07:30:00'),
(6, 12, 13, 'Question about my rejected GCash payment', 'billing_payment_concern', 'Bakit po na-reject yung payment ko? Nagbayad naman po ako.', NULL, 'non_urgent', 'in_progress', 267, NULL, '2026-09-30 14:13:55', '2026-09-30 16:13:55'),
(7, 15, 16, 'Broken door lock', 'security_concern', 'Hindi po nagla-lock nang maayos ang pinto ng Room 201, kailangan pang itulak.', NULL, 'urgent', 'resolved', 268, '2026-09-24 09:30:00', '2026-09-22 09:30:00', '2026-09-22 11:30:00'),
(8, 18, 20, 'WiFi keeps disconnecting', 'facilities_amenities', 'The WiFi on the 2nd floor drops every 10-15 minutes, hard to attend online classes.', NULL, 'non_urgent', 'open', NULL, NULL, '2026-09-30 04:30:00', '2026-09-30 05:30:00'),
(9, 20, 24, 'Ceiling leak in solo room', 'structural_damage', 'May tumutulo po sa kisame tuwing malakas ang ulan, malapit sa bintana.', NULL, 'urgent', 'in_progress', 268, NULL, '2026-09-28 06:30:00', '2026-09-28 09:30:00');

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
(80, '2026_09_29_000002_add_photo_updated_at_to_vr_scenes', 52),
(81, '2026_09_30_000001_create_tenant_notifications_and_deposit_refunds', 53),
(82, '2026_09_30_000002_create_monthly_expenses_table', 54);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_expenses`
--

CREATE TABLE `monthly_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `month` date NOT NULL,
  `electricity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `water` decimal(10,2) NOT NULL DEFAULT 0.00,
  `internet` decimal(10,2) NOT NULL DEFAULT 0.00,
  `salaries` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_notes` varchar(255) DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monthly_expenses`
--

INSERT INTO `monthly_expenses` (`id`, `month`, `electricity`, `water`, `internet`, `salaries`, `other`, `other_notes`, `recorded_by`, `created_at`, `updated_at`) VALUES
(1, '2025-10-01', 42670.00, 5199.00, 3499.00, 27000.00, 0.00, NULL, 267, '2025-10-31 15:59:59', '2025-10-31 15:59:59'),
(2, '2025-11-01', 46784.00, 5734.00, 3499.00, 27000.00, 4800.00, 'Replacement of water pump capacitor', 267, '2025-11-30 15:59:59', '2025-11-30 15:59:59'),
(3, '2025-12-01', 46617.00, 4774.00, 3499.00, 54000.00, 2500.00, 'Pest control', 267, '2025-12-31 15:59:59', '2025-12-31 15:59:59'),
(4, '2026-01-01', 45813.00, 4281.00, 3499.00, 27000.00, 1649.00, 'Cleaning supplies and toiletries for common areas', 267, '2026-01-31 15:59:59', '2026-01-31 15:59:59'),
(5, '2026-02-01', 44493.00, 4409.00, 3499.00, 27000.00, 12000.00, 'Repainting of hallway and lobby', 267, '2026-02-28 15:59:59', '2026-02-28 15:59:59'),
(6, '2026-03-01', 57568.00, 5310.00, 3499.00, 27000.00, 0.00, NULL, 267, '2026-03-31 15:59:59', '2026-03-31 15:59:59'),
(7, '2026-04-01', 59788.00, 4577.00, 3499.00, 27000.00, 1985.00, 'Cleaning supplies and toiletries for common areas', 267, '2026-04-30 15:59:59', '2026-04-30 15:59:59'),
(8, '2026-05-01', 56679.00, 4103.00, 3499.00, 27000.00, 3200.00, 'Plumbing repair, 2F shared CR', 267, '2026-05-31 15:59:59', '2026-05-31 15:59:59'),
(9, '2026-06-01', 42797.00, 5220.00, 3499.00, 27000.00, 1286.00, 'Cleaning supplies and toiletries for common areas', 267, '2026-06-30 15:59:59', '2026-06-30 15:59:59'),
(10, '2026-07-01', 41390.00, 4994.00, 3499.00, 27000.00, 1472.00, 'Cleaning supplies and toiletries for common areas', 267, '2026-07-31 15:59:59', '2026-07-31 15:59:59'),
(11, '2026-08-01', 42358.00, 5390.00, 3499.00, 27000.00, 8500.00, 'Aircon cleaning (all rooms)', 267, '2026-08-31 15:59:59', '2026-08-31 15:59:59'),
(12, '2026-09-01', 44488.00, 5433.00, 3499.00, 27000.00, 0.00, NULL, 267, '2026-09-30 15:59:59', '2026-09-30 15:59:59');

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
(1, 1, 1, 7000.00, 'cash', 'Cash Payment', NULL, '2026-04-20', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-20 02:15:00'),
(2, 2, 1, 4250.00, 'gcash', 'GCash', '1000048319271', '2026-04-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-26 02:00:00', NULL, '2026-04-25 03:15:00'),
(3, 3, 1, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1002', '2026-05-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-25 02:00:00', NULL, '2026-05-24 04:15:00'),
(4, 4, 1, 4250.00, 'cash', 'Cash Payment', NULL, '2026-06-23', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-23 05:15:00'),
(5, 5, 1, 4250.00, 'gcash', 'GCash', '1000048415813', '2026-07-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-23 02:00:00', NULL, '2026-07-22 06:15:00'),
(6, 6, 1, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1004', '2026-08-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-27 02:00:00', NULL, '2026-08-26 07:15:00'),
(7, 7, 1, 4250.00, 'cash', 'Cash Payment', NULL, '2026-09-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-09-25 08:15:00'),
(8, 8, 2, 7000.00, 'cash', 'Cash Payment', NULL, '2026-06-28', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-28 09:15:00'),
(9, 9, 2, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1005', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-03 02:00:00', NULL, '2026-07-02 01:15:00'),
(10, 10, 2, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-31', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-31 02:15:00'),
(11, 11, 2, 4250.00, 'gcash', 'GCash', '1000048560626', '2026-08-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-31 02:00:00', NULL, '2026-08-30 03:15:00'),
(12, 13, 3, 7000.00, 'cash', 'Cash Payment', NULL, '2026-07-26', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-07-26 05:15:00'),
(13, 14, 3, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-29', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-29 06:15:00'),
(14, 15, 3, 4250.00, 'gcash', 'GCash', '1000048608897', '2026-08-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-29 02:00:00', NULL, '2026-08-28 07:15:00'),
(15, 16, 3, 4250.00, 'gcash', 'GCash', '1000048657168', '2026-10-01', 'pending', 'demo/sample-payment-proof.png', 'Time of payment: 08:42. Full payment for this month po.', NULL, NULL, NULL, NULL, '2026-10-01 08:15:00'),
(16, 17, 4, 6800.00, 'cash', 'Cash Payment', NULL, '2026-07-18', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-07-18 09:15:00'),
(17, 18, 4, 4150.00, 'gcash', 'GCash', '1000048705439', '2026-07-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-21 02:00:00', NULL, '2026-07-20 01:15:00'),
(18, 19, 4, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1010', '2026-08-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-25 02:00:00', NULL, '2026-08-24 02:15:00'),
(19, 20, 4, 4150.00, 'cash', 'Cash Payment', NULL, '2026-09-23', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-09-23 03:15:00'),
(20, 21, 5, 9000.00, 'cash', 'Cash Payment', NULL, '2026-03-15', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-15 04:15:00'),
(21, 22, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1011', '2026-03-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-22 02:00:00', NULL, '2026-03-21 05:15:00'),
(22, 23, 5, 5400.00, 'cash', 'Cash Payment', NULL, '2026-04-20', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-20 06:15:00'),
(23, 24, 5, 5850.00, 'gcash', 'GCash', '1000048850252', '2026-05-27', 'approved', 'demo/sample-payment-proof.png', 'Sorry po late, na-delay sweldo.', NULL, 267, '2026-05-28 02:00:00', NULL, '2026-05-27 07:15:00'),
(24, 25, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1013', '2026-06-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-19 02:00:00', NULL, '2026-06-18 08:15:00'),
(25, 26, 5, 5400.00, 'cash', 'Cash Payment', NULL, '2026-07-17', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-17 09:15:00'),
(26, 27, 5, 5400.00, 'gcash', 'GCash', '1000048946794', '2026-08-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-22 02:00:00', NULL, '2026-08-21 01:15:00'),
(27, 28, 5, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1015', '2026-09-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-21 02:00:00', NULL, '2026-09-20 02:15:00'),
(28, 29, 6, 5200.00, 'cash', 'Cash Payment', NULL, '2026-06-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-16 03:15:00'),
(29, 30, 6, 3200.00, 'cash', 'Cash Payment', NULL, '2026-06-21', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-21 04:15:00'),
(30, 31, 6, 3200.00, 'gcash', 'GCash', '1000049043336', '2026-07-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-21 02:00:00', NULL, '2026-07-20 05:15:00'),
(31, 32, 6, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1017', '2026-08-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-20 02:00:00', NULL, '2026-08-19 06:15:00'),
(32, 34, 7, 6500.00, 'cash', 'Cash Payment', NULL, '2026-08-25', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-08-25 08:15:00'),
(33, 35, 7, 4000.00, 'gcash', 'GCash', '1000049139878', '2026-08-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-30 02:00:00', NULL, '2026-08-29 09:15:00'),
(34, 36, 7, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1019', '2026-09-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-29 02:00:00', NULL, '2026-09-28 01:15:00'),
(35, 37, 8, 6500.00, 'cash', 'Cash Payment', NULL, '2026-05-27', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-05-27 02:15:00'),
(36, 38, 8, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1020', '2026-05-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-31 02:00:00', NULL, '2026-05-30 03:15:00'),
(37, 39, 8, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-29', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-29 04:15:00'),
(38, 40, 8, 4000.00, 'gcash', 'GCash', '1000049284691', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-03 02:00:00', NULL, '2026-08-02 05:15:00'),
(39, 41, 8, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1022', '2026-09-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-02 02:00:00', NULL, '2026-09-01 06:15:00'),
(40, 42, 8, 2000.00, 'cash', 'Cash Payment', NULL, '2026-09-30', 'approved', NULL, 'Partial muna po, babayaran ko yung natitira sa sweldo.', NULL, NULL, NULL, 267, '2026-09-30 07:15:00'),
(41, 43, 9, 6500.00, 'cash', 'Cash Payment', NULL, '2026-06-22', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-22 08:15:00'),
(42, 44, 9, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-24', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-24 09:15:00'),
(43, 45, 9, 4000.00, 'gcash', 'GCash', '1000049381233', '2026-07-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-29 02:00:00', NULL, '2026-07-28 01:15:00'),
(44, 46, 9, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1024', '2026-08-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-28 02:00:00', NULL, '2026-08-27 02:15:00'),
(45, 48, 10, 15600.00, 'cash', 'Cash Payment', NULL, '2026-04-18', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-18 04:15:00'),
(46, 49, 10, 9100.00, 'gcash', 'GCash', '1000049477775', '2026-04-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-25 02:00:00', NULL, '2026-04-24 05:15:00'),
(47, 50, 10, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1026', '2026-05-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-24 02:00:00', NULL, '2026-05-23 06:15:00'),
(48, 51, 10, 9100.00, 'cash', 'Cash Payment', NULL, '2026-06-22', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-22 07:15:00'),
(49, 52, 10, 9100.00, 'gcash', 'GCash', '1000049574317', '2026-07-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-22 02:00:00', NULL, '2026-07-21 08:15:00'),
(50, 53, 10, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1028', '2026-08-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-21 02:00:00', NULL, '2026-08-20 09:15:00'),
(51, 54, 10, 9100.00, 'cash', 'Cash Payment', NULL, '2026-09-24', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-09-24 01:15:00'),
(52, 55, 11, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-10', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-01-10 02:15:00'),
(53, 56, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1029', '2026-01-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-16 02:00:00', NULL, '2026-01-15 03:15:00'),
(54, 57, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-02-14', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-14 04:15:00'),
(55, 58, 11, 2850.00, 'gcash', 'GCash', '1000049719130', '2026-03-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-14 02:00:00', NULL, '2026-03-13 05:15:00'),
(56, 59, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1031', '2026-04-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-13 02:00:00', NULL, '2026-04-12 06:15:00'),
(57, 60, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-05-16', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-16 07:15:00'),
(58, 61, 11, 2850.00, 'gcash', 'GCash', '1000049815672', '2026-06-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-16 02:00:00', NULL, '2026-06-15 08:15:00'),
(59, 62, 11, 2850.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1033', '2026-07-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-15 02:00:00', NULL, '2026-07-14 09:15:00'),
(60, 63, 11, 2850.00, 'cash', 'Cash Payment', NULL, '2026-08-13', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-13 01:15:00'),
(61, 64, 11, 2850.00, 'gcash', 'GCash', '1000049912214', '2026-09-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-13 02:00:00', NULL, '2026-09-12 02:15:00'),
(62, 65, 12, 5000.00, 'cash', 'Cash Payment', NULL, '2026-07-29', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-07-29 03:15:00'),
(63, 66, 12, 3100.00, 'cash', 'Cash Payment', NULL, '2026-08-02', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-02 04:15:00'),
(64, 67, 12, 3100.00, 'gcash', 'GCash', '1000049960485', '2026-09-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-02 02:00:00', NULL, '2026-09-01 05:15:00'),
(65, 68, 12, 3100.00, 'gcash', 'GCash', '1000050008756', '2026-09-30', 'rejected', 'demo/sample-payment-proof.png', 'Bayad ko po for this month.', 'Screenshot is cropped -- the reference number and amount are not visible. Please upload the full receipt.', 267, '2026-10-01 02:00:00', NULL, '2026-09-30 06:15:00'),
(66, 69, 13, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-19', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-19 07:15:00'),
(67, 70, 13, 3100.00, 'gcash', 'GCash', '1000050057027', '2026-06-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-23 02:00:00', NULL, '2026-06-22 08:15:00'),
(68, 71, 13, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1038', '2026-07-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-22 02:00:00', NULL, '2026-07-21 09:15:00'),
(69, 72, 13, 3100.00, 'cash', 'Cash Payment', NULL, '2026-08-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-25 01:15:00'),
(70, 74, 14, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-30', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-30 03:15:00'),
(71, 75, 14, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1039', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-03 02:00:00', NULL, '2026-07-02 04:15:00'),
(72, 76, 14, 3100.00, 'cash', 'Cash Payment', NULL, '2026-08-06', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-06 05:15:00'),
(73, 77, 14, 3100.00, 'gcash', 'GCash', '1000050201840', '2026-09-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-06 02:00:00', NULL, '2026-09-05 06:15:00'),
(74, 79, 15, 5000.00, 'cash', 'Cash Payment', NULL, '2026-06-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-23 08:15:00'),
(75, 80, 15, 3100.00, 'cash', 'Cash Payment', NULL, '2026-06-29', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-29 09:15:00'),
(76, 81, 15, 3100.00, 'gcash', 'GCash', '1000050250111', '2026-07-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-29 02:00:00', NULL, '2026-07-28 01:15:00'),
(77, 82, 15, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1042', '2026-08-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-28 02:00:00', NULL, '2026-08-27 02:15:00'),
(78, 83, 15, 3100.00, 'cash', 'Cash Payment', NULL, '2026-09-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-09-26 03:15:00'),
(79, 85, 17, 9500.00, 'cash', 'Cash Payment', NULL, '2026-07-21', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-07-21 05:15:00'),
(80, 86, 17, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1043', '2026-07-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-26 02:00:00', NULL, '2026-07-25 06:15:00'),
(81, 87, 17, 5650.00, 'cash', 'Cash Payment', NULL, '2026-08-24', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-24 07:15:00'),
(82, 88, 17, 5650.00, 'gcash', 'GCash', '1000050394924', '2026-09-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-24 02:00:00', NULL, '2026-09-23 08:15:00'),
(83, 89, 18, 7000.00, 'cash', 'Cash Payment', NULL, '2026-05-12', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-05-12 09:15:00'),
(84, 90, 18, 4250.00, 'cash', 'Cash Payment', NULL, '2026-05-15', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-15 01:15:00'),
(85, 91, 18, 4250.00, 'gcash', 'GCash', '1000050443195', '2026-06-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-15 02:00:00', NULL, '2026-06-14 02:15:00'),
(86, 92, 18, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1046', '2026-07-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-19 02:00:00', NULL, '2026-07-18 03:15:00'),
(87, 93, 18, 4250.00, 'cash', 'Cash Payment', NULL, '2026-08-17', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-17 04:15:00'),
(88, 94, 18, 4250.00, 'gcash', 'GCash', '1000050539737', '2026-09-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-17 02:00:00', NULL, '2026-09-16 05:15:00'),
(89, 95, 19, 7000.00, 'gcash', 'GCash', '1000050588008', '2026-09-30', 'pending', 'demo/sample-payment-proof.png', 'Move-in fee (deposit + advance). Sent via GCash po.', NULL, NULL, NULL, NULL, '2026-09-30 06:15:00'),
(90, 96, 20, 16000.00, 'cash', 'Cash Payment', NULL, '2026-04-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-24 07:15:00'),
(91, 97, 20, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1049', '2026-04-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-01 02:00:00', NULL, '2026-04-30 08:15:00'),
(92, 98, 20, 9300.00, 'cash', 'Cash Payment', NULL, '2026-05-29', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-29 09:15:00'),
(93, 99, 20, 9300.00, 'gcash', 'GCash', '1000050684550', '2026-06-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-29 02:00:00', NULL, '2026-06-28 01:15:00'),
(94, 100, 20, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1051', '2026-07-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-28 02:00:00', NULL, '2026-07-27 02:15:00'),
(95, 101, 20, 9300.00, 'cash', 'Cash Payment', NULL, '2026-08-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-26 03:15:00'),
(96, 102, 20, 9300.00, 'gcash', 'GCash', '1000050781092', '2026-09-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-10-01 02:00:00', NULL, '2026-09-30 04:15:00'),
(97, 103, 21, 6800.00, 'cash', 'Cash Payment', NULL, '2026-06-17', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-17 05:15:00'),
(98, 104, 21, 4150.00, 'cash', 'Cash Payment', NULL, '2026-06-22', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-22 06:15:00'),
(99, 105, 21, 4150.00, 'gcash', 'GCash', '1000050829363', '2026-07-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-22 02:00:00', NULL, '2026-07-21 07:15:00'),
(100, 106, 21, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1054', '2026-08-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-21 02:00:00', NULL, '2026-08-20 08:15:00'),
(101, 107, 21, 4150.00, 'cash', 'Cash Payment', NULL, '2026-09-19', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-09-19 09:15:00'),
(102, 108, 22, 6800.00, 'cash', 'Cash Payment', NULL, '2026-08-26', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-08-26 01:15:00'),
(103, 109, 22, 4150.00, 'gcash', 'GCash', '1000050925905', '2026-08-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-31 02:00:00', NULL, '2026-08-30 02:15:00'),
(104, 110, 22, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1056', '2026-09-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-30 02:00:00', NULL, '2026-09-29 03:15:00'),
(105, 111, 23, 9000.00, 'cash', 'Cash Payment', NULL, '2026-03-22', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-22 04:15:00'),
(106, 112, 23, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1057', '2026-03-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-26 02:00:00', NULL, '2026-03-25 05:15:00'),
(107, 113, 23, 5400.00, 'cash', 'Cash Payment', NULL, '2026-04-24', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-24 06:15:00'),
(108, 114, 23, 5400.00, 'gcash', 'GCash', '1000051070718', '2026-05-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-29 02:00:00', NULL, '2026-05-28 07:15:00'),
(109, 115, 23, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1059', '2026-06-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-28 02:00:00', NULL, '2026-06-27 08:15:00'),
(110, 116, 23, 5400.00, 'cash', 'Cash Payment', NULL, '2026-07-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-26 09:15:00'),
(111, 117, 24, 5200.00, 'cash', 'Cash Payment', NULL, '2026-04-11', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-11 01:15:00'),
(112, 118, 24, 3200.00, 'cash', 'Cash Payment', NULL, '2026-04-13', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-13 02:15:00'),
(113, 119, 24, 3200.00, 'gcash', 'GCash', '1000051167260', '2026-05-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-18 02:00:00', NULL, '2026-05-17 03:15:00'),
(114, 120, 24, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1061', '2026-06-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-17 02:00:00', NULL, '2026-06-16 04:15:00'),
(115, 121, 24, 3200.00, 'cash', 'Cash Payment', NULL, '2026-07-15', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-15 05:15:00'),
(116, 123, 25, 7000.00, 'cash', 'Cash Payment', NULL, '2026-02-14', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-02-14 07:15:00'),
(117, 124, 25, 4250.00, 'gcash', 'GCash', '1000051263802', '2026-02-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-21 02:00:00', NULL, '2026-02-20 08:15:00'),
(118, 125, 25, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1063', '2026-03-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-20 02:00:00', NULL, '2026-03-19 09:15:00'),
(119, 126, 25, 4250.00, 'cash', 'Cash Payment', NULL, '2026-04-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-18 01:15:00'),
(120, 127, 25, 4250.00, 'gcash', 'GCash', '1000051360344', '2026-05-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-18 02:00:00', NULL, '2026-05-17 02:15:00'),
(121, 128, 25, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1065', '2026-06-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-17 02:00:00', NULL, '2026-06-16 03:15:00'),
(122, 129, 25, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-20', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-20 04:15:00'),
(123, 130, 25, 4250.00, 'gcash', 'GCash', '1000051456886', '2026-08-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-20 02:00:00', NULL, '2026-08-19 05:15:00'),
(124, 131, 25, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1067', '2026-09-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-19 02:00:00', NULL, '2026-09-18 06:15:00'),
(125, 132, 26, 9000.00, 'cash', 'Cash Payment', NULL, '2026-06-19', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-19 07:15:00'),
(126, 133, 26, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1068', '2026-06-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-25 02:00:00', NULL, '2026-06-24 08:15:00'),
(127, 134, 26, 5400.00, 'cash', 'Cash Payment', NULL, '2026-07-23', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-23 09:15:00'),
(128, 135, 26, 5400.00, 'gcash', 'GCash', '1000051601699', '2026-08-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-23 02:00:00', NULL, '2026-08-22 01:15:00'),
(129, 136, 26, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1070', '2026-09-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-22 02:00:00', NULL, '2026-09-21 02:15:00'),
(130, 137, 27, 6500.00, 'cash', 'Cash Payment', NULL, '2026-07-21', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-07-21 03:15:00'),
(131, 138, 27, 4000.00, 'cash', 'Cash Payment', NULL, '2026-07-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-25 04:15:00'),
(132, 139, 27, 4000.00, 'gcash', 'GCash', '1000051698241', '2026-08-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-25 02:00:00', NULL, '2026-08-24 05:15:00'),
(133, 140, 27, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1072', '2026-09-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-24 02:00:00', NULL, '2026-09-23 06:15:00'),
(134, 141, 28, 9000.00, 'cash', 'Cash Payment', NULL, '2026-09-09', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-09-09 07:15:00'),
(135, 142, 28, 5400.00, 'gcash', 'GCash', '1000051794783', '2026-10-01', 'pending', 'demo/sample-payment-proof.png', 'Time of payment: 08:42. Full payment for this month po.', NULL, NULL, NULL, NULL, '2026-10-01 08:15:00'),
(136, 143, 29, 5200.00, 'cash', 'Cash Payment', NULL, '2026-09-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-09-16 09:15:00'),
(137, 144, 29, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1074', '2026-09-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-19 02:00:00', NULL, '2026-09-18 01:15:00'),
(138, 145, 30, 5200.00, 'cash', 'Cash Payment', NULL, '2026-09-28', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-09-28 02:15:00'),
(139, 147, 31, 5200.00, 'cash', 'Cash Payment', NULL, '2026-05-06', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-05-06 04:15:00'),
(140, 148, 31, 3200.00, 'gcash', 'GCash', '1000051891325', '2026-05-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-12 02:00:00', NULL, '2026-05-11 05:15:00'),
(141, 149, 31, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1076', '2026-06-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-11 02:00:00', NULL, '2026-06-10 06:15:00'),
(142, 150, 31, 3200.00, 'cash', 'Cash Payment', NULL, '2026-07-09', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-09 07:15:00'),
(143, 151, 31, 3200.00, 'gcash', 'GCash', '1000051987867', '2026-08-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-09 02:00:00', NULL, '2026-08-08 08:15:00'),
(144, 152, 31, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1078', '2026-09-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-13 02:00:00', NULL, '2026-09-12 09:15:00'),
(145, 153, 32, 7000.00, 'cash', 'Cash Payment', NULL, '2025-08-31', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-08-31 01:15:00'),
(146, 154, 32, 4250.00, 'gcash', 'GCash', '1000052084409', '2025-09-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-06 02:00:00', NULL, '2025-09-05 02:15:00'),
(147, 155, 32, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1080', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-05 02:00:00', NULL, '2025-10-04 03:15:00'),
(148, 156, 32, 4250.00, 'gcash', 'GCash', '1000052180951', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-04 02:00:00', NULL, '2025-11-03 04:15:00'),
(149, 157, 32, 4250.00, 'cash', 'Cash Payment', NULL, '2025-12-02', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-02 05:15:00'),
(150, 158, 32, 4250.00, 'gcash', 'GCash', '1000052229222', '2026-01-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-07 02:00:00', NULL, '2026-01-06 06:15:00'),
(151, 159, 32, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1083', '2026-02-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-06 02:00:00', NULL, '2026-02-05 07:15:00'),
(152, 160, 33, 7000.00, 'cash', 'Cash Payment', NULL, '2025-09-06', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-06 08:15:00'),
(153, 161, 33, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1085', '2025-09-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-11 02:00:00', NULL, '2025-09-10 09:15:00'),
(154, 162, 33, 4250.00, 'gcash', 'GCash', '1000052422306', '2025-10-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-10 02:00:00', NULL, '2025-10-09 01:15:00'),
(155, 163, 33, 4250.00, 'cash', 'Cash Payment', NULL, '2025-11-08', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-08 02:15:00'),
(156, 164, 33, 4250.00, 'gcash', 'GCash', '1000052470577', '2025-12-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-13 02:00:00', NULL, '2025-12-12 03:15:00'),
(157, 165, 33, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1088', '2026-01-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-12 02:00:00', NULL, '2026-01-11 04:15:00'),
(158, 166, 34, 7000.00, 'cash', 'Cash Payment', NULL, '2026-03-18', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-18 05:15:00'),
(159, 167, 34, 4250.00, 'gcash', 'GCash', '1000052567119', '2026-03-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-22 02:00:00', NULL, '2026-03-21 06:15:00'),
(160, 168, 34, 4250.00, 'cash', 'Cash Payment', NULL, '2026-04-20', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-20 07:15:00'),
(161, 169, 34, 4250.00, 'gcash', 'GCash', '1000052615390', '2026-05-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-25 02:00:00', NULL, '2026-05-24 08:15:00'),
(162, 170, 34, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1091', '2026-06-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-24 02:00:00', NULL, '2026-06-23 09:15:00'),
(163, 171, 35, 7000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-24 01:15:00'),
(164, 172, 35, 4250.00, 'cash', 'Cash Payment', NULL, '2025-09-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-26 02:15:00'),
(165, 173, 35, 4250.00, 'gcash', 'GCash', '1000052760203', '2025-10-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-31 02:00:00', NULL, '2025-10-30 03:15:00'),
(166, 174, 35, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1094', '2025-11-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-30 02:00:00', NULL, '2025-11-29 04:15:00'),
(167, 175, 35, 4250.00, 'gcash', 'GCash', '1000052856745', '2025-12-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-29 02:00:00', NULL, '2025-12-28 05:15:00'),
(168, 176, 35, 4250.00, 'cash', 'Cash Payment', NULL, '2026-01-27', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-27 06:15:00'),
(169, 177, 36, 7000.00, 'cash', 'Cash Payment', NULL, '2026-04-17', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-17 07:15:00'),
(170, 178, 36, 4250.00, 'gcash', 'GCash', '1000052905016', '2026-04-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-24 02:00:00', NULL, '2026-04-23 08:15:00'),
(171, 179, 36, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1097', '2026-05-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-23 02:00:00', NULL, '2026-05-22 09:15:00'),
(172, 180, 36, 4250.00, 'gcash', 'GCash', '1000053001558', '2026-06-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-22 02:00:00', NULL, '2026-06-21 01:15:00'),
(173, 181, 36, 4250.00, 'cash', 'Cash Payment', NULL, '2026-07-20', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-20 02:15:00'),
(174, 182, 37, 7000.00, 'cash', 'Cash Payment', NULL, '2025-09-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-23 03:15:00'),
(175, 183, 37, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1100', '2025-09-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-29 02:00:00', NULL, '2025-09-28 04:15:00'),
(176, 184, 37, 4250.00, 'gcash', 'GCash', '1000053146371', '2025-10-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-28 02:00:00', NULL, '2025-10-27 05:15:00'),
(177, 185, 37, 4250.00, 'cash', 'Cash Payment', NULL, '2025-11-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-26 06:15:00'),
(178, 186, 37, 4250.00, 'gcash', 'GCash', '1000053194642', '2025-12-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-26 02:00:00', NULL, '2025-12-25 07:15:00'),
(179, 187, 38, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-23 08:15:00'),
(180, 188, 38, 5400.00, 'gcash', 'GCash', '1000053242913', '2025-09-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-28 02:00:00', NULL, '2025-09-27 09:15:00'),
(181, 189, 38, 5400.00, 'cash', 'Cash Payment', NULL, '2025-10-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-26 01:15:00'),
(182, 190, 38, 5400.00, 'gcash', 'GCash', '1000053291184', '2025-11-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-26 02:00:00', NULL, '2025-11-25 02:15:00'),
(183, 191, 38, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1105', '2025-12-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-30 02:00:00', NULL, '2025-12-29 03:15:00'),
(184, 192, 38, 5400.00, 'gcash', 'GCash', '1000053387726', '2026-01-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-29 02:00:00', NULL, '2026-01-28 04:15:00'),
(185, 193, 38, 5400.00, 'cash', 'Cash Payment', NULL, '2026-02-27', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-27 05:15:00'),
(186, 194, 39, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-12', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-12 06:15:00'),
(187, 195, 39, 5400.00, 'cash', 'Cash Payment', NULL, '2025-09-15', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-15 07:15:00'),
(188, 196, 39, 5400.00, 'gcash', 'GCash', '1000053484268', '2025-10-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-15 02:00:00', NULL, '2025-10-14 08:15:00'),
(189, 197, 39, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1109', '2025-11-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-19 02:00:00', NULL, '2025-11-18 09:15:00'),
(190, 198, 39, 5400.00, 'gcash', 'GCash', '1000053580810', '2025-12-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-18 02:00:00', NULL, '2025-12-17 01:15:00'),
(191, 199, 39, 5400.00, 'cash', 'Cash Payment', NULL, '2026-01-16', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-16 02:15:00'),
(192, 200, 39, 5400.00, 'gcash', 'GCash', '1000053629081', '2026-02-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-16 02:00:00', NULL, '2026-02-15 03:15:00'),
(193, 201, 39, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1112', '2026-03-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-15 02:00:00', NULL, '2026-03-14 04:15:00'),
(194, 202, 40, 6500.00, 'cash', 'Cash Payment', NULL, '2025-09-09', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-09 05:15:00'),
(195, 203, 40, 4000.00, 'gcash', 'GCash', '1000053725623', '2025-09-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-12 02:00:00', NULL, '2025-09-11 06:15:00'),
(196, 204, 40, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1114', '2025-10-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-16 02:00:00', NULL, '2025-10-15 07:15:00'),
(197, 205, 40, 4000.00, 'gcash', 'GCash', '1000053822165', '2025-11-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-15 02:00:00', NULL, '2025-11-14 08:15:00'),
(198, 206, 40, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-13', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-13 09:15:00'),
(199, 207, 40, 4000.00, 'gcash', 'GCash', '1000053870436', '2026-01-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-13 02:00:00', NULL, '2026-01-12 01:15:00'),
(200, 208, 41, 6500.00, 'cash', 'Cash Payment', NULL, '2026-03-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-24 02:15:00'),
(201, 209, 41, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1118', '2026-03-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-31 02:00:00', NULL, '2026-03-30 03:15:00'),
(202, 210, 41, 4000.00, 'gcash', 'GCash', '1000054015249', '2026-04-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-30 02:00:00', NULL, '2026-04-29 04:15:00'),
(203, 211, 41, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-28', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-28 05:15:00'),
(204, 212, 41, 4000.00, 'gcash', 'GCash', '1000054063520', '2026-06-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-28 02:00:00', NULL, '2026-06-27 06:15:00'),
(205, 213, 41, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1121', '2026-07-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-27 02:00:00', NULL, '2026-07-26 07:15:00'),
(206, 214, 42, 6500.00, 'cash', 'Cash Payment', NULL, '2025-09-22', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-22 08:15:00'),
(207, 215, 42, 4000.00, 'gcash', 'GCash', '1000054160062', '2025-09-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-28 02:00:00', NULL, '2025-09-27 09:15:00'),
(208, 216, 42, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-26 01:15:00'),
(209, 217, 42, 4000.00, 'gcash', 'GCash', '1000054208333', '2025-11-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-26 02:00:00', NULL, '2025-11-25 02:15:00'),
(210, 218, 42, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1124', '2025-12-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-25 02:00:00', NULL, '2025-12-24 03:15:00'),
(211, 219, 42, 4000.00, 'gcash', 'GCash', '1000054304875', '2026-01-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-29 02:00:00', NULL, '2026-01-28 04:15:00'),
(212, 220, 42, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-27', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-27 05:15:00'),
(213, 221, 43, 6500.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-20 06:15:00'),
(214, 222, 43, 4000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-24 07:15:00'),
(215, 223, 43, 4000.00, 'gcash', 'GCash', '1000054401417', '2025-10-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-24 02:00:00', NULL, '2025-10-23 08:15:00'),
(216, 224, 43, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1128', '2025-11-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-23 02:00:00', NULL, '2025-11-22 09:15:00'),
(217, 225, 43, 4000.00, 'gcash', 'GCash', '1000054497959', '2025-12-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-27 02:00:00', NULL, '2025-12-26 01:15:00'),
(218, 226, 43, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-25 02:15:00'),
(219, 227, 43, 4000.00, 'gcash', 'GCash', '1000054546230', '2026-02-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-25 02:00:00', NULL, '2026-02-24 03:15:00'),
(220, 228, 44, 6500.00, 'cash', 'Cash Payment', NULL, '2025-10-11', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-11 04:15:00'),
(221, 229, 44, 4000.00, 'gcash', 'GCash', '1000054594501', '2025-10-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-15 02:00:00', NULL, '2025-10-14 05:15:00'),
(222, 230, 44, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1132', '2025-11-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-14 02:00:00', NULL, '2025-11-13 06:15:00'),
(223, 231, 44, 4000.00, 'gcash', 'GCash', '1000054691043', '2025-12-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-18 02:00:00', NULL, '2025-12-17 07:15:00'),
(224, 232, 44, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-16', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-16 08:15:00'),
(225, 233, 44, 4000.00, 'gcash', 'GCash', '1000054739314', '2026-02-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-16 02:00:00', NULL, '2026-02-15 09:15:00'),
(226, 234, 44, 4000.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1135', '2026-03-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-15 02:00:00', NULL, '2026-03-14 01:15:00'),
(227, 235, 44, 4000.00, 'gcash', 'GCash', '1000054835856', '2026-04-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-14 02:00:00', NULL, '2026-04-13 02:15:00'),
(228, 236, 45, 5000.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-20 03:15:00'),
(229, 237, 45, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1138', '2025-09-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-23 02:00:00', NULL, '2025-09-22 04:15:00'),
(230, 238, 45, 3100.00, 'gcash', 'GCash', '1000054980669', '2025-10-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-27 02:00:00', NULL, '2025-10-26 05:15:00'),
(231, 239, 45, 3100.00, 'cash', 'Cash Payment', NULL, '2025-11-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-25 06:15:00'),
(232, 240, 46, 5000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-24 07:15:00'),
(233, 241, 46, 3100.00, 'gcash', 'GCash', '1000055028940', '2025-09-30', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-01 02:00:00', NULL, '2025-09-30 08:15:00'),
(234, 242, 46, 3100.00, 'cash', 'Cash Payment', NULL, '2025-10-29', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-29 09:15:00'),
(235, 243, 46, 3100.00, 'gcash', 'GCash', '1000055077211', '2025-11-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-29 02:00:00', NULL, '2025-11-28 01:15:00'),
(236, 244, 46, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1142', '2025-12-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-28 02:00:00', NULL, '2025-12-27 02:15:00'),
(237, 245, 46, 3100.00, 'gcash', 'GCash', '1000055173753', '2026-01-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-27 02:00:00', NULL, '2026-01-26 03:15:00'),
(238, 246, 46, 3100.00, 'cash', 'Cash Payment', NULL, '2026-03-02', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-02 04:15:00'),
(239, 247, 47, 5000.00, 'cash', 'Cash Payment', NULL, '2026-04-28', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-28 05:15:00'),
(240, 248, 47, 3100.00, 'cash', 'Cash Payment', NULL, '2026-05-03', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-03 06:15:00'),
(241, 249, 47, 3100.00, 'gcash', 'GCash', '1000055270295', '2026-06-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-02 02:00:00', NULL, '2026-06-01 07:15:00'),
(242, 250, 47, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1146', '2026-07-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-02 02:00:00', NULL, '2026-07-01 08:15:00'),
(243, 251, 48, 5000.00, 'cash', 'Cash Payment', NULL, '2025-10-12', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-12 09:15:00'),
(244, 252, 48, 3100.00, 'gcash', 'GCash', '1000055366837', '2025-10-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-17 02:00:00', NULL, '2025-10-16 01:15:00'),
(245, 253, 48, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1148', '2025-11-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-16 02:00:00', NULL, '2025-11-15 02:15:00'),
(246, 254, 48, 3100.00, 'gcash', 'GCash', '1000055463379', '2025-12-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-15 02:00:00', NULL, '2025-12-14 03:15:00'),
(247, 255, 48, 3100.00, 'cash', 'Cash Payment', NULL, '2026-01-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-18 04:15:00'),
(248, 256, 48, 3100.00, 'gcash', 'GCash', '1000055511650', '2026-02-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-18 02:00:00', NULL, '2026-02-17 05:15:00'),
(249, 257, 49, 5000.00, 'cash', 'Cash Payment', NULL, '2025-09-19', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-19 06:15:00'),
(250, 258, 49, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1152', '2025-09-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-23 02:00:00', NULL, '2025-09-22 07:15:00'),
(251, 259, 49, 3100.00, 'gcash', 'GCash', '1000055656463', '2025-10-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-22 02:00:00', NULL, '2025-10-21 08:15:00'),
(252, 260, 49, 3100.00, 'cash', 'Cash Payment', NULL, '2025-11-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-25 09:15:00'),
(253, 261, 49, 3100.00, 'gcash', 'GCash', '1000055704734', '2025-12-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-25 02:00:00', NULL, '2025-12-24 01:15:00'),
(254, 262, 49, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1155', '2026-01-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-24 02:00:00', NULL, '2026-01-23 02:15:00'),
(255, 263, 49, 3100.00, 'gcash', 'GCash', '1000055801276', '2026-02-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-23 02:00:00', NULL, '2026-02-22 03:15:00'),
(256, 264, 49, 3100.00, 'cash', 'Cash Payment', NULL, '2026-03-21', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-21 04:15:00'),
(257, 265, 49, 3100.00, 'gcash', 'GCash', '1000055849547', '2026-04-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-26 02:00:00', NULL, '2026-04-25 05:15:00'),
(258, 266, 50, 5000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-24 06:15:00'),
(259, 267, 50, 3100.00, 'gcash', 'GCash', '1000055897818', '2025-09-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-27 02:00:00', NULL, '2025-09-26 07:15:00'),
(260, 268, 50, 3100.00, 'cash', 'Cash Payment', NULL, '2025-10-30', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-30 08:15:00'),
(261, 269, 50, 3100.00, 'gcash', 'GCash', '1000055946089', '2025-11-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-30 02:00:00', NULL, '2025-11-29 09:15:00'),
(262, 270, 51, 5000.00, 'cash', 'Cash Payment', NULL, '2026-01-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-01-24 01:15:00'),
(263, 271, 51, 3100.00, 'cash', 'Cash Payment', NULL, '2026-01-30', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-30 02:15:00'),
(264, 272, 51, 3100.00, 'gcash', 'GCash', '1000056042631', '2026-03-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-02 02:00:00', NULL, '2026-03-01 03:15:00'),
(265, 273, 51, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1162', '2026-03-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-29 02:00:00', NULL, '2026-03-28 04:15:00'),
(266, 274, 51, 3100.00, 'gcash', 'GCash', '1000056139173', '2026-04-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-28 02:00:00', NULL, '2026-04-27 05:15:00'),
(267, 275, 51, 3100.00, 'cash', 'Cash Payment', NULL, '2026-05-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-26 06:15:00'),
(268, 276, 52, 5000.00, 'cash', 'Cash Payment', NULL, '2025-09-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-16 07:15:00'),
(269, 277, 52, 3100.00, 'gcash', 'GCash', '1000056187444', '2025-09-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-22 02:00:00', NULL, '2025-09-21 08:15:00'),
(270, 278, 52, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1165', '2025-10-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-21 02:00:00', NULL, '2025-10-20 09:15:00'),
(271, 279, 52, 3100.00, 'gcash', 'GCash', '1000056283986', '2025-11-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-20 02:00:00', NULL, '2025-11-19 01:15:00'),
(272, 280, 52, 3100.00, 'cash', 'Cash Payment', NULL, '2025-12-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-18 02:15:00'),
(273, 281, 52, 3100.00, 'gcash', 'GCash', '1000056332257', '2026-01-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-23 02:00:00', NULL, '2026-01-22 03:15:00'),
(274, 282, 52, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1168', '2026-02-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-22 02:00:00', NULL, '2026-02-21 04:15:00'),
(275, 283, 53, 5000.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-01 05:15:00'),
(276, 284, 53, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1170', '2026-04-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-06 02:00:00', NULL, '2026-04-05 06:15:00'),
(277, 285, 53, 3100.00, 'gcash', 'GCash', '1000056525341', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(278, 286, 53, 3100.00, 'cash', 'Cash Payment', NULL, '2026-06-03', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-03 08:15:00'),
(279, 287, 53, 3100.00, 'gcash', 'GCash', '1000056573612', '2026-07-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-08 02:00:00', NULL, '2026-07-07 09:15:00'),
(280, 288, 53, 3100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1173', '2026-08-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-08-07 02:00:00', NULL, '2026-08-06 01:15:00');
INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `payment_method_label`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(281, 289, 53, 3100.00, 'gcash', 'GCash', '1000056670154', '2026-09-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-06 02:00:00', NULL, '2026-09-05 02:15:00'),
(282, 290, 54, 9500.00, 'cash', 'Cash Payment', NULL, '2025-09-17', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-17 03:15:00'),
(283, 291, 54, 5650.00, 'gcash', 'GCash', '1000056718425', '2025-09-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-21 02:00:00', NULL, '2025-09-20 04:15:00'),
(284, 292, 54, 5650.00, 'cash', 'Cash Payment', NULL, '2025-10-19', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-19 05:15:00'),
(285, 293, 54, 5650.00, 'gcash', 'GCash', '1000056766696', '2025-11-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-24 02:00:00', NULL, '2025-11-23 06:15:00'),
(286, 294, 54, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1177', '2025-12-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-23 02:00:00', NULL, '2025-12-22 07:15:00'),
(287, 295, 55, 9500.00, 'cash', 'Cash Payment', NULL, '2026-03-03', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-03 08:15:00'),
(288, 296, 55, 5650.00, 'cash', 'Cash Payment', NULL, '2026-03-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-05 09:15:00'),
(289, 297, 55, 5650.00, 'gcash', 'GCash', '1000056911509', '2026-04-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-10 02:00:00', NULL, '2026-04-09 01:15:00'),
(290, 298, 55, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1180', '2026-05-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-09 02:00:00', NULL, '2026-05-08 02:15:00'),
(291, 299, 55, 5650.00, 'gcash', 'GCash', '1000057008051', '2026-06-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-08 02:00:00', NULL, '2026-06-07 03:15:00'),
(292, 300, 55, 5650.00, 'cash', 'Cash Payment', NULL, '2026-07-06', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-07-06 04:15:00'),
(293, 301, 56, 9500.00, 'cash', 'Cash Payment', NULL, '2025-10-13', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-13 05:15:00'),
(294, 302, 56, 5650.00, 'gcash', 'GCash', '1000057056322', '2025-10-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-20 02:00:00', NULL, '2025-10-19 06:15:00'),
(295, 303, 56, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1183', '2025-11-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-19 02:00:00', NULL, '2025-11-18 07:15:00'),
(296, 304, 56, 5650.00, 'gcash', 'GCash', '1000057152864', '2025-12-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-18 02:00:00', NULL, '2025-12-17 08:15:00'),
(297, 305, 57, 9500.00, 'cash', 'Cash Payment', NULL, '2026-01-31', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-01-31 09:15:00'),
(298, 306, 57, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1186', '2026-02-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-06 02:00:00', NULL, '2026-02-05 01:15:00'),
(299, 307, 57, 5650.00, 'gcash', 'GCash', '1000057297677', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-05 02:00:00', NULL, '2026-03-04 02:15:00'),
(300, 308, 57, 5650.00, 'cash', 'Cash Payment', NULL, '2026-04-03', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-03 03:15:00'),
(301, 309, 57, 5650.00, 'gcash', 'GCash', '1000057345948', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-03 02:00:00', NULL, '2026-05-02 04:15:00'),
(302, 310, 57, 5650.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1189', '2026-06-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-07 02:00:00', NULL, '2026-06-06 05:15:00'),
(303, 311, 58, 7000.00, 'cash', 'Cash Payment', NULL, '2025-09-18', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-18 06:15:00'),
(304, 312, 58, 4250.00, 'gcash', 'GCash', '1000057442490', '2025-09-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-23 02:00:00', NULL, '2025-09-22 07:15:00'),
(305, 313, 58, 4250.00, 'cash', 'Cash Payment', NULL, '2025-10-21', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-21 08:15:00'),
(306, 314, 58, 4250.00, 'gcash', 'GCash', '1000057490761', '2025-11-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-21 02:00:00', NULL, '2025-11-20 09:15:00'),
(307, 315, 58, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1192', '2025-12-24', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-25 02:00:00', NULL, '2025-12-24 01:15:00'),
(308, 316, 59, 7000.00, 'cash', 'Cash Payment', NULL, '2026-02-15', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-02-15 02:15:00'),
(309, 317, 59, 4250.00, 'cash', 'Cash Payment', NULL, '2026-02-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-18 03:15:00'),
(310, 318, 59, 4250.00, 'gcash', 'GCash', '1000057635574', '2026-03-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-18 02:00:00', NULL, '2026-03-17 04:15:00'),
(311, 319, 59, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1195', '2026-04-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-22 02:00:00', NULL, '2026-04-21 05:15:00'),
(312, 320, 60, 7000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-01 06:15:00'),
(313, 321, 60, 4250.00, 'gcash', 'GCash', '1000057732116', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-04 02:00:00', NULL, '2025-10-03 07:15:00'),
(314, 322, 60, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1197', '2025-11-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-08 02:00:00', NULL, '2025-11-07 08:15:00'),
(315, 323, 60, 4250.00, 'gcash', 'GCash', '1000057828658', '2025-12-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-07 02:00:00', NULL, '2025-12-06 09:15:00'),
(316, 324, 60, 4250.00, 'cash', 'Cash Payment', NULL, '2026-01-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-05 01:15:00'),
(317, 325, 60, 4250.00, 'gcash', 'GCash', '1000057876929', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-05 02:00:00', NULL, '2026-02-04 02:15:00'),
(318, 326, 60, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1200', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-04 02:00:00', NULL, '2026-03-03 03:15:00'),
(319, 327, 60, 4250.00, 'gcash', 'GCash', '1000057973471', '2026-04-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-08 02:00:00', NULL, '2026-04-07 04:15:00'),
(320, 328, 60, 4250.00, 'cash', 'Cash Payment', NULL, '2026-05-06', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-06 05:15:00'),
(321, 329, 61, 7000.00, 'cash', 'Cash Payment', NULL, '2026-06-17', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-06-17 06:15:00'),
(322, 330, 61, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1203', '2026-06-23', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-24 02:00:00', NULL, '2026-06-23 07:15:00'),
(323, 331, 61, 4250.00, 'gcash', 'GCash', '1000058118284', '2026-07-22', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-23 02:00:00', NULL, '2026-07-22 08:15:00'),
(324, 332, 61, 4250.00, 'cash', 'Cash Payment', NULL, '2026-08-21', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-21 09:15:00'),
(325, 333, 61, 4250.00, 'gcash', 'GCash', '1000058166555', '2026-09-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-21 02:00:00', NULL, '2026-09-20 01:15:00'),
(326, 334, 62, 7000.00, 'cash', 'Cash Payment', NULL, '2025-09-14', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-14 02:15:00'),
(327, 335, 62, 4250.00, 'gcash', 'GCash', '1000058214826', '2025-09-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-20 02:00:00', NULL, '2025-09-19 03:15:00'),
(328, 336, 62, 4250.00, 'cash', 'Cash Payment', NULL, '2025-10-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-10-18 04:15:00'),
(329, 337, 62, 4250.00, 'gcash', 'GCash', '1000058263097', '2025-11-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-18 02:00:00', NULL, '2025-11-17 05:15:00'),
(330, 338, 62, 4250.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1208', '2025-12-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-17 02:00:00', NULL, '2025-12-16 06:15:00'),
(331, 339, 62, 4250.00, 'gcash', 'GCash', '1000058359639', '2026-01-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-21 02:00:00', NULL, '2026-01-20 07:15:00'),
(332, 340, 62, 4250.00, 'cash', 'Cash Payment', NULL, '2026-02-19', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-19 08:15:00'),
(333, 341, 63, 16000.00, 'cash', 'Cash Payment', NULL, '2025-09-03', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-03 09:15:00'),
(334, 342, 63, 9300.00, 'cash', 'Cash Payment', NULL, '2025-09-07', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-07 01:15:00'),
(335, 343, 63, 9300.00, 'gcash', 'GCash', '1000058456181', '2025-10-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-07 02:00:00', NULL, '2025-10-06 02:15:00'),
(336, 344, 63, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1212', '2025-11-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-06 02:00:00', NULL, '2025-11-05 03:15:00'),
(337, 345, 64, 16000.00, 'cash', 'Cash Payment', NULL, '2026-01-07', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-01-07 04:15:00'),
(338, 346, 64, 9300.00, 'gcash', 'GCash', '1000058552723', '2026-01-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-11 02:00:00', NULL, '2026-01-10 05:15:00'),
(339, 347, 64, 9300.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1214', '2026-02-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-10 02:00:00', NULL, '2026-02-09 06:15:00'),
(340, 348, 64, 9300.00, 'gcash', 'GCash', '1000058649265', '2026-03-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-14 02:00:00', NULL, '2026-03-13 07:15:00'),
(341, 349, 64, 9300.00, 'cash', 'Cash Payment', NULL, '2026-04-12', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-12 08:15:00'),
(342, 350, 65, 6800.00, 'cash', 'Cash Payment', NULL, '2025-10-05', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-05 09:15:00'),
(343, 351, 65, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1217', '2025-10-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-08 02:00:00', NULL, '2025-10-07 01:15:00'),
(344, 352, 65, 4150.00, 'gcash', 'GCash', '1000058794078', '2025-11-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-12 02:00:00', NULL, '2025-11-11 02:15:00'),
(345, 353, 65, 4150.00, 'cash', 'Cash Payment', NULL, '2025-12-10', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-10 03:15:00'),
(346, 354, 65, 4150.00, 'gcash', 'GCash', '1000058842349', '2026-01-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-10 02:00:00', NULL, '2026-01-09 04:15:00'),
(347, 355, 65, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1220', '2026-02-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-09 02:00:00', NULL, '2026-02-08 05:15:00'),
(348, 356, 65, 4150.00, 'gcash', 'GCash', '1000058938891', '2026-03-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-08 02:00:00', NULL, '2026-03-07 06:15:00'),
(349, 357, 66, 6800.00, 'cash', 'Cash Payment', NULL, '2025-09-30', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-30 07:15:00'),
(350, 358, 66, 4150.00, 'gcash', 'GCash', '1000058987162', '2025-10-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-07 02:00:00', NULL, '2025-10-06 08:15:00'),
(351, 359, 66, 4150.00, 'cash', 'Cash Payment', NULL, '2025-11-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-05 09:15:00'),
(352, 360, 66, 4150.00, 'gcash', 'GCash', '1000059035433', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-05 02:00:00', NULL, '2025-12-04 01:15:00'),
(353, 361, 66, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1224', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-04 02:00:00', NULL, '2026-01-03 02:15:00'),
(354, 362, 67, 6800.00, 'cash', 'Cash Payment', NULL, '2026-03-09', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-09 03:15:00'),
(355, 363, 67, 4150.00, 'cash', 'Cash Payment', NULL, '2026-03-14', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-14 04:15:00'),
(356, 364, 67, 4150.00, 'gcash', 'GCash', '1000059180246', '2026-04-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-14 02:00:00', NULL, '2026-04-13 05:15:00'),
(357, 365, 67, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1227', '2026-05-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-13 02:00:00', NULL, '2026-05-12 06:15:00'),
(358, 366, 67, 4150.00, 'gcash', 'GCash', '1000059276788', '2026-06-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-12 02:00:00', NULL, '2026-06-11 07:15:00'),
(359, 367, 68, 6800.00, 'cash', 'Cash Payment', NULL, '2025-10-04', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-04 08:15:00'),
(360, 368, 68, 4150.00, 'gcash', 'GCash', '1000059325059', '2025-10-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-09 02:00:00', NULL, '2025-10-08 09:15:00'),
(361, 369, 68, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1230', '2025-11-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-08 02:00:00', NULL, '2025-11-07 01:15:00'),
(362, 370, 68, 4150.00, 'gcash', 'GCash', '1000059421601', '2025-12-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-07 02:00:00', NULL, '2025-12-06 02:15:00'),
(363, 371, 68, 4150.00, 'cash', 'Cash Payment', NULL, '2026-01-10', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-10 03:15:00'),
(364, 372, 68, 4150.00, 'gcash', 'GCash', '1000059469872', '2026-02-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-10 02:00:00', NULL, '2026-02-09 04:15:00'),
(365, 373, 68, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1233', '2026-03-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-09 02:00:00', NULL, '2026-03-08 05:15:00'),
(366, 374, 68, 4150.00, 'gcash', 'GCash', '1000059566414', '2026-04-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-08 02:00:00', NULL, '2026-04-07 06:15:00'),
(367, 375, 69, 6800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-01 07:15:00'),
(368, 376, 69, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1236', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-05 02:00:00', NULL, '2025-10-04 08:15:00'),
(369, 377, 69, 4150.00, 'gcash', 'GCash', '1000059711227', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-04 02:00:00', NULL, '2025-11-03 09:15:00'),
(370, 378, 69, 4150.00, 'cash', 'Cash Payment', NULL, '2025-12-07', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-07 01:15:00'),
(371, 379, 69, 4150.00, 'gcash', 'GCash', '1000059759498', '2026-01-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-07 02:00:00', NULL, '2026-01-06 02:15:00'),
(372, 380, 69, 4150.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1239', '2026-02-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-06 02:00:00', NULL, '2026-02-05 03:15:00'),
(373, 381, 69, 4150.00, 'gcash', 'GCash', '1000059856040', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-05 02:00:00', NULL, '2026-03-04 04:15:00'),
(374, 382, 70, 6800.00, 'cash', 'Cash Payment', NULL, '2026-04-24', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-24 05:15:00'),
(375, 383, 70, 4150.00, 'gcash', 'GCash', '1000059904311', '2026-04-26', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-27 02:00:00', NULL, '2026-04-26 06:15:00'),
(376, 384, 70, 4150.00, 'cash', 'Cash Payment', NULL, '2026-05-30', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-30 07:15:00'),
(377, 385, 70, 4150.00, 'gcash', 'GCash', '1000059952582', '2026-06-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-30 02:00:00', NULL, '2026-06-29 08:15:00'),
(378, 386, 71, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-11 09:15:00'),
(379, 387, 71, 5400.00, 'cash', 'Cash Payment', NULL, '2025-09-17', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-17 01:15:00'),
(380, 388, 71, 5400.00, 'gcash', 'GCash', '1000060049124', '2025-10-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-17 02:00:00', NULL, '2025-10-16 02:15:00'),
(381, 389, 71, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1245', '2025-11-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-16 02:00:00', NULL, '2025-11-15 03:15:00'),
(382, 390, 71, 5400.00, 'gcash', 'GCash', '1000060145666', '2025-12-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-15 02:00:00', NULL, '2025-12-14 04:15:00'),
(383, 391, 71, 5400.00, 'cash', 'Cash Payment', NULL, '2026-01-13', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-13 05:15:00'),
(384, 392, 71, 5400.00, 'gcash', 'GCash', '1000060193937', '2026-02-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-18 02:00:00', NULL, '2026-02-17 06:15:00'),
(385, 393, 71, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1248', '2026-03-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-17 02:00:00', NULL, '2026-03-16 07:15:00'),
(386, 394, 72, 9000.00, 'cash', 'Cash Payment', NULL, '2025-10-13', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-13 08:15:00'),
(387, 395, 72, 5400.00, 'gcash', 'GCash', '1000060290479', '2025-10-18', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-19 02:00:00', NULL, '2025-10-18 09:15:00'),
(388, 396, 72, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1250', '2025-11-17', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-18 02:00:00', NULL, '2025-11-17 01:15:00'),
(389, 397, 72, 5400.00, 'gcash', 'GCash', '1000060387021', '2025-12-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-17 02:00:00', NULL, '2025-12-16 02:15:00'),
(390, 398, 72, 5400.00, 'cash', 'Cash Payment', NULL, '2026-01-15', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-15 03:15:00'),
(391, 399, 72, 5400.00, 'gcash', 'GCash', '1000060435292', '2026-02-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-20 02:00:00', NULL, '2026-02-19 04:15:00'),
(392, 400, 73, 9000.00, 'cash', 'Cash Payment', NULL, '2026-04-05', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-05 05:15:00'),
(393, 401, 73, 5400.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1254', '2026-04-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-10 02:00:00', NULL, '2026-04-09 06:15:00'),
(394, 402, 73, 5400.00, 'gcash', 'GCash', '1000060580105', '2026-05-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-09 02:00:00', NULL, '2026-05-08 07:15:00'),
(395, 403, 73, 5400.00, 'cash', 'Cash Payment', NULL, '2026-06-07', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-06-07 08:15:00'),
(396, 404, 73, 5400.00, 'gcash', 'GCash', '1000060628376', '2026-07-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-12 02:00:00', NULL, '2026-07-11 09:15:00'),
(397, 405, 74, 5200.00, 'cash', 'Cash Payment', NULL, '2025-10-03', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-10-03 01:15:00'),
(398, 406, 74, 3200.00, 'gcash', 'GCash', '1000060676647', '2025-10-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-07 02:00:00', NULL, '2025-10-06 02:15:00'),
(399, 407, 74, 3200.00, 'cash', 'Cash Payment', NULL, '2025-11-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-11-05 03:15:00'),
(400, 408, 74, 3200.00, 'gcash', 'GCash', '1000060724918', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-10 02:00:00', NULL, '2025-12-09 04:15:00'),
(401, 409, 74, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1259', '2026-01-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-09 02:00:00', NULL, '2026-01-08 05:15:00'),
(402, 410, 74, 3200.00, 'gcash', 'GCash', '1000060821460', '2026-02-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-08 02:00:00', NULL, '2026-02-07 06:15:00'),
(403, 411, 74, 3200.00, 'cash', 'Cash Payment', NULL, '2026-03-06', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-06 07:15:00'),
(404, 412, 74, 3200.00, 'gcash', 'GCash', '1000060869731', '2026-04-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-06 02:00:00', NULL, '2026-04-05 08:15:00'),
(405, 413, 75, 5200.00, 'cash', 'Cash Payment', NULL, '2025-09-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-23 09:15:00'),
(406, 414, 75, 3200.00, 'cash', 'Cash Payment', NULL, '2025-09-25', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-25 01:15:00'),
(407, 415, 75, 3200.00, 'gcash', 'GCash', '1000060966273', '2025-10-29', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-30 02:00:00', NULL, '2025-10-29 02:15:00'),
(408, 416, 75, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1264', '2025-11-28', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-29 02:00:00', NULL, '2025-11-28 03:15:00'),
(409, 417, 75, 3200.00, 'gcash', 'GCash', '1000061062815', '2025-12-27', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-28 02:00:00', NULL, '2025-12-27 04:15:00'),
(410, 418, 75, 3200.00, 'cash', 'Cash Payment', NULL, '2026-01-26', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-26 05:15:00'),
(411, 419, 75, 3200.00, 'gcash', 'GCash', '1000061111086', '2026-02-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-26 02:00:00', NULL, '2026-02-25 06:15:00'),
(412, 420, 76, 5200.00, 'cash', 'Cash Payment', NULL, '2025-09-06', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-06 07:15:00'),
(413, 421, 76, 3200.00, 'gcash', 'GCash', '1000061159357', '2025-09-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-13 02:00:00', NULL, '2025-09-12 08:15:00'),
(414, 422, 76, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1268', '2025-10-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-12 02:00:00', NULL, '2025-10-11 09:15:00'),
(415, 423, 76, 3200.00, 'gcash', 'GCash', '1000061255899', '2025-11-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-11 02:00:00', NULL, '2025-11-10 01:15:00'),
(416, 424, 77, 5200.00, 'cash', 'Cash Payment', NULL, '2025-12-16', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-12-16 02:15:00'),
(417, 425, 77, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1271', '2025-12-21', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-22 02:00:00', NULL, '2025-12-21 03:15:00'),
(418, 426, 77, 3200.00, 'gcash', 'GCash', '1000061400712', '2026-01-20', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-21 02:00:00', NULL, '2026-01-20 04:15:00'),
(419, 427, 77, 3200.00, 'cash', 'Cash Payment', NULL, '2026-02-19', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-02-19 05:15:00'),
(420, 428, 78, 5200.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-04-01 06:15:00'),
(421, 429, 78, 3200.00, 'gcash', 'GCash', '1000061448983', '2026-04-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-06 02:00:00', NULL, '2026-04-05 07:15:00'),
(422, 430, 78, 3200.00, 'cash', 'Cash Payment', NULL, '2026-05-04', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-05-04 08:15:00'),
(423, 431, 78, 3200.00, 'gcash', 'GCash', '1000061497254', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-04 02:00:00', NULL, '2026-06-03 09:15:00'),
(424, 432, 78, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1275', '2026-07-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-08 02:00:00', NULL, '2026-07-07 01:15:00'),
(425, 433, 79, 5200.00, 'cash', 'Cash Payment', NULL, '2025-09-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-01 02:15:00'),
(426, 434, 79, 3200.00, 'cash', 'Cash Payment', NULL, '2025-09-04', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-04 03:15:00'),
(427, 435, 79, 3200.00, 'gcash', 'GCash', '1000061642067', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-04 02:00:00', NULL, '2025-10-03 04:15:00'),
(428, 436, 79, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1278', '2025-11-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-08 02:00:00', NULL, '2025-11-07 05:15:00'),
(429, 437, 79, 3200.00, 'gcash', 'GCash', '1000061738609', '2025-12-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-07 02:00:00', NULL, '2025-12-06 06:15:00'),
(430, 438, 79, 3200.00, 'cash', 'Cash Payment', NULL, '2026-01-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-05 07:15:00'),
(431, 439, 79, 3200.00, 'gcash', 'GCash', '1000061786880', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-05 02:00:00', NULL, '2026-02-04 08:15:00'),
(432, 440, 80, 5200.00, 'cash', 'Cash Payment', NULL, '2026-03-29', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-29 09:15:00'),
(433, 441, 80, 3200.00, 'gcash', 'GCash', '1000061835151', '2026-03-31', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-01 02:00:00', NULL, '2026-03-31 01:15:00'),
(434, 442, 80, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1282', '2026-05-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-06 02:00:00', NULL, '2026-05-05 02:15:00'),
(435, 443, 80, 3200.00, 'gcash', 'GCash', '1000061931693', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-04 02:00:00', NULL, '2026-06-03 03:15:00'),
(436, 444, 81, 5200.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-29 04:15:00'),
(437, 445, 81, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1285', '2025-10-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-06 02:00:00', NULL, '2025-10-05 05:15:00'),
(438, 446, 81, 3200.00, 'gcash', 'GCash', '1000062076506', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-04 02:00:00', NULL, '2025-11-03 06:15:00'),
(439, 447, 81, 3200.00, 'cash', 'Cash Payment', NULL, '2025-12-03', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-03 07:15:00'),
(440, 448, 81, 3200.00, 'gcash', 'GCash', '1000062124777', '2026-01-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-02 02:00:00', NULL, '2026-01-01 08:15:00'),
(441, 449, 81, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1288', '2026-01-31', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-01 02:00:00', NULL, '2026-01-31 09:15:00'),
(442, 450, 82, 5200.00, 'cash', 'Cash Payment', NULL, '2026-03-09', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-03-09 01:15:00'),
(443, 451, 82, 3200.00, 'gcash', 'GCash', '1000062221319', '2026-03-14', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-15 02:00:00', NULL, '2026-03-14 02:15:00'),
(444, 452, 82, 3200.00, 'cash', 'Cash Payment', NULL, '2026-04-13', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-04-13 03:15:00'),
(445, 453, 82, 3200.00, 'gcash', 'GCash', '1000062269590', '2026-05-12', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-05-13 02:00:00', NULL, '2026-05-12 04:15:00'),
(446, 454, 82, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1291', '2026-06-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-06-12 02:00:00', NULL, '2026-06-11 05:15:00'),
(447, 455, 82, 3200.00, 'gcash', 'GCash', '1000062366132', '2026-07-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-07-16 02:00:00', NULL, '2026-07-15 06:15:00'),
(448, 456, 82, 3200.00, 'cash', 'Cash Payment', NULL, '2026-08-14', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-08-14 07:15:00'),
(449, 457, 82, 3200.00, 'gcash', 'GCash', '1000062414403', '2026-09-13', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-09-14 02:00:00', NULL, '2026-09-13 08:15:00'),
(450, 458, 83, 5200.00, 'cash', 'Cash Payment', NULL, '2025-09-01', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-01 09:15:00'),
(451, 459, 83, 3200.00, 'cash', 'Cash Payment', NULL, '2025-09-05', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-09-05 01:15:00'),
(452, 460, 83, 3200.00, 'gcash', 'GCash', '1000062510945', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-05 02:00:00', NULL, '2025-10-04 02:15:00'),
(453, 461, 83, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1296', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-04 02:00:00', NULL, '2025-11-03 03:15:00'),
(454, 462, 83, 3200.00, 'gcash', 'GCash', '1000062607487', '2025-12-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-12-08 02:00:00', NULL, '2025-12-07 04:15:00'),
(455, 463, 83, 3200.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-01-06 05:15:00'),
(456, 464, 83, 3200.00, 'gcash', 'GCash', '1000062655758', '2026-02-05', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-02-06 02:00:00', NULL, '2026-02-05 06:15:00'),
(457, 465, 83, 3200.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1299', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-05 02:00:00', NULL, '2026-03-04 07:15:00'),
(458, 466, 83, 3200.00, 'gcash', 'GCash', '1000062752300', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-04-04 02:00:00', NULL, '2026-04-03 08:15:00'),
(459, 467, 84, 15600.00, 'cash', 'Cash Payment', NULL, '2025-09-13', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2025-09-13 09:15:00'),
(460, 468, 84, 9100.00, 'gcash', 'GCash', '1000062800571', '2025-09-16', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-09-17 02:00:00', NULL, '2025-09-16 01:15:00'),
(461, 469, 84, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1302', '2025-10-15', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-10-16 02:00:00', NULL, '2025-10-15 02:15:00'),
(462, 470, 84, 9100.00, 'gcash', 'GCash', '1000062897113', '2025-11-19', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2025-11-20 02:00:00', NULL, '2025-11-19 03:15:00'),
(463, 471, 84, 9100.00, 'cash', 'Cash Payment', NULL, '2025-12-18', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2025-12-18 04:15:00'),
(464, 472, 85, 15600.00, 'cash', 'Cash Payment', NULL, '2026-01-23', 'approved', NULL, 'Move-in fee paid at the admin office.', NULL, NULL, NULL, 267, '2026-01-23 05:15:00'),
(465, 473, 85, 9100.00, 'bank_transfer', 'BDO Bank Transfer', 'BDO-261001-1305', '2026-01-25', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-01-26 02:00:00', NULL, '2026-01-25 06:15:00'),
(466, 474, 85, 9100.00, 'gcash', 'GCash', '1000063041926', '2026-03-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 267, '2026-03-02 02:00:00', NULL, '2026-03-01 07:15:00'),
(467, 475, 85, 9100.00, 'cash', 'Cash Payment', NULL, '2026-03-28', 'approved', NULL, NULL, NULL, NULL, NULL, 267, '2026-03-28 08:15:00');

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
(1, 5, NULL, 24, 'manual', 'Late payment fee (10% of monthly rent)', 450.00, '2026-05-25', 'active', 266, '2026-05-24 16:00:00', '2026-05-24 16:00:00'),
(2, 6, NULL, 33, 'manual', 'Late payment fee (10% of monthly rent)', 260.00, '2026-09-26', 'active', 266, '2026-09-25 16:00:00', '2026-09-25 16:00:00'),
(3, 13, NULL, 73, 'manual', 'Late payment fee (10% of monthly rent)', 250.00, '2026-09-29', 'active', 266, '2026-09-28 16:00:00', '2026-09-28 16:00:00'),
(4, 14, 1, 78, 'damage', 'Damage: Broken cabinet door hinge (bed-side locker)', 800.00, '2026-09-19', 'active', 266, '2026-09-18 16:00:00', '2026-09-18 16:00:00'),
(5, 14, NULL, NULL, 'manual', 'Lost room key replacement', 50.00, '2026-08-22', 'waived', 266, '2026-08-21 16:00:00', '2026-08-21 16:00:00'),
(6, 15, NULL, NULL, 'manual', 'House rule violation: butane stove found in room (hazardous goods)', 500.00, '2026-09-29', 'active', 266, '2026-09-28 16:00:00', '2026-09-28 16:00:00'),
(7, 24, NULL, 122, 'manual', 'Late payment fee (10% of monthly rent)', 260.00, '2026-08-21', 'active', 266, '2026-08-20 16:00:00', '2026-08-20 16:00:00');

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
(1, 1, 'created', 266, NULL, '2026-05-24 16:00:00'),
(2, 2, 'created', 266, NULL, '2026-09-25 16:00:00'),
(3, 3, 'created', 266, NULL, '2026-09-28 16:00:00'),
(4, 4, 'created', 266, NULL, '2026-09-18 16:00:00'),
(5, 5, 'created', 266, NULL, '2026-08-21 16:00:00'),
(6, 5, 'waived', 3, 'Key was found by the guard the next day. First offense -- waived.', '2026-08-22 16:00:00'),
(7, 6, 'created', 266, NULL, '2026-09-28 16:00:00'),
(8, 7, 'created', 266, NULL, '2026-08-20 16:00:00');

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
(1, 1, 5, 'Sobrang linis ng rooms and very accommodating ang staff. Malapit pa sa UST, walking distance lang. Highly recommended!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-24 16:00:00', '2026-09-24 16:00:00'),
(2, 5, 4, 'Maayos ang WiFi at tahimik sa gabi. Minsan lang medyo mahina ang tubig sa umaga pero agad naman inaayos.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-20 16:00:00', '2026-09-20 16:00:00'),
(3, 11, 5, 'Almost a year na ako dito. Safe, may curfew, at mabait si Ma\'am Tess. Perfect para sa mga nurse na shifting.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-14 16:00:00', '2026-09-14 16:00:00'),
(4, 15, 3, 'Okay naman overall. Sana lang may kitchen na pwedeng gamitin kasi bawal magluto sa room.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-10 16:00:00', '2026-09-10 16:00:00'),
(5, 18, 4, 'Good value for money. Malinis ang CR at laging may tubig. Medyo strict sa visitors pero understandable.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-07 16:00:00', '2026-09-07 16:00:00'),
(6, 23, 5, 'Nag-move out na ako kasi lumipat ang work ko, pero sobrang saya ng stay ko dito. Salamat NEST.PH!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-22 16:00:00', '2026-09-22 16:00:00'),
(7, 25, 5, 'Ang ganda ng study hall sa baba, dito ako nagre-review lagi. Mabilis din sumagot ang admin sa tickets.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-20 16:00:00', '2026-09-20 16:00:00'),
(8, 12, 1, 'Mas mura sa amin! Visit www.cheapdorms-manila.com for promo, message 0917-000-0000', 0, 'hidden', '[\"Contains a link\",\"Contains a phone number\"]', NULL, NULL, NULL, '2026-09-27 16:00:00', '2026-09-27 16:00:00'),
(9, 17, 2, 'Pangit ugali ng roommate ko, [name removed] sobrang ingay.', 0, 'removed', NULL, 266, '2026-09-24 16:00:00', 'Removed: names another tenant. Concern was redirected to a support ticket instead.', '2026-09-23 16:00:00', '2026-09-24 16:00:00');

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
(16, 3, '101', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Cabinet per bed\",\"Shared CR\"]', 14000.00, 2000.00, 1000.00, 'full', NULL, 'Bright quad room beside the study hall', 'public', '2026-08-30 18:57:43', '2026-09-30 16:13:55'),
(17, 3, '102', 'Double Sharing', '[\"Air-conditioned\",\"Study table\",\"Private CR\"]', 9000.00, 1200.00, 600.00, 'full', NULL, 'Cozy double room with private CR', 'draft', '2026-08-31 10:49:31', '2026-09-30 16:13:55'),
(18, 3, '103', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Shared CR\"]', 13000.00, 2000.00, 1000.00, 'full', NULL, 'Spacious quad room with computer corner', 'public', '2026-09-03 06:27:16', '2026-09-30 16:13:55'),
(19, 11, '201', '6-Bed Dormitory', '[\"Air-conditioned\",\"Double-deck beds\",\"Lockers\",\"Shared CR\"]', 15000.00, 2400.00, 1200.00, 'full', NULL, NULL, 'draft', '2026-09-04 13:04:53', '2026-09-30 16:13:55'),
(84, 3, '104', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Mini fridge\"]', 7500.00, 800.00, 500.00, 'maintenance', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(85, 11, '202', 'Double Sharing', '[\"Air-conditioned\",\"Study table\",\"Private CR\"]', 9500.00, 1200.00, 600.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(86, 11, '203', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Cabinet per bed\"]', 14000.00, 2000.00, 1000.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(87, 11, '204', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Balcony\"]', 8000.00, 800.00, 500.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(88, 12, '301', 'Quad Sharing', '[\"Air-conditioned\",\"Study table\",\"Balcony access\"]', 13600.00, 2000.00, 1000.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(89, 12, '302', 'Double Sharing', '[\"Air-conditioned\",\"Private CR\"]', 9000.00, 1200.00, 600.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(90, 12, '303', '6-Bed Dormitory', '[\"Air-conditioned\",\"Double-deck beds\",\"Lockers\"]', 15600.00, 2400.00, 1200.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55'),
(91, 12, '304', 'Solo Room', '[\"Air-conditioned\",\"Private CR\",\"Work desk\"]', 7800.00, 800.00, 500.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-09-30 16:13:55');

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VKcuvOvQ8ann3uWr0p4CJJrRoIq2elFlHj6L3hiI', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOG5hZ3gyWTJlbURLMFNJOHAwNHgzQllKZGgyYldGZzNhRkFLUTVQWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3JlcG9ydHMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3JlcG9ydHMvZXhwZW5zZXM/bW9udGg9MjAyNi0wOSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1790786164);

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
(1, 270, 'Maria Angelica', 'Santos', '09181242486', 'maria.santos@gmail.com', 'Rodelio Santos', '09182034386', '2004-03-14', 'Brgy. San Isidro, Angono, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-12 02:15:00', '2026-09-30 16:13:55'),
(2, 271, 'Kimberly Anne', 'Dela Cruz', '09271250405', 'kimberly.delacruz@gmail.com', 'Marites Dela Cruz', '09272042305', '2005-07-02', '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-19 03:15:00', '2026-09-30 16:13:55'),
(3, 272, 'Patricia Mae', 'Gonzales', '09391258324', 'patricia.gonzales@gmail.com', 'Lorna Gonzales', '09392050224', '2003-11-21', 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-16 04:15:00', '2026-09-30 16:13:55'),
(4, 273, 'Nicole Joy', 'Ramos', '09451266243', 'nicole.ramos@gmail.com', 'Ernesto Ramos', '09452058143', '2004-01-09', 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-07 05:15:00', '2026-09-30 16:13:55'),
(5, 274, 'Juan Miguel', 'Reyes', '09561274162', 'juan.reyes@gmail.com', 'Carmelita Reyes', '09562066062', '1999-05-30', '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-08 06:15:00', '2026-09-30 16:13:55'),
(6, 275, 'John Paul', 'Mendoza', '09212408565', 'john.mendoza@gmail.com', 'Rosalie Mendoza', '09212408565', '2004-08-17', 'Brgy. Bagong Silang, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 1, 0, '2026-06-08 07:15:00', '2026-09-30 16:13:55'),
(7, 276, 'Mark Joseph', 'Aquino', '09981290000', 'mark.aquino@gmail.com', 'Josefina Aquino', '09982081900', '2005-02-11', 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-16 08:15:00', '2026-09-30 16:13:55'),
(8, 277, 'Christian Dave', 'Torres', '09081297919', 'christian.torres@gmail.com', 'Dante Torres', '09082089819', '2002-12-03', '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-17 01:15:00', '2026-09-30 16:13:55'),
(9, 278, 'Benjamin', 'Robles', '09212408565', 'benjamin.robles@gmail.com', 'Evelyn Robles', '09212408565', '2004-06-25', 'Brgy. Santo Cristo, San Fernando, Pampanga', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-11 02:15:00', '2026-09-30 16:13:55'),
(10, 279, 'Rafael Luis', 'Navarro', '09171313757', 'rafael.navarro@gmail.com', 'Gloria Navarro', '09172105657', '2001-09-14', '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-11 03:15:00', '2026-09-30 16:13:55'),
(11, 280, 'Angela Marie', 'Villanueva', '09181321676', 'angela.villanueva@gmail.com', 'Arnel Villanueva', '09182113576', '1998-04-19', 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-02 04:15:00', '2026-09-30 16:13:55'),
(12, 281, 'Jasmine Rose', 'Garcia', '09271329595', 'jasmine.garcia@gmail.com', 'Ramil Garcia', '09272121495', '2005-10-05', 'Purok 2, Brgy. Talon, Las Piñas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-20 05:15:00', '2026-09-30 16:13:55'),
(13, 282, 'Camille Louise', 'Flores', '09212408565', 'camille.flores@gmail.com', 'Susan Flores', '09212408565', '2004-12-28', 'Brgy. Mabini, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 1, '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(14, 283, 'Bea Katrina', 'Pascual', '09451345433', 'bea.pascual@gmail.com', 'Gerardo Pascual', '09452137333', '2003-05-16', 'Brgy. Sta. Rita, Olongapo City, Zambales', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-19 07:15:00', '2026-09-30 16:13:55'),
(15, 284, 'Princess Joy', 'Manalo', '09561353352', 'princess.manalo@gmail.com', 'Cristina Manalo', '09562145252', '2002-08-08', 'Brgy. Parian, Calamba City, Laguna', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-16 08:15:00', '2026-09-30 16:13:55'),
(16, 285, 'Kathleen Mae', 'Salazar', '09661361271', 'kathleen.salazar@gmail.com', 'Rogelio Salazar', '09662153171', '2006-01-30', 'Brgy. Poblacion, Tagum City, Davao del Norte', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-26 01:15:00', '2026-09-30 16:13:55'),
(17, 286, 'Carlo Miguel', 'Bautista', '09981369190', 'carlo.bautista@gmail.com', 'Nenita Bautista', '09982161090', '2000-03-03', 'San Pablo City, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-12 02:15:00', '2026-09-30 16:13:55'),
(18, 287, 'Joshua Emmanuel', 'Lim', '09081377109', 'joshua.lim@gmail.com', 'Wilson Lim', '09082169009', '2004-11-11', 'Sta. Cruz, Laguna', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-02 03:15:00', '2026-09-30 16:13:55'),
(19, 288, 'Paolo Andres', 'Ocampo', '09951385028', 'paolo.ocampo@gmail.com', 'Amelia Ocampo', '09952176928', '2005-04-22', 'Brgy. Poblacion, Malolos City, Bulacan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-23 04:15:00', '2026-09-30 16:13:55'),
(20, 289, 'Andrea Nicole', 'Tan', '09171392947', 'andrea.tan@gmail.com', 'Rebecca Tan', '09172184847', '1997-09-09', 'Brgy. Kauswagan, Cagayan de Oro City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-17 05:15:00', '2026-09-30 16:13:55'),
(21, 290, 'Erika Jane', 'Morales', '09181400866', 'erika.morales@gmail.com', 'Ronaldo Morales', '09182192766', '2004-07-07', 'Brgy. Pantal, Dagupan City, Pangasinan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-09 06:15:00', '2026-09-30 16:13:55'),
(22, 291, 'Hannah Grace', 'Soriano', '09271408785', 'hannah.soriano@gmail.com', 'Marilou Soriano', '09272200685', '2006-02-14', 'Brgy. Dolores, Taytay, Rizal', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-17 07:15:00', '2026-09-30 16:13:55'),
(23, 292, 'Gabriel Jose', 'Rivera', '09391416704', 'gabriel.rivera@gmail.com', 'Imelda Rivera', '09392208604', '2001-06-01', 'Brgy. San Antonio, Biñan, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract -- transferred to a job in Laguna.', '2026-08-22 16:00:00', 266, 0, 0, 0, '2026-03-12 08:15:00', '2026-09-30 16:13:55'),
(24, 293, 'Joseph Allan', 'Cruz', '09212408565', 'joseph.cruz@gmail.com', 'Nora Cruz', '09212408565', '2003-03-27', 'Brgy. Tabing Ilog, Marilao, Bulacan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 1, 1, 0, '2026-03-31 01:15:00', '2026-09-30 16:13:55'),
(25, 294, 'Stephanie Claire', 'Uy', '09561432542', 'stephanie.uy@gmail.com', 'Henry Uy', '09562224442', '2004-09-02', 'Brgy. Lourdes, Dagupan City, Pangasinan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-02-07 02:15:00', '2026-09-30 16:13:56'),
(26, 295, 'Adrian Paul', 'Castro', '09661440461', 'adrian.castro@gmail.com', 'Leticia Castro', '09662232361', '1999-12-12', 'Brgy. Sampaloc, Tanauan City, Batangas', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-11 03:15:00', '2026-09-30 16:13:56'),
(27, 296, 'Vincent Ray', 'Magbanua', '09981448380', 'vincent.magbanua@gmail.com', 'Ramon Magbanua', '09982240280', '2005-06-19', 'Brgy. Poblacion, Roxas City, Capiz', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-12 04:15:00', '2026-09-30 16:13:56'),
(28, 297, 'Luis Antonio', 'Del Rosario', '09081456299', 'luis.delrosario@gmail.com', 'Rowena Del Rosario', '09082248199', '2003-02-27', 'Brgy. Cutcut, Angeles City, Pampanga', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-30 05:15:00', '2026-09-30 16:13:56'),
(29, 298, 'Jerome Anthony', 'Pineda', '09951464218', 'jerome.pineda@gmail.com', 'Grace Pineda', '09952256118', '2004-10-30', 'Brgy. Poblacion, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-05 06:15:00', '2026-09-30 16:13:56'),
(30, 299, 'Kenneth Bryan', 'Sy', '09171472137', 'kenneth.sy@gmail.com', 'Victor Sy', '09172264037', '2006-01-08', 'Brgy. Balibago, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-21 07:15:00', '2026-09-30 16:13:56'),
(31, 300, 'Emmanuel Jose', 'Villareal', '09181480056', 'emmanuel.villareal@gmail.com', 'Nelia Villareal', '09182271956', '2002-07-21', 'Brgy. San Vicente, Tacloban City, Leyte', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-28 08:15:00', '2026-09-30 16:13:56'),
(32, 301, 'Janelle', 'Tolentino', '09182826286', 'janelle.tolentino@gmail.com', 'Ricardo Tolentino', '09183618186', '2006-03-02', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-03-01 02:00:00', 266, 0, 0, 0, '2025-08-19 02:30:00', '2026-02-28 16:00:00'),
(33, 302, 'Oliver', 'Galang', '09272834205', 'oliver.galang@gmail.com', 'Elena Galang', '09273626105', '2006-02-17', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-07 02:00:00', 266, 0, 0, 0, '2025-08-24 03:30:00', '2026-02-06 16:00:00'),
(34, 303, 'Elijah', 'Sison', '09392842124', 'elijah.sison@gmail.com', 'Manuel Sison', '09393634024', '2001-02-04', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-06-22 02:00:00', 266, 0, 0, 0, '2026-03-11 04:30:00', '2026-06-21 16:00:00'),
(35, 304, 'Irish', 'Umali', '09452850043', 'irish.umali@gmail.com', 'Teresa Umali', '09453641943', '2006-01-22', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-02-25 02:00:00', 266, 0, 0, 0, '2025-09-16 05:30:00', '2026-02-24 16:00:00'),
(36, 305, 'Nathan', 'Tolentino', '09562857962', 'nathan.tolentino@gmail.com', 'Roberto Tolentino', '09563649862', '2006-01-09', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-07-20 02:00:00', 266, 0, 0, 0, '2026-04-08 06:30:00', '2026-07-19 16:00:00'),
(37, 306, 'Clarisse', 'Figueroa', '09662865881', 'clarisse.figueroa@gmail.com', 'Lourdes Figueroa', '09663657781', '2005-12-27', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-01-24 02:00:00', 266, 0, 0, 0, '2025-09-13 07:30:00', '2026-01-23 16:00:00'),
(38, 307, 'Jericho', 'Figueroa', '09982873800', 'jericho.figueroa@gmail.com', 'Ricardo Figueroa', '09983665700', '2000-12-14', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-03-09 02:00:00', 266, 0, 0, 0, '2025-09-12 08:30:00', '2026-03-08 16:00:00'),
(39, 308, 'Alyssa', 'Ilagan', '09082881719', 'alyssa.ilagan@gmail.com', 'Elena Ilagan', '09083673619', '2006-09-27', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-04-13 02:00:00', 266, 0, 0, 0, '2025-08-31 01:30:00', '2026-04-12 16:00:00'),
(40, 309, 'Queenie', 'Zamora', '09952889638', 'queenie.zamora@gmail.com', 'Manuel Zamora', '09953681538', '2006-09-14', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-02-10 02:00:00', 266, 0, 0, 0, '2025-08-27 02:30:00', '2026-02-09 16:00:00'),
(41, 310, 'Ella', 'Ortega', '09172897557', 'ella.ortega@gmail.com', 'Teresa Ortega', '09173689457', '2006-09-01', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-08-19 02:00:00', 266, 0, 0, 0, '2026-03-17 03:30:00', '2026-08-18 16:00:00'),
(42, 311, 'Irish', 'Galang', '09182905476', 'irish.galang@gmail.com', 'Roberto Galang', '09183697376', '2001-08-19', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-03-23 02:00:00', 266, 0, 0, 0, '2025-09-14 04:30:00', '2026-03-22 16:00:00'),
(43, 312, 'Oliver', 'Figueroa', '09272913395', 'oliver.figueroa@gmail.com', 'Lourdes Figueroa', '09273705295', '2006-08-06', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-03-21 02:00:00', 266, 0, 0, 0, '2025-09-11 05:30:00', '2026-03-20 16:00:00'),
(44, 313, 'Queenie', 'Figueroa', '09392921314', 'queenie.figueroa@gmail.com', 'Ricardo Figueroa', '09393713214', '2006-07-24', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-12 02:00:00', 266, 0, 0, 0, '2025-10-01 06:30:00', '2026-05-11 16:00:00'),
(45, 314, 'Sofia', 'Zamora', '09452929233', 'sofia.zamora@gmail.com', 'Elena Zamora', '09453721133', '2006-07-11', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2025-12-21 02:00:00', 266, 0, 0, 0, '2025-09-09 07:30:00', '2025-12-20 16:00:00'),
(46, 315, 'Marco', 'Galang', '09562937152', 'marco.galang@gmail.com', 'Manuel Galang', '09563729052', '2001-06-28', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-03-25 02:00:00', 266, 0, 0, 0, '2025-09-12 08:30:00', '2026-03-24 16:00:00'),
(47, 316, 'Bianca', 'Nepomuceno', '09662945071', 'bianca.nepomuceno@gmail.com', 'Teresa Nepomuceno', '09663736971', '2006-06-15', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-07-23 02:00:00', 266, 0, 0, 0, '2026-04-15 01:30:00', '2026-07-22 16:00:00'),
(48, 317, 'Warren', 'Macaraeg', '09982952990', 'warren.macaraeg@gmail.com', 'Roberto Macaraeg', '09983744890', '2006-06-02', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-03-13 02:00:00', 266, 0, 0, 0, '2025-10-05 02:30:00', '2026-03-12 16:00:00'),
(49, 318, 'Francine', 'Cordero', '09082960909', 'francine.cordero@gmail.com', 'Lourdes Cordero', '09083752809', '2006-05-20', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-05-20 02:00:00', 266, 0, 0, 0, '2025-09-11 03:30:00', '2026-05-19 16:00:00'),
(50, 319, 'Vanessa', 'Umali', '09952968828', 'vanessa.umali@gmail.com', 'Ricardo Umali', '09953760728', '2001-05-07', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2025-12-25 02:00:00', 266, 0, 0, 0, '2025-09-15 04:30:00', '2025-12-24 16:00:00'),
(51, 320, 'Ysabel', 'Figueroa', '09172976747', 'ysabel.figueroa@gmail.com', 'Elena Figueroa', '09173768647', '2006-04-24', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-06-17 02:00:00', 266, 0, 0, 0, '2026-01-14 05:30:00', '2026-06-16 16:00:00'),
(52, 321, 'Elijah', 'Fernandez', '09182984666', 'elijah.fernandez@gmail.com', 'Manuel Fernandez', '09183776566', '2006-04-11', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-03-17 02:00:00', 266, 0, 0, 0, '2025-09-05 06:30:00', '2026-03-16 16:00:00'),
(53, 322, 'Harold', 'Hernandez', '09272992585', 'harold.hernandez@gmail.com', 'Teresa Hernandez', '09273784485', '2006-03-29', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-09-28 02:00:00', 266, 0, 0, 0, '2026-03-20 07:30:00', '2026-09-27 16:00:00'),
(54, 323, 'Francine', 'Nepomuceno', '09393000504', 'francine.nepomuceno@gmail.com', 'Roberto Nepomuceno', '09393792404', '2001-03-16', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-18 02:00:00', 266, 0, 0, 0, '2025-09-04 08:30:00', '2026-01-17 16:00:00'),
(55, 324, 'Bryan', 'Macaraeg', '09453008423', 'bryan.macaraeg@gmail.com', 'Lourdes Macaraeg', '09453800323', '2006-03-03', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-07-15 02:00:00', 266, 0, 0, 0, '2026-02-24 01:30:00', '2026-07-14 16:00:00'),
(56, 325, 'Gwen', 'Abad', '09563016342', 'gwen.abad@gmail.com', 'Ricardo Abad', '09563808242', '2006-02-18', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-01-14 02:00:00', 266, 0, 0, 0, '2025-10-05 02:30:00', '2026-01-13 16:00:00'),
(57, 326, 'Ella', 'Yap', '09663024261', 'ella.yap@gmail.com', 'Elena Yap', '09663816161', '2006-02-05', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-07-01 02:00:00', 266, 0, 0, 0, '2026-01-22 03:30:00', '2026-06-30 16:00:00'),
(58, 327, 'Gian', 'Hernandez', '09983032180', 'gian.hernandez@gmail.com', 'Manuel Hernandez', '09983824080', '2001-01-23', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-01-19 02:00:00', 266, 0, 0, 0, '2025-09-08 04:30:00', '2026-01-18 16:00:00'),
(59, 328, 'Clarisse', 'Cordero', '09083040099', 'clarisse.cordero@gmail.com', 'Teresa Cordero', '09083831999', '2006-01-10', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-05-06 02:00:00', 266, 0, 0, 0, '2026-02-04 05:30:00', '2026-05-05 16:00:00'),
(60, 329, 'Lianne', 'Quiambao', '09953048018', 'lianne.quiambao@gmail.com', 'Roberto Quiambao', '09953839918', '2005-12-28', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-06-02 02:00:00', 266, 0, 0, 0, '2025-09-19 06:30:00', '2026-06-01 16:00:00'),
(61, 330, 'Dominic', 'Agustin', '09173055937', 'dominic.agustin@gmail.com', 'Lourdes Agustin', '09173847837', '2005-12-15', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-09-28 02:00:00', 266, 0, 0, 0, '2026-06-04 07:30:00', '2026-09-27 16:00:00'),
(62, 331, 'Marco', 'Rosales', '09183063856', 'marco.rosales@gmail.com', 'Ricardo Rosales', '09183855756', '2001-09-28', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 266, 0, 0, 0, '2025-09-07 08:30:00', '2026-02-27 16:00:00'),
(63, 332, 'Aldrin', 'Dizon', '09273071775', 'aldrin.dizon@gmail.com', 'Elena Dizon', '09273863675', '2006-09-15', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2025-12-04 02:00:00', 266, 0, 0, 0, '2025-08-26 01:30:00', '2025-12-03 16:00:00'),
(64, 333, 'Sofia', 'Umali', '09393079694', 'sofia.umali@gmail.com', 'Manuel Umali', '09393871594', '2006-09-02', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-04-18 02:00:00', 266, 0, 0, 0, '2025-12-29 02:30:00', '2026-04-17 16:00:00'),
(65, 334, 'Bianca', 'Javier', '09453087613', 'bianca.javier@gmail.com', 'Teresa Javier', '09453879513', '2006-08-20', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-06 02:00:00', 266, 0, 0, 0, '2025-09-25 03:30:00', '2026-04-05 16:00:00'),
(66, 335, 'Renz', 'Javier', '09563095532', 'renz.javier@gmail.com', 'Roberto Javier', '09563887432', '2001-08-07', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-02-01 02:00:00', 266, 0, 0, 0, '2025-09-19 04:30:00', '2026-01-31 16:00:00'),
(67, 336, 'Queenie', 'Fernandez', '09663103451', 'queenie.fernandez@gmail.com', 'Lourdes Fernandez', '09663895351', '2006-07-25', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-07-10 02:00:00', 266, 0, 0, 0, '2026-02-25 05:30:00', '2026-07-09 16:00:00'),
(68, 337, 'Kevin', 'Macaraeg', '09983111370', 'kevin.macaraeg@gmail.com', 'Ricardo Macaraeg', '09983903270', '2006-07-12', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-05 02:00:00', 266, 0, 0, 0, '2025-09-21 06:30:00', '2026-05-04 16:00:00'),
(69, 338, 'Danica', 'Castillo', '09083119289', 'danica.castillo@gmail.com', 'Elena Castillo', '09083911189', '2006-06-29', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-04-02 02:00:00', 266, 0, 0, 0, '2025-09-24 07:30:00', '2026-04-01 16:00:00'),
(70, 339, 'Oliver', 'Umali', '09953127208', 'oliver.umali@gmail.com', 'Manuel Umali', '09953919108', '2001-06-16', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-07-12 02:00:00', 266, 0, 0, 0, '2026-04-16 08:30:00', '2026-07-11 16:00:00'),
(71, 340, 'Troy', 'Quiambao', '09173135127', 'troy.quiambao@gmail.com', 'Teresa Quiambao', '09173927027', '2006-06-03', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-03-16 02:00:00', 266, 0, 0, 0, '2025-09-02 01:30:00', '2026-03-15 16:00:00'),
(72, 341, 'Irish', 'Fernandez', '09183143046', 'irish.fernandez@gmail.com', 'Roberto Fernandez', '09183934946', '2006-05-21', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-03-14 02:00:00', 266, 0, 0, 0, '2025-10-03 02:30:00', '2026-03-13 16:00:00'),
(73, 342, 'Lance', 'Rosales', '09273150965', 'lance.rosales@gmail.com', 'Lourdes Rosales', '09273942865', '2006-05-08', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-07-11 02:00:00', 266, 0, 0, 0, '2026-03-25 03:30:00', '2026-07-10 16:00:00'),
(74, 343, 'Gian', 'Ortega', '09393158884', 'gian.ortega@gmail.com', 'Ricardo Ortega', '09393950784', '2001-04-25', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-04-05 02:00:00', 266, 0, 0, 0, '2025-09-21 04:30:00', '2026-04-04 16:00:00'),
(75, 344, 'Renz', 'Ortega', '09453166803', 'renz.ortega@gmail.com', 'Elena Ortega', '09453958703', '2006-04-12', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-27 02:00:00', 266, 0, 0, 0, '2025-09-10 05:30:00', '2026-02-26 16:00:00'),
(76, 345, 'Janelle', 'Agustin', '09563174722', 'janelle.agustin@gmail.com', 'Manuel Agustin', '09563966622', '2006-03-30', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2025-12-07 02:00:00', 266, 0, 0, 0, '2025-08-30 06:30:00', '2025-12-06 16:00:00'),
(77, 346, 'Troy', 'Panganiban', '09663182641', 'troy.panganiban@gmail.com', 'Teresa Panganiban', '09663974541', '2006-03-17', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-03-17 02:00:00', 266, 0, 0, 0, '2025-12-08 07:30:00', '2026-03-16 16:00:00'),
(78, 347, 'Lance', 'Umali', '09983190560', 'lance.umali@gmail.com', 'Roberto Umali', '09983982460', '2001-03-04', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-08-02 02:00:00', 266, 0, 0, 0, '2026-03-23 08:30:00', '2026-08-01 16:00:00'),
(79, 348, 'Kyla', 'Belmonte', '09083198479', 'kyla.belmonte@gmail.com', 'Lourdes Belmonte', '09083990379', '2006-02-19', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-03-02 02:00:00', 266, 0, 0, 0, '2025-08-22 01:30:00', '2026-03-01 16:00:00'),
(80, 349, 'Renz', 'Abad', '09953206398', 'renz.abad@gmail.com', 'Ricardo Abad', '09953998298', '2006-02-06', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-06-10 02:00:00', 266, 0, 0, 0, '2026-03-18 02:30:00', '2026-06-09 16:00:00'),
(81, 350, 'Gian', 'Macaraeg', '09173214317', 'gian.macaraeg@gmail.com', 'Elena Macaraeg', '09174006217', '2006-01-24', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 266, 0, 0, 0, '2025-09-17 03:30:00', '2026-02-27 16:00:00'),
(82, 351, 'Ivan', 'Nepomuceno', '09183222236', 'ivan.nepomuceno@gmail.com', 'Manuel Nepomuceno', '09184014136', '2001-01-11', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-09-22 02:00:00', 266, 0, 0, 0, '2026-02-24 04:30:00', '2026-09-21 16:00:00'),
(83, 352, 'Danica', 'Abad', '09273230155', 'danica.abad@gmail.com', 'Teresa Abad', '09274022055', '2005-12-29', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', 266, 0, 0, 0, '2025-08-25 05:30:00', '2026-04-29 16:00:00'),
(84, 353, 'Zach', 'Fernandez', '09393238074', 'zach.fernandez@gmail.com', 'Roberto Fernandez', '09394029974', '2005-12-16', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-14 02:00:00', 266, 0, 0, 0, '2025-09-05 06:30:00', '2026-01-13 16:00:00'),
(85, 354, 'Clarisse', 'Dizon', '09453245993', 'clarisse.dizon@gmail.com', 'Lourdes Dizon', '09454037893', '2006-09-29', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-04-12 02:00:00', 266, 0, 0, 0, '2026-01-14 07:30:00', '2026-04-11 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_notifications`
--

CREATE TABLE `tenant_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(40) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `dedupe_key` varchar(120) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 268, NULL, 'Noted po. Papupuntahin namin ang technician bukas ng umaga.', '2026-09-11 06:30:00'),
(2, 1, NULL, 1, 'Salamat po!', '2026-09-11 09:30:00'),
(3, 1, 268, NULL, 'Nalinis na po ang filter at na-recharge ang freon. Paki-check po kung okay na.', '2026-09-11 12:30:00'),
(4, 3, 268, NULL, 'Chine-check na po ng plumber ang main line. Update po kami mamaya.', '2026-09-27 10:30:00'),
(5, 5, 266, NULL, 'Thank you for the suggestion! Hindi po kasya sa space ng lobby sa ngayon, pero isasama namin sa renovation plan next year.', '2026-09-16 08:30:00'),
(6, 6, 267, NULL, 'Hi Jasmine, naka-crop po kasi yung screenshot kaya hindi makita ang reference number. Paki-upload po ulit yung buong receipt.', '2026-09-30 16:13:55'),
(7, 7, 268, NULL, 'Napalitan na po ang lock. Paki-kuha po ang bagong susi sa front desk.', '2026-09-22 12:30:00'),
(8, 9, 268, NULL, 'Na-inspect na po, may crack sa roof gutter. Schedule ang repair ngayong Sabado.', '2026-09-28 09:30:00'),
(9, 9, NULL, 20, 'Sige po, thank you. Ililipat ko muna yung gamit ko.', '2026-09-28 12:30:00');

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
(3, 'Teresita Mendoza', 'owner@nestph.test', NULL, '$2y$12$4y5BjtNpf7P7PQ0VXyHu3.YAY2Cv4hwnmwGIjK6uCfbfRC0Q5qaea', 2, 1, NULL, '2026-07-24 22:12:00', '2026-09-30 16:13:55'),
(266, 'Kristine Joy Bautista', 'kristine.bautista@nestph.test', NULL, '$2y$12$4y5BjtNpf7P7PQ0VXyHu3.YAY2Cv4hwnmwGIjK6uCfbfRC0Q5qaea', 2, 1, NULL, '2026-04-09 16:00:00', '2026-04-09 16:00:00'),
(267, 'Mark Anthony Villanueva', 'mark.villanueva@nestph.test', NULL, '$2y$12$4y5BjtNpf7P7PQ0VXyHu3.YAY2Cv4hwnmwGIjK6uCfbfRC0Q5qaea', 2, 1, NULL, '2026-04-18 16:00:00', '2026-04-18 16:00:00'),
(268, 'Jerome Castillo', 'jerome.castillo@nestph.test', NULL, '$2y$12$4y5BjtNpf7P7PQ0VXyHu3.YAY2Cv4hwnmwGIjK6uCfbfRC0Q5qaea', 2, 1, NULL, '2026-04-27 16:00:00', '2026-04-27 16:00:00'),
(269, 'Lea Mae Fernandez', 'lea.fernandez@nestph.test', NULL, '$2y$12$4y5BjtNpf7P7PQ0VXyHu3.YAY2Cv4hwnmwGIjK6uCfbfRC0Q5qaea', 2, 0, NULL, '2026-05-06 16:00:00', '2026-05-06 16:00:00'),
(270, 'Maria Angelica Santos', 'maria.santos@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-04-12 02:15:00', '2026-04-12 02:15:00'),
(271, 'Kimberly Anne Dela Cruz', 'kimberly.delacruz@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-19 03:15:00', '2026-06-19 03:15:00'),
(272, 'Patricia Mae Gonzales', 'patricia.gonzales@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-07-16 04:15:00', '2026-07-16 04:15:00'),
(273, 'Nicole Joy Ramos', 'nicole.ramos@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-07-07 05:15:00', '2026-07-07 05:15:00'),
(274, 'Juan Miguel Reyes', 'juan.reyes@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-03-08 06:15:00', '2026-03-08 06:15:00'),
(275, 'John Paul Mendoza', 'john.mendoza@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-08 07:15:00', '2026-06-08 07:15:00'),
(276, 'Mark Joseph Aquino', 'mark.aquino@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-08-16 08:15:00', '2026-08-16 08:15:00'),
(277, 'Christian Dave Torres', 'christian.torres@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-05-17 01:15:00', '2026-05-17 01:15:00'),
(278, 'Benjamin Robles', 'benjamin.robles@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-11 02:15:00', '2026-06-11 02:15:00'),
(279, 'Rafael Luis Navarro', 'rafael.navarro@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-04-11 03:15:00', '2026-04-11 03:15:00'),
(280, 'Angela Marie Villanueva', 'angela.villanueva@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-01-02 04:15:00', '2026-01-02 04:15:00'),
(281, 'Jasmine Rose Garcia', 'jasmine.garcia@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-07-20 05:15:00', '2026-07-20 05:15:00'),
(282, 'Camille Louise Flores', 'camille.flores@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-09 06:15:00', '2026-06-09 06:15:00'),
(283, 'Bea Katrina Pascual', 'bea.pascual@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-19 07:15:00', '2026-06-19 07:15:00'),
(284, 'Princess Joy Manalo', 'princess.manalo@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-16 08:15:00', '2026-06-16 08:15:00'),
(285, 'Kathleen Mae Salazar', 'kathleen.salazar@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-09-26 01:15:00', '2026-09-26 01:15:00'),
(286, 'Carlo Miguel Bautista', 'carlo.bautista@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-07-12 02:15:00', '2026-07-12 02:15:00'),
(287, 'Joshua Emmanuel Lim', 'joshua.lim@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-05-02 03:15:00', '2026-05-02 03:15:00'),
(288, 'Paolo Andres Ocampo', 'paolo.ocampo@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-09-23 04:15:00', '2026-09-23 04:15:00'),
(289, 'Andrea Nicole Tan', 'andrea.tan@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-04-17 05:15:00', '2026-04-17 05:15:00'),
(290, 'Erika Jane Morales', 'erika.morales@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-09 06:15:00', '2026-06-09 06:15:00'),
(291, 'Hannah Grace Soriano', 'hannah.soriano@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-08-17 07:15:00', '2026-08-17 07:15:00'),
(292, 'Gabriel Jose Rivera', 'gabriel.rivera@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 0, NULL, '2026-03-12 08:15:00', '2026-03-12 08:15:00'),
(293, 'Joseph Allan Cruz', 'joseph.cruz@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-03-31 01:15:00', '2026-03-31 01:15:00'),
(294, 'Stephanie Claire Uy', 'stephanie.uy@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-02-07 02:15:00', '2026-02-07 02:15:00'),
(295, 'Adrian Paul Castro', 'adrian.castro@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-06-11 03:15:00', '2026-06-11 03:15:00'),
(296, 'Vincent Ray Magbanua', 'vincent.magbanua@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-07-12 04:15:00', '2026-07-12 04:15:00'),
(297, 'Luis Antonio Del Rosario', 'luis.delrosario@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-08-30 05:15:00', '2026-08-30 05:15:00'),
(298, 'Jerome Anthony Pineda', 'jerome.pineda@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-09-05 06:15:00', '2026-09-05 06:15:00'),
(299, 'Kenneth Bryan Sy', 'kenneth.sy@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-09-21 07:15:00', '2026-09-21 07:15:00'),
(300, 'Emmanuel Jose Villareal', 'emmanuel.villareal@gmail.com', NULL, '$2y$12$yRpIjzdAlZCmRNMIww5q8uUU49usbB2FTWbWFUUH826Y04mEGkxb2', 1, 1, NULL, '2026-04-28 08:15:00', '2026-04-28 08:15:00'),
(301, 'Janelle Tolentino', 'janelle.tolentino@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-19 02:30:00', '2026-02-28 16:00:00'),
(302, 'Oliver Galang', 'oliver.galang@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-24 03:30:00', '2026-02-06 16:00:00'),
(303, 'Elijah Sison', 'elijah.sison@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-11 04:30:00', '2026-06-21 16:00:00'),
(304, 'Irish Umali', 'irish.umali@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-16 05:30:00', '2026-02-24 16:00:00'),
(305, 'Nathan Tolentino', 'nathan.tolentino@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-04-08 06:30:00', '2026-07-19 16:00:00'),
(306, 'Clarisse Figueroa', 'clarisse.figueroa@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-13 07:30:00', '2026-01-23 16:00:00'),
(307, 'Jericho Figueroa', 'jericho.figueroa@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-12 08:30:00', '2026-03-08 16:00:00'),
(308, 'Alyssa Ilagan', 'alyssa.ilagan@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-31 01:30:00', '2026-04-12 16:00:00'),
(309, 'Queenie Zamora', 'queenie.zamora@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-27 02:30:00', '2026-02-09 16:00:00'),
(310, 'Ella Ortega', 'ella.ortega@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-17 03:30:00', '2026-08-18 16:00:00'),
(311, 'Irish Galang', 'irish.galang@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-14 04:30:00', '2026-03-22 16:00:00'),
(312, 'Oliver Figueroa', 'oliver.figueroa@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-11 05:30:00', '2026-03-20 16:00:00'),
(313, 'Queenie Figueroa', 'queenie.figueroa@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-10-01 06:30:00', '2026-05-11 16:00:00'),
(314, 'Sofia Zamora', 'sofia.zamora@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-09 07:30:00', '2025-12-20 16:00:00'),
(315, 'Marco Galang', 'marco.galang@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-12 08:30:00', '2026-03-24 16:00:00'),
(316, 'Bianca Nepomuceno', 'bianca.nepomuceno@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-04-15 01:30:00', '2026-07-22 16:00:00'),
(317, 'Warren Macaraeg', 'warren.macaraeg@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-10-05 02:30:00', '2026-03-12 16:00:00'),
(318, 'Francine Cordero', 'francine.cordero@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-11 03:30:00', '2026-05-19 16:00:00'),
(319, 'Vanessa Umali', 'vanessa.umali@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-15 04:30:00', '2025-12-24 16:00:00'),
(320, 'Ysabel Figueroa', 'ysabel.figueroa@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-01-14 05:30:00', '2026-06-16 16:00:00'),
(321, 'Elijah Fernandez', 'elijah.fernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-05 06:30:00', '2026-03-16 16:00:00'),
(322, 'Harold Hernandez', 'harold.hernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-20 07:30:00', '2026-09-27 16:00:00'),
(323, 'Francine Nepomuceno', 'francine.nepomuceno@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-04 08:30:00', '2026-01-17 16:00:00'),
(324, 'Bryan Macaraeg', 'bryan.macaraeg@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-02-24 01:30:00', '2026-07-14 16:00:00'),
(325, 'Gwen Abad', 'gwen.abad@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-10-05 02:30:00', '2026-01-13 16:00:00'),
(326, 'Ella Yap', 'ella.yap@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-01-22 03:30:00', '2026-06-30 16:00:00'),
(327, 'Gian Hernandez', 'gian.hernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-08 04:30:00', '2026-01-18 16:00:00'),
(328, 'Clarisse Cordero', 'clarisse.cordero@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-02-04 05:30:00', '2026-05-05 16:00:00'),
(329, 'Lianne Quiambao', 'lianne.quiambao@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-19 06:30:00', '2026-06-01 16:00:00'),
(330, 'Dominic Agustin', 'dominic.agustin@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-06-04 07:30:00', '2026-09-27 16:00:00'),
(331, 'Marco Rosales', 'marco.rosales@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-07 08:30:00', '2026-02-27 16:00:00'),
(332, 'Aldrin Dizon', 'aldrin.dizon@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-26 01:30:00', '2025-12-03 16:00:00'),
(333, 'Sofia Umali', 'sofia.umali@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-12-29 02:30:00', '2026-04-17 16:00:00'),
(334, 'Bianca Javier', 'bianca.javier@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-25 03:30:00', '2026-04-05 16:00:00'),
(335, 'Renz Javier', 'renz.javier@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-19 04:30:00', '2026-01-31 16:00:00'),
(336, 'Queenie Fernandez', 'queenie.fernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-02-25 05:30:00', '2026-07-09 16:00:00'),
(337, 'Kevin Macaraeg', 'kevin.macaraeg@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-21 06:30:00', '2026-05-04 16:00:00'),
(338, 'Danica Castillo', 'danica.castillo@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-24 07:30:00', '2026-04-01 16:00:00'),
(339, 'Oliver Umali', 'oliver.umali@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-04-16 08:30:00', '2026-07-11 16:00:00'),
(340, 'Troy Quiambao', 'troy.quiambao@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-02 01:30:00', '2026-03-15 16:00:00'),
(341, 'Irish Fernandez', 'irish.fernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-10-03 02:30:00', '2026-03-13 16:00:00'),
(342, 'Lance Rosales', 'lance.rosales@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-25 03:30:00', '2026-07-10 16:00:00'),
(343, 'Gian Ortega', 'gian.ortega@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-21 04:30:00', '2026-04-04 16:00:00'),
(344, 'Renz Ortega', 'renz.ortega@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-10 05:30:00', '2026-02-26 16:00:00'),
(345, 'Janelle Agustin', 'janelle.agustin@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-30 06:30:00', '2025-12-06 16:00:00'),
(346, 'Troy Panganiban', 'troy.panganiban@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-12-08 07:30:00', '2026-03-16 16:00:00'),
(347, 'Lance Umali', 'lance.umali@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-23 08:30:00', '2026-08-01 16:00:00'),
(348, 'Kyla Belmonte', 'kyla.belmonte@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-22 01:30:00', '2026-03-01 16:00:00'),
(349, 'Renz Abad', 'renz.abad@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-03-18 02:30:00', '2026-06-09 16:00:00'),
(350, 'Gian Macaraeg', 'gian.macaraeg@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-17 03:30:00', '2026-02-27 16:00:00'),
(351, 'Ivan Nepomuceno', 'ivan.nepomuceno@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-02-24 04:30:00', '2026-09-21 16:00:00'),
(352, 'Danica Abad', 'danica.abad@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-08-25 05:30:00', '2026-04-29 16:00:00'),
(353, 'Zach Fernandez', 'zach.fernandez@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2025-09-05 06:30:00', '2026-01-13 16:00:00'),
(354, 'Clarisse Dizon', 'clarisse.dizon@gmail.com', NULL, '$2y$12$JdU0Qct7rHTjF3GGBaAuTeSwW1/jgiy1FVHY5iKUIgeNgLj6Nd5SG', 1, 0, NULL, '2026-01-14 07:30:00', '2026-04-11 16:00:00');

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
(4, 5, 4, -9.4008, 19.1198, 'Lobby', '2026-09-04 12:01:09', '2026-09-04 12:01:09'),
(12, 12, 8, -11.3206, 114.0896, 'Go to Computer Room', '2026-09-30 08:54:53', '2026-09-30 08:54:53'),
(13, 8, 12, -12.8188, -102.5332, 'Go to Living Room', '2026-09-30 08:55:02', '2026-09-30 08:55:02');

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
(8, 18, 'Computer Room', 'vr-scenes/lMdjSMn8A122wbntaKbN868Fm7zATkwqiwu0GwOT.jpg', 'vr-scenes/filled/e522f4b9-f751-4777-9568-f832200ce33e.jpg', '2026-09-29 07:20:06', 1, 0, '2026-09-29 07:20:06', '2026-09-30 08:54:57', 360.00, 180.00, 0.00, 0),
(12, 18, 'Living Room', 'vr-scenes/xd1JiYq1xCKrAm6oaRNmZBseztpB2yfwIdG2vCdj.jpg', 'vr-scenes/filled/96ae1fb3-a61a-484a-967a-ebb984e8bc99.jpg', '2026-09-30 08:54:40', 0, 1, '2026-09-30 08:54:40', '2026-09-30 08:54:57', 360.00, 180.00, 0.00, 0);

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
-- Indexes for table `deposit_refunds`
--
ALTER TABLE `deposit_refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_refunds_tenant_id_foreign` (`tenant_id`),
  ADD KEY `deposit_refunds_recorded_by_foreign` (`recorded_by`);

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
-- Indexes for table `monthly_expenses`
--
ALTER TABLE `monthly_expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_expenses_month_unique` (`month`),
  ADD KEY `monthly_expenses_recorded_by_foreign` (`recorded_by`);

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
-- Indexes for table `tenant_notifications`
--
ALTER TABLE `tenant_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenant_notifications_dedupe_key_unique` (`dedupe_key`),
  ADD KEY `tenant_notifications_tenant_id_read_at_index` (`tenant_id`,`read_at`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `billing_statements`
--
ALTER TABLE `billing_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=476;

--
-- AUTO_INCREMENT for table `damages`
--
ALTER TABLE `damages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deposit_refunds`
--
ALTER TABLE `deposit_refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `monthly_expenses`
--
ALTER TABLE `monthly_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=468;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `tenant_notifications`
--
ALTER TABLE `tenant_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT for table `vr_hotspots`
--
ALTER TABLE `vr_hotspots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vr_scenes`
--
ALTER TABLE `vr_scenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `deposit_refunds`
--
ALTER TABLE `deposit_refunds`
  ADD CONSTRAINT `deposit_refunds_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `deposit_refunds_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `monthly_expenses`
--
ALTER TABLE `monthly_expenses`
  ADD CONSTRAINT `monthly_expenses_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `tenant_notifications`
--
ALTER TABLE `tenant_notifications`
  ADD CONSTRAINT `tenant_notifications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

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
