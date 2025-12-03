-- ============================================
-- Thêm các cột còn thiếu vào bảng states
-- Chạy file này TRƯỚC KHI import states.sql
-- ============================================

ALTER TABLE `states` 
ADD COLUMN `iso3166_2` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `iso2`,
ADD COLUMN `level` int DEFAULT NULL AFTER `type`,
ADD COLUMN `parent_id` int unsigned DEFAULT NULL AFTER `level`,
ADD COLUMN `native` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `parent_id`,
ADD COLUMN `timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IANA timezone identifier (e.g., America/New_York)' AFTER `longitude`;

-- Note: MySQL không hỗ trợ IF NOT EXISTS cho ALTER TABLE
-- Nếu báo lỗi "Duplicate column", bỏ qua và tiếp tục import states.sql

