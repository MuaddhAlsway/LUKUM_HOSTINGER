<?php
/**
 * FULL HYBRID TRANSLATION ARCHITECTURE
 * READ QUERY REFERENCE PATTERNS
 * 
 * These are template patterns for updating all read queries
 * Apply these patterns to:
 * - get_events.php
 * - get_event_details.php
 * - get_blogs.php
 * - blog.php
 * - get_press.php
 * - press-details.php
 * - get_pricing.php
 * - pricing.php
 * - legal.php
 */

// ============================================
// PATTERN 1: GET ALL EVENTS (with translation)
// ============================================
// File: api/get_events.php
// Updated query pattern:

$lang = getCurrentLanguage(); // From lang/loader.php

$query = '
    SELECT 
        e.id,
        e.event_date,
        e.event_time,
        e.event_end_time,
        e.end_date,
        e.cover_image,
        e.video_url,
        e.is_featured,
        e.category,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.description, t_en.description) as description,
        COALESCE(t_current.location, t_en.location) as location,
        COALESCE(t_current.slug, t_en.slug) as slug
    FROM events e
    LEFT JOIN event_translations t_current ON e.id = t_current.event_id AND t_current.language = ?
    LEFT JOIN event_translations t_en ON e.id = t_en.event_id AND t_en.language = "en"
    WHERE 1=1
    ORDER BY e.event_date ASC
    LIMIT ? OFFSET ?
';

// Bind parameters: $lang, $limit, $offset
// Response structure UNCHANGED:
// {
//     id, title, description, location, slug,
//     event_date, event_time, event_end_time, end_date,
//     cover_image, video_url, is_featured, category
// }

// ============================================
// PATTERN 2: GET EVENT DETAILS (slug + language)
// ============================================
// File: api/get_event_details.php
// Updated query pattern:

$lang = getCurrentLanguage();
$slug = $_GET['slug'] ?? null;
$id = $_GET['id'] ?? null;

if ($slug) {
    // Query by slug + language (SEO-safe)
    $query = '
        SELECT 
            e.id,
            e.event_date,
            e.event_time,
            e.event_end_time,
            e.end_date,
            e.cover_image,
            e.video_url,
            e.category,
            COALESCE(t_current.title, t_en.title) as title,
            COALESCE(t_current.description, t_en.description) as description,
            COALESCE(t_current.location, t_en.location) as location,
            COALESCE(t_current.slug, t_en.slug) as slug
        FROM events e
        LEFT JOIN event_translations t_current ON e.id = t_current.event_id AND t_current.language = ?
        LEFT JOIN event_translations t_en ON e.id = t_en.event_id AND t_en.language = "en"
        WHERE COALESCE(t_current.slug, t_en.slug) = ?
        LIMIT 1
    ';
    // Bind: $lang, $slug
} else {
    // Query by ID (fallback)
    $query = '
        SELECT 
            e.id,
            e.event_date,
            e.event_time,
            e.event_end_time,
            e.end_date,
            e.cover_image,
            e.video_url,
            e.category,
            COALESCE(t_current.title, t_en.title) as title,
            COALESCE(t_current.description, t_en.description) as description,
            COALESCE(t_current.location, t_en.location) as location,
            COALESCE(t_current.slug, t_en.slug) as slug
        FROM events e
        LEFT JOIN event_translations t_current ON e.id = t_current.event_id AND t_current.language = ?
        LEFT JOIN event_translations t_en ON e.id = t_en.event_id AND t_en.language = "en"
        WHERE e.id = ?
        LIMIT 1
    ';
    // Bind: $lang, $id
}

// Response structure UNCHANGED:
// {
//     id, title, description, location, slug,
//     event_date, event_time, event_end_time, end_date,
//     cover_image, video_url, category
// }

// ============================================
// PATTERN 3: GET ALL BLOGS (with translation)
// ============================================
// File: api/get_blogs.php
// Updated query pattern:

$lang = getCurrentLanguage();

$query = '
    SELECT 
        b.id,
        b.author,
        b.category,
        b.cover_image,
        b.read_time,
        b.is_featured,
        b.created_at,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.content, t_en.content) as content,
        COALESCE(t_current.excerpt, t_en.excerpt) as excerpt,
        COALESCE(t_current.slug, t_en.slug) as slug
    FROM blogs b
    LEFT JOIN blog_translations t_current ON b.id = t_current.blog_id AND t_current.language = ?
    LEFT JOIN blog_translations t_en ON b.id = t_en.blog_id AND t_en.language = "en"
    WHERE b.is_published = 1
    ORDER BY b.created_at DESC
    LIMIT ? OFFSET ?
';

// Bind: $lang, $limit, $offset
// Response structure UNCHANGED:
// {
//     id, title, content, excerpt, slug,
//     author, category, cover_image, read_time,
//     is_featured, created_at
// }

// ============================================
// PATTERN 4: GET BLOG DETAILS (slug + language)
// ============================================
// File: blog.php or get_blog_details.php
// Updated query pattern:

$lang = getCurrentLanguage();
$slug = $_GET['slug'] ?? null;

