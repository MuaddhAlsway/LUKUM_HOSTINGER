-- ============================================================================
-- LAKUM Artspace - Exhibitions Table Creation Script
-- For MySQL (Compatible with Hostinger, XAMPP, and any MySQL server)
-- ============================================================================
-- 
-- INSTRUCTIONS:
-- 1. On Hostinger: Go to cPanel > MySQL Databases or phpMyAdmin
-- 2. Select your database and click "SQL" tab
-- 3. Copy and paste this entire script
-- 4. Click "Go" or "Execute"
-- 
-- ALTERNATIVELY (using phpMyAdmin):
-- 1. Open phpMyAdmin
-- 2. Select your database
-- 3. Click "Import" tab
-- 4. Choose this file or paste the SQL
-- 5. Click "Go"
--
-- ============================================================================

-- Create exhibitions table
CREATE TABLE IF NOT EXISTS `exhibitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title_en` VARCHAR(255) NOT NULL,
    `title_ar` VARCHAR(255),
    `description_en` LONGTEXT,
    `description_ar` LONGTEXT,
    `location_en` VARCHAR(255),
    `location_ar` VARCHAR(255),
    `exhibition_date` DATE NOT NULL,
    `exhibition_time` TIME,
    `exhibition_end_time` TIME,
    `end_date` DATE,
    `cover_image` VARCHAR(500),
    `category` VARCHAR(50) DEFAULT 'exhibition',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_date` (`exhibition_date`),
    INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- OPTIONAL: Insert sample data (uncomment to use)
-- ============================================================================

-- INSERT INTO `exhibitions` 
-- (`title_en`, `title_ar`, `description_en`, `description_ar`, 
--  `location_en`, `location_ar`, `exhibition_date`, `exhibition_time`, 
--  `exhibition_end_time`, `cover_image`, `category`) 
-- VALUES 
-- ('Contemporary Art Showcase 2026', 'معرض الفن المعاصر 2026',
--  'A stunning collection of contemporary artworks from emerging artists around the world.',
--  'مجموعة رائعة من الأعمال الفنية المعاصرة من الفنانين الناشئين حول العالم.',
--  'Hall 1', 'القاعة 1',
--  '2026-03-15', '10:00:00', '18:00:00',
--  'assest/img-4.png', 'exhibition');

-- ============================================================================
-- VERIFICATION QUERIES (run after creating table)
-- ============================================================================

-- Check if table was created successfully:
-- DESCRIBE exhibitions;

-- Check table structure:
-- SHOW CREATE TABLE exhibitions;

-- Count exhibitions:
-- SELECT COUNT(*) as total_exhibitions FROM exhibitions;

-- ============================================================================
