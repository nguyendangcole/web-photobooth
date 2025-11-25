-- ============================================
-- Script SQL hoàn chỉnh cho Web Photobooth
-- Import vào phpMyAdmin
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- ============================================
-- 1. BẢNG COUNTRIES (Quốc gia)
-- ============================================

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso3` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeric_code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso2` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tld` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `native` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subregion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `emoji` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emojiU` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `wikiDataId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. BẢNG STATES (Tỉnh/Thành phố)
-- ============================================

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` mediumint unsigned NOT NULL,
  `country_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fips_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `wikiDataId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country_region` (`country_id`),
  KEY `country_region_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. BẢNG USERS (Người dùng)
-- ============================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `provider` enum('local','google','facebook') NOT NULL DEFAULT 'local',
  `provider_id` varchar(190) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `premium_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `provider` (`provider`,`provider_id`),
  KEY `fk_country` (`country_id`),
  KEY `fk_state` (`state_id`),
  KEY `idx_is_premium` (`is_premium`),
  CONSTRAINT `fk_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  CONSTRAINT `fk_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================
-- 4. BẢNG FRAMES (Khung ảnh)
-- ============================================

DROP TABLE IF EXISTS `frames`;
CREATE TABLE IF NOT EXISTS `frames` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `src` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'square',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Premium frame flag',
  PRIMARY KEY (`id`),
  KEY `idx_is_premium` (`is_premium`),
  KEY `idx_layout_premium` (`layout`,`is_premium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. BẢNG PREMIUM_REQUESTS (Yêu cầu nâng cấp)
-- ============================================

DROP TABLE IF EXISTS `premium_requests`;
CREATE TABLE IF NOT EXISTS `premium_requests` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user id',
  `notes` text DEFAULT NULL COMMENT 'Admin notes',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_requested_at` (`requested_at`),
  CONSTRAINT `fk_premium_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. BẢNG PHOTOBOOK_ALBUMS (Album photobook)
-- ============================================

DROP TABLE IF EXISTS `photobook_albums`;
CREATE TABLE IF NOT EXISTS `photobook_albums` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'My Photobook',
  `slug` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` enum('private','unlisted','public') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. BẢNG PHOTOBOOK_PAGES (Trang photobook)
-- ============================================

DROP TABLE IF EXISTS `photobook_pages`;
CREATE TABLE IF NOT EXISTS `photobook_pages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` bigint UNSIGNED DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout` enum('square','vertical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'square',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pb_created_at` (`created_at`),
  KEY `album_id` (`album_id`),
  CONSTRAINT `fk_photobook_pages_album` FOREIGN KEY (`album_id`) REFERENCES `photobook_albums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CHÈN DỮ LIỆU MẪU
-- ============================================

-- Chèn dữ liệu frames
INSERT INTO `frames` (`id`, `name`, `src`, `layout`, `is_premium`) VALUES
(1, 'Normal', 'public/images/6.png', 'vertical', 0),
(2, 'Cat', 'public/images/20.png', 'vertical', 0),
(3, 'Star', 'public/images/21.png', 'vertical', 0),
(4, 'Crazy(1)', 'public/images/22.png', 'vertical', 0),
(5, 'Crazy(2)', 'public/images/23.png', 'vertical', 0),
(6, 'Friends', 'public/images/24.png', 'vertical', 0),
(7, 'Mybeloved', 'public/images/25.png', 'vertical', 0),
(8, 'QUANQUE', 'public/images/26.png', 'vertical', 0),
(9, 'Longtimenosee', 'public/images/27.png', 'vertical', 0),
(10, 'vintage (1)', 'public/images/7.png', 'vertical', 0),
(11, 'Y2K', 'public/images/8.png', 'vertical', 0),
(12, '#Vietnamese', 'public/images/11.png', 'vertical', 0),
(13, '#Vietnamese', 'public/images/12.png', 'square', 0),
(14, '#Crazy', 'public/images/13.png', 'square', 0),
(15, '#1989', 'public/images/14.png', 'square', 0),
(16, 'Papers', 'public/images/10.png', 'square', 0);

-- ============================================
-- LƯU Ý: 
-- Để import đầy đủ dữ liệu countries và states,
-- bạn cần chạy thêm 2 file sau:
-- 1. countries.sql - chứa dữ liệu 250 quốc gia
-- 2. states.sql - chứa dữ liệu các tỉnh/thành phố
-- 
-- Hoặc bạn có thể import từng file riêng biệt:
-- - Import countries.sql trước
-- - Import states.sql sau
-- - Sau đó import file này (database_complete.sql)
-- ============================================

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
COMMIT;

