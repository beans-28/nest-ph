-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2026 at 09:46 AM
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
(1, 3, NULL, 'manage_tenants', '2026-07-25 06:12:00'),
(2, 3, NULL, 'manage_rooms', '2026-07-25 06:12:00'),
(3, 3, NULL, 'manage_contracts', '2026-07-25 06:12:00'),
(4, 3, NULL, 'manage_billing', '2026-07-25 06:12:00'),
(5, 3, NULL, 'manage_users', '2026-07-25 06:12:00'),
(6, 3, NULL, 'view_reports', '2026-07-25 06:12:00'),
(7, 2, NULL, 'manage_tenants', '2026-07-25 06:12:00'),
(8, 2, NULL, 'manage_rooms', '2026-07-25 06:12:00'),
(9, 2, NULL, 'manage_billing', '2026-07-25 06:12:00'),
(10, 2, NULL, 'view_reports', '2026-07-25 06:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
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
  `father_name` varchar(150) DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
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

INSERT INTO `applications` (`id`, `inquiry_id`, `tenant_id`, `full_name`, `birthdate`, `gender`, `nationality`, `medical_condition`, `occupation`, `school_company`, `school_company_address`, `contact_number`, `email`, `landline`, `home_address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_email`, `emergency_contact_landline`, `father_name`, `mother_name`, `bed_id`, `preferred_start_date`, `tenant_end_date`, `type_of_tenant`, `id_document_path`, `signed_contract_path`, `dpa_consent`, `status`, `rejection_reason`, `re_application_note`, `created_by`, `approved_by`, `created_at`, `updated_at`) VALUES
(8, NULL, 13, 'adasdasdas', '2026-08-13', 'female', 'Filipino', 'None', 'adadasda', 'sdadasd', 'sadasdsd', '09223213123', 'adadas@gmail.com', NULL, 'asdadasdasd', 'asdsadasdasd', '092131231232', 'sadasdasdsad@gmail.com', NULL, 'asdads', 'dasdadasd', 34, '2026-09-02', '2026-11-26', 'student', 'application-documents/xdM4EJ5QZo9hWXroCtu3E6kfYOw0GqBkVdzXRbe5.jpg', 'application-documents/shhDmU4ZxvyXMhMj2ZAFmAxJl3STz2QCQBOSdNt6.pdf', 1, 'approved', NULL, NULL, NULL, 3, '2026-08-31 06:16:41', '2026-08-31 10:04:14'),
(9, NULL, 14, 'Test Tenant 1', '2026-08-12', 'female', 'Filipino', 'None', 'assadasdasddsa', 'sdadsd', 'sdadasd', '09778643524', 'testtenant1@gmail.com', NULL, 'asdasdasd', 'parents', '09573426732', 'parents@gmail.com', NULL, 'Father Parents', 'Mother Parents', 38, '2026-09-03', '2026-12-23', 'student', 'application-documents/uesfq0GVNL0tc8y3dFTloKnAIpePXiTGudlj3azU.jpg', 'application-documents/iY01Hw1mW8CceLrI5195ioAA0VOnRvrPSSK3K5ih.pdf', 1, 'approved', NULL, NULL, NULL, 3, '2026-08-31 10:52:25', '2026-08-31 10:53:46'),
(10, NULL, 25, 'Valid ID Check', '2005-06-04', 'female', 'Filipino', 'None', 'Student', 'PUP', 'Sta Mesa', '09867354632', 'validIDcheck@gmail.com', NULL, 'Pampanga', 'ID Mother', '09673526321', 'idparent@gmail.com', '23421', 'ID Father', 'ID Mother', 42, '2026-09-08', '2027-01-20', 'student', 'application-documents/w7J1TMMwHZFVd9utDat9ONw0oNcZSy4mOKuOWSL0.jpg', 'application-documents/mpENdhsErV3jGsQfbEALu6S4JVSPn4l9hIalA0bD.pdf', 1, 'approved', NULL, NULL, NULL, 3, '2026-09-04 05:58:12', '2026-09-04 05:59:16'),
(11, NULL, 26, 'Valid ID CheckTwo', '2008-07-04', 'male', 'Filipino', 'None', 'Student', 'PUP', 'Sta Mesa', '09273648212', 'validid2@gmail.com', NULL, 'Sta Mesa', 'Valid Mother', '09364729591', 'validmom@gmail.com', '133334', 'Valid Father', 'Valid Mother', 43, '2026-09-10', '2027-02-16', 'student', 'application-documents/TOFczC49z8YC8L7F0ByePeuWycWMrnziMdTQ8gpG.jpg', 'application-documents/6GRQ8iAXX5F2xoXmL0mOkKGcho5Fu4uE3u0qmOAp.pdf', 1, 'approved', NULL, NULL, NULL, 3, '2026-09-04 06:12:49', '2026-09-04 06:13:15'),
(12, NULL, 27, 'Tenant Check One', '2026-09-01', 'female', 'Filipino', 'None', 'Student', 'PUP', 'Sta Mesa', '09362537482', 'tenantcheck1@gmail.com', NULL, 'asdasdads', 'Tenant Mother One', '097726373482', 'tenantmom1@gmail.com', '54213', 'Tenant Father One', 'Tenant Mother One', 39, '2026-09-05', '2027-01-16', 'student', 'application-documents/iCC60BARPEwmBDlUFQq0dCTNRG0uvDMtewSdfjAw.jpg', 'application-documents/W7BBmTsPzJYQWg8jMx38fCrYVHT6W5V4hFBukQPd.pdf', 1, 'approved', NULL, NULL, NULL, 3, '2026-09-04 06:49:48', '2026-09-04 06:50:33');

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
(34, 16, 'Bed 1', 'occupied', '2026-08-30 18:57:43', '2026-09-01 10:56:54'),
(35, 16, 'Bed 2', 'occupied', '2026-08-30 18:57:43', '2026-09-03 07:57:06'),
(36, 16, 'Bed 3', 'occupied', '2026-08-30 18:57:43', '2026-09-03 11:13:25'),
(38, 17, 'Bed 1', 'occupied', '2026-08-31 10:49:31', '2026-08-31 11:21:55'),
(39, 17, 'Bed 2', 'occupied', '2026-08-31 10:49:31', '2026-09-04 06:53:18'),
(40, 17, 'Bed 3', 'vacant', '2026-08-31 10:49:31', '2026-09-03 07:56:27'),
(41, 17, 'Bed 4', 'vacant', '2026-08-31 10:49:31', '2026-09-03 07:56:27'),
(42, 18, 'Bed 1', 'reserved', '2026-09-03 06:27:16', '2026-09-04 05:58:12'),
(43, 18, 'Bed 2', 'occupied', '2026-09-03 06:27:16', '2026-09-04 06:20:12'),
(44, 18, 'Bed 3', 'vacant', '2026-09-03 06:27:16', '2026-09-03 07:56:27'),
(45, 18, 'Bed 4', 'vacant', '2026-09-03 06:27:16', '2026-09-03 06:27:16');

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
(5, 14, 13, 'move_in', '2026-09-02', '2026-09-02', '2026-09-02', 10000.00, 0.00, 0.00, 0.00, 10000.00, 'paid', '2026-08-31 10:04:14', '2026-09-01 10:56:54'),
(6, 15, 14, 'move_in', '2026-09-03', '2026-09-03', '2026-09-03', 4250.00, 0.00, 0.00, 0.00, 4250.00, 'paid', '2026-08-31 10:53:46', '2026-08-31 11:21:55'),
(15, 24, 23, 'monthly', '2026-07-24', '2026-08-23', '2026-08-24', 4000.00, 300.00, 200.00, 150.00, 4650.00, 'overdue', '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(16, 25, 24, 'monthly', '2026-08-01', '2026-08-31', '2026-08-23', 5000.00, 0.00, 0.00, 500.00, 5500.00, 'overdue', '2026-09-03 11:25:35', '2026-09-03 13:11:01'),
(17, 26, 25, 'move_in', '2026-09-08', '2026-09-08', '2026-09-08', 3250.00, 0.00, 0.00, 0.00, 3250.00, 'unpaid', '2026-09-04 05:59:16', '2026-09-04 05:59:16'),
(18, 27, 26, 'move_in', '2026-09-10', '2026-09-10', '2026-09-10', 3250.00, 0.00, 0.00, 0.00, 3250.00, 'paid', '2026-09-04 06:13:15', '2026-09-04 06:20:12'),
(19, 28, 27, 'move_in', '2026-09-05', '2026-09-05', '2026-09-05', 4250.00, 0.00, 0.00, 0.00, 4250.00, 'paid', '2026-09-04 06:50:33', '2026-09-04 06:53:18');

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
(2, 14, 17, 38, 'Lababo', 200.00, '2026-09-01', NULL, 3, '2026-09-01 11:24:38', '2026-09-01 11:24:38');

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
  `policies_file_path` varchar(255) DEFAULT NULL,
  `contract_template_path` varchar(255) DEFAULT NULL,
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

INSERT INTO `dormitory_profile` (`id`, `dorm_name`, `description`, `address`, `contact_number`, `contact_email`, `logo_path`, `policies_file_path`, `contract_template_path`, `gcash_number`, `bdo_account_number`, `payments_and_fees`, `house_rules`, `checkout_procedures`, `created_at`, `updated_at`) VALUES
(1, 'NEST.PH', 'A safe, comfortable, and affordable place to live, study, and grow.', 'Pureza Station, Manila', '0917-893-2970', 'dormitorypurezastation@gmail.com', NULL, 'policies/test.pdf', 'contracts/dormitory-contract.pdf', NULL, NULL, 'Rent may be paid in cash, GCash, or bank deposit (BDO).\n\nTenancy is subject to a three-month minimum. Tenants must provide the start\nand end date of their stay upon registration.\n\nUpon registration, new tenants pay a reservation fee composed of a security\ndeposit (one month, refundable for a 3-month contract) and one month advance\nrent. The reservation fee is non-refundable if the tenant cancels or checks\nout earlier than the three-month minimum. The deposit is returned within\n2–3 weeks after the check-out date.\n\nRent is due every 1st day of the month. For GCash or bank deposit, payment\nconfirmation must be sent to the dormitory\'s official contact channels.\n\nTenants are granted a 3-day grace period for late rent payments. Beyond the\ngrace period, a 10% penalty fee applies. Failure to pay within one month\nresults in a notice of eviction for non-payment.\n\nTenants wishing to extend their stay must give at least 1 month notice.\nMove-out requires at least 2 weeks notice; move-out schedule is end of month.', 'Alcoholic beverages, smoking, and vaping are not allowed on dormitory premises.\n\nWashing of clothes is not allowed; a laundry service is available outside.\n\nTenants are responsible for keeping common areas clean after use, and must\npromptly report any damages or issues to maintenance staff.\n\nTenants must pay for any loss or damage to dormitory property caused by\nthemselves or their guests, at the cost of the damage (minimum ₱500).\n\nOnly registered tenants may enter the rooms. Visitors may be entertained at\nthe receiving area.\n\nHazardous goods (gas, cooking stoves, flammable fuels, firearms) are strictly\nprohibited; violation carries a ₱500 fine and may be reported to authorities.\nDrugs and illegal substances are strictly prohibited and will be reported.\n\nSilence should be observed at all times out of consideration for other tenants.\nTreat fellow tenants and staff with respect — harassment, discrimination, or\nbullying will not be tolerated.\n\nManagement is not responsible for losses or injuries occurring on the premises.\nTenants should exercise care and diligence at all times.\n\nDoors and windows must be closed when using the air-conditioner. When leaving,\nturn off all faucets, showers, lights, air conditioners, and appliances, and\nlock the door. Lost or damaged keys cost ₱50 to replace.\n\nA strict NO PETS policy is enforced.\n\nCurfew hours: 11PM – 4AM. Aircon schedule: 10PM – 5AM.', 'Advanced notice: Residents planning to check out must give written notice at\nleast two weeks before their intended departure date.\n\nRoom inspection: A staff member will inspect the room/bed before check-out to\nassess damages or cleanliness issues. Rooms should be clean before inspection.\n\nDamages and repairs: Residents are responsible for damage beyond normal wear\nand tear, and will be charged for repairs or replacements.\n\nFurniture and equipment: All dormitory-provided furniture and equipment must\nbe present and in good condition. Missing or damaged items incur charges.\n\nCleanliness: Rooms must be left in move-in condition, with all personal\nbelongings removed and shared areas cleaned.\n\nTrash disposal: Dispose of all trash and recyclables in designated bins.\n\nKey return: Room keys must be returned upon check-out. Failure to return keys\nmay result in a fine.\n\nCheck-out time: Residents must vacate by 2:00 PM on the check-out date.\n\nFinal settlement: After inspection, the security deposit is returned minus any\ndeductions for damages or outstanding charges, within two weeks of check-out.', '2026-08-27 12:47:03', '2026-08-30 18:38:26');

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
(43, 23, 15, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(44, 23, 15, 2, 'sms_reminder_day1', '[pre-seeded for testing, not actually sent]', 'sent', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(45, 23, 15, 2, 'sms_reminder_day3', '[pre-seeded for testing, not actually sent]', 'sent', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(46, 23, 15, 2, 'sms_reminder_day7', '[pre-seeded for testing, not actually sent]', 'sent', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(47, 23, 15, 3, 'portal_restricted', '[pre-seeded for testing, not actually sent]', 'sent', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(48, 23, 15, 4, 'emergency_contact_notified', '[pre-seeded for testing, not actually sent]', 'sent', NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(49, 23, 15, 5, 'demand_letter_generated', 'demand-letters/23_15.pdf', 'sent', NULL, '2026-09-03 07:58:15', '2026-09-03 07:58:15'),
(89, 24, 16, 1, 'account_flagged', NULL, 'resolved', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(90, 24, 16, 2, 'sms_reminder_day1', '[TEST SEED — not actually sent] Reminder: overdue balance.', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(91, 24, 16, 2, 'sms_reminder_day3', '[TEST SEED — not actually sent] Reminder: overdue balance.', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(92, 24, 16, 2, 'sms_reminder_day7', '[TEST SEED — not actually sent] URGENT: overdue balance.', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(93, 24, 16, 3, 'portal_restricted', '[TEST SEED — not actually sent] Portal access restricted.', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(94, 24, 16, 4, 'emergency_contact_notified', '[TEST SEED — not actually sent] Emergency contact notified.', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(95, 24, 16, 5, 'demand_letter_generated', 'demand-letters/(test-seed-no-real-pdf).pdf', 'sent', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01'),
(96, 24, 16, 6, 'delinquent_blacklisted', NULL, 'resolved', NULL, '2026-09-03 13:11:01', '2026-09-03 13:11:01');

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
(3, 'Second Floor', 0.00, 0.00, 1, 'Shared rooms, east wing', '2026-07-29 22:22:25', '2026-08-26 05:49:20');

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
(1, 'Juan Dela Cruz', NULL, 'juan@example.com', NULL, 'Interested in a shared room for August', NULL, NULL, NULL, NULL, 1, 'closed', '2026-08-23 23:00:38', '2026-08-30 05:20:59'),
(3, 'Vince Lopez', '09289811405', 'vincehh28@gmail.com', NULL, 'Up to ilan yung pede sa room po?', 'hello! up to 4', '2026-08-30 05:17:57', 3, 'Standard', 1, 'contacted', '2026-08-30 05:15:43', '2026-08-30 05:17:57'),
(4, 'John Wick', '09289811405', 'johnwick@gmail.com', NULL, 'may aso po ba d2?', 'wala ssob', '2026-08-30 05:27:02', 3, NULL, 1, 'contacted', '2026-08-30 05:25:58', '2026-08-30 05:27:02'),
(5, 'Tung Tung', '09289811405', 'tungtungsahur@gmail.com', NULL, 'hey', NULL, NULL, NULL, 'Standard', 1, 'new', '2026-08-30 05:30:35', '2026-08-30 05:30:35');

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
(14, 8, 13, 34, NULL, '2026-09-02', '2026-11-26', 5000.00, NULL, 'signed', 'signed-contracts/5FYVdnBPMocx6j22WH29lux4n25icoqRuQRrEhkW.pdf', '2026-08-31 16:00:00', 'active', NULL, NULL, NULL, NULL, 3, 3, '2026-08-31 10:04:14', '2026-09-01 11:57:42'),
(15, 9, 14, 38, NULL, '2026-09-03', '2026-12-23', 2125.00, NULL, 'signed', 'signed-contracts/mezX8KAOVqUaMIzhmsqxuYfocP0dJcPPNPnklwW6.pdf', '2026-08-31 16:00:00', 'active', NULL, NULL, NULL, NULL, 3, 3, '2026-08-31 10:53:46', '2026-09-01 11:23:04'),
(24, NULL, 23, 35, NULL, '2026-07-03', NULL, 4000.00, NULL, 'pending', NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(25, NULL, 24, 36, NULL, '2026-07-03', NULL, 5000.00, NULL, 'signed', NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-03 11:13:25', '2026-09-03 11:13:25'),
(26, 10, 25, 42, NULL, '2026-09-08', '2027-01-20', 1625.00, NULL, 'signed', 'signed-contracts/kWCtNSE2guatuGtBSxT21hhi4yGq7NrJvElRnlXE.pdf', '2026-09-04 06:02:02', 'active', NULL, NULL, NULL, NULL, 3, 3, '2026-09-04 05:59:16', '2026-09-04 06:02:02'),
(27, 11, 26, 43, NULL, '2026-09-10', '2027-02-16', 1625.00, NULL, 'pending', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, 3, 3, '2026-09-04 06:13:15', '2026-09-04 06:13:15'),
(28, 12, 27, 39, NULL, '2026-09-05', '2027-01-16', 2125.00, NULL, 'signed', 'application-documents/W7BBmTsPzJYQWg8jMx38fCrYVHT6W5V4hFBukQPd.pdf', '2026-09-04 06:50:33', 'active', NULL, NULL, NULL, NULL, 3, 3, '2026-09-04 06:50:33', '2026-09-04 06:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_tickets`
--

CREATE TABLE `maintenance_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `bed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `category` enum('electrical','plumbing','furniture','cleanliness','other') NOT NULL DEFAULT 'other',
  `description` text DEFAULT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(55, '2026_09_04_153627_add_deactivated_by_to_tenants_table', 33);

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

INSERT INTO `payments` (`id`, `billing_id`, `tenant_id`, `amount_paid`, `payment_method`, `reference_number`, `payment_date`, `status`, `proof_path`, `notes`, `review_notes`, `reviewed_by`, `reviewed_at`, `recorded_by`, `created_at`) VALUES
(3, 5, 13, 10000.00, 'gcash', '1234 5467 8162', '2026-08-31', 'approved', 'payment-proofs/ypW8WoUCXSEIwMVyWEXnv1tTS7hIyxZv3itPcP6G.png', 'Time of payment: 18:19. Here po', NULL, 3, '2026-09-01 10:56:54', NULL, '2026-08-31 10:19:22'),
(4, 6, 14, 4250.00, 'gcash', '1234 5467 8161', '2026-08-31', 'approved', 'payment-proofs/Mi8dv1TOqcEKFhjqS8y37ACZbze4b233k0yVoikl.png', 'Time of payment: 18:56. sadasd', NULL, 3, '2026-08-31 11:21:55', NULL, '2026-08-31 10:56:26'),
(5, 17, 25, 3250.00, 'gcash', '1234 5467 8163', '2026-09-04', 'pending', 'payment-proofs/OzbHZkJeSFxSM89zLztosYAY4SjhnJMT2nL8Y9YL.png', 'Time of payment: 14:00. paid', NULL, NULL, NULL, NULL, '2026-09-04 06:00:36'),
(6, 18, 26, 3250.00, 'gcash', '1234 5467 8165', '2026-09-04', 'approved', 'payment-proofs/CBnUuknJf3uSv95dRLtFY0woh0Cy8uBuhBgLsPz8.png', 'Time of payment: 14:14. paid', NULL, 3, '2026-09-04 06:20:12', NULL, '2026-09-04 06:14:10'),
(7, 19, 27, 4250.00, 'gcash', '1234 5467 8176', '2026-09-04', 'approved', 'payment-proofs/aj8kAipxg9LVurlIRYX9fchoMdHg91AFuGEDk4ch.png', 'Time of payment: 14:51.', NULL, 3, '2026-09-04 06:53:18', NULL, '2026-09-04 06:51:48');

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
(3, 13, NULL, NULL, 'manual', 'Late payment fee', 250.00, NULL, 'active', NULL, '2026-09-01 10:41:18', '2026-09-01 10:41:18'),
(4, 14, 2, NULL, 'damage', 'Damage: Lababo', 200.00, NULL, 'waived', 3, '2026-09-01 11:24:38', '2026-09-01 11:30:43');

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
(6, 4, 'created', 3, 'Auto-created from damage record #2', '2026-09-01 11:24:38'),
(7, 4, 'waived', 3, 'Paid', '2026-09-01 11:30:43');

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

INSERT INTO `rooms` (`id`, `floor_id`, `room_no`, `room_type`, `amenities`, `monthly_rate`, `status`, `vr_asset_path`, `vr_caption`, `vr_visibility`, `created_at`, `updated_at`) VALUES
(16, 3, '1', 'Standard', '[]', 5000.00, 'full', NULL, NULL, 'draft', '2026-08-30 18:57:43', '2026-09-03 11:13:25'),
(17, 3, '2', 'Standard', '[]', 8500.00, 'available', NULL, NULL, 'draft', '2026-08-31 10:49:31', '2026-09-03 07:56:27'),
(18, 3, '3', 'Standard', '[]', 6500.00, 'available', NULL, NULL, 'draft', '2026-09-03 06:27:16', '2026-09-03 06:27:16');

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
('6hpqdYUvLeMynkip8ff8kgnN3LX8SCECwjv2jfYZ', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUWptakw1Zk5MQThXZ2U5TTJQdUh1cnhNRWZ6YVR4bjdNWXdzekI4ZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90ZW5hbnQtbWFuYWdlciI7czo1OiJyb3V0ZSI7czoyMDoidGVuYW50LW1hbmFnZXIuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1788507846);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `tenant_type` varchar(30) DEFAULT NULL,
  `id_document_path` varchar(255) DEFAULT NULL,
  `signed_contract_path` varchar(255) DEFAULT NULL,
  `status` enum('pending_move_in_payment','active','archived') NOT NULL DEFAULT 'pending_move_in_payment',
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

INSERT INTO `tenants` (`id`, `user_id`, `full_name`, `contact_number`, `email`, `emergency_contact_name`, `emergency_contact_number`, `date_of_birth`, `home_address`, `tenant_type`, `id_document_path`, `signed_contract_path`, `status`, `deactivation_reason`, `deactivated_at`, `deactivated_by`, `is_blacklisted`, `portal_restricted`, `escalation_paused`, `created_at`, `updated_at`) VALUES
(13, 18, 'adasdasdas', '09223213123', 'adadas@gmail.com', 'asdsadasdasd', '092131231232', NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-31 10:04:14', '2026-09-01 10:56:54'),
(14, 19, 'Test Tenant 1', '09778643524', 'testtenant1@gmail.com', 'parents', '09573426732', NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 0, 0, 0, '2026-08-31 10:53:46', '2026-08-31 11:21:55'),
(23, NULL, 'Test Delinquent - Stage 5 Only', '00000000000', 'test.delinquent.stage5only@nestph.test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending_move_in_payment', NULL, NULL, NULL, 0, 1, 0, '2026-09-03 07:57:06', '2026-09-03 07:57:06'),
(24, 20, 'Delinquency Test Tenant', '09171234567', 'delinquency.test@nestph.test', 'Test Emergency Contact', '09179876543', NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 1, 1, 0, '2026-09-03 11:13:24', '2026-09-03 13:11:01'),
(25, 21, 'Valid ID Check', '09867354632', 'validIDcheck@gmail.com', 'ID Mother', '09673526321', NULL, NULL, NULL, NULL, NULL, 'pending_move_in_payment', NULL, NULL, NULL, 0, 0, 0, '2026-09-04 05:59:16', '2026-09-04 05:59:16'),
(26, 22, 'Valid ID CheckTwo', '09273648212', 'validid2@gmail.com', 'Valid Mother', '09364729591', '2008-07-04', 'Sta Mesa', 'student', 'application-documents/TOFczC49z8YC8L7F0ByePeuWycWMrnziMdTQ8gpG.jpg', 'application-documents/6GRQ8iAXX5F2xoXmL0mOkKGcho5Fu4uE3u0qmOAp.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-04 06:13:15', '2026-09-04 06:20:12'),
(27, 23, 'Tenant Check One', '09362537482', 'tenantcheck1@gmail.com', 'Tenant Mother One', '097726373482', '2026-09-01', 'asdasdads', 'student', 'application-documents/iCC60BARPEwmBDlUFQq0dCTNRG0uvDMtewSdfjAw.jpg', 'application-documents/W7BBmTsPzJYQWg8jMx38fCrYVHT6W5V4hFBukQPd.pdf', 'active', NULL, NULL, NULL, 0, 0, 0, '2026-09-04 06:50:33', '2026-09-04 06:53:18');

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
(1, 'Test Tenant', 'tenant@nestph.test', NULL, '$2y$12$dxcMGpzmn1dy2l1.L96SEellNJOxyS8gWEm0haGvhn858I.HVXzP6', 1, 1, NULL, '2026-07-24 22:11:59', '2026-08-28 18:36:17'),
(2, 'Test Admin', 'admin@nestph.test', NULL, '$2y$12$If2FiAnM0xiHlnJA9.cgDOAChO1s6Nsp0YvsDdodxHkZKeBe4xmkm', 2, 1, NULL, '2026-07-24 22:11:59', '2026-07-24 22:11:59'),
(3, 'Test Owner', 'owner@nestph.test', NULL, '$2y$12$tBbYyy35ASpYBp1J16pNkOLef7.5rNtW/O/3jO5M.0Vkg/xe4CHfK', 2, 1, NULL, '2026-07-24 22:12:00', '2026-07-24 22:12:00'),
(9, 'John Dela Cruz', 'delacruz@gmail.com', NULL, '$2y$12$k3PCXiSoLTXe4G3NnIT24eaeQtjFdK3ckRw0kMS71z5IYiu.Jjt9a', 1, 1, NULL, '2026-08-30 06:11:32', '2026-08-30 06:11:32'),
(10, 'Carla Bugasto', 'carla@gmail.com', NULL, '$2y$12$zpurO4Udqsfep0Dga16Emuuo29pZblCvUUzyuZS1V0tFID9ToKEm2', 1, 1, NULL, '2026-08-30 19:05:31', '2026-08-30 19:05:31'),
(18, 'adasdasdas', 'adadas@gmail.com', NULL, '$2y$12$jYvF.c77Jy9w5.7rMNqogeHuWorg/.ULCOXujNRuZeDdZ24d1/yn2', 1, 1, NULL, '2026-08-31 10:04:14', '2026-08-31 10:04:14'),
(19, 'Test Tenant 1', 'testtenant1@gmail.com', NULL, '$2y$12$3B5WRI0MHOAO5U/p7mPhR.QeXvj.ZEQvZiubtg9/h1.J0wY/V8UoG', 1, 1, NULL, '2026-08-31 10:53:46', '2026-08-31 10:53:46'),
(20, 'Delinquency Test Tenant', 'delinquency.test@nestph.test', NULL, '$2y$12$Cs/NIVuc..mH3dMtzC4y6eM1g/Cidfim9eTipl3jefYr6XgQ34S7O', 1, 1, NULL, '2026-09-03 11:13:24', '2026-09-03 13:11:01'),
(21, 'Valid ID Check', 'validIDcheck@gmail.com', NULL, '$2y$12$2Jut6k1qH/hlL0YX9zdste2jp9SN/AjlAWPHnrWJlsKdRtCaAghCG', 1, 1, NULL, '2026-09-04 05:59:16', '2026-09-04 05:59:16'),
(22, 'Valid ID CheckTwo', 'validid2@gmail.com', NULL, '$2y$12$wmUAZqRpGFnyrEU4mHMV5OPW5MDbmSU47lIhy6VGEXD5fK4UY2PWu', 1, 1, NULL, '2026-09-04 06:13:15', '2026-09-04 06:35:23'),
(23, 'Tenant Check One', 'tenantcheck1@gmail.com', NULL, '$2y$12$86PWBv4cs27dKuFh9BiyFe1AYppLjEp3PHkd3CYXoUitpBBmI6lwi', 1, 1, NULL, '2026-09-04 06:50:33', '2026-09-04 06:50:33');

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

-- --------------------------------------------------------

--
-- Table structure for table `vr_scenes`
--

CREATE TABLE `vr_scenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `panorama_path` varchar(255) NOT NULL,
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
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_privilege` (`user_id`,`privilege_name`),
  ADD KEY `admin_privileges_granted_by_foreign` (`granted_by`);

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
  ADD KEY `maintenance_tickets_assigned_to_foreign` (`assigned_to`),
  ADD KEY `maintenance_tickets_resolved_by_foreign` (`resolved_by`);

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
-- AUTO_INCREMENT for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `billing_statements`
--
ALTER TABLE `billing_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `damages`
--
ALTER TABLE `damages`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lease_contracts`
--
ALTER TABLE `lease_contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `maintenance_tickets`
--
ALTER TABLE `maintenance_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penalty_audit_logs`
--
ALTER TABLE `penalty_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `vr_hotspots`
--
ALTER TABLE `vr_hotspots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vr_scenes`
--
ALTER TABLE `vr_scenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_privileges`
--
ALTER TABLE `admin_privileges`
  ADD CONSTRAINT `admin_privileges_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admin_privileges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `maintenance_tickets_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
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
