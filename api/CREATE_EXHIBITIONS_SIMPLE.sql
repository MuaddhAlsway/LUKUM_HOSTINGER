-- ============================================================================
-- LAKUM ARTSPACE - EXHIBITIONS TABLE (SIMPLE)
-- Exactly what you need - no extra columns
-- ============================================================================

CREATE TABLE IF NOT EXISTS `exhibitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE,
    `description` LONGTEXT,
    `location` VARCHAR(255),
    `exhibition_date` DATE NOT NULL,
    `exhibition_time` TIME,
    `exhibition_end_time` TIME,
    `end_date` DATE,
    `cover_image` VARCHAR(500),
    `category` VARCHAR(50) DEFAULT 'exhibition',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_exhibition_date` (`exhibition_date`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA
-- ============================================================================

INSERT INTO `exhibitions` 
(`title`, `slug`, `description`, `location`, `exhibition_date`, `exhibition_time`, `exhibition_end_time`, `end_date`, `cover_image`, `category`, `is_featured`) 
VALUES 
('Contemporary Art Showcase 2026', 'contemporary-art-showcase-2026',
 'A stunning collection of contemporary artworks from emerging artists around the world. Experience innovation, creativity, and diverse perspectives in this immersive exhibition.',
 'Hall 1',
 '2026-03-15', '10:00:00', '18:00:00', '2026-03-31',
 'assest/img-4.png', 'exhibition', 1),

('Digital Art Workshop', 'digital-art-workshop',
 'Learn digital art techniques from industry professionals. Master Photoshop, digital painting, and 3D design in this hands-on workshop.',
 'Studio A',
 '2026-04-05', '14:00:00', '17:00:00', '2026-04-05',
 'assest/img-5.png', 'workshop', 0),

('Artist Talk: The Future of Art', 'artist-talk-future-of-art',
 'Join renowned artist Maria Santos as she discusses her creative process and vision for the future of contemporary art.',
 'Hall 2',
 '2026-04-12', '15:00:00', '16:30:00', '2026-04-12',
 'assest/img-6.png', 'lecture', 0);

-- ============================================================================
-- RUN THIS TO CREATE THE TABLE:
-- 1. phpMyAdmin: Copy and paste into SQL tab, click Go
-- 2. Command line: mysql -u user -p database < CREATE_EXHIBITIONS_SIMPLE.sql
-- ============================================================================
