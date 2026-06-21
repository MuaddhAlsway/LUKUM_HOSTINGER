-- ============================================================================
-- LAKUM ARTSPACE - EXHIBITIONS TABLE (FINAL)
-- Based on Admin Panel Form Fields
-- ============================================================================

DROP TABLE IF EXISTS `exhibitions`;

CREATE TABLE IF NOT EXISTS `exhibitions` (
    -- Primary Key
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique exhibition ID',
    
    -- English Content (Required)
    `title_en` VARCHAR(255) NOT NULL COMMENT 'Exhibition title in English (Required)',
    `description_en` LONGTEXT COMMENT 'Exhibition description in English',
    `location_en` VARCHAR(255) NOT NULL COMMENT 'Location in English (e.g., Hall 1, Hall 2, Café, Meeting Room)',
    
    -- Arabic Content (Optional)
    `title_ar` VARCHAR(255) COMMENT 'Exhibition title in Arabic',
    `description_ar` LONGTEXT COMMENT 'Exhibition description in Arabic',
    `location_ar` VARCHAR(255) COMMENT 'Location in Arabic (e.g., القاعة 1, الكافيه)',
    
    -- Date & Time Fields
    `exhibition_date` DATE NOT NULL COMMENT 'Exhibition start date (Past dates only)',
    `exhibition_time` TIME COMMENT 'Exhibition start time (default: 10:00)',
    `exhibition_end_time` TIME COMMENT 'Exhibition end time (default: 18:00)',
    `end_date` DATE COMMENT 'Exhibition end date (for multi-day exhibitions)',
    
    -- Media
    `cover_image` VARCHAR(500) COMMENT 'Cover image path (e.g., assest/img-4.png)',
    
    -- Metadata
    `category` VARCHAR(50) DEFAULT 'exhibition' COMMENT 'Type: exhibition (fixed)',
    `is_featured` TINYINT(1) DEFAULT 0 COMMENT 'Featured on homepage (0=no, 1=yes)',
    
    -- Timestamps
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/time created',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date/time last updated',
    
    -- Indexes for Performance
    INDEX `idx_exhibition_date` (`exhibition_date`) COMMENT 'Speed up date queries',
    INDEX `idx_category` (`category`) COMMENT 'Speed up category filter',
    INDEX `idx_is_featured` (`is_featured`) COMMENT 'Speed up featured query'
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT='LAKUM Exhibitions - Bilingual Support (English & Arabic)';



-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check table structure:
-- DESCRIBE exhibitions;

-- View all exhibitions:
-- SELECT * FROM exhibitions ORDER BY exhibition_date DESC;

-- View exhibitions with details:
-- SELECT id, title_en, location_en, exhibition_date, exhibition_time, exhibition_end_time, cover_image 
-- FROM exhibitions 
-- WHERE exhibition_date >= CURDATE() 
-- ORDER BY exhibition_date ASC;

-- Count total exhibitions:
-- SELECT COUNT(*) as total_exhibitions FROM exhibitions;

-- ============================================================================
-- TABLE STRUCTURE SUMMARY
-- ============================================================================
-- REQUIRED FIELDS (Must have data):
--   - title_en: Exhibition title in English
--   - location_en: Location (Hall 1, Hall 2, Café, Meeting Room, or custom)
--   - exhibition_date: Start date (past dates only)
--
-- OPTIONAL FIELDS (Can be empty):
--   - description_en, title_ar, description_ar, location_ar
--   - exhibition_time, exhibition_end_time (defaults to 10:00-18:00)
--   - end_date (for multi-day exhibitions)
--   - cover_image (defaults to assest/img-4.png)
--   - is_featured (0 or 1, defaults to 0)
--
-- AUTO-GENERATED FIELDS:
--   - id (auto-increment)
--   - created_at (current timestamp)
--   - updated_at (auto-update on changes)
--   - category (always 'exhibition')
-- ============================================================================

