-- ============================================
-- FULL HYBRID TRANSLATION ARCHITECTURE
-- ============================================
-- Complete bilingual normalization for all dynamic content
-- Date: February 18, 2026
-- Status: Production-ready schema
-- 
-- This schema creates translation tables for:
-- 1. Events
-- 2. Blogs
-- 3. Press
-- 4. Pricing
-- 5. Legal Pages
--
-- All tables use:
-- - InnoDB engine
-- - UTF8MB4 charset
-- - ENUM('en','ar') for language
-- - UNIQUE constraints per language
-- - Foreign keys with CASCADE delete
-- - Proper indexing for performance

USE `lakum_artspace`;

-- ============================================
-- 1. EVENT_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS event_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    language ENUM('en','ar') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    location VARCHAR(255),
    slug VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_event_language (event_id, language),
    UNIQUE KEY unique_event_slug_language (slug, language),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    
    INDEX idx_event_id (event_id),
    INDEX idx_language (language),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. BLOG_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS blog_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    language ENUM('en','ar') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    excerpt VARCHAR(500),
    slug VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_blog_language (blog_id, language),
    UNIQUE KEY unique_blog_slug_language (slug, language),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    
    INDEX idx_blog_id (blog_id),
    INDEX idx_language (language),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. PRESS_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS press_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    press_id INT NOT NULL,
    language ENUM('en','ar') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    excerpt VARCHAR(500),
    slug VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_press_language (press_id, language),
    UNIQUE KEY unique_press_slug_language (slug, language),
    FOREIGN KEY (press_id) REFERENCES press(id) ON DELETE CASCADE,
    
    INDEX idx_press_id (press_id),
    INDEX idx_language (language),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. PRICING_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS pricing_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pricing_id INT NOT NULL,
    language ENUM('en','ar') NOT NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    duration VARCHAR(255),
    features LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_pricing_language (pricing_id, language),
    FOREIGN KEY (pricing_id) REFERENCES pricing(id) ON DELETE CASCADE,
    
    INDEX idx_pricing_id (pricing_id),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. LEGAL_PAGE_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS legal_page_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(100) NOT NULL,
    language ENUM('en','ar') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    last_updated DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_legal_page_language (page_key, language),
    
    INDEX idx_page_key (page_key),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MIGRATION PHASE 2: ENGLISH DATA MIGRATION
-- ============================================
-- These queries migrate existing English content to translation tables
-- Execute after table creation

-- Migrate Events
-- INSERT INTO event_translations (event_id, language, title, description, location, slug, meta_title, meta_description)
-- SELECT id, 'en', title, description, location, slug, title, description FROM events WHERE title IS NOT NULL;

-- Migrate Blogs
-- INSERT INTO blog_translations (blog_id, language, title, content, excerpt, slug, meta_title, meta_description)
-- SELECT id, 'en', title, content, excerpt, slug, title, excerpt FROM blogs WHERE title IS NOT NULL;

-- Migrate Press
-- INSERT INTO press_translations (press_id, language, title, content, excerpt, slug, meta_title, meta_description)
-- SELECT id, 'en', title, content, excerpt, slug, title, excerpt FROM press WHERE title IS NOT NULL;

-- Migrate Pricing
-- INSERT INTO pricing_translations (pricing_id, language, name, description, duration, features)
-- SELECT id, 'en', name, description, duration, features FROM pricing WHERE name IS NOT NULL;

-- Migrate Legal Pages (from HTML files - manual insertion)
-- INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
-- VALUES 
--   ('terms', 'en', 'Terms & Conditions', '<html content here>', CURDATE()),
--   ('privacy', 'en', 'Privacy Policy', '<html content here>', CURDATE());

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these after migration to verify data integrity

-- Check migration success
-- SELECT 'events' as entity, COUNT(*) as total_migrated FROM event_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'blogs', COUNT(*) FROM blog_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'press', COUNT(*) FROM press_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'pricing', COUNT(*) FROM pricing_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'legal_pages', COUNT(*) FROM legal_page_translations WHERE language = 'en';

-- Check for duplicates (should be 0)
-- SELECT 'events' as entity, event_id, COUNT(*) as count FROM event_translations GROUP BY event_id HAVING count > 1
-- UNION ALL
-- SELECT 'blogs', blog_id, COUNT(*) FROM blog_translations GROUP BY blog_id HAVING count > 1
-- UNION ALL
-- SELECT 'press', press_id, COUNT(*) FROM press_translations GROUP BY press_id HAVING count > 1
-- UNION ALL
-- SELECT 'pricing', pricing_id, COUNT(*) FROM pricing_translations GROUP BY pricing_id HAVING count > 1;

-- Check foreign key integrity
-- SELECT 'events' as entity, COUNT(*) as orphaned FROM event_translations et LEFT JOIN events e ON et.event_id = e.id WHERE e.id IS NULL
-- UNION ALL
-- SELECT 'blogs', COUNT(*) FROM blog_translations bt LEFT JOIN blogs b ON bt.blog_id = b.id WHERE b.id IS NULL
-- UNION ALL
-- SELECT 'press', COUNT(*) FROM press_translations pt LEFT JOIN press p ON pt.press_id = p.id WHERE p.id IS NULL
-- UNION ALL
-- SELECT 'pricing', COUNT(*) FROM pricing_translations prt LEFT JOIN pricing pr ON prt.pricing_id = pr.id WHERE pr.id IS NULL;

-- Check slug uniqueness per language
-- SELECT 'events' as entity, slug, language, COUNT(*) as count FROM event_translations GROUP BY slug, language HAVING count > 1
-- UNION ALL
-- SELECT 'blogs', slug, language, COUNT(*) FROM blog_translations GROUP BY slug, language HAVING count > 1
-- UNION ALL
-- SELECT 'press', slug, language, COUNT(*) FROM press_translations GROUP BY slug, language HAVING count > 1;

-- ============================================
-- ROLLBACK PROCEDURE
-- ============================================
-- If needed, drop all translation tables (events table unaffected)
-- DROP TABLE IF EXISTS legal_page_translations;
-- DROP TABLE IF EXISTS pricing_translations;
-- DROP TABLE IF EXISTS press_translations;
-- DROP TABLE IF EXISTS blog_translations;
-- DROP TABLE IF EXISTS event_translations;
-- All data preserved, zero loss


