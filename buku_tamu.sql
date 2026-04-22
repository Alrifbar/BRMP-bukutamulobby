-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 04:59 AM
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
-- Database: `buku_tamu`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `profile_photo`) VALUES
(1, 'Administrator', 'bisip.kementan@gmail.com', '$2y$12$gKlNd5XslqVhl4nxvivCfuWVxI4gPYJs0g3.C62.LVhY2yXEgSgHK', NULL, NULL, '2026-03-31 04:02:25', '2026-03-31 04:02:25', NULL);

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
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_core` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `form_fields`
--

INSERT INTO `form_fields` (`id`, `name`, `label`, `placeholder`, `type`, `options`, `is_required`, `is_visible`, `is_core`, `order`, `created_at`, `updated_at`) VALUES
(1, 'nama', 'Nama Lengkap', 'Masukkan nama lengkap Anda', 'text', NULL, 1, 1, 1, 1, NULL, '2026-04-13 02:18:39'),
(2, 'usia', 'Usia', '-- Pilih Usia --', 'number', NULL, 1, 1, 1, 2, NULL, '2026-04-13 02:18:39'),
(3, 'gender', 'Jenis Kelamin', '-- Pilih Jenis Kelamin --', 'select', '{\"L\":\"Laki-laki\",\"P\":\"Perempuan\"}', 1, 1, 1, 3, NULL, '2026-04-13 02:18:39'),
(4, 'no_hp', 'No. Handphone', '0812-3456-7890', 'tel', NULL, 1, 1, 1, 4, NULL, '2026-04-13 02:18:39'),
(5, 'keperluan_kategori', 'Keperluan', '-- Pilih Keperluan --', 'select', '{\"1\":\"Layanan Pengelolaan Hasil (Paten, PVT, Cipta, Merek)\",\"2\":\"Layanan Pemanfaatan Hasil (Kerja sama, Lisensi, Mediasi, Konsultasi)\",\"3\":\"Layanan Perpustakaan\",\"4\":\"Layanan Magang\",\"5\":\"Layanan Informasi dan Dokumentasi\",\"6\":\"Layanan Publikasi Warta\",\"7\":\"Rapat\\/Pertemuan\",\"8\":\"Lainnya\"}', 1, 1, 1, 5, NULL, '2026-04-13 02:18:39'),
(6, 'instansi', 'Instansi', 'Nama kantor, sekolah, atau organisasi', 'text', NULL, 1, 1, 1, 6, NULL, '2026-04-13 02:18:39'),
(7, 'pendidikan', 'Pendidikan/Pekerjaan', '-- Pilih Pendidikan/Pekerjaan --', 'select', '{\"SD\":\"SD\",\"SMP\":\"SMP\",\"SMA\\/SMK\":\"SMA\\/SMK\",\"D3\":\"D3\",\"S1\":\"S1\",\"S2\":\"S2\",\"S3\":\"S3\",\"Lainnya\":\"Lainnya\"}', 1, 1, 1, 7, NULL, '2026-04-13 02:18:39'),
(8, 'yang_ditemui', 'Yang Ditemui', 'Nama orang yang Anda temui', 'text', NULL, 1, 1, 1, 8, NULL, '2026-04-13 02:18:39'),
(9, 'email', 'E-mail', 'email@example.com', 'email', NULL, 0, 1, 1, 9, NULL, '2026-04-13 02:18:39'),
(10, 'selfie_photo', 'Selfie', NULL, 'file', NULL, 0, 1, 1, 10, NULL, '2026-04-13 02:18:39');

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

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"7301a21f-e65a-4f1d-a034-b8446513e4c4\",\"displayName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\AppendPengunjungCsv\\\":1:{s:15:\\\"\\u0000*\\u0000pengunjungId\\\";i:1;}\"},\"createdAt\":1774929850,\"delay\":null}', 0, NULL, 1774929850, 1774929850),
(2, 'default', '{\"uuid\":\"9f4405c7-7fff-41e9-a089-16324317790f\",\"displayName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\AppendPengunjungCsv\\\":1:{s:15:\\\"\\u0000*\\u0000pengunjungId\\\";i:2;}\"},\"createdAt\":1774929913,\"delay\":null}', 0, NULL, 1774929913, 1774929913),
(3, 'default', '{\"uuid\":\"6aed96a8-6d2a-4185-aa6f-e0a0dfd365fa\",\"displayName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\AppendPengunjungCsv\\\":1:{s:15:\\\"\\u0000*\\u0000pengunjungId\\\";i:3;}\"},\"createdAt\":1774943099,\"delay\":null}', 0, NULL, 1774943099, 1774943099),
(4, 'default', '{\"uuid\":\"f01ba73c-0038-44c4-bdb2-c82818187ebe\",\"displayName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\AppendPengunjungCsv\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\AppendPengunjungCsv\\\":1:{s:15:\\\"\\u0000*\\u0000pengunjungId\\\";i:4;}\"},\"createdAt\":1775090460,\"delay\":null}', 0, NULL, 1775090460, 1775090460);

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
(4, '2025_01_01_000100_create_pengunjungs_table', 1),
(5, '2026_01_07_012320_add_edit_attempts_to_pengunjung_table', 1),
(6, '2026_01_07_013633_create_admins_table', 1),
(7, '2026_01_07_015451_add_profile_photo_to_admins_table', 1),
(8, '2026_01_08_031004_add_gender_to_pengunjungs_table', 1),
(9, '2026_01_08_055342_add_selfie_photo_to_pengunjungs_table', 1),
(10, '2026_01_12_084156_create_settings_table', 1),
(11, '2026_01_14_134500_add_pendidikan_to_pengunjungs_table', 1),
(12, '2026_01_22_000000_add_pendidikan_lainnya_to_pengunjungs_table', 1),
(13, '2026_02_03_120148_change_columns_to_text_in_pengunjungs_table', 1),
(14, '2026_02_05_120000_add_soft_deletes_to_pengunjungs_table', 1),
(15, '2026_04_13_090734_create_form_fields_table', 2),
(16, '2026_04_13_090749_add_metadata_to_pengunjungs_table', 2);

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
-- Table structure for table `pengunjungs`
--

CREATE TABLE `pengunjungs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `usia` int(10) UNSIGNED DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `instansi` varchar(150) DEFAULT NULL,
  `pendidikan` varchar(50) DEFAULT NULL,
  `pendidikan_lainnya` varchar(100) DEFAULT NULL,
  `yang_ditemui` varchar(100) DEFAULT NULL,
  `keperluan_kategori` tinyint(3) UNSIGNED NOT NULL,
  `selfie_photo` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `keperluan_lainnya` varchar(255) DEFAULT NULL,
  `edit_attempts` int(11) NOT NULL DEFAULT 3,
  `unique_token` varchar(255) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `tanggal_kunjungan` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengunjungs`
--

INSERT INTO `pengunjungs` (`id`, `nama`, `usia`, `no_hp`, `email`, `instansi`, `pendidikan`, `pendidikan_lainnya`, `yang_ditemui`, `keperluan_kategori`, `selfie_photo`, `metadata`, `keperluan_lainnya`, `edit_attempts`, `unique_token`, `gender`, `tanggal_kunjungan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'jfheufuef', 1, '087801684033', 'altafrifqibareeq@gmail.com', 'vshrgsg', 'S1', NULL, 'fdfsfsfe', 4, NULL, NULL, NULL, 3, '8bVsVmRPjdaN4YJfygZDnkj7OpDzliAL', 'L', '2026-03-31 04:04:08', '2026-03-31 04:04:08', '2026-03-31 04:04:08', NULL),
(2, 'uigamhvgchvjhbj', 3, '087801684033', 'altafrifqibareeq@gmail.com', 'saffafa', 'Swasta', NULL, 'fdfsfsfe', 2, NULL, NULL, NULL, 3, 'mnHL8LBv1PRD6o5QqhKyMoQddy2iqPXX', 'P', '2026-03-31 04:05:13', '2026-03-31 04:05:13', '2026-03-31 04:05:13', NULL),
(3, 'dwdw', 0, '087801684033', 'altafrifqibareeq@gmail.com', 'dwdw', 'Swasta', NULL, 'fdfsfsfe', 1, NULL, NULL, NULL, 3, '54K6qXaqXSAVekwiIR46aTtDzfsTWO6C', 'L', '2026-03-31 07:44:57', '2026-03-31 07:44:57', '2026-03-31 07:44:57', NULL),
(4, 'Altaf Alrifbar', 0, '087801684033', 'altafrifqibareeq@gmail.com', 'dsdsds', 'Swasta', NULL, 'dagegege', 2, NULL, NULL, NULL, 3, 'IAbJmj4jAYnOKKYH9I5vb8DX6t2zNBSt', 'L', '2026-04-02 00:40:57', '2026-04-02 00:40:57', '2026-04-02 00:40:57', NULL),
(5, 'Altaf Alrifbar', 2, '087801684033', 'altafrifqibareeq@gmail.com', 'dsad', 'Swasta', NULL, 'dadsada', 1, NULL, NULL, NULL, 3, 'G5ZIKMxnJBkXWAnqIFXxFk8pssrRA8vV', 'L', '2026-04-08 02:29:29', '2026-04-08 02:29:29', '2026-04-08 02:29:29', NULL),
(6, 'Altaf Alrifba', 11, '08780168403', 'altafrifqibareeq@gmail.com', 'fgfghfgdfsdgasf', 'SD', NULL, 'dhsgafghdjfk', 1, NULL, '{\"testing\":\"srhdjfkjtrew\"}', NULL, 3, '3gOhzPpnDhcAGvCLOsOC1fGqLvFskjpP', 'L', '2026-04-13 02:27:44', '2026-04-13 02:27:44', '2026-04-13 02:27:44', NULL),
(7, 'csggigwifqbfu', 45, '98765465678', 'giowgihwio@gmail.com', 'vbvnvmhngfbsc', 'SD', NULL, 'fsgwrgwgw', 5, NULL, '[]', NULL, 3, 'hC8yUdpewvyxWtxjj47gKjvJs8iC7uSw', 'L', '2026-04-22 01:45:18', '2026-04-22 01:45:18', '2026-04-22 01:45:18', NULL);

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
('ww20DFLQ1KjL3tkgOHuhQXL4PbJdssAT4RcLEuMY', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiZTNlYmNxNFpOM0NpQW1NUVJ4b1YweXc1T1RBRWFVc010U0NJb1BQaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Nzc6Imh0dHA6Ly9sb2NhbGhvc3QvYnVrdS10YW11di41L3B1YmxpYy9hZG1pbi9yZWthcC1wZW5ndW5qdW5nP21vbnRoPTQmeWVhcj0yMDI2IjtzOjU6InJvdXRlIjtzOjIyOiJhZG1pbi5yZWthcC1wZW5ndW5qdW5nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MjoibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTU6ImFkbWluX2xvZ2dlZF9pbiI7YjoxO3M6ODoiYWRtaW5faWQiO2k6MTtzOjE2OiJhZG1pbl9sb2dpbl90aW1lIjtPOjI1OiJJbGx1bWluYXRlXFN1cHBvcnRcQ2FyYm9uIjozOntzOjQ6ImRhdGUiO3M6MjY6IjIwMjYtMDQtMjIgMDg6NDU6MzIuNDgxNjU2IjtzOjEzOiJ0aW1lem9uZV90eXBlIjtpOjM7czo4OiJ0aW1lem9uZSI7czoxMjoiQXNpYS9KYWthcnRhIjt9czoxNDoiYWRtaW5fdmVyaWZpZWQiO2I6MTt9', 1776826242);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'drive_sync_dir', 'C:\\xampp\\htdocs\\buku-tamuv.5\\storage\\app\\public', 'text', NULL, '2026-03-31 04:02:26', '2026-04-08 04:26:04'),
(2, 'auto_backup_last_run_at', '2026-04-02 07:48:51', 'text', 'Terakhir auto backup SQL', '2026-03-31 04:03:12', '2026-04-02 00:48:51'),
(3, 'auto_backup_interval', 'monthly', 'text', 'Interval backup otomatis SQL', '2026-03-31 07:46:58', '2026-03-31 07:46:58'),
(4, 'backup_sql_dir', 'C:\\xampp\\htdocs\\buku-tamuv.5\\backups', 'text', NULL, '2026-03-31 07:46:58', '2026-04-08 04:26:11'),
(5, 'auto_backup_custom_unit', 'minutes', 'text', 'Unit interval custom (minutes/hours/days)', '2026-03-31 07:46:58', '2026-03-31 07:46:58');

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
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `form_fields_name_unique` (`name`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengunjungs`
--
ALTER TABLE `pengunjungs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengunjungs_gender_index` (`gender`),
  ADD KEY `pengunjungs_deleted_at_index` (`deleted_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_fields`
--
ALTER TABLE `form_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pengunjungs`
--
ALTER TABLE `pengunjungs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
