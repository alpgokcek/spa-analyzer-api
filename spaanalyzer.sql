-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 13 Şub 2020, 12:23:47
-- Sunucu sürümü: 10.1.39-MariaDB
-- PHP Sürümü: 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `spaanalyzer`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_log`
--

CREATE TABLE `admin_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `website` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `areaid` int(11) NOT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `assessment`
--

CREATE TABLE `assessment` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage` double NOT NULL DEFAULT '0',
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `authority`
--

CREATE TABLE `authority` (
  `id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `area` int(11) UNSIGNED NOT NULL,
  `user` int(11) UNSIGNED NOT NULL,
  `work` varchar(191) NOT NULL,
  `c` tinyint(1) NOT NULL,
  `r` tinyint(1) NOT NULL,
  `u` tinyint(1) NOT NULL,
  `d` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `authority`
--

INSERT INTO `authority` (`id`, `type`, `area`, `user`, `work`, `c`, `r`, `u`, `d`, `created_at`, `updated_at`) VALUES
(1, '', 7, 32, '1', 1, 1, 1, 1, '2019-11-29 09:10:46', '2019-11-29 09:10:46'),
(2, '', 8, 32, '1', 1, 1, 1, 1, '2019-11-29 09:13:09', '2019-11-29 09:13:09'),
(3, '', 10, 33, '1', 1, 1, 1, 1, '2019-12-03 12:06:14', '2019-12-03 12:06:14'),
(4, '', 18, 41, '1', 1, 1, 1, 1, '2019-12-06 11:40:09', '2019-12-06 11:40:09'),
(5, '', 18, 41, '1', 1, 1, 1, 1, '2019-12-06 11:40:09', '2019-12-06 11:40:09'),
(6, '', 19, 42, '1', 1, 1, 1, 1, '2019-12-06 11:41:49', '2019-12-06 11:41:49'),
(7, '', 21, 1, '1', 1, 1, 1, 1, '2019-12-11 11:39:04', '2019-12-11 11:39:04'),
(8, '', 22, 1, '1', 1, 1, 1, 1, '2019-12-11 11:41:30', '2019-12-11 11:41:30'),
(9, '', 25, 52, '1', 1, 1, 1, 1, '2019-12-11 11:43:30', '2019-12-11 11:43:30'),
(10, '', 27, 1, '1', 1, 1, 1, 1, '2019-12-11 12:10:02', '2019-12-11 12:10:02'),
(11, '', 27, 1, '1', 1, 1, 1, 1, '2019-12-11 12:10:02', '2019-12-11 12:10:02'),
(12, 'project', 29, 2, '1', 1, 1, 1, 1, '2019-12-24 04:56:01', '2019-12-24 04:56:01'),
(13, 'project', 1, 1, '1', 1, 1, 1, 1, '2019-12-27 09:51:07', '2019-12-27 09:51:07'),
(14, 'project', 1, 1, '1', 1, 1, 1, 1, '2019-12-27 14:14:18', '2019-12-27 14:14:18'),
(15, 'project', 2, 1, '1', 1, 1, 1, 1, '2020-01-02 10:02:25', '2020-01-02 10:02:25'),
(16, 'project', 3, 6, '1', 1, 1, 1, 1, '2020-01-02 13:45:24', '2020-01-02 13:45:24'),
(17, 'project', 4, 1, '1', 1, 1, 1, 1, '2020-01-26 07:45:22', '2020-01-26 07:45:22'),
(18, 'project', 5, 3, '1', 1, 1, 1, 1, '2020-01-26 08:42:36', '2020-01-26 08:42:36'),
(19, 'project', 6, 4, '1', 1, 1, 1, 1, '2020-02-01 17:42:41', '2020-02-01 17:42:41'),
(20, 'project', 7, 5, '1', 1, 1, 1, 1, '2020-02-01 17:44:52', '2020-02-01 17:44:52'),
(21, 'project', 8, 6, '1', 1, 1, 1, 1, '2020-02-01 17:45:41', '2020-02-01 17:45:41'),
(22, 'project', 9, 9, '1', 1, 1, 1, 1, '2020-02-01 17:48:33', '2020-02-01 17:48:33'),
(23, 'project', 10, 10, '1', 1, 1, 1, 1, '2020-02-01 18:17:35', '2020-02-01 18:17:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `department` int(11) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `year_and_term` varchar(191) DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `credit` varchar(191) DEFAULT NULL,
  `date_time` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `course_outcome`
--

CREATE TABLE `course_outcome` (
  `id` int(11) NOT NULL,
  `explanation` varchar(150) NOT NULL,
  `code` varchar(10) NOT NULL,
  `survey_average` double NOT NULL DEFAULT '0',
  `measured_average` double NOT NULL DEFAULT '0',
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `faculty` int(11) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `departments_has_instructors`
--

CREATE TABLE `departments_has_instructors` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `instructor_email` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `faculty`
--

CREATE TABLE `faculty` (
  `id` int(10) UNSIGNED NOT NULL,
  `university` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `grading_tool`
--

CREATE TABLE `grading_tool` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `question_number` int(11) NOT NULL,
  `percentage` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `grading_tool_covers_course_outcome`
--

CREATE TABLE `grading_tool_covers_course_outcome` (
  `id` int(11) NOT NULL,
  `grading_tool_id` int(11) NOT NULL,
  `course_outcome_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `instructors_gives_sections`
--

CREATE TABLE `instructors_gives_sections` (
  `id` int(11) NOT NULL,
  `instructor_email` varchar(200) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `area` varchar(191) NOT NULL,
  `areaid` int(11) DEFAULT NULL,
  `user` int(11) NOT NULL,
  `ip` varchar(22) NOT NULL,
  `type` int(11) NOT NULL,
  `info` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('hi@ahmetsc.com', '$2y$10$AdgiyzH3l.Q2FecoMdh5FeWV0KlgPduqkBi4ouF1ygkP5EVmgpMC6', '2019-12-23 09:40:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `program_outcome`
--

CREATE TABLE `program_outcome` (
  `id` int(11) NOT NULL,
  `explanation` varchar(1000) NOT NULL,
  `code` varchar(10) NOT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `program_outcomes_provides_course_outcomes`
--

CREATE TABLE `program_outcomes_provides_course_outcomes` (
  `id` int(11) NOT NULL,
  `course_outcome_id` int(11) NOT NULL,
  `program_outcome_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `section`
--

CREATE TABLE `section` (
  `id` int(10) UNSIGNED NOT NULL,
  `course` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `students_takes_sections`
--

CREATE TABLE `students_takes_sections` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `section_id` int(11) NOT NULL,
  `letter_grade` varchar(3) DEFAULT NULL,
  `average` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `student_answers_grading_tool`
--

CREATE TABLE `student_answers_grading_tool` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `grading_tool_id` int(11) NOT NULL,
  `grade` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `student_gets_measured_grade_course_outcome`
--

CREATE TABLE `student_gets_measured_grade_course_outcome` (
  `id` int(11) NOT NULL,
  `course_outcome_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `grade` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `student_gets_measured_grade_program_outcome`
--

CREATE TABLE `student_gets_measured_grade_program_outcome` (
  `id` int(11) NOT NULL,
  `program_outcome_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `grade` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `university`
--

CREATE TABLE `university` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `tel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `university`, `email`, `password`, `level`, `photo`, `phone`, `api_token`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ilker bekmezci', NULL, 'bekmezcii@mef.edu.tr', '$2y$10$E8hbTI6U8wS6viE/r/hdrefC3p2Nrr7rfptCft6vySaFu6M.JfZI6', '1', NULL, NULL, 'BUFA808sL80oYevbm5ZsKxACJYWUTXCIUyypRM1SZGSIiNxZjeLN2Fo6BrDKaf1x', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users_admin`
--

CREATE TABLE `users_admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users_instructor`
--

CREATE TABLE `users_instructor` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users_student`
--

CREATE TABLE `users_student` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `advisor` int(11) NOT NULL,
  `department` int(11) NOT NULL,
  `is_major` tinyint(1) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admin_log`
--
ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_log_website_foreign` (`website`),
  ADD KEY `admin_log_user_foreign` (`user`);

--
-- Tablo için indeksler `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_fk` (`course_id`);

--
-- Tablo için indeksler `authority`
--
ALTER TABLE `authority`
  ADD PRIMARY KEY (`id`),
  ADD KEY `authority_business` (`area`),
  ADD KEY `authority_user` (`user`);

--
-- Tablo için indeksler `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `course_outcome`
--
ALTER TABLE `course_outcome`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id_fk` (`course_id`);

--
-- Tablo için indeksler `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `departments_has_instructors`
--
ALTER TABLE `departments_has_instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_id_instructor_email` (`department_id`,`instructor_email`),
  ADD KEY `instructor_email` (`instructor_email`);

--
-- Tablo için indeksler `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `grading_tool`
--
ALTER TABLE `grading_tool`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assessment_id_question_number` (`assessment_id`,`question_number`);

--
-- Tablo için indeksler `grading_tool_covers_course_outcome`
--
ALTER TABLE `grading_tool_covers_course_outcome`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grading_tool_id_course_outcome_id` (`grading_tool_id`,`course_outcome_id`),
  ADD KEY `course_outcome_FK` (`course_outcome_id`);

--
-- Tablo için indeksler `instructors_gives_sections`
--
ALTER TABLE `instructors_gives_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `instructors_gives_sections_unique_key` (`section_id`,`instructor_email`),
  ADD KEY `instructors_gives_sections_instructor_email_fk` (`instructor_email`);

--
-- Tablo için indeksler `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `program_outcome`
--
ALTER TABLE `program_outcome`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id_fk` (`department_id`);

--
-- Tablo için indeksler `program_outcomes_provides_course_outcomes`
--
ALTER TABLE `program_outcomes_provides_course_outcomes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_outcomes_provides_course_outcomes_unique_key` (`course_outcome_id`,`program_outcome_id`),
  ADD KEY `program_outcomes_provides_course_outcomes_program_outcome_fk` (`program_outcome_id`);

--
-- Tablo için indeksler `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `students_takes_sections`
--
ALTER TABLE `students_takes_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_takes_sections_unique_key` (`section_id`,`student_id`),
  ADD KEY `students_takes_sections_student_id_fk` (`student_id`);

--
-- Tablo için indeksler `student_answers_grading_tool`
--
ALTER TABLE `student_answers_grading_tool`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id_FK` (`student_id`),
  ADD KEY `grading_tool_id_FK` (`grading_tool_id`);

--
-- Tablo için indeksler `student_gets_measured_grade_course_outcome`
--
ALTER TABLE `student_gets_measured_grade_course_outcome`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_gets_measured_grade_course_outcome_student_id_FK` (`student_id`),
  ADD KEY `student_gets_measured_grade_course_outcome_course_outcome_id_FK` (`course_outcome_id`);

--
-- Tablo için indeksler `student_gets_measured_grade_program_outcome`
--
ALTER TABLE `student_gets_measured_grade_program_outcome`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_gets_measured_grade_program_outcome_student_id_FK` (`student_id`),
  ADD KEY `student_gets_measured_grade_program_outcome_po_id_FK` (`program_outcome_id`);

--
-- Tablo için indeksler `university`
--
ALTER TABLE `university`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users_admin`
--
ALTER TABLE `users_admin`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users_instructor`
--
ALTER TABLE `users_instructor`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users_student`
--
ALTER TABLE `users_student`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `assessment`
--
ALTER TABLE `assessment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `authority`
--
ALTER TABLE `authority`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `course_outcome`
--
ALTER TABLE `course_outcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `departments_has_instructors`
--
ALTER TABLE `departments_has_instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `grading_tool`
--
ALTER TABLE `grading_tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `grading_tool_covers_course_outcome`
--
ALTER TABLE `grading_tool_covers_course_outcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `instructors_gives_sections`
--
ALTER TABLE `instructors_gives_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `program_outcome`
--
ALTER TABLE `program_outcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `program_outcomes_provides_course_outcomes`
--
ALTER TABLE `program_outcomes_provides_course_outcomes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `section`
--
ALTER TABLE `section`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `students_takes_sections`
--
ALTER TABLE `students_takes_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `student_answers_grading_tool`
--
ALTER TABLE `student_answers_grading_tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `student_gets_measured_grade_course_outcome`
--
ALTER TABLE `student_gets_measured_grade_course_outcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `student_gets_measured_grade_program_outcome`
--
ALTER TABLE `student_gets_measured_grade_program_outcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `university`
--
ALTER TABLE `university`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users_admin`
--
ALTER TABLE `users_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users_instructor`
--
ALTER TABLE `users_instructor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users_student`
--
ALTER TABLE `users_student`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `admin_log`
--
ALTER TABLE `admin_log`
  ADD CONSTRAINT `admin_log_user_foreign` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_log_website_foreign` FOREIGN KEY (`website`) REFERENCES `faculty` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `project_company` FOREIGN KEY (`university`) REFERENCES `university` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `user_company` FOREIGN KEY (`university`) REFERENCES `university` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
