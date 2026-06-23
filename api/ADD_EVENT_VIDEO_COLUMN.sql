-- ============================================================================
-- Add event_video column to exhibitions table if it doesn't exist
-- ============================================================================

-- Check if column exists and add if missing
ALTER TABLE exhibitions 
ADD COLUMN IF NOT EXISTS event_video VARCHAR(500) COMMENT 'Event video URL (YouTube or Vimeo)' 
AFTER cover_image;

-- Verify the column was added
-- DESCRIBE exhibitions;

-- Show all columns with event_video
-- SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'exhibitions' AND TABLE_SCHEMA = 'u812122863_lakum_artspace'
-- ORDER BY ORDINAL_POSITION;
