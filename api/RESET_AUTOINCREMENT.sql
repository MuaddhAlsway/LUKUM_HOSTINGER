-- ============================================================================
-- RESET AUTO_INCREMENT IN EXHIBITIONS TABLE
-- ============================================================================
-- This resets the auto-increment counter so new exhibitions start from ID 1
-- Use this ONLY if you've deleted all exhibitions and want to reset the ID counter

-- WARNING: Only use when the table is completely empty!

-- Reset auto-increment to 1
ALTER TABLE exhibitions AUTO_INCREMENT = 1;

-- Verify:
-- SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'exhibitions' AND TABLE_SCHEMA = 'u812122863_lakum_artspace';

-- ============================================================================
-- HOW TO USE
-- ============================================================================
-- 1. Delete all exhibitions via the admin panel OR run:
--    DELETE FROM exhibitions;
--
-- 2. Then run this query:
--    ALTER TABLE exhibitions AUTO_INCREMENT = 1;
--
-- 3. Add a new exhibition - it will have ID = 1

-- ============================================================================
-- IMPORTANT NOTE
-- ============================================================================
-- Why does MySQL keep incrementing IDs even after deletion?
-- 
-- This is NORMAL and BY DESIGN:
-- - Prevents ID reuse (data integrity)
-- - Maintains referential integrity
-- - It's a feature, not a bug!
--
-- If you MUST reset it, use the ALTER TABLE command above.
-- But only reset when the table is completely empty.
-- ============================================================================
