-- ============================================
-- Script thêm cột reset_token và reset_expires_at vào bảng users
-- Chạy script này nếu database của bạn chưa có 2 cột này
-- ============================================

-- Kiểm tra và thêm cột reset_token nếu chưa có
SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "reset_token";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 'Column reset_token already exists.' AS message;",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(64) DEFAULT NULL AFTER verification_token;")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Kiểm tra và thêm cột reset_expires_at nếu chưa có
SET @columnname = "reset_expires_at";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 'Column reset_expires_at already exists.' AS message;",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " datetime DEFAULT NULL AFTER reset_token;")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Hiển thị kết quả
SELECT 'Migration completed! Check if columns were added.' AS message;

