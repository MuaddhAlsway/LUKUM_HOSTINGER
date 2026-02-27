-- ============================================
-- FULL HYBRID TRANSLATION ARCHITECTURE
-- PHASE 2: DATA MIGRATION QUERIES (SIMPLIFIED)
-- ============================================

USE `lakum_artspace`;

-- ============================================
-- MIGRATION: EVENTS
-- ============================================
INSERT INTO event_translations (event_id, language, title, description, location, slug)
SELECT 
    id,
    'en',
    COALESCE(title_en, title),
    COALESCE(description_en, description),
    location,
    CONCAT(LOWER(REPLACE(COALESCE(title_en, title), ' ', '-')), '-', id)
FROM events 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- ============================================
-- MIGRATION: BLOGS
-- ============================================
INSERT INTO blog_translations (blog_id, language, title, content, excerpt, slug)
SELECT 
    id,
    'en',
    COALESCE(title_en, title),
    COALESCE(content_en, content),
    excerpt,
    CONCAT(LOWER(REPLACE(COALESCE(title_en, title), ' ', '-')), '-', id)
FROM blogs 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- ============================================
-- MIGRATION: PRESS
-- ============================================
INSERT INTO press_translations (press_id, language, title, content, excerpt, slug)
SELECT 
    id,
    'en',
    COALESCE(title_en, title),
    COALESCE(content_en, content),
    excerpt,
    COALESCE(slug, CONCAT(LOWER(REPLACE(COALESCE(title_en, title), ' ', '-')), '-', id))
FROM press 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- ============================================
-- MIGRATION: PRICING
-- ============================================
INSERT INTO pricing_translations (pricing_id, language, name, description, duration, features)
SELECT 
    id,
    'en',
    COALESCE(title_en, title),
    COALESCE(content_en, content),
    '',
    ''
FROM pricing 
WHERE title IS NOT NULL OR title_en IS NOT NULL;

-- ============================================
-- MIGRATION: LEGAL PAGES
-- ============================================
-- Legal pages require manual migration
-- Placeholder for manual insertion

