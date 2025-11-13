-- ============================================
-- SQL SETUP CHO HỆ THỐNG PREMIUM FRAMES
-- ============================================
-- File này được tạo để UPDATE database hiện tại
-- Chạy file này trong phpMyAdmin (database: myapp)
-- hoặc via terminal: mysql -u root -p myapp < config/premium_setup.sql

-- ============================================
-- KIỂM TRA: Nếu bạn đã import users.sql và frames.sql
-- thì CÁC CỘT ĐÃ CÓ SẴN, không cần chạy file này!
-- ============================================

-- Chỉ chạy các câu lệnh dưới đây NẾU bạn chưa import lại users.sql và frames.sql

-- 1) Thêm cột is_premium vào bảng users (nếu chưa có)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_premium TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Premium user status' AFTER email_verified;

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS premium_until DATETIME NULL DEFAULT NULL COMMENT 'Premium expiry date' AFTER is_premium;

-- 2) Thêm index để query nhanh hơn cho users
-- Kiểm tra trước khi thêm
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() AND table_name = 'users' 
               AND index_name = 'idx_is_premium');
SET @sqlstmt := IF(@exist > 0, 'SELECT ''Index already exists.''', 
                   'ALTER TABLE users ADD INDEX idx_is_premium (is_premium)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- 3) Thêm cột is_premium vào bảng frames (nếu chưa có)
ALTER TABLE frames 
ADD COLUMN IF NOT EXISTS is_premium TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Premium frame flag' AFTER layout;

-- 4) Thêm index cho frames
-- Kiểm tra idx_is_premium
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() AND table_name = 'frames' 
               AND index_name = 'idx_is_premium');
SET @sqlstmt := IF(@exist > 0, 'SELECT ''Index idx_is_premium already exists.''', 
                   'ALTER TABLE frames ADD INDEX idx_is_premium (is_premium)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- Kiểm tra idx_layout_premium
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() AND table_name = 'frames' 
               AND index_name = 'idx_layout_premium');
SET @sqlstmt := IF(@exist > 0, 'SELECT ''Index idx_layout_premium already exists.''', 
                   'ALTER TABLE frames ADD INDEX idx_layout_premium (layout, is_premium)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- ============================================
-- OPTIONAL: Tạo admin user (nếu chưa có)
-- ============================================
-- Uncomment dòng dưới nếu muốn tạo admin user
-- INSERT INTO users (name, email, password_hash, provider, email_verified, is_premium, premium_until) 
-- VALUES ('Admin', 'admin@photobooth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'local', 1, 1, '2099-12-31 23:59:59')
-- ON DUPLICATE KEY UPDATE is_premium=1;
-- Password mặc định: password

-- ============================================
-- TEST DATA: Thêm vài frame premium demo
-- ============================================
-- Uncomment nếu muốn thêm frame premium demo
-- UPDATE frames SET is_premium = 1 WHERE id IN (1, 2, 3);

-- ============================================
-- DONE! Kiểm tra kết quả:
-- ============================================
-- SELECT * FROM users LIMIT 5;
-- SHOW COLUMNS FROM users;
-- SHOW COLUMNS FROM frames;

