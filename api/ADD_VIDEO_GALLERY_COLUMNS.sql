-- ============================================================================
-- MIGRATION: ADD VIDEO & GALLERY COLUMNS TO EXHIBITIONS TABLE
-- Date: 2026-06-21
-- Purpose: Add Event Video (Optional) and Gallery Images support
-- ============================================================================

-- ============================================================================
-- STEP 1: Add event_video column
-- ============================================================================
-- This column stores a single video URL (optional)
-- Supports: YouTube embed URLs, Vimeo URLs, direct video file URLs
-- Max length: 500 characters

ALTER TABLE exhibitions ADD COLUMN `event_video` VARCHAR(500) 
COMMENT 'Exhibition video URL (YouTube, Vimeo, or direct video URL - optional)' 
AFTER `cover_image`;

-- ============================================================================
-- STEP 2: Add gallery_images column
-- ============================================================================
-- This column stores multiple image paths as a JSON array
-- Format example: ["assest/gallery1.jpg", "assest/gallery2.jpg", "assest/gallery3.jpg"]
-- Stored as LONGTEXT to handle large JSON arrays (100+ images possible)

ALTER TABLE exhibitions ADD COLUMN `gallery_images` LONGTEXT 
COMMENT 'Gallery images as JSON array (e.g., ["path1.jpg", "path2.jpg"])' 
AFTER `event_video`;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- View the updated table structure:
-- DESCRIBE exhibitions;

-- View all columns for a specific exhibition:
-- SELECT id, title_en, cover_image, event_video, gallery_images FROM exhibitions WHERE id = 1;

-- Check if columns exist:
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'exhibitions' AND TABLE_SCHEMA = 'u812122863_lakum_artspace';

-- ============================================================================
-- COLUMN DETAILS
-- ============================================================================

-- event_video (VARCHAR 500)
--   • Stores: Single video URL (optional)
--   • Accepts:
--     - YouTube embed URL: https://www.youtube.com/embed/VIDEO_ID
--     - Vimeo URL: https://vimeo.com/VIDEO_ID
--     - Direct video file: assest/video.mp4
--   • Nullable: YES (can be NULL if not provided)
--   • Index: No (optional performance optimization if many video queries needed)

-- gallery_images (LONGTEXT)
--   • Stores: JSON array of image file paths
--   • Format: ["assest/gallery1.jpg", "assest/gallery2.jpg", ...]
--   • Example: ["assest/img-1.jpg", "assest/img-2.jpg", "assest/img-3.jpg"]
--   • Nullable: YES (can be NULL if no gallery images)
--   • Capacity: Can handle 100+ images per exhibition
--   • Index: No (not indexed as it's variable JSON data)

-- ============================================================================
-- EXECUTION STEPS (For PhpMyAdmin or MySQL CLI)
-- ============================================================================

-- Option 1: Execute this entire file at once
-- • Copy all SQL statements
-- • Paste into PhpMyAdmin Query Tab
-- • Click "Go" button

-- Option 2: Execute step by step
-- STEP 1: Paste and execute the ALTER TABLE for event_video
-- STEP 2: Paste and execute the ALTER TABLE for gallery_images
-- STEP 3: Run verification queries

-- Option 3: Via MySQL CLI
-- mysql -h localhost -u u812122863_neama -p u812122863_lakum_artspace < ADD_VIDEO_GALLERY_COLUMNS.sql

-- ============================================================================
-- ROLLBACK (If you need to undo these changes)
-- ============================================================================
-- DROP TABLE exhibitions; -- This would delete the entire table
-- 
-- To remove just the new columns:
-- ALTER TABLE exhibitions DROP COLUMN event_video;
-- ALTER TABLE exhibitions DROP COLUMN gallery_images;

-- ============================================================================
-- EXPECTED OUTPUT
-- ============================================================================
-- After running these commands, the exhibitions table will have:
-- 
-- Column List (in order):
-- 1. id (INT)
-- 2. title_en (VARCHAR)
-- 3. description_en (LONGTEXT)
-- 4. location_en (VARCHAR)
-- 5. title_ar (VARCHAR)
-- 6. description_ar (LONGTEXT)
-- 7. location_ar (VARCHAR)
-- 8. exhibition_date (DATE)
-- 9. exhibition_time (TIME)
-- 10. exhibition_end_time (TIME)
-- 11. end_date (DATE)
-- 12. cover_image (VARCHAR)
-- 13. event_video (VARCHAR) ← NEW
-- 14. gallery_images (LONGTEXT) ← NEW
-- 15. category (VARCHAR)
-- 16. is_featured (TINYINT)
-- 17. created_at (TIMESTAMP)
-- 18. updated_at (TIMESTAMP)

-- ============================================================================
-- NOTES
-- ============================================================================
-- • These columns are OPTIONAL (can be NULL)
-- • Existing exhibitions will have NULL values for these new columns
-- • The admin forms (add-exhibition.html, edit-exhibition.html) handle empty/null values
-- • No data migration needed - this is a pure schema addition
-- • No performance impact - these are optional columns
-- ============================================================================
