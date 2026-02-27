-- ============================================
-- FULL HYBRID TRANSLATION ARCHITECTURE
-- PHASE 2: DATA MIGRATION QUERIES
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
    title,
    description,
    location,
    slug,
    title as meta_title,
    description as meta_description
FROM events 
WHERE title IS NOT NULL;

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
    title,
    content,
    excerpt,
    slug,
    title as meta_title,
    excerpt as meta_description
FROM blogs 
WHERE title IS NOT NULL;

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
    title,
    content,
    excerpt,
    slug,
    title as meta_title,
    excerpt as meta_description
FROM press 
WHERE title IS NOT NULL;

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
    name,
    description,
    duration,
    features
FROM pricing 
WHERE name IS NOT NULL;

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

-- ============================================
-- COMPREHENSIVE VERIFICATION QUERIES
-- ============================================

-- 1. Migration Success Summary
-- SELECT 
--     'events' as entity, COUNT(*) as total_migrated 
-- FROM event_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'blogs', COUNT(*) FROM blog_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'press', COUNT(*) FROM press_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'pricing', COUNT(*) FROM pricing_translations WHERE language = 'en'
-- UNION ALL
-- SELECT 'legal_pages', COUNT(*) FROM legal_page_translations WHERE language = 'en';

-- 2. Check for Duplicate Translations (should be 0)
-- SELECT 'events' as entity, event_id, COUNT(*) as duplicate_count 
-- FROM event_translations 
-- GROUP BY event_id 
-- HAVING COUNT(*) > 1
-- UNION ALL
-- SELECT 'blogs', blog_id, COUNT(*) 
-- FROM blog_translations 
-- GROUP BY blog_id 
-- HAVING COUNT(*) > 1
-- UNION ALL
-- SELECT 'press', press_id, COUNT(*) 
-- FROM press_translations 
-- GROUP BY press_id 
-- HAVING COUNT(*) > 1
-- UNION ALL
-- SELECT 'pricing', pricing_id, COUNT(*) 
-- FROM pricing_translations 
-- GROUP BY pricing_id 
-- HAVING COUNT(*) > 1;

-- 3. Check Foreign Key Integrity (should be 0)
-- SELECT 'events' as entity, COUNT(*) as orphaned_records 
-- FROM event_translations et 
-- LEFT JOIN events e ON et.event_id = e.id 
-- WHERE e.id IS NULL
-- UNION ALL
-- SELECT 'blogs', COUNT(*) 
-- FROM blog_translations bt 
-- LEFT JOIN blogs b ON bt.blog_id = b.id 
-- WHERE b.id IS NULL
-- UNION ALL
-- SELECT 'press', COUNT(*) 
-- FROM press_translations pt 
-- LEFT JOIN press p ON pt.press_id = p.id 
-- WHERE p.id IS NULL
-- UNION ALL
-- SELECT 'pricing', COUNT(*) 
-- FROM pricing_translations prt 
-- LEFT JOIN pricing pr ON prt.pricing_id = pr.id 
-- WHERE pr.id IS NULL;

-- 4. Check Slug Uniqueness per Language (should be 0)
-- SELECT 'events' as entity, slug, language, COUNT(*) as duplicate_slugs 
-- FROM event_translations 
-- GROUP BY slug, language 
-- HAVING COUNT(*) > 1
-- UNION ALL
-- SELECT 'blogs', slug, language, COUNT(*) 
-- FROM blog_translations 
-- GROUP BY slug, language 
-- HAVING COUNT(*) > 1
-- UNION ALL
-- SELECT 'press', slug, language, COUNT(*) 
-- FROM press_translations 
-- GROUP BY slug, language 
-- HAVING COUNT(*) > 1;

-- 5. Data Integrity Check - Row Count Comparison
-- SELECT 
--     'events' as entity,
--     (SELECT COUNT(*) FROM events) as base_table_count,
--     (SELECT COUNT(*) FROM event_translations WHERE language = 'en') as translation_count
-- UNION ALL
-- SELECT 
--     'blogs',
--     (SELECT COUNT(*) FROM blogs),
--     (SELECT COUNT(*) FROM blog_translations WHERE language = 'en')
-- UNION ALL
-- SELECT 
--     'press',
--     (SELECT COUNT(*) FROM press),
--     (SELECT COUNT(*) FROM press_translations WHERE language = 'en')
-- UNION ALL
-- SELECT 
--     'pricing',
--     (SELECT COUNT(*) FROM pricing),
--     (SELECT COUNT(*) FROM pricing_translations WHERE language = 'en');

-- ============================================
-- NOTES
-- ============================================
-- 1. Original columns in base tables are PRESERVED
-- 2. No data is deleted
-- 3. Rollback is simple: DROP translation tables
-- 4. All foreign keys enforced
-- 5. All unique constraints enforced
-- 6. Cascade delete prevents orphaned records
-- 7. Timestamps track all changes


