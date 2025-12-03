-- Migration: Add storage_used column to users table
-- This column tracks total storage used by user in bytes (for photobook gallery)

-- Add storage_used column to users table
ALTER TABLE `users` 
ADD COLUMN `storage_used` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total storage used in bytes (photobook gallery)' 
AFTER `premium_until`;

-- Create index for faster queries
ALTER TABLE `users` 
ADD INDEX `idx_storage_used` (`storage_used`);

-- Note: Existing users will start with storage_used = 0
-- Storage will be calculated automatically when they upload/delete photos going forward

