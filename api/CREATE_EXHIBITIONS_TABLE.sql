-- ============================================================================
-- LAKUM ARTSPACE - EXHIBITIONS TABLE (MySQL)
-- Complete schema with bilingual support, indexes, and constraints
-- ============================================================================
-- Database: MySQL 5.7+ or MySQL 8.0+
-- Compatible with: Hostinger, XAMPP, cPanel, phpMyAdmin
-- Character Set: utf8mb4 (supports Arabic and Emoji)
-- ============================================================================

USE `lakum_artspace`;

-- ============================================================================
-- 1. EXHIBITIONS TABLE (Main Table)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `exhibitions` (
    -- Primary Key
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique exhibition ID',
    
    -- Basic Information (Bilingual)
    `title_en` VARCHAR(255) NOT NULL COMMENT 'Exhibition title in English',
    `title_ar` VARCHAR(255) COMMENT 'Exhibition title in Arabic',
    
    -- Description (Bilingual, Full Text Search)
    `description_en` LONGTEXT COMMENT 'Detailed description in English',
    `description_ar` LONGTEXT COMMENT 'Detailed description in Arabic',
    
    -- Location (Bilingual)
    `location_en` VARCHAR(255) COMMENT 'Exhibition location in English (e.g., Hall 1)',
    `location_ar` VARCHAR(255) COMMENT 'Exhibition location in Arabic',
    
    -- Date & Time
    `exhibition_date` DATE NOT NULL COMMENT 'Exhibition start date',
    `exhibition_time` TIME COMMENT 'Exhibition start time (optional)',
    `exhibition_end_time` TIME COMMENT 'Exhibition end time (optional)',
    `end_date` DATE COMMENT 'Exhibition end date (for multi-day exhibitions)',
    
    -- Media
    `cover_image` VARCHAR(500) COMMENT 'Path to cover image (e.g., assest/img-1.png)',
    `gallery_folder` VARCHAR(255) COMMENT 'Folder containing exhibition images',
    
    -- Metadata
    `category` VARCHAR(50) DEFAULT 'exhibition' COMMENT 'Type: exhibition, workshop, event',
    `status` ENUM('upcoming', 'ongoing', 'past', 'archived') DEFAULT 'upcoming' COMMENT 'Exhibition status',
    `is_featured` TINYINT(1) DEFAULT 0 COMMENT 'Featured on homepage (0=no, 1=yes)',
    `views_count` INT DEFAULT 0 COMMENT 'Number of page views',
    `created_by` INT COMMENT 'User ID who created this exhibition',
    `updated_by` INT COMMENT 'User ID who last updated this exhibition',
    
    -- Timestamps
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/time created',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date/time last updated',
    `deleted_at` TIMESTAMP NULL COMMENT 'Soft delete timestamp (NULL = not deleted)',
    
    -- Indexes (Performance Optimization)
    INDEX `idx_exhibition_date` (`exhibition_date`) COMMENT 'Speed up date queries',
    INDEX `idx_category` (`category`) COMMENT 'Speed up category filters',
    INDEX `idx_status` (`status`) COMMENT 'Speed up status filters',
    INDEX `idx_is_featured` (`is_featured`) COMMENT 'Speed up featured query',
    INDEX `idx_created_at` (`created_at`) COMMENT 'Speed up sorting by creation date',
    FULLTEXT INDEX `ft_title_en` (`title_en`) COMMENT 'Full-text search on English title',
    FULLTEXT INDEX `ft_description_en` (`description_en`) COMMENT 'Full-text search on English description',
    FULLTEXT INDEX `ft_title_ar` (`title_ar`) COMMENT 'Full-text search on Arabic title',
    FULLTEXT INDEX `ft_description_ar` (`description_ar`) COMMENT 'Full-text search on Arabic description'
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
  COMMENT='LAKUM Artspace Exhibitions - Bilingual Support';

-- ============================================================================
-- 2. EXHIBITION_IMAGES TABLE (Gallery)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `exhibition_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique image ID',
    `exhibition_id` INT NOT NULL COMMENT 'Reference to exhibitions table',
    `image_path` VARCHAR(500) NOT NULL COMMENT 'Path to image file',
    `alt_text_en` VARCHAR(255) COMMENT 'Alt text in English',
    `alt_text_ar` VARCHAR(255) COMMENT 'Alt text in Arabic',
    `caption_en` TEXT COMMENT 'Caption in English',
    `caption_ar` TEXT COMMENT 'Caption in Arabic',
    `sort_order` INT DEFAULT 0 COMMENT 'Display order in gallery',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX `idx_exhibition_id` (`exhibition_id`),
    INDEX `idx_sort_order` (`sort_order`),
    
    -- Foreign Key
    CONSTRAINT `fk_exhibition_images_exhibition_id` 
        FOREIGN KEY (`exhibition_id`) 
        REFERENCES `exhibitions` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Gallery images for each exhibition';

-- ============================================================================
-- 3. EXHIBITION_CATEGORIES TABLE (Lookup)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `exhibition_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Category name (e.g., exhibition, workshop, event)',
    `label_en` VARCHAR(100) COMMENT 'Display label in English',
    `label_ar` VARCHAR(100) COMMENT 'Display label in Arabic',
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_name` (`name`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Exhibition categories/types';

-- ============================================================================
-- 4. INSERT DEFAULT CATEGORIES
-- ============================================================================

INSERT IGNORE INTO `exhibition_categories` (`name`, `label_en`, `label_ar`, `description`) VALUES
('exhibition', 'Exhibition', 'معرض', 'Art exhibitions and displays'),
('workshop', 'Workshop', 'ورشة عمل', 'Interactive workshops and training sessions'),
('event', 'Event', 'حدث', 'Special events and gatherings'),
('lecture', 'Lecture', 'محاضرة', 'Educational lectures and talks'),
('performance', 'Performance', 'عرض', 'Live performances and shows');

-- ============================================================================
-- 5. SAMPLE DATA (Optional - Uncomment to use)
-- ============================================================================

/*
INSERT INTO `exhibitions` 
(`title_en`, `title_ar`, `description_en`, `description_ar`, 
 `location_en`, `location_ar`, `exhibition_date`, `exhibition_time`, 
 `exhibition_end_time`, `end_date`, `cover_image`, `category`, `is_featured`, `status`) 
VALUES 
('Contemporary Art Showcase 2026', 'معرض الفن المعاصر 2026',
 'A stunning collection of contemporary artworks from emerging artists around the world. Experience innovation, creativity, and diverse perspectives in this immersive exhibition.',
 'مجموعة رائعة من الأعمال الفنية المعاصرة من الفنانين الناشئين حول العالم. تجربة الابتكار والإبداع وجهات نظر متنوعة في هذا المعرض الغامر.',
 'Hall 1', 'القاعة 1',
 '2026-03-15', '10:00:00', '18:00:00', '2026-03-31',
 'assest/img-4.png', 'exhibition', 1, 'upcoming'),

('Digital Art Workshop', 'ورشة الفن الرقمي',
 'Learn digital art techniques from industry professionals. Master Photoshop, digital painting, and 3D design in this hands-on workshop.',
 'تعلم تقنيات الفن الرقمي من المحترفين في الصناعة. احتراف Photoshop والرسم الرقمي والتصميم ثلاثي الأبعاد.',
 'Studio A', 'الاستوديو أ',
 '2026-04-05', '14:00:00', '17:00:00', '2026-04-05',
 'assest/img-5.png', 'workshop', 0, 'upcoming'),

('Artist Talk: The Future of Art', 'حديث الفنان: مستقبل الفن',
 'Join renowned artist Maria Santos as she discusses her creative process and vision for the future of contemporary art.',
 'انضم إلى الفنانة الشهيرة ماريا سانتوس وهي تناقش عمليتها الإبداعية ورؤيتها لمستقبل الفن المعاصر.',
 'Hall 2', 'القاعة 2',
 '2026-04-12', '15:00:00', '16:30:00', '2026-04-12',
 'assest/img-6.png', 'lecture', 0, 'upcoming');
*/

-- ============================================================================
-- 6. VERIFICATION QUERIES
-- ============================================================================

-- Check table structure:
-- DESCRIBE exhibitions;
-- SHOW CREATE TABLE exhibitions;

-- View all exhibitions:
-- SELECT * FROM exhibitions ORDER BY exhibition_date DESC;

-- View upcoming exhibitions:
-- SELECT * FROM exhibitions WHERE status = 'upcoming' ORDER BY exhibition_date ASC;

-- View featured exhibitions:
-- SELECT * FROM exhibitions WHERE is_featured = 1 AND status IN ('upcoming', 'ongoing') LIMIT 5;

-- Search exhibitions by title:
-- SELECT * FROM exhibitions WHERE MATCH(title_en, description_en) AGAINST('art' IN BOOLEAN MODE);

-- Get exhibition count by category:
-- SELECT category, COUNT(*) as total FROM exhibitions GROUP BY category;

-- ============================================================================
-- 7. QUERY EXAMPLES FOR YOUR PHP CODE
-- ============================================================================

/*
-- Get upcoming exhibitions (next 7 days)
SELECT * FROM exhibitions 
WHERE status = 'upcoming' 
  AND exhibition_date >= CURDATE() 
  AND exhibition_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY exhibition_date ASC;

-- Get past exhibitions (archive)
SELECT * FROM exhibitions 
WHERE status = 'past' 
  AND exhibition_date < CURDATE()
ORDER BY exhibition_date DESC;

-- Get by language (example for English page)
SELECT id, title_en as title, description_en as description, 
       location_en as location, exhibition_date, exhibition_time, 
       cover_image, category, is_featured
FROM exhibitions 
WHERE status IN ('upcoming', 'ongoing')
ORDER BY exhibition_date ASC;

-- Get by language (example for Arabic page)
SELECT id, title_ar as title, description_ar as description, 
       location_ar as location, exhibition_date, exhibition_time, 
       cover_image, category, is_featured
FROM exhibitions 
WHERE status IN ('upcoming', 'ongoing')
ORDER BY exhibition_date ASC;

-- Get featured exhibitions with pagination
SELECT * FROM exhibitions 
WHERE is_featured = 1 AND status IN ('upcoming', 'ongoing')
ORDER BY exhibition_date ASC
LIMIT 0, 6;  -- Page 1, 6 items per page

-- Get single exhibition with images
SELECT e.*, COUNT(i.id) as image_count
FROM exhibitions e
LEFT JOIN exhibition_images i ON e.id = i.exhibition_id
WHERE e.id = 1
GROUP BY e.id;

-- Get exhibition gallery (all images for one exhibition)
SELECT * FROM exhibition_images 
WHERE exhibition_id = 1 
ORDER BY sort_order ASC;
*/

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
