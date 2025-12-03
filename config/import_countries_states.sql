-- ============================================
-- Import Countries and States Data
-- Run this AFTER importing database_fixed.sql
-- ============================================

-- Import countries data
-- Note: This file should be imported AFTER the countries table structure exists
-- If you get errors, make sure countries table is created first

-- The actual INSERT statements will be in countries.sql and states.sql files
-- This is just a placeholder to remind you to import those files

-- IMPORTANT: Import in this order:
-- 1. First: countries.sql (creates countries table with data)
-- 2. Second: states.sql (creates states table with data)
-- 3. Then: database_fixed.sql (creates other tables with foreign keys)

-- OR if tables already exist, just import the INSERT statements from:
-- - config/countries.sql (INSERT INTO countries...)
-- - config/states.sql (INSERT INTO states...)

