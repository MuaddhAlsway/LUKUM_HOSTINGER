-- ============================================
-- FULL HYBRID TRANSLATION ARCHITECTURE
-- PHASE 2: DATA MIGRATION QUERIES (FIXED)
-- ============================================
-- Execute these queries after table creation
-- These migrate all English content to translation tables
-- Original columns remain untouched for rollback capability

USE `lakum_artspace`;

-- ============================================
-- MIGRATION: EVENTS
-- ============================================
INSERT INTO event_translations (event_id, language, title, description, location, slug, meta_title, meta_description)
SELECT 
    id,
    'en' as language,
    COALESCE(title_en, title) as title,
    COALESCE(description_en, description) as description,
    location,
    LOWER(REPLACE(REPLACE(COALESCE(title_en, title), ' ', '-'), '--', '-')) as slug,
    COALESCE(title_en, title) as meta_title,
    COALESCE(description_en, description) as meta_description
FROM events 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- Verification: Events
-- SELECT COUNT(*) as events_migrated FROM event_translations WHERE language = 'en';
-- Should equal: SELECT COUNT(*) FROM events;

-- ============================================
-- MIGRATION: BLOGS
-- ============================================
INSERT INTO blog_translations (blog_id, language, title, content, excerpt, slug, meta_title, meta_description)
SELECT 
    id,
    'en' as language,
    COALESCE(title_en, title) as title,
    COALESCE(content_en, content) as content,
    excerpt,
    LOWER(REPLACE(REPLACE(COALESCE(title_en, title), ' ', '-'), '--', '-')) as slug,
    COALESCE(title_en, title) as meta_title,
    excerpt as meta_description
FROM blogs 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- Verification: Blogs
-- SELECT COUNT(*) as blogs_migrated FROM blog_translations WHERE language = 'en';
-- Should equal: SELECT COUNT(*) FROM blogs;

-- ============================================
-- MIGRATION: PRESS
-- ============================================
INSERT INTO press_translations (press_id, language, title, content, excerpt, slug, meta_title, meta_description)
SELECT 
    id,
    'en' as language,
    COALESCE(title_en, title) as title,
    COALESCE(content_en, content) as content,
    excerpt,
    COALESCE(slug, LOWER(REPLACE(REPLACE(COALESCE(title_en, title), ' ', '-'), '--', '-'))) as slug,
    COALESCE(title_en, title) as meta_title,
    excerpt as meta_description
FROM press 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- Verification: Press
-- SELECT COUNT(*) as press_migrated FROM press_translations WHERE language = 'en';
-- Should equal: SELECT COUNT(*) FROM press;

-- ============================================
-- MIGRATION: PRICING
-- ============================================
INSERT INTO pricing_translations (pricing_id, language, name, description, duration, features)
SELECT 
    id,
    'en' as language,
    COALESCE(title_en, title) as name,
    COALESCE(content_en, content) as description,
    '' as duration,
    '' as features
FROM pricing 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- Verification: Pricing
-- SELECT COUNT(*) as pricing_migrated FROM pricing_translations WHERE language = 'en';
-- Should equal: SELECT COUNT(*) FROM pricing;

-- ============================================
-- MIGRATION: LEGAL PAGES
-- ============================================
-- Legal pages require manual migration from HTML files
-- Insert page_key with language='en'
-- Content must be extracted from terms.html and privacy.html

-- Example structure (adjust content as needed):
-- INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
-- VALUES 
--   ('terms', 'en', 'Terms & Conditions', '<extracted HTML content>', CURDATE()),
--   ('privacy', 'en', 'Privacy Policy', '<extracted HTML content>', CURDATE());

