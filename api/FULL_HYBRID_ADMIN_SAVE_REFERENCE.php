<?php
/**
 * FULL HYBRID TRANSLATION ARCHITECTURE
 * ADMIN SAVE LOGIC REFERENCE PATTERNS
 * 
 * These are template patterns for updating all admin save queries
 * Apply these patterns to:
 * - add_event.php / edit_event.php
 * - add_blog.php / edit_blog.php
 * - add_press.php / edit_press.php
 * - add_pricing.php / edit_pricing.php
 * - save_legal_page.php
 * 
 * Key principle: UPSERT logic prevents overwrites
 * Editing English does NOT overwrite Arabic
 * Editing Arabic does NOT overwrite English
 */

// ============================================
// PATTERN 1: ADD EVENT (with bilingual support)
// ============================================
// File: api/add_event.php
// Updated save logic:

function addEventHybrid($db, $data) {
    try {
        // Extract English fields (required)
        $title_en = $data['title_en'] ?? '';
        $description_en = $data['description_en'] ?? '';
        $location_en = $data['location_en'] ?? '';
        $slug_en = $data['slug_en'] ?? '';
        
        // Extract Arabic fields (optional)
        $title_ar = $data['title_ar'] ?? null;
        $description_ar = $data['description_ar'] ?? null;
        $location_ar = $data['location_ar'] ?? null;
        $slug_ar = $data['slug_ar'] ?? null;
        
        // Extract base fields (language-independent)
        $event_date = $data['event_date'] ?? date('Y-m-d');
        $event_time = $data['event_time'] ?? '10:00';
        $event_end_time = $data['event_end_time'] ?? '18:00';
        $end_date = $data['end_date'] ?? null;
        $cover_image = $data['cover_image'] ?? '';
        $video_url = $data['video_url'] ?? '';
        $is_featured = (int)($data['is_featured'] ?? 0);
        $category = $data['category'] ?? 'exhibition';
        
        // Step 1: Insert into events table (base entity)
        $insertEventQuery = '
            INSERT INTO events (
                title, description, location, slug,
                event_date, event_time, event_end_time, end_date,
                cover_image, video_url, is_featured, category
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ';
        
        $stmt = $db->prepare($insertEventQuery);
        $stmt->bind_param(
            'ssssssssssii',
            $title_en, $description_en, $location_en, $slug_en,
            $event_date, $event_time, $event_end_time, $end_date,
            $cover_image, $video_url, $is_featured, $category
        );
        $stmt->execute();
        $event_id = $db->insert_id;
        
        // Step 2: Insert English translation (UPSERT)
        $insertEnglishQuery = '
            INSERT INTO event_translations (event_id, language, title, description, location, slug)
            VALUES (?, "en", ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                location = VALUES(location),
                slug = VALUES(slug),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($insertEnglishQuery);
        $stmt->bind_param('issss', $event_id, $title_en, $description_en, $location_en, $slug_en);
        $stmt->execute();
        
        // Step 3: Insert Arabic translation if provided (UPSERT)
        if ($title_ar) {
            $insertArabicQuery = '
                INSERT INTO event_translations (event_id, language, title, description, location, slug)
                VALUES (?, "ar", ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    description = VALUES(description),
                    location = VALUES(location),
                    slug = VALUES(slug),
                    updated_at = CURRENT_TIMESTAMP
            ';
            
            $stmt = $db->prepare($insertArabicQuery);
            $stmt->bind_param('issss', $event_id, $title_ar, $description_ar, $location_ar, $slug_ar);
            $stmt->execute();
        }
        
        return [
            'success' => true,
            'event_id' => $event_id,
            'translations' => ['en' => true, 'ar' => !empty($title_ar)]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ============================================
// PATTERN 2: EDIT EVENT (with safe language isolation)
// ============================================
// File: api/edit_event.php
// Updated save logic:

function editEventHybrid($db, $data) {
    try {
        $event_id = (int)$data['event_id'];
        
        // Extract English fields (optional - only update if provided)
        $title_en = $data['title_en'] ?? null;
        $description_en = $data['description_en'] ?? null;
        $location_en = $data['location_en'] ?? null;
        $slug_en = $data['slug_en'] ?? null;
        
        // Extract Arabic fields (optional - only update if provided)
        $title_ar = $data['title_ar'] ?? null;
        $description_ar = $data['description_ar'] ?? null;
        $location_ar = $data['location_ar'] ?? null;
        $slug_ar = $data['slug_ar'] ?? null;
        
        // Extract base fields (optional)
        $event_date = $data['event_date'] ?? null;
        $event_time = $data['event_time'] ?? null;
        $event_end_time = $data['event_end_time'] ?? null;
        $end_date = $data['end_date'] ?? null;
        $cover_image = $data['cover_image'] ?? null;
        $video_url = $data['video_url'] ?? null;
        $is_featured = isset($data['is_featured']) ? (int)$data['is_featured'] : null;
        $category = $data['category'] ?? null;
        
        // Step 1: Update base event fields (only if provided)
        $updateFields = [];
        $updateParams = [];
        $updateTypes = '';
        
        if ($event_date !== null) {
            $updateFields[] = 'event_date = ?';
            $updateParams[] = $event_date;
            $updateTypes .= 's';
        }
        if ($event_time !== null) {
            $updateFields[] = 'event_time = ?';
            $updateParams[] = $event_time;
            $updateTypes .= 's';
        }
        if ($event_end_time !== null) {
            $updateFields[] = 'event_end_time = ?';
            $updateParams[] = $event_end_time;
            $updateTypes .= 's';
        }
        if ($end_date !== null) {
            $updateFields[] = 'end_date = ?';
            $updateParams[] = $end_date;
            $updateTypes .= 's';
        }
        if ($cover_image !== null) {
            $updateFields[] = 'cover_image = ?';
            $updateParams[] = $cover_image;
            $updateTypes .= 's';
        }
        if ($video_url !== null) {
            $updateFields[] = 'video_url = ?';
            $updateParams[] = $video_url;
            $updateTypes .= 's';
        }
        if ($is_featured !== null) {
            $updateFields[] = 'is_featured = ?';
            $updateParams[] = $is_featured;
            $updateTypes .= 'i';
        }
        if ($category !== null) {
            $updateFields[] = 'category = ?';
            $updateParams[] = $category;
            $updateTypes .= 's';
        }
        
        if (!empty($updateFields)) {
            $updateEventQuery = 'UPDATE events SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
            $updateParams[] = $event_id;
            $updateTypes .= 'i';
            
            $stmt = $db->prepare($updateEventQuery);
            $stmt->bind_param($updateTypes, ...$updateParams);
            $stmt->execute();
        }
        
        // Step 2: Update English translation (UPSERT - only if provided)
        if ($title_en !== null || $description_en !== null || $location_en !== null || $slug_en !== null) {
            $updateEnglishQuery = '
                INSERT INTO event_translations (event_id, language, title, description, location, slug)
                VALUES (?, "en", ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = COALESCE(VALUES(title), title),
                    description = COALESCE(VALUES(description), description),
                    location = COALESCE(VALUES(location), location),
                    slug = COALESCE(VALUES(slug), slug),
                    updated_at = CURRENT_TIMESTAMP
            ';
            
            $stmt = $db->prepare($updateEnglishQuery);
            $stmt->bind_param('issss', $event_id, $title_en, $description_en, $location_en, $slug_en);
            $stmt->execute();
        }
        
        // Step 3: Update Arabic translation (UPSERT - only if provided)
        if ($title_ar !== null || $description_ar !== null || $location_ar !== null || $slug_ar !== null) {
            $updateArabicQuery = '
                INSERT INTO event_translations (event_id, language, title, description, location, slug)
                VALUES (?, "ar", ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = COALESCE(VALUES(title), title),
                    description = COALESCE(VALUES(description), description),
                    location = COALESCE(VALUES(location), location),
                    slug = COALESCE(VALUES(slug), slug),
                    updated_at = CURRENT_TIMESTAMP
            ';
            
            $stmt = $db->prepare($updateArabicQuery);
            $stmt->bind_param('issss', $event_id, $title_ar, $description_ar, $location_ar, $slug_ar);
            $stmt->execute();
        }
        
        return [
            'success' => true,
            'event_id' => $event_id,
            'updates' => [
                'base_fields' => !empty($updateFields),
                'english' => ($title_en !== null || $description_en !== null || $location_en !== null || $slug_en !== null),
                'arabic' => ($title_ar !== null || $description_ar !== null || $location_ar !== null || $slug_ar !== null)
            ]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ============================================
// PATTERN 3: ADD BLOG (with bilingual support)
// ============================================
// File: api/add_blog.php
// Same pattern as events, but with blog-specific fields:
// - title_en, content_en, excerpt_en, slug_en
// - title_ar, content_ar, excerpt_ar, slug_ar
// - Base fields: author, category, cover_image, read_time, is_featured

// ============================================
// PATTERN 4: ADD PRESS (with bilingual support)
// ============================================
// File: api/add_press.php
// Same pattern as events, but with press-specific fields:
// - title_en, content_en, excerpt_en, slug_en
// - title_ar, content_ar, excerpt_ar, slug_ar
// - Base fields: source, press_date, url, category, cover_image

// ============================================
// PATTERN 5: ADD PRICING (with bilingual support)
// ============================================
// File: api/add_pricing.php
// Same pattern as events, but with pricing-specific fields:
// - name_en, description_en, duration_en, features_en
// - name_ar, description_ar, duration_ar, features_ar
// - Base fields: price, currency, is_popular, is_active, display_order
// - NO slug field for pricing

// ============================================
// PATTERN 6: SAVE LEGAL PAGE (with bilingual support)
// ============================================
// File: api/save_legal_page.php
// Updated save logic:

function saveLegalPageHybrid($db, $data) {
    try {
        $page_key = $data['page_key'] ?? 'terms'; // 'terms' or 'privacy'
        
        // Extract English content (required)
        $title_en = $data['title_en'] ?? '';
        $content_en = $data['content_en'] ?? '';
        
        // Extract Arabic content (optional)
        $title_ar = $data['title_ar'] ?? null;
        $content_ar = $data['content_ar'] ?? null;
        
        $today = date('Y-m-d');
        
        // Step 1: Insert/Update English legal page (UPSERT)
        $insertEnglishQuery = '
            INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
            VALUES (?, "en", ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                content = VALUES(content),
                last_updated = VALUES(last_updated),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($insertEnglishQuery);
        $stmt->bind_param('ssss', $page_key, $title_en, $content_en, $today);
        $stmt->execute();
        
        // Step 2: Insert/Update Arabic legal page if provided (UPSERT)
        if ($title_ar) {
            $insertArabicQuery = '
                INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
                VALUES (?, "ar", ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    content = VALUES(content),
                    last_updated = VALUES(last_updated),
                    updated_at = CURRENT_TIMESTAMP
            ';
            
            $stmt = $db->prepare($insertArabicQuery);
            $stmt->bind_param('ssss', $page_key, $title_ar, $content_ar, $today);
            $stmt->execute();
        }
        
        return [
            'success' => true,
            'page_key' => $page_key,
            'translations' => ['en' => true, 'ar' => !empty($title_ar)]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ============================================
// KEY PRINCIPLES FOR ALL ADMIN SAVE LOGIC
// ============================================
// 1. Always insert into base table first (events, blogs, press, pricing)
// 2. Then insert/update translation tables
// 3. Use UPSERT (INSERT ... ON DUPLICATE KEY UPDATE)
// 4. Only update provided fields (null = don't update)
// 5. Use COALESCE(VALUES(field), field) to preserve existing values
// 6. Editing English does NOT overwrite Arabic
// 7. Editing Arabic does NOT overwrite English
// 8. Base fields always update (language-independent)
// 9. Translation fields only update if provided
// 10. Return success/failure status with details

// ============================================
// UPSERT LOGIC EXPLANATION
// ============================================
// INSERT ... ON DUPLICATE KEY UPDATE means:
// - If (entity_id, language) doesn't exist: INSERT new row
// - If (entity_id, language) exists: UPDATE existing row
// - This prevents duplicate key errors
// - This allows safe partial updates
// - This prevents overwrites of other languages

// COALESCE(VALUES(field), field) means:
// - If new value provided: use new value
// - If new value is NULL: keep existing value
// - This allows partial updates without losing data

?>

