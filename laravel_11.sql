-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 08:38 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_11`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sl_no` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `project` varchar(255) DEFAULT NULL,
  `invoice_type` varchar(255) DEFAULT NULL,
  `account_full_name` varchar(255) DEFAULT NULL,
  `from_account_type` varchar(255) DEFAULT NULL,
  `full_account_number` varchar(255) DEFAULT NULL,
  `to` varchar(255) DEFAULT NULL,
  `to_account_type` varchar(255) DEFAULT NULL,
  `name_of_beneficiary` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `name_of_bank` varchar(255) DEFAULT NULL,
  `ifsc_Code_id` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'User Logged-In with email-ID [superadmin@getnada.com]', 'User successfully logged-In', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::authenticated', 1, 'App\\Models\\User', 1, '{\"last_login_at\":\"2024-10-03 04:54:08\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"}', NULL, '2024-10-02 23:24:08', '2024-10-02 23:24:08'),
(2, 'User Logged-In with email-ID [kvats69@gmail.com]', 'User successfully logged-In', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::authenticated', 3, 'App\\Models\\User', 3, '{\"last_login_at\":\"2024-10-03 04:55:28\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"}', NULL, '2024-10-02 23:25:28', '2024-10-02 23:25:28'),
(3, 'User successfully Logout with email-ID [superadmin@getnada.com]', 'User successfully Logout', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::logout', 1, 'App\\Models\\User', 1, '{\"last_logout_at\":\"2024-10-03 05:10:32\",\"last_login_at\":\"2024-10-03 05:10:32\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"}', NULL, '2024-10-02 23:40:32', '2024-10-02 23:40:32'),
(4, 'User Logged-In with email-ID [superadmin@getnada.com]', 'User successfully logged-In', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::authenticated', 1, 'App\\Models\\User', 1, '{\"last_login_at\":\"2024-10-03 09:12:19\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"}', NULL, '2024-10-03 03:42:19', '2024-10-03 03:42:19'),
(5, 'User Logged-In with email-ID [kvats69@gmail.com]', 'User successfully logged-In', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::authenticated', 3, 'App\\Models\\User', 3, '{\"last_login_at\":\"2024-10-03 09:12:32\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/129.0.0.0 Safari\\/537.36\"}', NULL, '2024-10-03 03:42:32', '2024-10-03 03:42:32'),
(6, 'User Logged-In with email-ID [superadmin@getnada.com]', 'User successfully logged-In', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::authenticated', 1, 'App\\Models\\User', 1, '{\"last_login_at\":\"2024-11-18 03:51:49\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/130.0.0.0 Safari\\/537.36\"}', NULL, '2024-11-17 22:21:49', '2024-11-17 22:21:49'),
(7, 'Account Import', 'File Imported Successfully', 'App\\Models\\Vendor', 'App\\Http\\Controllers\\Backend\\Import\\Vendor\\VendorController::store', NULL, 'App\\Models\\User', 1, '[]', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(8, 'User successfully Logout with email-ID [superadmin@getnada.com]', 'User successfully Logout', 'App\\Models\\User', 'App\\Http\\Controllers\\Backend\\Auth\\LoginController::logout', 1, 'App\\Models\\User', 1, '{\"last_logout_at\":\"2024-11-18 06:15:57\",\"last_login_at\":\"2024-11-18 06:15:57\",\"last_login_ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/130.0.0.0 Safari\\/537.36\"}', NULL, '2024-11-18 00:45:57', '2024-11-18 00:45:57');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:21:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"view-role\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:11:\"create-role\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"edit-role\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"delete-role\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:9:\"view-user\";s:1:\"c\";s:3:\"web\";}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"create-user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"edit-user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:11:\"delete-user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"view-product\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"create-product\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"edit-product\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"delete-product\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:12;a:3:{s:1:\"a\";i:13;s:1:\"b\";s:16:\"view-beneficiary\";s:1:\"c\";s:3:\"web\";}i:13;a:3:{s:1:\"a\";i:14;s:1:\"b\";s:18:\"create-beneficiary\";s:1:\"c\";s:3:\"web\";}i:14;a:3:{s:1:\"a\";i:15;s:1:\"b\";s:16:\"edit-beneficiary\";s:1:\"c\";s:3:\"web\";}i:15;a:3:{s:1:\"a\";i:16;s:1:\"b\";s:18:\"delete-beneficiary\";s:1:\"c\";s:3:\"web\";}i:16;a:3:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"view-payment\";s:1:\"c\";s:3:\"web\";}i:17;a:3:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"create-payment\";s:1:\"c\";s:3:\"web\";}i:18;a:3:{s:1:\"a\";i:19;s:1:\"b\";s:12:\"edit-payment\";s:1:\"c\";s:3:\"web\";}i:19;a:3:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"delete-payment\";s:1:\"c\";s:3:\"web\";}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:20:\"import-payment-excel\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:4:\"User\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"Manager\";s:1:\"c\";s:3:\"web\";}}}', 1731988309);

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
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_one_id` bigint(20) UNSIGNED NOT NULL,
  `user_two_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user_one_id`, `user_two_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2024-10-02 23:24:45', '2024-10-02 23:24:45');

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
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folder_message`
--

CREATE TABLE `folder_message` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folder_id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `label_message`
--

CREATE TABLE `label_message` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label_id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `trashed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `recipient_id`, `body`, `attachment`, `is_read`, `trashed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'Hi...', 'attachments/KDp2UIBKETTgsH1ZWDiHGL0Xq2i7oc3LKn6P6WmL.webp', 0, NULL, '2024-10-02 23:24:45', '2024-10-02 23:24:45'),
(2, 1, 3, 1, 'Hi admin..', 'attachments/fip1A6HEt8ujAfEV7Fq9SzPXjXlfoDESQ2sE3YXA.webp', 0, NULL, '2024-10-02 23:26:03', '2024-10-02 23:26:03'),
(3, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:48:33', '2024-10-03 03:48:33'),
(4, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:48:54', '2024-10-03 03:48:54'),
(5, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:49:08', '2024-10-03 03:49:08'),
(6, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:49:26', '2024-10-03 03:49:26'),
(7, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:49:59', '2024-10-03 03:49:59'),
(8, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:50:20', '2024-10-03 03:50:20'),
(9, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:50:40', '2024-10-03 03:50:40'),
(10, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:51:15', '2024-10-03 03:51:15'),
(11, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:51:32', '2024-10-03 03:51:32'),
(12, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:51:50', '2024-10-03 03:51:50'),
(13, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:55:36', '2024-10-03 03:55:36'),
(14, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:57:21', '2024-10-03 03:57:21'),
(15, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:58:32', '2024-10-03 03:58:32'),
(16, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:59:14', '2024-10-03 03:59:14'),
(17, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 03:59:17', '2024-10-03 03:59:17'),
(18, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:01:36', '2024-10-03 04:01:36'),
(19, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:02:30', '2024-10-03 04:02:30'),
(20, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:03:26', '2024-10-03 04:03:26'),
(21, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:04:37', '2024-10-03 04:04:37'),
(22, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:04:49', '2024-10-03 04:04:49'),
(23, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:06:06', '2024-10-03 04:06:06'),
(24, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:07:17', '2024-10-03 04:07:17'),
(25, 1, 1, 3, 'asjkfas', NULL, 0, NULL, '2024-10-03 04:07:59', '2024-10-03 04:07:59');

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
(4, '2024_06_26_052804_create_permission_tables', 1),
(5, '2024_06_26_054523_create_products_table', 1),
(6, '2024_07_27_191356_create_accounts_table', 1),
(7, '2024_07_27_191356_create_payments_table', 1),
(8, '2024_07_27_191358_create_vendors_table', 1),
(9, '2024_07_28_055710_create_activity_log_table', 1),
(10, '2024_07_28_055711_add_event_column_to_activity_log_table', 1),
(11, '2024_07_28_055712_add_batch_uuid_column_to_activity_log_table', 1),
(12, '2024_08_11_032852_create_notifications_table', 1),
(13, '2024_08_11_183115_create_user_login_histories_table', 1),
(14, '2024_10_03_032022_create_conversations_table', 1),
(15, '2024_10_03_032138_create_messages_table', 1),
(16, '2024_10_03_033325_create_folders_table', 1),
(17, '2024_10_03_033509_create_labels_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `to` varchar(255) DEFAULT NULL,
  `from` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
  `sl_no` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `template_type` varchar(255) DEFAULT 'rtgs',
  `date` timestamp NULL DEFAULT current_timestamp(),
  `project` varchar(255) DEFAULT NULL,
  `invoice_type` varchar(255) DEFAULT NULL,
  `account_full_name` varchar(255) DEFAULT NULL,
  `from_account_type` varchar(255) DEFAULT NULL,
  `full_account_number` varchar(255) DEFAULT NULL,
  `to` varchar(255) DEFAULT NULL,
  `to_account_type` varchar(255) DEFAULT NULL,
  `name_of_beneficiary` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `name_of_bank` varchar(255) DEFAULT NULL,
  `ifsc_code_id` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view-role', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(2, 'create-role', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(3, 'edit-role', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(4, 'delete-role', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(5, 'view-user', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(6, 'create-user', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(7, 'edit-user', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(8, 'delete-user', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(9, 'view-product', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(10, 'create-product', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(11, 'edit-product', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(12, 'delete-product', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(13, 'view-beneficiary', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(14, 'create-beneficiary', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(15, 'edit-beneficiary', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(16, 'delete-beneficiary', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(17, 'view-payment', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(18, 'create-payment', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(19, 'edit-payment', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(20, 'delete-payment', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(21, 'import-payment-excel', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(2, 'Admin', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(3, 'Manager', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00'),
(4, 'User', 'web', '2024-10-02 23:24:00', '2024-10-02 23:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(6, 2),
(7, 2),
(8, 2),
(9, 4),
(10, 2),
(10, 3),
(11, 2),
(11, 3),
(12, 2),
(12, 3),
(21, 2);

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
('2KXIHNDqponsEX7jCZVzwgwSwlDVvBzak5R68Jcy', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicHBNamZXckZpbVRNakZnYU1qWVhhOFZXcnlDb1BFT01sTGM4VERyeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9iYWNrZW5kL3BheW1lbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzMxOTAxOTA5O319', 1731910260),
('CHAsOIjy5TVmg3N0QinIcv90ZUwzQG0tZAZq31CB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlFJY1FBUVJNSUJUQ1FKZXNmT3IwUFJxY0dLamlqOW9hZDNjejVoYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9iYWNrZW5kL2xvZ2luIjt9fQ==', 1731910557),
('cLn5b5fr13GBQe9BdQbVBOp30IAgbSjt1GxKDLcl', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidFFaVjVPUU5jM21JNHZmMExobGYyMEU4OEhlc1B4c0I0bXdHcGMzdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9jb252ZXJzYXRpb24vMyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzI3OTQ2NzUyO319', 1727947757),
('DUZP7wJU4bAqkKNEKlWaJ2uk4bNGvcsP3MfigACt', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWE03bE95Njhsek10Q3VaYU9CWW1PcU16bjMxVVJlY2VBc1JVQWRlYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9jb252ZXJzYXRpb24vMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzI3OTMxMzI4O319', 1727931448),
('fzWWqQhkj44sJeuXUsZs2b8uKSIpWp1ihdkIQq5X', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQUdVZWlTVk5ZQ1pKdUxZOEliV1NxVjVHOFVORzVVejJZRFBUQ0FkUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9pbmJveCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzI3OTMxMjQ4O319', 1727931569),
('hCJrjtYnx9qCYb9MzKs77cdtnHrLRyz72CuKu8XT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib045bHVHcGZVTlJBb0VVNFlVVGx5eTV3VzFteGpwa29ibHpGbW5rUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9iYWNrZW5kL2xvZ2luIjt9fQ==', 1727932214),
('HmroYAjBgtzL2nrYDIl3C9w3f0LDv7njyV3TW9je', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiYWppUXczTVR1MkdNaTFRSFZDNktBN0VMMmx3cDN4YVllSUpXSlpIVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1728383577),
('iN1Js6GVlF5zv91WeStQNGRQwt4WMH97Td24b1sw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiYmRKVjJ1c0pCcG9oZUNsNHhINWZPUUttSUwwdThua09TemhtZjhCSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1732511702),
('LzcCRFetQ0NMH3z9ye5Tx6GFjlKRsiWuS5cZv8Fa', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiVnFleUkwQ2U4WnRWWEI3MEhyckk4c0NXN3ZSb1B3ZE92MFZLY1ZHaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1732528005),
('nM0hpYK0nun9sLDG9haxQN6oV9kczTtjw20YwwW4', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicjhGdkZnMjE0Szh2UVJBU3YwNGdrdU9pUW1hd3BSTlRyQjAwcENNbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3Mjc5NDY3Mzk7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjYwOiJodHRwOi8vbGFyYXZlbC0xMS1yb2xlcy1hbmQtcGVybWlzc2lvbnMubG9jYWwvY29udmVyc2F0aW9uLzEiO319', 1727948378),
('QhVqgLQpzLB5THisU16KRqlCPcu3gwfjCjqBfdAf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEVEWTQwMW1YWGJmYVJsaUFiTUNkNjNyTmtxdElNdnBIMnZwdEVDdyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly9sYXJhdmVsLTExLXJvbGVzLWFuZC1wZXJtaXNzaW9ucy5sb2NhbC9iYWNrZW5kL2xvZ2luIjt9fQ==', 1727932232),
('Qz0B7MBxD7AYQXQ9x0vHaT5ovnZdfNKUW4Mh1Xoc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoid0ZaNkZwWERHd2R3MXJsZVRNREZGOENZcGluQW9YUml5ZnU0N1QzVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1727946753);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_logout_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `last_login_at`, `last_logout_at`, `last_login_ip`, `user_agent`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin', 'superadmin@getnada.com', '2024-11-18 00:45:57', '2024-11-18 00:45:57', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', NULL, '$2y$12$2PHT9i/c6TtNOcesDm2/9.VZZsMVzym3DM0cGMqXOSxXhdY86PIPK', NULL, '2024-10-02 23:24:00', '2024-11-18 00:45:57'),
(2, 'Admin', 'admin', 'admin@agetnada.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$KOkHJIwjTSVG7BWvosE0w./FV0Iam44uXgYyxEXbUZ.5k/YmjHRRK', NULL, '2024-10-02 23:24:01', '2024-10-02 23:24:01'),
(3, 'Kushal Vats', 'kvats', 'kvats69@gmail.com', '2024-10-03 03:42:32', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '$2y$12$d7DYfbpu6ccYNPeYschYMO5o5tbw2eS4jypJ1fBpJlkyv9N4l1wLK', NULL, '2024-10-02 23:24:01', '2024-10-03 03:42:32'),
(4, 'Kushal Pal Sharma', 'kushal', 'ksharma.sharma27@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$KV0gSgFZJRF2APD2kEpFFOH/g4JOqSTudQ7myOUCBDR4QpeeT/i12', NULL, '2024-10-02 23:24:01', '2024-10-02 23:24:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_login_histories`
--

CREATE TABLE `user_login_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `last_logout_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_login_histories`
--

INSERT INTO `user_login_histories` (`id`, `user_id`, `last_logout_at`, `last_login_at`, `last_login_ip`, `user_agent`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2024-10-02 23:24:08', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '2024-10-03 04:54:08', NULL),
(2, 3, NULL, '2024-10-02 23:25:28', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '2024-10-03 04:55:28', NULL),
(3, 1, '2024-10-02 23:40:32', '2024-10-02 23:40:32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '2024-10-03 05:10:32', NULL),
(4, 1, NULL, '2024-10-03 03:42:19', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '2024-10-03 09:12:19', NULL),
(5, 3, NULL, '2024-10-03 03:42:32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', NULL, '2024-10-03 09:12:32', NULL),
(6, 1, NULL, '2024-11-17 22:21:49', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', NULL, '2024-11-18 03:51:49', NULL),
(7, 1, '2024-11-18 00:45:57', '2024-11-18 00:45:57', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', NULL, '2024-11-18 06:15:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `s_no` varchar(255) DEFAULT NULL,
  `from_account_type` varchar(255) DEFAULT NULL,
  `project` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `name_of_bank` varchar(255) DEFAULT NULL,
  `ifsc_code_id` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `vendor_type` varchar(255) DEFAULT NULL,
  `vendor_code` varchar(255) DEFAULT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `vendor_nick_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `gstin` varchar(255) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `country_id` varchar(255) DEFAULT NULL,
  `state_id` varchar(255) DEFAULT NULL,
  `city_id` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `state_name` varchar(255) DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `msme` varchar(255) DEFAULT NULL,
  `msme_registration_number` varchar(255) DEFAULT NULL,
  `msme_start_date` timestamp NULL DEFAULT NULL,
  `msme_end_date` timestamp NULL DEFAULT NULL,
  `material_nature` varchar(255) DEFAULT NULL,
  `gst_defaulted` varchar(255) DEFAULT NULL,
  `section_206AB_verified` varchar(255) DEFAULT NULL,
  `benificiary_name` varchar(255) DEFAULT NULL,
  `remarks_address` varchar(255) DEFAULT NULL,
  `common_bank_details` varchar(255) DEFAULT NULL COMMENT 'Common Bank Details Required For Location Level Or Not',
  `income_tax_type` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `s_no`, `from_account_type`, `project`, `account_name`, `short_name`, `parent`, `account_number`, `name_of_bank`, `ifsc_code_id`, `ifsc_code`, `vendor_type`, `vendor_code`, `vendor_name`, `vendor_nick_name`, `email`, `mobile`, `gstin`, `pan`, `country_id`, `state_id`, `city_id`, `country_name`, `state_name`, `city_name`, `msme`, `msme_registration_number`, `msme_start_date`, `msme_end_date`, `material_nature`, `gst_defaulted`, `section_206AB_verified`, `benificiary_name`, `remarks_address`, `common_bank_details`, `income_tax_type`, `date_added`, `last_updated`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1', 'Internal', 'P1_AS', 'Escrow Account', 'Escrow A/c', NULL, '40619717901', 'Kotek', NULL, 'BNK001', 'Internal', 'I001', 'Escrow Account', 'Escrow Account', 'abc1@yopmail.com', '8010631469', 'GSTIN001', 'PAN001', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME001', 'MSMER001', '2024-08-28 18:30:00', '2024-09-28 18:30:00', 'MN001', 'GSTD001', 'SEC001', 'Escrow A/c', 'Remark 001', 'Common 001', 'Salary', '2024-08-27 18:30:00', '2024-08-27 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(2, '4', 'Internal', 'P1_AS', 'O&M Expenses Account', 'ONMExp', NULL, '40619717904', 'ICICI Bank', NULL, 'BNK004', 'Internal', 'I004', 'O&M Expenses Account', 'O&M Expenses Account', 'abc4@yopmail.com', '8010631472', 'GSTIN004', 'PAN004', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME004', 'MSMER004', '2024-08-31 18:30:00', '2024-10-01 18:30:00', 'MN004', 'GSTD004', 'SEC004', 'ONMExp', 'Remark 004', 'Common 004', 'Self', '2024-08-30 18:30:00', '2024-08-30 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(3, '6', 'Internal', 'P1_AS', 'Construction Account denominated', 'Construction a/c', NULL, '40619717906', 'Kotek', NULL, 'BNK006', 'Internal', 'I006', 'Construction Account denominated', 'Construction Account denominated', 'abc6@yopmail.com', '8010631474', 'GSTIN006', 'PAN006', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME006', 'MSMER006', '2024-09-02 18:30:00', '2024-10-03 18:30:00', 'MN006', 'GSTD006', 'SEC006', 'Construction Account denominated', 'Remark 006', 'Common 006', 'Self', '2024-09-01 18:30:00', '2024-09-01 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(4, '17', 'Internal', 'P1_AS', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account', NULL, '40619717917', 'SBI Bank', NULL, 'BNK017', 'Internal', 'I017', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account ( Common For All Projects)', 'abc17@yopmail.com', '8010631485', 'GSTIN017', 'PAN017', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME017', 'MSMER017', '2024-09-13 18:30:00', '2024-10-14 18:30:00', 'MN017', 'GSTD017', 'SEC017', 'Common Payments Pool account', 'Remark 017', 'Common 017', 'Self', '2024-09-12 18:30:00', '2024-09-12 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(5, '1', 'Internal', 'NWPPL', 'Escrow Account', 'Escrow A/c', NULL, '40619717919', 'Kotek', NULL, 'BNK001', 'Internal', 'I001', 'Escrow Account', 'Escrow Account', 'abc1@yopmail.com', '8010631469', 'GSTIN019', 'PAN019', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME001', 'MSMER001', '2024-08-28 18:30:00', '2024-09-28 18:30:00', 'MN001', 'GSTD001', 'SEC001', 'Escrow A/c', 'Remark 001', 'Common 001', 'Salary', '2024-08-27 18:30:00', '2024-08-27 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(6, '4', 'Internal', 'NWPPL', 'O&M Expenses Account', 'ONMExp', NULL, '40619717922', 'ICICI Bank', NULL, 'BNK004', 'Internal', 'I004', 'O&M Expenses Account', 'O&M Expenses Account', 'abc4@yopmail.com', '8010631472', 'GSTIN022', 'PAN022', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME004', 'MSMER004', '2024-08-31 18:30:00', '2024-10-01 18:30:00', 'MN004', 'GSTD004', 'SEC004', 'ONMExp', 'Remark 004', 'Common 004', 'Self', '2024-08-30 18:30:00', '2024-08-30 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(7, '6', 'Internal', 'NWPPL', 'Construction Account denominated', 'Construction a/c', NULL, '40619717924', 'Kotek', NULL, 'BNK006', 'Internal', 'I006', 'Construction Account denominated', 'Construction Account denominated', 'abc6@yopmail.com', '8010631474', 'GSTIN024', 'PAN024', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME006', 'MSMER006', '2024-09-02 18:30:00', '2024-10-03 18:30:00', 'MN006', 'GSTD006', 'SEC006', 'Construction Account denominated', 'Remark 006', 'Common 006', 'Self', '2024-09-01 18:30:00', '2024-09-01 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(8, '17', 'Internal', 'NWPPL', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account', NULL, '40619717935', 'SBI Bank', NULL, 'BNK017', 'Internal', 'I017', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account ( Common For All Projects)', 'abc17@yopmail.com', '8010631485', 'GSTIN035', 'PAN035', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME017', 'MSMER017', '2024-09-13 18:30:00', '2024-10-14 18:30:00', 'MN017', 'GSTD017', 'SEC017', 'Common Payments Pool account', 'Remark 017', 'Common 017', 'Self', '2024-09-12 18:30:00', '2024-09-12 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(9, '1', 'Internal', 'P5_CK', 'Escrow Account', 'Escrow A/c', NULL, '40619717937', 'Kotek', NULL, 'BNK001', 'Internal', 'I001', 'Escrow Account', 'Escrow Account', 'abc1@yopmail.com', '8010631469', 'GSTIN037', 'PAN037', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME001', 'MSMER001', '2024-08-28 18:30:00', '2024-09-28 18:30:00', 'MN001', 'GSTD001', 'SEC001', 'Escrow A/c', 'Remark 001', 'Common 001', 'Salary', '2024-08-27 18:30:00', '2024-08-27 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(10, '4', 'Internal', 'P5_CK', 'O&M Expenses Account', 'ONMExp', NULL, '40619717940', 'ICICI Bank', NULL, 'BNK004', 'Internal', 'I004', 'O&M Expenses Account', 'O&M Expenses Account', 'abc4@yopmail.com', '8010631472', 'GSTIN040', 'PAN040', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME004', 'MSMER004', '2024-08-31 18:30:00', '2024-10-01 18:30:00', 'MN004', 'GSTD004', 'SEC004', 'ONMExp', 'Remark 004', 'Common 004', 'Self', '2024-08-30 18:30:00', '2024-08-30 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(11, '6', 'Internal', 'P5_CK', 'Construction Account denominated', 'Construction a/c', NULL, '40619717942', 'Kotek', NULL, 'BNK006', 'Internal', 'I006', 'Construction Account denominated', 'Construction Account denominated', 'abc6@yopmail.com', '8010631474', 'GSTIN042', 'PAN042', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME006', 'MSMER006', '2024-09-02 18:30:00', '2024-10-03 18:30:00', 'MN006', 'GSTD006', 'SEC006', 'Construction Account denominated', 'Remark 006', 'Common 006', 'Self', '2024-09-01 18:30:00', '2024-09-01 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(12, '17', 'Internal', 'P5_CK', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account', NULL, '40619717954', 'ICICI Bank', NULL, 'BNK018', 'Internal', 'I018', 'Statutory Payments Pool account ( Common for all Projects)', 'Statutory Payments Pool account ( Common for all Projects)', 'abc18@yopmail.com', '8010631486', 'GSTIN054', 'PAN054', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME018', 'MSMER018', '2024-09-14 18:30:00', '2024-10-15 18:30:00', 'MN018', 'GSTD018', 'SEC018', 'Common Payments Pool account', 'Remark 018', 'Common 018', 'Salary', '2024-09-13 18:30:00', '2024-09-13 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(13, '1', 'Internal', 'P3_KK', 'Escrow Account', 'Escrow A/c', NULL, '40619717955', 'Kotek', NULL, 'BNK001', 'Internal', 'I001', 'Escrow Account', 'Escrow Account', 'abc1@yopmail.com', '8010631469', 'GSTIN055', 'PAN055', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME001', 'MSMER001', '2024-08-28 18:30:00', '2024-09-28 18:30:00', 'MN001', 'GSTD001', 'SEC001', 'Escrow A/c', 'Remark 001', 'Common 001', 'Salary', '2024-08-27 18:30:00', '2024-08-27 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(14, '4', 'Internal', 'P3_KK', 'O&M Expenses Account', 'ONMExp', NULL, '40619717958', 'ICICI Bank', NULL, 'BNK004', 'Internal', 'I004', 'O&M Expenses Account', 'O&M Expenses Account', 'abc4@yopmail.com', '8010631472', 'GSTIN058', 'PAN058', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME004', 'MSMER004', '2024-08-31 18:30:00', '2024-10-01 18:30:00', 'MN004', 'GSTD004', 'SEC004', 'ONMExp', 'Remark 004', 'Common 004', 'Self', '2024-08-30 18:30:00', '2024-08-30 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(15, '6', 'Internal', 'P3_KK', 'Construction Account denominated', 'Construction a/c', NULL, '40619717960', 'Kotek', NULL, 'BNK006', 'Internal', 'I006', 'Construction Account denominated', 'Construction Account denominated', 'abc6@yopmail.com', '8010631474', 'GSTIN060', 'PAN060', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME006', 'MSMER006', '2024-09-02 18:30:00', '2024-10-03 18:30:00', 'MN006', 'GSTD006', 'SEC006', 'Construction Account denominated', 'Remark 006', 'Common 006', 'Self', '2024-09-01 18:30:00', '2024-09-01 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(16, '17', 'Internal', 'P3_KK', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account', NULL, '40619717971', 'SBI Bank', NULL, 'BNK017', 'Internal', 'I017', 'Common Payments Pool account ( Common For All Projects)', 'Common Payments Pool account ( Common For All Projects)', 'abc17@yopmail.com', '8010631485', 'GSTIN071', 'PAN071', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME017', 'MSMER017', '2024-09-13 18:30:00', '2024-10-14 18:30:00', 'MN017', 'GSTD017', 'SEC017', 'Common Payments Pool account', 'Remark 017', 'Common 017', 'Self', '2024-09-12 18:30:00', '2024-09-12 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(17, NULL, 'External', NULL, NULL, 'Neeraj Khadka', NULL, '40619717972', 'ICICI Bank', 'ICICI0001184', 'BNK018', 'External', 'EX001', 'Neeraj Khadka', 'Neeraj Khadka', 'abc18@yopmail.com', '8010631486', 'GSTIN072', 'PAN072', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME018', 'MSMER018', '2024-09-13 18:30:00', '2024-10-14 18:30:00', 'MN018', 'GSTD018', 'SEC018', 'Neeraj Khadka', 'Remark 018', 'Common 018', 'Self', '2024-09-12 18:30:00', '2024-09-12 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30'),
(18, NULL, 'External', NULL, NULL, 'Rajesh Kumar Singh', NULL, '40619717973', 'HDFC bank', 'HDFC0009248', 'BNK019', 'External', 'EX002', 'Rajesh Kumar Singh', 'Rajesh Kumar Singh', 'abc19@yopmail.com', '8010631487', 'GSTIN073', 'PAN073', 'India', 'Delhi', 'Delhi', 'India', 'Delhi', 'Delhi', 'MSME019', 'MSMER019', '2024-09-13 18:30:00', '2024-10-14 18:30:00', 'MN019', 'GSTD019', 'SEC019', 'Rajesh Kumar Singh', 'Remark 019', 'Common 019', 'Self', '2024-09-12 18:30:00', '2024-09-12 18:30:00', NULL, '2024-11-17 22:40:30', '2024-11-17 22:40:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversations_user_one_id_foreign` (`user_one_id`),
  ADD KEY `conversations_user_two_id_foreign` (`user_two_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folders_user_id_foreign` (`user_id`);

--
-- Indexes for table `folder_message`
--
ALTER TABLE `folder_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_message_folder_id_foreign` (`folder_id`),
  ADD KEY `folder_message_message_id_foreign` (`message_id`);

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
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `labels_user_id_foreign` (`user_id`);

--
-- Indexes for table `label_message`
--
ALTER TABLE `label_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `label_message_label_id_foreign` (`label_id`),
  ADD KEY `label_message_message_id_foreign` (`message_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_conversation_id_foreign` (`conversation_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_recipient_id_foreign` (`recipient_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_login_histories`
--
ALTER TABLE `user_login_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_login_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_gstin_unique` (`gstin`),
  ADD UNIQUE KEY `vendors_pan_unique` (`pan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folder_message`
--
ALTER TABLE `folder_message`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `label_message`
--
ALTER TABLE `label_message`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_login_histories`
--
ALTER TABLE `user_login_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_user_one_id_foreign` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_user_two_id_foreign` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folder_message`
--
ALTER TABLE `folder_message`
  ADD CONSTRAINT `folder_message_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folder_message_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `label_message`
--
ALTER TABLE `label_message`
  ADD CONSTRAINT `label_message_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `label_message_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_login_histories`
--
ALTER TABLE `user_login_histories`
  ADD CONSTRAINT `user_login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
