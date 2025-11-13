-- ============================================
-- Admin System Setup
-- ============================================

-- Thêm cột is_admin vào bảng users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Admin role flag' 
AFTER is_premium;

-- Thêm index (kiểm tra xem index đã tồn tại chưa)
SET @exist := (SELECT COUNT(*) 
               FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND index_name = 'idx_is_admin');

SET @sqlstmt := IF(@exist > 0, 
                   'SELECT ''Index idx_is_admin already exists.''', 
                   'ALTER TABLE users ADD INDEX idx_is_admin (is_admin)');

PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- Set email của bạn thành admin (THAY ĐỔI EMAIL)
-- ============================================

-- Cách 1: Set admin theo email
UPDATE users SET is_admin = 1 WHERE email = 'nguyenduydang225@gmail.com';

-- Cách 2: Set admin theo user ID
-- UPDATE users SET is_admin = 1 WHERE id = 4;

-- Cách 3: Set nhiều admin cùng lúc
-- UPDATE users SET is_admin = 1 WHERE email IN ('admin1@gmail.com', 'admin2@gmail.com');

-- ============================================
-- Kiểm tra
-- ============================================
SELECT id, name, email, is_admin, is_premium FROM users WHERE is_admin = 1;