$query = '
    SELECT 
        b.id,
        b.author,
        b.category,
        b.cover_image,
        b.read_time,
        b.created_at,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.content, t_en.content) as content,
        COALESCE(t_current.excerpt, t_en.excerpt) as excerpt,
        COALESCE(t_current.slug, t_en.slug) as slug
    FROM blogs b
    LEFT JOIN blog_translations t_current ON b.id = t_current.blog_id AND t_current.language = ?
    LEFT JOIN blog_translations t_en ON b.id = t_en.blog_id AND t_en.language = "en"
    WHERE COALESCE(t_current.slug, t_en.slug) = ? AND b.is_published = 1
    LIMIT 1
';

// Bind: $lang, $slug
// Response structure UNCHANGED

// ============================================
// PATTERN 5: GET ALL PRESS (with translation)
// ============================================
// File: api/get_press.php
// Updated query pattern:

$lang = getCurrentLanguage();

$query = '
    SELECT 
        p.id,
        p.source,
        p.press_date,
        p.url,
        p.category,
        p.cover_image,
        p.created_at,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.content, t_en.content) as content,
        COALESCE(t_current.excerpt, t_en.excerpt) as excerpt,
        COALESCE(t_current.slug, t_en.slug) as slug
    FROM press p
    LEFT JOIN press_translations t_current ON p.id = t_current.press_id AND t_current.language = ?
    LEFT JOIN press_translations t_en ON p.id = t_en.press_id AND t_en.language = "en"
    WHERE p.is_published = 1
    ORDER BY p.press_date DESC
    LIMIT ? OFFSET ?
';

// Bind: $lang, $limit, $offset
// Response structure UNCHANGED

// ============================================
// PATTERN 6: GET PRESS DETAILS (slug + language)
// ============================================
// File: press-details.php or get_press_details.php
// Updated query pattern:

$lang = getCurrentLanguage();
$slug = $_GET['slug'] ?? null;

$query = '
    SELECT 
        p.id,
        p.source,
        p.press_date,
        p.url,
        p.category,
        p.cover_image,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.content, t_en.content) as content,
        COALESCE(t_current.excerpt, t_en.excerpt) as excerpt,
        COALESCE(t_current.slug, t_en.slug) as slug
    FROM press p
    LEFT JOIN press_translations t_current ON p.id = t_current.press_id AND t_current.language = ?
    LEFT JOIN press_translations t_en ON p.id = t_en.press_id AND t_en.language = "en"
    WHERE COALESCE(t_current.slug, t_en.slug) = ? AND p.is_published = 1
    LIMIT 1
';

// Bind: $lang, $slug
// Response structure UNCHANGED

// ============================================
// PATTERN 7: GET ALL PRICING (with translation)
// ============================================
// File: api/get_pricing.php
// Updated query pattern:

$lang = getCurrentLanguage();

$query = '
    SELECT 
        p.id,
        p.price,
        p.currency,
        p.is_popular,
        p.is_active,
        p.display_order,
        COALESCE(t_current.name, t_en.name) as name,
        COALESCE(t_current.description, t_en.description) as description,
        COALESCE(t_current.duration, t_en.duration) as duration,
        COALESCE(t_current.features, t_en.features) as features
    FROM pricing p
    LEFT JOIN pricing_translations t_current ON p.id = t_current.pricing_id AND t_current.language = ?
    LEFT JOIN pricing_translations t_en ON p.id = t_en.pricing_id AND t_en.language = "en"
    WHERE p.is_active = 1
    ORDER BY p.display_order ASC
';

// Bind: $lang
// Response structure UNCHANGED:
// {
//     id, name, description, duration, features,
//     price, currency, is_popular, is_active, display_order
// }

// ============================================
// PATTERN 8: GET LEGAL PAGE (with translation)
// ============================================
// File: legal.php or get_legal_page.php
// Updated query pattern:

$lang = getCurrentLanguage();
$page_key = $_GET['page'] ?? 'terms'; // 'terms' or 'privacy'

$query = '
    SELECT 
        page_key,
        language,
        COALESCE(t_current.title, t_en.title) as title,
        COALESCE(t_current.content, t_en.content) as content,
        COALESCE(t_current.last_updated, t_en.last_updated) as last_updated
    FROM legal_page_translations t_current
    LEFT JOIN legal_page_translations t_en ON t_current.page_key = t_en.page_key AND t_en.language = "en"
    WHERE t_current.page_key = ? AND t_current.language = ?
    LIMIT 1
';

// Bind: $page_key, $lang
// If no result, fallback to English:
// SELECT ... WHERE page_key = ? AND language = "en"

// Response structure UNCHANGED:
// {
//     page_key, title, content, last_updated
// }

// ============================================
// KEY PRINCIPLES FOR ALL QUERIES
// ============================================
// 1. Use COALESCE(t_current.field, t_en.field) for fallback
// 2. Always LEFT JOIN to translation tables
// 3. Filter by language in JOIN condition
// 4. Use indexes: (entity_id, language) and (slug, language)
// 5. Keep response field names IDENTICAL to before migration
// 6. Frontend binding remains unchanged
// 7. No new fields added to response
// 8. No translation array returned
// 9. Language filtering happens at database level
// 10. Fallback logic is automatic via COALESCE

// ============================================
// FALLBACK LOGIC EXPLANATION
// ============================================
// COALESCE(t_current.title, t_en.title) means:
// - If current language translation exists: use it
// - If current language translation missing: use English
// - If both missing: NULL (should not happen with proper migration)
//
// This ensures:
// - Arabic users see Arabic if available
// - Arabic users see English if Arabic not available
// - No broken content
// - No 404 errors
// - Graceful degradation

?>

