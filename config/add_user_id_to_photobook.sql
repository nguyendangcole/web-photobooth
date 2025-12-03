-- ============================================
-- Migration: Add user_id to photobook_pages
-- This ensures each user has their own private gallery
-- ============================================

-- Add user_id column to photobook_pages table
ALTER TABLE `photobook_pages` 
ADD COLUMN `user_id` int UNSIGNED DEFAULT NULL AFTER `album_id`,
ADD INDEX `idx_user_id` (`user_id`),
ADD CONSTRAINT `fk_photobook_pages_user` 
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
  ON DELETE CASCADE;

-- Optional: Set existing photos to NULL (or delete them if you want)
-- UPDATE photobook_pages SET user_id = NULL WHERE user_id IS NULL;

-- Make user_id required for new entries (after migration)
-- ALTER TABLE `photobook_pages` MODIFY COLUMN `user_id` int UNSIGNED NOT NULL;

