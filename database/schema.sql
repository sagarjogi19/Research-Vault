-- ResearchVault Digital Library Portal
-- MySQL 8.0+ / MariaDB 10.4+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- CREATE DATABASE IF NOT EXISTS `research_vault`
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;

USE `if0_42421838_research`;

DROP TABLE IF EXISTS `downloads`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `purchases`;
DROP TABLE IF EXISTS `papers`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `papers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(280) NOT NULL,
  `abstract` TEXT NOT NULL,
  `authors` VARCHAR(255) NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `uploaded_by` INT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL DEFAULT 0,
  `mime_type` VARCHAR(100) NOT NULL DEFAULT 'application/pdf',
  `preview_path` VARCHAR(255) NULL,
  `is_free` TINYINT(1) NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `publication_year` YEAR NULL,
  `keywords` VARCHAR(255) NULL,
  `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `papers_slug_unique` (`slug`),
  KEY `papers_category_id_index` (`category_id`),
  KEY `papers_uploaded_by_index` (`uploaded_by`),
  KEY `papers_status_index` (`status`),
  KEY `papers_is_free_index` (`is_free`),
  KEY `papers_title_index` (`title`),
  CONSTRAINT `papers_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `papers_uploader_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchases` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `paper_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'NGN',
  `status` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `purchased_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_user_paper_unique` (`user_id`, `paper_id`),
  KEY `purchases_paper_id_index` (`paper_id`),
  KEY `purchases_status_index` (`status`),
  CONSTRAINT `purchases_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_paper_fk` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `reference` VARCHAR(100) NOT NULL,
  `access_code` VARCHAR(100) NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'NGN',
  `gateway` VARCHAR(50) NOT NULL DEFAULT 'paystack',
  `gateway_response` JSON NULL,
  `status` ENUM('initialized', 'success', 'failed', 'abandoned') NOT NULL DEFAULT 'initialized',
  `paid_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_reference_unique` (`reference`),
  KEY `payments_purchase_id_index` (`purchase_id`),
  KEY `payments_user_id_index` (`user_id`),
  KEY `payments_status_index` (`status`),
  CONSTRAINT `payments_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `downloads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `paper_id` INT UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `downloaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `downloads_user_id_index` (`user_id`),
  KEY `downloads_paper_id_index` (`paper_id`),
  CONSTRAINT `downloads_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `downloads_paper_fk` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin@researchvault.test / Admin@12345
-- Default student: student@researchvault.test / Student@12345
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('System Admin', 'admin@researchvault.test', '$2y$12$YkVfFNLf1UgroJRChcwdPerSiRNgjBoZFPRNlJwDWutXty70g2HvG', 'admin', 'active'),
('Demo Student', 'student@researchvault.test', '$2y$12$UucK4c90Ftd1GAxXFq83vu4WsP/SiFOZWsqcIVSltVVEA3R59URG.', 'student', 'active');

-- Optional: run `php database/seed.php` to refresh passwords and add sample papers.

INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Computer Science', 'computer-science', 'Algorithms, AI, software engineering, and computing research'),
('Engineering', 'engineering', 'Civil, mechanical, electrical, and related engineering studies'),
('Medicine & Health', 'medicine-health', 'Clinical research, public health, and biomedical sciences'),
('Business & Economics', 'business-economics', 'Management, finance, economics, and entrepreneurship'),
('Education', 'education', 'Pedagogy, curriculum studies, and educational technology'),
('Social Sciences', 'social-sciences', 'Sociology, psychology, political science, and related fields'),
('Environmental Science', 'environmental-science', 'Climate, sustainability, and environmental management'),
('Law & Policy', 'law-policy', 'Legal studies, governance, and public policy research');

SET FOREIGN_KEY_CHECKS = 1;
