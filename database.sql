-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2026 at 03:40 PM
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
(1, 355, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms, manage_contracts, manage_billing, view_reports', '2026-04-09 16:00:00'),
(2, 356, 3, 'granted', 'Admin access granted: manage_billing, view_reports', '2026-04-18 16:00:00'),
(3, 357, 3, 'granted', 'Admin access granted: manage_tenants, manage_rooms', '2026-04-27 16:00:00'),
(4, 358, 3, 'granted', 'Admin access granted: manage_tenants, view_reports', '2026-05-06 16:00:00'),
(5, 356, 3, 'privileges_updated', 'Privileges updated: manage_billing, view_reports', '2026-08-21 16:00:00'),
(6, 358, 3, 'revoked', 'Admin access revoked. Reason: No longer employed at the dormitory.', '2026-09-09 16:00:00');

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
(1, 3, '4c1dcc1c6c6e70f27f699d9523794fefa9de16cd', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(2, 3, '5257c1f413686decdbad36b9fdbba4fd0ea12452', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(3, 3, 'ea4478a366140c40a8eed6ded97ce367a87dbe58', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 12:22:00', '2026-09-27 15:34:00'),
(4, 3, 'c85ea054b8ddea7ccbc911f4f78f0f4100c6c045', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 09:28:00', '2026-09-25 12:40:00'),
(5, 3, 'b4cf208399ed38280ab36812a4f0b51f382e5aed', '192.168.1.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-22 12:37:00', '2026-09-22 15:49:00'),
(6, 3, '1c547da42713df88ae56be450fe9e649f6dbf4f5', '192.168.1.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-18 11:49:00', '2026-09-18 15:01:00'),
(7, 355, 'd682d90947877d94fbe623bf05eb993546e9e893', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-10-01 08:10:00', NULL),
(8, 355, 'cbdbf40913d0d6f1496af6b7b6a7ef198ca95cbe', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(9, 355, '50ef494b95ba9522baf4c5f4dfafa60d98481c5f', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(10, 355, 'fdd1fac84d1967c6fb7edb343470e5761f5d4718', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 11:19:00', '2026-09-28 14:31:00'),
(11, 355, '39663146a14394fd00a7d68d72182dccc1982b22', '192.168.1.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-26 08:25:00', '2026-09-26 11:37:00'),
(12, 355, '4b3dcc1901ce5ebd5548b969f43d768005428c6e', '192.168.1.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-24 10:31:00', '2026-09-24 13:43:00'),
(13, 355, '778a347f56944ea6b67a0f398bf30b810ad80e68', '192.168.1.28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-23 11:34:00', '2026-09-23 14:46:00'),
(14, 356, '20fa6961105c167b7749e7dcad68d014d6b19fec', '192.168.1.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-30 09:13:00', '2026-09-30 12:25:00'),
(15, 356, 'a3aa7aada89777908ba2df58291865f7d3423de0', '192.168.1.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-28 11:19:00', '2026-09-28 14:31:00'),
(16, 356, 'c8443ca88c3c734960e2a7bc8c92938e93a8b16a', '192.168.1.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-25 09:28:00', '2026-09-25 12:40:00'),
(17, 356, '581f01d4b3ee72f3fb81fa9ace9006dc556e10ef', '192.168.1.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-21 08:40:00', '2026-09-21 11:52:00'),
(18, 357, '3af6034f737c939d50b1bd2b943bd9f2671592a8', '192.168.1.20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-10-01 08:10:00', NULL),
(19, 357, 'c018391c8f50d4a269a800b55149cac126185e2f', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-29 10:16:00', '2026-09-29 13:28:00'),
(20, 357, 'fdb586bdb0024c5e8bcb386e2ee56440c6432cba', '192.168.1.24', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-27 12:22:00', '2026-09-27 15:34:00'),
(21, 357, 'b32a3889e2f843f6988bed99560963eb8e24b225', '192.168.1.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2026-09-20 09:43:00', '2026-09-20 12:55:00'),
(22, 3, 'WkeYbF5VxJ8WLYN5iV4CJvKpgZF3NYHOnwkkWYlL', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 23:18:52', NULL);

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
(94, 355, 3, 'manage_tenants', '2026-04-09 16:00:00'),
(95, 355, 3, 'manage_rooms', '2026-04-09 16:00:00'),
(96, 355, 3, 'manage_contracts', '2026-04-09 16:00:00'),
(97, 355, 3, 'manage_billing', '2026-04-09 16:00:00'),
(98, 355, 3, 'view_reports', '2026-04-09 16:00:00'),
(99, 356, 3, 'manage_billing', '2026-04-18 16:00:00'),
(100, 356, 3, 'view_reports', '2026-04-18 16:00:00'),
(101, 357, 3, 'manage_tenants', '2026-04-27 16:00:00'),
(102, 357, 3, 'manage_rooms', '2026-04-27 16:00:00');

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
(2, 355, 'Reminder: Rent is due on the 1st of every month. You have a 3-day grace period; after that, a one-time 10% late fee is added. Pay in cash at the admin office, or through our official GCash (0917 893 2970, Patricia Joy N.) or BDO account, then upload your proof of payment in the portal.', 0, '2026-09-25 00:30:00', '2026-09-25 00:30:00'),
(3, 3, 'Fire drill and building inspection next Wednesday, 3:00 PM. Attendance is required for all tenants who are in the building. Please review the evacuation plan posted on every floor (Rules and Regulations, item 12). Comments are turned off for this post.', 1, '2026-09-21 00:30:00', '2026-09-21 00:30:00');

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
(3, 1, 355, NULL, 'Opo, lahat ng floors po. May drum ng tubig sa ground floor na pwede gamitin.', '2026-09-30 05:30:00'),
(4, 2, NULL, 8, 'Pwede po ba partial muna ngayong week?', '2026-09-25 01:30:00'),
(5, 2, 356, NULL, 'Pwede po, pero may 10% late fee sa natitirang upa kung lumampas sa 3-day grace period.', '2026-09-25 03:30:00');

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
  `emergency_contact_signed` tinyint(1) NOT NULL DEFAULT 0,
  `emergency_billing_consent` tinyint(1) NOT NULL DEFAULT 0,
  `bed_id` bigint(20) UNSIGNED NOT NULL,
  `preferred_start_date` date DEFAULT NULL,
  `tenant_end_date` date DEFAULT NULL,
  `type_of_tenant` varchar(30) DEFAULT NULL,
  `id_document_path` varchar(255) DEFAULT NULL,
  `signed_contract_path` varchar(255) DEFAULT NULL,
  `dpa_consent` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected','re_application_requested','cancelled') NOT NULL DEFAULT 'pending',
  `overdue_notified_at` timestamp NULL DEFAULT NULL,
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

INSERT INTO `applications` (`id`, `inquiry_id`, `tenant_id`, `first_name`, `last_name`, `birthdate`, `gender`, `nationality`, `medical_condition`, `occupation`, `school_company`, `school_company_address`, `contact_number`, `email`, `landline`, `home_address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_email`, `emergency_contact_landline`, `emergency_contact_relation`, `emergency_contact_signed`, `emergency_billing_consent`, `bed_id`, `preferred_start_date`, `tenant_end_date`, `type_of_tenant`, `id_document_path`, `signed_contract_path`, `dpa_consent`, `status`, `overdue_notified_at`, `rejection_reason`, `re_application_note`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Maria Angelica', 'Santos', '2004-03-14', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09181242486', 'maria.santos@gmail.com', NULL, 'Brgy. San Isidro, Angono, Rizal', 'Rodelio Santos', '09182034386', 'rodelio.santos.parent@gmail.com', NULL, 'Father', 0, 0, 1, '2026-04-11', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-31 02:15:00', '2026-04-02 02:15:00'),
(2, NULL, 2, 'Kimberly Anne', 'Dela Cruz', '2005-07-02', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'kimberly.delacruz@gmail.com', NULL, '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'Marites Dela Cruz', '09212408565', 'marites.delacruz.parent@gmail.com', NULL, 'Mother', 0, 0, 2, '2026-06-03', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-22 03:15:00', '2026-05-24 03:15:00'),
(3, NULL, 3, 'Patricia Mae', 'Gonzales', '2003-11-21', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'patricia.gonzales@gmail.com', NULL, 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'Lorna Gonzales', '09212408565', 'lorna.gonzales.parent@gmail.com', NULL, 'Mother', 0, 0, 3, '2026-07-05', '2027-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-22 04:15:00', '2026-06-24 04:15:00'),
(4, NULL, 4, 'Nicole Joy', 'Ramos', '2004-01-09', 'female', 'Filipino', 'None', 'Student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila', '09451266243', 'nicole.ramos@gmail.com', NULL, 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'Ernesto Ramos', '09452058143', 'ernesto.ramos.parent@gmail.com', NULL, 'Father', 0, 0, 38, '2026-07-13', '2026-10-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-29 05:15:00', '2026-07-01 05:15:00'),
(5, NULL, 5, 'Juan Miguel', 'Reyes', '1999-05-30', 'male', 'Filipino', 'None', 'Employee', 'Accenture Philippines', 'Cyber One Bldg., Eastwood City, Quezon City', '09561274162', 'juan.reyes@gmail.com', NULL, '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'Carmelita Reyes', '09562066062', 'carmelita.reyes.parent@gmail.com', NULL, 'Mother', 0, 0, 5, '2026-03-16', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-06 06:15:00', '2026-03-08 06:15:00'),
(6, NULL, 6, 'John Paul', 'Mendoza', '2004-08-17', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Mapúa University', 'Muralla St., Intramuros, Manila', '09212408565', 'john.mendoza@gmail.com', NULL, 'Brgy. Bagong Silang, Lucena City, Quezon', 'Rosalie Mendoza', '09212408565', 'rosalie.mendoza.parent@gmail.com', NULL, 'Mother', 0, 0, 48, '2026-05-15', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-04 07:15:00', '2026-05-06 07:15:00'),
(7, NULL, 7, 'Mark Joseph', 'Aquino', '2005-02-11', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09981290000', 'mark.aquino@gmail.com', NULL, 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'Josefina Aquino', '09982081900', 'josefina.aquino.parent@gmail.com', NULL, 'Mother', 0, 0, 9, '2026-08-06', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-25 08:15:00', '2026-07-27 08:15:00'),
(8, NULL, 8, 'Christian Dave', 'Torres', '2002-12-03', 'male', 'Filipino', 'None', 'Employee', 'Jollibee Foods Corp. (V. Mapa branch)', 'V. Mapa St., Sta. Mesa, Manila', '09212408565', 'christian.torres@gmail.com', NULL, '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'Dante Torres', '09212408565', 'dante.torres.parent@gmail.com', NULL, 'Father', 0, 0, 10, '2026-05-04', '2027-03-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-21 01:15:00', '2026-04-23 01:15:00'),
(9, NULL, 9, 'Benjamin', 'Robles', '2004-06-25', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'benjamin.robles@gmail.com', NULL, 'Brgy. Santo Cristo, San Fernando, Pampanga', 'Evelyn Robles', '09212408565', 'evelyn.robles.parent@gmail.com', NULL, 'Mother', 0, 0, 11, '2026-05-09', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-25 02:15:00', '2026-04-27 02:15:00'),
(10, NULL, 10, 'Rafael Luis', 'Navarro', '2001-09-14', 'male', 'Filipino', 'None', 'Employee', 'BDO Unibank, Sta. Mesa Branch', 'Ramon Magsaysay Blvd., Sta. Mesa, Manila', '09171313757', 'rafael.navarro@gmail.com', NULL, '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'Gloria Navarro', '09172105657', 'gloria.navarro.parent@gmail.com', NULL, 'Mother', 0, 0, 57, '2026-04-13', '2026-09-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-03 03:15:00', '2026-04-05 03:15:00'),
(11, NULL, 11, 'Angela Marie', 'Villanueva', '1998-04-19', 'female', 'Filipino', 'None', 'Employee', 'Philippine General Hospital', 'Taft Ave., Ermita, Manila', '09181321676', 'angela.villanueva@gmail.com', NULL, 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'Arnel Villanueva', '09182113576', 'arnel.villanueva.parent@gmail.com', NULL, 'Father', 0, 0, 15, '2026-01-21', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-10 04:15:00', '2026-01-12 04:15:00'),
(12, NULL, 12, 'Jasmine Rose', 'Garcia', '2005-10-05', 'female', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Centro Escolar University', 'Mendiola St., San Miguel, Manila', '09212408565', 'jasmine.garcia@gmail.com', NULL, 'Purok 2, Brgy. Talon, Las Piñas City', 'Ramil Garcia', '09212408565', 'ramil.garcia.parent@gmail.com', NULL, 'Father', 0, 0, 16, '2026-07-02', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-20 05:15:00', '2026-06-22 05:15:00'),
(13, NULL, 13, 'Camille Louise', 'Flores', '2004-12-28', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'camille.flores@gmail.com', NULL, 'Brgy. Mabini, Batangas City, Batangas', 'Susan Flores', '09212408565', 'susan.flores.parent@gmail.com', NULL, 'Mother', 0, 0, 17, '2026-05-12', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-29 06:15:00', '2026-05-01 06:15:00'),
(14, NULL, 14, 'Bea Katrina', 'Pascual', '2003-05-16', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09212408565', 'bea.pascual@gmail.com', NULL, 'Brgy. Sta. Rita, Olongapo City, Zambales', 'Gerardo Pascual', '09212408565', 'gerardo.pascual.parent@gmail.com', NULL, 'Father', 0, 0, 18, '2026-06-01', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-18 07:15:00', '2026-05-20 07:15:00'),
(15, NULL, 15, 'Princess Joy', 'Manalo', '2002-08-08', 'female', 'Filipino', 'None', 'Student', 'Pamantasan ng Lungsod ng Maynila', 'General Luna St., Intramuros, Manila', '09561353352', 'princess.manalo@gmail.com', NULL, 'Brgy. Parian, Calamba City, Laguna', 'Cristina Manalo', '09562145252', 'cristina.manalo.parent@gmail.com', NULL, 'Mother', 0, 0, 19, '2026-06-08', '2027-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-29 08:15:00', '2026-05-31 08:15:00'),
(16, NULL, 16, 'Kathleen Mae', 'Salazar', '2006-01-30', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09661361271', 'kathleen.salazar@gmail.com', NULL, 'Brgy. Poblacion, Tagum City, Davao del Norte', 'Rogelio Salazar', '09662153171', 'rogelio.salazar.parent@gmail.com', NULL, 'Father', 0, 0, 20, '2026-10-05', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-09-24 01:15:00', '2026-09-26 01:15:00'),
(17, NULL, 17, 'Carlo Miguel', 'Bautista', '2000-03-03', 'male', 'Filipino', 'None', 'Employee', 'Globe Telecom', 'The Globe Tower, BGC, Taguig City', '09981369190', 'carlo.bautista@gmail.com', NULL, 'San Pablo City, Laguna', 'Nenita Bautista', '09982161090', 'nenita.bautista.parent@gmail.com', NULL, 'Mother', 0, 0, 21, '2026-07-10', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-28 02:15:00', '2026-06-30 02:15:00'),
(18, NULL, 18, 'Joshua Emmanuel', 'Lim', '2004-11-11', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'De La Salle University', 'Taft Ave., Malate, Manila', '09081377109', 'joshua.lim@gmail.com', NULL, 'Sta. Cruz, Laguna', 'Wilson Lim', '09082169009', 'wilson.lim.parent@gmail.com', NULL, 'Father', 0, 0, 25, '2026-05-19', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-06 03:15:00', '2026-05-08 03:15:00'),
(19, NULL, 19, 'Paolo Andres', 'Ocampo', '2005-04-22', 'male', 'Filipino', 'None', 'Student', 'Adamson University', 'San Marcelino St., Ermita, Manila', '09951385028', 'paolo.ocampo@gmail.com', NULL, 'Brgy. Poblacion, Malolos City, Bulacan', 'Amelia Ocampo', '09952176928', 'amelia.ocampo.parent@gmail.com', NULL, 'Mother', 0, 0, 26, '2026-10-05', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-09-21 04:15:00', '2026-09-23 04:15:00'),
(20, NULL, 20, 'Andrea Nicole', 'Tan', '1997-09-09', 'female', 'Filipino', 'None', 'Employee', 'SM Supermalls Corporate Office', 'Mall of Asia Complex, Pasay City', '09171392947', 'andrea.tan@gmail.com', NULL, 'Brgy. Kauswagan, Cagayan de Oro City', 'Rebecca Tan', '09172184847', 'rebecca.tan.parent@gmail.com', NULL, 'Mother', 0, 0, 29, '2026-04-07', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-28 05:15:00', '2026-03-30 05:15:00'),
(21, NULL, 21, 'Erika Jane', 'Morales', '2004-07-07', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09181400866', 'erika.morales@gmail.com', NULL, 'Brgy. Pantal, Dagupan City, Pangasinan', 'Ronaldo Morales', '09182192766', 'ronaldo.morales.parent@gmail.com', NULL, 'Father', 0, 0, 35, '2026-06-14', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-03 06:15:00', '2026-06-05 06:15:00'),
(22, NULL, 22, 'Hannah Grace', 'Soriano', '2006-02-14', 'female', 'Filipino', 'None', 'Student', 'Philippine Normal University', 'Taft Ave., Ermita, Manila', '09271408785', 'hannah.soriano@gmail.com', NULL, 'Brgy. Dolores, Taytay, Rizal', 'Marilou Soriano', '09272200685', 'marilou.soriano.parent@gmail.com', NULL, 'Mother', 0, 0, 36, '2026-08-05', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-24 07:15:00', '2026-07-26 07:15:00'),
(23, NULL, 23, 'Gabriel Jose', 'Rivera', '2001-06-01', 'male', 'Filipino', 'None', 'Employee', 'Meralco', 'Ortigas Ave., Pasig City', '09391416704', 'gabriel.rivera@gmail.com', NULL, 'Brgy. San Antonio, Biñan, Laguna', 'Imelda Rivera', '09392208604', 'imelda.rivera.parent@gmail.com', NULL, 'Mother', 0, 0, 41, '2026-02-17', '2026-07-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-04 08:15:00', '2026-02-06 08:15:00'),
(24, NULL, 24, 'Joseph Allan', 'Cruz', '2003-03-27', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Emilio Aguinaldo College', 'Gen. Malvar St., Malate, Manila', '09212408565', 'joseph.cruz@gmail.com', NULL, 'Brgy. Tabing Ilog, Marilao, Bulacan', 'Nora Cruz', '09212408565', 'nora.cruz.parent@gmail.com', NULL, 'Mother', 0, 0, 45, '2026-03-24', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-10 01:15:00', '2026-03-12 01:15:00'),
(25, NULL, 25, 'Stephanie Claire', 'Uy', '2004-09-02', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09561432542', 'stephanie.uy@gmail.com', NULL, 'Brgy. Lourdes, Dagupan City, Pangasinan', 'Henry Uy', '09562224442', 'henry.uy.parent@gmail.com', NULL, 'Father', 0, 0, 4, '2026-02-17', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-07 02:15:00', '2026-02-09 02:15:00'),
(26, NULL, 26, 'Adrian Paul', 'Castro', '1999-12-12', 'male', 'Filipino', 'None', 'Employee', 'Teleperformance Philippines', 'Robinsons Cybergate, Mandaluyong City', '09661440461', 'adrian.castro@gmail.com', NULL, 'Brgy. Sampaloc, Tanauan City, Batangas', 'Leticia Castro', '09662232361', 'leticia.castro.parent@gmail.com', NULL, 'Mother', 0, 0, 6, '2026-06-12', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-01 03:15:00', '2026-06-03 03:15:00'),
(27, NULL, 27, 'Vincent Ray', 'Magbanua', '2005-06-19', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09981448380', 'vincent.magbanua@gmail.com', NULL, 'Brgy. Poblacion, Roxas City, Capiz', 'Ramon Magbanua', '09982240280', 'ramon.magbanua.parent@gmail.com', NULL, 'Father', 0, 0, 12, '2026-07-10', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-28 04:15:00', '2026-06-30 04:15:00'),
(28, NULL, 28, 'Luis Antonio', 'Del Rosario', '2003-02-27', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09212408565', 'luis.delrosario@gmail.com', NULL, 'Brgy. Cutcut, Angeles City, Pampanga', 'Rowena Del Rosario', '09212408565', 'rowena.delrosario.parent@gmail.com', NULL, 'Mother', 0, 0, 41, '2026-09-22', '2027-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-09-09 05:15:00', '2026-09-11 05:15:00'),
(29, NULL, 29, 'Jerome Anthony', 'Pineda', '2004-10-30', 'male', 'Filipino', 'None', 'Student', 'Mapúa University', 'Muralla St., Intramuros, Manila', '09951464218', 'jerome.pineda@gmail.com', NULL, 'Brgy. Poblacion, Tarlac City, Tarlac', 'Grace Pineda', '09952256118', 'grace.pineda.parent@gmail.com', NULL, 'Mother', 0, 0, 45, '2026-09-15', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-09-01 06:15:00', '2026-09-03 06:15:00'),
(30, NULL, 30, 'Kenneth Bryan', 'Sy', '2006-01-08', 'male', 'Filipino', 'Asthma (mild, with inhaler)', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'kenneth.sy@gmail.com', NULL, 'Brgy. Balibago, Sta. Rosa City, Laguna', 'Victor Sy', '09212408565', 'victor.sy.parent@gmail.com', NULL, 'Father', 0, 0, 49, '2026-09-03', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-08-24 07:15:00', '2026-08-26 07:15:00'),
(31, NULL, 31, 'Emmanuel Jose', 'Villareal', '2002-07-21', 'male', 'Filipino', 'None', 'Employee', '7-Eleven (Legarda branch)', 'Legarda St., Sampaloc, Manila', '09181480056', 'emmanuel.villareal@gmail.com', NULL, 'Brgy. San Vicente, Tacloban City, Leyte', 'Nelia Villareal', '09182271956', 'nelia.villareal.parent@gmail.com', NULL, 'Mother', 0, 0, 50, '2026-05-25', '2027-03-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-14 08:15:00', '2026-05-16 08:15:00'),
(32, NULL, 32, 'Pauline', 'Delos Reyes', '2007-04-14', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'pauline.delosreyes@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Delos Reyes', '09212408565', 'roberto.delosreyes.parent@gmail.com', NULL, 'Mother', 0, 0, 7, '2026-07-23', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-15 06:40:00', '2026-07-17 06:40:00'),
(33, NULL, 33, 'Froilan', 'Natividad', '2006-05-31', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'froilan.natividad@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Natividad', '09212408565', 'lourdes.natividad.parent@gmail.com', NULL, 'Father', 0, 0, 8, '2026-06-27', '2027-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-18 07:40:00', '2026-06-20 07:40:00'),
(34, NULL, 34, 'Krizia', 'Natividad', '2006-03-14', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09212408565', 'krizia.natividad@gmail.com', NULL, 'Brgy. Malabanias, Angeles City, Pampanga', 'Ricardo Natividad', '09212408565', 'ricardo.natividad.parent@gmail.com', NULL, 'Mother', 0, 0, 23, '2026-07-04', '2026-11-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-24 08:40:00', '2026-06-26 08:40:00'),
(35, NULL, 35, 'Jamaica', 'Evangelista', '2004-10-07', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09212408565', 'jamaica.evangelista@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City, Quezon', 'Elena Evangelista', '09212408565', 'elena.evangelista.parent@gmail.com', NULL, 'Father', 0, 0, 24, '2026-07-10', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-29 01:40:00', '2026-07-01 01:40:00'),
(36, NULL, 36, 'Patrick', 'Espiritu', '2007-09-04', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09565233662', 'patrick.espiritu@gmail.com', NULL, 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'Manuel Espiritu', '09566025562', 'manuel.espiritu.parent@gmail.com', NULL, 'Mother', 0, 0, 30, '2026-01-07', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-26 02:40:00', '2025-12-28 02:40:00'),
(37, NULL, 37, 'Lester', 'Ventura', '2005-03-06', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09665241581', 'lester.ventura@gmail.com', NULL, 'Brgy. Sto. Niño, San Fernando, La Union', 'Gemma Ventura', '09666033481', 'gemma.ventura.parent@gmail.com', NULL, 'Father', 0, 0, 31, '2026-02-07', '2027-05-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-25 03:40:00', '2026-01-27 03:40:00'),
(38, NULL, 38, 'Tristan', 'Dimaculangan', '1995-12-05', 'male', 'Filipino', 'None', 'Employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City', '09212408565', 'tristan.dimaculangan@gmail.com', NULL, 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Arturo Dimaculangan', '09212408565', 'arturo.dimaculangan.parent@gmail.com', NULL, 'Mother', 0, 0, 32, '2026-09-11', '2027-04-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-08-28 04:40:00', '2026-08-30 04:40:00'),
(39, NULL, 39, 'Aaron', 'Hidalgo', '2002-11-15', 'male', 'Filipino', 'None', 'Employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City', '09085257419', 'aaron.hidalgo@gmail.com', NULL, 'Brgy. Bayanan, Bacoor, Cavite', 'Rosario Hidalgo', '09086049319', 'rosario.hidalgo.parent@gmail.com', NULL, 'Father', 0, 0, 39, '2026-02-19', '2026-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-04 05:40:00', '2026-02-06 05:40:00'),
(40, NULL, 40, 'Carl', 'Lagman', '1998-03-28', 'male', 'Filipino', 'None', 'Employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila', '09955265338', 'carl.lagman@gmail.com', NULL, 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'Danilo Lagman', '09956057238', 'danilo.lagman.parent@gmail.com', NULL, 'Mother', 0, 0, 40, '2026-09-20', '2027-01-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-09-04 06:40:00', '2026-09-06 06:40:00'),
(41, NULL, 41, 'Gerald', 'Delos Reyes', '2005-04-20', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09175273257', 'gerald.delosreyes@gmail.com', NULL, 'Brgy. Poblacion, Batangas City, Batangas', 'Teresa Delos Reyes', '09176065157', 'teresa.delosreyes.parent@gmail.com', NULL, 'Father', 0, 0, 43, '2026-08-02', '2026-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-26 07:40:00', '2026-07-28 07:40:00'),
(42, NULL, 42, 'Darwin', 'Zaragoza', '2004-04-22', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09185281176', 'darwin.zaragoza@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Zaragoza', '09186073076', 'roberto.zaragoza.parent@gmail.com', NULL, 'Mother', 0, 0, 44, '2026-03-10', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-02 08:40:00', '2026-03-04 08:40:00'),
(43, NULL, 43, 'Darwin', 'Natividad', '2006-01-09', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09275289095', 'darwin.natividad@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Natividad', '09276080995', 'lourdes.natividad.parent@gmail.com', NULL, 'Father', 0, 0, 46, '2025-12-15', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-06 01:40:00', '2025-12-08 01:40:00'),
(44, NULL, 44, 'Jamaica', 'Cabrera', '2004-04-08', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09395297014', 'jamaica.cabrera@gmail.com', NULL, 'Brgy. Malabanias, Angeles City, Pampanga', 'Ricardo Cabrera', '09396088914', 'ricardo.cabrera.parent@gmail.com', NULL, 'Mother', 0, 0, 47, '2026-07-24', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-14 02:40:00', '2026-07-16 02:40:00'),
(45, NULL, 45, 'Odessa', 'Quinto', '2004-05-14', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09455304933', 'odessa.quinto@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City, Quezon', 'Elena Quinto', '09456096833', 'elena.quinto.parent@gmail.com', NULL, 'Father', 0, 0, 51, '2026-09-04', '2027-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-08-24 03:40:00', '2026-08-26 03:40:00'),
(46, NULL, 46, 'Elaine', 'Aguilar', '2006-08-02', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09565312852', 'elaine.aguilar@gmail.com', NULL, 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'Manuel Aguilar', '09566104752', 'manuel.aguilar.parent@gmail.com', NULL, 'Mother', 0, 0, 52, '2025-11-14', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-11-02 04:40:00', '2025-11-04 04:40:00'),
(47, NULL, 47, 'Aaron', 'Romualdez', '2005-10-07', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09665320771', 'aaron.romualdez@gmail.com', NULL, 'Brgy. Sto. Niño, San Fernando, La Union', 'Gemma Romualdez', '09666112671', 'gemma.romualdez.parent@gmail.com', NULL, 'Father', 0, 0, 53, '2026-04-09', '2027-05-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-27 05:40:00', '2026-03-29 05:40:00'),
(48, NULL, 48, 'Sean', 'Dimaculangan', '2001-04-24', 'male', 'Filipino', 'None', 'Employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City', '09985328690', 'sean.dimaculangan@gmail.com', NULL, 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Arturo Dimaculangan', '09986120590', 'arturo.dimaculangan.parent@gmail.com', NULL, 'Mother', 0, 0, 54, '2026-01-06', '2026-11-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-23 06:40:00', '2025-12-25 06:40:00'),
(49, NULL, 49, 'Bernadette', 'Romualdez', '2001-05-28', 'female', 'Filipino', 'None', 'Employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City', '09212408565', 'bernadette.romualdez@gmail.com', NULL, 'Brgy. Bayanan, Bacoor, Cavite', 'Rosario Romualdez', '09212408565', 'rosario.romualdez.parent@gmail.com', NULL, 'Father', 0, 0, 58, '2026-06-10', '2027-04-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-26 07:40:00', '2026-05-28 07:40:00'),
(50, NULL, 50, 'Lester', 'Romualdez', '1999-12-21', 'male', 'Filipino', 'None', 'Employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila', '09955344528', 'lester.romualdez@gmail.com', NULL, 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'Danilo Romualdez', '09956136428', 'danilo.romualdez.parent@gmail.com', NULL, 'Mother', 0, 0, 59, '2026-07-03', '2026-12-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-17 08:40:00', '2026-06-19 08:40:00'),
(51, NULL, 51, 'Rhea', 'Yambao', '2007-01-27', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09175352447', 'rhea.yambao@gmail.com', NULL, 'Brgy. Poblacion, Batangas City, Batangas', 'Teresa Yambao', '09176144347', 'teresa.yambao.parent@gmail.com', NULL, 'Father', 0, 0, 60, '2025-11-08', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-11-01 01:40:00', '2025-11-03 01:40:00'),
(52, NULL, 52, 'Jhon', 'Alcantara', '2007-05-05', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09185360366', 'jhon.alcantara@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Alcantara', '09186152266', 'roberto.alcantara.parent@gmail.com', NULL, 'Mother', 0, 0, 61, '2026-08-01', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-24 02:40:00', '2026-07-26 02:40:00'),
(53, NULL, 53, 'Carl', 'Dimaculangan', '2007-11-13', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'carl.dimaculangan@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Dimaculangan', '09212408565', 'lourdes.dimaculangan.parent@gmail.com', NULL, 'Father', 0, 0, 62, '2026-03-13', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-04 03:40:00', '2026-03-06 03:40:00'),
(54, NULL, 54, 'Gela', 'Aguilar', '2005-10-17', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09395376204', 'gela.aguilar@gmail.com', NULL, 'Brgy. Malabanias, Angeles City, Pampanga', 'Ricardo Aguilar', '09396168104', 'ricardo.aguilar.parent@gmail.com', NULL, 'Mother', 0, 0, 63, '2026-03-25', '2026-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-15 04:40:00', '2026-03-17 04:40:00'),
(55, NULL, 55, 'Isabelle', 'Ventura', '2008-06-04', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09455384123', 'isabelle.ventura@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City, Quezon', 'Elena Ventura', '09456176023', 'elena.ventura.parent@gmail.com', NULL, 'Father', 0, 0, 64, '2026-01-23', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-12 05:40:00', '2026-01-14 05:40:00'),
(56, NULL, 56, 'Froilan', 'Padilla', '2006-05-30', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09212408565', 'froilan.padilla@gmail.com', NULL, 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'Manuel Padilla', '09212408565', 'manuel.padilla.parent@gmail.com', NULL, 'Mother', 0, 0, 65, '2025-11-10', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-10-29 06:40:00', '2025-10-31 06:40:00'),
(57, NULL, 57, 'Charmaine', 'Mangubat', '2004-05-14', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'charmaine.mangubat@gmail.com', NULL, 'Brgy. Sto. Niño, San Fernando, La Union', 'Gemma Mangubat', '09212408565', 'gemma.mangubat.parent@gmail.com', NULL, 'Father', 0, 0, 66, '2026-01-15', '2027-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-02 07:40:00', '2026-01-04 07:40:00'),
(58, NULL, 58, 'Faith', 'Quinto', '2002-09-14', 'female', 'Filipino', 'None', 'Employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City', '09985407880', 'faith.quinto@gmail.com', NULL, 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Arturo Quinto', '09986199780', 'arturo.quinto.parent@gmail.com', NULL, 'Mother', 0, 0, 67, '2026-08-02', '2027-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-19 08:40:00', '2026-07-21 08:40:00'),
(59, NULL, 59, 'Charmaine', 'Bernardo', '1999-01-30', 'female', 'Filipino', 'None', 'Employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City', '09212408565', 'charmaine.bernardo@gmail.com', NULL, 'Brgy. Bayanan, Bacoor, Cavite', 'Rosario Bernardo', '09212408565', 'rosario.bernardo.parent@gmail.com', NULL, 'Father', 0, 0, 68, '2026-06-01', '2027-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-17 01:40:00', '2026-05-19 01:40:00'),
(60, NULL, 60, 'Kurt', 'Delos Reyes', '2001-12-18', 'male', 'Filipino', 'None', 'Employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila', '09955423718', 'kurt.delosreyes@gmail.com', NULL, 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'Danilo Delos Reyes', '09956215618', 'danilo.delosreyes.parent@gmail.com', NULL, 'Mother', 0, 0, 69, '2026-07-25', '2027-05-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-09 02:40:00', '2026-07-11 02:40:00'),
(61, NULL, 61, 'Owen', 'Lagman', '2007-04-27', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09175431637', 'owen.lagman@gmail.com', NULL, 'Brgy. Poblacion, Batangas City, Batangas', 'Teresa Lagman', '09176223537', 'teresa.lagman.parent@gmail.com', NULL, 'Father', 0, 0, 70, '2026-02-10', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-03 03:40:00', '2026-02-05 03:40:00'),
(62, NULL, 62, 'Bernadette', 'Yambao', '2004-07-26', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09185439556', 'bernadette.yambao@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Yambao', '09186231456', 'roberto.yambao.parent@gmail.com', NULL, 'Mother', 0, 0, 71, '2025-12-20', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-12 04:40:00', '2025-12-14 04:40:00'),
(63, NULL, 63, 'Noel', 'Cabrera', '2005-10-16', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09275447475', 'noel.cabrera@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Cabrera', '09276239375', 'lourdes.cabrera.parent@gmail.com', NULL, 'Father', 0, 0, 72, '2026-04-05', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-27 05:40:00', '2026-03-29 05:40:00'),
(64, NULL, 64, 'Lovely', 'Zaragoza', '2005-11-18', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09395455394', 'lovely.zaragoza@gmail.com', NULL, 'Brgy. Malabanias, Angeles City, Pampanga', 'Ricardo Zaragoza', '09396247294', 'ricardo.zaragoza.parent@gmail.com', NULL, 'Mother', 0, 0, 73, '2026-09-01', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-08-22 06:40:00', '2026-08-24 06:40:00'),
(65, NULL, 65, 'Maureen', 'Jimenez', '2007-08-10', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09455463313', 'maureen.jimenez@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City, Quezon', 'Elena Jimenez', '09456255213', 'elena.jimenez.parent@gmail.com', NULL, 'Father', 0, 0, 74, '2026-04-18', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-07 07:40:00', '2026-04-09 07:40:00'),
(66, NULL, 66, 'Krizia', 'Ventura', '2005-03-03', 'female', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09565471232', 'krizia.ventura@gmail.com', NULL, 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'Manuel Ventura', '09566263132', 'manuel.ventura.parent@gmail.com', NULL, 'Mother', 0, 0, 75, '2026-06-06', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-25 08:40:00', '2026-05-27 08:40:00'),
(67, NULL, 67, 'Darwin', 'Sarmiento', '2008-03-28', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09665479151', 'darwin.sarmiento@gmail.com', NULL, 'Brgy. Sto. Niño, San Fernando, La Union', 'Gemma Sarmiento', '09666271051', 'gemma.sarmiento.parent@gmail.com', NULL, 'Father', 0, 0, 76, '2025-12-22', '2027-05-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-09 01:40:00', '2025-12-11 01:40:00'),
(68, NULL, 68, 'Earl', 'Bonifacio', '2000-08-25', 'male', 'Filipino', 'None', 'Employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City', '09985487070', 'earl.bonifacio@gmail.com', NULL, 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Arturo Bonifacio', '09986278970', 'arturo.bonifacio.parent@gmail.com', NULL, 'Mother', 0, 0, 77, '2025-12-25', '2026-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-11 02:40:00', '2025-12-13 02:40:00'),
(69, NULL, 69, 'Gela', 'Bonifacio', '2003-05-09', 'female', 'Filipino', 'None', 'Employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City', '09212408565', 'gela.bonifacio@gmail.com', NULL, 'Brgy. Bayanan, Bacoor, Cavite', 'Rosario Bonifacio', '09212408565', 'rosario.bonifacio.parent@gmail.com', NULL, 'Father', 0, 0, 78, '2025-12-05', '2027-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-11-20 03:40:00', '2025-11-22 03:40:00'),
(70, NULL, 70, 'Althea', 'Dimaculangan', '1999-09-03', 'female', 'Filipino', 'None', 'Employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila', '09955502908', 'althea.dimaculangan@gmail.com', NULL, 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'Danilo Dimaculangan', '09956294808', 'danilo.dimaculangan.parent@gmail.com', NULL, 'Mother', 0, 0, 83, '2026-06-01', '2027-01-31', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-16 04:40:00', '2026-05-18 04:40:00'),
(71, NULL, 71, 'Isabelle', 'Alcantara', '2006-01-16', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09175510827', 'isabelle.alcantara@gmail.com', NULL, 'Brgy. Poblacion, Batangas City, Batangas', 'Teresa Alcantara', '09176302727', 'teresa.alcantara.parent@gmail.com', NULL, 'Father', 0, 0, 84, '2026-03-20', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-13 05:40:00', '2026-03-15 05:40:00'),
(72, NULL, 72, 'Carl', 'Padilla', '2004-05-01', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09185518746', 'carl.padilla@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Padilla', '09186310646', 'roberto.padilla.parent@gmail.com', NULL, 'Mother', 0, 0, 85, '2026-05-16', '2027-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-05-08 06:40:00', '2026-05-10 06:40:00'),
(73, NULL, 73, 'Darwin', 'Evangelista', '2008-09-07', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09275526665', 'darwin.evangelista@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Evangelista', '09276318565', 'lourdes.evangelista.parent@gmail.com', NULL, 'Father', 0, 0, 86, '2025-12-22', '2026-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-13 07:40:00', '2025-12-15 07:40:00'),
(74, NULL, 74, 'Ian', 'Sarmiento', '2005-01-04', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila', '09395534584', 'ian.sarmiento@gmail.com', NULL, 'Brgy. Malabanias, Angeles City, Pampanga', 'Ricardo Sarmiento', '09396326484', 'ricardo.sarmiento.parent@gmail.com', NULL, 'Mother', 0, 0, 87, '2026-08-03', '2026-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-24 08:40:00', '2026-07-26 08:40:00'),
(75, NULL, 75, 'Patrick', 'Delos Reyes', '2006-12-20', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila', '09455542503', 'patrick.delosreyes@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City, Quezon', 'Elena Delos Reyes', '09456334403', 'elena.delosreyes.parent@gmail.com', NULL, 'Father', 0, 0, 88, '2026-01-19', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-08 01:40:00', '2026-01-10 01:40:00'),
(76, NULL, 76, 'Wendell', 'Catapang', '2004-04-02', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila', '09565550422', 'wendell.catapang@gmail.com', NULL, 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'Manuel Catapang', '09566342322', 'manuel.catapang.parent@gmail.com', NULL, 'Mother', 0, 0, 89, '2026-01-08', '2026-11-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-27 02:40:00', '2025-12-29 02:40:00'),
(77, NULL, 77, 'Faith', 'Sarmiento', '2007-07-31', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'faith.sarmiento@gmail.com', NULL, 'Brgy. Sto. Niño, San Fernando, La Union', 'Gemma Sarmiento', '09212408565', 'gemma.sarmiento.parent@gmail.com', NULL, 'Father', 0, 0, 90, '2026-06-16', '2027-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-06-03 03:40:00', '2026-06-05 03:40:00'),
(78, NULL, 78, 'Lovely', 'Evangelista', '1999-06-18', 'female', 'Filipino', 'None', 'Employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City', '09985566260', 'lovely.evangelista@gmail.com', NULL, 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Arturo Evangelista', '09986358160', 'arturo.evangelista.parent@gmail.com', NULL, 'Mother', 0, 0, 91, '2026-05-08', '2027-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-24 04:40:00', '2026-04-26 04:40:00'),
(79, NULL, 79, 'Harvey', 'Cabrera', '2000-08-24', 'male', 'Filipino', 'None', 'Employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City', '09212408565', 'harvey.cabrera@gmail.com', NULL, 'Brgy. Bayanan, Bacoor, Cavite', 'Rosario Cabrera', '09212408565', 'rosario.cabrera.parent@gmail.com', NULL, 'Father', 0, 0, 92, '2026-05-11', '2027-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-04-26 05:40:00', '2026-04-28 05:40:00'),
(80, NULL, 80, 'Tristan', 'Romualdez', '1997-04-22', 'male', 'Filipino', 'None', 'Employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila', '09212408565', 'tristan.romualdez@gmail.com', NULL, 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'Danilo Romualdez', '09212408565', 'danilo.romualdez.parent@gmail.com', NULL, 'Mother', 0, 0, 93, '2025-11-14', '2027-04-30', 'part_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-10-29 06:40:00', '2025-10-31 06:40:00'),
(81, NULL, 81, 'Carl', 'Espiritu', '2005-11-09', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09212408565', 'carl.espiritu@gmail.com', NULL, 'Brgy. Poblacion, Batangas City, Batangas', 'Teresa Espiritu', '09212408565', 'teresa.espiritu.parent@gmail.com', NULL, 'Father', 0, 0, 97, '2025-12-23', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-16 07:40:00', '2025-12-18 07:40:00'),
(82, NULL, 82, 'Bryle', 'Mangubat', '2006-05-20', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09185597936', 'bryle.mangubat@gmail.com', NULL, 'Brgy. San Jose, Tarlac City, Tarlac', 'Roberto Mangubat', '09186389836', 'roberto.mangubat.parent@gmail.com', NULL, 'Mother', 0, 0, 98, '2026-08-04', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-07-27 08:40:00', '2026-07-29 08:40:00'),
(83, NULL, 83, 'Tristan', 'Alcantara', '2006-05-26', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila', '09275605855', 'tristan.alcantara@gmail.com', NULL, 'Brgy. Centro, Naga City, Camarines Sur', 'Lourdes Alcantara', '09276397755', 'lourdes.alcantara.parent@gmail.com', NULL, 'Father', 0, 0, 99, '2026-08-11', '2026-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-08-02 01:40:00', '2026-08-04 01:40:00'),
(84, NULL, 84, 'Alyssa', 'Estrada', '2006-03-02', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09182826286', 'alyssa.estrada@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Estrada', '09183618186', 'ricardo.estrada.parent@gmail.com', NULL, 'Father', 0, 0, 1, '2025-09-23', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-08 02:30:00', '2025-09-10 02:30:00'),
(85, NULL, 85, 'Jericho', 'Tolentino', '2006-02-17', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09272834205', 'jericho.tolentino@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Tolentino', '09273626105', 'elena.tolentino.parent@gmail.com', NULL, 'Mother', 0, 0, 2, '2025-10-15', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-29 03:30:00', '2025-10-01 03:30:00'),
(86, NULL, 86, 'Lance', 'Estrada', '2001-02-04', 'male', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09392842124', 'lance.estrada@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Estrada', '09393634024', 'manuel.estrada.parent@gmail.com', NULL, 'Father', 0, 0, 3, '2025-09-04', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-25 04:30:00', '2025-08-27 04:30:00'),
(87, NULL, 87, 'Oliver', 'Ilagan', '2006-01-22', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09452850043', 'oliver.ilagan@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Ilagan', '09453641943', 'teresa.ilagan.parent@gmail.com', NULL, 'Mother', 0, 0, 3, '2026-02-02', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-22 05:30:00', '2026-01-24 05:30:00'),
(88, NULL, 88, 'Mika', 'Galang', '2006-01-09', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09562857962', 'mika.galang@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Galang', '09563649862', 'roberto.galang.parent@gmail.com', NULL, 'Father', 0, 0, 4, '2025-09-05', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-24 06:30:00', '2025-08-26 06:30:00'),
(89, NULL, 89, 'Vanessa', 'Sison', '2005-12-27', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09662865881', 'vanessa.sison@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Sison', '09663657781', 'lourdes.sison.parent@gmail.com', NULL, 'Mother', 0, 0, 5, '2025-09-12', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-30 07:30:00', '2025-09-01 07:30:00'),
(90, NULL, 90, 'Kevin', 'Sison', '2000-12-14', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09982873800', 'kevin.sison@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Sison', '09983665700', 'ricardo.sison.parent@gmail.com', NULL, 'Father', 0, 0, 6, '2025-09-12', '2026-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-29 08:30:00', '2025-08-31 08:30:00');
INSERT INTO `applications` (`id`, `inquiry_id`, `tenant_id`, `first_name`, `last_name`, `birthdate`, `gender`, `nationality`, `medical_condition`, `occupation`, `school_company`, `school_company_address`, `contact_number`, `email`, `landline`, `home_address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_email`, `emergency_contact_landline`, `emergency_contact_relation`, `emergency_contact_signed`, `emergency_billing_consent`, `bed_id`, `preferred_start_date`, `tenant_end_date`, `type_of_tenant`, `id_document_path`, `signed_contract_path`, `dpa_consent`, `status`, `overdue_notified_at`, `rejection_reason`, `re_application_note`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(91, NULL, 91, 'Aldrin', 'Abad', '2006-09-27', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09082881719', 'aldrin.abad@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Abad', '09083673619', 'elena.abad.parent@gmail.com', NULL, 'Mother', 0, 0, 7, '2025-09-11', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-27 01:30:00', '2025-08-29 01:30:00'),
(92, NULL, 92, 'Franco', 'Ortega', '2006-09-14', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09952889638', 'franco.ortega@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Ortega', '09953681538', 'manuel.ortega.parent@gmail.com', NULL, 'Father', 0, 0, 8, '2025-10-16', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-30 02:30:00', '2025-10-02 02:30:00'),
(93, NULL, 93, 'Bianca', 'Valdez', '2006-09-01', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09172897557', 'bianca.valdez@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Valdez', '09173689457', 'teresa.valdez.parent@gmail.com', NULL, 'Mother', 0, 0, 9, '2025-10-10', '2026-06-30', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-30 03:30:00', '2025-10-02 03:30:00'),
(94, NULL, 94, 'Kevin', 'Ilagan', '2001-08-19', 'male', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09182905476', 'kevin.ilagan@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Ilagan', '09183697376', 'roberto.ilagan.parent@gmail.com', NULL, 'Father', 0, 0, 10, '2025-09-30', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-19 04:30:00', '2025-09-21 04:30:00'),
(95, NULL, 95, 'Gian', 'Javier', '2006-08-06', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09272913395', 'gian.javier@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Javier', '09273705295', 'lourdes.javier.parent@gmail.com', NULL, 'Mother', 0, 0, 11, '2025-09-23', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-11 05:30:00', '2025-09-13 05:30:00'),
(96, NULL, 96, 'Lianne', 'Hernandez', '2006-07-24', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09392921314', 'lianne.hernandez@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Ricardo Hernandez', '09393713214', 'ricardo.hernandez.parent@gmail.com', NULL, 'Father', 0, 0, 12, '2025-09-21', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-08 06:30:00', '2025-09-10 06:30:00'),
(97, NULL, 97, 'Renz', 'Figueroa', '2006-07-11', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09452929233', 'renz.figueroa@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Elena Figueroa', '09453721133', 'elena.figueroa.parent@gmail.com', NULL, 'Mother', 0, 0, 14, '2025-10-10', '2026-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-26 07:30:00', '2025-09-28 07:30:00'),
(98, NULL, 98, 'Nina', 'Macaraeg', '2001-06-28', 'female', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09562937152', 'nina.macaraeg@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Manuel Macaraeg', '09563729052', 'manuel.macaraeg.parent@gmail.com', NULL, 'Father', 0, 0, 15, '2025-09-17', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-02 08:30:00', '2025-09-04 08:30:00'),
(99, NULL, 99, 'Franco', 'Panganiban', '2006-06-15', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09662945071', 'franco.panganiban@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Teresa Panganiban', '09663736971', 'teresa.panganiban.parent@gmail.com', NULL, 'Mother', 0, 0, 16, '2025-09-21', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-05 01:30:00', '2025-09-07 01:30:00'),
(100, NULL, 100, 'Bryan', 'Guevarra', '2006-06-02', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09982952990', 'bryan.guevarra@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Roberto Guevarra', '09983744890', 'roberto.guevarra.parent@gmail.com', NULL, 'Father', 0, 0, 16, '2026-02-12', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-02 02:30:00', '2026-02-04 02:30:00'),
(101, NULL, 101, 'Gwen', 'Figueroa', '2006-05-20', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09082960909', 'gwen.figueroa@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Lourdes Figueroa', '09083752809', 'lourdes.figueroa.parent@gmail.com', NULL, 'Mother', 0, 0, 17, '2025-09-13', '2026-04-30', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-02 03:30:00', '2025-09-04 03:30:00'),
(102, NULL, 102, 'Lianne', 'Ilagan', '2001-05-07', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09952968828', 'lianne.ilagan@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Ricardo Ilagan', '09953760728', 'ricardo.ilagan.parent@gmail.com', NULL, 'Father', 0, 0, 18, '2025-09-12', '2026-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-31 04:30:00', '2025-09-02 04:30:00'),
(103, NULL, 103, 'Irish', 'Castillo', '2006-04-24', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09172976747', 'irish.castillo@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Elena Castillo', '09173768647', 'elena.castillo.parent@gmail.com', NULL, 'Mother', 0, 0, 19, '2025-09-09', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-27 05:30:00', '2025-08-29 05:30:00'),
(104, NULL, 104, 'Pia', 'Valdez', '2006-04-11', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09182984666', 'pia.valdez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Manuel Valdez', '09183776566', 'manuel.valdez.parent@gmail.com', NULL, 'Father', 0, 0, 20, '2025-09-04', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-21 06:30:00', '2025-08-23 06:30:00'),
(105, NULL, 105, 'Ivan', 'Zamora', '2006-03-29', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09272992585', 'ivan.zamora@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Teresa Zamora', '09273784485', 'teresa.zamora.parent@gmail.com', NULL, 'Mother', 0, 0, 20, '2026-03-09', '2026-06-30', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-22 07:30:00', '2026-02-24 07:30:00'),
(106, NULL, 106, 'Cedric', 'Yap', '2001-03-16', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09393000504', 'cedric.yap@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Roberto Yap', '09393792404', 'roberto.yap.parent@gmail.com', NULL, 'Father', 0, 0, 21, '2025-09-16', '2026-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-31 08:30:00', '2025-09-02 08:30:00'),
(107, NULL, 107, 'Ysabel', 'Ortega', '2006-03-03', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09453008423', 'ysabel.ortega@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Lourdes Ortega', '09453800323', 'lourdes.ortega.parent@gmail.com', NULL, 'Mother', 0, 0, 22, '2025-09-20', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-10 01:30:00', '2025-09-12 01:30:00'),
(108, NULL, 108, 'Queenie', 'Belmonte', '2006-02-18', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09563016342', 'queenie.belmonte@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Ricardo Belmonte', '09563808242', 'ricardo.belmonte.parent@gmail.com', NULL, 'Father', 0, 0, 23, '2025-10-02', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-21 02:30:00', '2025-09-23 02:30:00'),
(109, NULL, 109, 'Gian', 'Dizon', '2006-02-05', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09663024261', 'gian.dizon@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Elena Dizon', '09663816161', 'elena.dizon.parent@gmail.com', NULL, 'Mother', 0, 0, 24, '2025-09-06', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-25 03:30:00', '2025-08-27 03:30:00'),
(110, NULL, 110, 'Pia', 'Ortega', '2001-01-23', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09983032180', 'pia.ortega@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Manuel Ortega', '09983824080', 'manuel.ortega.parent@gmail.com', NULL, 'Father', 0, 0, 24, '2026-01-17', '2026-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-04 04:30:00', '2026-01-06 04:30:00'),
(111, NULL, 111, 'Gian', 'Abad', '2006-01-10', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09083040099', 'gian.abad@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Teresa Abad', '09083831999', 'teresa.abad.parent@gmail.com', NULL, 'Mother', 0, 0, 25, '2025-10-15', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-10-01 05:30:00', '2025-10-03 05:30:00'),
(112, NULL, 112, 'Sofia', 'Panganiban', '2005-12-28', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09953048018', 'sofia.panganiban@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Roberto Panganiban', '09953839918', 'roberto.panganiban.parent@gmail.com', NULL, 'Father', 0, 0, 26, '2025-09-06', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-22 06:30:00', '2025-08-24 06:30:00'),
(113, NULL, 113, 'Hazel', 'Valdez', '2005-12-15', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09173055937', 'hazel.valdez@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Lourdes Valdez', '09173847837', 'lourdes.valdez.parent@gmail.com', NULL, 'Mother', 0, 0, 26, '2026-04-03', '2026-08-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-18 07:30:00', '2026-03-20 07:30:00'),
(114, NULL, 114, 'Rica', 'Yap', '2001-09-28', 'female', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09183063856', 'rica.yap@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Yap', '09183855756', 'ricardo.yap.parent@gmail.com', NULL, 'Father', 0, 0, 27, '2025-09-14', '2026-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-04 08:30:00', '2025-09-06 08:30:00'),
(115, NULL, 115, 'Elijah', 'Rosales', '2006-09-15', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09273071775', 'elijah.rosales@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Rosales', '09273863675', 'elena.rosales.parent@gmail.com', NULL, 'Mother', 0, 0, 29, '2025-09-11', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-31 01:30:00', '2025-09-02 01:30:00'),
(116, NULL, 116, 'Ella', 'Sison', '2006-09-02', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09393079694', 'ella.sison@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Sison', '09393871594', 'manuel.sison.parent@gmail.com', NULL, 'Father', 0, 0, 31, '2025-09-25', '2025-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-13 02:30:00', '2025-09-15 02:30:00'),
(117, NULL, 117, 'Francine', 'Nepomuceno', '2006-08-20', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09453087613', 'francine.nepomuceno@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Nepomuceno', '09453879513', 'teresa.nepomuceno.parent@gmail.com', NULL, 'Mother', 0, 0, 32, '2025-09-10', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-28 03:30:00', '2025-08-30 03:30:00'),
(118, NULL, 118, 'Pia', 'Figueroa', '2001-08-07', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09563095532', 'pia.figueroa@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Figueroa', '09563887432', 'roberto.figueroa.parent@gmail.com', NULL, 'Father', 0, 0, 32, '2026-03-15', '2026-08-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-01 04:30:00', '2026-03-03 04:30:00'),
(119, NULL, 119, 'Harold', 'Cordero', '2006-07-25', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09663103451', 'harold.cordero@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Cordero', '09663895351', 'lourdes.cordero.parent@gmail.com', NULL, 'Mother', 0, 0, 33, '2025-09-12', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-28 05:30:00', '2025-08-30 05:30:00'),
(120, NULL, 120, 'Irish', 'Dizon', '2006-07-12', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09983111370', 'irish.dizon@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Dizon', '09983903270', 'ricardo.dizon.parent@gmail.com', NULL, 'Father', 0, 0, 34, '2025-09-30', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-14 06:30:00', '2025-09-16 06:30:00'),
(121, NULL, 121, 'Franco', 'Galang', '2006-06-29', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09083119289', 'franco.galang@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Galang', '09083911189', 'elena.galang.parent@gmail.com', NULL, 'Mother', 0, 0, 35, '2025-10-16', '2026-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-10-06 07:30:00', '2025-10-08 07:30:00'),
(122, NULL, 122, 'Dominic', 'Estrada', '2001-06-16', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09953127208', 'dominic.estrada@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Estrada', '09953919108', 'manuel.estrada.parent@gmail.com', NULL, 'Father', 0, 0, 35, '2026-02-18', '2026-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-07 08:30:00', '2026-02-09 08:30:00'),
(123, NULL, 123, 'Gian', 'Nepomuceno', '2006-06-03', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09173135127', 'gian.nepomuceno@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Nepomuceno', '09173927027', 'teresa.nepomuceno.parent@gmail.com', NULL, 'Mother', 0, 0, 36, '2025-09-14', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-02 01:30:00', '2025-09-04 01:30:00'),
(124, NULL, 124, 'Jericho', 'Enriquez', '2006-05-21', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09183143046', 'jericho.enriquez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Enriquez', '09183934946', 'roberto.enriquez.parent@gmail.com', NULL, 'Father', 0, 0, 37, '2025-09-25', '2025-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-12 02:30:00', '2025-09-14 02:30:00'),
(125, NULL, 125, 'Renz', 'Zamora', '2006-05-08', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09273150965', 'renz.zamora@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Zamora', '09273942865', 'lourdes.zamora.parent@gmail.com', NULL, 'Mother', 0, 0, 38, '2025-09-23', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-09 03:30:00', '2025-09-11 03:30:00'),
(126, NULL, 126, 'Vanessa', 'Cordero', '2001-04-25', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09393158884', 'vanessa.cordero@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Ricardo Cordero', '09393950784', 'ricardo.cordero.parent@gmail.com', NULL, 'Father', 0, 0, 38, '2026-01-14', '2026-06-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-30 04:30:00', '2026-01-01 04:30:00'),
(127, NULL, 127, 'Lance', 'Agustin', '2006-04-12', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09453166803', 'lance.agustin@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Elena Agustin', '09453958703', 'elena.agustin.parent@gmail.com', NULL, 'Mother', 0, 0, 39, '2025-10-09', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-23 05:30:00', '2025-09-25 05:30:00'),
(128, NULL, 128, 'Cedric', 'Quiambao', '2006-03-30', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09563174722', 'cedric.quiambao@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Manuel Quiambao', '09563966622', 'manuel.quiambao.parent@gmail.com', NULL, 'Father', 0, 0, 40, '2025-09-21', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-11 06:30:00', '2025-09-13 06:30:00'),
(129, NULL, 129, 'Sofia', 'Umali', '2006-03-17', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09663182641', 'sofia.umali@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Teresa Umali', '09663974541', 'teresa.umali.parent@gmail.com', NULL, 'Mother', 0, 0, 41, '2025-10-13', '2026-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-10-02 07:30:00', '2025-10-04 07:30:00'),
(130, NULL, 130, 'Cedric', 'Rosales', '2001-03-04', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09983190560', 'cedric.rosales@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Roberto Rosales', '09983982460', 'roberto.rosales.parent@gmail.com', NULL, 'Father', 0, 0, 42, '2025-09-04', '2026-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-23 08:30:00', '2025-08-25 08:30:00'),
(131, NULL, 131, 'Clarisse', 'Figueroa', '2006-02-19', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09083198479', 'clarisse.figueroa@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Lourdes Figueroa', '09083990379', 'lourdes.figueroa.parent@gmail.com', NULL, 'Mother', 0, 0, 43, '2025-09-10', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-28 01:30:00', '2025-08-30 01:30:00'),
(132, NULL, 132, 'Marco', 'Figueroa', '2006-02-06', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09953206398', 'marco.figueroa@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Ricardo Figueroa', '09953998298', 'ricardo.figueroa.parent@gmail.com', NULL, 'Father', 0, 0, 44, '2025-09-30', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-16 02:30:00', '2025-09-18 02:30:00'),
(133, NULL, 133, 'Gian', 'Belmonte', '2006-01-24', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09173214317', 'gian.belmonte@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Elena Belmonte', '09174006217', 'elena.belmonte.parent@gmail.com', NULL, 'Mother', 0, 0, 45, '2025-09-27', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-12 03:30:00', '2025-09-14 03:30:00'),
(134, NULL, 134, 'Lianne', 'Castillo', '2001-01-11', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09183222236', 'lianne.castillo@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Manuel Castillo', '09184014136', 'manuel.castillo.parent@gmail.com', NULL, 'Father', 0, 0, 47, '2025-10-07', '2026-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-21 04:30:00', '2025-09-23 04:30:00'),
(135, NULL, 135, 'Kyla', 'Zamora', '2005-12-29', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09273230155', 'kyla.zamora@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Teresa Zamora', '09274022055', 'teresa.zamora.parent@gmail.com', NULL, 'Mother', 0, 0, 48, '2025-09-10', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-31 05:30:00', '2025-09-02 05:30:00'),
(136, NULL, 136, 'Kyla', 'Galang', '2005-12-16', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09393238074', 'kyla.galang@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Roberto Galang', '09394029974', 'roberto.galang.parent@gmail.com', NULL, 'Father', 0, 0, 49, '2025-09-25', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-14 06:30:00', '2025-09-16 06:30:00'),
(137, NULL, 137, 'Queenie', 'Buenaventura', '2006-09-29', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09453245993', 'queenie.buenaventura@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Lourdes Buenaventura', '09454037893', 'lourdes.buenaventura.parent@gmail.com', NULL, 'Mother', 0, 0, 49, '2026-03-14', '2026-07-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-03-02 07:30:00', '2026-03-04 07:30:00'),
(138, NULL, 138, 'Hazel', 'Nepomuceno', '2001-09-16', 'female', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09563253912', 'hazel.nepomuceno@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Ricardo Nepomuceno', '09564045812', 'ricardo.nepomuceno.parent@gmail.com', NULL, 'Father', 0, 0, 50, '2025-10-13', '2026-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-30 08:30:00', '2025-10-02 08:30:00'),
(139, NULL, 139, 'Troy', 'Enriquez', '2006-09-03', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09663261831', 'troy.enriquez@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Elena Enriquez', '09664053731', 'elena.enriquez.parent@gmail.com', NULL, 'Mother', 0, 0, 51, '2025-09-21', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-07 01:30:00', '2025-09-09 01:30:00'),
(140, NULL, 140, 'Sofia', 'Macaraeg', '2006-08-21', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09983269750', 'sofia.macaraeg@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Manuel Macaraeg', '09984061650', 'manuel.macaraeg.parent@gmail.com', NULL, 'Father', 0, 0, 53, '2025-09-24', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-09 02:30:00', '2025-09-11 02:30:00'),
(141, NULL, 141, 'Bianca', 'Nepomuceno', '2006-08-08', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09083277669', 'bianca.nepomuceno@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Teresa Nepomuceno', '09084069569', 'teresa.nepomuceno.parent@gmail.com', NULL, 'Mother', 0, 0, 55, '2025-09-30', '2026-04-30', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-14 03:30:00', '2025-09-16 03:30:00'),
(142, NULL, 142, 'Bryan', 'Galang', '2001-07-26', 'male', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09953285588', 'bryan.galang@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Roberto Galang', '09954077488', 'roberto.galang.parent@gmail.com', NULL, 'Father', 0, 0, 56, '2025-09-10', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-31 04:30:00', '2025-09-02 04:30:00'),
(143, NULL, 143, 'Dominic', 'Abad', '2006-07-13', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09173293507', 'dominic.abad@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Lourdes Abad', '09174085407', 'lourdes.abad.parent@gmail.com', NULL, 'Mother', 0, 0, 56, '2026-02-09', '2026-08-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-29 05:30:00', '2026-01-31 05:30:00'),
(144, NULL, 144, 'Kevin', 'Panganiban', '2006-06-30', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09183301426', 'kevin.panganiban@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Panganiban', '09184093326', 'ricardo.panganiban.parent@gmail.com', NULL, 'Father', 0, 0, 57, '2025-09-01', '2025-11-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-20 06:30:00', '2025-08-22 06:30:00'),
(145, NULL, 145, 'Aldrin', 'Yap', '2006-06-17', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09273309345', 'aldrin.yap@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Yap', '09274101245', 'elena.yap.parent@gmail.com', NULL, 'Mother', 0, 0, 57, '2025-12-24', '2026-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-11 07:30:00', '2025-12-13 07:30:00'),
(146, NULL, 146, 'Franco', 'Castillo', '2001-06-04', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09393317264', 'franco.castillo@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Castillo', '09394109164', 'manuel.castillo.parent@gmail.com', NULL, 'Father', 0, 0, 58, '2025-10-06', '2026-05-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-22 08:30:00', '2025-09-24 08:30:00'),
(147, NULL, 147, 'Kevin', 'Rosales', '2006-05-22', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09453325183', 'kevin.rosales@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Rosales', '09454117083', 'teresa.rosales.parent@gmail.com', NULL, 'Mother', 0, 0, 59, '2025-09-06', '2025-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-22 01:30:00', '2025-08-24 01:30:00'),
(148, NULL, 148, 'Elijah', 'Domingo', '2006-05-09', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09563333102', 'elijah.domingo@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Domingo', '09564125002', 'roberto.domingo.parent@gmail.com', NULL, 'Father', 0, 0, 59, '2026-02-02', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-17 02:30:00', '2026-01-19 02:30:00'),
(149, NULL, 149, 'Harold', 'Estrada', '2006-04-26', 'male', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09663341021', 'harold.estrada@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Estrada', '09664132921', 'lourdes.estrada.parent@gmail.com', NULL, 'Mother', 0, 0, 61, '2025-09-12', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-02 03:30:00', '2025-09-04 03:30:00'),
(150, NULL, 150, 'Ella', 'Figueroa', '2001-04-13', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09983348940', 'ella.figueroa@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Figueroa', '09984140840', 'ricardo.figueroa.parent@gmail.com', NULL, 'Father', 0, 0, 61, '2026-02-02', '2026-06-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-22 04:30:00', '2026-01-24 04:30:00'),
(151, NULL, 151, 'Kevin', 'Guevarra', '2006-03-31', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09083356859', 'kevin.guevarra@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Guevarra', '09084148759', 'elena.guevarra.parent@gmail.com', NULL, 'Mother', 0, 0, 62, '2025-09-27', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-15 05:30:00', '2025-09-17 05:30:00'),
(152, NULL, 152, 'Queenie', 'Quiambao', '2006-03-18', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09953364778', 'queenie.quiambao@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Quiambao', '09954156678', 'manuel.quiambao.parent@gmail.com', NULL, 'Father', 0, 0, 63, '2025-09-27', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-14 06:30:00', '2025-09-16 06:30:00'),
(153, NULL, 153, 'Rica', 'Quiambao', '2006-03-05', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09173372697', 'rica.quiambao@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Quiambao', '09174164597', 'teresa.quiambao.parent@gmail.com', NULL, 'Mother', 0, 0, 64, '2025-09-19', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-05 07:30:00', '2025-09-07 07:30:00'),
(154, NULL, 154, 'Nina', 'Agustin', '2001-02-20', 'female', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09183380616', 'nina.agustin@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Agustin', '09184172516', 'roberto.agustin.parent@gmail.com', NULL, 'Father', 0, 0, 66, '2025-09-16', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-01 08:30:00', '2025-09-03 08:30:00'),
(155, NULL, 155, 'Lance', 'Panganiban', '2006-02-07', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09273388535', 'lance.panganiban@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Panganiban', '09274180435', 'lourdes.panganiban.parent@gmail.com', NULL, 'Mother', 0, 0, 67, '2025-09-14', '2025-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-29 01:30:00', '2025-08-31 01:30:00'),
(156, NULL, 156, 'Hazel', 'Guevarra', '2006-01-25', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09393396454', 'hazel.guevarra@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Ricardo Guevarra', '09394188354', 'ricardo.guevarra.parent@gmail.com', NULL, 'Father', 0, 0, 67, '2026-01-07', '2026-06-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-28 02:30:00', '2025-12-30 02:30:00'),
(157, NULL, 157, 'Ella', 'Rosales', '2006-01-12', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09453404373', 'ella.rosales@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Elena Rosales', '09454196273', 'elena.rosales.parent@gmail.com', NULL, 'Mother', 0, 0, 68, '2025-09-15', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-04 03:30:00', '2025-09-06 03:30:00'),
(158, NULL, 158, 'Harold', 'Umali', '2000-12-30', 'male', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09563412292', 'harold.umali@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Manuel Umali', '09564204192', 'manuel.umali.parent@gmail.com', NULL, 'Father', 0, 0, 69, '2025-09-30', '2025-12-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-18 04:30:00', '2025-09-20 04:30:00'),
(159, NULL, 159, 'Pia', 'Abad', '2005-12-17', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09663420211', 'pia.abad@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Teresa Abad', '09664212111', 'teresa.abad.parent@gmail.com', NULL, 'Mother', 0, 0, 69, '2026-01-15', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-02 05:30:00', '2026-01-04 05:30:00'),
(160, NULL, 160, 'Oliver', 'Figueroa', '2006-09-30', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09983428130', 'oliver.figueroa@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Roberto Figueroa', '09984220030', 'roberto.figueroa.parent@gmail.com', NULL, 'Father', 0, 0, 70, '2025-09-12', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-29 06:30:00', '2025-08-31 06:30:00'),
(161, NULL, 161, 'Ysabel', 'Galang', '2006-09-17', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09083436049', 'ysabel.galang@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Lourdes Galang', '09084227949', 'lourdes.galang.parent@gmail.com', NULL, 'Mother', 0, 0, 72, '2025-09-21', '2026-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-06 07:30:00', '2025-09-08 07:30:00'),
(162, NULL, 162, 'Oliver', 'Yap', '2001-09-04', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09953443968', 'oliver.yap@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Ricardo Yap', '09954235868', 'ricardo.yap.parent@gmail.com', NULL, 'Father', 0, 0, 73, '2025-09-04', '2026-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-19 08:30:00', '2025-08-21 08:30:00'),
(163, NULL, 163, 'Kevin', 'Javier', '2006-08-22', 'male', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09173451887', 'kevin.javier@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Elena Javier', '09174243787', 'elena.javier.parent@gmail.com', NULL, 'Mother', 0, 0, 73, '2026-02-25', '2026-06-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-02-15 01:30:00', '2026-02-17 01:30:00'),
(164, NULL, 164, 'Queenie', 'Rosales', '2006-08-09', 'female', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09183459806', 'queenie.rosales@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Manuel Rosales', '09184251706', 'manuel.rosales.parent@gmail.com', NULL, 'Father', 0, 0, 74, '2025-10-08', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-27 02:30:00', '2025-09-29 02:30:00'),
(165, NULL, 165, 'Irish', 'Hernandez', '2006-07-27', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09273467725', 'irish.hernandez@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Teresa Hernandez', '09274259625', 'teresa.hernandez.parent@gmail.com', NULL, 'Mother', 0, 0, 75, '2025-09-02', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-21 03:30:00', '2025-08-23 03:30:00'),
(166, NULL, 166, 'Sofia', 'Javier', '2001-07-14', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09393475644', 'sofia.javier@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Roberto Javier', '09394267544', 'roberto.javier.parent@gmail.com', NULL, 'Father', 0, 0, 79, '2025-09-19', '2026-02-28', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-06 04:30:00', '2025-09-08 04:30:00'),
(167, NULL, 167, 'Vanessa', 'Ortega', '2006-07-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09453483563', 'vanessa.ortega@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Lourdes Ortega', '09454275463', 'lourdes.ortega.parent@gmail.com', NULL, 'Mother', 0, 0, 80, '2025-09-15', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-01 05:30:00', '2025-09-03 05:30:00'),
(168, NULL, 168, 'Sofia', 'Fernandez', '2006-06-18', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09563491482', 'sofia.fernandez@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Ricardo Fernandez', '09564283382', 'ricardo.fernandez.parent@gmail.com', NULL, 'Father', 0, 0, 81, '2025-09-03', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-19 06:30:00', '2025-08-21 06:30:00'),
(169, NULL, 169, 'Dominic', 'Javier', '2006-06-05', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09663499401', 'dominic.javier@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Elena Javier', '09664291301', 'elena.javier.parent@gmail.com', NULL, 'Mother', 0, 0, 82, '2025-09-11', '2026-01-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-26 07:30:00', '2025-08-28 07:30:00'),
(170, NULL, 170, 'Cedric', 'Galang', '2001-05-23', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09983507320', 'cedric.galang@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Manuel Galang', '09984299220', 'manuel.galang.parent@gmail.com', NULL, 'Father', 0, 0, 83, '2025-09-06', '2026-04-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-27 08:30:00', '2025-08-29 08:30:00'),
(171, NULL, 171, 'Ella', 'Valdez', '2006-05-10', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09083515239', 'ella.valdez@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Teresa Valdez', '09084307139', 'teresa.valdez.parent@gmail.com', NULL, 'Mother', 0, 0, 84, '2025-10-03', '2026-02-28', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-22 01:30:00', '2025-09-24 01:30:00'),
(172, NULL, 172, 'Franco', 'Guevarra', '2006-04-27', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09953523158', 'franco.guevarra@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Roberto Guevarra', '09954315058', 'roberto.guevarra.parent@gmail.com', NULL, 'Father', 0, 0, 85, '2025-10-06', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-24 02:30:00', '2025-09-26 02:30:00'),
(173, NULL, 173, 'Janelle', 'Hernandez', '2006-04-14', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09173531077', 'janelle.hernandez@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Lourdes Hernandez', '09174322977', 'lourdes.hernandez.parent@gmail.com', NULL, 'Mother', 0, 0, 87, '2025-09-20', '2025-12-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-07 03:30:00', '2025-09-09 03:30:00'),
(174, NULL, 174, 'Janelle', 'Agustin', '2001-04-01', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09183538996', 'janelle.agustin@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Ricardo Agustin', '09184330896', 'ricardo.agustin.parent@gmail.com', NULL, 'Father', 0, 0, 87, '2026-01-06', '2026-06-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-12-23 04:30:00', '2025-12-25 04:30:00'),
(175, NULL, 175, 'Renz', 'Belmonte', '2006-03-19', 'male', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09273546915', 'renz.belmonte@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Elena Belmonte', '09274338815', 'elena.belmonte.parent@gmail.com', NULL, 'Mother', 0, 0, 89, '2025-09-09', '2025-12-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-25 05:30:00', '2025-08-27 05:30:00'),
(176, NULL, 176, 'Renz', 'Enriquez', '2006-03-06', 'male', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09393554834', 'renz.enriquez@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Manuel Enriquez', '09394346734', 'manuel.enriquez.parent@gmail.com', NULL, 'Father', 0, 0, 90, '2025-09-25', '2026-04-30', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-09 06:30:00', '2025-09-11 06:30:00'),
(177, NULL, 177, 'Troy', 'Ilagan', '2006-02-21', 'male', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09453562753', 'troy.ilagan@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Teresa Ilagan', '09454354653', 'teresa.ilagan.parent@gmail.com', NULL, 'Mother', 0, 0, 91, '2025-09-26', '2026-03-31', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-16 07:30:00', '2025-09-18 07:30:00'),
(178, NULL, 178, 'Ivan', 'Fernandez', '2001-02-08', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Manila', 'Manila', '09563570672', 'ivan.fernandez@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Roberto Fernandez', '09564362572', 'roberto.fernandez.parent@gmail.com', NULL, 'Father', 0, 0, 92, '2025-09-29', '2026-03-31', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-18 08:30:00', '2025-09-20 08:30:00'),
(179, NULL, 179, 'Danica', 'Quiambao', '2006-01-26', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09663578591', 'danica.quiambao@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Lourdes Quiambao', '09664370491', 'lourdes.quiambao.parent@gmail.com', NULL, 'Mother', 0, 0, 94, '2025-09-07', '2026-03-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-26 01:30:00', '2025-08-28 01:30:00'),
(180, NULL, 180, 'Jericho', 'Guevarra', '2006-01-13', 'male', 'Filipino', 'None', 'Student', 'University of the East', 'Manila', '09983586510', 'jericho.guevarra@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Ricardo Guevarra', '09984378410', 'ricardo.guevarra.parent@gmail.com', NULL, 'Father', 0, 0, 95, '2025-09-26', '2026-05-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-13 02:30:00', '2025-09-15 02:30:00'),
(181, NULL, 181, 'Ysabel', 'Panganiban', '2005-12-31', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09083594429', 'ysabel.panganiban@gmail.com', NULL, 'Brgy. Malabanias, Angeles City', 'Elena Panganiban', '09084386329', 'elena.panganiban.parent@gmail.com', NULL, 'Mother', 0, 0, 96, '2025-09-07', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-24 03:30:00', '2025-08-26 03:30:00'),
(182, NULL, 182, 'Pia', 'Fernandez', '2000-12-18', 'female', 'Filipino', 'None', 'Employee', 'University of Santo Tomas', 'Manila', '09953602348', 'pia.fernandez@gmail.com', NULL, 'Brgy. Bagumbayan, Lucena City', 'Manuel Fernandez', '09954394248', 'manuel.fernandez.parent@gmail.com', NULL, 'Father', 0, 0, 98, '2025-09-11', '2026-04-30', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-08-27 04:30:00', '2025-08-29 04:30:00'),
(183, NULL, 183, 'Janelle', 'Panganiban', '2006-10-01', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09173610267', 'janelle.panganiban@gmail.com', NULL, 'Brgy. Poblacion, Batangas City', 'Teresa Panganiban', '09174402167', 'teresa.panganiban.parent@gmail.com', NULL, 'Mother', 0, 0, 99, '2025-10-16', '2026-01-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-30 05:30:00', '2025-10-02 05:30:00'),
(184, NULL, 184, 'Gwen', 'Rosales', '2006-09-18', 'female', 'Filipino', 'None', 'Student', 'National University', 'Manila', '09183618186', 'gwen.rosales@gmail.com', NULL, 'Brgy. San Jose, Tarlac City', 'Roberto Rosales', '09184410086', 'roberto.rosales.parent@gmail.com', NULL, 'Father', 0, 0, 99, '2026-02-10', '2026-07-31', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2026-01-31 06:30:00', '2026-02-02 06:30:00'),
(185, NULL, 185, 'Janelle', 'Macaraeg', '2006-09-05', 'female', 'Filipino', 'None', 'Student', 'Accenture Philippines', 'Manila', '09273626105', 'janelle.macaraeg@gmail.com', NULL, 'Brgy. Centro, Naga City', 'Lourdes Macaraeg', '09274418005', 'lourdes.macaraeg.parent@gmail.com', NULL, 'Mother', 0, 0, 100, '2025-10-02', '2026-02-28', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 1, 'approved', NULL, NULL, NULL, NULL, 355, '2025-09-21 07:30:00', '2025-09-23 07:30:00'),
(186, NULL, NULL, 'Ana Beatriz', 'Salonga', '2006-03-08', 'female', 'Filipino', 'None', 'Student', 'Polytechnic University of the Philippines', 'Manila', '09212408565', 'ana.salonga@gmail.com', NULL, 'Brgy. Poblacion, Bontoc, Mountain Province', 'Ramon Salonga', '09173610267', NULL, NULL, 'Father', 1, 1, 22, '2026-10-04', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-ana-beatriz-salonga.pdf', 1, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-29 05:05:00', '2026-09-29 05:05:00'),
(187, NULL, NULL, 'Ryan Christopher', 'Santiago', '2005-09-18', 'male', 'Filipino', 'None', 'Student', 'University of Santo Tomas', 'Manila', '09182826286', 'ryan.santiago@gmail.com', NULL, 'Brgy. Bagumbayan, Taguig City', 'Rommel Santiago', '09183618186', NULL, NULL, 'Mother', 1, 1, 27, '2026-10-07', '2027-02-28', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-ryan-christopher-santiago.pdf', 1, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-30 06:05:00', '2026-09-30 06:05:00'),
(188, NULL, NULL, 'Alyssa Mae', 'Mercado', '2006-04-03', 'female', 'Filipino', 'None', 'Student', 'Far Eastern University', 'Manila', '09272834205', 'alyssa.mercado@gmail.com', NULL, 'Brgy. Malinta, Valenzuela City', 'Liza Mercado', '09273626105', NULL, NULL, 'Father', 1, 0, 37, '2026-10-10', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-alyssa-mae-mercado.pdf', 1, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-10-01 07:05:00', '2026-10-01 07:05:00');
INSERT INTO `applications` (`id`, `inquiry_id`, `tenant_id`, `first_name`, `last_name`, `birthdate`, `gender`, `nationality`, `medical_condition`, `occupation`, `school_company`, `school_company_address`, `contact_number`, `email`, `landline`, `home_address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_email`, `emergency_contact_landline`, `emergency_contact_relation`, `emergency_contact_signed`, `emergency_billing_consent`, `bed_id`, `preferred_start_date`, `tenant_end_date`, `type_of_tenant`, `id_document_path`, `signed_contract_path`, `dpa_consent`, `status`, `overdue_notified_at`, `rejection_reason`, `re_application_note`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(189, NULL, NULL, 'Kevin James', 'Dizon', '2000-10-10', 'male', 'Filipino', 'None', 'Employee', 'Concentrix Philippines', 'Manila', '09392842124', 'kevin.dizon@gmail.com', NULL, 'Brgy. San Isidro, Cainta, Rizal', 'Jun Dizon', '09393634024', NULL, NULL, 'Mother', 1, 1, 42, '2026-10-13', '2027-01-31', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kevin-james-dizon.pdf', 1, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-29 08:05:00', '2026-09-29 08:05:00'),
(190, NULL, NULL, 'Sophia Isabel', 'Lopez', '2004-05-25', 'female', 'Filipino', 'None', 'Student', 'San Beda University', 'Manila', '09452850043', 'sophia.lopez@gmail.com', NULL, 'Brgy. Poblacion, Muntinlupa City', 'Maricel Lopez', '09453641943', NULL, NULL, 'Father', 1, 0, 46, '2026-10-16', '2026-11-30', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-sophia-isabel-lopez.pdf', 1, 'rejected', NULL, 'Requested stay is only 1 month; the dormitory requires a minimum 3-month stay (Payments and Fees Schedule 4.1). A short-term stay needs a separate agreement -- please contact the office.', NULL, NULL, NULL, '2026-09-25 09:05:00', '2026-09-26 09:05:00'),
(191, NULL, NULL, 'Daniel Lorenzo', 'Cruz', '2005-01-15', 'male', 'Filipino', 'None', 'Student', 'Mapúa University', 'Manila', '09562857962', 'daniel.cruz@gmail.com', NULL, 'Brgy. Tambo, Parañaque City', 'Bong Cruz', '09563649862', NULL, NULL, 'Mother', 1, 1, 47, '2026-10-19', '2027-03-31', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-daniel-lorenzo-cruz.pdf', 1, 're_application_requested', NULL, NULL, 'Uploaded ID is blurry and the name cannot be read. Please re-apply with a clear photo of a valid school or government ID.', NULL, NULL, '2026-09-27 10:05:00', '2026-09-28 10:05:00'),
(192, NULL, NULL, 'Mika Ella', 'Santos', '2006-08-12', 'female', 'Filipino', 'None', 'Student', 'Centro Escolar University', 'Manila', '09662865881', 'mika.santos@gmail.com', NULL, 'Brgy. Sto. Niño, Marikina City', 'Tess Santos', '09663657781', NULL, NULL, 'Father', 1, 0, 49, '2026-10-22', '2027-01-31', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-mika-ella-santos.pdf', 1, 'cancelled', NULL, NULL, NULL, NULL, NULL, '2026-09-22 11:05:00', '2026-09-23 11:05:00');

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
(1, 16, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(2, 16, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(3, 16, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(4, 16, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(5, 18, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(6, 18, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(7, 18, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(8, 18, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(9, 17, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(10, 17, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(11, 17, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(12, 17, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(13, 19, 'Bed 1', 'maintenance', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(14, 84, 'Bed 1', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(15, 85, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(16, 85, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(17, 85, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(18, 85, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(19, 85, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(20, 85, 'Bed 6', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(21, 86, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(22, 86, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(23, 86, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(24, 86, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(25, 87, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(26, 87, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(27, 87, 'Bed 3', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(28, 87, 'Bed 4', 'maintenance', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(29, 88, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(30, 88, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(31, 88, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(32, 88, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(33, 88, 'Bed 5', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(34, 88, 'Bed 6', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(35, 89, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(36, 89, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(37, 89, 'Bed 3', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(38, 89, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(39, 89, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(40, 89, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(41, 90, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(42, 90, 'Bed 2', 'reserved', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(43, 90, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(44, 90, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(45, 91, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(46, 91, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(47, 91, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(48, 91, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(49, 91, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(50, 91, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(51, 91, 'Bed 7', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(52, 91, 'Bed 8', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(53, 91, 'Bed 9', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(54, 91, 'Bed 10', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(55, 91, 'Bed 11', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(56, 91, 'Bed 12', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(57, 92, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(58, 92, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(59, 92, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(60, 92, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(61, 93, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(62, 93, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(63, 93, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(64, 93, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(65, 93, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(66, 93, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(67, 94, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(68, 94, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(69, 94, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(70, 94, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(71, 94, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(72, 94, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(73, 94, 'Bed 7', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(74, 94, 'Bed 8', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(75, 94, 'Bed 9', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(76, 94, 'Bed 10', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(77, 94, 'Bed 11', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(78, 94, 'Bed 12', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(79, 94, 'Bed 13', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(80, 94, 'Bed 14', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(81, 94, 'Bed 15', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(82, 94, 'Bed 16', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(83, 95, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(84, 95, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(85, 95, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(86, 95, 'Bed 4', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(87, 95, 'Bed 5', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(88, 95, 'Bed 6', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(89, 95, 'Bed 7', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(90, 95, 'Bed 8', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(91, 95, 'Bed 9', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(92, 95, 'Bed 10', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(93, 95, 'Bed 11', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(94, 95, 'Bed 12', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(95, 95, 'Bed 13', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(96, 95, 'Bed 14', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(97, 96, 'Bed 1', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(98, 96, 'Bed 2', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(99, 96, 'Bed 3', 'occupied', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(100, 96, 'Bed 4', 'vacant', '2025-07-31 16:00:00', '2026-10-01 15:12:48');

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
(1, 1, 1, 'move_in', '2026-04-11', '2026-04-11', '2026-04-11', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-04-02 02:15:00', '2026-10-01 15:12:49'),
(2, 1, 1, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:12:49'),
(3, 1, 1, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:49'),
(4, 1, 1, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:49'),
(5, 1, 1, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:49'),
(6, 1, 1, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:49'),
(7, 1, 1, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:12:49'),
(8, 2, 2, 'move_in', '2026-06-03', '2026-06-03', '2026-06-03', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-05-24 03:15:00', '2026-10-01 15:12:50'),
(9, 2, 2, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:50'),
(10, 2, 2, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:50'),
(11, 2, 2, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:50'),
(12, 2, 2, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:12:50'),
(13, 3, 3, 'move_in', '2026-07-05', '2026-07-05', '2026-07-05', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-06-24 04:15:00', '2026-10-01 15:12:51'),
(14, 3, 3, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:51'),
(15, 3, 3, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:51'),
(16, 3, 3, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:12:51'),
(17, 4, 4, 'move_in', '2026-07-13', '2026-07-13', '2026-07-13', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-07-01 05:15:00', '2026-10-01 15:12:52'),
(18, 4, 4, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:52'),
(19, 4, 4, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:52'),
(20, 4, 4, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:12:52'),
(21, 5, 5, 'move_in', '2026-03-16', '2026-03-16', '2026-03-16', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-03-08 06:15:00', '2026-10-01 15:12:53'),
(22, 5, 5, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:12:53'),
(23, 5, 5, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:12:53'),
(24, 5, 5, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:53'),
(25, 5, 5, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:53'),
(26, 5, 5, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:53'),
(27, 5, 5, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:53'),
(28, 5, 5, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:12:53'),
(29, 6, 6, 'move_in', '2026-05-15', '2026-05-15', '2026-05-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-06 07:15:00', '2026-10-01 15:12:55'),
(30, 6, 6, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:55'),
(31, 6, 6, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:55'),
(32, 6, 6, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:55'),
(33, 6, 6, 'monthly', '2026-09-01', '2026-09-30', '2026-09-19', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'overdue', '2026-08-31 16:05:00', '2026-10-01 15:12:55'),
(34, 7, 7, 'move_in', '2026-08-06', '2026-08-06', '2026-08-06', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-07-27 08:15:00', '2026-10-01 15:12:56'),
(35, 7, 7, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:56'),
(36, 7, 7, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:12:56'),
(37, 8, 8, 'move_in', '2026-05-04', '2026-05-04', '2026-05-04', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-04-23 01:15:00', '2026-10-01 15:12:57'),
(38, 8, 8, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:57'),
(39, 8, 8, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:57'),
(40, 8, 8, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:57'),
(41, 8, 8, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:57'),
(42, 8, 8, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'partial', '2026-09-30 16:05:00', '2026-10-01 15:12:57'),
(43, 9, 9, 'move_in', '2026-05-09', '2026-05-09', '2026-05-09', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-04-27 02:15:00', '2026-10-01 15:12:58'),
(44, 9, 9, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:58'),
(45, 9, 9, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:58'),
(46, 9, 9, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:58'),
(47, 9, 9, 'monthly', '2026-09-01', '2026-09-30', '2026-09-25', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'overdue', '2026-08-31 16:05:00', '2026-10-01 15:12:58'),
(48, 10, 10, 'move_in', '2026-04-13', '2026-04-13', '2026-04-13', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-04-05 03:15:00', '2026-10-01 15:12:59'),
(49, 10, 10, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:12:59'),
(50, 10, 10, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:12:59'),
(51, 10, 10, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:12:59'),
(52, 10, 10, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:12:59'),
(53, 10, 10, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:12:59'),
(54, 10, 10, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:12:59'),
(55, 11, 11, 'move_in', '2026-01-21', '2026-01-21', '2026-01-21', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-01-12 04:15:00', '2026-10-01 15:13:00'),
(56, 11, 11, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:00'),
(57, 11, 11, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:00'),
(58, 11, 11, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:00'),
(59, 11, 11, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:00'),
(60, 11, 11, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:00'),
(61, 11, 11, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:00'),
(62, 11, 11, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:00'),
(63, 11, 11, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:00'),
(64, 11, 11, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:00'),
(65, 12, 12, 'move_in', '2026-07-02', '2026-07-02', '2026-07-02', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-06-22 05:15:00', '2026-10-01 15:13:01'),
(66, 12, 12, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:01'),
(67, 12, 12, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:01'),
(68, 12, 12, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:01'),
(69, 13, 13, 'move_in', '2026-05-12', '2026-05-12', '2026-05-12', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-05-01 06:15:00', '2026-10-01 15:13:02'),
(70, 13, 13, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:02'),
(71, 13, 13, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:02'),
(72, 13, 13, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:02'),
(73, 13, 13, 'monthly', '2026-09-01', '2026-09-30', '2026-09-22', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'overdue', '2026-08-31 16:05:00', '2026-10-01 15:13:02'),
(74, 14, 14, 'move_in', '2026-06-01', '2026-06-01', '2026-06-01', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-05-20 07:15:00', '2026-10-01 15:13:03'),
(75, 14, 14, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:03'),
(76, 14, 14, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:03'),
(77, 14, 14, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:03'),
(78, 14, 14, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 800.00, 4800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:03'),
(79, 15, 15, 'move_in', '2026-06-08', '2026-06-08', '2026-06-08', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-05-31 08:15:00', '2026-10-01 15:13:04'),
(80, 15, 15, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:04'),
(81, 15, 15, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:04'),
(82, 15, 15, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:04'),
(83, 15, 15, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:04'),
(84, 16, 16, 'move_in', '2026-10-05', '2026-10-05', '2026-10-05', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'unpaid', '2026-09-26 01:15:00', '2026-10-01 15:13:05'),
(85, 17, 17, 'move_in', '2026-07-10', '2026-07-10', '2026-07-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-06-30 02:15:00', '2026-10-01 15:13:06'),
(86, 17, 17, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:06'),
(87, 17, 17, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:06'),
(88, 17, 17, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:06'),
(89, 18, 18, 'move_in', '2026-05-19', '2026-05-19', '2026-05-19', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-05-08 03:15:00', '2026-10-01 15:13:07'),
(90, 18, 18, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:07'),
(91, 18, 18, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:07'),
(92, 18, 18, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:07'),
(93, 18, 18, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:07'),
(94, 18, 18, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:07'),
(95, 19, 19, 'move_in', '2026-10-05', '2026-10-05', '2026-10-05', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'unpaid', '2026-09-23 04:15:00', '2026-10-01 15:13:10'),
(96, 20, 20, 'move_in', '2026-04-07', '2026-04-07', '2026-04-07', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-03-30 05:15:00', '2026-10-01 15:13:12'),
(97, 20, 20, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:12'),
(98, 20, 20, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:12'),
(99, 20, 20, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:12'),
(100, 20, 20, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:12'),
(101, 20, 20, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:12'),
(102, 20, 20, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:12'),
(103, 21, 21, 'move_in', '2026-06-14', '2026-06-14', '2026-06-14', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-06-05 06:15:00', '2026-10-01 15:13:14'),
(104, 21, 21, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:14'),
(105, 21, 21, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:14'),
(106, 21, 21, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:14'),
(107, 21, 21, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:14'),
(108, 22, 22, 'move_in', '2026-08-05', '2026-08-05', '2026-08-05', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-07-26 07:15:00', '2026-10-01 15:13:17'),
(109, 22, 22, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:17'),
(110, 22, 22, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:17'),
(111, 23, 23, 'move_in', '2026-02-17', '2026-02-17', '2026-02-17', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-02-06 08:15:00', '2026-10-01 15:13:17'),
(112, 23, 23, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:17'),
(113, 23, 23, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:17'),
(114, 23, 23, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:17'),
(115, 23, 23, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:17'),
(116, 23, 23, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:17'),
(117, 24, 24, 'move_in', '2026-03-24', '2026-03-24', '2026-03-24', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-03-12 01:15:00', '2026-10-01 15:13:17'),
(118, 24, 24, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:17'),
(119, 24, 24, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:17'),
(120, 24, 24, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:17'),
(121, 24, 24, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:17'),
(122, 24, 24, 'monthly', '2026-08-01', '2026-08-31', '2026-08-14', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'overdue', '2026-07-31 16:05:00', '2026-10-01 15:13:17'),
(123, 25, 25, 'move_in', '2026-02-17', '2026-02-17', '2026-02-17', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-02-09 02:15:00', '2026-10-01 15:13:19'),
(124, 25, 25, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:19'),
(125, 25, 25, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:19'),
(126, 25, 25, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:19'),
(127, 25, 25, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:19'),
(128, 25, 25, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:19'),
(129, 25, 25, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:19'),
(130, 25, 25, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:19'),
(131, 25, 25, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:19'),
(132, 26, 26, 'move_in', '2026-06-12', '2026-06-12', '2026-06-12', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-06-03 03:15:00', '2026-10-01 15:13:22'),
(133, 26, 26, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:22'),
(134, 26, 26, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:22'),
(135, 26, 26, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:22'),
(136, 26, 26, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:22'),
(137, 27, 27, 'move_in', '2026-07-10', '2026-07-10', '2026-07-10', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-06-30 04:15:00', '2026-10-01 15:13:24'),
(138, 27, 27, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:24'),
(139, 27, 27, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:24'),
(140, 27, 27, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:24'),
(141, 28, 28, 'move_in', '2026-09-22', '2026-09-22', '2026-09-22', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-09-11 05:15:00', '2026-10-01 15:13:26'),
(142, 28, 28, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:26'),
(143, 29, 29, 'move_in', '2026-09-15', '2026-09-15', '2026-09-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-09-03 06:15:00', '2026-10-01 15:13:27'),
(144, 29, 29, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:27'),
(145, 30, 30, 'move_in', '2026-09-03', '2026-09-03', '2026-09-03', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-08-26 07:15:00', '2026-10-01 15:13:29'),
(146, 30, 30, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:29'),
(147, 31, 31, 'move_in', '2026-05-25', '2026-05-25', '2026-05-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-16 08:15:00', '2026-10-01 15:13:31'),
(148, 31, 31, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:31'),
(149, 31, 31, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:31'),
(150, 31, 31, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:31'),
(151, 31, 31, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:31'),
(152, 31, 31, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:31'),
(153, 32, 32, 'move_in', '2026-07-23', '2026-07-23', '2026-07-23', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-07-17 06:40:00', '2026-10-01 15:13:33'),
(154, 32, 32, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:33'),
(155, 32, 32, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:33'),
(156, 32, 32, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:33'),
(157, 33, 33, 'move_in', '2026-06-27', '2026-06-27', '2026-06-27', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-06-20 07:40:00', '2026-10-01 15:13:35'),
(158, 33, 33, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:35'),
(159, 33, 33, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:35'),
(160, 33, 33, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:35'),
(161, 33, 33, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:35'),
(162, 34, 34, 'move_in', '2026-07-04', '2026-07-04', '2026-07-04', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-06-26 08:40:00', '2026-10-01 15:13:36'),
(163, 34, 34, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:36'),
(164, 34, 34, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:36'),
(165, 34, 34, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:36'),
(166, 35, 35, 'move_in', '2026-07-10', '2026-07-10', '2026-07-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-07-01 01:40:00', '2026-10-01 15:13:38'),
(167, 35, 35, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:38'),
(168, 35, 35, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:38'),
(169, 35, 35, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:38'),
(170, 36, 36, 'move_in', '2026-01-07', '2026-01-07', '2026-01-07', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-12-28 02:40:00', '2026-10-01 15:13:40'),
(171, 36, 36, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:40'),
(172, 36, 36, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:40'),
(173, 36, 36, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:40'),
(174, 36, 36, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:40'),
(175, 36, 36, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:40'),
(176, 36, 36, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:40'),
(177, 36, 36, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:40'),
(178, 36, 36, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:40'),
(179, 36, 36, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:40'),
(180, 37, 37, 'move_in', '2026-02-07', '2026-02-07', '2026-02-07', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-01-27 03:40:00', '2026-10-01 15:13:42'),
(181, 37, 37, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:42'),
(182, 37, 37, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:42'),
(183, 37, 37, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:42'),
(184, 37, 37, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:42'),
(185, 37, 37, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:42'),
(186, 37, 37, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:42'),
(187, 37, 37, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:42'),
(188, 37, 37, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:42'),
(189, 38, 38, 'move_in', '2026-09-11', '2026-09-11', '2026-09-11', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-08-30 04:40:00', '2026-10-01 15:13:43'),
(190, 38, 38, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:43'),
(191, 39, 39, 'move_in', '2026-02-19', '2026-02-19', '2026-02-19', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-02-06 05:40:00', '2026-10-01 15:13:45'),
(192, 39, 39, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:45'),
(193, 39, 39, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:45'),
(194, 39, 39, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:45'),
(195, 39, 39, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:45'),
(196, 39, 39, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:45'),
(197, 39, 39, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:45'),
(198, 39, 39, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:45'),
(199, 39, 39, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:45'),
(200, 40, 40, 'move_in', '2026-09-20', '2026-09-20', '2026-09-20', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-09-06 06:40:00', '2026-10-01 15:13:46'),
(201, 40, 40, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:46'),
(202, 41, 41, 'move_in', '2026-08-02', '2026-08-02', '2026-08-02', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-07-28 07:40:00', '2026-10-01 15:13:47'),
(203, 41, 41, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:47'),
(204, 41, 41, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:47'),
(205, 42, 42, 'move_in', '2026-03-10', '2026-03-10', '2026-03-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-03-04 08:40:00', '2026-10-01 15:13:48'),
(206, 42, 42, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:48'),
(207, 42, 42, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:48'),
(208, 42, 42, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:48'),
(209, 42, 42, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:48'),
(210, 42, 42, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:48'),
(211, 42, 42, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:48'),
(212, 42, 42, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:48'),
(213, 43, 43, 'move_in', '2025-12-15', '2025-12-15', '2025-12-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-08 01:40:00', '2026-10-01 15:13:49'),
(214, 43, 43, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:13:49'),
(215, 43, 43, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:49'),
(216, 43, 43, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:49'),
(217, 43, 43, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:49'),
(218, 43, 43, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:49'),
(219, 43, 43, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:49'),
(220, 43, 43, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:49'),
(221, 43, 43, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:49'),
(222, 43, 43, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:49'),
(223, 43, 43, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:49'),
(224, 44, 44, 'move_in', '2026-07-24', '2026-07-24', '2026-07-24', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-07-16 02:40:00', '2026-10-01 15:13:50'),
(225, 44, 44, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:50'),
(226, 44, 44, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:50'),
(227, 44, 44, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:50'),
(228, 45, 45, 'move_in', '2026-09-04', '2026-09-04', '2026-09-04', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-08-26 03:40:00', '2026-10-01 15:13:52'),
(229, 45, 45, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:52'),
(230, 46, 46, 'move_in', '2025-11-14', '2025-11-14', '2025-11-14', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-11-04 04:40:00', '2026-10-01 15:13:53'),
(231, 46, 46, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:13:53'),
(232, 46, 46, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:13:53'),
(233, 46, 46, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:53'),
(234, 46, 46, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:53'),
(235, 46, 46, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:53'),
(236, 46, 46, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:53'),
(237, 46, 46, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:53'),
(238, 46, 46, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:53'),
(239, 46, 46, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:53'),
(240, 46, 46, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:53'),
(241, 46, 46, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:53'),
(242, 47, 47, 'move_in', '2026-04-09', '2026-04-09', '2026-04-09', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-03-29 05:40:00', '2026-10-01 15:13:54'),
(243, 47, 47, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:54'),
(244, 47, 47, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:54'),
(245, 47, 47, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:54'),
(246, 47, 47, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:54'),
(247, 47, 47, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:54'),
(248, 47, 47, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:54'),
(249, 48, 48, 'move_in', '2026-01-06', '2026-01-06', '2026-01-06', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-25 06:40:00', '2026-10-01 15:13:55'),
(250, 48, 48, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:55'),
(251, 48, 48, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:55'),
(252, 48, 48, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:55'),
(253, 48, 48, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:55'),
(254, 48, 48, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:55'),
(255, 48, 48, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:55'),
(256, 48, 48, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:55'),
(257, 48, 48, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:55'),
(258, 48, 48, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:55'),
(259, 49, 49, 'move_in', '2026-06-10', '2026-06-10', '2026-06-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-05-28 07:40:00', '2026-10-01 15:13:56'),
(260, 49, 49, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:56'),
(261, 49, 49, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:56'),
(262, 49, 49, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:56'),
(263, 49, 49, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:13:56'),
(264, 50, 50, 'move_in', '2026-07-03', '2026-07-03', '2026-07-03', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-06-19 08:40:00', '2026-10-01 15:13:57'),
(265, 50, 50, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:57'),
(266, 50, 50, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:57'),
(267, 50, 50, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:57'),
(268, 51, 51, 'move_in', '2025-11-08', '2025-11-08', '2025-11-08', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-11-03 01:40:00', '2026-10-01 15:13:58'),
(269, 51, 51, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:13:58'),
(270, 51, 51, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:13:58'),
(271, 51, 51, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:13:58'),
(272, 51, 51, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:13:58'),
(273, 51, 51, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:13:58'),
(274, 51, 51, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:13:58'),
(275, 51, 51, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:13:58'),
(276, 51, 51, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:13:58'),
(277, 51, 51, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:13:58'),
(278, 51, 51, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:58'),
(279, 51, 51, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:58'),
(280, 52, 52, 'move_in', '2026-08-01', '2026-08-01', '2026-08-01', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-07-26 02:40:00', '2026-10-01 15:13:59'),
(281, 52, 52, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:13:59'),
(282, 52, 52, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:13:59'),
(283, 53, 53, 'move_in', '2026-03-13', '2026-03-13', '2026-03-13', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-03-06 03:40:00', '2026-10-01 15:14:00'),
(284, 53, 53, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:00'),
(285, 53, 53, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:00'),
(286, 53, 53, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:00'),
(287, 53, 53, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:00'),
(288, 53, 53, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:00'),
(289, 53, 53, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:00'),
(290, 53, 53, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:00'),
(291, 54, 54, 'move_in', '2026-03-25', '2026-03-25', '2026-03-25', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-03-17 04:40:00', '2026-10-01 15:14:01'),
(292, 54, 54, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:01'),
(293, 54, 54, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:01'),
(294, 54, 54, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:01'),
(295, 54, 54, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:01'),
(296, 54, 54, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:01'),
(297, 54, 54, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:01'),
(298, 54, 54, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:01'),
(299, 55, 55, 'move_in', '2026-01-23', '2026-01-23', '2026-01-23', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-01-14 05:40:00', '2026-10-01 15:14:03'),
(300, 55, 55, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:03'),
(301, 55, 55, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:03'),
(302, 55, 55, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:03'),
(303, 55, 55, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:03'),
(304, 55, 55, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:03'),
(305, 55, 55, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:03'),
(306, 55, 55, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:03'),
(307, 55, 55, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:03'),
(308, 55, 55, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:03'),
(309, 56, 56, 'move_in', '2025-11-10', '2025-11-10', '2025-11-10', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-10-31 06:40:00', '2026-10-01 15:14:04'),
(310, 56, 56, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:04'),
(311, 56, 56, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:04'),
(312, 56, 56, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:04'),
(313, 56, 56, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:04'),
(314, 56, 56, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:04'),
(315, 56, 56, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:04'),
(316, 56, 56, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:04'),
(317, 56, 56, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:04'),
(318, 56, 56, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:04'),
(319, 56, 56, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:04');
INSERT INTO `billing_statements` (`id`, `contract_id`, `tenant_id`, `type`, `billing_period_start`, `billing_period_end`, `due_date`, `base_rent`, `utilities_amount`, `wifi_amount`, `penalty_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(320, 56, 56, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:04'),
(321, 57, 57, 'move_in', '2026-01-15', '2026-01-15', '2026-01-15', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-01-04 07:40:00', '2026-10-01 15:14:05'),
(322, 57, 57, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:05'),
(323, 57, 57, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:05'),
(324, 57, 57, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:05'),
(325, 57, 57, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:05'),
(326, 57, 57, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:05'),
(327, 57, 57, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:05'),
(328, 57, 57, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:05'),
(329, 57, 57, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:05'),
(330, 57, 57, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:05'),
(331, 58, 58, 'move_in', '2026-08-02', '2026-08-02', '2026-08-02', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-07-21 08:40:00', '2026-10-01 15:14:06'),
(332, 58, 58, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:06'),
(333, 58, 58, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:06'),
(334, 59, 59, 'move_in', '2026-06-01', '2026-06-01', '2026-06-01', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-19 01:40:00', '2026-10-01 15:14:07'),
(335, 59, 59, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:07'),
(336, 59, 59, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:07'),
(337, 59, 59, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:07'),
(338, 59, 59, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:07'),
(339, 60, 60, 'move_in', '2026-07-25', '2026-07-25', '2026-07-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-07-11 02:40:00', '2026-10-01 15:14:08'),
(340, 60, 60, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:08'),
(341, 60, 60, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:08'),
(342, 60, 60, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:08'),
(343, 61, 61, 'move_in', '2026-02-10', '2026-02-10', '2026-02-10', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-02-05 03:40:00', '2026-10-01 15:14:09'),
(344, 61, 61, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:09'),
(345, 61, 61, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:09'),
(346, 61, 61, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:09'),
(347, 61, 61, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:09'),
(348, 61, 61, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:09'),
(349, 61, 61, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:09'),
(350, 61, 61, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:09'),
(351, 61, 61, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:09'),
(352, 62, 62, 'move_in', '2025-12-20', '2025-12-20', '2025-12-20', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-14 04:40:00', '2026-10-01 15:14:10'),
(353, 62, 62, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:10'),
(354, 62, 62, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:10'),
(355, 62, 62, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:10'),
(356, 62, 62, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:10'),
(357, 62, 62, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:10'),
(358, 62, 62, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:11'),
(359, 62, 62, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:11'),
(360, 62, 62, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:11'),
(361, 62, 62, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:11'),
(362, 62, 62, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:11'),
(363, 63, 63, 'move_in', '2026-04-05', '2026-04-05', '2026-04-05', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-03-29 05:40:00', '2026-10-01 15:14:12'),
(364, 63, 63, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:12'),
(365, 63, 63, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:12'),
(366, 63, 63, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:12'),
(367, 63, 63, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:12'),
(368, 63, 63, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:12'),
(369, 63, 63, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:12'),
(370, 64, 64, 'move_in', '2026-09-01', '2026-09-01', '2026-09-01', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-08-24 06:40:00', '2026-10-01 15:14:13'),
(371, 64, 64, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:13'),
(372, 65, 65, 'move_in', '2026-04-18', '2026-04-18', '2026-04-18', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-04-09 07:40:00', '2026-10-01 15:14:14'),
(373, 65, 65, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:14'),
(374, 65, 65, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:14'),
(375, 65, 65, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:14'),
(376, 65, 65, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:14'),
(377, 65, 65, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:14'),
(378, 65, 65, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:14'),
(379, 66, 66, 'move_in', '2026-06-06', '2026-06-06', '2026-06-06', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-27 08:40:00', '2026-10-01 15:14:15'),
(380, 66, 66, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:15'),
(381, 66, 66, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:15'),
(382, 66, 66, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:15'),
(383, 66, 66, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:15'),
(384, 67, 67, 'move_in', '2025-12-22', '2025-12-22', '2025-12-22', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-11 01:40:00', '2026-10-01 15:14:16'),
(385, 67, 67, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:16'),
(386, 67, 67, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:16'),
(387, 67, 67, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:16'),
(388, 67, 67, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:16'),
(389, 67, 67, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:16'),
(390, 67, 67, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:16'),
(391, 67, 67, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:16'),
(392, 67, 67, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:16'),
(393, 67, 67, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:16'),
(394, 67, 67, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:16'),
(395, 68, 68, 'move_in', '2025-12-25', '2025-12-25', '2025-12-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-13 02:40:00', '2026-10-01 15:14:17'),
(396, 68, 68, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:17'),
(397, 68, 68, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:17'),
(398, 68, 68, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:17'),
(399, 68, 68, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:17'),
(400, 68, 68, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:17'),
(401, 68, 68, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:17'),
(402, 68, 68, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:17'),
(403, 68, 68, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:17'),
(404, 68, 68, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:17'),
(405, 68, 68, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:17'),
(406, 69, 69, 'move_in', '2025-12-05', '2025-12-05', '2025-12-05', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-11-22 03:40:00', '2026-10-01 15:14:18'),
(407, 69, 69, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:18'),
(408, 69, 69, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:18'),
(409, 69, 69, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:18'),
(410, 69, 69, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:18'),
(411, 69, 69, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:18'),
(412, 69, 69, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:18'),
(413, 69, 69, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:18'),
(414, 69, 69, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:18'),
(415, 69, 69, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:18'),
(416, 69, 69, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:18'),
(417, 70, 70, 'move_in', '2026-06-01', '2026-06-01', '2026-06-01', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-18 04:40:00', '2026-10-01 15:14:19'),
(418, 70, 70, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:20'),
(419, 70, 70, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:20'),
(420, 70, 70, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:20'),
(421, 70, 70, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:20'),
(422, 71, 71, 'move_in', '2026-03-20', '2026-03-20', '2026-03-20', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-03-15 05:40:00', '2026-10-01 15:14:21'),
(423, 71, 71, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:21'),
(424, 71, 71, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:21'),
(425, 71, 71, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:21'),
(426, 71, 71, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:21'),
(427, 71, 71, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:21'),
(428, 71, 71, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:21'),
(429, 71, 71, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:21'),
(430, 72, 72, 'move_in', '2026-05-16', '2026-05-16', '2026-05-16', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-05-10 06:40:00', '2026-10-01 15:14:22'),
(431, 72, 72, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:22'),
(432, 72, 72, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:22'),
(433, 72, 72, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:22'),
(434, 72, 72, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:22'),
(435, 72, 72, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:22'),
(436, 73, 73, 'move_in', '2025-12-22', '2025-12-22', '2025-12-22', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-15 07:40:00', '2026-10-01 15:14:23'),
(437, 73, 73, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:23'),
(438, 73, 73, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:23'),
(439, 73, 73, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:23'),
(440, 73, 73, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:23'),
(441, 73, 73, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:23'),
(442, 73, 73, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:23'),
(443, 73, 73, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:23'),
(444, 73, 73, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:23'),
(445, 73, 73, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:23'),
(446, 73, 73, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:23'),
(447, 74, 74, 'move_in', '2026-08-03', '2026-08-03', '2026-08-03', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-07-26 08:40:00', '2026-10-01 15:14:24'),
(448, 74, 74, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:24'),
(449, 74, 74, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:24'),
(450, 75, 75, 'move_in', '2026-01-19', '2026-01-19', '2026-01-19', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-01-10 01:40:00', '2026-10-01 15:14:25'),
(451, 75, 75, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:25'),
(452, 75, 75, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:25'),
(453, 75, 75, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:25'),
(454, 75, 75, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:25'),
(455, 75, 75, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:25'),
(456, 75, 75, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:25'),
(457, 75, 75, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:25'),
(458, 75, 75, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:25'),
(459, 75, 75, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:25'),
(460, 76, 76, 'move_in', '2026-01-08', '2026-01-08', '2026-01-08', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-29 02:40:00', '2026-10-01 15:14:26'),
(461, 76, 76, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:26'),
(462, 76, 76, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:26'),
(463, 76, 76, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:26'),
(464, 76, 76, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:26'),
(465, 76, 76, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:26'),
(466, 76, 76, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:26'),
(467, 76, 76, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:26'),
(468, 76, 76, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:26'),
(469, 76, 76, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:26'),
(470, 77, 77, 'move_in', '2026-06-16', '2026-06-16', '2026-06-16', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-06-05 03:40:00', '2026-10-01 15:14:27'),
(471, 77, 77, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:27'),
(472, 77, 77, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:27'),
(473, 77, 77, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:27'),
(474, 77, 77, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:27'),
(475, 78, 78, 'move_in', '2026-05-08', '2026-05-08', '2026-05-08', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-04-26 04:40:00', '2026-10-01 15:14:28'),
(476, 78, 78, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:28'),
(477, 78, 78, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:28'),
(478, 78, 78, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:28'),
(479, 78, 78, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:28'),
(480, 78, 78, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:28'),
(481, 79, 79, 'move_in', '2026-05-11', '2026-05-11', '2026-05-11', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-04-28 05:40:00', '2026-10-01 15:14:29'),
(482, 79, 79, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:29'),
(483, 79, 79, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:29'),
(484, 79, 79, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:29'),
(485, 79, 79, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:29'),
(486, 79, 79, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:29'),
(487, 80, 80, 'move_in', '2025-11-14', '2025-11-14', '2025-11-14', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-10-31 06:40:00', '2026-10-01 15:14:31'),
(488, 80, 80, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:31'),
(489, 80, 80, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:31'),
(490, 80, 80, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:31'),
(491, 80, 80, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:31'),
(492, 80, 80, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:31'),
(493, 80, 80, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:31'),
(494, 80, 80, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:31'),
(495, 80, 80, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:31'),
(496, 80, 80, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:31'),
(497, 80, 80, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:31'),
(498, 80, 80, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:31'),
(499, 81, 81, 'move_in', '2025-12-23', '2025-12-23', '2025-12-23', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-12-18 07:40:00', '2026-10-01 15:14:32'),
(500, 81, 81, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:32'),
(501, 81, 81, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:32'),
(502, 81, 81, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:32'),
(503, 81, 81, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:32'),
(504, 81, 81, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:32'),
(505, 81, 81, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:32'),
(506, 81, 81, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:32'),
(507, 81, 81, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:32'),
(508, 81, 81, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:32'),
(509, 81, 81, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'unpaid', '2026-09-30 16:05:00', '2026-10-01 15:14:32'),
(510, 82, 82, 'move_in', '2026-08-04', '2026-08-04', '2026-08-04', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-07-29 08:40:00', '2026-10-01 15:14:33'),
(511, 82, 82, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:33'),
(512, 82, 82, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:33'),
(513, 83, 83, 'move_in', '2026-08-11', '2026-08-11', '2026-08-11', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-08-04 01:40:00', '2026-10-01 15:14:34'),
(514, 83, 83, 'monthly', '2026-09-01', '2026-09-30', '2026-09-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-08-31 16:05:00', '2026-10-01 15:14:34'),
(515, 83, 83, 'monthly', '2026-10-01', '2026-10-31', '2026-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-09-30 16:05:00', '2026-10-01 15:14:34'),
(516, 84, 84, 'move_in', '2025-09-23', '2025-09-23', '2025-09-23', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-10 02:30:00', '2026-10-01 15:14:34'),
(517, 84, 84, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(518, 84, 84, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(519, 84, 84, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(520, 84, 84, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(521, 84, 84, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(522, 84, 84, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(523, 85, 85, 'move_in', '2025-10-15', '2025-10-15', '2025-10-15', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-10-01 03:30:00', '2026-10-01 15:14:34'),
(524, 85, 85, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(525, 85, 85, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(526, 85, 85, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(527, 85, 85, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(528, 86, 86, 'move_in', '2025-09-04', '2025-09-04', '2025-09-04', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-08-27 04:30:00', '2026-10-01 15:14:34'),
(529, 86, 86, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(530, 86, 86, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(531, 86, 86, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(532, 87, 87, 'move_in', '2026-02-02', '2026-02-02', '2026-02-02', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2026-01-24 05:30:00', '2026-10-01 15:14:34'),
(533, 87, 87, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(534, 87, 87, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(535, 87, 87, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:34'),
(536, 88, 88, 'move_in', '2025-09-05', '2025-09-05', '2025-09-05', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-08-26 06:30:00', '2026-10-01 15:14:34'),
(537, 88, 88, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(538, 88, 88, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(539, 88, 88, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(540, 88, 88, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(541, 89, 89, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-01 07:30:00', '2026-10-01 15:14:34'),
(542, 89, 89, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(543, 89, 89, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(544, 89, 89, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(545, 90, 90, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-08-31 08:30:00', '2026-10-01 15:14:34'),
(546, 90, 90, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(547, 90, 90, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(548, 90, 90, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(549, 90, 90, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(550, 90, 90, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(551, 91, 91, 'move_in', '2025-09-11', '2025-09-11', '2025-09-11', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-08-29 01:30:00', '2026-10-01 15:14:34'),
(552, 91, 91, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(553, 91, 91, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(554, 91, 91, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(555, 91, 91, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(556, 91, 91, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(557, 92, 92, 'move_in', '2025-10-16', '2025-10-16', '2025-10-16', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-10-02 02:30:00', '2026-10-01 15:14:34'),
(558, 92, 92, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(559, 92, 92, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(560, 92, 92, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(561, 92, 92, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(562, 92, 92, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(563, 92, 92, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(564, 92, 92, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:34'),
(565, 93, 93, 'move_in', '2025-10-10', '2025-10-10', '2025-10-10', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-10-02 03:30:00', '2026-10-01 15:14:34'),
(566, 93, 93, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(567, 93, 93, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(568, 93, 93, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(569, 93, 93, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(570, 93, 93, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(571, 93, 93, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(572, 93, 93, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:34'),
(573, 93, 93, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:34'),
(574, 94, 94, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-21 04:30:00', '2026-10-01 15:14:34'),
(575, 94, 94, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(576, 94, 94, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(577, 94, 94, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(578, 95, 95, 'move_in', '2025-09-23', '2025-09-23', '2025-09-23', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-13 05:30:00', '2026-10-01 15:14:34'),
(579, 95, 95, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(580, 95, 95, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(581, 95, 95, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(582, 95, 95, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(583, 95, 95, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(584, 95, 95, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(585, 95, 95, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(586, 96, 96, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-10 06:30:00', '2026-10-01 15:14:34'),
(587, 96, 96, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(588, 96, 96, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(589, 96, 96, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(590, 96, 96, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(591, 96, 96, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(592, 97, 97, 'move_in', '2025-10-10', '2025-10-10', '2025-10-10', 9000.00, 0.00, 0.00, 0.00, 9000.00, 'paid', '2025-09-28 07:30:00', '2026-10-01 15:14:34'),
(593, 97, 97, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(594, 97, 97, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4500.00, 0.00, 0.00, 0.00, 4500.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(595, 97, 97, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4500.00, 0.00, 0.00, 450.00, 4950.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(596, 98, 98, 'move_in', '2025-09-17', '2025-09-17', '2025-09-17', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-04 08:30:00', '2026-10-01 15:14:34'),
(597, 98, 98, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(598, 98, 98, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(599, 98, 98, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(600, 99, 99, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-07 01:30:00', '2026-10-01 15:14:34'),
(601, 99, 99, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(602, 99, 99, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(603, 99, 99, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(604, 99, 99, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(605, 100, 100, 'move_in', '2026-02-12', '2026-02-12', '2026-02-12', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-02-04 02:30:00', '2026-10-01 15:14:34'),
(606, 100, 100, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(607, 100, 100, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(608, 100, 100, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:34'),
(609, 101, 101, 'move_in', '2025-09-13', '2025-09-13', '2025-09-13', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-04 03:30:00', '2026-10-01 15:14:34'),
(610, 101, 101, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(611, 101, 101, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(612, 101, 101, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(613, 101, 101, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(614, 101, 101, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(615, 101, 101, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(616, 101, 101, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(617, 102, 102, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-02 04:30:00', '2026-10-01 15:14:34'),
(618, 102, 102, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(619, 102, 102, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(620, 102, 102, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(621, 102, 102, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(622, 102, 102, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(623, 103, 103, 'move_in', '2025-09-09', '2025-09-09', '2025-09-09', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-08-29 05:30:00', '2026-10-01 15:14:34'),
(624, 103, 103, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(625, 103, 103, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(626, 103, 103, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(627, 103, 103, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(628, 103, 103, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:34'),
(629, 103, 103, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:34'),
(630, 103, 103, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34'),
(631, 104, 104, 'move_in', '2025-09-04', '2025-09-04', '2025-09-04', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-08-23 06:30:00', '2026-10-01 15:14:34'),
(632, 104, 104, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:34'),
(633, 104, 104, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:34'),
(634, 104, 104, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:34'),
(635, 104, 104, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:34'),
(636, 105, 105, 'move_in', '2026-03-09', '2026-03-09', '2026-03-09', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-02-24 07:30:00', '2026-10-01 15:14:34'),
(637, 105, 105, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:34');
INSERT INTO `billing_statements` (`id`, `contract_id`, `tenant_id`, `type`, `billing_period_start`, `billing_period_end`, `due_date`, `base_rent`, `utilities_amount`, `wifi_amount`, `penalty_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(638, 105, 105, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:34'),
(639, 105, 105, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:34'),
(640, 106, 106, 'move_in', '2025-09-16', '2025-09-16', '2025-09-16', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-02 08:30:00', '2026-10-01 15:14:35'),
(641, 106, 106, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(642, 106, 106, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(643, 106, 106, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(644, 106, 106, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(645, 106, 106, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(646, 106, 106, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(647, 106, 106, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(648, 106, 106, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(649, 107, 107, 'move_in', '2025-09-20', '2025-09-20', '2025-09-20', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-12 01:30:00', '2026-10-01 15:14:35'),
(650, 107, 107, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(651, 107, 107, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(652, 107, 107, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(653, 107, 107, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(654, 108, 108, 'move_in', '2025-10-02', '2025-10-02', '2025-10-02', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-23 02:30:00', '2026-10-01 15:14:35'),
(655, 108, 108, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(656, 108, 108, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(657, 108, 108, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(658, 108, 108, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(659, 108, 108, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(660, 108, 108, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(661, 108, 108, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(662, 109, 109, 'move_in', '2025-09-06', '2025-09-06', '2025-09-06', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-27 03:30:00', '2026-10-01 15:14:35'),
(663, 109, 109, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(664, 109, 109, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(665, 109, 109, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(666, 110, 110, 'move_in', '2026-01-17', '2026-01-17', '2026-01-17', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-01-06 04:30:00', '2026-10-01 15:14:35'),
(667, 110, 110, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(668, 110, 110, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(669, 110, 110, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(670, 110, 110, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(671, 111, 111, 'move_in', '2025-10-15', '2025-10-15', '2025-10-15', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-10-03 05:30:00', '2026-10-01 15:14:35'),
(672, 111, 111, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(673, 111, 111, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(674, 111, 111, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(675, 111, 111, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(676, 111, 111, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(677, 111, 111, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(678, 112, 112, 'move_in', '2025-09-06', '2025-09-06', '2025-09-06', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-24 06:30:00', '2026-10-01 15:14:35'),
(679, 112, 112, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(680, 112, 112, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(681, 112, 112, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(682, 112, 112, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(683, 112, 112, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(684, 113, 113, 'move_in', '2026-04-03', '2026-04-03', '2026-04-03', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-03-20 07:30:00', '2026-10-01 15:14:35'),
(685, 113, 113, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(686, 113, 113, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(687, 113, 113, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:35'),
(688, 113, 113, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:35'),
(689, 114, 114, 'move_in', '2025-09-14', '2025-09-14', '2025-09-14', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-06 08:30:00', '2026-10-01 15:14:35'),
(690, 114, 114, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(691, 114, 114, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(692, 114, 114, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(693, 114, 114, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(694, 114, 114, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(695, 114, 114, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(696, 114, 114, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(697, 114, 114, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(698, 115, 115, 'move_in', '2025-09-11', '2025-09-11', '2025-09-11', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-02 01:30:00', '2026-10-01 15:14:35'),
(699, 115, 115, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(700, 115, 115, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(701, 115, 115, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(702, 115, 115, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(703, 115, 115, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(704, 115, 115, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(705, 116, 116, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-15 02:30:00', '2026-10-01 15:14:35'),
(706, 116, 116, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(707, 116, 116, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(708, 116, 116, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(709, 117, 117, 'move_in', '2025-09-10', '2025-09-10', '2025-09-10', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-08-30 03:30:00', '2026-10-01 15:14:35'),
(710, 117, 117, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(711, 117, 117, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(712, 117, 117, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(713, 117, 117, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(714, 117, 117, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(715, 118, 118, 'move_in', '2026-03-15', '2026-03-15', '2026-03-15', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-03-03 04:30:00', '2026-10-01 15:14:35'),
(716, 118, 118, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(717, 118, 118, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(718, 118, 118, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(719, 118, 118, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:35'),
(720, 118, 118, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:35'),
(721, 119, 119, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-08-30 05:30:00', '2026-10-01 15:14:35'),
(722, 119, 119, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(723, 119, 119, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(724, 119, 119, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(725, 119, 119, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(726, 119, 119, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(727, 119, 119, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(728, 120, 120, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(729, 120, 120, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(730, 120, 120, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(731, 120, 120, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(732, 120, 120, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(733, 120, 120, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(734, 120, 120, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(735, 120, 120, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(736, 120, 120, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(737, 121, 121, 'move_in', '2025-10-16', '2025-10-16', '2025-10-16', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-10-08 07:30:00', '2026-10-01 15:14:35'),
(738, 121, 121, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(739, 121, 121, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(740, 121, 121, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(741, 122, 122, 'move_in', '2026-02-18', '2026-02-18', '2026-02-18', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-02-09 08:30:00', '2026-10-01 15:14:35'),
(742, 122, 122, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(743, 122, 122, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(744, 122, 122, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(745, 123, 123, 'move_in', '2025-09-14', '2025-09-14', '2025-09-14', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-04 01:30:00', '2026-10-01 15:14:35'),
(746, 123, 123, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(747, 123, 123, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(748, 123, 123, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(749, 123, 123, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(750, 123, 123, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(751, 123, 123, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(752, 123, 123, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(753, 123, 123, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(754, 124, 124, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-14 02:30:00', '2026-10-01 15:14:35'),
(755, 124, 124, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(756, 124, 124, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(757, 124, 124, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(758, 125, 125, 'move_in', '2025-09-23', '2025-09-23', '2025-09-23', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-11 03:30:00', '2026-10-01 15:14:35'),
(759, 125, 125, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(760, 125, 125, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(761, 125, 125, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(762, 126, 126, 'move_in', '2026-01-14', '2026-01-14', '2026-01-14', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-01-01 04:30:00', '2026-10-01 15:14:35'),
(763, 126, 126, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(764, 126, 126, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(765, 126, 126, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(766, 126, 126, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(767, 126, 126, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(768, 127, 127, 'move_in', '2025-10-09', '2025-10-09', '2025-10-09', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-25 05:30:00', '2026-10-01 15:14:35'),
(769, 127, 127, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(770, 127, 127, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(771, 127, 127, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(772, 128, 128, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-13 06:30:00', '2026-10-01 15:14:35'),
(773, 128, 128, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(774, 128, 128, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(775, 128, 128, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(776, 128, 128, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(777, 128, 128, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(778, 128, 128, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(779, 128, 128, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(780, 128, 128, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(781, 129, 129, 'move_in', '2025-10-13', '2025-10-13', '2025-10-13', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-10-04 07:30:00', '2026-10-01 15:14:35'),
(782, 129, 129, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(783, 129, 129, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(784, 129, 129, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(785, 130, 130, 'move_in', '2025-09-04', '2025-09-04', '2025-09-04', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-25 08:30:00', '2026-10-01 15:14:35'),
(786, 130, 130, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(787, 130, 130, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(788, 130, 130, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(789, 130, 130, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(790, 131, 131, 'move_in', '2025-09-10', '2025-09-10', '2025-09-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-30 01:30:00', '2026-10-01 15:14:35'),
(791, 131, 131, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(792, 131, 131, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(793, 131, 131, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(794, 131, 131, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(795, 131, 131, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(796, 131, 131, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(797, 131, 131, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(798, 131, 131, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(799, 132, 132, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-18 02:30:00', '2026-10-01 15:14:35'),
(800, 132, 132, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(801, 132, 132, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(802, 132, 132, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(803, 132, 132, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(804, 132, 132, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(805, 133, 133, 'move_in', '2025-09-27', '2025-09-27', '2025-09-27', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-14 03:30:00', '2026-10-01 15:14:35'),
(806, 133, 133, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(807, 133, 133, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(808, 133, 133, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(809, 134, 134, 'move_in', '2025-10-07', '2025-10-07', '2025-10-07', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-23 04:30:00', '2026-10-01 15:14:35'),
(810, 134, 134, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(811, 134, 134, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(812, 134, 134, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(813, 134, 134, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(814, 135, 135, 'move_in', '2025-09-10', '2025-09-10', '2025-09-10', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-02 05:30:00', '2026-10-01 15:14:35'),
(815, 135, 135, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(816, 135, 135, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(817, 135, 135, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(818, 135, 135, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(819, 135, 135, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(820, 135, 135, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(821, 135, 135, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(822, 136, 136, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(823, 136, 136, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(824, 136, 136, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(825, 136, 136, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(826, 136, 136, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(827, 137, 137, 'move_in', '2026-03-14', '2026-03-14', '2026-03-14', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-03-04 07:30:00', '2026-10-01 15:14:35'),
(828, 137, 137, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(829, 137, 137, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(830, 137, 137, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(831, 137, 137, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:35'),
(832, 138, 138, 'move_in', '2025-10-13', '2025-10-13', '2025-10-13', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-10-02 08:30:00', '2026-10-01 15:14:35'),
(833, 138, 138, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(834, 138, 138, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(835, 138, 138, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(836, 139, 139, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-09 01:30:00', '2026-10-01 15:14:35'),
(837, 139, 139, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(838, 139, 139, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(839, 139, 139, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(840, 139, 139, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(841, 139, 139, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(842, 139, 139, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(843, 139, 139, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(844, 140, 140, 'move_in', '2025-09-24', '2025-09-24', '2025-09-24', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-11 02:30:00', '2026-10-01 15:14:35'),
(845, 140, 140, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(846, 140, 140, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(847, 140, 140, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(848, 140, 140, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(849, 141, 141, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-16 03:30:00', '2026-10-01 15:14:35'),
(850, 141, 141, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(851, 141, 141, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(852, 141, 141, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(853, 141, 141, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(854, 141, 141, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(855, 141, 141, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(856, 141, 141, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(857, 142, 142, 'move_in', '2025-09-10', '2025-09-10', '2025-09-10', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-02 04:30:00', '2026-10-01 15:14:35'),
(858, 142, 142, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(859, 142, 142, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(860, 142, 142, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(861, 143, 143, 'move_in', '2026-02-09', '2026-02-09', '2026-02-09', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-01-31 05:30:00', '2026-10-01 15:14:35'),
(862, 143, 143, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(863, 143, 143, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(864, 143, 143, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(865, 143, 143, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(866, 143, 143, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:35'),
(867, 143, 143, 'monthly', '2026-08-01', '2026-08-31', '2026-08-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-07-31 16:05:00', '2026-10-01 15:14:35'),
(868, 144, 144, 'move_in', '2025-09-01', '2025-09-01', '2025-09-01', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-22 06:30:00', '2026-10-01 15:14:35'),
(869, 144, 144, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 420.00, 4620.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(870, 144, 144, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(871, 145, 145, 'move_in', '2025-12-24', '2025-12-24', '2025-12-24', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-12-13 07:30:00', '2026-10-01 15:14:35'),
(872, 145, 145, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(873, 145, 145, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(874, 145, 145, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(875, 146, 146, 'move_in', '2025-10-06', '2025-10-06', '2025-10-06', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-24 08:30:00', '2026-10-01 15:14:35'),
(876, 146, 146, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(877, 146, 146, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(878, 146, 146, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(879, 146, 146, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(880, 146, 146, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(881, 146, 146, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(882, 146, 146, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(883, 147, 147, 'move_in', '2025-09-06', '2025-09-06', '2025-09-06', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-24 01:30:00', '2026-10-01 15:14:35'),
(884, 147, 147, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(885, 147, 147, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(886, 147, 147, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(887, 148, 148, 'move_in', '2026-02-02', '2026-02-02', '2026-02-02', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-01-19 02:30:00', '2026-10-01 15:14:35'),
(888, 148, 148, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(889, 148, 148, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(890, 148, 148, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(891, 149, 149, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-04 03:30:00', '2026-10-01 15:14:35'),
(892, 149, 149, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(893, 149, 149, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(894, 149, 149, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(895, 150, 150, 'move_in', '2026-02-02', '2026-02-02', '2026-02-02', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2026-01-24 04:30:00', '2026-10-01 15:14:35'),
(896, 150, 150, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(897, 150, 150, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(898, 150, 150, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(899, 150, 150, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(900, 151, 151, 'move_in', '2025-09-27', '2025-09-27', '2025-09-27', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-17 05:30:00', '2026-10-01 15:14:35'),
(901, 151, 151, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(902, 151, 151, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(903, 151, 151, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(904, 151, 151, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(905, 151, 151, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(906, 152, 152, 'move_in', '2025-09-27', '2025-09-27', '2025-09-27', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(907, 152, 152, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(908, 152, 152, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(909, 152, 152, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(910, 152, 152, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:35'),
(911, 152, 152, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(912, 153, 153, 'move_in', '2025-09-19', '2025-09-19', '2025-09-19', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-07 07:30:00', '2026-10-01 15:14:35'),
(913, 153, 153, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 400.00, 4400.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(914, 153, 153, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(915, 153, 153, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(916, 154, 154, 'move_in', '2025-09-16', '2025-09-16', '2025-09-16', 8000.00, 0.00, 0.00, 0.00, 8000.00, 'paid', '2025-09-03 08:30:00', '2026-10-01 15:14:35'),
(917, 154, 154, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(918, 154, 154, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(919, 154, 154, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4000.00, 0.00, 0.00, 0.00, 4000.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(920, 155, 155, 'move_in', '2025-09-14', '2025-09-14', '2025-09-14', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-31 01:30:00', '2026-10-01 15:14:35'),
(921, 155, 155, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(922, 155, 155, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(923, 155, 155, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(924, 156, 156, 'move_in', '2026-01-07', '2026-01-07', '2026-01-07', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-30 02:30:00', '2026-10-01 15:14:35'),
(925, 156, 156, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:35'),
(926, 156, 156, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:35'),
(927, 156, 156, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:35'),
(928, 156, 156, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:35'),
(929, 156, 156, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:35'),
(930, 157, 157, 'move_in', '2025-09-15', '2025-09-15', '2025-09-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-06 03:30:00', '2026-10-01 15:14:35'),
(931, 157, 157, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:35'),
(932, 157, 157, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:35'),
(933, 157, 157, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:35'),
(934, 157, 157, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(935, 157, 157, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(936, 158, 158, 'move_in', '2025-09-30', '2025-09-30', '2025-09-30', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-20 04:30:00', '2026-10-01 15:14:36'),
(937, 158, 158, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(938, 158, 158, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(939, 158, 158, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(940, 159, 159, 'move_in', '2026-01-15', '2026-01-15', '2026-01-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-01-04 05:30:00', '2026-10-01 15:14:36'),
(941, 159, 159, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(942, 159, 159, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(943, 159, 159, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(944, 160, 160, 'move_in', '2025-09-12', '2025-09-12', '2025-09-12', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-31 06:30:00', '2026-10-01 15:14:36'),
(945, 160, 160, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(946, 160, 160, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(947, 160, 160, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(948, 160, 160, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(949, 161, 161, 'move_in', '2025-09-21', '2025-09-21', '2025-09-21', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-08 07:30:00', '2026-10-01 15:14:36'),
(950, 161, 161, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(951, 161, 161, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36');
INSERT INTO `billing_statements` (`id`, `contract_id`, `tenant_id`, `type`, `billing_period_start`, `billing_period_end`, `due_date`, `base_rent`, `utilities_amount`, `wifi_amount`, `penalty_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(952, 161, 161, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(953, 161, 161, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(954, 162, 162, 'move_in', '2025-09-04', '2025-09-04', '2025-09-04', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-21 08:30:00', '2026-10-01 15:14:36'),
(955, 162, 162, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(956, 162, 162, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(957, 162, 162, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(958, 162, 162, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(959, 163, 163, 'move_in', '2026-02-25', '2026-02-25', '2026-02-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2026-02-17 01:30:00', '2026-10-01 15:14:36'),
(960, 163, 163, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(961, 163, 163, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(962, 163, 163, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:36'),
(963, 163, 163, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:36'),
(964, 164, 164, 'move_in', '2025-10-08', '2025-10-08', '2025-10-08', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-29 02:30:00', '2026-10-01 15:14:36'),
(965, 164, 164, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(966, 164, 164, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(967, 164, 164, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(968, 164, 164, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(969, 164, 164, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(970, 165, 165, 'move_in', '2025-09-02', '2025-09-02', '2025-09-02', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-23 03:30:00', '2026-10-01 15:14:36'),
(971, 165, 165, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(972, 165, 165, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(973, 165, 165, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(974, 165, 165, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(975, 165, 165, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(976, 166, 166, 'move_in', '2025-09-19', '2025-09-19', '2025-09-19', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-08 04:30:00', '2026-10-01 15:14:36'),
(977, 166, 166, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(978, 166, 166, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(979, 166, 166, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(980, 166, 166, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(981, 166, 166, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(982, 167, 167, 'move_in', '2025-09-15', '2025-09-15', '2025-09-15', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-03 05:30:00', '2026-10-01 15:14:36'),
(983, 167, 167, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(984, 167, 167, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(985, 167, 167, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(986, 167, 167, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(987, 167, 167, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(988, 167, 167, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(989, 168, 168, 'move_in', '2025-09-03', '2025-09-03', '2025-09-03', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-21 06:30:00', '2026-10-01 15:14:36'),
(990, 168, 168, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(991, 168, 168, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(992, 168, 168, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(993, 168, 168, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(994, 168, 168, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(995, 169, 169, 'move_in', '2025-09-11', '2025-09-11', '2025-09-11', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-28 07:30:00', '2026-10-01 15:14:36'),
(996, 169, 169, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(997, 169, 169, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(998, 169, 169, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(999, 169, 169, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1000, 170, 170, 'move_in', '2025-09-06', '2025-09-06', '2025-09-06', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-29 08:30:00', '2026-10-01 15:14:36'),
(1001, 170, 170, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1002, 170, 170, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1003, 170, 170, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1004, 170, 170, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1005, 170, 170, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1006, 170, 170, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1007, 170, 170, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1008, 171, 171, 'move_in', '2025-10-03', '2025-10-03', '2025-10-03', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-24 01:30:00', '2026-10-01 15:14:36'),
(1009, 171, 171, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1010, 171, 171, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1011, 171, 171, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1012, 171, 171, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1013, 172, 172, 'move_in', '2025-10-06', '2025-10-06', '2025-10-06', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-26 02:30:00', '2026-10-01 15:14:36'),
(1014, 172, 172, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1015, 172, 172, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1016, 172, 172, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1017, 172, 172, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1018, 172, 172, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1019, 173, 173, 'move_in', '2025-09-20', '2025-09-20', '2025-09-20', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-09 03:30:00', '2026-10-01 15:14:36'),
(1020, 173, 173, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1021, 173, 173, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1022, 173, 173, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1023, 174, 174, 'move_in', '2026-01-06', '2026-01-06', '2026-01-06', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-12-25 04:30:00', '2026-10-01 15:14:36'),
(1024, 174, 174, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1025, 174, 174, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1026, 174, 174, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1027, 174, 174, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:36'),
(1028, 174, 174, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:36'),
(1029, 175, 175, 'move_in', '2025-09-09', '2025-09-09', '2025-09-09', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-27 05:30:00', '2026-10-01 15:14:36'),
(1030, 175, 175, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1031, 175, 175, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1032, 175, 175, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1033, 176, 176, 'move_in', '2025-09-25', '2025-09-25', '2025-09-25', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-11 06:30:00', '2026-10-01 15:14:36'),
(1034, 176, 176, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1035, 176, 176, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1036, 176, 176, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1037, 176, 176, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1038, 176, 176, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1039, 176, 176, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1040, 176, 176, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1041, 177, 177, 'move_in', '2025-09-26', '2025-09-26', '2025-09-26', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-18 07:30:00', '2026-10-01 15:14:36'),
(1042, 177, 177, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1043, 177, 177, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1044, 177, 177, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1045, 177, 177, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1046, 177, 177, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1047, 177, 177, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1048, 178, 178, 'move_in', '2025-09-29', '2025-09-29', '2025-09-29', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-20 08:30:00', '2026-10-01 15:14:36'),
(1049, 178, 178, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1050, 178, 178, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1051, 178, 178, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1052, 178, 178, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1053, 178, 178, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1054, 178, 178, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1055, 179, 179, 'move_in', '2025-09-07', '2025-09-07', '2025-09-07', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-28 01:30:00', '2026-10-01 15:14:36'),
(1056, 179, 179, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1057, 179, 179, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1058, 179, 179, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1059, 179, 179, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1060, 179, 179, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1061, 179, 179, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1062, 180, 180, 'move_in', '2025-09-26', '2025-09-26', '2025-09-26', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-09-15 02:30:00', '2026-10-01 15:14:36'),
(1063, 180, 180, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 380.00, 4180.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1064, 180, 180, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1065, 180, 180, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1066, 180, 180, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1067, 180, 180, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1068, 180, 180, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1069, 180, 180, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1070, 180, 180, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:36'),
(1071, 181, 181, 'move_in', '2025-09-07', '2025-09-07', '2025-09-07', 7600.00, 0.00, 0.00, 0.00, 7600.00, 'paid', '2025-08-26 03:30:00', '2026-10-01 15:14:36'),
(1072, 181, 181, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1073, 181, 181, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1074, 181, 181, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1075, 181, 181, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1076, 181, 181, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 3800.00, 0.00, 0.00, 0.00, 3800.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1077, 182, 182, 'move_in', '2025-09-11', '2025-09-11', '2025-09-11', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-08-29 04:30:00', '2026-10-01 15:14:36'),
(1078, 182, 182, 'monthly', '2025-10-01', '2025-10-31', '2025-10-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-09-30 16:05:00', '2026-10-01 15:14:36'),
(1079, 182, 182, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1080, 182, 182, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1081, 182, 182, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1082, 182, 182, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36'),
(1083, 182, 182, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1084, 182, 182, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1085, 183, 183, 'move_in', '2025-10-16', '2025-10-16', '2025-10-16', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-10-02 05:30:00', '2026-10-01 15:14:36'),
(1086, 183, 183, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1087, 183, 183, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1088, 183, 183, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1089, 184, 184, 'move_in', '2026-02-10', '2026-02-10', '2026-02-10', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2026-02-02 06:30:00', '2026-10-01 15:14:36'),
(1090, 184, 184, 'monthly', '2026-03-01', '2026-03-31', '2026-03-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-02-28 16:05:00', '2026-10-01 15:14:36'),
(1091, 184, 184, 'monthly', '2026-04-01', '2026-04-30', '2026-04-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-03-31 16:05:00', '2026-10-01 15:14:36'),
(1092, 184, 184, 'monthly', '2026-05-01', '2026-05-31', '2026-05-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-04-30 16:05:00', '2026-10-01 15:14:36'),
(1093, 184, 184, 'monthly', '2026-06-01', '2026-06-30', '2026-06-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-05-31 16:05:00', '2026-10-01 15:14:36'),
(1094, 184, 184, 'monthly', '2026-07-01', '2026-07-31', '2026-07-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-06-30 16:05:00', '2026-10-01 15:14:36'),
(1095, 185, 185, 'move_in', '2025-10-02', '2025-10-02', '2025-10-02', 8400.00, 0.00, 0.00, 0.00, 8400.00, 'paid', '2025-09-23 07:30:00', '2026-10-01 15:14:36'),
(1096, 185, 185, 'monthly', '2025-11-01', '2025-11-30', '2025-11-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-10-31 16:05:00', '2026-10-01 15:14:36'),
(1097, 185, 185, 'monthly', '2025-12-01', '2025-12-31', '2025-12-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-11-30 16:05:00', '2026-10-01 15:14:36'),
(1098, 185, 185, 'monthly', '2026-01-01', '2026-01-31', '2026-01-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2025-12-31 16:05:00', '2026-10-01 15:14:36'),
(1099, 185, 185, 'monthly', '2026-02-01', '2026-02-28', '2026-02-01', 4200.00, 0.00, 0.00, 0.00, 4200.00, 'paid', '2026-01-31 16:05:00', '2026-10-01 15:14:36');

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
(1, 14, 85, 18, 'Broken locker door hinge (bed-side locker)', 800.00, '2026-09-19', NULL, 357, '2026-09-18 16:00:00', '2026-09-18 16:00:00');

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
(1, 23, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000051649970', '2026-08-14', 355, '2026-08-13 16:00:00', '2026-08-13 16:00:00'),
(2, 84, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000063572907', '2026-04-10', 355, '2026-04-09 16:00:00', '2026-04-09 16:00:00'),
(3, 85, 4500.00, 0.00, NULL, 4500.00, 'Bank transfer', 'BDO-261001-1321', '2026-03-11', 355, '2026-03-10 16:00:00', '2026-03-10 16:00:00'),
(4, 86, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000063910804', '2026-01-12', 355, '2026-01-11 16:00:00', '2026-01-11 16:00:00'),
(5, 87, 4500.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4450.00, 'Bank transfer', 'BDO-261001-1327', '2026-06-13', 355, '2026-06-12 16:00:00', '2026-06-12 16:00:00'),
(6, 88, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000064248701', '2026-02-14', 355, '2026-02-13 16:00:00', '2026-02-13 16:00:00'),
(7, 89, 4500.00, 0.00, NULL, 4500.00, 'Bank transfer', 'BDO-261001-1334', '2026-01-15', 355, '2026-01-14 16:00:00', '2026-01-14 16:00:00'),
(8, 90, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000064634869', '2026-03-16', 355, '2026-03-15 16:00:00', '2026-03-15 16:00:00'),
(9, 91, 4500.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4450.00, 'Bank transfer', 'BDO-261001-1343', '2026-03-17', 355, '2026-03-16 16:00:00', '2026-03-16 16:00:00'),
(10, 92, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000065165850', '2026-06-18', 355, '2026-06-17 16:00:00', '2026-06-17 16:00:00'),
(11, 93, 4500.00, 0.00, NULL, 4500.00, 'Bank transfer', 'BDO-261001-1357', '2026-07-05', 355, '2026-07-04 16:00:00', '2026-07-04 16:00:00'),
(12, 94, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000065648560', '2026-01-06', 355, '2026-01-05 16:00:00', '2026-01-05 16:00:00'),
(13, 95, 4500.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4450.00, 'Bank transfer', 'BDO-261001-1366', '2026-05-07', 355, '2026-05-06 16:00:00', '2026-05-06 16:00:00'),
(14, 96, 4500.00, 0.00, NULL, 4500.00, 'GCash', '1000066179541', '2026-03-08', 355, '2026-03-07 16:00:00', '2026-03-07 16:00:00'),
(15, 97, 4500.00, 0.00, NULL, 4500.00, 'Bank transfer', 'BDO-261001-1374', '2026-02-09', 355, '2026-02-08 16:00:00', '2026-02-08 16:00:00'),
(16, 98, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000066469167', '2026-01-10', 355, '2026-01-09 16:00:00', '2026-01-09 16:00:00'),
(17, 99, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1381', '2026-02-11', 355, '2026-02-10 16:00:00', '2026-02-10 16:00:00'),
(18, 100, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000066855335', '2026-06-12', 355, '2026-06-11 16:00:00', '2026-06-11 16:00:00'),
(19, 101, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1391', '2026-05-13', 355, '2026-05-12 16:00:00', '2026-05-12 16:00:00'),
(20, 102, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000067386316', '2026-03-14', 355, '2026-03-13 16:00:00', '2026-03-13 16:00:00'),
(21, 103, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1402', '2026-05-15', 355, '2026-05-14 16:00:00', '2026-05-14 16:00:00'),
(22, 104, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000067869026', '2026-02-16', 355, '2026-02-15 16:00:00', '2026-02-15 16:00:00'),
(23, 105, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1409', '2026-07-17', 355, '2026-07-16 16:00:00', '2026-07-16 16:00:00'),
(24, 106, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000068351736', '2026-06-18', 355, '2026-06-17 16:00:00', '2026-06-17 16:00:00'),
(25, 107, 4200.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4150.00, 'Bank transfer', 'BDO-261001-1420', '2026-02-05', 355, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(26, 108, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000068882717', '2026-06-06', 355, '2026-06-05 16:00:00', '2026-06-05 16:00:00'),
(27, 109, 4200.00, 0.00, NULL, 4200.00, 'Bank transfer', 'BDO-261001-1430', '2026-01-07', 355, '2026-01-06 16:00:00', '2026-01-06 16:00:00'),
(28, 110, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000069220614', '2026-06-08', 355, '2026-06-07 16:00:00', '2026-06-07 16:00:00'),
(29, 111, 4200.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4150.00, 'Bank transfer', 'BDO-261001-1439', '2026-05-09', 355, '2026-05-08 16:00:00', '2026-05-08 16:00:00'),
(30, 112, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000069703324', '2026-03-10', 355, '2026-03-09 16:00:00', '2026-03-09 16:00:00'),
(31, 113, 4200.00, 0.00, NULL, 4200.00, 'Bank transfer', 'BDO-261001-1448', '2026-09-11', 355, '2026-09-10 16:00:00', '2026-09-10 16:00:00'),
(32, 114, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000070234305', '2026-06-12', 355, '2026-06-11 16:00:00', '2026-06-11 16:00:00'),
(33, 115, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1460', '2026-04-13', 355, '2026-04-12 16:00:00', '2026-04-12 16:00:00'),
(34, 116, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000070668744', '2026-01-14', 355, '2026-01-13 16:00:00', '2026-01-13 16:00:00'),
(35, 117, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1469', '2026-03-15', 355, '2026-03-14 16:00:00', '2026-03-14 16:00:00'),
(36, 118, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000071151454', '2026-09-16', 355, '2026-09-15 16:00:00', '2026-09-15 16:00:00'),
(37, 119, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1479', '2026-04-17', 355, '2026-04-16 16:00:00', '2026-04-16 16:00:00'),
(38, 120, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000071730706', '2026-06-18', 355, '2026-06-17 16:00:00', '2026-06-17 16:00:00'),
(39, 121, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1489', '2026-02-05', 355, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(40, 122, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000072020332', '2026-06-06', 355, '2026-06-05 16:00:00', '2026-06-05 16:00:00'),
(41, 123, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1499', '2026-06-07', 355, '2026-06-06 16:00:00', '2026-06-06 16:00:00'),
(42, 124, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000072551313', '2026-01-08', 355, '2026-01-07 16:00:00', '2026-01-07 16:00:00'),
(43, 125, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1506', '2026-01-09', 355, '2026-01-08 16:00:00', '2026-01-08 16:00:00'),
(44, 126, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000072937481', '2026-07-10', 355, '2026-07-09 16:00:00', '2026-07-09 16:00:00'),
(45, 127, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1514', '2026-02-11', 355, '2026-02-10 16:00:00', '2026-02-10 16:00:00'),
(46, 128, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000073420191', '2026-06-12', 355, '2026-06-11 16:00:00', '2026-06-11 16:00:00'),
(47, 129, 4200.00, 0.00, NULL, 4200.00, 'Bank transfer', 'BDO-261001-1524', '2026-02-13', 355, '2026-02-12 16:00:00', '2026-02-12 16:00:00'),
(48, 130, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000073758088', '2026-02-14', 355, '2026-02-13 16:00:00', '2026-02-13 16:00:00'),
(49, 131, 4200.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4150.00, 'Bank transfer', 'BDO-261001-1535', '2026-06-15', 355, '2026-06-14 16:00:00', '2026-06-14 16:00:00'),
(50, 132, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000074337340', '2026-03-16', 355, '2026-03-15 16:00:00', '2026-03-15 16:00:00'),
(51, 133, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1543', '2026-01-17', 355, '2026-01-16 16:00:00', '2026-01-16 16:00:00'),
(52, 134, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000074675237', '2026-03-18', 355, '2026-03-17 16:00:00', '2026-03-17 16:00:00'),
(53, 135, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1553', '2026-05-05', 355, '2026-05-04 16:00:00', '2026-05-04 16:00:00'),
(54, 136, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000075157947', '2026-02-06', 355, '2026-02-05 16:00:00', '2026-02-05 16:00:00'),
(55, 137, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1561', '2026-08-07', 355, '2026-08-06 16:00:00', '2026-08-06 16:00:00'),
(56, 138, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000075495844', '2026-02-08', 355, '2026-02-07 16:00:00', '2026-02-07 16:00:00'),
(57, 139, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1570', '2026-05-09', 355, '2026-05-08 16:00:00', '2026-05-08 16:00:00'),
(58, 140, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000075978554', '2026-02-10', 355, '2026-02-09 16:00:00', '2026-02-09 16:00:00'),
(59, 141, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1580', '2026-05-11', 355, '2026-05-10 16:00:00', '2026-05-10 16:00:00'),
(60, 142, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000076412993', '2026-01-12', 355, '2026-01-11 16:00:00', '2026-01-11 16:00:00'),
(61, 143, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1588', '2026-09-13', 355, '2026-09-12 16:00:00', '2026-09-12 16:00:00'),
(62, 144, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000076799161', '2025-12-14', 355, '2025-12-13 16:00:00', '2025-12-13 16:00:00'),
(63, 145, 4200.00, 0.00, NULL, 4200.00, 'Bank transfer', 'BDO-261001-1594', '2026-04-15', 355, '2026-04-14 16:00:00', '2026-04-14 16:00:00'),
(64, 146, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000077233600', '2026-06-16', 355, '2026-06-15 16:00:00', '2026-06-15 16:00:00'),
(65, 147, 4200.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4150.00, 'Bank transfer', 'BDO-261001-1603', '2026-01-17', 355, '2026-01-16 16:00:00', '2026-01-16 16:00:00'),
(66, 148, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000077571497', '2026-06-18', 355, '2026-06-17 16:00:00', '2026-06-17 16:00:00'),
(67, 149, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1610', '2026-01-05', 355, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(68, 150, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000077909394', '2026-07-06', 355, '2026-07-05 16:00:00', '2026-07-05 16:00:00'),
(69, 151, 4000.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3950.00, 'Bank transfer', 'BDO-261001-1618', '2026-03-07', 355, '2026-03-06 16:00:00', '2026-03-06 16:00:00'),
(70, 152, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000078343833', '2026-03-08', 355, '2026-03-07 16:00:00', '2026-03-07 16:00:00'),
(71, 153, 4000.00, 0.00, NULL, 4000.00, 'Bank transfer', 'BDO-261001-1626', '2026-01-09', 355, '2026-01-08 16:00:00', '2026-01-08 16:00:00'),
(72, 154, 4000.00, 0.00, NULL, 4000.00, 'GCash', '1000078633459', '2026-01-10', 355, '2026-01-09 16:00:00', '2026-01-09 16:00:00'),
(73, 155, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1632', '2026-01-11', 355, '2026-01-10 16:00:00', '2026-01-10 16:00:00'),
(74, 156, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000079019627', '2026-07-12', 355, '2026-07-11 16:00:00', '2026-07-11 16:00:00'),
(75, 157, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1642', '2026-03-13', 355, '2026-03-12 16:00:00', '2026-03-12 16:00:00'),
(76, 158, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000079405795', '2026-01-14', 355, '2026-01-13 16:00:00', '2026-01-13 16:00:00'),
(77, 159, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1648', '2026-05-15', 355, '2026-05-14 16:00:00', '2026-05-14 16:00:00'),
(78, 160, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000079743692', '2026-02-16', 355, '2026-02-15 16:00:00', '2026-02-15 16:00:00'),
(79, 161, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1656', '2026-02-17', 355, '2026-02-16 16:00:00', '2026-02-16 16:00:00'),
(80, 162, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000080129860', '2026-02-18', 355, '2026-02-17 16:00:00', '2026-02-17 16:00:00'),
(81, 163, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1664', '2026-07-05', 355, '2026-07-04 16:00:00', '2026-07-04 16:00:00'),
(82, 164, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000080564299', '2026-04-06', 355, '2026-04-05 16:00:00', '2026-04-05 16:00:00'),
(83, 165, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1674', '2026-03-07', 355, '2026-03-06 16:00:00', '2026-03-06 16:00:00'),
(84, 166, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000081047009', '2026-03-08', 355, '2026-03-07 16:00:00', '2026-03-07 16:00:00'),
(85, 167, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1684', '2026-04-09', 355, '2026-04-08 16:00:00', '2026-04-08 16:00:00'),
(86, 168, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000081529719', '2026-03-10', 355, '2026-03-09 16:00:00', '2026-03-09 16:00:00'),
(87, 169, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1693', '2026-02-11', 355, '2026-02-10 16:00:00', '2026-02-10 16:00:00'),
(88, 170, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000082012429', '2026-05-12', 355, '2026-05-11 16:00:00', '2026-05-11 16:00:00'),
(89, 171, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1703', '2026-03-13', 355, '2026-03-12 16:00:00', '2026-03-12 16:00:00'),
(90, 172, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000082446868', '2026-04-14', 355, '2026-04-13 16:00:00', '2026-04-13 16:00:00'),
(91, 173, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1711', '2026-01-15', 355, '2026-01-14 16:00:00', '2026-01-14 16:00:00'),
(92, 174, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000082833036', '2026-07-16', 355, '2026-07-15 16:00:00', '2026-07-15 16:00:00'),
(93, 175, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1719', '2026-01-17', 355, '2026-01-16 16:00:00', '2026-01-16 16:00:00'),
(94, 176, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000083315746', '2026-05-18', 355, '2026-05-17 16:00:00', '2026-05-17 16:00:00'),
(95, 177, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1732', '2026-04-05', 355, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(96, 178, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000083846727', '2026-04-06', 355, '2026-04-05 16:00:00', '2026-04-05 16:00:00'),
(97, 179, 3800.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 3750.00, 'Bank transfer', 'BDO-261001-1742', '2026-04-07', 355, '2026-04-06 16:00:00', '2026-04-06 16:00:00'),
(98, 180, 3800.00, 0.00, NULL, 3800.00, 'GCash', '1000084425979', '2026-06-08', 355, '2026-06-07 16:00:00', '2026-06-07 16:00:00'),
(99, 181, 3800.00, 0.00, NULL, 3800.00, 'Bank transfer', 'BDO-261001-1754', '2026-03-09', 355, '2026-03-08 16:00:00', '2026-03-08 16:00:00'),
(100, 182, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000084956960', '2026-05-10', 355, '2026-05-09 16:00:00', '2026-05-09 16:00:00'),
(101, 183, 4200.00, 50.00, 'Lost room key (key duplication fee, Payments and Fees Schedule).', 4150.00, 'Bank transfer', 'BDO-261001-1763', '2026-02-11', 355, '2026-02-10 16:00:00', '2026-02-10 16:00:00'),
(102, 184, 4200.00, 0.00, NULL, 4200.00, 'GCash', '1000085343128', '2026-08-12', 355, '2026-08-11 16:00:00', '2026-08-11 16:00:00'),
(103, 185, 4200.00, 0.00, NULL, 4200.00, 'Bank transfer', 'BDO-261001-1772', '2026-03-13', 355, '2026-03-12 16:00:00', '2026-03-12 16:00:00');

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
-- Table structure for table `dormitory_charges`
--

CREATE TABLE `dormitory_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `amount_note` varchar(100) DEFAULT NULL,
  `when_applies` varchar(100) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_charges`
--

INSERT INTO `dormitory_charges` (`id`, `name`, `amount`, `amount_note`, `when_applies`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Lost or unreturned key (key duplication)', 50.00, NULL, 'Upon loss or check-out', 1, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(2, 'Possession or use of hazardous items (Rules, item 9)', 500.00, NULL, 'Per violation', 2, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(3, 'Damage to dormitory property', NULL, 'Reasonable repair or replacement cost', 'Upon assessment', 3, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(4, 'Approved high-power appliance', NULL, 'per month (amount to be set)', 'If approved', 4, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(5, 'Late check-out after 2:00 PM', NULL, 'Amount to be set', 'If applicable', 5, '2026-10-01 15:12:47', '2026-10-01 15:12:47');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_house_rules`
--

CREATE TABLE `dormitory_house_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section` varchar(80) DEFAULT NULL,
  `rule_text` varchar(500) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_house_rules`
--

INSERT INTO `dormitory_house_rules` (`id`, `section`, `rule_text`, `sort_order`, `created_at`, `updated_at`) VALUES
(3, 'A. Conduct and Respect', 'Treat all tenants, staff, and visitors with respect and consideration. Harassment, discrimination, and bullying are not tolerated.', 1, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(4, 'A. Conduct and Respect', 'Quiet hours are 10:00 PM to 6:00 AM. At all other times, tenants must still avoid unreasonable noise that disturbs others.', 2, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(5, 'A. Conduct and Respect', 'Curfew is from 11:00 PM to 4:00 AM. Tenants should be inside the dormitory during curfew hours. A tenant who must be out late (for example, because of night work or a class schedule) or expects to return during curfew must inform the management in advance. Emergencies are excepted, and the tenant should inform the management as soon as possible.', 3, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(6, 'A. Conduct and Respect', 'Complaints and suggestions to improve the dormitory are welcome and may be sent to the management or through NEST.PH.', 4, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(7, 'B. Visitors and Room Access', 'Only registered tenants may enter the rooms. Visitors are received at the receiving area only. Overnight guests are not allowed.', 5, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(8, 'B. Visitors and Room Access', 'Tenants are responsible for their visitors and for any loss or damage they cause.', 6, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(9, 'B. Visitors and Room Access', 'Management may enter rooms for inspection, maintenance, and safety checks as stated in Section 7 of the Agreement.', 7, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(10, 'C. Safety and Prohibited Items', 'Alcoholic beverages, smoking, and vaping are not allowed on the premises.', 8, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(11, 'C. Safety and Prohibited Items', 'Hazardous items such as gas, cooking stoves, flammable fuels, and firearms are strictly prohibited. A tenant found using or keeping them may be fined the amount listed in the Payments and Fees Schedule (currently ₱500), and the matter may be reported to the proper authorities where required by law.', 9, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(12, 'C. Safety and Prohibited Items', 'Drugs and other illegal substances are strictly prohibited. Possession may be reported to the proper authorities.', 10, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(13, 'C. Safety and Prohibited Items', 'Pets are not allowed on the premises.', 11, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(14, 'C. Safety and Prohibited Items', 'Do not tamper with fire alarms, extinguishers, or CCTV equipment, and do not block hallways, stairs, or exits. Follow the evacuation plan and staff instructions during a fire, earthquake, or other emergency.', 12, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(15, 'C. Safety and Prohibited Items', 'Do not bring or use appliances that draw high power (for example heaters, irons, or cooking appliances) unless the management allows them. Approved appliances may carry an electricity charge listed in the Payments and Fees Schedule.', 13, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(16, 'C. Safety and Prohibited Items', 'Cameras (CCTV) may be installed in common areas for safety and security. They are not placed in bedrooms or bathrooms. How footage is used and kept is explained in the Privacy Notice.', 14, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(17, 'D. Care of the Premises', 'Keep common areas clean and tidy after use and dispose of garbage in the proper place. Washing clothes in the dormitory is not allowed. A laundry service is available outside the dormitory.', 15, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(18, 'D. Care of the Premises', 'Use the air-conditioner only from 10:00 PM to 5:00 AM (aircon schedule), and keep doors and windows closed while it is running.', 16, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(19, 'D. Care of the Premises', 'When leaving the room, turn off all faucets, showers, lights, air-conditioners, and other devices. Close and lock the door.', 17, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(20, 'D. Care of the Premises', 'Do not move, remove, or swap beds, furniture, or rooms without the management’s approval. Do not transfer or sublet your bed to another person.', 18, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(21, 'D. Care of the Premises', 'Report any damage, defect, or unsafe condition promptly to the maintenance staff or through NEST.PH.', 19, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(22, 'D. Care of the Premises', 'The tenant pays the reasonable cost of repair or replacement for loss or damage caused by the tenant, the tenant’s visitors, or anyone the tenant is responsible for, as stated in Section 6.3 of the Agreement. Charges follow the Payments and Fees Schedule.', 20, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(23, 'D. Care of the Premises', 'If a key is lost or damaged, the tenant pays the key-duplication fee listed in the Payments and Fees Schedule (currently ₱50).', 21, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(24, 'E. Liability', 'Tenants must take reasonable care of their belongings and their own safety. The management’s responsibility for loss, damage, or injury is stated in Section 11 of the Agreement. Nothing in these Rules limits any liability that the law does not allow to be limited.', 22, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(25, 'F. Breach and Disciplinary Steps', 'For a breach of these Rules, the management will usually follow these steps: (a) verbal reminder; (b) written warning; (c) fine, where one is listed in the Payments and Fees Schedule; and (d) termination under Section 13.2 of the Agreement.', 23, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(26, 'F. Breach and Disciplinary Steps', 'The management may skip steps for serious violations, such as illegal drugs, weapons, violence, or conduct that threatens the safety of others. Termination is always subject to the Agreement and Philippine law. The tenant may explain his or her side before a fine or termination is finalized.', 24, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(27, 'F. Breach and Disciplinary Steps', 'The management may update these Rules for reasonable administrative, operational, safety, or security reasons, with advance notice to tenants as stated in Section 5.3 of the Agreement.', 25, '2026-10-01 15:12:47', '2026-10-01 15:12:47');

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
  `hero_photo_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hero_photo_paths`)),
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
  `updated_at` timestamp NULL DEFAULT NULL,
  `representative_name` varchar(150) DEFAULT NULL,
  `representative_position` varchar(100) DEFAULT NULL,
  `facebook_page_name` varchar(150) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `rent_due_day` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `grace_period_days` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `late_penalty_percent` decimal(5,2) NOT NULL DEFAULT 10.00,
  `minimum_stay_months` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `move_out_notice_days` smallint(5) UNSIGNED NOT NULL DEFAULT 14,
  `extension_notice_days` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `deposit_refund_days` smallint(5) UNSIGNED NOT NULL DEFAULT 21,
  `reservation_validity_days` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `mid_month_move_in` varchar(10) NOT NULL DEFAULT 'full',
  `water_included` tinyint(1) NOT NULL DEFAULT 0,
  `electricity_included` tinyint(1) NOT NULL DEFAULT 0,
  `wifi_included` tinyint(1) NOT NULL DEFAULT 0,
  `short_term_rate` decimal(10,2) DEFAULT NULL,
  `transient_rate` decimal(10,2) DEFAULT NULL,
  `rules_version` varchar(20) DEFAULT NULL,
  `fees_version` varchar(20) DEFAULT NULL,
  `documents_effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_profile`
--

INSERT INTO `dormitory_profile` (`id`, `dorm_name`, `description`, `address`, `contact_number`, `contact_email`, `logo_path`, `hero_photo_paths`, `brand_logo_path`, `policies_file_path`, `contract_template_path`, `business_permit_path`, `bir_registration_path`, `gcash_number`, `bdo_account_number`, `payments_and_fees`, `house_rules`, `checkout_procedures`, `created_at`, `updated_at`, `representative_name`, `representative_position`, `facebook_page_name`, `facebook_url`, `website_url`, `rent_due_day`, `grace_period_days`, `late_penalty_percent`, `minimum_stay_months`, `move_out_notice_days`, `extension_notice_days`, `deposit_refund_days`, `reservation_validity_days`, `mid_month_move_in`, `water_included`, `electricity_included`, `wifi_included`, `short_term_rate`, `transient_rate`, `rules_version`, `fees_version`, `documents_effective_date`) VALUES
(1, 'Pureza Station Dormitory', 'Pureza Station Dormitory has been a trusted home for students and young professionals since 1996. Just a 5-minute walk from PUP and the Pureza LRT Station, we offer clean, secure, and affordable rooms designed for easy, comfortable living close to school and work.', '329 C De Dios, Brgy. 632, Sta. Mesa, Manila', '09774322155', 'dormitorypurezastation@gmail.com', 'dormitory-profile/yYzcKk7O16Id6prPIyO47BJJlqGqSHs3EY1fNhes.jpg', NULL, NULL, 'dormitory-profile/JU08CTcqjUS5XJqlZKb7DemhpAryCiQObz8Xy0vp.pdf', 'contracts/dormitory-contract.pdf', 'dormitory-profile/legitimacy/SLgvrZYlX5qCvu1idRlgwXiWcWFJuSqy2Ecb2GgP.pdf', 'dormitory-profile/legitimacy/5sPbbKzi1OrwF8CRR2KFtur4qQtUgSCjbdJuJ6sr.png', NULL, NULL, 'Rent may be paid in cash, GCash, or bank deposit (BDO).\n\nTenancy is subject to a three-month minimum. Tenants must provide the start\nand end date of their stay upon registration.\n\nUpon registration, new tenants pay a reservation fee composed of a security\ndeposit (one month, refundable for a 3-month contract) and one month advance\nrent. The reservation fee is non-refundable if the tenant cancels or checks\nout earlier than the three-month minimum. The deposit is returned within\n2–3 weeks after the check-out date.\n\nRent is due every 1st day of the month. For GCash or bank deposit, payment\nconfirmation must be sent to the dormitory\'s official contact channels.\n\nTenants are granted a 3-day grace period for late rent payments. Beyond the\ngrace period, a 10% penalty fee applies. Failure to pay within one month\nresults in a notice of eviction for non-payment.\n\nTenants wishing to extend their stay must give at least 1 month notice.\nMove-out requires at least 2 weeks notice; move-out schedule is end of month.', 'Alcoholic beverages, smoking, and vaping are not allowed on dormitory premises.\n\nWashing of clothes is not allowed; a laundry service is available outside.\n\nTenants are responsible for keeping common areas clean after use, and must\npromptly report any damages or issues to maintenance staff.\n\nTenants must pay for any loss or damage to dormitory property caused by\nthemselves or their guests, at the cost of the damage (minimum ₱500).\n\nOnly registered tenants may enter the rooms. Visitors may be entertained at\nthe receiving area.\n\nHazardous goods (gas, cooking stoves, flammable fuels, firearms) are strictly\nprohibited; violation carries a ₱500 fine and may be reported to authorities.\nDrugs and illegal substances are strictly prohibited and will be reported.\n\nSilence should be observed at all times out of consideration for other tenants.\nTreat fellow tenants and staff with respect — harassment, discrimination, or\nbullying will not be tolerated.\n\nManagement is not responsible for losses or injuries occurring on the premises.\nTenants should exercise care and diligence at all times.\n\nDoors and windows must be closed when using the air-conditioner. When leaving,\nturn off all faucets, showers, lights, air conditioners, and appliances, and\nlock the door. Lost or damaged keys cost ₱50 to replace.\n\nA strict NO PETS policy is enforced.\n\nCurfew hours: 11PM – 4AM. Aircon schedule: 10PM – 5AM.', 'Advanced notice: Residents planning to check out must give written notice at\nleast two weeks before their intended departure date.\n\nRoom inspection: A staff member will inspect the room/bed before check-out to\nassess damages or cleanliness issues. Rooms should be clean before inspection.\n\nDamages and repairs: Residents are responsible for damage beyond normal wear\nand tear, and will be charged for repairs or replacements.\n\nFurniture and equipment: All dormitory-provided furniture and equipment must\nbe present and in good condition. Missing or damaged items incur charges.\n\nCleanliness: Rooms must be left in move-in condition, with all personal\nbelongings removed and shared areas cleaned.\n\nTrash disposal: Dispose of all trash and recyclables in designated bins.\n\nKey return: Room keys must be returned upon check-out. Failure to return keys\nmay result in a fine.\n\nCheck-out time: Residents must vacate by 2:00 PM on the check-out date.\n\nFinal settlement: After inspection, the security deposit is returned minus any\ndeductions for damages or outstanding charges, within two weeks of check-out.', '2026-08-27 12:47:03', '2026-10-01 15:12:47', 'Teresita Mendoza', 'Owner', 'Pureza Station Dormitory', 'https://www.facebook.com/pureza.dom', 'https://thenestphils.purezastationdormitory.com', 1, 3, 10.00, 3, 14, 30, 21, 30, 'full', 1, 1, 1, 4500.00, 300.00, '2', '1', '2026-09-30');

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
(2, 6, 33, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-23 22:00:00', '2026-09-23 22:00:00'),
(3, 6, 33, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-25 22:00:00', '2026-09-25 22:00:00'),
(4, 6, 33, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-28 22:00:00', '2026-09-28 22:00:00'),
(5, 6, 33, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST PH', 'sent', NULL, '2026-09-29 22:00:00', '2026-09-29 22:00:00'),
(6, 6, 33, 4, 'emergency_contact_notified', 'Skipped: the emergency contact has not agreed to receive billing reminders (Tenant Agreement Section 9.3).', 'resolved', NULL, '2026-09-30 22:00:00', '2026-09-30 22:00:00'),
(7, 9, 47, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-27 22:00:00', '2026-09-27 22:00:00'),
(8, 9, 47, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,950.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-29 22:00:00', '2026-09-29 22:00:00'),
(9, 13, 73, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-24 22:00:00', '2026-09-24 22:00:00'),
(10, 13, 73, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,400.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-26 22:00:00', '2026-09-26 22:00:00'),
(11, 13, 73, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,400.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-09-28 22:00:00', '2026-09-28 22:00:00'),
(12, 13, NULL, 2, 'admin_override_pause', 'Paused by admin: tenant agreed to a payment plan (half on the 15th, half on the 30th).', 'resolved', 355, '2026-09-30 06:20:00', '2026-09-30 06:20:00'),
(13, 24, 122, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-08-16 22:00:00', '2026-08-16 22:00:00'),
(14, 24, 122, 2, 'sms_reminder_day2', 'Reminder: Your account with NEST PH is now 2 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-18 22:00:00', '2026-08-18 22:00:00'),
(15, 24, 122, 2, 'sms_reminder_day4', 'Reminder: Your account with NEST PH is now 4 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-20 22:00:00', '2026-08-20 22:00:00'),
(16, 24, 122, 2, 'sms_reminder_day7', 'URGENT: Your account with NEST PH is now 7 day(s) overdue. Outstanding balance (incl. penalties): PHP 4,180.00. Please pay via the tenant portal to avoid further account restrictions.', 'sent', NULL, '2026-08-23 22:00:00', '2026-08-23 22:00:00'),
(17, 24, 122, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST PH', 'sent', NULL, '2026-08-24 22:00:00', '2026-08-24 22:00:00'),
(18, 24, 122, 4, 'emergency_contact_notified', 'Skipped: the emergency contact has not agreed to receive billing reminders (Tenant Agreement Section 9.3).', 'resolved', NULL, '2026-08-25 22:00:00', '2026-08-25 22:00:00'),
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
(3, 'Ground Floor', 0.00, 0.00, 1, 'Lobby, receiving area and study hall. Solo fan rooms and 4-person AC rooms.', '2026-07-29 22:22:25', '2026-10-01 15:12:48'),
(11, 'Second Floor', 0.00, 0.00, 2, '4-person and 6-person air-conditioned rooms.', '2026-09-04 13:04:53', '2026-10-01 15:12:48'),
(12, 'Third Floor', 0.00, 0.00, 3, 'Air-conditioned rooms, including a 12-bed dorm room.', '2026-09-29 13:00:54', '2026-10-01 15:12:48'),
(13, 'Fourth Floor', 0.00, 0.00, 4, '6-person room and a 16-bed dorm room.', '2026-10-01 15:12:48', '2026-10-01 15:12:48'),
(14, 'Fifth Floor', 0.00, 0.00, 5, 'Large dorm room and a 4-person room with a view of the LRT line.', '2026-10-01 15:12:48', '2026-10-01 15:12:48');

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
(1, 'Aira Nicole Dimaculangan', '09174402167', 'aira.dimaculangan@gmail.com', 89, 'Hello po! May available pa po bang bedspace for female this November? Magkano po ang 6-person AC room?', NULL, NULL, NULL, 'Room with AC, 6 persons', 1, 'new', '2026-10-01 12:14:43', '2026-10-01 12:14:43'),
(2, 'Rhenz Adrian Pacheco', '09184410086', 'rhenz.pacheco@gmail.com', 84, 'Good day! Pwede po ba mag-ocular visit this Saturday? Working po ako sa Makati, looking for a solo room.', NULL, NULL, NULL, 'Solo fan room', 1, 'new', '2026-09-30 03:12:00', '2026-09-30 03:12:00'),
(3, 'Janella Marie Quiambao', '09274418005', 'janella.quiambao@gmail.com', NULL, 'Kasama na po ba ang WiFi at kuryente sa monthly rate?', 'Hi Janella! Opo, kasama na po ang tubig, kuryente at WiFi sa monthly rate. ₱4,200 per bed po ang 4-person AC room sa 2nd to 5th floor. Welcome po kayong mag-visit!', '2026-09-28 10:12:00', 355, 'Room with AC, 4 persons', 1, 'contacted', '2026-09-28 05:12:00', '2026-09-28 10:12:00'),
(4, 'Luis Gabriel Soriano', '09394425924', 'luis.soriano@gmail.com', 91, 'Is there a curfew? I have night classes until 9 PM.', 'Hello Luis! Curfew is 11 PM to 4 AM, so 9 PM classes are no problem. If you ever need to come home later, just inform the office in advance. Feel free to apply online through our website.', '2026-09-26 12:12:00', 355, 'Room with AC, 10–16 persons', 1, 'contacted', '2026-09-26 07:12:00', '2026-09-26 12:12:00'),
(5, 'Ryan Christopher Santiago', '09454433843', 'ryan.santiago@gmail.com', 87, 'Interested po ako sa Room 203, pwede po ba mag-apply online?', 'Yes po! Na-send na namin ang link. Paki-fill up lang po ang application form at pirmahan ang documents online.', '2026-09-24 14:12:00', 355, 'Room with AC, 4 persons', 1, 'converted', '2026-09-24 09:12:00', '2026-09-24 14:12:00'),
(6, 'Mylene Castillo', '09564441762', 'mylene.castillo@gmail.com', NULL, 'Pwede po ba ang pets? May maliit po akong pusa.', 'Sorry po, bawal po ang pets sa dormitory (Rules and Regulations, item 11). Salamat sa interest!', '2026-09-19 16:12:00', 355, NULL, 1, 'closed', '2026-09-19 11:12:00', '2026-09-19 16:12:00');

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
(1, 1, 1, 1, NULL, '2026-04-11', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-maria-angelica-santos.pdf', '2026-04-01 02:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-02 02:15:00', '2026-10-01 15:12:49'),
(2, 2, 2, 2, NULL, '2026-06-03', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-kimberly-anne-dela-cruz.pdf', '2026-05-23 03:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-24 03:15:00', '2026-10-01 15:12:50'),
(3, 3, 3, 3, NULL, '2026-07-05', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-patricia-mae-gonzales.pdf', '2026-06-23 04:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-24 04:15:00', '2026-10-01 15:12:51'),
(4, 4, 4, 38, NULL, '2026-07-13', '2026-10-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-nicole-joy-ramos.pdf', '2026-06-30 05:15:00', 'expiring_soon', NULL, NULL, NULL, NULL, 355, 355, '2026-07-01 05:15:00', '2026-10-01 15:12:52'),
(5, 5, 5, 5, NULL, '2026-03-16', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-juan-miguel-reyes.pdf', '2026-03-07 06:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-08 06:15:00', '2026-10-01 15:12:53'),
(6, 6, 6, 48, NULL, '2026-05-15', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-john-paul-mendoza.pdf', '2026-05-05 07:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-06 07:15:00', '2026-10-01 15:12:55'),
(7, 7, 7, 9, NULL, '2026-08-06', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-mark-joseph-aquino.pdf', '2026-07-26 08:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-27 08:15:00', '2026-10-01 15:12:56'),
(8, 8, 8, 10, NULL, '2026-05-04', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-christian-dave-torres.pdf', '2026-04-22 01:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-23 01:15:00', '2026-10-01 15:12:57'),
(9, 9, 9, 11, NULL, '2026-05-09', '2027-02-28', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-benjamin-robles.pdf', '2026-04-26 02:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-27 02:15:00', '2026-10-01 15:12:58'),
(10, 10, 10, 57, NULL, '2026-04-13', '2026-09-30', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-rafael-luis-navarro.pdf', '2026-04-04 03:15:00', 'expired', NULL, NULL, NULL, NULL, 355, 355, '2026-04-05 03:15:00', '2026-10-01 15:12:59'),
(11, 11, 11, 15, NULL, '2026-01-21', '2027-03-31', 3800.00, 200.00, 'signed', 'application-documents/signed-contracts/demo-angela-marie-villanueva.pdf', '2026-01-11 04:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-01-12 04:15:00', '2026-10-01 15:13:00'),
(12, 12, 12, 16, NULL, '2026-07-02', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-jasmine-rose-garcia.pdf', '2026-06-21 05:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-22 05:15:00', '2026-10-01 15:13:01'),
(13, 13, 13, 17, NULL, '2026-05-12', '2027-02-28', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-camille-louise-flores.pdf', '2026-04-30 06:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-01 06:15:00', '2026-10-01 15:13:02'),
(14, 14, 14, 18, NULL, '2026-06-01', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-bea-katrina-pascual.pdf', '2026-05-19 07:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-20 07:15:00', '2026-10-01 15:13:03'),
(15, 15, 15, 19, NULL, '2026-06-08', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-princess-joy-manalo.pdf', '2026-05-30 08:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-31 08:15:00', '2026-10-01 15:13:04'),
(16, 16, 16, 20, NULL, '2026-10-05', '2027-04-30', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-kathleen-mae-salazar.pdf', '2026-09-25 01:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-09-26 01:15:00', '2026-10-01 15:13:05'),
(17, 17, 17, 21, NULL, '2026-07-10', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-carlo-miguel-bautista.pdf', '2026-06-29 02:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-30 02:15:00', '2026-10-01 15:13:06'),
(18, 18, 18, 25, NULL, '2026-05-19', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-joshua-emmanuel-lim.pdf', '2026-05-07 03:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-08 03:15:00', '2026-10-01 15:13:07'),
(19, 19, 19, 26, NULL, '2026-10-05', '2027-04-30', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-paolo-andres-ocampo.pdf', '2026-09-22 04:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-09-23 04:15:00', '2026-10-01 15:13:10'),
(20, 20, 20, 29, NULL, '2026-04-07', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-andrea-nicole-tan.pdf', '2026-03-29 05:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-30 05:15:00', '2026-10-01 15:13:12'),
(21, 21, 21, 35, NULL, '2026-06-14', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-erika-jane-morales.pdf', '2026-06-04 06:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-05 06:15:00', '2026-10-01 15:13:14'),
(22, 22, 22, 36, NULL, '2026-08-05', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-hannah-grace-soriano.pdf', '2026-07-25 07:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-26 07:15:00', '2026-10-01 15:13:17'),
(23, 23, 23, 41, NULL, '2026-02-17', '2026-07-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-05 08:15:00', 'terminated', 'Moved out at the end of contract -- transferred to a job in Laguna.', '2026-08-01 02:00:00', NULL, NULL, 355, 355, '2026-02-06 08:15:00', '2026-10-01 15:13:17'),
(24, 24, 24, 45, NULL, '2026-03-24', '2027-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-11 01:15:00', 'terminated', 'Terminated for non-payment after full delinquency escalation (Stage 6).', '2026-08-31 16:00:00', NULL, NULL, 355, 355, '2026-03-12 01:15:00', '2026-10-01 15:13:17'),
(25, 25, 25, 4, NULL, '2026-02-17', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-stephanie-claire-uy.pdf', '2026-02-08 02:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-02-09 02:15:00', '2026-10-01 15:13:19'),
(26, 26, 26, 6, NULL, '2026-06-12', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-adrian-paul-castro.pdf', '2026-06-02 03:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-03 03:15:00', '2026-10-01 15:13:22'),
(27, 27, 27, 12, NULL, '2026-07-10', '2027-03-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-vincent-ray-magbanua.pdf', '2026-06-29 04:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-30 04:15:00', '2026-10-01 15:13:24'),
(28, 28, 28, 41, NULL, '2026-09-22', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-luis-antonio-del-rosario.pdf', '2026-09-10 05:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-09-11 05:15:00', '2026-10-01 15:13:26'),
(29, 29, 29, 45, NULL, '2026-09-15', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-jerome-anthony-pineda.pdf', '2026-09-02 06:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-09-03 06:15:00', '2026-10-01 15:13:27'),
(30, 30, 30, 49, NULL, '2026-09-03', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-kenneth-bryan-sy.pdf', '2026-08-25 07:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-08-26 07:15:00', '2026-10-01 15:13:29'),
(31, 31, 31, 50, NULL, '2026-05-25', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-emmanuel-jose-villareal.pdf', '2026-05-15 08:15:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-16 08:15:00', '2026-10-01 15:13:31'),
(32, 32, 32, 7, NULL, '2026-07-23', '2027-02-28', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-pauline-delos-reyes.pdf', '2026-07-16 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-17 06:40:00', '2026-10-01 15:13:33'),
(33, 33, 33, 8, NULL, '2026-06-27', '2027-05-31', 4500.00, NULL, 'signed', 'application-documents/signed-contracts/demo-froilan-natividad.pdf', '2026-06-19 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-20 07:40:00', '2026-10-01 15:13:35'),
(34, 34, 34, 23, NULL, '2026-07-04', '2026-11-30', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-krizia-natividad.pdf', '2026-06-25 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-26 08:40:00', '2026-10-01 15:13:36'),
(35, 35, 35, 24, NULL, '2026-07-10', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-jamaica-evangelista.pdf', '2026-06-30 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-01 01:40:00', '2026-10-01 15:13:38'),
(36, 36, 36, 30, NULL, '2026-01-07', '2027-01-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-patrick-espiritu.pdf', '2025-12-27 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-28 02:40:00', '2026-10-01 15:13:40'),
(37, 37, 37, 31, NULL, '2026-02-07', '2027-05-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-lester-ventura.pdf', '2026-01-26 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-01-27 03:40:00', '2026-10-01 15:13:42'),
(38, 38, 38, 32, NULL, '2026-09-11', '2027-04-30', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-tristan-dimaculangan.pdf', '2026-08-29 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-08-30 04:40:00', '2026-10-01 15:13:43'),
(39, 39, 39, 39, NULL, '2026-02-19', '2026-12-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-aaron-hidalgo.pdf', '2026-02-05 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-02-06 05:40:00', '2026-10-01 15:13:45'),
(40, 40, 40, 40, NULL, '2026-09-20', '2027-01-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-carl-lagman.pdf', '2026-09-05 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-09-06 06:40:00', '2026-10-01 15:13:46'),
(41, 41, 41, 43, NULL, '2026-08-02', '2026-12-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-gerald-delos-reyes.pdf', '2026-07-27 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-28 07:40:00', '2026-10-01 15:13:47'),
(42, 42, 42, 44, NULL, '2026-03-10', '2027-02-28', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-darwin-zaragoza.pdf', '2026-03-03 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-04 08:40:00', '2026-10-01 15:13:48'),
(43, 43, 43, 46, NULL, '2025-12-15', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-darwin-natividad.pdf', '2025-12-07 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-08 01:40:00', '2026-10-01 15:13:49'),
(44, 44, 44, 47, NULL, '2026-07-24', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-jamaica-cabrera.pdf', '2026-07-15 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-16 02:40:00', '2026-10-01 15:13:50'),
(45, 45, 45, 51, NULL, '2026-09-04', '2027-05-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-odessa-quinto.pdf', '2026-08-25 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-08-26 03:40:00', '2026-10-01 15:13:52'),
(46, 46, 46, 52, NULL, '2025-11-14', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-elaine-aguilar.pdf', '2025-11-03 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-11-04 04:40:00', '2026-10-01 15:13:53'),
(47, 47, 47, 53, NULL, '2026-04-09', '2027-05-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-aaron-romualdez.pdf', '2026-03-28 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-29 05:40:00', '2026-10-01 15:13:54'),
(48, 48, 48, 54, NULL, '2026-01-06', '2026-11-30', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-sean-dimaculangan.pdf', '2025-12-24 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-25 06:40:00', '2026-10-01 15:13:55'),
(49, 49, 49, 58, NULL, '2026-06-10', '2027-04-30', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-bernadette-romualdez.pdf', '2026-05-27 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-28 07:40:00', '2026-10-01 15:13:56'),
(50, 50, 50, 59, NULL, '2026-07-03', '2026-12-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-lester-romualdez.pdf', '2026-06-18 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-19 08:40:00', '2026-10-01 15:13:57'),
(51, 51, 51, 60, NULL, '2025-11-08', '2027-02-28', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-rhea-yambao.pdf', '2025-11-02 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-11-03 01:40:00', '2026-10-01 15:13:58'),
(52, 52, 52, 61, NULL, '2026-08-01', '2027-04-30', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-jhon-alcantara.pdf', '2026-07-25 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-26 02:40:00', '2026-10-01 15:13:59'),
(53, 53, 53, 62, NULL, '2026-03-13', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-carl-dimaculangan.pdf', '2026-03-05 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-06 03:40:00', '2026-10-01 15:14:00'),
(54, 54, 54, 63, NULL, '2026-03-25', '2026-12-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-gela-aguilar.pdf', '2026-03-16 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-17 04:40:00', '2026-10-01 15:14:01'),
(55, 55, 55, 64, NULL, '2026-01-23', '2027-04-30', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-isabelle-ventura.pdf', '2026-01-13 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-01-14 05:40:00', '2026-10-01 15:14:03'),
(56, 56, 56, 65, NULL, '2025-11-10', '2027-04-30', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-froilan-padilla.pdf', '2025-10-30 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-10-31 06:40:00', '2026-10-01 15:14:04'),
(57, 57, 57, 66, NULL, '2026-01-15', '2027-03-31', 4000.00, NULL, 'signed', 'application-documents/signed-contracts/demo-charmaine-mangubat.pdf', '2026-01-03 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-01-04 07:40:00', '2026-10-01 15:14:05'),
(58, 58, 58, 67, NULL, '2026-08-02', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-faith-quinto.pdf', '2026-07-20 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-21 08:40:00', '2026-10-01 15:14:06'),
(59, 59, 59, 68, NULL, '2026-06-01', '2027-05-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-charmaine-bernardo.pdf', '2026-05-18 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-19 01:40:00', '2026-10-01 15:14:07'),
(60, 60, 60, 69, NULL, '2026-07-25', '2027-05-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-kurt-delos-reyes.pdf', '2026-07-10 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-11 02:40:00', '2026-10-01 15:14:08'),
(61, 61, 61, 70, NULL, '2026-02-10', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-owen-lagman.pdf', '2026-02-04 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-02-05 03:40:00', '2026-10-01 15:14:09'),
(62, 62, 62, 71, NULL, '2025-12-20', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-bernadette-yambao.pdf', '2025-12-13 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-14 04:40:00', '2026-10-01 15:14:10'),
(63, 63, 63, 72, NULL, '2026-04-05', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-noel-cabrera.pdf', '2026-03-28 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-29 05:40:00', '2026-10-01 15:14:12'),
(64, 64, 64, 73, NULL, '2026-09-01', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-lovely-zaragoza.pdf', '2026-08-23 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-08-24 06:40:00', '2026-10-01 15:14:13'),
(65, 65, 65, 74, NULL, '2026-04-18', '2027-04-30', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-maureen-jimenez.pdf', '2026-04-08 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-09 07:40:00', '2026-10-01 15:14:14'),
(66, 66, 66, 75, NULL, '2026-06-06', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-krizia-ventura.pdf', '2026-05-26 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-27 08:40:00', '2026-10-01 15:14:15'),
(67, 67, 67, 76, NULL, '2025-12-22', '2027-05-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-darwin-sarmiento.pdf', '2025-12-10 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-11 01:40:00', '2026-10-01 15:14:16'),
(68, 68, 68, 77, NULL, '2025-12-25', '2026-12-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-earl-bonifacio.pdf', '2025-12-12 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-13 02:40:00', '2026-10-01 15:14:17'),
(69, 69, 69, 78, NULL, '2025-12-05', '2027-02-28', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-gela-bonifacio.pdf', '2025-11-21 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-11-22 03:40:00', '2026-10-01 15:14:18'),
(70, 70, 70, 83, NULL, '2026-06-01', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-althea-dimaculangan.pdf', '2026-05-17 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-18 04:40:00', '2026-10-01 15:14:19'),
(71, 71, 71, 84, NULL, '2026-03-20', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-isabelle-alcantara.pdf', '2026-03-14 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-03-15 05:40:00', '2026-10-01 15:14:21'),
(72, 72, 72, 85, NULL, '2026-05-16', '2027-04-30', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-carl-padilla.pdf', '2026-05-09 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-05-10 06:40:00', '2026-10-01 15:14:22'),
(73, 73, 73, 86, NULL, '2025-12-22', '2026-12-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-darwin-evangelista.pdf', '2025-12-14 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-15 07:40:00', '2026-10-01 15:14:23'),
(74, 74, 74, 87, NULL, '2026-08-03', '2026-12-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-ian-sarmiento.pdf', '2026-07-25 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-26 08:40:00', '2026-10-01 15:14:24'),
(75, 75, 75, 88, NULL, '2026-01-19', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-patrick-delos-reyes.pdf', '2026-01-09 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-01-10 01:40:00', '2026-10-01 15:14:25'),
(76, 76, 76, 89, NULL, '2026-01-08', '2026-11-30', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-wendell-catapang.pdf', '2025-12-28 02:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-29 02:40:00', '2026-10-01 15:14:26'),
(77, 77, 77, 90, NULL, '2026-06-16', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-faith-sarmiento.pdf', '2026-06-04 03:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-06-05 03:40:00', '2026-10-01 15:14:27'),
(78, 78, 78, 91, NULL, '2026-05-08', '2027-03-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-lovely-evangelista.pdf', '2026-04-25 04:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-26 04:40:00', '2026-10-01 15:14:28'),
(79, 79, 79, 92, NULL, '2026-05-11', '2027-01-31', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-harvey-cabrera.pdf', '2026-04-27 05:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-04-28 05:40:00', '2026-10-01 15:14:29'),
(80, 80, 80, 93, NULL, '2025-11-14', '2027-04-30', 3800.00, NULL, 'signed', 'application-documents/signed-contracts/demo-tristan-romualdez.pdf', '2025-10-30 06:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-10-31 06:40:00', '2026-10-01 15:14:31'),
(81, 81, 81, 97, NULL, '2025-12-23', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-carl-espiritu.pdf', '2025-12-17 07:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2025-12-18 07:40:00', '2026-10-01 15:14:32'),
(82, 82, 82, 98, NULL, '2026-08-04', '2027-03-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-bryle-mangubat.pdf', '2026-07-28 08:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-07-29 08:40:00', '2026-10-01 15:14:33'),
(83, 83, 83, 99, NULL, '2026-08-11', '2026-12-31', 4200.00, NULL, 'signed', 'application-documents/signed-contracts/demo-tristan-alcantara.pdf', '2026-08-03 01:40:00', 'active', NULL, NULL, NULL, NULL, 355, 355, '2026-08-04 01:40:00', '2026-10-01 15:14:34'),
(84, 84, 84, 1, NULL, '2025-09-23', '2026-03-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-09 02:30:00', 'terminated', 'Found a job in another city.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-10 02:30:00', '2026-10-01 15:14:34'),
(85, 85, 85, 2, NULL, '2025-10-15', '2026-02-28', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-30 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-10-01 03:30:00', '2026-10-01 15:14:34'),
(86, 86, 86, 3, NULL, '2025-09-04', '2025-12-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-26 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-08-27 04:30:00', '2026-10-01 15:14:34'),
(87, 87, 87, 3, NULL, '2026-02-02', '2026-05-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-23 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2026-01-24 05:30:00', '2026-10-01 15:14:34'),
(88, 88, 88, 4, NULL, '2025-09-05', '2026-01-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-25 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-26 06:30:00', '2026-10-01 15:14:34'),
(89, 89, 89, 5, NULL, '2025-09-12', '2025-12-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-31 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-01 07:30:00', '2026-10-01 15:14:34'),
(90, 90, 90, 6, NULL, '2025-09-12', '2026-02-28', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-30 08:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-31 08:30:00', '2026-10-01 15:14:34'),
(91, 91, 91, 7, NULL, '2025-09-11', '2026-02-28', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-28 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-29 01:30:00', '2026-10-01 15:14:34'),
(92, 92, 92, 8, NULL, '2025-10-16', '2026-05-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-01 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-10-02 02:30:00', '2026-10-01 15:14:34'),
(93, 93, 93, 9, NULL, '2025-10-10', '2026-06-30', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-01 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2025-10-02 03:30:00', '2026-10-01 15:14:34'),
(94, 94, 94, 10, NULL, '2025-09-30', '2025-12-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-20 04:30:00', 'terminated', 'Moved out at the end of contract.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-21 04:30:00', '2026-10-01 15:14:34'),
(95, 95, 95, 11, NULL, '2025-09-23', '2026-04-30', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-12 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-13 05:30:00', '2026-10-01 15:14:34'),
(96, 96, 96, 12, NULL, '2025-09-21', '2026-02-28', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-09 06:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-10 06:30:00', '2026-10-01 15:14:34'),
(97, 97, 97, 14, NULL, '2025-10-10', '2026-01-31', 4500.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-27 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-28 07:30:00', '2026-10-01 15:14:34'),
(98, 98, 98, 15, NULL, '2025-09-17', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-03 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-04 08:30:00', '2026-10-01 15:14:34'),
(99, 99, 99, 16, NULL, '2025-09-21', '2026-01-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-06 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-07 01:30:00', '2026-10-01 15:14:34'),
(100, 100, 100, 16, NULL, '2026-02-12', '2026-05-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-03 02:30:00', 'terminated', 'Moved out at the end of contract.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2026-02-04 02:30:00', '2026-10-01 15:14:34'),
(101, 101, 101, 17, NULL, '2025-09-13', '2026-04-30', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-03 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-04 03:30:00', '2026-10-01 15:14:34'),
(102, 102, 102, 18, NULL, '2025-09-12', '2026-02-28', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 04:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-02 04:30:00', '2026-10-01 15:14:34'),
(103, 103, 103, 19, NULL, '2025-09-09', '2026-04-30', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-28 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-08-29 05:30:00', '2026-10-01 15:14:34'),
(104, 104, 104, 20, NULL, '2025-09-04', '2026-01-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-22 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-23 06:30:00', '2026-10-01 15:14:34'),
(105, 105, 105, 20, NULL, '2026-03-09', '2026-06-30', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-23 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2026-02-24 07:30:00', '2026-10-01 15:14:34'),
(106, 106, 106, 21, NULL, '2025-09-16', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 08:30:00', 'terminated', 'Moved out at the end of contract.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-02 08:30:00', '2026-10-01 15:14:35'),
(107, 107, 107, 22, NULL, '2025-09-20', '2026-01-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-11 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-12 01:30:00', '2026-10-01 15:14:35'),
(108, 108, 108, 23, NULL, '2025-10-02', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-22 02:30:00', 'terminated', 'Found a job in another city.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-23 02:30:00', '2026-10-01 15:14:35'),
(109, 109, 109, 24, NULL, '2025-09-06', '2025-12-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-26 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-08-27 03:30:00', '2026-10-01 15:14:35'),
(110, 110, 110, 24, NULL, '2026-01-17', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-05 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2026-01-06 04:30:00', '2026-10-01 15:14:35'),
(111, 111, 111, 25, NULL, '2025-10-15', '2026-04-30', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-02 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-10-03 05:30:00', '2026-10-01 15:14:35'),
(112, 112, 112, 26, NULL, '2025-09-06', '2026-02-28', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-23 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-24 06:30:00', '2026-10-01 15:14:35'),
(113, 113, 113, 26, NULL, '2026-04-03', '2026-08-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-19 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-08-31 02:00:00', NULL, NULL, 355, 355, '2026-03-20 07:30:00', '2026-10-01 15:14:35'),
(114, 114, 114, 27, NULL, '2025-09-14', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-05 08:30:00', 'terminated', 'Found a job in another city.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-06 08:30:00', '2026-10-01 15:14:35'),
(115, 115, 115, 29, NULL, '2025-09-11', '2026-03-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-02 01:30:00', '2026-10-01 15:14:35'),
(116, 116, 116, 31, NULL, '2025-09-25', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-14 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-15 02:30:00', '2026-10-01 15:14:35'),
(117, 117, 117, 32, NULL, '2025-09-10', '2026-02-28', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-29 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-30 03:30:00', '2026-10-01 15:14:35'),
(118, 118, 118, 32, NULL, '2026-03-15', '2026-08-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-02 04:30:00', 'terminated', 'Moved out at the end of contract.', '2026-08-31 02:00:00', NULL, NULL, 355, 355, '2026-03-03 04:30:00', '2026-10-01 15:14:35'),
(119, 119, 119, 33, NULL, '2025-09-12', '2026-03-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-29 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-08-30 05:30:00', '2026-10-01 15:14:35'),
(120, 120, 120, 34, NULL, '2025-09-30', '2026-05-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-15 06:30:00', 'terminated', 'Found a job in another city.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(121, 121, 121, 35, NULL, '2025-10-16', '2026-01-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-07 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-10-08 07:30:00', '2026-10-01 15:14:35'),
(122, 122, 122, 35, NULL, '2026-02-18', '2026-05-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-08 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2026-02-09 08:30:00', '2026-10-01 15:14:35'),
(123, 123, 123, 36, NULL, '2025-09-14', '2026-05-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-03 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-04 01:30:00', '2026-10-01 15:14:35'),
(124, 124, 124, 37, NULL, '2025-09-25', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-13 02:30:00', 'terminated', 'Moved out at the end of contract.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-14 02:30:00', '2026-10-01 15:14:35'),
(125, 125, 125, 38, NULL, '2025-09-23', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-10 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-11 03:30:00', '2026-10-01 15:14:35'),
(126, 126, 126, 38, NULL, '2026-01-14', '2026-06-30', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-31 04:30:00', 'terminated', 'Found a job in another city.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2026-01-01 04:30:00', '2026-10-01 15:14:35'),
(127, 127, 127, 39, NULL, '2025-10-09', '2026-01-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-24 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-25 05:30:00', '2026-10-01 15:14:35'),
(128, 128, 128, 40, NULL, '2025-09-21', '2026-05-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-12 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-13 06:30:00', '2026-10-01 15:14:35'),
(129, 129, 129, 41, NULL, '2025-10-13', '2026-01-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-03 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-10-04 07:30:00', '2026-10-01 15:14:35'),
(130, 130, 130, 42, NULL, '2025-09-04', '2026-01-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-24 08:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-25 08:30:00', '2026-10-01 15:14:35'),
(131, 131, 131, 43, NULL, '2025-09-10', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-29 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-08-30 01:30:00', '2026-10-01 15:14:35'),
(132, 132, 132, 44, NULL, '2025-09-30', '2026-02-28', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-17 02:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-18 02:30:00', '2026-10-01 15:14:35'),
(133, 133, 133, 45, NULL, '2025-09-27', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-13 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-14 03:30:00', '2026-10-01 15:14:35'),
(134, 134, 134, 47, NULL, '2025-10-07', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-22 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-23 04:30:00', '2026-10-01 15:14:35'),
(135, 135, 135, 48, NULL, '2025-09-10', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-02 05:30:00', '2026-10-01 15:14:35'),
(136, 136, 136, 49, NULL, '2025-09-25', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-15 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(137, 137, 137, 49, NULL, '2026-03-14', '2026-07-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-03-03 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-07-31 02:00:00', NULL, NULL, 355, 355, '2026-03-04 07:30:00', '2026-10-01 15:14:35'),
(138, 138, 138, 50, NULL, '2025-10-13', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-01 08:30:00', 'terminated', 'Found a job in another city.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-10-02 08:30:00', '2026-10-01 15:14:35'),
(139, 139, 139, 51, NULL, '2025-09-21', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-08 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-09 01:30:00', '2026-10-01 15:14:35'),
(140, 140, 140, 53, NULL, '2025-09-24', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-10 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-11 02:30:00', '2026-10-01 15:14:35'),
(141, 141, 141, 55, NULL, '2025-09-30', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-15 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-16 03:30:00', '2026-10-01 15:14:35'),
(142, 142, 142, 56, NULL, '2025-09-10', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-01 04:30:00', 'terminated', 'Moved out at the end of contract.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-02 04:30:00', '2026-10-01 15:14:35'),
(143, 143, 143, 56, NULL, '2026-02-09', '2026-08-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-30 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-08-31 02:00:00', NULL, NULL, 355, 355, '2026-01-31 05:30:00', '2026-10-01 15:14:35'),
(144, 144, 144, 57, NULL, '2025-09-01', '2025-11-30', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-21 06:30:00', 'terminated', 'Found a job in another city.', '2025-11-30 02:00:00', NULL, NULL, 355, 355, '2025-08-22 06:30:00', '2026-10-01 15:14:35'),
(145, 145, 145, 57, NULL, '2025-12-24', '2026-03-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-12 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-12-13 07:30:00', '2026-10-01 15:14:35'),
(146, 146, 146, 58, NULL, '2025-10-06', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-23 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-24 08:30:00', '2026-10-01 15:14:35'),
(147, 147, 147, 59, NULL, '2025-09-06', '2025-12-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-23 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-08-24 01:30:00', '2026-10-01 15:14:35'),
(148, 148, 148, 59, NULL, '2026-02-02', '2026-05-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-18 02:30:00', 'terminated', 'Moved out at the end of contract.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2026-01-19 02:30:00', '2026-10-01 15:14:35'),
(149, 149, 149, 61, NULL, '2025-09-12', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-03 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-04 03:30:00', '2026-10-01 15:14:35'),
(150, 150, 150, 61, NULL, '2026-02-02', '2026-06-30', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-23 04:30:00', 'terminated', 'Found a job in another city.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2026-01-24 04:30:00', '2026-10-01 15:14:35'),
(151, 151, 151, 62, NULL, '2025-09-27', '2026-02-28', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-16 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-17 05:30:00', '2026-10-01 15:14:35'),
(152, 152, 152, 63, NULL, '2025-09-27', '2026-02-28', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-15 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(153, 153, 153, 64, NULL, '2025-09-19', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-06 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-07 07:30:00', '2026-10-01 15:14:35'),
(154, 154, 154, 66, NULL, '2025-09-16', '2025-12-31', 4000.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-02 08:30:00', 'terminated', 'Moved out at the end of contract.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-03 08:30:00', '2026-10-01 15:14:35'),
(155, 155, 155, 67, NULL, '2025-09-14', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-30 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-08-31 01:30:00', '2026-10-01 15:14:35'),
(156, 156, 156, 67, NULL, '2026-01-07', '2026-06-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-29 02:30:00', 'terminated', 'Found a job in another city.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2025-12-30 02:30:00', '2026-10-01 15:14:35'),
(157, 157, 157, 68, NULL, '2025-09-15', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-05 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-06 03:30:00', '2026-10-01 15:14:35'),
(158, 158, 158, 69, NULL, '2025-09-30', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-19 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-20 04:30:00', '2026-10-01 15:14:36'),
(159, 159, 159, 69, NULL, '2026-01-15', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-01-03 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2026-01-04 05:30:00', '2026-10-01 15:14:36'),
(160, 160, 160, 70, NULL, '2025-09-12', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-30 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-31 06:30:00', '2026-10-01 15:14:36'),
(161, 161, 161, 72, NULL, '2025-09-21', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-07 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-09-08 07:30:00', '2026-10-01 15:14:36'),
(162, 162, 162, 73, NULL, '2025-09-04', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-20 08:30:00', 'terminated', 'Found a job in another city.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-21 08:30:00', '2026-10-01 15:14:36'),
(163, 163, 163, 73, NULL, '2026-02-25', '2026-06-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-16 01:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2026-02-17 01:30:00', '2026-10-01 15:14:36'),
(164, 164, 164, 74, NULL, '2025-10-08', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-28 02:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-29 02:30:00', '2026-10-01 15:14:36'),
(165, 165, 165, 75, NULL, '2025-09-02', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-22 03:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-23 03:30:00', '2026-10-01 15:14:36'),
(166, 166, 166, 79, NULL, '2025-09-19', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-07 04:30:00', 'terminated', 'Moved out at the end of contract.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-08 04:30:00', '2026-10-01 15:14:36'),
(167, 167, 167, 80, NULL, '2025-09-15', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-02 05:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-03 05:30:00', '2026-10-01 15:14:36'),
(168, 168, 168, 81, NULL, '2025-09-03', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-20 06:30:00', 'terminated', 'Found a job in another city.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-21 06:30:00', '2026-10-01 15:14:36'),
(169, 169, 169, 82, NULL, '2025-09-11', '2026-01-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-27 07:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-08-28 07:30:00', '2026-10-01 15:14:36'),
(170, 170, 170, 83, NULL, '2025-09-06', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-28 08:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-08-29 08:30:00', '2026-10-01 15:14:36'),
(171, 171, 171, 84, NULL, '2025-10-03', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-23 01:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-24 01:30:00', '2026-10-01 15:14:36'),
(172, 172, 172, 85, NULL, '2025-10-06', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-25 02:30:00', 'terminated', 'Moved out at the end of contract.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-26 02:30:00', '2026-10-01 15:14:36'),
(173, 173, 173, 87, NULL, '2025-09-20', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-08 03:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-09-09 03:30:00', '2026-10-01 15:14:36'),
(174, 174, 174, 87, NULL, '2026-01-06', '2026-06-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-12-24 04:30:00', 'terminated', 'Found a job in another city.', '2026-06-30 02:00:00', NULL, NULL, 355, 355, '2025-12-25 04:30:00', '2026-10-01 15:14:36'),
(175, 175, 175, 89, NULL, '2025-09-09', '2025-12-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-26 05:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', NULL, NULL, 355, 355, '2025-08-27 05:30:00', '2026-10-01 15:14:36'),
(176, 176, 176, 90, NULL, '2025-09-25', '2026-04-30', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-10 06:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-09-11 06:30:00', '2026-10-01 15:14:36'),
(177, 177, 177, 91, NULL, '2025-09-26', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-17 07:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-18 07:30:00', '2026-10-01 15:14:36'),
(178, 178, 178, 92, NULL, '2025-09-29', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-19 08:30:00', 'terminated', 'Moved out at the end of contract.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-09-20 08:30:00', '2026-10-01 15:14:36'),
(179, 179, 179, 94, NULL, '2025-09-07', '2026-03-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-27 01:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', NULL, NULL, 355, 355, '2025-08-28 01:30:00', '2026-10-01 15:14:36'),
(180, 180, 180, 95, NULL, '2025-09-26', '2026-05-31', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-14 02:30:00', 'terminated', 'Found a job in another city.', '2026-05-31 02:00:00', NULL, NULL, 355, 355, '2025-09-15 02:30:00', '2026-10-01 15:14:36'),
(181, 181, 181, 96, NULL, '2025-09-07', '2026-02-28', 3800.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-25 03:30:00', 'terminated', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-08-26 03:30:00', '2026-10-01 15:14:36'),
(182, 182, 182, 98, NULL, '2025-09-11', '2026-04-30', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-08-28 04:30:00', 'terminated', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', NULL, NULL, 355, 355, '2025-08-29 04:30:00', '2026-10-01 15:14:36');
INSERT INTO `lease_contracts` (`id`, `application_id`, `tenant_id`, `bed_id`, `inquiry_id`, `start_date`, `end_date`, `monthly_rate`, `discount_amount`, `esign_status`, `signed_document_url`, `signed_at`, `status`, `termination_reason`, `terminated_at`, `last_renewed_at`, `last_renewed_by`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(183, 183, 183, 99, NULL, '2025-10-16', '2026-01-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-10-01 05:30:00', 'terminated', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', NULL, NULL, 355, 355, '2025-10-02 05:30:00', '2026-10-01 15:14:36'),
(184, 184, 184, 99, NULL, '2026-02-10', '2026-07-31', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2026-02-01 06:30:00', 'terminated', 'Moved out at the end of contract.', '2026-07-31 02:00:00', NULL, NULL, 355, 355, '2026-02-02 06:30:00', '2026-10-01 15:14:36'),
(185, 185, 185, 100, NULL, '2025-10-02', '2026-02-28', 4200.00, NULL, 'signed', 'contracts/dormitory-contract.pdf', '2025-09-22 07:30:00', 'terminated', 'Transferred to a dorm closer to the new campus.', '2026-02-28 02:00:00', NULL, NULL, 355, 355, '2025-09-23 07:30:00', '2026-10-01 15:14:36');

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
(1, 1, 1, 'Aircon not cooling', 'maintenance_repairs', 'The aircon in Room 101 blows air but it is not cold anymore since last night.', NULL, 'non_urgent', 'resolved', 357, '2026-09-13 03:30:00', '2026-09-11 03:30:00', '2026-09-11 07:30:00'),
(2, 2, 2, 'Sparking outlet near Bed 2', 'electrical_issue', 'The outlet beside my bed sparked when I plugged in my charger. I stopped using it.', NULL, 'urgent', 'open', NULL, NULL, '2026-10-01 13:12:50', '2026-10-01 14:12:50'),
(3, 5, 5, 'Low water pressure in CR', 'plumbing_water_emergency', 'Mahina po ang tulo ng tubig sa shower tuwing 6-7 AM.', NULL, 'non_urgent', 'in_progress', 357, NULL, '2026-09-27 07:30:00', '2026-09-27 09:30:00'),
(4, 8, 10, 'Noisy neighbors after quiet hours', 'noise_roommate_concern', 'May maingay po sa kabilang room past 12 midnight, 3 nights na. Quiet hours po ay 10 PM.', NULL, 'non_urgent', 'seen', NULL, NULL, '2026-09-29 02:30:00', '2026-09-29 03:30:00'),
(5, 11, 15, 'Request for a bigger study table in the lobby', 'suggestion_feedback', 'Suggestion lang po: sana may mas malaking study table sa lobby for group study.', NULL, 'non_urgent', 'rejected', 355, NULL, '2026-09-16 05:30:00', '2026-09-16 07:30:00'),
(6, 12, 16, 'Question about my rejected GCash payment', 'billing_payment_concern', 'Bakit po na-reject yung payment ko? Nagbayad naman po ako.', NULL, 'non_urgent', 'in_progress', 356, NULL, '2026-10-01 13:13:01', '2026-10-01 15:13:01'),
(7, 15, 19, 'Broken door lock', 'security_concern', 'Hindi po nagla-lock nang maayos ang pinto ng Room 201, kailangan pang itulak.', NULL, 'urgent', 'resolved', 357, '2026-09-24 09:30:00', '2026-09-22 09:30:00', '2026-09-22 11:30:00'),
(8, 18, 25, 'WiFi keeps disconnecting', 'facilities_amenities', 'The WiFi on the 2nd floor drops every 10-15 minutes, hard to attend online classes.', NULL, 'non_urgent', 'open', NULL, NULL, '2026-09-30 04:30:00', '2026-09-30 05:30:00'),
(9, 20, 29, 'Ceiling leak near the window', 'structural_damage', 'May tumutulo po sa kisame tuwing malakas ang ulan, malapit sa bintana ng Room 204.', NULL, 'urgent', 'in_progress', 357, NULL, '2026-09-28 06:30:00', '2026-09-28 09:30:00');

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
(82, '2026_09_30_000002_create_monthly_expenses_table', 54),
(83, '2026_10_01_000001_align_with_dormitory_documents', 55),
(84, '2026_10_04_000001_add_overdue_notified_at_to_applications', 56),
(85, '2026_10_04_000002_add_hero_photo_paths_to_dormitory_profile', 57);

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
(1, '2025-10-01', 101673.00, 13359.00, 6998.00, 57000.00, 0.00, NULL, 356, '2025-10-31 15:59:59', '2025-10-31 15:59:59'),
(2, '2025-11-01', 92715.00, 14067.00, 6998.00, 57000.00, 9800.00, 'Replacement of water pump capacitor', 356, '2025-11-30 15:59:59', '2025-11-30 15:59:59'),
(3, '2025-12-01', 101500.00, 13872.00, 6998.00, 114000.00, 4500.00, 'Pest control (whole building)', 356, '2025-12-31 15:59:59', '2025-12-31 15:59:59'),
(4, '2026-01-01', 101090.00, 14737.00, 6998.00, 57000.00, 2130.00, 'Cleaning supplies and toiletries for common areas', 356, '2026-01-31 15:59:59', '2026-01-31 15:59:59'),
(5, '2026-02-01', 97279.00, 13909.00, 6998.00, 57000.00, 24000.00, 'Repainting of hallway and lobby', 356, '2026-02-28 15:59:59', '2026-02-28 15:59:59'),
(6, '2026-03-01', 122391.00, 16165.00, 6998.00, 57000.00, 0.00, NULL, 356, '2026-03-31 15:59:59', '2026-03-31 15:59:59'),
(7, '2026-04-01', 134953.00, 13305.00, 6998.00, 57000.00, 1594.00, 'Cleaning supplies and toiletries for common areas', 356, '2026-04-30 15:59:59', '2026-04-30 15:59:59'),
(8, '2026-05-01', 130743.00, 13102.00, 6998.00, 57000.00, 6400.00, 'Plumbing repair, 3F shared CR', 356, '2026-05-31 15:59:59', '2026-05-31 15:59:59'),
(9, '2026-06-01', 103034.00, 15947.00, 6998.00, 57000.00, 1533.00, 'Cleaning supplies and toiletries for common areas', 356, '2026-06-30 15:59:59', '2026-06-30 15:59:59'),
(10, '2026-07-01', 103584.00, 16763.00, 6998.00, 57000.00, 2463.00, 'Cleaning supplies and toiletries for common areas', 356, '2026-07-31 15:59:59', '2026-07-31 15:59:59'),
(11, '2026-08-01', 92666.00, 14743.00, 6998.00, 57000.00, 18500.00, 'Aircon cleaning and freon recharge (all AC rooms)', 356, '2026-08-31 15:59:59', '2026-08-31 15:59:59'),
(12, '2026-09-01', 99163.00, 13541.00, 6998.00, 57000.00, 0.00, NULL, 356, '2026-09-30 15:59:59', '2026-09-30 15:59:59');

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
(1, 1, 1, 9000.00, 'cash', 'Cash Payment', NULL, '2026-04-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-10 02:15:00'),
(2, 2, 1, 4500.00, 'gcash', 'GCash', '1000048319271', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 03:15:00'),
(3, 3, 1, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1002', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 04:15:00'),
(4, 4, 1, 4500.00, 'gcash', 'GCash', '1000048415813', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 05:15:00'),
(5, 5, 1, 4500.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 06:15:00'),
(6, 6, 1, 4500.00, 'gcash', 'GCash', '1000048464084', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 07:15:00'),
(7, 7, 1, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1005', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:12:49', NULL, '2026-10-01 08:15:00'),
(8, 8, 2, 9000.00, 'cash', 'Cash Payment', NULL, '2026-06-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-02 09:15:00'),
(9, 9, 2, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1006', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 01:15:00'),
(10, 10, 2, 4500.00, 'gcash', 'GCash', '1000048608897', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 02:15:00'),
(11, 11, 2, 4500.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 03:15:00'),
(12, 13, 3, 9000.00, 'cash', 'Cash Payment', NULL, '2026-07-04', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-04 05:15:00'),
(13, 14, 3, 4500.00, 'gcash', 'GCash', '1000048657168', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 06:15:00'),
(14, 15, 3, 4500.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 07:15:00'),
(15, 16, 3, 4500.00, 'gcash', 'GCash', '1000048705439', '2026-10-01', 'pending', 'demo/sample-payment-proof.png', 'Time of payment: 08:42. Full payment for this month po.', NULL, NULL, NULL, NULL, '2026-10-01 08:15:00'),
(16, 17, 4, 8000.00, 'cash', 'Cash Payment', NULL, '2026-07-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-12 09:15:00'),
(17, 18, 4, 4000.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 01:15:00'),
(18, 19, 4, 4000.00, 'gcash', 'GCash', '1000048753710', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 02:15:00'),
(19, 20, 4, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1011', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:12:52', NULL, '2026-10-01 03:15:00'),
(20, 21, 5, 9000.00, 'cash', 'Cash Payment', NULL, '2026-03-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-15 04:15:00'),
(21, 22, 5, 4500.00, 'gcash', 'GCash', '1000048850252', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 05:15:00'),
(22, 23, 5, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1013', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 06:15:00'),
(23, 24, 5, 4950.00, 'gcash', 'GCash', '1000048946794', '2026-06-07', 'approved', 'demo/sample-payment-proof.png', 'Sorry po late, na-delay sweldo.', NULL, 356, '2026-06-08 02:00:00', NULL, '2026-06-07 07:15:00'),
(24, 25, 5, 4500.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 08:15:00'),
(25, 26, 5, 4500.00, 'gcash', 'GCash', '1000048995065', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 09:15:00'),
(26, 27, 5, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1016', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 01:15:00'),
(27, 28, 5, 4950.00, 'gcash', 'GCash', '1000049091607', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', 'Sorry po late, na-delay sweldo.', NULL, 356, '2026-10-01 15:12:53', NULL, '2026-10-01 02:15:00'),
(28, 29, 6, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-14 03:15:00'),
(29, 30, 6, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1018', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 04:15:00'),
(30, 31, 6, 3800.00, 'gcash', 'GCash', '1000049188149', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 05:15:00'),
(31, 32, 6, 3800.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 06:15:00'),
(32, 34, 7, 9000.00, 'cash', 'Cash Payment', NULL, '2026-08-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-05 08:15:00'),
(33, 35, 7, 4500.00, 'gcash', 'GCash', '1000049236420', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 09:15:00'),
(34, 36, 7, 4500.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 01:15:00'),
(35, 37, 8, 9000.00, 'cash', 'Cash Payment', NULL, '2026-05-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-03 02:15:00'),
(36, 38, 8, 4500.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 03:15:00'),
(37, 39, 8, 4500.00, 'gcash', 'GCash', '1000049284691', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 04:15:00'),
(38, 40, 8, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1022', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 05:15:00'),
(39, 41, 8, 4950.00, 'gcash', 'GCash', '1000049381233', '2026-09-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-12 02:00:00', NULL, '2026-09-11 06:15:00'),
(40, 42, 8, 2300.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, 'Partial muna po, babayaran ko yung natitira sa sweldo.', NULL, NULL, NULL, 356, '2026-10-01 07:15:00'),
(41, 43, 9, 9000.00, 'cash', 'Cash Payment', NULL, '2026-05-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-08 08:15:00'),
(42, 44, 9, 4500.00, 'gcash', 'GCash', '1000049429504', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 09:15:00'),
(43, 45, 9, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1025', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 01:15:00'),
(44, 46, 9, 4950.00, 'gcash', 'GCash', '1000049526046', '2026-08-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-12 02:00:00', NULL, '2026-08-11 02:15:00'),
(45, 48, 10, 8400.00, 'cash', 'Cash Payment', NULL, '2026-04-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-12 04:15:00'),
(46, 49, 10, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1027', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 05:15:00'),
(47, 50, 10, 4620.00, 'gcash', 'GCash', '1000049622588', '2026-06-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-12 02:00:00', NULL, '2026-06-11 06:15:00'),
(48, 51, 10, 4200.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 07:15:00'),
(49, 52, 10, 4200.00, 'gcash', 'GCash', '1000049670859', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 08:15:00'),
(50, 53, 10, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1030', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 09:15:00'),
(51, 54, 10, 4200.00, 'gcash', 'GCash', '1000049767401', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:12:59', NULL, '2026-10-01 01:15:00'),
(52, 55, 11, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-20 02:15:00'),
(53, 56, 11, 4180.00, 'gcash', 'GCash', '1000049815672', '2026-02-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-12 02:00:00', NULL, '2026-02-11 03:15:00'),
(54, 57, 11, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 04:15:00'),
(55, 58, 11, 3800.00, 'gcash', 'GCash', '1000049863943', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 05:15:00'),
(56, 59, 11, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1034', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 06:15:00'),
(57, 60, 11, 3800.00, 'gcash', 'GCash', '1000049960485', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 07:15:00'),
(58, 61, 11, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 08:15:00'),
(59, 62, 11, 3800.00, 'gcash', 'GCash', '1000050008756', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 09:15:00'),
(60, 63, 11, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1037', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 01:15:00'),
(61, 64, 11, 3800.00, 'gcash', 'GCash', '1000050105298', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:00', NULL, '2026-10-01 02:15:00'),
(62, 65, 12, 8000.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(63, 66, 12, 4000.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 04:15:00'),
(64, 67, 12, 4000.00, 'gcash', 'GCash', '1000050153569', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 05:15:00'),
(65, 68, 12, 4000.00, 'gcash', 'GCash', '1000050201840', '2026-10-01', 'rejected', 'demo/sample-payment-proof.png', 'Bayad ko po for this month.', 'Screenshot is cropped -- the reference number and amount are not visible. Please upload the full receipt.', 356, '2026-10-01 15:13:01', NULL, '2026-10-01 06:15:00'),
(66, 69, 13, 8000.00, 'cash', 'Cash Payment', NULL, '2026-05-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-11 07:15:00'),
(67, 70, 13, 4000.00, 'gcash', 'GCash', '1000050250111', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 08:15:00'),
(68, 71, 13, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1042', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 09:15:00'),
(69, 72, 13, 4000.00, 'gcash', 'GCash', '1000050346653', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 01:15:00'),
(70, 74, 14, 8000.00, 'cash', 'Cash Payment', NULL, '2026-05-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-31 03:15:00'),
(71, 75, 14, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1044', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 04:15:00'),
(72, 76, 14, 4000.00, 'gcash', 'GCash', '1000050443195', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 05:15:00'),
(73, 77, 14, 4000.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 06:15:00'),
(74, 79, 15, 8000.00, 'cash', 'Cash Payment', NULL, '2026-06-07', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-07 08:15:00'),
(75, 80, 15, 4000.00, 'gcash', 'GCash', '1000050491466', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 09:15:00'),
(76, 81, 15, 4000.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 01:15:00'),
(77, 82, 15, 4000.00, 'gcash', 'GCash', '1000050539737', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 02:15:00'),
(78, 83, 15, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1048', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:04', NULL, '2026-10-01 03:15:00'),
(79, 85, 17, 8400.00, 'cash', 'Cash Payment', NULL, '2026-07-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-09 05:15:00'),
(80, 86, 17, 4200.00, 'gcash', 'GCash', '1000050636279', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 06:15:00'),
(81, 87, 17, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1050', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 07:15:00'),
(82, 88, 17, 4200.00, 'gcash', 'GCash', '1000050732821', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:06', NULL, '2026-10-01 08:15:00'),
(83, 89, 18, 8400.00, 'cash', 'Cash Payment', NULL, '2026-05-18', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-18 09:15:00'),
(84, 90, 18, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1052', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 01:15:00'),
(85, 91, 18, 4200.00, 'gcash', 'GCash', '1000050829363', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 02:15:00'),
(86, 92, 18, 4200.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 03:15:00'),
(87, 93, 18, 4200.00, 'gcash', 'GCash', '1000050877634', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 04:15:00'),
(88, 94, 18, 4620.00, 'bank_transfer', 'BDO', 'BDO-261001-1055', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:07', NULL, '2026-10-01 05:15:00'),
(89, 95, 19, 8400.00, 'gcash', 'GCash', '1000050974176', '2026-09-30', 'pending', 'demo/sample-payment-proof.png', 'Move-in fee (deposit + advance). Sent via GCash po.', NULL, NULL, NULL, NULL, '2026-09-30 06:15:00'),
(90, 96, 20, 8000.00, 'cash', 'Cash Payment', NULL, '2026-04-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-06 07:15:00'),
(91, 97, 20, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 08:15:00'),
(92, 98, 20, 4000.00, 'gcash', 'GCash', '1000051022447', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 09:15:00'),
(93, 99, 20, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1058', '2026-07-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-11 02:00:00', NULL, '2026-07-10 01:15:00'),
(94, 100, 20, 4000.00, 'gcash', 'GCash', '1000051118989', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 02:15:00'),
(95, 101, 20, 4000.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 03:15:00'),
(96, 102, 20, 4000.00, 'gcash', 'GCash', '1000051167260', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:12', NULL, '2026-10-01 04:15:00'),
(97, 103, 21, 8000.00, 'cash', 'Cash Payment', NULL, '2026-06-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-13 05:15:00'),
(98, 104, 21, 4000.00, 'gcash', 'GCash', '1000051215531', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 06:15:00'),
(99, 105, 21, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1062', '2026-08-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-11 02:00:00', NULL, '2026-08-10 07:15:00'),
(100, 106, 21, 4000.00, 'gcash', 'GCash', '1000051312073', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 08:15:00'),
(101, 107, 21, 4000.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 09:15:00'),
(102, 108, 22, 8000.00, 'cash', 'Cash Payment', NULL, '2026-08-04', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-04 01:15:00'),
(103, 109, 22, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1064', '2026-09-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-11 02:00:00', NULL, '2026-09-10 02:15:00'),
(104, 110, 22, 4000.00, 'gcash', 'GCash', '1000051408615', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:17', NULL, '2026-10-01 03:15:00'),
(105, 111, 23, 8400.00, 'cash', 'Cash Payment', NULL, '2026-02-16', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-16 04:15:00'),
(106, 112, 23, 4200.00, 'gcash', 'GCash', '1000051456886', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 05:15:00'),
(107, 113, 23, 4200.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 06:15:00'),
(108, 114, 23, 4200.00, 'gcash', 'GCash', '1000051505157', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 07:15:00'),
(109, 115, 23, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1068', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 08:15:00'),
(110, 116, 23, 4200.00, 'gcash', 'GCash', '1000051601699', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 09:15:00'),
(111, 117, 24, 7600.00, 'cash', 'Cash Payment', NULL, '2026-03-23', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-23 01:15:00'),
(112, 118, 24, 3800.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 02:15:00'),
(113, 119, 24, 3800.00, 'gcash', 'GCash', '1000051698241', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 03:15:00'),
(114, 120, 24, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1072', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 04:15:00'),
(115, 121, 24, 3800.00, 'gcash', 'GCash', '1000051794783', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 05:15:00'),
(116, 123, 25, 9000.00, 'cash', 'Cash Payment', NULL, '2026-02-16', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-16 07:15:00'),
(117, 124, 25, 4500.00, 'gcash', 'GCash', '1000051843054', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 08:15:00'),
(118, 125, 25, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1075', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 09:15:00'),
(119, 126, 25, 4500.00, 'gcash', 'GCash', '1000051939596', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 01:15:00'),
(120, 127, 25, 4500.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 02:15:00'),
(121, 128, 25, 4500.00, 'gcash', 'GCash', '1000051987867', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 03:15:00'),
(122, 129, 25, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1078', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 04:15:00'),
(123, 130, 25, 4500.00, 'gcash', 'GCash', '1000052084409', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 05:15:00'),
(124, 131, 25, 4500.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 06:15:00'),
(125, 132, 26, 9000.00, 'cash', 'Cash Payment', NULL, '2026-06-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-11 07:15:00'),
(126, 133, 26, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1080', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 08:15:00'),
(127, 134, 26, 4500.00, 'gcash', 'GCash', '1000052180951', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 09:15:00'),
(128, 135, 26, 4500.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 01:15:00'),
(129, 136, 26, 4500.00, 'gcash', 'GCash', '1000052229222', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:22', NULL, '2026-10-01 02:15:00'),
(130, 137, 27, 9000.00, 'cash', 'Cash Payment', NULL, '2026-07-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-09 03:15:00'),
(131, 138, 27, 4500.00, 'gcash', 'GCash', '1000052277493', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 04:15:00'),
(132, 139, 27, 4500.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 05:15:00'),
(133, 140, 27, 4500.00, 'gcash', 'GCash', '1000052325764', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:24', NULL, '2026-10-01 06:15:00'),
(134, 141, 28, 8400.00, 'cash', 'Cash Payment', NULL, '2026-09-21', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-21 07:15:00'),
(135, 142, 28, 4200.00, 'gcash', 'GCash', '1000052374035', '2026-10-01', 'pending', 'demo/sample-payment-proof.png', 'Time of payment: 08:42. Full payment for this month po.', NULL, NULL, NULL, NULL, '2026-10-01 08:15:00'),
(136, 143, 29, 7600.00, 'cash', 'Cash Payment', NULL, '2026-09-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-14 09:15:00'),
(137, 144, 29, 3800.00, 'gcash', 'GCash', '1000052422306', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:27', NULL, '2026-10-01 01:15:00'),
(138, 145, 30, 7600.00, 'cash', 'Cash Payment', NULL, '2026-09-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-02 02:15:00'),
(139, 147, 31, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-24 04:15:00'),
(140, 148, 31, 3800.00, 'gcash', 'GCash', '1000052470577', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 05:15:00'),
(141, 149, 31, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 06:15:00'),
(142, 150, 31, 4180.00, 'gcash', 'GCash', '1000052518848', '2026-08-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-10 02:00:00', NULL, '2026-08-09 07:15:00'),
(143, 151, 31, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1089', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 08:15:00'),
(144, 152, 31, 3800.00, 'gcash', 'GCash', '1000052615390', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:31', NULL, '2026-10-01 09:15:00'),
(145, 153, 32, 9000.00, 'cash', 'Cash Payment', NULL, '2026-07-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-22 01:15:00'),
(146, 154, 32, 4500.00, 'gcash', 'GCash', '1000052663661', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 02:15:00'),
(147, 155, 32, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1092', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 03:15:00'),
(148, 157, 33, 9000.00, 'cash', 'Cash Payment', NULL, '2026-06-26', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-26 05:15:00'),
(149, 158, 33, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1093', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 06:15:00'),
(150, 159, 33, 4500.00, 'gcash', 'GCash', '1000052808474', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 07:15:00'),
(151, 160, 33, 4500.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 08:15:00'),
(152, 162, 34, 8400.00, 'cash', 'Cash Payment', NULL, '2026-07-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-03 01:15:00'),
(153, 163, 34, 4200.00, 'gcash', 'GCash', '1000052856745', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 02:15:00'),
(154, 164, 34, 4200.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 03:15:00'),
(155, 166, 35, 8400.00, 'cash', 'Cash Payment', NULL, '2026-07-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-09 05:15:00'),
(156, 167, 35, 4620.00, 'cash', 'Cash Payment', NULL, '2026-08-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-06 06:15:00'),
(157, 168, 35, 4200.00, 'gcash', 'GCash', '1000052905016', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 07:15:00'),
(158, 170, 36, 8000.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-06 09:15:00'),
(159, 171, 36, 4000.00, 'gcash', 'GCash', '1000052953287', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 01:15:00'),
(160, 172, 36, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1098', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 02:15:00'),
(161, 173, 36, 4000.00, 'gcash', 'GCash', '1000053049829', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 03:15:00'),
(162, 174, 36, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 04:15:00'),
(163, 175, 36, 4000.00, 'gcash', 'GCash', '1000053098100', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 05:15:00'),
(164, 176, 36, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1101', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 06:15:00'),
(165, 177, 36, 4000.00, 'gcash', 'GCash', '1000053194642', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 07:15:00'),
(166, 178, 36, 4000.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 08:15:00'),
(167, 179, 36, 4000.00, 'gcash', 'GCash', '1000053242913', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:40', NULL, '2026-10-01 09:15:00'),
(168, 180, 37, 8000.00, 'cash', 'Cash Payment', NULL, '2026-02-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-06 01:15:00'),
(169, 181, 37, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1104', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 02:15:00'),
(170, 182, 37, 4000.00, 'gcash', 'GCash', '1000053339455', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 03:15:00'),
(171, 183, 37, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 04:15:00'),
(172, 184, 37, 4000.00, 'gcash', 'GCash', '1000053387726', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 05:15:00'),
(173, 185, 37, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1107', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 06:15:00'),
(174, 186, 37, 4000.00, 'gcash', 'GCash', '1000053484268', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 07:15:00'),
(175, 187, 37, 4000.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 08:15:00'),
(176, 188, 37, 4000.00, 'gcash', 'GCash', '1000053532539', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:42', NULL, '2026-10-01 09:15:00'),
(177, 189, 38, 8000.00, 'cash', 'Cash Payment', NULL, '2026-09-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-10 01:15:00'),
(178, 191, 39, 8000.00, 'cash', 'Cash Payment', NULL, '2026-02-18', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-18 03:15:00'),
(179, 192, 39, 4400.00, 'cash', 'Cash Payment', NULL, '2026-03-10', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-10 04:15:00'),
(180, 193, 39, 4000.00, 'gcash', 'GCash', '1000053580810', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 05:15:00'),
(181, 194, 39, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1111', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 06:15:00'),
(182, 195, 39, 4000.00, 'gcash', 'GCash', '1000053677352', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 07:15:00'),
(183, 196, 39, 4000.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 08:15:00'),
(184, 197, 39, 4000.00, 'gcash', 'GCash', '1000053725623', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 09:15:00'),
(185, 198, 39, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1114', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 01:15:00'),
(186, 199, 39, 4000.00, 'gcash', 'GCash', '1000053822165', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:45', NULL, '2026-10-01 02:15:00'),
(187, 200, 40, 8000.00, 'cash', 'Cash Payment', NULL, '2026-09-19', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-19 03:15:00'),
(188, 201, 40, 4000.00, 'gcash', 'GCash', '1000053870436', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:46', NULL, '2026-10-01 04:15:00'),
(189, 202, 41, 8400.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-01 05:15:00'),
(190, 203, 41, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1117', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 06:15:00'),
(191, 204, 41, 4200.00, 'gcash', 'GCash', '1000053966978', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:47', NULL, '2026-10-01 07:15:00'),
(192, 205, 42, 8400.00, 'cash', 'Cash Payment', NULL, '2026-03-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-09 08:15:00'),
(193, 206, 42, 4200.00, 'gcash', 'GCash', '1000054015249', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 09:15:00'),
(194, 207, 42, 4200.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 01:15:00'),
(195, 208, 42, 4200.00, 'gcash', 'GCash', '1000054063520', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 02:15:00'),
(196, 209, 42, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1121', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 03:15:00'),
(197, 210, 42, 4200.00, 'gcash', 'GCash', '1000054160062', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 04:15:00'),
(198, 211, 42, 4200.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 05:15:00'),
(199, 212, 42, 4200.00, 'gcash', 'GCash', '1000054208333', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:48', NULL, '2026-10-01 06:15:00'),
(200, 213, 43, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-14 07:15:00'),
(201, 214, 43, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(202, 215, 43, 3800.00, 'gcash', 'GCash', '1000054256604', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 09:15:00'),
(203, 216, 43, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1125', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 01:15:00'),
(204, 217, 43, 3800.00, 'gcash', 'GCash', '1000054353146', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 02:15:00'),
(205, 218, 43, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 03:15:00'),
(206, 219, 43, 4180.00, 'gcash', 'GCash', '1000054401417', '2026-06-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-08 02:00:00', NULL, '2026-06-07 04:15:00'),
(207, 220, 43, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1128', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 05:15:00'),
(208, 221, 43, 3800.00, 'gcash', 'GCash', '1000054497959', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 06:15:00'),
(209, 222, 43, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 07:15:00'),
(210, 223, 43, 3800.00, 'gcash', 'GCash', '1000054546230', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:49', NULL, '2026-10-01 08:15:00'),
(211, 224, 44, 7600.00, 'cash', 'Cash Payment', NULL, '2026-07-23', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-23 09:15:00'),
(212, 225, 44, 3800.00, 'gcash', 'GCash', '1000054594501', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 01:15:00'),
(213, 226, 44, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1132', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 02:15:00'),
(214, 227, 44, 3800.00, 'gcash', 'GCash', '1000054691043', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:50', NULL, '2026-10-01 03:15:00'),
(215, 228, 45, 7600.00, 'cash', 'Cash Payment', NULL, '2026-09-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-09-03 04:15:00'),
(216, 229, 45, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1134', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:52', NULL, '2026-10-01 05:15:00'),
(217, 230, 46, 7600.00, 'cash', 'Cash Payment', NULL, '2025-11-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-11-13 06:15:00'),
(218, 231, 46, 3800.00, 'gcash', 'GCash', '1000054787585', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 07:15:00'),
(219, 232, 46, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(220, 233, 46, 3800.00, 'gcash', 'GCash', '1000054835856', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 09:15:00'),
(221, 234, 46, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1137', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 01:15:00'),
(222, 235, 46, 3800.00, 'gcash', 'GCash', '1000054932398', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 02:15:00'),
(223, 236, 46, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 03:15:00'),
(224, 237, 46, 3800.00, 'gcash', 'GCash', '1000054980669', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 04:15:00'),
(225, 238, 46, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1140', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 05:15:00'),
(226, 239, 46, 3800.00, 'gcash', 'GCash', '1000055077211', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 06:15:00'),
(227, 240, 46, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 07:15:00'),
(228, 241, 46, 3800.00, 'gcash', 'GCash', '1000055125482', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:53', NULL, '2026-10-01 08:15:00'),
(229, 242, 47, 7600.00, 'cash', 'Cash Payment', NULL, '2026-04-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-08 09:15:00'),
(230, 243, 47, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 01:15:00'),
(231, 244, 47, 3800.00, 'gcash', 'GCash', '1000055173753', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 02:15:00'),
(232, 245, 47, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1144', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 03:15:00'),
(233, 246, 47, 3800.00, 'gcash', 'GCash', '1000055270295', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 04:15:00'),
(234, 247, 47, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 05:15:00'),
(235, 248, 47, 3800.00, 'gcash', 'GCash', '1000055318566', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:54', NULL, '2026-10-01 06:15:00'),
(236, 249, 48, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-05 07:15:00'),
(237, 250, 48, 4180.00, 'gcash', 'GCash', '1000055366837', '2026-02-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-08 02:00:00', NULL, '2026-02-07 08:15:00'),
(238, 251, 48, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1148', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 09:15:00'),
(239, 252, 48, 3800.00, 'gcash', 'GCash', '1000055463379', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 01:15:00'),
(240, 253, 48, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 02:15:00'),
(241, 254, 48, 3800.00, 'gcash', 'GCash', '1000055511650', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 03:15:00'),
(242, 255, 48, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1151', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 04:15:00'),
(243, 256, 48, 3800.00, 'gcash', 'GCash', '1000055608192', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 05:15:00'),
(244, 257, 48, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 06:15:00'),
(245, 258, 48, 3800.00, 'gcash', 'GCash', '1000055656463', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:55', NULL, '2026-10-01 07:15:00'),
(246, 259, 49, 8400.00, 'cash', 'Cash Payment', NULL, '2026-06-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-09 08:15:00'),
(247, 260, 49, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1154', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 09:15:00'),
(248, 261, 49, 4620.00, 'gcash', 'GCash', '1000055753005', '2026-08-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-10 02:00:00', NULL, '2026-08-09 01:15:00'),
(249, 262, 49, 4200.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 02:15:00'),
(250, 264, 50, 8400.00, 'cash', 'Cash Payment', NULL, '2026-07-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-02 04:15:00'),
(251, 265, 50, 4200.00, 'gcash', 'GCash', '1000055801276', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 05:15:00'),
(252, 266, 50, 4200.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 06:15:00'),
(253, 267, 50, 4200.00, 'gcash', 'GCash', '1000055849547', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:57', NULL, '2026-10-01 07:15:00'),
(254, 268, 51, 8400.00, 'cash', 'Cash Payment', NULL, '2025-11-07', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-11-07 08:15:00'),
(255, 269, 51, 4200.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 09:15:00'),
(256, 270, 51, 4200.00, 'gcash', 'GCash', '1000055897818', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 01:15:00'),
(257, 271, 51, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1159', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 02:15:00'),
(258, 272, 51, 4200.00, 'gcash', 'GCash', '1000055994360', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 03:15:00'),
(259, 273, 51, 4200.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 04:15:00'),
(260, 274, 51, 4200.00, 'gcash', 'GCash', '1000056042631', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 05:15:00'),
(261, 275, 51, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1162', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 06:15:00'),
(262, 276, 51, 4200.00, 'gcash', 'GCash', '1000056139173', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 07:15:00'),
(263, 277, 51, 4200.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 08:15:00'),
(264, 278, 51, 4200.00, 'gcash', 'GCash', '1000056187444', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 09:15:00'),
(265, 279, 51, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1165', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:58', NULL, '2026-10-01 01:15:00'),
(266, 280, 52, 8000.00, 'cash', 'Cash Payment', NULL, '2026-07-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-31 02:15:00'),
(267, 281, 52, 4000.00, 'gcash', 'GCash', '1000056283986', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 03:15:00'),
(268, 282, 52, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1167', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:13:59', NULL, '2026-10-01 04:15:00'),
(269, 283, 53, 8000.00, 'cash', 'Cash Payment', NULL, '2026-03-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-12 05:15:00'),
(270, 284, 53, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1168', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(271, 285, 53, 4000.00, 'gcash', 'GCash', '1000056428799', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(272, 286, 53, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 08:15:00');
INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `payment_method_label`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(273, 287, 53, 4400.00, 'gcash', 'GCash', '1000056477070', '2026-07-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-10 02:00:00', NULL, '2026-07-09 09:15:00'),
(274, 288, 53, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1171', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 01:15:00'),
(275, 289, 53, 4000.00, 'gcash', 'GCash', '1000056573612', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 02:15:00'),
(276, 291, 54, 8000.00, 'cash', 'Cash Payment', NULL, '2026-03-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-24 04:15:00'),
(277, 292, 54, 4400.00, 'gcash', 'GCash', '1000056621883', '2026-04-07', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-08 02:00:00', NULL, '2026-04-07 05:15:00'),
(278, 293, 54, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 06:15:00'),
(279, 294, 54, 4000.00, 'gcash', 'GCash', '1000056670154', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 07:15:00'),
(280, 295, 54, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1175', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 08:15:00'),
(281, 296, 54, 4000.00, 'gcash', 'GCash', '1000056766696', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 09:15:00'),
(282, 297, 54, 4000.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 01:15:00'),
(283, 298, 54, 4000.00, 'gcash', 'GCash', '1000056814967', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:01', NULL, '2026-10-01 02:15:00'),
(284, 299, 55, 8000.00, 'cash', 'Cash Payment', NULL, '2026-01-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-22 03:15:00'),
(285, 300, 55, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 04:15:00'),
(286, 301, 55, 4000.00, 'gcash', 'GCash', '1000056863238', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 05:15:00'),
(287, 302, 55, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1179', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(288, 303, 55, 4000.00, 'gcash', 'GCash', '1000056959780', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(289, 304, 55, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 08:15:00'),
(290, 305, 55, 4000.00, 'gcash', 'GCash', '1000057008051', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 09:15:00'),
(291, 306, 55, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1182', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 01:15:00'),
(292, 307, 55, 4000.00, 'gcash', 'GCash', '1000057104593', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 02:15:00'),
(293, 308, 55, 4000.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 03:15:00'),
(294, 309, 56, 8000.00, 'cash', 'Cash Payment', NULL, '2025-11-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-11-09 04:15:00'),
(295, 310, 56, 4000.00, 'gcash', 'GCash', '1000057152864', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 05:15:00'),
(296, 311, 56, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1185', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 06:15:00'),
(297, 312, 56, 4000.00, 'gcash', 'GCash', '1000057249406', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 07:15:00'),
(298, 313, 56, 4000.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(299, 314, 56, 4000.00, 'gcash', 'GCash', '1000057297677', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 09:15:00'),
(300, 315, 56, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1188', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 01:15:00'),
(301, 316, 56, 4000.00, 'gcash', 'GCash', '1000057394219', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 02:15:00'),
(302, 317, 56, 4000.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(303, 318, 56, 4000.00, 'gcash', 'GCash', '1000057442490', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 04:15:00'),
(304, 319, 56, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1191', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 05:15:00'),
(305, 321, 57, 8000.00, 'cash', 'Cash Payment', NULL, '2026-01-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-14 07:15:00'),
(306, 322, 57, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1192', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 08:15:00'),
(307, 323, 57, 4000.00, 'gcash', 'GCash', '1000057587303', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 09:15:00'),
(308, 324, 57, 4000.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 01:15:00'),
(309, 325, 57, 4000.00, 'gcash', 'GCash', '1000057635574', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 02:15:00'),
(310, 326, 57, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1195', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 03:15:00'),
(311, 327, 57, 4000.00, 'gcash', 'GCash', '1000057732116', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 04:15:00'),
(312, 328, 57, 4000.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 05:15:00'),
(313, 329, 57, 4000.00, 'gcash', 'GCash', '1000057780387', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 06:15:00'),
(314, 331, 58, 7600.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-01 08:15:00'),
(315, 332, 58, 3800.00, 'gcash', 'GCash', '1000057828658', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 09:15:00'),
(316, 333, 58, 3800.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 01:15:00'),
(317, 334, 59, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-31 02:15:00'),
(318, 335, 59, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(319, 336, 59, 3800.00, 'gcash', 'GCash', '1000057876929', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 04:15:00'),
(320, 337, 59, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1200', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 05:15:00'),
(321, 339, 60, 7600.00, 'cash', 'Cash Payment', NULL, '2026-07-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-07-24 07:15:00'),
(322, 340, 60, 3800.00, 'gcash', 'GCash', '1000057973471', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 08:15:00'),
(323, 341, 60, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1202', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 09:15:00'),
(324, 342, 60, 3800.00, 'gcash', 'GCash', '1000058070013', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:08', NULL, '2026-10-01 01:15:00'),
(325, 343, 61, 7600.00, 'cash', 'Cash Payment', NULL, '2026-02-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-09 02:15:00'),
(326, 344, 61, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1204', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 03:15:00'),
(327, 345, 61, 3800.00, 'gcash', 'GCash', '1000058166555', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 04:15:00'),
(328, 346, 61, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 05:15:00'),
(329, 347, 61, 3800.00, 'gcash', 'GCash', '1000058214826', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 06:15:00'),
(330, 348, 61, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1207', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 07:15:00'),
(331, 349, 61, 3800.00, 'gcash', 'GCash', '1000058311368', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 08:15:00'),
(332, 350, 61, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 09:15:00'),
(333, 351, 61, 3800.00, 'gcash', 'GCash', '1000058359639', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:09', NULL, '2026-10-01 01:15:00'),
(334, 352, 62, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-19', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-19 02:15:00'),
(335, 353, 62, 3800.00, 'gcash', 'GCash', '1000058407910', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 03:15:00'),
(336, 354, 62, 4180.00, 'cash', 'Cash Payment', NULL, '2026-02-10', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-10 04:15:00'),
(337, 355, 62, 3800.00, 'gcash', 'GCash', '1000058456181', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 05:15:00'),
(338, 356, 62, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1212', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(339, 357, 62, 3800.00, 'gcash', 'GCash', '1000058552723', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(340, 358, 62, 3800.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 08:15:00'),
(341, 359, 62, 4180.00, 'gcash', 'GCash', '1000058600994', '2026-07-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-10 02:00:00', NULL, '2026-07-09 09:15:00'),
(342, 360, 62, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1215', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 01:15:00'),
(343, 361, 62, 3800.00, 'gcash', 'GCash', '1000058697536', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 02:15:00'),
(344, 362, 62, 3800.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 03:15:00'),
(345, 363, 63, 7600.00, 'cash', 'Cash Payment', NULL, '2026-04-04', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-04 04:15:00'),
(346, 364, 63, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 05:15:00'),
(347, 365, 63, 3800.00, 'gcash', 'GCash', '1000058745807', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 06:15:00'),
(348, 366, 63, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1218', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 07:15:00'),
(349, 367, 63, 3800.00, 'gcash', 'GCash', '1000058842349', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 08:15:00'),
(350, 368, 63, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 09:15:00'),
(351, 369, 63, 3800.00, 'gcash', 'GCash', '1000058890620', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:12', NULL, '2026-10-01 01:15:00'),
(352, 370, 64, 7600.00, 'cash', 'Cash Payment', NULL, '2026-08-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-31 02:15:00'),
(353, 371, 64, 3800.00, 'gcash', 'GCash', '1000058938891', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:13', NULL, '2026-10-01 03:15:00'),
(354, 372, 65, 7600.00, 'cash', 'Cash Payment', NULL, '2026-04-17', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-17 04:15:00'),
(355, 373, 65, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1222', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 05:15:00'),
(356, 374, 65, 3800.00, 'gcash', 'GCash', '1000059035433', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 06:15:00'),
(357, 375, 65, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 07:15:00'),
(358, 376, 65, 3800.00, 'gcash', 'GCash', '1000059083704', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 08:15:00'),
(359, 377, 65, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1225', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 09:15:00'),
(360, 378, 65, 3800.00, 'gcash', 'GCash', '1000059180246', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:14', NULL, '2026-10-01 01:15:00'),
(361, 379, 66, 7600.00, 'cash', 'Cash Payment', NULL, '2026-06-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-05 02:15:00'),
(362, 380, 66, 3800.00, 'gcash', 'GCash', '1000059228517', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 03:15:00'),
(363, 381, 66, 3800.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 04:15:00'),
(364, 382, 66, 3800.00, 'gcash', 'GCash', '1000059276788', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 05:15:00'),
(365, 383, 66, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1229', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:15', NULL, '2026-10-01 06:15:00'),
(366, 384, 67, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-21', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-21 07:15:00'),
(367, 385, 67, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(368, 386, 67, 3800.00, 'gcash', 'GCash', '1000059373330', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 09:15:00'),
(369, 387, 67, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1231', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 01:15:00'),
(370, 388, 67, 3800.00, 'gcash', 'GCash', '1000059469872', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 02:15:00'),
(371, 389, 67, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 03:15:00'),
(372, 390, 67, 3800.00, 'gcash', 'GCash', '1000059518143', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 04:15:00'),
(373, 391, 67, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1234', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 05:15:00'),
(374, 392, 67, 3800.00, 'gcash', 'GCash', '1000059614685', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 06:15:00'),
(375, 393, 67, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 07:15:00'),
(376, 394, 67, 3800.00, 'gcash', 'GCash', '1000059662956', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:16', NULL, '2026-10-01 08:15:00'),
(377, 395, 68, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-24 09:15:00'),
(378, 396, 68, 3800.00, 'gcash', 'GCash', '1000059711227', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 01:15:00'),
(379, 397, 68, 4180.00, 'bank_transfer', 'BDO', 'BDO-261001-1238', '2026-02-10', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-11 02:00:00', NULL, '2026-02-10 02:15:00'),
(380, 398, 68, 3800.00, 'gcash', 'GCash', '1000059807769', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 03:15:00'),
(381, 399, 68, 3800.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 04:15:00'),
(382, 400, 68, 3800.00, 'gcash', 'GCash', '1000059856040', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 05:15:00'),
(383, 401, 68, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1241', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 06:15:00'),
(384, 402, 68, 3800.00, 'gcash', 'GCash', '1000059952582', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 07:15:00'),
(385, 403, 68, 3800.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 08:15:00'),
(386, 404, 68, 3800.00, 'gcash', 'GCash', '1000060000853', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 09:15:00'),
(387, 405, 68, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1244', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:17', NULL, '2026-10-01 01:15:00'),
(388, 406, 69, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-04', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-04 02:15:00'),
(389, 407, 69, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1245', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 03:15:00'),
(390, 408, 69, 3800.00, 'gcash', 'GCash', '1000060145666', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 04:15:00'),
(391, 409, 69, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 05:15:00'),
(392, 410, 69, 3800.00, 'gcash', 'GCash', '1000060193937', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 06:15:00'),
(393, 411, 69, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1248', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 07:15:00'),
(394, 412, 69, 3800.00, 'gcash', 'GCash', '1000060290479', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 08:15:00'),
(395, 413, 69, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 09:15:00'),
(396, 414, 69, 3800.00, 'gcash', 'GCash', '1000060338750', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 01:15:00'),
(397, 415, 69, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1251', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 02:15:00'),
(398, 417, 70, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-31 04:15:00'),
(399, 418, 70, 3800.00, 'gcash', 'GCash', '1000060435292', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 05:15:00'),
(400, 419, 70, 3800.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 06:15:00'),
(401, 420, 70, 3800.00, 'gcash', 'GCash', '1000060483563', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 07:15:00'),
(402, 421, 70, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1254', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:20', NULL, '2026-10-01 08:15:00'),
(403, 422, 71, 7600.00, 'cash', 'Cash Payment', NULL, '2026-03-19', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-19 09:15:00'),
(404, 423, 71, 3800.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 01:15:00'),
(405, 424, 71, 3800.00, 'gcash', 'GCash', '1000060580105', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 02:15:00'),
(406, 425, 71, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1256', '2026-06-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-04 02:00:00', NULL, '2026-06-03 03:15:00'),
(407, 426, 71, 3800.00, 'gcash', 'GCash', '1000060676647', '2026-07-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-05 02:00:00', NULL, '2026-07-04 04:15:00'),
(408, 427, 71, 3800.00, 'cash', 'Cash Payment', NULL, '2026-08-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-08-01 05:15:00'),
(409, 428, 71, 3800.00, 'gcash', 'GCash', '1000060724918', '2026-09-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-03 02:00:00', NULL, '2026-09-02 06:15:00'),
(410, 429, 71, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1259', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:21', NULL, '2026-10-01 07:15:00'),
(411, 430, 72, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-15 08:15:00'),
(412, 431, 72, 3800.00, 'gcash', 'GCash', '1000060821460', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 09:15:00'),
(413, 432, 72, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1261', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 01:15:00'),
(414, 433, 72, 3800.00, 'gcash', 'GCash', '1000060918002', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 02:15:00'),
(415, 434, 72, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 03:15:00'),
(416, 435, 72, 3800.00, 'gcash', 'GCash', '1000060966273', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:22', NULL, '2026-10-01 04:15:00'),
(417, 436, 73, 7600.00, 'cash', 'Cash Payment', NULL, '2025-12-21', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-21 05:15:00'),
(418, 437, 73, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1264', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 06:15:00'),
(419, 438, 73, 3800.00, 'gcash', 'GCash', '1000061062815', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 07:15:00'),
(420, 439, 73, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(421, 440, 73, 3800.00, 'gcash', 'GCash', '1000061111086', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 09:15:00'),
(422, 441, 73, 4180.00, 'bank_transfer', 'BDO', 'BDO-261001-1267', '2026-05-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-07 02:00:00', NULL, '2026-05-06 01:15:00'),
(423, 442, 73, 3800.00, 'gcash', 'GCash', '1000061207628', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 02:15:00'),
(424, 443, 73, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(425, 444, 73, 3800.00, 'gcash', 'GCash', '1000061255899', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 04:15:00'),
(426, 445, 73, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1270', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 05:15:00'),
(427, 446, 73, 3800.00, 'gcash', 'GCash', '1000061352441', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:23', NULL, '2026-10-01 06:15:00'),
(428, 447, 74, 7600.00, 'cash', 'Cash Payment', NULL, '2026-08-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-02 07:15:00'),
(429, 448, 74, 3800.00, 'gcash', 'GCash', '1000061400712', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 08:15:00'),
(430, 449, 74, 3800.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 09:15:00'),
(431, 450, 75, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-18', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-18 01:15:00'),
(432, 451, 75, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 02:15:00'),
(433, 452, 75, 3800.00, 'gcash', 'GCash', '1000061448983', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 03:15:00'),
(434, 453, 75, 4180.00, 'bank_transfer', 'BDO', 'BDO-261001-1274', '2026-04-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-07 02:00:00', NULL, '2026-04-06 04:15:00'),
(435, 454, 75, 3800.00, 'gcash', 'GCash', '1000061545525', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 05:15:00'),
(436, 455, 75, 3800.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 06:15:00'),
(437, 456, 75, 3800.00, 'gcash', 'GCash', '1000061593796', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 07:15:00'),
(438, 457, 75, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1277', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 08:15:00'),
(439, 458, 75, 3800.00, 'gcash', 'GCash', '1000061690338', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 09:15:00'),
(440, 459, 75, 3800.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 01:15:00'),
(441, 460, 76, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-07', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-07 02:15:00'),
(442, 461, 76, 3800.00, 'gcash', 'GCash', '1000061738609', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 03:15:00'),
(443, 462, 76, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1280', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 04:15:00'),
(444, 463, 76, 3800.00, 'gcash', 'GCash', '1000061835151', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 05:15:00'),
(445, 464, 76, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 06:15:00'),
(446, 465, 76, 3800.00, 'gcash', 'GCash', '1000061883422', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 07:15:00'),
(447, 466, 76, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1283', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 08:15:00'),
(448, 467, 76, 3800.00, 'gcash', 'GCash', '1000061979964', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 09:15:00'),
(449, 468, 76, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 01:15:00'),
(450, 469, 76, 3800.00, 'gcash', 'GCash', '1000062028235', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:26', NULL, '2026-10-01 02:15:00'),
(451, 470, 77, 7600.00, 'cash', 'Cash Payment', NULL, '2026-06-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-06-15 03:15:00'),
(452, 471, 77, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1286', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 04:15:00'),
(453, 472, 77, 3800.00, 'gcash', 'GCash', '1000062124777', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 05:15:00'),
(454, 473, 77, 3800.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 06:15:00'),
(455, 475, 78, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-07', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-07 08:15:00'),
(456, 476, 78, 3800.00, 'gcash', 'GCash', '1000062173048', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 09:15:00'),
(457, 477, 78, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 01:15:00'),
(458, 478, 78, 3800.00, 'gcash', 'GCash', '1000062221319', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 02:15:00'),
(459, 479, 78, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1290', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 03:15:00'),
(460, 480, 78, 3800.00, 'gcash', 'GCash', '1000062317861', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:28', NULL, '2026-10-01 04:15:00'),
(461, 481, 79, 7600.00, 'cash', 'Cash Payment', NULL, '2026-05-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-05-10 05:15:00'),
(462, 482, 79, 3800.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 06:15:00'),
(463, 483, 79, 3800.00, 'gcash', 'GCash', '1000062366132', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 07:15:00'),
(464, 484, 79, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1293', '2026-08-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-04 02:00:00', NULL, '2026-08-03 08:15:00'),
(465, 485, 79, 3800.00, 'gcash', 'GCash', '1000062462674', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 09:15:00'),
(466, 487, 80, 7600.00, 'cash', 'Cash Payment', NULL, '2025-11-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-11-13 02:15:00'),
(467, 488, 80, 4180.00, 'gcash', 'GCash', '1000062510945', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 03:15:00'),
(468, 489, 80, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1296', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 04:15:00'),
(469, 490, 80, 4180.00, 'gcash', 'GCash', '1000062607487', '2026-02-11', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-12 02:00:00', NULL, '2026-02-11 05:15:00'),
(470, 491, 80, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 06:15:00'),
(471, 492, 80, 3800.00, 'gcash', 'GCash', '1000062655758', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 07:15:00'),
(472, 493, 80, 4180.00, 'bank_transfer', 'BDO', 'BDO-261001-1299', '2026-05-08', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-09 02:00:00', NULL, '2026-05-08 08:15:00'),
(473, 494, 80, 3800.00, 'gcash', 'GCash', '1000062752300', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 09:15:00'),
(474, 495, 80, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 01:15:00'),
(475, 496, 80, 3800.00, 'gcash', 'GCash', '1000062800571', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 02:15:00'),
(476, 497, 80, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1302', '2026-09-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-04 02:00:00', NULL, '2026-09-03 03:15:00'),
(477, 499, 81, 8400.00, 'cash', 'Cash Payment', NULL, '2025-12-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-22 05:15:00'),
(478, 500, 81, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1303', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 06:15:00'),
(479, 501, 81, 4200.00, 'gcash', 'GCash', '1000062945384', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 07:15:00'),
(480, 502, 81, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(481, 503, 81, 4200.00, 'gcash', 'GCash', '1000062993655', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 09:15:00'),
(482, 504, 81, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1306', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 01:15:00'),
(483, 505, 81, 4200.00, 'gcash', 'GCash', '1000063090197', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 02:15:00'),
(484, 506, 81, 4200.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(485, 507, 81, 4200.00, 'gcash', 'GCash', '1000063138468', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 04:15:00'),
(486, 508, 81, 4620.00, 'bank_transfer', 'BDO', 'BDO-261001-1309', '2026-09-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-07 02:00:00', NULL, '2026-09-06 05:15:00'),
(487, 510, 82, 8400.00, 'cash', 'Cash Payment', NULL, '2026-08-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-03 07:15:00'),
(488, 511, 82, 4200.00, 'gcash', 'GCash', '1000063235010', '2026-09-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-09-05 02:00:00', NULL, '2026-09-04 08:15:00'),
(489, 512, 82, 4200.00, 'cash', 'Cash Payment', NULL, '2026-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-10-01 09:15:00'),
(490, 513, 83, 8400.00, 'cash', 'Cash Payment', NULL, '2026-08-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-08-10 01:15:00'),
(491, 514, 83, 4200.00, 'cash', 'Cash Payment', NULL, '2026-09-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-09-01 02:15:00'),
(492, 515, 83, 4200.00, 'gcash', 'GCash', '1000063283281', '2026-10-01', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-10-01 15:14:34', NULL, '2026-10-01 03:15:00'),
(493, 516, 84, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-22 04:15:00'),
(494, 517, 84, 4500.00, 'gcash', 'GCash', '1000063331552', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 05:15:00'),
(495, 518, 84, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1313', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 06:15:00'),
(496, 519, 84, 4500.00, 'gcash', 'GCash', '1000063428094', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 07:15:00'),
(497, 520, 84, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(498, 521, 84, 4500.00, 'gcash', 'GCash', '1000063476365', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 09:15:00'),
(499, 522, 84, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1316', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 01:15:00'),
(500, 523, 85, 9000.00, 'cash', 'Cash Payment', NULL, '2025-10-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-14 02:15:00'),
(501, 524, 85, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1318', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 03:15:00'),
(502, 525, 85, 4500.00, 'gcash', 'GCash', '1000063669449', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 04:15:00'),
(503, 526, 85, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 05:15:00'),
(504, 527, 85, 4500.00, 'gcash', 'GCash', '1000063717720', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 06:15:00'),
(505, 528, 86, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-03 07:15:00'),
(506, 529, 86, 4500.00, 'gcash', 'GCash', '1000063814262', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 08:15:00'),
(507, 530, 86, 4500.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 09:15:00'),
(508, 531, 86, 4500.00, 'gcash', 'GCash', '1000063862533', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 01:15:00'),
(509, 532, 87, 9000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-01 02:15:00'),
(510, 533, 87, 4500.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 03:15:00'),
(511, 534, 87, 4500.00, 'gcash', 'GCash', '1000063959075', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 04:15:00'),
(512, 535, 87, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1326', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 05:15:00'),
(513, 536, 88, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-04', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-04 06:15:00'),
(514, 537, 88, 4500.00, 'gcash', 'GCash', '1000064103888', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 07:15:00'),
(515, 538, 88, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1329', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 08:15:00'),
(516, 539, 88, 4950.00, 'gcash', 'GCash', '1000064200430', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 09:15:00'),
(517, 540, 88, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 01:15:00'),
(518, 541, 89, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 02:15:00'),
(519, 542, 89, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1332', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 03:15:00'),
(520, 543, 89, 4950.00, 'gcash', 'GCash', '1000064345243', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 04:15:00'),
(521, 544, 89, 4500.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 05:15:00'),
(522, 545, 90, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 06:15:00'),
(523, 546, 90, 4950.00, 'gcash', 'GCash', '1000064441785', '2025-10-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-10 02:00:00', NULL, '2025-10-09 07:15:00'),
(524, 547, 90, 4500.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 08:15:00'),
(525, 548, 90, 4500.00, 'gcash', 'GCash', '1000064490056', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 09:15:00'),
(526, 549, 90, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1337', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 01:15:00'),
(527, 550, 90, 4500.00, 'gcash', 'GCash', '1000064586598', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 02:15:00'),
(528, 551, 91, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-10 03:15:00'),
(529, 552, 91, 4500.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 04:15:00'),
(530, 553, 91, 4500.00, 'gcash', 'GCash', '1000064683140', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 05:15:00'),
(531, 554, 91, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1341', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 06:15:00'),
(532, 555, 91, 4500.00, 'gcash', 'GCash', '1000064779682', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 07:15:00'),
(533, 556, 91, 4500.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 08:15:00'),
(534, 557, 92, 9000.00, 'cash', 'Cash Payment', NULL, '2025-10-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-15 09:15:00'),
(535, 558, 92, 4500.00, 'gcash', 'GCash', '1000064876224', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 01:15:00'),
(536, 559, 92, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1345', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 02:15:00'),
(537, 560, 92, 4500.00, 'gcash', 'GCash', '1000064972766', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 03:15:00'),
(538, 561, 92, 4500.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 04:15:00'),
(539, 562, 92, 4500.00, 'gcash', 'GCash', '1000065021037', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 05:15:00'),
(540, 563, 92, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1348', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(541, 564, 92, 4500.00, 'gcash', 'GCash', '1000065117579', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(542, 565, 93, 9000.00, 'cash', 'Cash Payment', NULL, '2025-10-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-09 08:15:00'),
(543, 566, 93, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1351', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 09:15:00'),
(544, 567, 93, 4500.00, 'gcash', 'GCash', '1000065262392', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 01:15:00'),
(545, 568, 93, 4500.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 02:15:00'),
(546, 569, 93, 4500.00, 'gcash', 'GCash', '1000065310663', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 03:15:00'),
(547, 570, 93, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1354', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 04:15:00');
INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `payment_method_label`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(548, 571, 93, 4500.00, 'gcash', 'GCash', '1000065407205', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 05:15:00'),
(549, 572, 93, 4950.00, 'cash', 'Cash Payment', NULL, '2026-05-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-06 06:15:00'),
(550, 573, 93, 4500.00, 'gcash', 'GCash', '1000065455476', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 07:15:00'),
(551, 574, 94, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-29 08:15:00'),
(552, 575, 94, 4500.00, 'gcash', 'GCash', '1000065552018', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 09:15:00'),
(553, 576, 94, 4500.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 01:15:00'),
(554, 577, 94, 4500.00, 'gcash', 'GCash', '1000065600289', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 02:15:00'),
(555, 578, 95, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-22 03:15:00'),
(556, 579, 95, 4500.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 04:15:00'),
(557, 580, 95, 4500.00, 'gcash', 'GCash', '1000065696831', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 05:15:00'),
(558, 581, 95, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1362', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 06:15:00'),
(559, 582, 95, 4500.00, 'gcash', 'GCash', '1000065793373', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 07:15:00'),
(560, 583, 95, 4950.00, 'cash', 'Cash Payment', NULL, '2026-02-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-06 08:15:00'),
(561, 584, 95, 4500.00, 'gcash', 'GCash', '1000065841644', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 09:15:00'),
(562, 585, 95, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1365', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 01:15:00'),
(563, 586, 96, 9000.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-20 02:15:00'),
(564, 587, 96, 4500.00, 'gcash', 'GCash', '1000065986457', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 03:15:00'),
(565, 588, 96, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1368', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 04:15:00'),
(566, 589, 96, 4500.00, 'gcash', 'GCash', '1000066082999', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 05:15:00'),
(567, 590, 96, 4950.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-06 06:15:00'),
(568, 591, 96, 4500.00, 'gcash', 'GCash', '1000066131270', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 07:15:00'),
(569, 592, 97, 9000.00, 'cash', 'Cash Payment', NULL, '2025-10-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-09 08:15:00'),
(570, 593, 97, 4500.00, 'bank_transfer', 'BDO', 'BDO-261001-1372', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 09:15:00'),
(571, 594, 97, 4500.00, 'gcash', 'GCash', '1000066276083', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 01:15:00'),
(572, 595, 97, 4950.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-06 02:15:00'),
(573, 596, 98, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-16', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-16 03:15:00'),
(574, 597, 98, 4000.00, 'gcash', 'GCash', '1000066372625', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 04:15:00'),
(575, 598, 98, 4400.00, 'cash', 'Cash Payment', NULL, '2025-11-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-06 05:15:00'),
(576, 599, 98, 4000.00, 'gcash', 'GCash', '1000066420896', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 06:15:00'),
(577, 600, 99, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-20 07:15:00'),
(578, 601, 99, 4400.00, 'cash', 'Cash Payment', NULL, '2025-10-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-06 08:15:00'),
(579, 602, 99, 4000.00, 'gcash', 'GCash', '1000066517438', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 09:15:00'),
(580, 603, 99, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1379', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 01:15:00'),
(581, 604, 99, 4000.00, 'gcash', 'GCash', '1000066613980', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 02:15:00'),
(582, 605, 100, 8000.00, 'cash', 'Cash Payment', NULL, '2026-02-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-11 03:15:00'),
(583, 606, 100, 4000.00, 'gcash', 'GCash', '1000066710522', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 04:15:00'),
(584, 607, 100, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1383', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 05:15:00'),
(585, 608, 100, 4000.00, 'gcash', 'GCash', '1000066807064', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 06:15:00'),
(586, 609, 101, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-12 07:15:00'),
(587, 610, 101, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1386', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 08:15:00'),
(588, 611, 101, 4000.00, 'gcash', 'GCash', '1000066951877', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 09:15:00'),
(589, 612, 101, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 01:15:00'),
(590, 613, 101, 4000.00, 'gcash', 'GCash', '1000067000148', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 02:15:00'),
(591, 614, 101, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1389', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 03:15:00'),
(592, 615, 101, 4000.00, 'gcash', 'GCash', '1000067096690', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 04:15:00'),
(593, 616, 101, 4000.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 05:15:00'),
(594, 617, 102, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 06:15:00'),
(595, 618, 102, 4000.00, 'gcash', 'GCash', '1000067193232', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 07:15:00'),
(596, 619, 102, 4000.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 08:15:00'),
(597, 620, 102, 4000.00, 'gcash', 'GCash', '1000067241503', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 09:15:00'),
(598, 621, 102, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1394', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 01:15:00'),
(599, 622, 102, 4000.00, 'gcash', 'GCash', '1000067338045', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 02:15:00'),
(600, 623, 103, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-08 03:15:00'),
(601, 624, 103, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 04:15:00'),
(602, 625, 103, 4000.00, 'gcash', 'GCash', '1000067434587', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 05:15:00'),
(603, 626, 103, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1398', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 06:15:00'),
(604, 627, 103, 4000.00, 'gcash', 'GCash', '1000067531129', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 07:15:00'),
(605, 628, 103, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 08:15:00'),
(606, 629, 103, 4400.00, 'gcash', 'GCash', '1000067579400', '2026-03-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-10 02:00:00', NULL, '2026-03-09 09:15:00'),
(607, 630, 103, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1401', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 01:15:00'),
(608, 631, 104, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-03 02:15:00'),
(609, 632, 104, 4000.00, 'gcash', 'GCash', '1000067724213', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 03:15:00'),
(610, 633, 104, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1404', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 04:15:00'),
(611, 634, 104, 4000.00, 'gcash', 'GCash', '1000067820755', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 05:15:00'),
(612, 635, 104, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 06:15:00'),
(613, 636, 105, 8000.00, 'cash', 'Cash Payment', NULL, '2026-03-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-08 07:15:00'),
(614, 637, 105, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1407', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 08:15:00'),
(615, 638, 105, 4000.00, 'gcash', 'GCash', '1000067965568', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 09:15:00'),
(616, 639, 105, 4000.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 01:15:00'),
(617, 640, 106, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-15 02:15:00'),
(618, 641, 106, 4200.00, 'gcash', 'GCash', '1000068062110', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 03:15:00'),
(619, 642, 106, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 04:15:00'),
(620, 643, 106, 4620.00, 'gcash', 'GCash', '1000068110381', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 05:15:00'),
(621, 644, 106, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1412', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 06:15:00'),
(622, 645, 106, 4200.00, 'gcash', 'GCash', '1000068206923', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 07:15:00'),
(623, 646, 106, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(624, 647, 106, 4200.00, 'gcash', 'GCash', '1000068255194', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 09:15:00'),
(625, 648, 106, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1415', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 01:15:00'),
(626, 649, 107, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-19', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-19 02:15:00'),
(627, 650, 107, 4200.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 03:15:00'),
(628, 651, 107, 4620.00, 'gcash', 'GCash', '1000068400007', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 04:15:00'),
(629, 652, 107, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1418', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 05:15:00'),
(630, 653, 107, 4200.00, 'gcash', 'GCash', '1000068496549', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 06:15:00'),
(631, 654, 108, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-01 07:15:00'),
(632, 655, 108, 4620.00, 'gcash', 'GCash', '1000068593091', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 08:15:00'),
(633, 656, 108, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1422', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 09:15:00'),
(634, 657, 108, 4200.00, 'gcash', 'GCash', '1000068689633', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 01:15:00'),
(635, 658, 108, 4200.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 02:15:00'),
(636, 659, 108, 4200.00, 'gcash', 'GCash', '1000068737904', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 03:15:00'),
(637, 660, 108, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1425', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 04:15:00'),
(638, 661, 108, 4200.00, 'gcash', 'GCash', '1000068834446', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 05:15:00'),
(639, 662, 109, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-05 06:15:00'),
(640, 663, 109, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1428', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 07:15:00'),
(641, 664, 109, 4200.00, 'gcash', 'GCash', '1000068979259', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 08:15:00'),
(642, 665, 109, 4200.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 09:15:00'),
(643, 666, 110, 8400.00, 'cash', 'Cash Payment', NULL, '2026-01-16', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-16 01:15:00'),
(644, 667, 110, 4200.00, 'gcash', 'GCash', '1000069075801', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 02:15:00'),
(645, 668, 110, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 03:15:00'),
(646, 669, 110, 4200.00, 'gcash', 'GCash', '1000069124072', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 04:15:00'),
(647, 670, 110, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1433', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 05:15:00'),
(648, 671, 111, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-14 06:15:00'),
(649, 672, 111, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 07:15:00'),
(650, 673, 111, 4200.00, 'gcash', 'GCash', '1000069268885', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 08:15:00'),
(651, 674, 111, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1436', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 09:15:00'),
(652, 675, 111, 4200.00, 'gcash', 'GCash', '1000069365427', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 01:15:00'),
(653, 676, 111, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 02:15:00'),
(654, 677, 111, 4200.00, 'gcash', 'GCash', '1000069413698', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 03:15:00'),
(655, 678, 112, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-05 04:15:00'),
(656, 679, 112, 4200.00, 'gcash', 'GCash', '1000069510240', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 05:15:00'),
(657, 680, 112, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1441', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 06:15:00'),
(658, 681, 112, 4200.00, 'gcash', 'GCash', '1000069606782', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 07:15:00'),
(659, 682, 112, 4200.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(660, 683, 112, 4200.00, 'gcash', 'GCash', '1000069655053', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 09:15:00'),
(661, 684, 113, 8400.00, 'cash', 'Cash Payment', NULL, '2026-04-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-04-02 01:15:00'),
(662, 685, 113, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1445', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 02:15:00'),
(663, 686, 113, 4200.00, 'gcash', 'GCash', '1000069799866', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 03:15:00'),
(664, 687, 113, 4200.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 04:15:00'),
(665, 688, 113, 4200.00, 'gcash', 'GCash', '1000069848137', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 05:15:00'),
(666, 689, 114, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-13 06:15:00'),
(667, 690, 114, 4200.00, 'gcash', 'GCash', '1000069944679', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 07:15:00'),
(668, 691, 114, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 08:15:00'),
(669, 692, 114, 4200.00, 'gcash', 'GCash', '1000069992950', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 09:15:00'),
(670, 693, 114, 4620.00, 'bank_transfer', 'BDO', 'BDO-261001-1451', '2026-01-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-07 02:00:00', NULL, '2026-01-06 01:15:00'),
(671, 694, 114, 4200.00, 'gcash', 'GCash', '1000070089492', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 02:15:00'),
(672, 695, 114, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 03:15:00'),
(673, 696, 114, 4200.00, 'gcash', 'GCash', '1000070137763', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 04:15:00'),
(674, 697, 114, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1454', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 05:15:00'),
(675, 698, 115, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-10 06:15:00'),
(676, 699, 115, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 07:15:00'),
(677, 700, 115, 4000.00, 'gcash', 'GCash', '1000070282576', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 08:15:00'),
(678, 701, 115, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1457', '2025-12-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-07 02:00:00', NULL, '2025-12-06 09:15:00'),
(679, 702, 115, 4000.00, 'gcash', 'GCash', '1000070379118', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 01:15:00'),
(680, 703, 115, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 02:15:00'),
(681, 704, 115, 4000.00, 'gcash', 'GCash', '1000070427389', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 03:15:00'),
(682, 705, 116, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-24 04:15:00'),
(683, 706, 116, 4000.00, 'gcash', 'GCash', '1000070523931', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 05:15:00'),
(684, 707, 116, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1462', '2025-11-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-07 02:00:00', NULL, '2025-11-06 06:15:00'),
(685, 708, 116, 4000.00, 'gcash', 'GCash', '1000070620473', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 07:15:00'),
(686, 709, 117, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-09 08:15:00'),
(687, 710, 117, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1465', '2025-10-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-07 02:00:00', NULL, '2025-10-06 09:15:00'),
(688, 711, 117, 4000.00, 'gcash', 'GCash', '1000070765286', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 01:15:00'),
(689, 712, 117, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 02:15:00'),
(690, 713, 117, 4000.00, 'gcash', 'GCash', '1000070813557', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 03:15:00'),
(691, 714, 117, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1468', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 04:15:00'),
(692, 715, 118, 8000.00, 'cash', 'Cash Payment', NULL, '2026-03-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-14 05:15:00'),
(693, 716, 118, 4000.00, 'gcash', 'GCash', '1000070958370', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 06:15:00'),
(694, 717, 118, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 07:15:00'),
(695, 718, 118, 4000.00, 'gcash', 'GCash', '1000071006641', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 08:15:00'),
(696, 719, 118, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1472', '2026-07-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-04 02:00:00', NULL, '2026-07-03 09:15:00'),
(697, 720, 118, 4000.00, 'gcash', 'GCash', '1000071103183', '2026-08-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-05 02:00:00', NULL, '2026-08-04 01:15:00'),
(698, 721, 119, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 02:15:00'),
(699, 722, 119, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 03:15:00'),
(700, 723, 119, 4000.00, 'gcash', 'GCash', '1000071199725', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 04:15:00'),
(701, 724, 119, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1476', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 05:15:00'),
(702, 725, 119, 4000.00, 'gcash', 'GCash', '1000071296267', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 06:15:00'),
(703, 726, 119, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 07:15:00'),
(704, 727, 119, 4000.00, 'gcash', 'GCash', '1000071344538', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 08:15:00'),
(705, 728, 120, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-29 09:15:00'),
(706, 729, 120, 4000.00, 'gcash', 'GCash', '1000071441080', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 01:15:00'),
(707, 730, 120, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1481', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 02:15:00'),
(708, 731, 120, 4000.00, 'gcash', 'GCash', '1000071537622', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 03:15:00'),
(709, 732, 120, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 04:15:00'),
(710, 733, 120, 4000.00, 'gcash', 'GCash', '1000071585893', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 05:15:00'),
(711, 734, 120, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1484', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 06:15:00'),
(712, 735, 120, 4400.00, 'gcash', 'GCash', '1000071682435', '2026-04-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-10 02:00:00', NULL, '2026-04-09 07:15:00'),
(713, 736, 120, 4000.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 08:15:00'),
(714, 737, 121, 8000.00, 'cash', 'Cash Payment', NULL, '2025-10-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-15 09:15:00'),
(715, 738, 121, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1487', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(716, 739, 121, 4000.00, 'gcash', 'GCash', '1000071827248', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 02:15:00'),
(717, 740, 121, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 03:15:00'),
(718, 741, 122, 8000.00, 'cash', 'Cash Payment', NULL, '2026-02-17', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-17 04:15:00'),
(719, 742, 122, 4000.00, 'gcash', 'GCash', '1000071923790', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 05:15:00'),
(720, 743, 122, 4000.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 06:15:00'),
(721, 744, 122, 4000.00, 'gcash', 'GCash', '1000071972061', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 07:15:00'),
(722, 745, 123, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-13 08:15:00'),
(723, 746, 123, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 09:15:00'),
(724, 747, 123, 4000.00, 'gcash', 'GCash', '1000072068603', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 01:15:00'),
(725, 748, 123, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1494', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 02:15:00'),
(726, 749, 123, 4400.00, 'gcash', 'GCash', '1000072165145', '2026-01-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-10 02:00:00', NULL, '2026-01-09 03:15:00'),
(727, 750, 123, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 04:15:00'),
(728, 751, 123, 4000.00, 'gcash', 'GCash', '1000072213416', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 05:15:00'),
(729, 752, 123, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1497', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(730, 753, 123, 4000.00, 'gcash', 'GCash', '1000072309958', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(731, 754, 124, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-24 08:15:00'),
(732, 755, 124, 4000.00, 'gcash', 'GCash', '1000072406500', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 09:15:00'),
(733, 756, 124, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1501', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(734, 757, 124, 4400.00, 'gcash', 'GCash', '1000072503042', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 02:15:00'),
(735, 758, 125, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-22', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-22 03:15:00'),
(736, 759, 125, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1504', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 04:15:00'),
(737, 760, 125, 4400.00, 'gcash', 'GCash', '1000072647855', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 05:15:00'),
(738, 761, 125, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 06:15:00'),
(739, 762, 126, 8000.00, 'cash', 'Cash Payment', NULL, '2026-01-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-13 07:15:00'),
(740, 763, 126, 4400.00, 'gcash', 'GCash', '1000072744397', '2026-02-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-10 02:00:00', NULL, '2026-02-09 08:15:00'),
(741, 764, 126, 4000.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 09:15:00'),
(742, 765, 126, 4000.00, 'gcash', 'GCash', '1000072792668', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 01:15:00'),
(743, 766, 126, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1509', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 02:15:00'),
(744, 767, 126, 4000.00, 'gcash', 'GCash', '1000072889210', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 03:15:00'),
(745, 768, 127, 8000.00, 'cash', 'Cash Payment', NULL, '2025-10-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-08 04:15:00'),
(746, 769, 127, 4000.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 05:15:00'),
(747, 770, 127, 4000.00, 'gcash', 'GCash', '1000072985752', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 06:15:00'),
(748, 771, 127, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1513', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 07:15:00'),
(749, 772, 128, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-20 08:15:00'),
(750, 773, 128, 4000.00, 'gcash', 'GCash', '1000073130565', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 09:15:00'),
(751, 774, 128, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1516', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(752, 775, 128, 4000.00, 'gcash', 'GCash', '1000073227107', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 02:15:00'),
(753, 776, 128, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 03:15:00'),
(754, 777, 128, 4000.00, 'gcash', 'GCash', '1000073275378', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 04:15:00'),
(755, 778, 128, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1519', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 05:15:00'),
(756, 779, 128, 4000.00, 'gcash', 'GCash', '1000073371920', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 06:15:00'),
(757, 780, 128, 4400.00, 'cash', 'Cash Payment', NULL, '2026-05-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-06 07:15:00'),
(758, 781, 129, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-12 08:15:00'),
(759, 782, 129, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1522', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 09:15:00'),
(760, 783, 129, 4200.00, 'gcash', 'GCash', '1000073516733', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 01:15:00'),
(761, 784, 129, 4200.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 02:15:00'),
(762, 785, 130, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-03 03:15:00'),
(763, 786, 130, 4200.00, 'gcash', 'GCash', '1000073613275', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 04:15:00'),
(764, 787, 130, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 05:15:00'),
(765, 788, 130, 4200.00, 'gcash', 'GCash', '1000073661546', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 06:15:00'),
(766, 789, 130, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1527', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 07:15:00'),
(767, 790, 131, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-09 08:15:00'),
(768, 791, 131, 4200.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 09:15:00'),
(769, 792, 131, 4200.00, 'gcash', 'GCash', '1000073806359', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 01:15:00'),
(770, 793, 131, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1530', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 02:15:00'),
(771, 794, 131, 4200.00, 'gcash', 'GCash', '1000073902901', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 03:15:00'),
(772, 795, 131, 4620.00, 'cash', 'Cash Payment', NULL, '2026-02-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-06 04:15:00'),
(773, 796, 131, 4200.00, 'gcash', 'GCash', '1000073951172', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 05:15:00'),
(774, 797, 131, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1533', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 06:15:00'),
(775, 798, 131, 4200.00, 'gcash', 'GCash', '1000074047714', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 07:15:00'),
(776, 799, 132, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-29 08:15:00'),
(777, 800, 132, 4200.00, 'gcash', 'GCash', '1000074144256', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 09:15:00'),
(778, 801, 132, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1537', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(779, 802, 132, 4200.00, 'gcash', 'GCash', '1000074240798', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 02:15:00'),
(780, 803, 132, 4620.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-06 03:15:00'),
(781, 804, 132, 4200.00, 'gcash', 'GCash', '1000074289069', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 04:15:00'),
(782, 805, 133, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-26', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-26 05:15:00'),
(783, 806, 133, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1541', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 06:15:00'),
(784, 807, 133, 3800.00, 'gcash', 'GCash', '1000074433882', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 07:15:00'),
(785, 808, 133, 4180.00, 'cash', 'Cash Payment', NULL, '2025-12-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-06 08:15:00'),
(786, 809, 134, 7600.00, 'cash', 'Cash Payment', NULL, '2025-10-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-06 09:15:00'),
(787, 810, 134, 3800.00, 'gcash', 'GCash', '1000074530424', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 01:15:00'),
(788, 811, 134, 4180.00, 'cash', 'Cash Payment', NULL, '2025-12-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-06 02:15:00'),
(789, 812, 134, 3800.00, 'gcash', 'GCash', '1000074578695', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 03:15:00'),
(790, 813, 134, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1546', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 04:15:00'),
(791, 814, 135, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-09 05:15:00'),
(792, 815, 135, 4180.00, 'cash', 'Cash Payment', NULL, '2025-10-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-06 06:15:00'),
(793, 816, 135, 3800.00, 'gcash', 'GCash', '1000074723508', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 07:15:00'),
(794, 817, 135, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1549', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 08:15:00'),
(795, 818, 135, 3800.00, 'gcash', 'GCash', '1000074820050', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 09:15:00'),
(796, 819, 135, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 01:15:00'),
(797, 820, 135, 3800.00, 'gcash', 'GCash', '1000074868321', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 02:15:00'),
(798, 821, 135, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1552', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 03:15:00'),
(799, 822, 136, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-24 04:15:00'),
(800, 823, 136, 3800.00, 'gcash', 'GCash', '1000075013134', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 05:15:00'),
(801, 824, 136, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1555', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 06:15:00'),
(802, 825, 136, 3800.00, 'gcash', 'GCash', '1000075109676', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 07:15:00'),
(803, 826, 136, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 08:15:00'),
(804, 827, 137, 7600.00, 'cash', 'Cash Payment', NULL, '2026-03-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-03-13 09:15:00'),
(805, 828, 137, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1558', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 01:15:00'),
(806, 829, 137, 3800.00, 'gcash', 'GCash', '1000075254489', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 02:15:00'),
(807, 830, 137, 3800.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 03:15:00'),
(808, 831, 137, 3800.00, 'gcash', 'GCash', '1000075302760', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 04:15:00'),
(809, 832, 138, 7600.00, 'cash', 'Cash Payment', NULL, '2025-10-12', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-12 05:15:00'),
(810, 833, 138, 3800.00, 'gcash', 'GCash', '1000075399302', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 06:15:00'),
(811, 834, 138, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 07:15:00'),
(812, 835, 138, 3800.00, 'gcash', 'GCash', '1000075447573', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 08:15:00'),
(813, 836, 139, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-20 09:15:00'),
(814, 837, 139, 3800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 01:15:00'),
(815, 838, 139, 3800.00, 'gcash', 'GCash', '1000075544115', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 02:15:00'),
(816, 839, 139, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1566', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 03:15:00'),
(817, 840, 139, 3800.00, 'gcash', 'GCash', '1000075640657', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 04:15:00'),
(818, 841, 139, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 05:15:00'),
(819, 842, 139, 4180.00, 'gcash', 'GCash', '1000075688928', '2026-03-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-10 02:00:00', NULL, '2026-03-09 06:15:00'),
(820, 843, 139, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1569', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 07:15:00');
INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `payment_method_label`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(821, 844, 140, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-23', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-23 08:15:00'),
(822, 845, 140, 3800.00, 'gcash', 'GCash', '1000075833741', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 09:15:00'),
(823, 846, 140, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1572', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(824, 847, 140, 3800.00, 'gcash', 'GCash', '1000075930283', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 02:15:00'),
(825, 848, 140, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 03:15:00'),
(826, 849, 141, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-29 04:15:00'),
(827, 850, 141, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1575', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 05:15:00'),
(828, 851, 141, 3800.00, 'gcash', 'GCash', '1000076075096', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 06:15:00'),
(829, 852, 141, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 07:15:00'),
(830, 853, 141, 4180.00, 'gcash', 'GCash', '1000076123367', '2026-01-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-10 02:00:00', NULL, '2026-01-09 08:15:00'),
(831, 854, 141, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1578', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 09:15:00'),
(832, 855, 141, 3800.00, 'gcash', 'GCash', '1000076219909', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 01:15:00'),
(833, 856, 141, 3800.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 02:15:00'),
(834, 857, 142, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-09 03:15:00'),
(835, 858, 142, 3800.00, 'gcash', 'GCash', '1000076316451', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 04:15:00'),
(836, 859, 142, 3800.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 05:15:00'),
(837, 860, 142, 4180.00, 'gcash', 'GCash', '1000076364722', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 06:15:00'),
(838, 861, 143, 7600.00, 'cash', 'Cash Payment', NULL, '2026-02-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-08 07:15:00'),
(839, 862, 143, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(840, 863, 143, 4180.00, 'gcash', 'GCash', '1000076461264', '2026-04-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-10 02:00:00', NULL, '2026-04-09 09:15:00'),
(841, 864, 143, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1585', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 01:15:00'),
(842, 865, 143, 3800.00, 'gcash', 'GCash', '1000076557806', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 02:15:00'),
(843, 866, 143, 3800.00, 'cash', 'Cash Payment', NULL, '2026-07-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-07-01 03:15:00'),
(844, 867, 143, 3800.00, 'gcash', 'GCash', '1000076606077', '2026-08-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-08-03 02:00:00', NULL, '2026-08-02 04:15:00'),
(845, 868, 144, 8400.00, 'cash', 'Cash Payment', NULL, '2025-08-31', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-08-31 05:15:00'),
(846, 869, 144, 4620.00, 'gcash', 'GCash', '1000076702619', '2025-10-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-10 02:00:00', NULL, '2025-10-09 06:15:00'),
(847, 870, 144, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1590', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 07:15:00'),
(848, 871, 145, 8400.00, 'cash', 'Cash Payment', NULL, '2025-12-23', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-12-23 08:15:00'),
(849, 872, 145, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1592', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 09:15:00'),
(850, 873, 145, 4200.00, 'gcash', 'GCash', '1000076895703', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 01:15:00'),
(851, 874, 145, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 02:15:00'),
(852, 875, 146, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-05 03:15:00'),
(853, 876, 146, 4200.00, 'gcash', 'GCash', '1000076992245', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 04:15:00'),
(854, 877, 146, 4200.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 05:15:00'),
(855, 878, 146, 4200.00, 'gcash', 'GCash', '1000077040516', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 06:15:00'),
(856, 879, 146, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1597', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 07:15:00'),
(857, 880, 146, 4200.00, 'gcash', 'GCash', '1000077137058', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 08:15:00'),
(858, 881, 146, 4200.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 09:15:00'),
(859, 882, 146, 4200.00, 'gcash', 'GCash', '1000077185329', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 01:15:00'),
(860, 883, 147, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-05 02:15:00'),
(861, 884, 147, 4200.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 03:15:00'),
(862, 885, 147, 4200.00, 'gcash', 'GCash', '1000077281871', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 04:15:00'),
(863, 886, 147, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1602', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 05:15:00'),
(864, 887, 148, 8400.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-01 06:15:00'),
(865, 888, 148, 4200.00, 'gcash', 'GCash', '1000077426684', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 07:15:00'),
(866, 889, 148, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1605', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 08:15:00'),
(867, 890, 148, 4200.00, 'gcash', 'GCash', '1000077523226', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 09:15:00'),
(868, 891, 149, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 01:15:00'),
(869, 892, 149, 4000.00, 'bank_transfer', 'BDO', 'BDO-261001-1608', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 02:15:00'),
(870, 893, 149, 4000.00, 'gcash', 'GCash', '1000077668039', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 03:15:00'),
(871, 894, 149, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 04:15:00'),
(872, 895, 150, 8000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-01 05:15:00'),
(873, 896, 150, 4000.00, 'gcash', 'GCash', '1000077764581', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 06:15:00'),
(874, 897, 150, 4000.00, 'cash', 'Cash Payment', NULL, '2026-04-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-04-01 07:15:00'),
(875, 898, 150, 4000.00, 'gcash', 'GCash', '1000077812852', '2026-05-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-03 02:00:00', NULL, '2026-05-02 08:15:00'),
(876, 899, 150, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1613', '2026-06-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-07 02:00:00', NULL, '2026-06-06 09:15:00'),
(877, 900, 151, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-26', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-26 01:15:00'),
(878, 901, 151, 4000.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 02:15:00'),
(879, 902, 151, 4000.00, 'gcash', 'GCash', '1000077957665', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 03:15:00'),
(880, 903, 151, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1616', '2025-12-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-07 02:00:00', NULL, '2025-12-06 04:15:00'),
(881, 904, 151, 4000.00, 'gcash', 'GCash', '1000078054207', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 05:15:00'),
(882, 905, 151, 4000.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 06:15:00'),
(883, 906, 152, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-26', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-26 07:15:00'),
(884, 907, 152, 4000.00, 'gcash', 'GCash', '1000078150749', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 08:15:00'),
(885, 908, 152, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1620', '2025-11-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-07 02:00:00', NULL, '2025-11-06 09:15:00'),
(886, 909, 152, 4000.00, 'gcash', 'GCash', '1000078247291', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 01:15:00'),
(887, 910, 152, 4000.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 02:15:00'),
(888, 911, 152, 4000.00, 'gcash', 'GCash', '1000078295562', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 03:15:00'),
(889, 912, 153, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-18', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-18 04:15:00'),
(890, 913, 153, 4400.00, 'bank_transfer', 'BDO', 'BDO-261001-1624', '2025-10-06', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-07 02:00:00', NULL, '2025-10-06 05:15:00'),
(891, 914, 153, 4000.00, 'gcash', 'GCash', '1000078440375', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 06:15:00'),
(892, 915, 153, 4000.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 07:15:00'),
(893, 916, 154, 8000.00, 'cash', 'Cash Payment', NULL, '2025-09-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-15 08:15:00'),
(894, 917, 154, 4000.00, 'gcash', 'GCash', '1000078536917', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 09:15:00'),
(895, 918, 154, 4000.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 01:15:00'),
(896, 919, 154, 4000.00, 'gcash', 'GCash', '1000078585188', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 02:15:00'),
(897, 920, 155, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-13', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-13 03:15:00'),
(898, 921, 155, 3800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 04:15:00'),
(899, 922, 155, 3800.00, 'gcash', 'GCash', '1000078681730', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 05:15:00'),
(900, 923, 155, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1631', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 06:15:00'),
(901, 924, 156, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-06 07:15:00'),
(902, 925, 156, 3800.00, 'gcash', 'GCash', '1000078826543', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 08:15:00'),
(903, 926, 156, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1634', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 09:15:00'),
(904, 927, 156, 3800.00, 'gcash', 'GCash', '1000078923085', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 01:15:00'),
(905, 928, 156, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 02:15:00'),
(906, 929, 156, 3800.00, 'gcash', 'GCash', '1000078971356', '2026-06-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-03 02:00:00', NULL, '2026-06-02 03:15:00'),
(907, 930, 157, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-14 04:15:00'),
(908, 931, 157, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1638', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 05:15:00'),
(909, 932, 157, 3800.00, 'gcash', 'GCash', '1000079116169', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 06:15:00'),
(910, 933, 157, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 07:15:00'),
(911, 934, 157, 3800.00, 'gcash', 'GCash', '1000079164440', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 08:15:00'),
(912, 935, 157, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1641', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 09:15:00'),
(913, 936, 158, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-29', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-29 01:15:00'),
(914, 937, 158, 3800.00, 'gcash', 'GCash', '1000079309253', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 02:15:00'),
(915, 938, 158, 3800.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 03:15:00'),
(916, 939, 158, 3800.00, 'gcash', 'GCash', '1000079357524', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 04:15:00'),
(917, 940, 159, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-14 05:15:00'),
(918, 941, 159, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 06:15:00'),
(919, 942, 159, 3800.00, 'gcash', 'GCash', '1000079454066', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 07:15:00'),
(920, 943, 159, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1647', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 08:15:00'),
(921, 944, 160, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-11', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-11 09:15:00'),
(922, 945, 160, 3800.00, 'gcash', 'GCash', '1000079598879', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 01:15:00'),
(923, 946, 160, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1650', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 02:15:00'),
(924, 947, 160, 4180.00, 'gcash', 'GCash', '1000079695421', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 03:15:00'),
(925, 948, 160, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 04:15:00'),
(926, 949, 161, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-20', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-20 05:15:00'),
(927, 950, 161, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1653', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 06:15:00'),
(928, 951, 161, 4180.00, 'gcash', 'GCash', '1000079840234', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 07:15:00'),
(929, 952, 161, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 08:15:00'),
(930, 953, 161, 3800.00, 'gcash', 'GCash', '1000079888505', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 09:15:00'),
(931, 954, 162, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-03', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-03 01:15:00'),
(932, 955, 162, 4180.00, 'gcash', 'GCash', '1000079985047', '2025-10-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-10 02:00:00', NULL, '2025-10-09 02:15:00'),
(933, 956, 162, 3800.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 03:15:00'),
(934, 957, 162, 3800.00, 'gcash', 'GCash', '1000080033318', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 04:15:00'),
(935, 958, 162, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1659', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 05:15:00'),
(936, 959, 163, 7600.00, 'cash', 'Cash Payment', NULL, '2026-02-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-24 06:15:00'),
(937, 960, 163, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 07:15:00'),
(938, 961, 163, 3800.00, 'gcash', 'GCash', '1000080178131', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 08:15:00'),
(939, 962, 163, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1662', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 09:15:00'),
(940, 963, 163, 3800.00, 'gcash', 'GCash', '1000080274673', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 01:15:00'),
(941, 964, 164, 7600.00, 'cash', 'Cash Payment', NULL, '2025-10-07', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-07 02:15:00'),
(942, 965, 164, 3800.00, 'gcash', 'GCash', '1000080371215', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 03:15:00'),
(943, 966, 164, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1666', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 04:15:00'),
(944, 967, 164, 3800.00, 'gcash', 'GCash', '1000080467757', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 05:15:00'),
(945, 968, 164, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 06:15:00'),
(946, 969, 164, 3800.00, 'gcash', 'GCash', '1000080516028', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 07:15:00'),
(947, 970, 165, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-01 08:15:00'),
(948, 971, 165, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1670', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 09:15:00'),
(949, 972, 165, 3800.00, 'gcash', 'GCash', '1000080660841', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 01:15:00'),
(950, 973, 165, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 02:15:00'),
(951, 974, 165, 3800.00, 'gcash', 'GCash', '1000080709112', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 03:15:00'),
(952, 975, 165, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1673', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 04:15:00'),
(953, 976, 166, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-18', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-18 05:15:00'),
(954, 977, 166, 3800.00, 'gcash', 'GCash', '1000080853925', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 06:15:00'),
(955, 978, 166, 3800.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 07:15:00'),
(956, 979, 166, 3800.00, 'gcash', 'GCash', '1000080902196', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 08:15:00'),
(957, 980, 166, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1677', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 09:15:00'),
(958, 981, 166, 3800.00, 'gcash', 'GCash', '1000080998738', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 01:15:00'),
(959, 982, 167, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-14', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-14 02:15:00'),
(960, 983, 167, 3800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 03:15:00'),
(961, 984, 167, 3800.00, 'gcash', 'GCash', '1000081095280', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 04:15:00'),
(962, 985, 167, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1681', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 05:15:00'),
(963, 986, 167, 3800.00, 'gcash', 'GCash', '1000081191822', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 06:15:00'),
(964, 987, 167, 4180.00, 'cash', 'Cash Payment', NULL, '2026-02-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-06 07:15:00'),
(965, 988, 167, 3800.00, 'gcash', 'GCash', '1000081240093', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 08:15:00'),
(966, 989, 168, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-02 09:15:00'),
(967, 990, 168, 3800.00, 'gcash', 'GCash', '1000081336635', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 01:15:00'),
(968, 991, 168, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1686', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 02:15:00'),
(969, 992, 168, 3800.00, 'gcash', 'GCash', '1000081433177', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 03:15:00'),
(970, 993, 168, 4180.00, 'cash', 'Cash Payment', NULL, '2026-01-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-06 04:15:00'),
(971, 994, 168, 3800.00, 'gcash', 'GCash', '1000081481448', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 05:15:00'),
(972, 995, 169, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-10 06:15:00'),
(973, 996, 169, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1690', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 07:15:00'),
(974, 997, 169, 3800.00, 'gcash', 'GCash', '1000081626261', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 08:15:00'),
(975, 998, 169, 4180.00, 'cash', 'Cash Payment', NULL, '2025-12-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-06 09:15:00'),
(976, 999, 169, 3800.00, 'gcash', 'GCash', '1000081674532', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 01:15:00'),
(977, 1000, 170, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-05 02:15:00'),
(978, 1001, 170, 3800.00, 'gcash', 'GCash', '1000081771074', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 03:15:00'),
(979, 1002, 170, 4180.00, 'cash', 'Cash Payment', NULL, '2025-11-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-06 04:15:00'),
(980, 1003, 170, 3800.00, 'gcash', 'GCash', '1000081819345', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 05:15:00'),
(981, 1004, 170, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1696', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 06:15:00'),
(982, 1005, 170, 3800.00, 'gcash', 'GCash', '1000081915887', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 07:15:00'),
(983, 1006, 170, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 08:15:00'),
(984, 1007, 170, 3800.00, 'gcash', 'GCash', '1000081964158', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 09:15:00'),
(985, 1008, 171, 7600.00, 'cash', 'Cash Payment', NULL, '2025-10-02', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-02 01:15:00'),
(986, 1009, 171, 4180.00, 'cash', 'Cash Payment', NULL, '2025-11-06', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-06 02:15:00'),
(987, 1010, 171, 3800.00, 'gcash', 'GCash', '1000082060700', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 03:15:00'),
(988, 1011, 171, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1701', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 04:15:00'),
(989, 1012, 171, 3800.00, 'gcash', 'GCash', '1000082157242', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 05:15:00'),
(990, 1013, 172, 7600.00, 'cash', 'Cash Payment', NULL, '2025-10-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-05 06:15:00'),
(991, 1014, 172, 3800.00, 'gcash', 'GCash', '1000082253784', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 07:15:00'),
(992, 1015, 172, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1705', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 08:15:00'),
(993, 1016, 172, 3800.00, 'gcash', 'GCash', '1000082350326', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 09:15:00'),
(994, 1017, 172, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 01:15:00'),
(995, 1018, 172, 3800.00, 'gcash', 'GCash', '1000082398597', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 02:15:00'),
(996, 1019, 173, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-19', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-19 03:15:00'),
(997, 1020, 173, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1709', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 04:15:00'),
(998, 1021, 173, 3800.00, 'gcash', 'GCash', '1000082543410', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 05:15:00'),
(999, 1022, 173, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 06:15:00'),
(1000, 1023, 174, 7600.00, 'cash', 'Cash Payment', NULL, '2026-01-05', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-01-05 07:15:00'),
(1001, 1024, 174, 3800.00, 'gcash', 'GCash', '1000082639952', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 08:15:00'),
(1002, 1025, 174, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 09:15:00'),
(1003, 1026, 174, 3800.00, 'gcash', 'GCash', '1000082688223', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 01:15:00'),
(1004, 1027, 174, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1714', '2026-05-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-04 02:00:00', NULL, '2026-05-03 02:15:00'),
(1005, 1028, 174, 3800.00, 'gcash', 'GCash', '1000082784765', '2026-06-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-06-05 02:00:00', NULL, '2026-06-04 03:15:00'),
(1006, 1029, 175, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-08', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-08 04:15:00'),
(1007, 1030, 175, 3800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 05:15:00'),
(1008, 1031, 175, 3800.00, 'gcash', 'GCash', '1000082881307', '2025-11-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-03 02:00:00', NULL, '2025-11-02 06:15:00'),
(1009, 1032, 175, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1718', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 07:15:00'),
(1010, 1033, 176, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-24', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-24 08:15:00'),
(1011, 1034, 176, 3800.00, 'gcash', 'GCash', '1000083026120', '2025-10-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-03 02:00:00', NULL, '2025-10-02 09:15:00'),
(1012, 1035, 176, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1721', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 01:15:00'),
(1013, 1036, 176, 3800.00, 'gcash', 'GCash', '1000083122662', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 02:15:00'),
(1014, 1037, 176, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 03:15:00'),
(1015, 1038, 176, 4180.00, 'gcash', 'GCash', '1000083170933', '2026-02-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-10 02:00:00', NULL, '2026-02-09 04:15:00'),
(1016, 1039, 176, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1724', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 05:15:00'),
(1017, 1040, 176, 3800.00, 'gcash', 'GCash', '1000083267475', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 06:15:00'),
(1018, 1041, 177, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-25', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-25 07:15:00'),
(1019, 1042, 177, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1727', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 08:15:00'),
(1020, 1043, 177, 3800.00, 'gcash', 'GCash', '1000083412288', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 09:15:00'),
(1021, 1044, 177, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 01:15:00'),
(1022, 1045, 177, 4180.00, 'gcash', 'GCash', '1000083460559', '2026-01-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-10 02:00:00', NULL, '2026-01-09 02:15:00'),
(1023, 1046, 177, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1730', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 03:15:00'),
(1024, 1047, 177, 3800.00, 'gcash', 'GCash', '1000083557101', '2026-03-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-05 02:00:00', NULL, '2026-03-04 04:15:00'),
(1025, 1048, 178, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-28', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-28 05:15:00'),
(1026, 1049, 178, 3800.00, 'gcash', 'GCash', '1000083653643', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 06:15:00'),
(1027, 1050, 178, 3800.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 07:15:00'),
(1028, 1051, 178, 4180.00, 'gcash', 'GCash', '1000083701914', '2025-12-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-10 02:00:00', NULL, '2025-12-09 08:15:00'),
(1029, 1052, 178, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1735', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 09:15:00'),
(1030, 1053, 178, 3800.00, 'gcash', 'GCash', '1000083798456', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 01:15:00'),
(1031, 1054, 178, 3800.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 02:15:00'),
(1032, 1055, 179, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-06 03:15:00'),
(1033, 1056, 179, 3800.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-10-01 04:15:00'),
(1034, 1057, 179, 4180.00, 'gcash', 'GCash', '1000083894998', '2025-11-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-10 02:00:00', NULL, '2025-11-09 05:15:00'),
(1035, 1058, 179, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1739', '2025-12-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-04 02:00:00', NULL, '2025-12-03 06:15:00'),
(1036, 1059, 179, 3800.00, 'gcash', 'GCash', '1000083991540', '2026-01-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-05 02:00:00', NULL, '2026-01-04 07:15:00'),
(1037, 1060, 179, 3800.00, 'cash', 'Cash Payment', NULL, '2026-02-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-02-01 08:15:00'),
(1038, 1061, 179, 3800.00, 'gcash', 'GCash', '1000084039811', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 09:15:00'),
(1039, 1062, 180, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-25', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-25 01:15:00'),
(1040, 1063, 180, 4180.00, 'gcash', 'GCash', '1000084136353', '2025-10-09', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-10 02:00:00', NULL, '2025-10-09 02:15:00'),
(1041, 1064, 180, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1744', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 03:15:00'),
(1042, 1065, 180, 3800.00, 'gcash', 'GCash', '1000084232895', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 04:15:00'),
(1043, 1066, 180, 3800.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 05:15:00'),
(1044, 1067, 180, 3800.00, 'gcash', 'GCash', '1000084281166', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 06:15:00'),
(1045, 1068, 180, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1747', '2026-03-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-04 02:00:00', NULL, '2026-03-03 07:15:00'),
(1046, 1069, 180, 3800.00, 'gcash', 'GCash', '1000084377708', '2026-04-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-05 02:00:00', NULL, '2026-04-04 08:15:00'),
(1047, 1070, 180, 3800.00, 'cash', 'Cash Payment', NULL, '2026-05-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-05-01 09:15:00'),
(1048, 1071, 181, 7600.00, 'cash', 'Cash Payment', NULL, '2025-09-06', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-06 01:15:00'),
(1049, 1072, 181, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1750', '2025-10-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-04 02:00:00', NULL, '2025-10-03 02:15:00'),
(1050, 1073, 181, 3800.00, 'gcash', 'GCash', '1000084522521', '2025-11-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-05 02:00:00', NULL, '2025-11-04 03:15:00'),
(1051, 1074, 181, 3800.00, 'cash', 'Cash Payment', NULL, '2025-12-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-12-01 04:15:00'),
(1052, 1075, 181, 3800.00, 'gcash', 'GCash', '1000084570792', '2026-01-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-03 02:00:00', NULL, '2026-01-02 05:15:00'),
(1053, 1076, 181, 3800.00, 'bank_transfer', 'BDO', 'BDO-261001-1753', '2026-02-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-04 02:00:00', NULL, '2026-02-03 06:15:00'),
(1054, 1077, 182, 8400.00, 'cash', 'Cash Payment', NULL, '2025-09-10', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-09-10 07:15:00'),
(1055, 1078, 182, 4200.00, 'gcash', 'GCash', '1000084715605', '2025-10-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-10-05 02:00:00', NULL, '2025-10-04 08:15:00'),
(1056, 1079, 182, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 09:15:00'),
(1057, 1080, 182, 4200.00, 'gcash', 'GCash', '1000084763876', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 01:15:00'),
(1058, 1081, 182, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1757', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 02:15:00'),
(1059, 1082, 182, 4200.00, 'gcash', 'GCash', '1000084860418', '2026-02-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-05 02:00:00', NULL, '2026-02-04 03:15:00'),
(1060, 1083, 182, 4200.00, 'cash', 'Cash Payment', NULL, '2026-03-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-03-01 04:15:00'),
(1061, 1084, 182, 4200.00, 'gcash', 'GCash', '1000084908689', '2026-04-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-03 02:00:00', NULL, '2026-04-02 05:15:00'),
(1062, 1085, 183, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-15', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-15 06:15:00'),
(1063, 1086, 183, 4200.00, 'cash', 'Cash Payment', NULL, '2025-11-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2025-11-01 07:15:00'),
(1064, 1087, 183, 4200.00, 'gcash', 'GCash', '1000085005231', '2025-12-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-03 02:00:00', NULL, '2025-12-02 08:15:00'),
(1065, 1088, 183, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1762', '2026-01-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-01-04 02:00:00', NULL, '2026-01-03 09:15:00'),
(1066, 1089, 184, 8400.00, 'cash', 'Cash Payment', NULL, '2026-02-09', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2026-02-09 01:15:00'),
(1067, 1090, 184, 4200.00, 'gcash', 'GCash', '1000085150044', '2026-03-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-03-03 02:00:00', NULL, '2026-03-02 02:15:00'),
(1068, 1091, 184, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1765', '2026-04-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-04-04 02:00:00', NULL, '2026-04-03 03:15:00'),
(1069, 1092, 184, 4200.00, 'gcash', 'GCash', '1000085246586', '2026-05-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-05-05 02:00:00', NULL, '2026-05-04 04:15:00'),
(1070, 1093, 184, 4200.00, 'cash', 'Cash Payment', NULL, '2026-06-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-06-01 05:15:00'),
(1071, 1094, 184, 4200.00, 'gcash', 'GCash', '1000085294857', '2026-07-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-07-03 02:00:00', NULL, '2026-07-02 06:15:00'),
(1072, 1095, 185, 8400.00, 'cash', 'Cash Payment', NULL, '2025-10-01', 'approved', NULL, 'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.', NULL, NULL, NULL, 356, '2025-10-01 07:15:00'),
(1073, 1096, 185, 4200.00, 'bank_transfer', 'BDO', 'BDO-261001-1769', '2025-11-03', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-11-04 02:00:00', NULL, '2025-11-03 08:15:00'),
(1074, 1097, 185, 4200.00, 'gcash', 'GCash', '1000085439670', '2025-12-04', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2025-12-05 02:00:00', NULL, '2025-12-04 09:15:00'),
(1075, 1098, 185, 4200.00, 'cash', 'Cash Payment', NULL, '2026-01-01', 'approved', NULL, NULL, NULL, NULL, NULL, 356, '2026-01-01 01:15:00'),
(1076, 1099, 185, 4200.00, 'gcash', 'GCash', '1000085487941', '2026-02-02', 'approved', 'demo/sample-payment-proof.png', NULL, NULL, 356, '2026-02-03 02:00:00', NULL, '2026-02-02 02:15:00');

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
(6, 'cash', 'Cash Payment', NULL, NULL, NULL, 'Paid to authorized dormitory staff, who record the payment in NEST.PH and issue a receipt.', 0, '2026-09-25 06:52:59', '2026-10-01 15:12:47'),
(7, 'ewallet', 'GCash', 'Patricia Joy N.', '09178932970', NULL, NULL, 1, '2026-09-25 06:56:42', '2026-10-01 15:12:47'),
(8, 'bank', 'BDO', 'Patricia Joy Han', '005548032974', NULL, NULL, 2, '2026-10-01 15:12:47', '2026-10-01 15:12:47');

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `damage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `billing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'manual',
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
(1, 5, NULL, 24, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for June 2026', 450.00, '2026-06-05', 'active', NULL, '2026-06-04 16:00:00', '2026-06-04 16:00:00'),
(2, 5, NULL, 28, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for October 2026', 450.00, '2026-10-05', 'active', NULL, '2026-10-04 16:00:00', '2026-10-04 16:00:00'),
(3, 6, NULL, 33, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for September 2026', 380.00, '2026-09-23', 'active', NULL, '2026-09-22 16:00:00', '2026-09-22 16:00:00'),
(4, 8, NULL, 41, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for September 2026', 450.00, '2026-09-05', 'active', NULL, '2026-09-04 16:00:00', '2026-09-04 16:00:00'),
(5, 9, NULL, 46, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for August 2026', 450.00, '2026-08-05', 'active', NULL, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(6, 9, NULL, 47, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for September 2026', 450.00, '2026-09-29', 'active', NULL, '2026-09-28 16:00:00', '2026-09-28 16:00:00'),
(7, 10, NULL, 50, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for June 2026', 420.00, '2026-06-05', 'active', NULL, '2026-06-04 16:00:00', '2026-06-04 16:00:00'),
(8, 11, NULL, 56, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(9, 13, NULL, 73, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for September 2026', 400.00, '2026-09-26', 'active', NULL, '2026-09-25 16:00:00', '2026-09-25 16:00:00'),
(10, 14, 1, 78, 'damage', 'Damage to dormitory property: broken locker door hinge (repair cost)', 800.00, '2026-09-19', 'active', 355, '2026-09-18 16:00:00', '2026-09-18 16:00:00'),
(11, 14, NULL, NULL, 'manual', 'Lost or unreturned key (key duplication)', 50.00, '2026-08-22', 'waived', 355, '2026-08-21 16:00:00', '2026-08-21 16:00:00'),
(12, 15, NULL, NULL, 'manual', 'Possession or use of hazardous items (Rules, item 9): butane stove found in room', 500.00, '2026-09-29', 'active', 355, '2026-09-28 16:00:00', '2026-09-28 16:00:00'),
(13, 18, NULL, 94, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for October 2026', 420.00, '2026-10-05', 'active', NULL, '2026-10-04 16:00:00', '2026-10-04 16:00:00'),
(14, 20, NULL, 99, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for July 2026', 400.00, '2026-07-05', 'active', NULL, '2026-07-04 16:00:00', '2026-07-04 16:00:00'),
(15, 21, NULL, 105, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for August 2026', 400.00, '2026-08-05', 'active', NULL, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(16, 22, NULL, 109, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for September 2026', 400.00, '2026-09-05', 'active', NULL, '2026-09-04 16:00:00', '2026-09-04 16:00:00'),
(17, 24, NULL, 122, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for August 2026', 380.00, '2026-08-18', 'active', NULL, '2026-08-17 16:00:00', '2026-08-17 16:00:00'),
(18, 31, NULL, 150, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for August 2026', 380.00, '2026-08-05', 'active', NULL, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(19, 35, NULL, 167, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for August 2026', 420.00, '2026-08-05', 'active', NULL, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(20, 39, NULL, 192, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for March 2026', 400.00, '2026-03-05', 'active', NULL, '2026-03-04 16:00:00', '2026-03-04 16:00:00'),
(21, 43, NULL, 219, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for June 2026', 380.00, '2026-06-05', 'active', NULL, '2026-06-04 16:00:00', '2026-06-04 16:00:00'),
(22, 48, NULL, 250, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(23, 49, NULL, 261, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for August 2026', 420.00, '2026-08-05', 'active', NULL, '2026-08-04 16:00:00', '2026-08-04 16:00:00'),
(24, 53, NULL, 287, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for July 2026', 400.00, '2026-07-05', 'active', NULL, '2026-07-04 16:00:00', '2026-07-04 16:00:00'),
(25, 54, NULL, 292, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for April 2026', 400.00, '2026-04-05', 'active', NULL, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(26, 62, NULL, 354, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(27, 62, NULL, 359, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for July 2026', 380.00, '2026-07-05', 'active', NULL, '2026-07-04 16:00:00', '2026-07-04 16:00:00'),
(28, 68, NULL, 397, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(29, 73, NULL, 441, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for May 2026', 380.00, '2026-05-05', 'active', NULL, '2026-05-04 16:00:00', '2026-05-04 16:00:00'),
(30, 75, NULL, 453, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for April 2026', 380.00, '2026-04-05', 'active', NULL, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(31, 80, NULL, 488, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(32, 80, NULL, 490, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(33, 80, NULL, 493, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for May 2026', 380.00, '2026-05-05', 'active', NULL, '2026-05-04 16:00:00', '2026-05-04 16:00:00'),
(34, 81, NULL, 508, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for September 2026', 420.00, '2026-09-05', 'active', NULL, '2026-09-04 16:00:00', '2026-09-04 16:00:00'),
(35, 88, NULL, 539, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for December 2025', 450.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(36, 89, NULL, 543, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for November 2025', 450.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(37, 90, NULL, 546, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for October 2025', 450.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(38, 93, NULL, 572, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for May 2026', 450.00, '2026-05-05', 'active', NULL, '2026-05-04 16:00:00', '2026-05-04 16:00:00'),
(39, 95, NULL, 583, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for February 2026', 450.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(40, 96, NULL, 590, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for January 2026', 450.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(41, 97, NULL, 595, 'late_payment', 'Late payment penalty: 10% of ₱4,500.00 unpaid rent for January 2026', 450.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(42, 98, NULL, 598, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for November 2025', 400.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(43, 99, NULL, 601, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for October 2025', 400.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(44, 103, NULL, 629, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for March 2026', 400.00, '2026-03-05', 'active', NULL, '2026-03-04 16:00:00', '2026-03-04 16:00:00'),
(45, 106, NULL, 643, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for December 2025', 420.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(46, 107, NULL, 651, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for November 2025', 420.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(47, 108, NULL, 655, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for November 2025', 420.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(48, 114, NULL, 693, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for January 2026', 420.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(49, 115, NULL, 701, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for December 2025', 400.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(50, 116, NULL, 707, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for November 2025', 400.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(51, 117, NULL, 710, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for October 2025', 400.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(52, 120, NULL, 735, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for April 2026', 400.00, '2026-04-05', 'active', NULL, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(53, 123, NULL, 749, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for January 2026', 400.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(54, 124, NULL, 757, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for December 2025', 400.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(55, 125, NULL, 760, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for November 2025', 400.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(56, 126, NULL, 763, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for February 2026', 400.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(57, 128, NULL, 780, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for May 2026', 400.00, '2026-05-05', 'active', NULL, '2026-05-04 16:00:00', '2026-05-04 16:00:00'),
(58, 131, NULL, 795, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for February 2026', 420.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(59, 132, NULL, 803, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for January 2026', 420.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(60, 133, NULL, 808, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(61, 134, NULL, 811, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(62, 135, NULL, 815, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for October 2025', 380.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(63, 139, NULL, 842, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for March 2026', 380.00, '2026-03-05', 'active', NULL, '2026-03-04 16:00:00', '2026-03-04 16:00:00'),
(64, 141, NULL, 853, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for January 2026', 380.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(65, 142, NULL, 860, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(66, 143, NULL, 863, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for April 2026', 380.00, '2026-04-05', 'active', NULL, '2026-04-04 16:00:00', '2026-04-04 16:00:00'),
(67, 144, NULL, 869, 'late_payment', 'Late payment penalty: 10% of ₱4,200.00 unpaid rent for October 2025', 420.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(68, 150, NULL, 899, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for June 2026', 400.00, '2026-06-05', 'active', NULL, '2026-06-04 16:00:00', '2026-06-04 16:00:00'),
(69, 151, NULL, 903, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for December 2025', 400.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(70, 152, NULL, 908, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for November 2025', 400.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(71, 153, NULL, 913, 'late_payment', 'Late payment penalty: 10% of ₱4,000.00 unpaid rent for October 2025', 400.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(72, 160, NULL, 947, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(73, 161, NULL, 951, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for November 2025', 380.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(74, 162, NULL, 955, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for October 2025', 380.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00'),
(75, 167, NULL, 987, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(76, 168, NULL, 993, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for January 2026', 380.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(77, 169, NULL, 998, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(78, 170, NULL, 1002, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for November 2025', 380.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(79, 171, NULL, 1009, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for November 2025', 380.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(80, 176, NULL, 1038, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for February 2026', 380.00, '2026-02-05', 'active', NULL, '2026-02-04 16:00:00', '2026-02-04 16:00:00'),
(81, 177, NULL, 1045, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for January 2026', 380.00, '2026-01-05', 'active', NULL, '2026-01-04 16:00:00', '2026-01-04 16:00:00'),
(82, 178, NULL, 1051, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for December 2025', 380.00, '2025-12-05', 'active', NULL, '2025-12-04 16:00:00', '2025-12-04 16:00:00'),
(83, 179, NULL, 1057, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for November 2025', 380.00, '2025-11-05', 'active', NULL, '2025-11-04 16:00:00', '2025-11-04 16:00:00'),
(84, 180, NULL, 1063, 'late_payment', 'Late payment penalty: 10% of ₱3,800.00 unpaid rent for October 2025', 380.00, '2025-10-05', 'active', NULL, '2025-10-04 16:00:00', '2025-10-04 16:00:00');

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
(1, 1, 'created', NULL, 'Added automatically after the grace period ended.', '2026-06-04 16:00:00'),
(2, 2, 'created', NULL, 'Added automatically after the grace period ended.', '2026-10-04 16:00:00'),
(3, 3, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-22 16:00:00'),
(4, 4, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-04 16:00:00'),
(5, 5, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-04 16:00:00'),
(6, 6, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-28 16:00:00'),
(7, 7, 'created', NULL, 'Added automatically after the grace period ended.', '2026-06-04 16:00:00'),
(8, 8, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(9, 9, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-25 16:00:00'),
(10, 10, 'created', 355, NULL, '2026-09-18 16:00:00'),
(11, 11, 'created', 355, NULL, '2026-08-21 16:00:00'),
(12, 11, 'waived', 3, 'Key was found by the guard the next day. First offense -- waived.', '2026-08-22 16:00:00'),
(13, 12, 'created', 355, NULL, '2026-09-28 16:00:00'),
(14, 13, 'created', NULL, 'Added automatically after the grace period ended.', '2026-10-04 16:00:00'),
(15, 14, 'created', NULL, 'Added automatically after the grace period ended.', '2026-07-04 16:00:00'),
(16, 15, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-04 16:00:00'),
(17, 16, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-04 16:00:00'),
(18, 17, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-17 16:00:00'),
(19, 18, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-04 16:00:00'),
(20, 19, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-04 16:00:00'),
(21, 20, 'created', NULL, 'Added automatically after the grace period ended.', '2026-03-04 16:00:00'),
(22, 21, 'created', NULL, 'Added automatically after the grace period ended.', '2026-06-04 16:00:00'),
(23, 22, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(24, 23, 'created', NULL, 'Added automatically after the grace period ended.', '2026-08-04 16:00:00'),
(25, 24, 'created', NULL, 'Added automatically after the grace period ended.', '2026-07-04 16:00:00'),
(26, 25, 'created', NULL, 'Added automatically after the grace period ended.', '2026-04-04 16:00:00'),
(27, 26, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(28, 27, 'created', NULL, 'Added automatically after the grace period ended.', '2026-07-04 16:00:00'),
(29, 28, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(30, 29, 'created', NULL, 'Added automatically after the grace period ended.', '2026-05-04 16:00:00'),
(31, 30, 'created', NULL, 'Added automatically after the grace period ended.', '2026-04-04 16:00:00'),
(32, 31, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(33, 32, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(34, 33, 'created', NULL, 'Added automatically after the grace period ended.', '2026-05-04 16:00:00'),
(35, 34, 'created', NULL, 'Added automatically after the grace period ended.', '2026-09-04 16:00:00'),
(36, 35, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(37, 36, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(38, 37, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(39, 38, 'created', NULL, 'Added automatically after the grace period ended.', '2026-05-04 16:00:00'),
(40, 39, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(41, 40, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(42, 41, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(43, 42, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(44, 43, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(45, 44, 'created', NULL, 'Added automatically after the grace period ended.', '2026-03-04 16:00:00'),
(46, 45, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(47, 46, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(48, 47, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(49, 48, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(50, 49, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(51, 50, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(52, 51, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(53, 52, 'created', NULL, 'Added automatically after the grace period ended.', '2026-04-04 16:00:00'),
(54, 53, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(55, 54, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(56, 55, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(57, 56, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(58, 57, 'created', NULL, 'Added automatically after the grace period ended.', '2026-05-04 16:00:00'),
(59, 58, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(60, 59, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(61, 60, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(62, 61, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(63, 62, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(64, 63, 'created', NULL, 'Added automatically after the grace period ended.', '2026-03-04 16:00:00'),
(65, 64, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(66, 65, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(67, 66, 'created', NULL, 'Added automatically after the grace period ended.', '2026-04-04 16:00:00'),
(68, 67, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(69, 68, 'created', NULL, 'Added automatically after the grace period ended.', '2026-06-04 16:00:00'),
(70, 69, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(71, 70, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(72, 71, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(73, 72, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(74, 73, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(75, 74, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00'),
(76, 75, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(77, 76, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(78, 77, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(79, 78, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(80, 79, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(81, 80, 'created', NULL, 'Added automatically after the grace period ended.', '2026-02-04 16:00:00'),
(82, 81, 'created', NULL, 'Added automatically after the grace period ended.', '2026-01-04 16:00:00'),
(83, 82, 'created', NULL, 'Added automatically after the grace period ended.', '2025-12-04 16:00:00'),
(84, 83, 'created', NULL, 'Added automatically after the grace period ended.', '2025-11-04 16:00:00'),
(85, 84, 'created', NULL, 'Added automatically after the grace period ended.', '2025-10-04 16:00:00');

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
(1, 1, 5, 'Sobrang linis ng rooms and very accommodating ang staff. Five minutes lang lakad papuntang PUP. Highly recommended!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-24 16:00:00', '2026-09-24 16:00:00'),
(2, 5, 4, 'Maayos ang WiFi at tahimik sa gabi. Minsan lang medyo mahina ang tubig sa umaga pero agad naman inaayos.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-20 16:00:00', '2026-09-20 16:00:00'),
(3, 11, 5, 'Almost a year na ako dito. Safe, may curfew, at mabait si Ma\'am Tess. Perfect para sa mga nurse na shifting.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-14 16:00:00', '2026-09-14 16:00:00'),
(4, 15, 3, 'Okay naman overall. Sana lang may kitchen na pwedeng gamitin kasi bawal magluto sa room.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-10 16:00:00', '2026-09-10 16:00:00'),
(5, 18, 4, 'Good value for money. Malinis ang CR at laging may tubig. Medyo strict sa visitors pero understandable.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-07 16:00:00', '2026-09-07 16:00:00'),
(6, 23, 5, 'Nag-move out na ako kasi lumipat ang work ko, pero sobrang saya ng stay ko dito. Salamat Pureza Station!', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-22 16:00:00', '2026-09-22 16:00:00'),
(7, 25, 5, 'Ang ganda ng study hall sa baba, dito ako nagre-review lagi. Mabilis din sumagot ang admin sa tickets.', 1, 'published', NULL, NULL, NULL, NULL, '2026-09-20 16:00:00', '2026-09-20 16:00:00'),
(8, 12, 1, 'Mas mura sa amin! Visit www.cheapdorms-manila.com for promo, message 0917-000-0000', 0, 'hidden', '[\"Contains a link\",\"Contains a phone number\"]', NULL, NULL, NULL, '2026-09-27 16:00:00', '2026-09-27 16:00:00'),
(9, 17, 2, 'Pangit ugali ng roommate ko, [name removed] sobrang ingay.', 0, 'removed', NULL, 355, '2026-09-24 16:00:00', 'Removed: names another tenant. Concern was redirected to a support ticket instead.', '2026-09-23 16:00:00', '2026-09-24 16:00:00');

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
  `room_type_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `rooms` (`id`, `floor_id`, `room_type_id`, `room_no`, `room_type`, `amenities`, `monthly_rate`, `monthly_utility_cost`, `monthly_wifi_cost`, `status`, `vr_asset_path`, `vr_caption`, `vr_visibility`, `created_at`, `updated_at`) VALUES
(16, 3, 2, '101', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 18000.00, 0.00, 0.00, 'full', NULL, 'Bright 4-person room beside the study hall', 'public', '2026-08-30 18:57:43', '2026-10-01 15:12:48'),
(17, 3, 2, '103', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 18000.00, 0.00, 0.00, 'full', NULL, 'Cozy double room with private CR', 'draft', '2026-08-31 10:49:31', '2026-10-01 15:12:48'),
(18, 3, 2, '102', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 18000.00, 0.00, 0.00, 'full', NULL, 'Quiet 4-person room with a computer corner', 'public', '2026-09-03 06:27:16', '2026-10-01 15:12:48'),
(19, 3, 1, '104', 'Solo fan room', '[\"Electric fan\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\"]', 4500.00, 0.00, 0.00, 'maintenance', NULL, NULL, 'draft', '2026-09-04 13:04:53', '2026-10-01 15:12:48'),
(84, 3, 1, '105', 'Solo fan room', '[\"Electric fan\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\"]', 4500.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(85, 11, 4, '201', 'Room with AC, 6 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 24000.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(86, 11, 3, '202', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 16800.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(87, 11, 3, '203', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 16800.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(88, 11, 4, '204', 'Room with AC, 6 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 24000.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(89, 12, 4, '301', 'Room with AC, 6 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 24000.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(90, 12, 3, '302', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 16800.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(91, 12, 5, '303', 'Room with AC, 10–16 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 45600.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(92, 12, 3, '304', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 16800.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(93, 13, 4, '401', 'Room with AC, 6 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 24000.00, 0.00, 0.00, 'full', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(94, 13, 5, '402', 'Room with AC, 10–16 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 60800.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(95, 14, 5, '501', 'Room with AC, 10–16 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Double-deck beds\",\"Lockers\"]', 53200.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48'),
(96, 14, 3, '502', 'Room with AC, 4 persons', '[\"Air-conditioned\",\"WiFi\",\"Electricity\",\"Water\",\"Study table\",\"Cabinet per bed\"]', 16800.00, 0.00, 0.00, 'available', NULL, NULL, 'draft', '2025-07-31 16:00:00', '2026-10-01 15:12:48');

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
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `location` varchar(80) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `pricing_mode` varchar(10) NOT NULL DEFAULT 'per_bed',
  `monthly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_capacity` smallint(5) UNSIGNED DEFAULT NULL,
  `max_capacity` smallint(5) UNSIGNED DEFAULT NULL,
  `has_aircon` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `location`, `description`, `pricing_mode`, `monthly_rate`, `min_capacity`, `max_capacity`, `has_aircon`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Solo fan room', 'Ground floor', NULL, 'per_room', 4500.00, 1, 1, 0, 1, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(2, 'Room with AC, 4 persons', 'Ground floor', NULL, 'per_bed', 4500.00, 4, 4, 1, 2, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(3, 'Room with AC, 4 persons', '2nd to 5th floor', NULL, 'per_bed', 4200.00, 4, 4, 1, 3, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(4, 'Room with AC, 6 persons', '2nd to 5th floor', NULL, 'per_bed', 4000.00, 6, 6, 1, 4, '2026-10-01 15:12:47', '2026-10-01 15:12:47'),
(5, 'Room with AC, 10–16 persons', '2nd to 5th floor', NULL, 'per_bed', 3800.00, 10, 16, 1, 5, '2026-10-01 15:12:47', '2026-10-01 15:12:47');

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
('WkeYbF5VxJ8WLYN5iV4CJvKpgZF3NYHOnwkkWYlL', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYldQYk1jbnNmNU8wcGxHT0hWeE5BczRkZzdBclR1TGxTdDZWS2JBbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90ZW5hbnQtbWFuYWdlci8xOS9zdGF0ZW1lbnQtb2YtYWNjb3VudCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1790868130);

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
  `emergency_billing_reminders` tinyint(1) NOT NULL DEFAULT 0,
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

INSERT INTO `tenants` (`id`, `user_id`, `first_name`, `last_name`, `contact_number`, `email`, `emergency_contact_name`, `emergency_contact_number`, `emergency_billing_reminders`, `date_of_birth`, `home_address`, `tenant_type`, `id_document_path`, `signed_contract_path`, `status`, `deactivation_reason`, `deactivated_at`, `deactivated_by`, `is_blacklisted`, `portal_restricted`, `escalation_paused`, `created_at`, `updated_at`) VALUES
(1, 359, 'Maria Angelica', 'Santos', '09181242486', 'maria.santos@gmail.com', 'Rodelio Santos', '09182034386', 1, '2004-03-14', 'Brgy. San Isidro, Angono, Rizal', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-maria-angelica-santos.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-02 02:15:00', '2026-10-01 15:12:49'),
(2, 360, 'Kimberly Anne', 'Dela Cruz', '09212408565', 'kimberly.delacruz@gmail.com', 'Marites Dela Cruz', '09212408565', 1, '2005-07-02', '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kimberly-anne-dela-cruz.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-24 03:15:00', '2026-10-01 15:12:50'),
(3, 361, 'Patricia Mae', 'Gonzales', '09212408565', 'patricia.gonzales@gmail.com', 'Lorna Gonzales', '09212408565', 0, '2003-11-21', 'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-patricia-mae-gonzales.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-24 04:15:00', '2026-10-01 15:12:51'),
(4, 362, 'Nicole Joy', 'Ramos', '09451266243', 'nicole.ramos@gmail.com', 'Ernesto Ramos', '09452058143', 1, '2004-01-09', 'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-nicole-joy-ramos.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-01 05:15:00', '2026-10-01 15:12:52'),
(5, 363, 'Juan Miguel', 'Reyes', '09561274162', 'juan.reyes@gmail.com', 'Carmelita Reyes', '09562066062', 1, '1999-05-30', '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-juan-miguel-reyes.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-08 06:15:00', '2026-10-01 15:12:53'),
(6, 364, 'John Paul', 'Mendoza', '09212408565', 'john.mendoza@gmail.com', 'Rosalie Mendoza', '09212408565', 0, '2004-08-17', 'Brgy. Bagong Silang, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-john-paul-mendoza.pdf', 'active', NULL, NULL, NULL, 0, 1, 0, '2026-05-06 07:15:00', '2026-10-01 15:12:55'),
(7, 365, 'Mark Joseph', 'Aquino', '09981290000', 'mark.aquino@gmail.com', 'Josefina Aquino', '09982081900', 1, '2005-02-11', 'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-mark-joseph-aquino.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-27 08:15:00', '2026-10-01 15:12:56'),
(8, 366, 'Christian Dave', 'Torres', '09212408565', 'christian.torres@gmail.com', 'Dante Torres', '09212408565', 1, '2002-12-03', '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-christian-dave-torres.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-23 01:15:00', '2026-10-01 15:12:57'),
(9, 367, 'Benjamin', 'Robles', '09212408565', 'benjamin.robles@gmail.com', 'Evelyn Robles', '09212408565', 1, '2004-06-25', 'Brgy. Santo Cristo, San Fernando, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-benjamin-robles.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-27 02:15:00', '2026-10-01 15:12:58'),
(10, 368, 'Rafael Luis', 'Navarro', '09171313757', 'rafael.navarro@gmail.com', 'Gloria Navarro', '09172105657', 1, '2001-09-14', '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-rafael-luis-navarro.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-05 03:15:00', '2026-10-01 15:12:59'),
(11, 369, 'Angela Marie', 'Villanueva', '09181321676', 'angela.villanueva@gmail.com', 'Arnel Villanueva', '09182113576', 1, '1998-04-19', 'Brgy. Poblacion, Tuguegarao City, Cagayan', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-angela-marie-villanueva.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-12 04:15:00', '2026-10-01 15:13:00'),
(12, 370, 'Jasmine Rose', 'Garcia', '09212408565', 'jasmine.garcia@gmail.com', 'Ramil Garcia', '09212408565', 0, '2005-10-05', 'Purok 2, Brgy. Talon, Las Piñas City', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-jasmine-rose-garcia.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-22 05:15:00', '2026-10-01 15:13:01'),
(13, 371, 'Camille Louise', 'Flores', '09212408565', 'camille.flores@gmail.com', 'Susan Flores', '09212408565', 1, '2004-12-28', 'Brgy. Mabini, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-camille-louise-flores.pdf', 'active', NULL, NULL, NULL, 0, 0, 1, '2026-05-01 06:15:00', '2026-10-01 15:13:02'),
(14, 372, 'Bea Katrina', 'Pascual', '09212408565', 'bea.pascual@gmail.com', 'Gerardo Pascual', '09212408565', 1, '2003-05-16', 'Brgy. Sta. Rita, Olongapo City, Zambales', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-bea-katrina-pascual.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-20 07:15:00', '2026-10-01 15:13:03'),
(15, 373, 'Princess Joy', 'Manalo', '09561353352', 'princess.manalo@gmail.com', 'Cristina Manalo', '09562145252', 0, '2002-08-08', 'Brgy. Parian, Calamba City, Laguna', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-princess-joy-manalo.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-31 08:15:00', '2026-10-01 15:13:04'),
(16, 374, 'Kathleen Mae', 'Salazar', '09661361271', 'kathleen.salazar@gmail.com', 'Rogelio Salazar', '09662153171', 1, '2006-01-30', 'Brgy. Poblacion, Tagum City, Davao del Norte', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kathleen-mae-salazar.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-26 01:15:00', '2026-10-01 15:13:05'),
(17, 375, 'Carlo Miguel', 'Bautista', '09981369190', 'carlo.bautista@gmail.com', 'Nenita Bautista', '09982161090', 1, '2000-03-03', 'San Pablo City, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-carlo-miguel-bautista.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-30 02:15:00', '2026-10-01 15:13:06'),
(18, 376, 'Joshua Emmanuel', 'Lim', '09081377109', 'joshua.lim@gmail.com', 'Wilson Lim', '09082169009', 0, '2004-11-11', 'Sta. Cruz, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-joshua-emmanuel-lim.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-08 03:15:00', '2026-10-01 15:13:07'),
(19, 377, 'Paolo Andres', 'Ocampo', '09951385028', 'paolo.ocampo@gmail.com', 'Amelia Ocampo', '09952176928', 1, '2005-04-22', 'Brgy. Poblacion, Malolos City, Bulacan', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-paolo-andres-ocampo.pdf', 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-23 04:15:00', '2026-10-01 15:13:10'),
(20, 378, 'Andrea Nicole', 'Tan', '09171392947', 'andrea.tan@gmail.com', 'Rebecca Tan', '09172184847', 1, '1997-09-09', 'Brgy. Kauswagan, Cagayan de Oro City', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-andrea-nicole-tan.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-30 05:15:00', '2026-10-01 15:13:12'),
(21, 379, 'Erika Jane', 'Morales', '09181400866', 'erika.morales@gmail.com', 'Ronaldo Morales', '09182192766', 0, '2004-07-07', 'Brgy. Pantal, Dagupan City, Pangasinan', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-erika-jane-morales.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-05 06:15:00', '2026-10-01 15:13:14'),
(22, 380, 'Hannah Grace', 'Soriano', '09271408785', 'hannah.soriano@gmail.com', 'Marilou Soriano', '09272200685', 1, '2006-02-14', 'Brgy. Dolores, Taytay, Rizal', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-hannah-grace-soriano.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-26 07:15:00', '2026-10-01 15:13:17'),
(23, 381, 'Gabriel Jose', 'Rivera', '09391416704', 'gabriel.rivera@gmail.com', 'Imelda Rivera', '09392208604', 1, '2001-06-01', 'Brgy. San Antonio, Biñan, Laguna', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract -- transferred to a job in Laguna.', '2026-07-31 16:00:00', 355, 0, 0, 0, '2026-02-06 08:15:00', '2026-10-01 15:13:17'),
(24, 382, 'Joseph Allan', 'Cruz', '09212408565', 'joseph.cruz@gmail.com', 'Nora Cruz', '09212408565', 0, '2003-03-27', 'Brgy. Tabing Ilog, Marilao, Bulacan', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'active', NULL, NULL, NULL, 1, 1, 0, '2026-03-12 01:15:00', '2026-10-01 15:13:17'),
(25, 383, 'Stephanie Claire', 'Uy', '09561432542', 'stephanie.uy@gmail.com', 'Henry Uy', '09562224442', 1, '2004-09-02', 'Brgy. Lourdes, Dagupan City, Pangasinan', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-stephanie-claire-uy.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-02-09 02:15:00', '2026-10-01 15:13:19'),
(26, 384, 'Adrian Paul', 'Castro', '09661440461', 'adrian.castro@gmail.com', 'Leticia Castro', '09662232361', 1, '1999-12-12', 'Brgy. Sampaloc, Tanauan City, Batangas', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-adrian-paul-castro.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-03 03:15:00', '2026-10-01 15:13:22'),
(27, 385, 'Vincent Ray', 'Magbanua', '09981448380', 'vincent.magbanua@gmail.com', 'Ramon Magbanua', '09982240280', 0, '2005-06-19', 'Brgy. Poblacion, Roxas City, Capiz', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-vincent-ray-magbanua.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-30 04:15:00', '2026-10-01 15:13:24'),
(28, 386, 'Luis Antonio', 'Del Rosario', '09212408565', 'luis.delrosario@gmail.com', 'Rowena Del Rosario', '09212408565', 1, '2003-02-27', 'Brgy. Cutcut, Angeles City, Pampanga', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-luis-antonio-del-rosario.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-11 05:15:00', '2026-10-01 15:13:26'),
(29, 387, 'Jerome Anthony', 'Pineda', '09951464218', 'jerome.pineda@gmail.com', 'Grace Pineda', '09952256118', 1, '2004-10-30', 'Brgy. Poblacion, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-jerome-anthony-pineda.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-03 06:15:00', '2026-10-01 15:13:27'),
(30, 388, 'Kenneth Bryan', 'Sy', '09212408565', 'kenneth.sy@gmail.com', 'Victor Sy', '09212408565', 0, '2006-01-08', 'Brgy. Balibago, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kenneth-bryan-sy.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-26 07:15:00', '2026-10-01 15:13:29'),
(31, 389, 'Emmanuel Jose', 'Villareal', '09181480056', 'emmanuel.villareal@gmail.com', 'Nelia Villareal', '09182271956', 1, '2002-07-21', 'Brgy. San Vicente, Tacloban City, Leyte', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-emmanuel-jose-villareal.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-16 08:15:00', '2026-10-01 15:13:31'),
(32, 390, 'Pauline', 'Delos Reyes', '09212408565', 'pauline.delosreyes@gmail.com', 'Roberto Delos Reyes', '09212408565', 1, '2007-04-14', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-pauline-delos-reyes.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-17 06:40:00', '2026-10-01 15:13:33'),
(33, 391, 'Froilan', 'Natividad', '09212408565', 'froilan.natividad@gmail.com', 'Lourdes Natividad', '09212408565', 1, '2006-05-31', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-froilan-natividad.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-20 07:40:00', '2026-10-01 15:13:35'),
(34, 392, 'Krizia', 'Natividad', '09212408565', 'krizia.natividad@gmail.com', 'Ricardo Natividad', '09212408565', 0, '2006-03-14', 'Brgy. Malabanias, Angeles City, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-krizia-natividad.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-26 08:40:00', '2026-10-01 15:13:36'),
(35, 393, 'Jamaica', 'Evangelista', '09212408565', 'jamaica.evangelista@gmail.com', 'Elena Evangelista', '09212408565', 1, '2004-10-07', 'Brgy. Bagumbayan, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-jamaica-evangelista.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-01 01:40:00', '2026-10-01 15:13:38'),
(36, 394, 'Patrick', 'Espiritu', '09565233662', 'patrick.espiritu@gmail.com', 'Manuel Espiritu', '09566025562', 1, '2007-09-04', 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-patrick-espiritu.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-28 02:40:00', '2026-10-01 15:13:40'),
(37, 395, 'Lester', 'Ventura', '09665241581', 'lester.ventura@gmail.com', 'Gemma Ventura', '09666033481', 1, '2005-03-06', 'Brgy. Sto. Niño, San Fernando, La Union', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-lester-ventura.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-27 03:40:00', '2026-10-01 15:13:42'),
(38, 396, 'Tristan', 'Dimaculangan', '09212408565', 'tristan.dimaculangan@gmail.com', 'Arturo Dimaculangan', '09212408565', 1, '1995-12-05', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-tristan-dimaculangan.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-30 04:40:00', '2026-10-01 15:13:43'),
(39, 397, 'Aaron', 'Hidalgo', '09085257419', 'aaron.hidalgo@gmail.com', 'Rosario Hidalgo', '09086049319', 1, '2002-11-15', 'Brgy. Bayanan, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-aaron-hidalgo.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-02-06 05:40:00', '2026-10-01 15:13:45'),
(40, 398, 'Carl', 'Lagman', '09955265338', 'carl.lagman@gmail.com', 'Danilo Lagman', '09956057238', 1, '1998-03-28', 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-carl-lagman.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-06 06:40:00', '2026-10-01 15:13:46'),
(41, 399, 'Gerald', 'Delos Reyes', '09175273257', 'gerald.delosreyes@gmail.com', 'Teresa Delos Reyes', '09176065157', 0, '2005-04-20', 'Brgy. Poblacion, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-gerald-delos-reyes.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-28 07:40:00', '2026-10-01 15:13:47'),
(42, 400, 'Darwin', 'Zaragoza', '09185281176', 'darwin.zaragoza@gmail.com', 'Roberto Zaragoza', '09186073076', 1, '2004-04-22', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-darwin-zaragoza.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-04 08:40:00', '2026-10-01 15:13:48'),
(43, 401, 'Darwin', 'Natividad', '09275289095', 'darwin.natividad@gmail.com', 'Lourdes Natividad', '09276080995', 1, '2006-01-09', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-darwin-natividad.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-08 01:40:00', '2026-10-01 15:13:49'),
(44, 402, 'Jamaica', 'Cabrera', '09395297014', 'jamaica.cabrera@gmail.com', 'Ricardo Cabrera', '09396088914', 1, '2004-04-08', 'Brgy. Malabanias, Angeles City, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-jamaica-cabrera.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-16 02:40:00', '2026-10-01 15:13:50'),
(45, 403, 'Odessa', 'Quinto', '09455304933', 'odessa.quinto@gmail.com', 'Elena Quinto', '09456096833', 1, '2004-05-14', 'Brgy. Bagumbayan, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-odessa-quinto.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-26 03:40:00', '2026-10-01 15:13:52'),
(46, 404, 'Elaine', 'Aguilar', '09565312852', 'elaine.aguilar@gmail.com', 'Manuel Aguilar', '09566104752', 1, '2006-08-02', 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-elaine-aguilar.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-11-04 04:40:00', '2026-10-01 15:13:53'),
(47, 405, 'Aaron', 'Romualdez', '09665320771', 'aaron.romualdez@gmail.com', 'Gemma Romualdez', '09666112671', 1, '2005-10-07', 'Brgy. Sto. Niño, San Fernando, La Union', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-aaron-romualdez.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-29 05:40:00', '2026-10-01 15:13:54'),
(48, 406, 'Sean', 'Dimaculangan', '09985328690', 'sean.dimaculangan@gmail.com', 'Arturo Dimaculangan', '09986120590', 0, '2001-04-24', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-sean-dimaculangan.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-25 06:40:00', '2026-10-01 15:13:55'),
(49, 407, 'Bernadette', 'Romualdez', '09212408565', 'bernadette.romualdez@gmail.com', 'Rosario Romualdez', '09212408565', 1, '2001-05-28', 'Brgy. Bayanan, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-bernadette-romualdez.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-28 07:40:00', '2026-10-01 15:13:56'),
(50, 408, 'Lester', 'Romualdez', '09955344528', 'lester.romualdez@gmail.com', 'Danilo Romualdez', '09956136428', 1, '1999-12-21', 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-lester-romualdez.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-19 08:40:00', '2026-10-01 15:13:57'),
(51, 409, 'Rhea', 'Yambao', '09175352447', 'rhea.yambao@gmail.com', 'Teresa Yambao', '09176144347', 0, '2007-01-27', 'Brgy. Poblacion, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-rhea-yambao.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-11-03 01:40:00', '2026-10-01 15:13:58'),
(52, 410, 'Jhon', 'Alcantara', '09185360366', 'jhon.alcantara@gmail.com', 'Roberto Alcantara', '09186152266', 0, '2007-05-05', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-jhon-alcantara.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-26 02:40:00', '2026-10-01 15:13:59'),
(53, 411, 'Carl', 'Dimaculangan', '09212408565', 'carl.dimaculangan@gmail.com', 'Lourdes Dimaculangan', '09212408565', 0, '2007-11-13', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-carl-dimaculangan.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-06 03:40:00', '2026-10-01 15:14:00'),
(54, 412, 'Gela', 'Aguilar', '09395376204', 'gela.aguilar@gmail.com', 'Ricardo Aguilar', '09396168104', 0, '2005-10-17', 'Brgy. Malabanias, Angeles City, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-gela-aguilar.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-17 04:40:00', '2026-10-01 15:14:01'),
(55, 413, 'Isabelle', 'Ventura', '09455384123', 'isabelle.ventura@gmail.com', 'Elena Ventura', '09456176023', 0, '2008-06-04', 'Brgy. Bagumbayan, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-isabelle-ventura.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-14 05:40:00', '2026-10-01 15:14:03'),
(56, 414, 'Froilan', 'Padilla', '09212408565', 'froilan.padilla@gmail.com', 'Manuel Padilla', '09212408565', 1, '2006-05-30', 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-froilan-padilla.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-10-31 06:40:00', '2026-10-01 15:14:04'),
(57, 415, 'Charmaine', 'Mangubat', '09212408565', 'charmaine.mangubat@gmail.com', 'Gemma Mangubat', '09212408565', 1, '2004-05-14', 'Brgy. Sto. Niño, San Fernando, La Union', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-charmaine-mangubat.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-04 07:40:00', '2026-10-01 15:14:05'),
(58, 416, 'Faith', 'Quinto', '09985407880', 'faith.quinto@gmail.com', 'Arturo Quinto', '09986199780', 0, '2002-09-14', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-faith-quinto.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-21 08:40:00', '2026-10-01 15:14:06'),
(59, 417, 'Charmaine', 'Bernardo', '09212408565', 'charmaine.bernardo@gmail.com', 'Rosario Bernardo', '09212408565', 1, '1999-01-30', 'Brgy. Bayanan, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-charmaine-bernardo.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-19 01:40:00', '2026-10-01 15:14:07'),
(60, 418, 'Kurt', 'Delos Reyes', '09955423718', 'kurt.delosreyes@gmail.com', 'Danilo Delos Reyes', '09956215618', 1, '2001-12-18', 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-kurt-delos-reyes.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-11 02:40:00', '2026-10-01 15:14:08'),
(61, 419, 'Owen', 'Lagman', '09175431637', 'owen.lagman@gmail.com', 'Teresa Lagman', '09176223537', 1, '2007-04-27', 'Brgy. Poblacion, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-owen-lagman.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-02-05 03:40:00', '2026-10-01 15:14:09'),
(62, 420, 'Bernadette', 'Yambao', '09185439556', 'bernadette.yambao@gmail.com', 'Roberto Yambao', '09186231456', 0, '2004-07-26', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-bernadette-yambao.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-14 04:40:00', '2026-10-01 15:14:10'),
(63, 421, 'Noel', 'Cabrera', '09275447475', 'noel.cabrera@gmail.com', 'Lourdes Cabrera', '09276239375', 1, '2005-10-16', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-noel-cabrera.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-29 05:40:00', '2026-10-01 15:14:12'),
(64, 422, 'Lovely', 'Zaragoza', '09395455394', 'lovely.zaragoza@gmail.com', 'Ricardo Zaragoza', '09396247294', 1, '2005-11-18', 'Brgy. Malabanias, Angeles City, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-lovely-zaragoza.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-24 06:40:00', '2026-10-01 15:14:13'),
(65, 423, 'Maureen', 'Jimenez', '09455463313', 'maureen.jimenez@gmail.com', 'Elena Jimenez', '09456255213', 1, '2007-08-10', 'Brgy. Bagumbayan, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-maureen-jimenez.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-09 07:40:00', '2026-10-01 15:14:14'),
(66, 424, 'Krizia', 'Ventura', '09565471232', 'krizia.ventura@gmail.com', 'Manuel Ventura', '09566263132', 1, '2005-03-03', 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-krizia-ventura.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-27 08:40:00', '2026-10-01 15:14:15'),
(67, 425, 'Darwin', 'Sarmiento', '09665479151', 'darwin.sarmiento@gmail.com', 'Gemma Sarmiento', '09666271051', 1, '2008-03-28', 'Brgy. Sto. Niño, San Fernando, La Union', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-darwin-sarmiento.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-11 01:40:00', '2026-10-01 15:14:16'),
(68, 426, 'Earl', 'Bonifacio', '09985487070', 'earl.bonifacio@gmail.com', 'Arturo Bonifacio', '09986278970', 0, '2000-08-25', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-earl-bonifacio.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-13 02:40:00', '2026-10-01 15:14:17'),
(69, 427, 'Gela', 'Bonifacio', '09212408565', 'gela.bonifacio@gmail.com', 'Rosario Bonifacio', '09212408565', 0, '2003-05-09', 'Brgy. Bayanan, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-gela-bonifacio.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-11-22 03:40:00', '2026-10-01 15:14:18'),
(70, 428, 'Althea', 'Dimaculangan', '09955502908', 'althea.dimaculangan@gmail.com', 'Danilo Dimaculangan', '09956294808', 0, '1999-09-03', 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-althea-dimaculangan.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-18 04:40:00', '2026-10-01 15:14:19'),
(71, 429, 'Isabelle', 'Alcantara', '09175510827', 'isabelle.alcantara@gmail.com', 'Teresa Alcantara', '09176302727', 1, '2006-01-16', 'Brgy. Poblacion, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-isabelle-alcantara.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-03-15 05:40:00', '2026-10-01 15:14:21'),
(72, 430, 'Carl', 'Padilla', '09185518746', 'carl.padilla@gmail.com', 'Roberto Padilla', '09186310646', 1, '2004-05-01', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-carl-padilla.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-05-10 06:40:00', '2026-10-01 15:14:22'),
(73, 431, 'Darwin', 'Evangelista', '09275526665', 'darwin.evangelista@gmail.com', 'Lourdes Evangelista', '09276318565', 0, '2008-09-07', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-darwin-evangelista.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-15 07:40:00', '2026-10-01 15:14:23'),
(74, 432, 'Ian', 'Sarmiento', '09395534584', 'ian.sarmiento@gmail.com', 'Ricardo Sarmiento', '09396326484', 1, '2005-01-04', 'Brgy. Malabanias, Angeles City, Pampanga', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-ian-sarmiento.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-26 08:40:00', '2026-10-01 15:14:24'),
(75, 433, 'Patrick', 'Delos Reyes', '09455542503', 'patrick.delosreyes@gmail.com', 'Elena Delos Reyes', '09456334403', 1, '2006-12-20', 'Brgy. Bagumbayan, Lucena City, Quezon', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-patrick-delos-reyes.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-01-10 01:40:00', '2026-10-01 15:14:25'),
(76, 434, 'Wendell', 'Catapang', '09565550422', 'wendell.catapang@gmail.com', 'Manuel Catapang', '09566342322', 0, '2004-04-02', 'Brgy. Tagapo, Sta. Rosa City, Laguna', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-wendell-catapang.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-29 02:40:00', '2026-10-01 15:14:26'),
(77, 435, 'Faith', 'Sarmiento', '09212408565', 'faith.sarmiento@gmail.com', 'Gemma Sarmiento', '09212408565', 0, '2007-07-31', 'Brgy. Sto. Niño, San Fernando, La Union', 'working_student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-faith-sarmiento.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-06-05 03:40:00', '2026-10-01 15:14:27'),
(78, 436, 'Lovely', 'Evangelista', '09985566260', 'lovely.evangelista@gmail.com', 'Arturo Evangelista', '09986358160', 1, '1999-06-18', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-lovely-evangelista.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-26 04:40:00', '2026-10-01 15:14:28'),
(79, 437, 'Harvey', 'Cabrera', '09212408565', 'harvey.cabrera@gmail.com', 'Rosario Cabrera', '09212408565', 1, '2000-08-24', 'Brgy. Bayanan, Bacoor, Cavite', 'full_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-harvey-cabrera.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-04-28 05:40:00', '2026-10-01 15:14:29'),
(80, 438, 'Tristan', 'Romualdez', '09212408565', 'tristan.romualdez@gmail.com', 'Danilo Romualdez', '09212408565', 0, '1997-04-22', 'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija', 'part_time_employee', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-tristan-romualdez.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-10-31 06:40:00', '2026-10-01 15:14:31'),
(81, 439, 'Carl', 'Espiritu', '09212408565', 'carl.espiritu@gmail.com', 'Teresa Espiritu', '09212408565', 1, '2005-11-09', 'Brgy. Poblacion, Batangas City, Batangas', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-carl-espiritu.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2025-12-18 07:40:00', '2026-10-01 15:14:32'),
(82, 440, 'Bryle', 'Mangubat', '09185597936', 'bryle.mangubat@gmail.com', 'Roberto Mangubat', '09186389836', 1, '2006-05-20', 'Brgy. San Jose, Tarlac City, Tarlac', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-bryle-mangubat.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-07-29 08:40:00', '2026-10-01 15:14:33'),
(83, 441, 'Tristan', 'Alcantara', '09275605855', 'tristan.alcantara@gmail.com', 'Lourdes Alcantara', '09276397755', 0, '2006-05-26', 'Brgy. Centro, Naga City, Camarines Sur', 'student', 'demo/sample-valid-id.png', 'application-documents/signed-contracts/demo-tristan-alcantara.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-04 01:40:00', '2026-10-01 15:14:34'),
(84, 442, 'Alyssa', 'Estrada', '09182826286', 'alyssa.estrada@gmail.com', 'Ricardo Estrada', '09183618186', 0, '2006-03-02', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-10 02:30:00', '2026-10-01 15:14:34'),
(85, 443, 'Jericho', 'Tolentino', '09272834205', 'jericho.tolentino@gmail.com', 'Elena Tolentino', '09273626105', 0, '2006-02-17', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-10-01 03:30:00', '2026-10-01 15:14:34'),
(86, 444, 'Lance', 'Estrada', '09392842124', 'lance.estrada@gmail.com', 'Manuel Estrada', '09393634024', 0, '2001-02-04', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-08-27 04:30:00', '2026-10-01 15:14:34'),
(87, 445, 'Oliver', 'Ilagan', '09452850043', 'oliver.ilagan@gmail.com', 'Teresa Ilagan', '09453641943', 0, '2006-01-22', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2026-01-24 05:30:00', '2026-10-01 15:14:34'),
(88, 446, 'Mika', 'Galang', '09562857962', 'mika.galang@gmail.com', 'Roberto Galang', '09563649862', 0, '2006-01-09', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-26 06:30:00', '2026-10-01 15:14:34'),
(89, 447, 'Vanessa', 'Sison', '09662865881', 'vanessa.sison@gmail.com', 'Lourdes Sison', '09663657781', 0, '2005-12-27', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-01 07:30:00', '2026-10-01 15:14:34'),
(90, 448, 'Kevin', 'Sison', '09982873800', 'kevin.sison@gmail.com', 'Ricardo Sison', '09983665700', 0, '2000-12-14', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-31 08:30:00', '2026-10-01 15:14:34'),
(91, 449, 'Aldrin', 'Abad', '09082881719', 'aldrin.abad@gmail.com', 'Elena Abad', '09083673619', 0, '2006-09-27', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-29 01:30:00', '2026-10-01 15:14:34'),
(92, 450, 'Franco', 'Ortega', '09952889638', 'franco.ortega@gmail.com', 'Manuel Ortega', '09953681538', 0, '2006-09-14', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-10-02 02:30:00', '2026-10-01 15:14:34'),
(93, 451, 'Bianca', 'Valdez', '09172897557', 'bianca.valdez@gmail.com', 'Teresa Valdez', '09173689457', 0, '2006-09-01', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2025-10-02 03:30:00', '2026-10-01 15:14:34'),
(94, 452, 'Kevin', 'Ilagan', '09182905476', 'kevin.ilagan@gmail.com', 'Roberto Ilagan', '09183697376', 0, '2001-08-19', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-21 04:30:00', '2026-10-01 15:14:34'),
(95, 453, 'Gian', 'Javier', '09272913395', 'gian.javier@gmail.com', 'Lourdes Javier', '09273705295', 0, '2006-08-06', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-13 05:30:00', '2026-10-01 15:14:34'),
(96, 454, 'Lianne', 'Hernandez', '09392921314', 'lianne.hernandez@gmail.com', 'Ricardo Hernandez', '09393713214', 0, '2006-07-24', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-10 06:30:00', '2026-10-01 15:14:34'),
(97, 455, 'Renz', 'Figueroa', '09452929233', 'renz.figueroa@gmail.com', 'Elena Figueroa', '09453721133', 0, '2006-07-11', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-28 07:30:00', '2026-10-01 15:14:34'),
(98, 456, 'Nina', 'Macaraeg', '09562937152', 'nina.macaraeg@gmail.com', 'Manuel Macaraeg', '09563729052', 0, '2001-06-28', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-04 08:30:00', '2026-10-01 15:14:34'),
(99, 457, 'Franco', 'Panganiban', '09662945071', 'franco.panganiban@gmail.com', 'Teresa Panganiban', '09663736971', 0, '2006-06-15', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-07 01:30:00', '2026-10-01 15:14:34'),
(100, 458, 'Bryan', 'Guevarra', '09982952990', 'bryan.guevarra@gmail.com', 'Roberto Guevarra', '09983744890', 0, '2006-06-02', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2026-02-04 02:30:00', '2026-10-01 15:14:34'),
(101, 459, 'Gwen', 'Figueroa', '09082960909', 'gwen.figueroa@gmail.com', 'Lourdes Figueroa', '09083752809', 0, '2006-05-20', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-04 03:30:00', '2026-10-01 15:14:34'),
(102, 460, 'Lianne', 'Ilagan', '09952968828', 'lianne.ilagan@gmail.com', 'Ricardo Ilagan', '09953760728', 0, '2001-05-07', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-02 04:30:00', '2026-10-01 15:14:34'),
(103, 461, 'Irish', 'Castillo', '09172976747', 'irish.castillo@gmail.com', 'Elena Castillo', '09173768647', 0, '2006-04-24', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-08-29 05:30:00', '2026-10-01 15:14:34'),
(104, 462, 'Pia', 'Valdez', '09182984666', 'pia.valdez@gmail.com', 'Manuel Valdez', '09183776566', 0, '2006-04-11', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-23 06:30:00', '2026-10-01 15:14:34'),
(105, 463, 'Ivan', 'Zamora', '09272992585', 'ivan.zamora@gmail.com', 'Teresa Zamora', '09273784485', 0, '2006-03-29', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2026-02-24 07:30:00', '2026-10-01 15:14:34'),
(106, 464, 'Cedric', 'Yap', '09393000504', 'cedric.yap@gmail.com', 'Roberto Yap', '09393792404', 0, '2001-03-16', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-02 08:30:00', '2026-10-01 15:14:35'),
(107, 465, 'Ysabel', 'Ortega', '09453008423', 'ysabel.ortega@gmail.com', 'Lourdes Ortega', '09453800323', 0, '2006-03-03', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-12 01:30:00', '2026-10-01 15:14:35'),
(108, 466, 'Queenie', 'Belmonte', '09563016342', 'queenie.belmonte@gmail.com', 'Ricardo Belmonte', '09563808242', 0, '2006-02-18', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-23 02:30:00', '2026-10-01 15:14:35'),
(109, 467, 'Gian', 'Dizon', '09663024261', 'gian.dizon@gmail.com', 'Elena Dizon', '09663816161', 0, '2006-02-05', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-08-27 03:30:00', '2026-10-01 15:14:35'),
(110, 468, 'Pia', 'Ortega', '09983032180', 'pia.ortega@gmail.com', 'Manuel Ortega', '09983824080', 0, '2001-01-23', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2026-01-06 04:30:00', '2026-10-01 15:14:35'),
(111, 469, 'Gian', 'Abad', '09083040099', 'gian.abad@gmail.com', 'Teresa Abad', '09083831999', 0, '2006-01-10', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-10-03 05:30:00', '2026-10-01 15:14:35'),
(112, 470, 'Sofia', 'Panganiban', '09953048018', 'sofia.panganiban@gmail.com', 'Roberto Panganiban', '09953839918', 0, '2005-12-28', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-24 06:30:00', '2026-10-01 15:14:35'),
(113, 471, 'Hazel', 'Valdez', '09173055937', 'hazel.valdez@gmail.com', 'Lourdes Valdez', '09173847837', 0, '2005-12-15', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-08-31 02:00:00', 355, 0, 0, 0, '2026-03-20 07:30:00', '2026-10-01 15:14:35'),
(114, 472, 'Rica', 'Yap', '09183063856', 'rica.yap@gmail.com', 'Ricardo Yap', '09183855756', 0, '2001-09-28', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-06 08:30:00', '2026-10-01 15:14:35'),
(115, 473, 'Elijah', 'Rosales', '09273071775', 'elijah.rosales@gmail.com', 'Elena Rosales', '09273863675', 0, '2006-09-15', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-02 01:30:00', '2026-10-01 15:14:35'),
(116, 474, 'Ella', 'Sison', '09393079694', 'ella.sison@gmail.com', 'Manuel Sison', '09393871594', 0, '2006-09-02', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-15 02:30:00', '2026-10-01 15:14:35'),
(117, 475, 'Francine', 'Nepomuceno', '09453087613', 'francine.nepomuceno@gmail.com', 'Teresa Nepomuceno', '09453879513', 0, '2006-08-20', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-30 03:30:00', '2026-10-01 15:14:35'),
(118, 476, 'Pia', 'Figueroa', '09563095532', 'pia.figueroa@gmail.com', 'Roberto Figueroa', '09563887432', 0, '2001-08-07', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-08-31 02:00:00', 355, 0, 0, 0, '2026-03-03 04:30:00', '2026-10-01 15:14:35'),
(119, 477, 'Harold', 'Cordero', '09663103451', 'harold.cordero@gmail.com', 'Lourdes Cordero', '09663895351', 0, '2006-07-25', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-08-30 05:30:00', '2026-10-01 15:14:35'),
(120, 478, 'Irish', 'Dizon', '09983111370', 'irish.dizon@gmail.com', 'Ricardo Dizon', '09983903270', 0, '2006-07-12', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(121, 479, 'Franco', 'Galang', '09083119289', 'franco.galang@gmail.com', 'Elena Galang', '09083911189', 0, '2006-06-29', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-10-08 07:30:00', '2026-10-01 15:14:35'),
(122, 480, 'Dominic', 'Estrada', '09953127208', 'dominic.estrada@gmail.com', 'Manuel Estrada', '09953919108', 0, '2001-06-16', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2026-02-09 08:30:00', '2026-10-01 15:14:35'),
(123, 481, 'Gian', 'Nepomuceno', '09173135127', 'gian.nepomuceno@gmail.com', 'Teresa Nepomuceno', '09173927027', 0, '2006-06-03', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-04 01:30:00', '2026-10-01 15:14:35'),
(124, 482, 'Jericho', 'Enriquez', '09183143046', 'jericho.enriquez@gmail.com', 'Roberto Enriquez', '09183934946', 0, '2006-05-21', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-14 02:30:00', '2026-10-01 15:14:35'),
(125, 483, 'Renz', 'Zamora', '09273150965', 'renz.zamora@gmail.com', 'Lourdes Zamora', '09273942865', 0, '2006-05-08', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-11 03:30:00', '2026-10-01 15:14:35'),
(126, 484, 'Vanessa', 'Cordero', '09393158884', 'vanessa.cordero@gmail.com', 'Ricardo Cordero', '09393950784', 0, '2001-04-25', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2026-01-01 04:30:00', '2026-10-01 15:14:35'),
(127, 485, 'Lance', 'Agustin', '09453166803', 'lance.agustin@gmail.com', 'Elena Agustin', '09453958703', 0, '2006-04-12', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-25 05:30:00', '2026-10-01 15:14:35'),
(128, 486, 'Cedric', 'Quiambao', '09563174722', 'cedric.quiambao@gmail.com', 'Manuel Quiambao', '09563966622', 0, '2006-03-30', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-13 06:30:00', '2026-10-01 15:14:35'),
(129, 487, 'Sofia', 'Umali', '09663182641', 'sofia.umali@gmail.com', 'Teresa Umali', '09663974541', 0, '2006-03-17', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-10-04 07:30:00', '2026-10-01 15:14:35'),
(130, 488, 'Cedric', 'Rosales', '09983190560', 'cedric.rosales@gmail.com', 'Roberto Rosales', '09983982460', 0, '2001-03-04', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-25 08:30:00', '2026-10-01 15:14:35'),
(131, 489, 'Clarisse', 'Figueroa', '09083198479', 'clarisse.figueroa@gmail.com', 'Lourdes Figueroa', '09083990379', 0, '2006-02-19', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-08-30 01:30:00', '2026-10-01 15:14:35'),
(132, 490, 'Marco', 'Figueroa', '09953206398', 'marco.figueroa@gmail.com', 'Ricardo Figueroa', '09953998298', 0, '2006-02-06', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-18 02:30:00', '2026-10-01 15:14:35'),
(133, 491, 'Gian', 'Belmonte', '09173214317', 'gian.belmonte@gmail.com', 'Elena Belmonte', '09174006217', 0, '2006-01-24', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-14 03:30:00', '2026-10-01 15:14:35'),
(134, 492, 'Lianne', 'Castillo', '09183222236', 'lianne.castillo@gmail.com', 'Manuel Castillo', '09184014136', 0, '2001-01-11', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-23 04:30:00', '2026-10-01 15:14:35');
INSERT INTO `tenants` (`id`, `user_id`, `first_name`, `last_name`, `contact_number`, `email`, `emergency_contact_name`, `emergency_contact_number`, `emergency_billing_reminders`, `date_of_birth`, `home_address`, `tenant_type`, `id_document_path`, `signed_contract_path`, `status`, `deactivation_reason`, `deactivated_at`, `deactivated_by`, `is_blacklisted`, `portal_restricted`, `escalation_paused`, `created_at`, `updated_at`) VALUES
(135, 493, 'Kyla', 'Zamora', '09273230155', 'kyla.zamora@gmail.com', 'Teresa Zamora', '09274022055', 0, '2005-12-29', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-02 05:30:00', '2026-10-01 15:14:35'),
(136, 494, 'Kyla', 'Galang', '09393238074', 'kyla.galang@gmail.com', 'Roberto Galang', '09394029974', 0, '2005-12-16', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(137, 495, 'Queenie', 'Buenaventura', '09453245993', 'queenie.buenaventura@gmail.com', 'Lourdes Buenaventura', '09454037893', 0, '2006-09-29', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-07-31 02:00:00', 355, 0, 0, 0, '2026-03-04 07:30:00', '2026-10-01 15:14:35'),
(138, 496, 'Hazel', 'Nepomuceno', '09563253912', 'hazel.nepomuceno@gmail.com', 'Ricardo Nepomuceno', '09564045812', 0, '2001-09-16', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-10-02 08:30:00', '2026-10-01 15:14:35'),
(139, 497, 'Troy', 'Enriquez', '09663261831', 'troy.enriquez@gmail.com', 'Elena Enriquez', '09664053731', 0, '2006-09-03', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-09 01:30:00', '2026-10-01 15:14:35'),
(140, 498, 'Sofia', 'Macaraeg', '09983269750', 'sofia.macaraeg@gmail.com', 'Manuel Macaraeg', '09984061650', 0, '2006-08-21', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-11 02:30:00', '2026-10-01 15:14:35'),
(141, 499, 'Bianca', 'Nepomuceno', '09083277669', 'bianca.nepomuceno@gmail.com', 'Teresa Nepomuceno', '09084069569', 0, '2006-08-08', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-16 03:30:00', '2026-10-01 15:14:35'),
(142, 500, 'Bryan', 'Galang', '09953285588', 'bryan.galang@gmail.com', 'Roberto Galang', '09954077488', 0, '2001-07-26', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-02 04:30:00', '2026-10-01 15:14:35'),
(143, 501, 'Dominic', 'Abad', '09173293507', 'dominic.abad@gmail.com', 'Lourdes Abad', '09174085407', 0, '2006-07-13', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-08-31 02:00:00', 355, 0, 0, 0, '2026-01-31 05:30:00', '2026-10-01 15:14:35'),
(144, 502, 'Kevin', 'Panganiban', '09183301426', 'kevin.panganiban@gmail.com', 'Ricardo Panganiban', '09184093326', 0, '2006-06-30', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2025-11-30 02:00:00', 355, 0, 0, 0, '2025-08-22 06:30:00', '2026-10-01 15:14:35'),
(145, 503, 'Aldrin', 'Yap', '09273309345', 'aldrin.yap@gmail.com', 'Elena Yap', '09274101245', 0, '2006-06-17', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-12-13 07:30:00', '2026-10-01 15:14:35'),
(146, 504, 'Franco', 'Castillo', '09393317264', 'franco.castillo@gmail.com', 'Manuel Castillo', '09394109164', 0, '2001-06-04', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-24 08:30:00', '2026-10-01 15:14:35'),
(147, 505, 'Kevin', 'Rosales', '09453325183', 'kevin.rosales@gmail.com', 'Teresa Rosales', '09454117083', 0, '2006-05-22', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-08-24 01:30:00', '2026-10-01 15:14:35'),
(148, 506, 'Elijah', 'Domingo', '09563333102', 'elijah.domingo@gmail.com', 'Roberto Domingo', '09564125002', 0, '2006-05-09', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2026-01-19 02:30:00', '2026-10-01 15:14:35'),
(149, 507, 'Harold', 'Estrada', '09663341021', 'harold.estrada@gmail.com', 'Lourdes Estrada', '09664132921', 0, '2006-04-26', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-04 03:30:00', '2026-10-01 15:14:35'),
(150, 508, 'Ella', 'Figueroa', '09983348940', 'ella.figueroa@gmail.com', 'Ricardo Figueroa', '09984140840', 0, '2001-04-13', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2026-01-24 04:30:00', '2026-10-01 15:14:35'),
(151, 509, 'Kevin', 'Guevarra', '09083356859', 'kevin.guevarra@gmail.com', 'Elena Guevarra', '09084148759', 0, '2006-03-31', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-17 05:30:00', '2026-10-01 15:14:35'),
(152, 510, 'Queenie', 'Quiambao', '09953364778', 'queenie.quiambao@gmail.com', 'Manuel Quiambao', '09954156678', 0, '2006-03-18', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-16 06:30:00', '2026-10-01 15:14:35'),
(153, 511, 'Rica', 'Quiambao', '09173372697', 'rica.quiambao@gmail.com', 'Teresa Quiambao', '09174164597', 0, '2006-03-05', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-07 07:30:00', '2026-10-01 15:14:35'),
(154, 512, 'Nina', 'Agustin', '09183380616', 'nina.agustin@gmail.com', 'Roberto Agustin', '09184172516', 0, '2001-02-20', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-03 08:30:00', '2026-10-01 15:14:35'),
(155, 513, 'Lance', 'Panganiban', '09273388535', 'lance.panganiban@gmail.com', 'Lourdes Panganiban', '09274180435', 0, '2006-02-07', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-08-31 01:30:00', '2026-10-01 15:14:35'),
(156, 514, 'Hazel', 'Guevarra', '09393396454', 'hazel.guevarra@gmail.com', 'Ricardo Guevarra', '09394188354', 0, '2006-01-25', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2025-12-30 02:30:00', '2026-10-01 15:14:35'),
(157, 515, 'Ella', 'Rosales', '09453404373', 'ella.rosales@gmail.com', 'Elena Rosales', '09454196273', 0, '2006-01-12', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-06 03:30:00', '2026-10-01 15:14:35'),
(158, 516, 'Harold', 'Umali', '09563412292', 'harold.umali@gmail.com', 'Manuel Umali', '09564204192', 0, '2000-12-30', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-20 04:30:00', '2026-10-01 15:14:36'),
(159, 517, 'Pia', 'Abad', '09663420211', 'pia.abad@gmail.com', 'Teresa Abad', '09664212111', 0, '2005-12-17', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2026-01-04 05:30:00', '2026-10-01 15:14:36'),
(160, 518, 'Oliver', 'Figueroa', '09983428130', 'oliver.figueroa@gmail.com', 'Roberto Figueroa', '09984220030', 0, '2006-09-30', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-31 06:30:00', '2026-10-01 15:14:36'),
(161, 519, 'Ysabel', 'Galang', '09083436049', 'ysabel.galang@gmail.com', 'Lourdes Galang', '09084227949', 0, '2006-09-17', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-09-08 07:30:00', '2026-10-01 15:14:36'),
(162, 520, 'Oliver', 'Yap', '09953443968', 'oliver.yap@gmail.com', 'Ricardo Yap', '09954235868', 0, '2001-09-04', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-21 08:30:00', '2026-10-01 15:14:36'),
(163, 521, 'Kevin', 'Javier', '09173451887', 'kevin.javier@gmail.com', 'Elena Javier', '09174243787', 0, '2006-08-22', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2026-02-17 01:30:00', '2026-10-01 15:14:36'),
(164, 522, 'Queenie', 'Rosales', '09183459806', 'queenie.rosales@gmail.com', 'Manuel Rosales', '09184251706', 0, '2006-08-09', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-29 02:30:00', '2026-10-01 15:14:36'),
(165, 523, 'Irish', 'Hernandez', '09273467725', 'irish.hernandez@gmail.com', 'Teresa Hernandez', '09274259625', 0, '2006-07-27', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-23 03:30:00', '2026-10-01 15:14:36'),
(166, 524, 'Sofia', 'Javier', '09393475644', 'sofia.javier@gmail.com', 'Roberto Javier', '09394267544', 0, '2001-07-14', 'Brgy. Malabanias, Angeles City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-08 04:30:00', '2026-10-01 15:14:36'),
(167, 525, 'Vanessa', 'Ortega', '09453483563', 'vanessa.ortega@gmail.com', 'Lourdes Ortega', '09454275463', 0, '2006-07-01', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-03 05:30:00', '2026-10-01 15:14:36'),
(168, 526, 'Sofia', 'Fernandez', '09563491482', 'sofia.fernandez@gmail.com', 'Ricardo Fernandez', '09564283382', 0, '2006-06-18', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-21 06:30:00', '2026-10-01 15:14:36'),
(169, 527, 'Dominic', 'Javier', '09663499401', 'dominic.javier@gmail.com', 'Elena Javier', '09664291301', 0, '2006-06-05', 'Brgy. San Jose, Tarlac City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-08-28 07:30:00', '2026-10-01 15:14:36'),
(170, 528, 'Cedric', 'Galang', '09983507320', 'cedric.galang@gmail.com', 'Manuel Galang', '09984299220', 0, '2001-05-23', 'Brgy. Centro, Naga City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-08-29 08:30:00', '2026-10-01 15:14:36'),
(171, 529, 'Ella', 'Valdez', '09083515239', 'ella.valdez@gmail.com', 'Teresa Valdez', '09084307139', 0, '2006-05-10', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-24 01:30:00', '2026-10-01 15:14:36'),
(172, 530, 'Franco', 'Guevarra', '09953523158', 'franco.guevarra@gmail.com', 'Roberto Guevarra', '09954315058', 0, '2006-04-27', 'Brgy. Bagumbayan, Lucena City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-26 02:30:00', '2026-10-01 15:14:36'),
(173, 531, 'Janelle', 'Hernandez', '09173531077', 'janelle.hernandez@gmail.com', 'Lourdes Hernandez', '09174322977', 0, '2006-04-14', 'Brgy. Poblacion, Batangas City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-09-09 03:30:00', '2026-10-01 15:14:36'),
(174, 532, 'Janelle', 'Agustin', '09183538996', 'janelle.agustin@gmail.com', 'Ricardo Agustin', '09184330896', 0, '2001-04-01', 'Brgy. San Jose, Tarlac City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-06-30 02:00:00', 355, 0, 0, 0, '2025-12-25 04:30:00', '2026-10-01 15:14:36'),
(175, 533, 'Renz', 'Belmonte', '09273546915', 'renz.belmonte@gmail.com', 'Elena Belmonte', '09274338815', 0, '2006-03-19', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2025-12-31 02:00:00', 355, 0, 0, 0, '2025-08-27 05:30:00', '2026-10-01 15:14:36'),
(176, 534, 'Renz', 'Enriquez', '09393554834', 'renz.enriquez@gmail.com', 'Manuel Enriquez', '09394346734', 0, '2006-03-06', 'Brgy. Malabanias, Angeles City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-09-11 06:30:00', '2026-10-01 15:14:36'),
(177, 535, 'Troy', 'Ilagan', '09453562753', 'troy.ilagan@gmail.com', 'Teresa Ilagan', '09454354653', 0, '2006-02-21', 'Brgy. Bagumbayan, Lucena City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-18 07:30:00', '2026-10-01 15:14:36'),
(178, 536, 'Ivan', 'Fernandez', '09563570672', 'ivan.fernandez@gmail.com', 'Roberto Fernandez', '09564362572', 0, '2001-02-08', 'Brgy. Poblacion, Batangas City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-09-20 08:30:00', '2026-10-01 15:14:36'),
(179, 537, 'Danica', 'Quiambao', '09663578591', 'danica.quiambao@gmail.com', 'Lourdes Quiambao', '09664370491', 0, '2006-01-26', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-03-31 02:00:00', 355, 0, 0, 0, '2025-08-28 01:30:00', '2026-10-01 15:14:36'),
(180, 538, 'Jericho', 'Guevarra', '09983586510', 'jericho.guevarra@gmail.com', 'Ricardo Guevarra', '09984378410', 0, '2006-01-13', 'Brgy. Centro, Naga City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Found a job in another city.', '2026-05-31 02:00:00', 355, 0, 0, 0, '2025-09-15 02:30:00', '2026-10-01 15:14:36'),
(181, 539, 'Ysabel', 'Panganiban', '09083594429', 'ysabel.panganiban@gmail.com', 'Elena Panganiban', '09084386329', 0, '2005-12-31', 'Brgy. Malabanias, Angeles City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved in with relatives in Quezon City.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-08-26 03:30:00', '2026-10-01 15:14:36'),
(182, 540, 'Pia', 'Fernandez', '09953602348', 'pia.fernandez@gmail.com', 'Manuel Fernandez', '09954394248', 0, '2000-12-18', 'Brgy. Bagumbayan, Lucena City', 'full_time_employee', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Semester ended; will not be renewing.', '2026-04-30 02:00:00', 355, 0, 0, 0, '2025-08-29 04:30:00', '2026-10-01 15:14:36'),
(183, 541, 'Janelle', 'Panganiban', '09173610267', 'janelle.panganiban@gmail.com', 'Teresa Panganiban', '09174402167', 0, '2006-10-01', 'Brgy. Poblacion, Batangas City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Graduated -- moved back home to the province.', '2026-01-31 02:00:00', 355, 0, 0, 0, '2025-10-02 05:30:00', '2026-10-01 15:14:36'),
(184, 542, 'Gwen', 'Rosales', '09183618186', 'gwen.rosales@gmail.com', 'Roberto Rosales', '09184410086', 0, '2006-09-18', 'Brgy. San Jose, Tarlac City', 'student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Moved out at the end of contract.', '2026-07-31 02:00:00', 355, 0, 0, 0, '2026-02-02 06:30:00', '2026-10-01 15:14:36'),
(185, 543, 'Janelle', 'Macaraeg', '09273626105', 'janelle.macaraeg@gmail.com', 'Lourdes Macaraeg', '09274418005', 0, '2006-09-05', 'Brgy. Centro, Naga City', 'working_student', 'demo/sample-valid-id.png', 'contracts/dormitory-contract.pdf', 'inactive', 'Transferred to a dorm closer to the new campus.', '2026-02-28 02:00:00', 355, 0, 0, 0, '2025-09-23 07:30:00', '2026-10-01 15:14:36');

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
(1, 1, 357, NULL, 'Noted po. Papupuntahin namin ang technician bukas ng umaga.', '2026-09-11 06:30:00'),
(2, 1, NULL, 1, 'Salamat po!', '2026-09-11 09:30:00'),
(3, 1, 357, NULL, 'Nalinis na po ang filter at na-recharge ang freon. Paki-check po kung okay na.', '2026-09-11 12:30:00'),
(4, 3, 357, NULL, 'Chine-check na po ng plumber ang main line. Update po kami mamaya.', '2026-09-27 10:30:00'),
(5, 5, 355, NULL, 'Thank you for the suggestion! Hindi po kasya sa space ng lobby sa ngayon, pero isasama namin sa renovation plan next year.', '2026-09-16 08:30:00'),
(6, 6, 356, NULL, 'Hi Jasmine, naka-crop po kasi yung screenshot kaya hindi makita ang reference number. Paki-upload po ulit yung buong receipt.', '2026-10-01 15:13:01'),
(7, 7, 357, NULL, 'Napalitan na po ang lock. Paki-kuha po ang bagong susi sa front desk.', '2026-09-22 12:30:00'),
(8, 9, 357, NULL, 'Na-inspect na po, may crack sa roof gutter. Schedule ang repair ngayong Sabado.', '2026-09-28 09:30:00'),
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
(3, 'Teresita Mendoza', 'owner@nestph.test', NULL, '$2y$12$cSuBuLjwpSFB33JD/2b4EO/mIphFD0jgJZaNL/fR2vVIhup6MxVaS', 2, 1, NULL, '2026-07-24 22:12:00', '2026-10-01 15:12:48'),
(355, 'Kristine Joy Bautista', 'kristine.bautista@nestph.test', NULL, '$2y$12$cSuBuLjwpSFB33JD/2b4EO/mIphFD0jgJZaNL/fR2vVIhup6MxVaS', 2, 1, NULL, '2026-04-09 16:00:00', '2026-04-09 16:00:00'),
(356, 'Mark Anthony Villanueva', 'mark.villanueva@nestph.test', NULL, '$2y$12$cSuBuLjwpSFB33JD/2b4EO/mIphFD0jgJZaNL/fR2vVIhup6MxVaS', 2, 1, NULL, '2026-04-18 16:00:00', '2026-04-18 16:00:00'),
(357, 'Jerome Castillo', 'jerome.castillo@nestph.test', NULL, '$2y$12$cSuBuLjwpSFB33JD/2b4EO/mIphFD0jgJZaNL/fR2vVIhup6MxVaS', 2, 1, NULL, '2026-04-27 16:00:00', '2026-04-27 16:00:00'),
(358, 'Lea Mae Fernandez', 'lea.fernandez@nestph.test', NULL, '$2y$12$cSuBuLjwpSFB33JD/2b4EO/mIphFD0jgJZaNL/fR2vVIhup6MxVaS', 2, 0, NULL, '2026-05-06 16:00:00', '2026-05-06 16:00:00'),
(359, 'Maria Angelica Santos', 'maria.santos@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-02 02:15:00', '2026-04-02 02:15:00'),
(360, 'Kimberly Anne Dela Cruz', 'kimberly.delacruz@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-24 03:15:00', '2026-05-24 03:15:00'),
(361, 'Patricia Mae Gonzales', 'patricia.gonzales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-24 04:15:00', '2026-06-24 04:15:00'),
(362, 'Nicole Joy Ramos', 'nicole.ramos@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-01 05:15:00', '2026-07-01 05:15:00'),
(363, 'Juan Miguel Reyes', 'juan.reyes@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-08 06:15:00', '2026-03-08 06:15:00'),
(364, 'John Paul Mendoza', 'john.mendoza@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-06 07:15:00', '2026-05-06 07:15:00'),
(365, 'Mark Joseph Aquino', 'mark.aquino@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-27 08:15:00', '2026-07-27 08:15:00'),
(366, 'Christian Dave Torres', 'christian.torres@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-23 01:15:00', '2026-04-23 01:15:00'),
(367, 'Benjamin Robles', 'benjamin.robles@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-27 02:15:00', '2026-04-27 02:15:00'),
(368, 'Rafael Luis Navarro', 'rafael.navarro@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-05 03:15:00', '2026-04-05 03:15:00'),
(369, 'Angela Marie Villanueva', 'angela.villanueva@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-01-12 04:15:00', '2026-01-12 04:15:00'),
(370, 'Jasmine Rose Garcia', 'jasmine.garcia@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-22 05:15:00', '2026-06-22 05:15:00'),
(371, 'Camille Louise Flores', 'camille.flores@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-01 06:15:00', '2026-05-01 06:15:00'),
(372, 'Bea Katrina Pascual', 'bea.pascual@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-20 07:15:00', '2026-05-20 07:15:00'),
(373, 'Princess Joy Manalo', 'princess.manalo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-31 08:15:00', '2026-05-31 08:15:00'),
(374, 'Kathleen Mae Salazar', 'kathleen.salazar@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-09-26 01:15:00', '2026-09-26 01:15:00'),
(375, 'Carlo Miguel Bautista', 'carlo.bautista@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-30 02:15:00', '2026-06-30 02:15:00'),
(376, 'Joshua Emmanuel Lim', 'joshua.lim@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-08 03:15:00', '2026-05-08 03:15:00'),
(377, 'Paolo Andres Ocampo', 'paolo.ocampo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-09-23 04:15:00', '2026-09-23 04:15:00'),
(378, 'Andrea Nicole Tan', 'andrea.tan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-30 05:15:00', '2026-03-30 05:15:00'),
(379, 'Erika Jane Morales', 'erika.morales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-05 06:15:00', '2026-06-05 06:15:00'),
(380, 'Hannah Grace Soriano', 'hannah.soriano@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-26 07:15:00', '2026-07-26 07:15:00'),
(381, 'Gabriel Jose Rivera', 'gabriel.rivera@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-06 08:15:00', '2026-02-06 08:15:00'),
(382, 'Joseph Allan Cruz', 'joseph.cruz@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-12 01:15:00', '2026-03-12 01:15:00'),
(383, 'Stephanie Claire Uy', 'stephanie.uy@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-02-09 02:15:00', '2026-02-09 02:15:00'),
(384, 'Adrian Paul Castro', 'adrian.castro@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-03 03:15:00', '2026-06-03 03:15:00'),
(385, 'Vincent Ray Magbanua', 'vincent.magbanua@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-30 04:15:00', '2026-06-30 04:15:00'),
(386, 'Luis Antonio Del Rosario', 'luis.delrosario@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-09-11 05:15:00', '2026-09-11 05:15:00'),
(387, 'Jerome Anthony Pineda', 'jerome.pineda@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-09-03 06:15:00', '2026-09-03 06:15:00'),
(388, 'Kenneth Bryan Sy', 'kenneth.sy@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-08-26 07:15:00', '2026-08-26 07:15:00'),
(389, 'Emmanuel Jose Villareal', 'emmanuel.villareal@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-16 08:15:00', '2026-05-16 08:15:00'),
(390, 'Pauline Delos Reyes', 'pauline.delosreyes@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-17 06:40:00', '2026-07-17 06:40:00'),
(391, 'Froilan Natividad', 'froilan.natividad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-20 07:40:00', '2026-06-20 07:40:00'),
(392, 'Krizia Natividad', 'krizia.natividad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-26 08:40:00', '2026-06-26 08:40:00'),
(393, 'Jamaica Evangelista', 'jamaica.evangelista@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-01 01:40:00', '2026-07-01 01:40:00'),
(394, 'Patrick Espiritu', 'patrick.espiritu@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-28 02:40:00', '2025-12-28 02:40:00'),
(395, 'Lester Ventura', 'lester.ventura@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-01-27 03:40:00', '2026-01-27 03:40:00'),
(396, 'Tristan Dimaculangan', 'tristan.dimaculangan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-08-30 04:40:00', '2026-08-30 04:40:00'),
(397, 'Aaron Hidalgo', 'aaron.hidalgo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-02-06 05:40:00', '2026-02-06 05:40:00'),
(398, 'Carl Lagman', 'carl.lagman@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-09-06 06:40:00', '2026-09-06 06:40:00'),
(399, 'Gerald Delos Reyes', 'gerald.delosreyes@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-28 07:40:00', '2026-07-28 07:40:00'),
(400, 'Darwin Zaragoza', 'darwin.zaragoza@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-04 08:40:00', '2026-03-04 08:40:00'),
(401, 'Darwin Natividad', 'darwin.natividad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-08 01:40:00', '2025-12-08 01:40:00'),
(402, 'Jamaica Cabrera', 'jamaica.cabrera@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-16 02:40:00', '2026-07-16 02:40:00'),
(403, 'Odessa Quinto', 'odessa.quinto@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-08-26 03:40:00', '2026-08-26 03:40:00'),
(404, 'Elaine Aguilar', 'elaine.aguilar@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-11-04 04:40:00', '2025-11-04 04:40:00'),
(405, 'Aaron Romualdez', 'aaron.romualdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-29 05:40:00', '2026-03-29 05:40:00'),
(406, 'Sean Dimaculangan', 'sean.dimaculangan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-25 06:40:00', '2025-12-25 06:40:00'),
(407, 'Bernadette Romualdez', 'bernadette.romualdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-28 07:40:00', '2026-05-28 07:40:00'),
(408, 'Lester Romualdez', 'lester.romualdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-19 08:40:00', '2026-06-19 08:40:00'),
(409, 'Rhea Yambao', 'rhea.yambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-11-03 01:40:00', '2025-11-03 01:40:00'),
(410, 'Jhon Alcantara', 'jhon.alcantara@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-26 02:40:00', '2026-07-26 02:40:00'),
(411, 'Carl Dimaculangan', 'carl.dimaculangan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-06 03:40:00', '2026-03-06 03:40:00'),
(412, 'Gela Aguilar', 'gela.aguilar@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-17 04:40:00', '2026-03-17 04:40:00'),
(413, 'Isabelle Ventura', 'isabelle.ventura@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-01-14 05:40:00', '2026-01-14 05:40:00'),
(414, 'Froilan Padilla', 'froilan.padilla@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-10-31 06:40:00', '2025-10-31 06:40:00'),
(415, 'Charmaine Mangubat', 'charmaine.mangubat@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-01-04 07:40:00', '2026-01-04 07:40:00'),
(416, 'Faith Quinto', 'faith.quinto@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-21 08:40:00', '2026-07-21 08:40:00'),
(417, 'Charmaine Bernardo', 'charmaine.bernardo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-19 01:40:00', '2026-05-19 01:40:00'),
(418, 'Kurt Delos Reyes', 'kurt.delosreyes@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-11 02:40:00', '2026-07-11 02:40:00'),
(419, 'Owen Lagman', 'owen.lagman@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-02-05 03:40:00', '2026-02-05 03:40:00'),
(420, 'Bernadette Yambao', 'bernadette.yambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-14 04:40:00', '2025-12-14 04:40:00'),
(421, 'Noel Cabrera', 'noel.cabrera@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-29 05:40:00', '2026-03-29 05:40:00'),
(422, 'Lovely Zaragoza', 'lovely.zaragoza@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-08-24 06:40:00', '2026-08-24 06:40:00'),
(423, 'Maureen Jimenez', 'maureen.jimenez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-09 07:40:00', '2026-04-09 07:40:00'),
(424, 'Krizia Ventura', 'krizia.ventura@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-27 08:40:00', '2026-05-27 08:40:00'),
(425, 'Darwin Sarmiento', 'darwin.sarmiento@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-11 01:40:00', '2025-12-11 01:40:00'),
(426, 'Earl Bonifacio', 'earl.bonifacio@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-13 02:40:00', '2025-12-13 02:40:00'),
(427, 'Gela Bonifacio', 'gela.bonifacio@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-11-22 03:40:00', '2025-11-22 03:40:00'),
(428, 'Althea Dimaculangan', 'althea.dimaculangan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-18 04:40:00', '2026-05-18 04:40:00'),
(429, 'Isabelle Alcantara', 'isabelle.alcantara@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-03-15 05:40:00', '2026-03-15 05:40:00'),
(430, 'Carl Padilla', 'carl.padilla@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-05-10 06:40:00', '2026-05-10 06:40:00'),
(431, 'Darwin Evangelista', 'darwin.evangelista@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-15 07:40:00', '2025-12-15 07:40:00'),
(432, 'Ian Sarmiento', 'ian.sarmiento@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-26 08:40:00', '2026-07-26 08:40:00'),
(433, 'Patrick Delos Reyes', 'patrick.delosreyes@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-01-10 01:40:00', '2026-01-10 01:40:00'),
(434, 'Wendell Catapang', 'wendell.catapang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-29 02:40:00', '2025-12-29 02:40:00'),
(435, 'Faith Sarmiento', 'faith.sarmiento@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-06-05 03:40:00', '2026-06-05 03:40:00'),
(436, 'Lovely Evangelista', 'lovely.evangelista@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-26 04:40:00', '2026-04-26 04:40:00'),
(437, 'Harvey Cabrera', 'harvey.cabrera@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-04-28 05:40:00', '2026-04-28 05:40:00'),
(438, 'Tristan Romualdez', 'tristan.romualdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-10-31 06:40:00', '2025-10-31 06:40:00'),
(439, 'Carl Espiritu', 'carl.espiritu@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2025-12-18 07:40:00', '2025-12-18 07:40:00'),
(440, 'Bryle Mangubat', 'bryle.mangubat@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-07-29 08:40:00', '2026-07-29 08:40:00'),
(441, 'Tristan Alcantara', 'tristan.alcantara@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 1, NULL, '2026-08-04 01:40:00', '2026-08-04 01:40:00'),
(442, 'Alyssa Estrada', 'alyssa.estrada@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-10 02:30:00', '2025-09-10 02:30:00'),
(443, 'Jericho Tolentino', 'jericho.tolentino@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-01 03:30:00', '2025-10-01 03:30:00'),
(444, 'Lance Estrada', 'lance.estrada@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-27 04:30:00', '2025-08-27 04:30:00'),
(445, 'Oliver Ilagan', 'oliver.ilagan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-24 05:30:00', '2026-01-24 05:30:00'),
(446, 'Mika Galang', 'mika.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-26 06:30:00', '2025-08-26 06:30:00'),
(447, 'Vanessa Sison', 'vanessa.sison@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-01 07:30:00', '2025-09-01 07:30:00'),
(448, 'Kevin Sison', 'kevin.sison@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-31 08:30:00', '2025-08-31 08:30:00'),
(449, 'Aldrin Abad', 'aldrin.abad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-29 01:30:00', '2025-08-29 01:30:00'),
(450, 'Franco Ortega', 'franco.ortega@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-02 02:30:00', '2025-10-02 02:30:00'),
(451, 'Bianca Valdez', 'bianca.valdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-02 03:30:00', '2025-10-02 03:30:00'),
(452, 'Kevin Ilagan', 'kevin.ilagan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-21 04:30:00', '2025-09-21 04:30:00'),
(453, 'Gian Javier', 'gian.javier@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-13 05:30:00', '2025-09-13 05:30:00'),
(454, 'Lianne Hernandez', 'lianne.hernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-10 06:30:00', '2025-09-10 06:30:00'),
(455, 'Renz Figueroa', 'renz.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-28 07:30:00', '2025-09-28 07:30:00'),
(456, 'Nina Macaraeg', 'nina.macaraeg@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-04 08:30:00', '2025-09-04 08:30:00'),
(457, 'Franco Panganiban', 'franco.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-07 01:30:00', '2025-09-07 01:30:00'),
(458, 'Bryan Guevarra', 'bryan.guevarra@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-04 02:30:00', '2026-02-04 02:30:00'),
(459, 'Gwen Figueroa', 'gwen.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-04 03:30:00', '2025-09-04 03:30:00'),
(460, 'Lianne Ilagan', 'lianne.ilagan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-02 04:30:00', '2025-09-02 04:30:00'),
(461, 'Irish Castillo', 'irish.castillo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-29 05:30:00', '2025-08-29 05:30:00'),
(462, 'Pia Valdez', 'pia.valdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-23 06:30:00', '2025-08-23 06:30:00'),
(463, 'Ivan Zamora', 'ivan.zamora@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-24 07:30:00', '2026-02-24 07:30:00'),
(464, 'Cedric Yap', 'cedric.yap@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-02 08:30:00', '2025-09-02 08:30:00'),
(465, 'Ysabel Ortega', 'ysabel.ortega@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-12 01:30:00', '2025-09-12 01:30:00'),
(466, 'Queenie Belmonte', 'queenie.belmonte@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-23 02:30:00', '2025-09-23 02:30:00'),
(467, 'Gian Dizon', 'gian.dizon@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-27 03:30:00', '2025-08-27 03:30:00'),
(468, 'Pia Ortega', 'pia.ortega@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-06 04:30:00', '2026-01-06 04:30:00'),
(469, 'Gian Abad', 'gian.abad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-03 05:30:00', '2025-10-03 05:30:00'),
(470, 'Sofia Panganiban', 'sofia.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-24 06:30:00', '2025-08-24 06:30:00'),
(471, 'Hazel Valdez', 'hazel.valdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-03-20 07:30:00', '2026-03-20 07:30:00'),
(472, 'Rica Yap', 'rica.yap@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-06 08:30:00', '2025-09-06 08:30:00'),
(473, 'Elijah Rosales', 'elijah.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-02 01:30:00', '2025-09-02 01:30:00'),
(474, 'Ella Sison', 'ella.sison@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-15 02:30:00', '2025-09-15 02:30:00'),
(475, 'Francine Nepomuceno', 'francine.nepomuceno@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-30 03:30:00', '2025-08-30 03:30:00'),
(476, 'Pia Figueroa', 'pia.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-03-03 04:30:00', '2026-03-03 04:30:00'),
(477, 'Harold Cordero', 'harold.cordero@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-30 05:30:00', '2025-08-30 05:30:00'),
(478, 'Irish Dizon', 'irish.dizon@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-16 06:30:00', '2025-09-16 06:30:00'),
(479, 'Franco Galang', 'franco.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-08 07:30:00', '2025-10-08 07:30:00'),
(480, 'Dominic Estrada', 'dominic.estrada@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-09 08:30:00', '2026-02-09 08:30:00'),
(481, 'Gian Nepomuceno', 'gian.nepomuceno@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-04 01:30:00', '2025-09-04 01:30:00'),
(482, 'Jericho Enriquez', 'jericho.enriquez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-14 02:30:00', '2025-09-14 02:30:00'),
(483, 'Renz Zamora', 'renz.zamora@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-11 03:30:00', '2025-09-11 03:30:00'),
(484, 'Vanessa Cordero', 'vanessa.cordero@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-01 04:30:00', '2026-01-01 04:30:00'),
(485, 'Lance Agustin', 'lance.agustin@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-25 05:30:00', '2025-09-25 05:30:00'),
(486, 'Cedric Quiambao', 'cedric.quiambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-13 06:30:00', '2025-09-13 06:30:00'),
(487, 'Sofia Umali', 'sofia.umali@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-04 07:30:00', '2025-10-04 07:30:00'),
(488, 'Cedric Rosales', 'cedric.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-25 08:30:00', '2025-08-25 08:30:00'),
(489, 'Clarisse Figueroa', 'clarisse.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-30 01:30:00', '2025-08-30 01:30:00'),
(490, 'Marco Figueroa', 'marco.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-18 02:30:00', '2025-09-18 02:30:00'),
(491, 'Gian Belmonte', 'gian.belmonte@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-14 03:30:00', '2025-09-14 03:30:00'),
(492, 'Lianne Castillo', 'lianne.castillo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-23 04:30:00', '2025-09-23 04:30:00'),
(493, 'Kyla Zamora', 'kyla.zamora@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-02 05:30:00', '2025-09-02 05:30:00'),
(494, 'Kyla Galang', 'kyla.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-16 06:30:00', '2025-09-16 06:30:00'),
(495, 'Queenie Buenaventura', 'queenie.buenaventura@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-03-04 07:30:00', '2026-03-04 07:30:00'),
(496, 'Hazel Nepomuceno', 'hazel.nepomuceno@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-02 08:30:00', '2025-10-02 08:30:00'),
(497, 'Troy Enriquez', 'troy.enriquez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-09 01:30:00', '2025-09-09 01:30:00'),
(498, 'Sofia Macaraeg', 'sofia.macaraeg@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-11 02:30:00', '2025-09-11 02:30:00'),
(499, 'Bianca Nepomuceno', 'bianca.nepomuceno@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-16 03:30:00', '2025-09-16 03:30:00'),
(500, 'Bryan Galang', 'bryan.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-02 04:30:00', '2025-09-02 04:30:00'),
(501, 'Dominic Abad', 'dominic.abad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-31 05:30:00', '2026-01-31 05:30:00'),
(502, 'Kevin Panganiban', 'kevin.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-22 06:30:00', '2025-08-22 06:30:00'),
(503, 'Aldrin Yap', 'aldrin.yap@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-12-13 07:30:00', '2025-12-13 07:30:00'),
(504, 'Franco Castillo', 'franco.castillo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-24 08:30:00', '2025-09-24 08:30:00'),
(505, 'Kevin Rosales', 'kevin.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-24 01:30:00', '2025-08-24 01:30:00'),
(506, 'Elijah Domingo', 'elijah.domingo@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-19 02:30:00', '2026-01-19 02:30:00'),
(507, 'Harold Estrada', 'harold.estrada@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-04 03:30:00', '2025-09-04 03:30:00'),
(508, 'Ella Figueroa', 'ella.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-24 04:30:00', '2026-01-24 04:30:00'),
(509, 'Kevin Guevarra', 'kevin.guevarra@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-17 05:30:00', '2025-09-17 05:30:00'),
(510, 'Queenie Quiambao', 'queenie.quiambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-16 06:30:00', '2025-09-16 06:30:00'),
(511, 'Rica Quiambao', 'rica.quiambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-07 07:30:00', '2025-09-07 07:30:00'),
(512, 'Nina Agustin', 'nina.agustin@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-03 08:30:00', '2025-09-03 08:30:00'),
(513, 'Lance Panganiban', 'lance.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-31 01:30:00', '2025-08-31 01:30:00'),
(514, 'Hazel Guevarra', 'hazel.guevarra@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-12-30 02:30:00', '2025-12-30 02:30:00'),
(515, 'Ella Rosales', 'ella.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-06 03:30:00', '2025-09-06 03:30:00'),
(516, 'Harold Umali', 'harold.umali@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-20 04:30:00', '2025-09-20 04:30:00'),
(517, 'Pia Abad', 'pia.abad@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-01-04 05:30:00', '2026-01-04 05:30:00'),
(518, 'Oliver Figueroa', 'oliver.figueroa@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-31 06:30:00', '2025-08-31 06:30:00'),
(519, 'Ysabel Galang', 'ysabel.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-08 07:30:00', '2025-09-08 07:30:00'),
(520, 'Oliver Yap', 'oliver.yap@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-21 08:30:00', '2025-08-21 08:30:00'),
(521, 'Kevin Javier', 'kevin.javier@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-17 01:30:00', '2026-02-17 01:30:00'),
(522, 'Queenie Rosales', 'queenie.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-29 02:30:00', '2025-09-29 02:30:00'),
(523, 'Irish Hernandez', 'irish.hernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-23 03:30:00', '2025-08-23 03:30:00'),
(524, 'Sofia Javier', 'sofia.javier@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-08 04:30:00', '2025-09-08 04:30:00'),
(525, 'Vanessa Ortega', 'vanessa.ortega@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-03 05:30:00', '2025-09-03 05:30:00'),
(526, 'Sofia Fernandez', 'sofia.fernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-21 06:30:00', '2025-08-21 06:30:00'),
(527, 'Dominic Javier', 'dominic.javier@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-28 07:30:00', '2025-08-28 07:30:00'),
(528, 'Cedric Galang', 'cedric.galang@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-29 08:30:00', '2025-08-29 08:30:00'),
(529, 'Ella Valdez', 'ella.valdez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-24 01:30:00', '2025-09-24 01:30:00'),
(530, 'Franco Guevarra', 'franco.guevarra@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-26 02:30:00', '2025-09-26 02:30:00'),
(531, 'Janelle Hernandez', 'janelle.hernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-09 03:30:00', '2025-09-09 03:30:00'),
(532, 'Janelle Agustin', 'janelle.agustin@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-12-25 04:30:00', '2025-12-25 04:30:00'),
(533, 'Renz Belmonte', 'renz.belmonte@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-27 05:30:00', '2025-08-27 05:30:00'),
(534, 'Renz Enriquez', 'renz.enriquez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-11 06:30:00', '2025-09-11 06:30:00'),
(535, 'Troy Ilagan', 'troy.ilagan@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-18 07:30:00', '2025-09-18 07:30:00'),
(536, 'Ivan Fernandez', 'ivan.fernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-20 08:30:00', '2025-09-20 08:30:00'),
(537, 'Danica Quiambao', 'danica.quiambao@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-28 01:30:00', '2025-08-28 01:30:00'),
(538, 'Jericho Guevarra', 'jericho.guevarra@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-15 02:30:00', '2025-09-15 02:30:00'),
(539, 'Ysabel Panganiban', 'ysabel.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-26 03:30:00', '2025-08-26 03:30:00'),
(540, 'Pia Fernandez', 'pia.fernandez@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-08-29 04:30:00', '2025-08-29 04:30:00'),
(541, 'Janelle Panganiban', 'janelle.panganiban@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-10-02 05:30:00', '2025-10-02 05:30:00'),
(542, 'Gwen Rosales', 'gwen.rosales@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2026-02-02 06:30:00', '2026-02-02 06:30:00'),
(543, 'Janelle Macaraeg', 'janelle.macaraeg@gmail.com', NULL, '$2y$12$sZVRxFr/Zl89brat9ods5uFrKLVuatGMKy4cbg3xjGfz2ndYta9Ii', 1, 0, NULL, '2025-09-23 07:30:00', '2025-09-23 07:30:00');

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
-- Indexes for table `dormitory_charges`
--
ALTER TABLE `dormitory_charges`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `rooms_status_index` (`status`),
  ADD KEY `rooms_room_type_id_foreign` (`room_type_id`);

--
-- Indexes for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_photos_room_id_foreign` (`room_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `billing_statements`
--
ALTER TABLE `billing_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1100;

--
-- AUTO_INCREMENT for table `damages`
--
ALTER TABLE `damages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deposit_refunds`
--
ALTER TABLE `deposit_refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `dormitory_amenities`
--
ALTER TABLE `dormitory_amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dormitory_charges`
--
ALTER TABLE `dormitory_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dormitory_house_rules`
--
ALTER TABLE `dormitory_house_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `monthly_expenses`
--
ALTER TABLE `monthly_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1077;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `penalty_audit_logs`
--
ALTER TABLE `penalty_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;

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
  ADD CONSTRAINT `rooms_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL;

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
