<?php
/**
 * LAKUM Artspace - Add Event API
 * Creates new event in database with bilingual support
 * 
 * SLUG ARCHITECTURE:
 * - Single slug per event (language-independent)
 * - Generated from English title only
 * - Stored in events table
 * - Used for all languages
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'db.php';
require_once 'slug-utils.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Log the input for debugging
    error_log('ADD_EVENT_INPUT: ' . json_encode($input));
    
    // Validate required fields
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    
    if (empty($title_en)) {
        throw new Exception('Event title (English) is required');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Extract English fields (required)
    $description_en = $input['description_en'] ?? $input['description'] ?? '';
    $location_en = $input['location_en'] ?? $input['location'] ?? '';
    
    // Check if slug column exists in events table
    $columnCheckQuery = "SHOW COLUMNS FROM events LIKE 'slug'";
    $columnResult = $db->getConnection()->query($columnCheckQuery);
    $slugColumnExists = $columnResult && $columnResult->num_rows > 0;
    
    // Generate slug from English title ONLY (language-independent) - only if column exists
    $slug = null;
    if ($slugColumnExists) {
        $slug = $input['slug'] ?? generateSlug($title_en);
        // Ensure slug is unique
        $slug = makeUniqueSlug($slug, $db->getConnection(), 'events');
    }
    
    // Extract Arabic fields (optional)
    $title_ar = $input['title_ar'] ?? null;
    $description_ar = $input['description_ar'] ?? null;
    $location_ar = $input['location_ar'] ?? null;
    
    // Only treat as Arabic translation if title_ar is provided and not empty
    $hasArabicTranslation = !empty($title_ar);
    
    error_log('ARABIC_DETECTION: title_ar=' . ($title_ar ? 'YES' : 'NO') . ', hasArabic=' . ($hasArabicTranslation ? 'YES' : 'NO'));
    
    // Extract base fields (language-independent)
    $event_date = $input['event_date'] ?? date('Y-m-d');
    $event_time = $input['event_time'] ?? '10:00:00';
    $event_end_time = $input['event_end_time'] ?? '18:00:00';
    $end_date = $input['end_date'] ?? null;
    $cover_image = $input['cover_image'] ?? 'assest/img-4.png';
    $video_url = $input['video_url'] ?? '';
    $category = $input['category'] ?? 'exhibition';
    $is_featured = (int)($input['is_featured'] ?? 0);
    
    // Step 1: Insert into events table (base entity) with slug if column exists
    if ($slugColumnExists) {
        $insertEventQuery = '
            INSERT INTO events (
                title, description, location, slug,
                event_date, event_time, event_end_time, end_date,
                cover_image, video_url, is_featured, category
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ';
        
        $stmt = $db->prepare($insertEventQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param(
            'ssssssssssii',
            $title_en,
            $description_en,
            $location_en,
            $slug,
            $event_date,
            $event_time,
            $event_end_time,
            $end_date,
            $cover_image,
            $video_url,
            $is_featured,
            $category
        );
    } else {
        // Fallback: insert without slug column
        $insertEventQuery = '
            INSERT INTO events (
                title, description, location,
                event_date, event_time, event_end_time, end_date,
                cover_image, video_url, is_featured, category
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ';
        
        $stmt = $db->prepare($insertEventQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param(
            'sssssssssii',
            $title_en,
            $description_en,
            $location_en,
            $event_date,
            $event_time,
            $event_end_time,
            $end_date,
            $cover_image,
            $video_url,
            $is_featured,
            $category
        );
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $event_id = $db->getConnection()->insert_id;
    
    // Step 2: Insert English translation (NO SLUG - stored in base table)
    $insertEnglishQuery = '
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES (?, "en", ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            location = VALUES(location),
            updated_at = CURRENT_TIMESTAMP
    ';
    
    $stmt = $db->prepare($insertEnglishQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('isss', $event_id, $title_en, $description_en, $location_en);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Step 3: Insert Arabic translation if provided (NO SLUG - stored in base table)
    $arabicInserted = false;
    if ($hasArabicTranslation) {
        error_log('INSERTING_ARABIC: event_id=' . $event_id . ', title_ar=' . $title_ar);
        
        $insertArabicQuery = '
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES (?, "ar", ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                location = VALUES(location),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($insertArabicQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('isss', $event_id, $title_ar, $description_ar, $location_ar);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $arabicInserted = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'event_id' => $event_id,
        'slug' => $slug,
        'slug_column_exists' => $slugColumnExists,
        'translations' => [
            'en' => true,
            'ar' => $arabicInserted
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
