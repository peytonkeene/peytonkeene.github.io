-- =========================================================
-- MedNarrate Starter Database Schema
-- File: database/mednarrate.sql
-- Compatible with MySQL + phpMyAdmin import (GoDaddy cPanel)
-- =========================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------------------------------------------------------
-- Table: agencies
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `agencies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `contact_email` VARCHAR(255) DEFAULT NULL,
  `contact_phone` VARCHAR(50) DEFAULT NULL,
  `address_line_1` VARCHAR(255) DEFAULT NULL,
  `address_line_2` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(120) DEFAULT NULL,
  `state` VARCHAR(120) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_agencies_name` (`name`),
  KEY `idx_agencies_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user',
  `agency_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_agency_id` (`agency_id`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active` (`is_active`),
  CONSTRAINT `fk_users_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: narrative_generators
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `narrative_generators` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `agency_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by_user_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_narrative_generators_slug` (`slug`),
  KEY `idx_narrative_generators_agency_id` (`agency_id`),
  KEY `idx_narrative_generators_created_by` (`created_by_user_id`),
  KEY `idx_narrative_generators_active` (`is_active`),
  KEY `idx_narrative_generators_name` (`name`),
  CONSTRAINT `fk_narrative_generators_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_narrative_generators_created_by` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: generated_reports
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `generated_reports` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `generator_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `report_type` VARCHAR(100) NOT NULL,
  `report_content` LONGTEXT NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_generated_reports_user_id` (`user_id`),
  KEY `idx_generated_reports_generator_id` (`generator_id`),
  KEY `idx_generated_reports_status` (`status`),
  KEY `idx_generated_reports_created_at` (`created_at`),
  CONSTRAINT `fk_generated_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_generated_reports_generator` FOREIGN KEY (`generator_id`) REFERENCES `narrative_generators` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: contact_messages
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `agency` VARCHAR(150) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_messages_email` (`email`),
  KEY `idx_contact_messages_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Optional seed data (edit before production use)
-- =========================================================

-- Sample agency
INSERT INTO `agencies` (
  `id`, `name`, `contact_email`, `contact_phone`, `address_line_1`, `city`, `state`, `postal_code`, `is_active`
) VALUES (
  1, 'Sample EMS Agency', 'admin@sampleems.org', '555-0100', '100 Main Street', 'Dallas', 'TX', '75001', 1
) ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `contact_email` = VALUES(`contact_email`),
  `contact_phone` = VALUES(`contact_phone`),
  `address_line_1` = VALUES(`address_line_1`),
  `city` = VALUES(`city`),
  `state` = VALUES(`state`),
  `postal_code` = VALUES(`postal_code`),
  `is_active` = VALUES(`is_active`);

-- Sample admin user
-- IMPORTANT: Replace the password hash below with a real value generated by PHP password_hash().
-- Example PHP snippet: password_hash('ChangeThisPasswordNow!', PASSWORD_DEFAULT)
INSERT INTO `users` (
  `id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `agency_id`, `is_active`
) VALUES (
  1,
  'Admin',
  'User',
  'admin@mednarrate.com',
  '$2y$10$REPLACE_WITH_REAL_PASSWORD_HASH_FROM_PHP_PASSWORD_HASH',
  'admin',
  1,
  1
) ON DUPLICATE KEY UPDATE
  `first_name` = VALUES(`first_name`),
  `last_name` = VALUES(`last_name`),
  `password_hash` = VALUES(`password_hash`),
  `role` = VALUES(`role`),
  `agency_id` = VALUES(`agency_id`),
  `is_active` = VALUES(`is_active`);

-- Sample narrative generators
INSERT INTO `narrative_generators` (`id`, `agency_id`, `name`, `slug`, `description`, `is_active`, `created_by_user_id`) VALUES
  (1, 1, 'EMS Narrative Generator', 'ems-narrative-generator', 'Generates a structured EMS patient care narrative.', 1, 1),
  (2, 1, 'Trauma Report Generator', 'trauma-report-generator', 'Builds trauma-focused narrative reports.', 1, 1),
  (3, 1, 'Incident Summary Generator', 'incident-summary-generator', 'Creates concise incident summary narratives for review.', 1, 1)
ON DUPLICATE KEY UPDATE
  `agency_id` = VALUES(`agency_id`),
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `is_active` = VALUES(`is_active`),
  `created_by_user_id` = VALUES(`created_by_user_id`);

-- ---------------------------------------------------------
-- Admin Generator Builder tables
-- ---------------------------------------------------------

CREATE TABLE IF NOT EXISTS `generator_sections` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `generator_id` BIGINT UNSIGNED NOT NULL,
  `section_name` VARCHAR(180) NOT NULL,
  `section_slug` VARCHAR(190) NOT NULL,
  `section_order` INT NOT NULL DEFAULT 1,
  `section_description` TEXT DEFAULT NULL,
  `is_toggleable` TINYINT(1) NOT NULL DEFAULT 0,
  `default_open` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_generator_sections_generator_id` (`generator_id`),
  KEY `idx_generator_sections_order` (`section_order`),
  CONSTRAINT `fk_generator_sections_generator` FOREIGN KEY (`generator_id`) REFERENCES `narrative_generators` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `generator_fields` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `generator_id` BIGINT UNSIGNED NOT NULL,
  `section_id` BIGINT UNSIGNED NOT NULL,
  `field_name` VARCHAR(180) NOT NULL,
  `field_label` VARCHAR(180) NOT NULL,
  `field_slug` VARCHAR(190) NOT NULL,
  `field_type` VARCHAR(80) NOT NULL,
  `field_order` INT NOT NULL DEFAULT 1,
  `placeholder_text` VARCHAR(255) DEFAULT NULL,
  `help_text` VARCHAR(255) DEFAULT NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `options_json` LONGTEXT DEFAULT NULL,
  `config_json` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_generator_fields_generator_id` (`generator_id`),
  KEY `idx_generator_fields_section_id` (`section_id`),
  KEY `idx_generator_fields_order` (`field_order`),
  CONSTRAINT `fk_generator_fields_generator` FOREIGN KEY (`generator_id`) REFERENCES `narrative_generators` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_generator_fields_section` FOREIGN KEY (`section_id`) REFERENCES `generator_sections` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `generator_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `generator_id` BIGINT UNSIGNED NOT NULL,
  `template_name` VARCHAR(180) NOT NULL,
  `template_content` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_generator_templates_generator_id` (`generator_id`),
  CONSTRAINT `fk_generator_templates_generator` FOREIGN KEY (`generator_id`) REFERENCES `narrative_generators` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
