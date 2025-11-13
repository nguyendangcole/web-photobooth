-- ============================================
-- Admin System Setup (Simple Version)
-- Chạy file này trong phpMyAdmin
-- ============================================

-- Bước 1: Thêm cột is_admin (nếu chưa có)
ALTER TABLE users 
ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Admin role flag' 
AFTER is_premium;

-- Bước 2: Thêm index (nếu chưa có)
ALTER TABLE users 
ADD INDEX idx_is_admin (is_admin);

-- Bước 3: Set email của bạn thành admin (THAY ĐỔI EMAIL NÀY!)
UPDATE users SET is_admin = 1 WHERE email = 'nguyenduydang225@gmail.com';

-- Bước 4: Kiểm tra
SELECT id, name, email, is_admin, is_premium FROM users WHERE is_admin = 1;

