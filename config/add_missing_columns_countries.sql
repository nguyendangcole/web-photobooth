-- ============================================
-- Thêm các cột còn thiếu vào bảng countries
-- Chạy file này TRƯỚC KHI import countries.sql
-- ============================================

ALTER TABLE `countries` 
ADD COLUMN IF NOT EXISTS `population` bigint unsigned DEFAULT NULL AFTER `native`,
ADD COLUMN IF NOT EXISTS `gdp` bigint unsigned DEFAULT NULL AFTER `population`,
ADD COLUMN IF NOT EXISTS `region_id` mediumint unsigned DEFAULT NULL AFTER `region`,
ADD COLUMN IF NOT EXISTS `subregion_id` mediumint unsigned DEFAULT NULL AFTER `subregion`,
ADD COLUMN IF NOT EXISTS `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `subregion_id`,
ADD COLUMN IF NOT EXISTS `timezones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `nationality`,
ADD COLUMN IF NOT EXISTS `translations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `timezones`;

-- Note: MySQL không hỗ trợ IF NOT EXISTS cho ALTER TABLE
-- Nếu báo lỗi "Duplicate column", bỏ qua và tiếp tục import countries.sql

