<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Add Event API (Simple)
 * Handles bilingual event creation with Arabic translations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields - support both title and title_en
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    
    if (empty($title_en)) {
        throw new Exception('Event title is required');
    }
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Extract English fields
    $description_en = $conn->real_escape_string($input['description_en'] ?? $input['description'] ?? '');
    $location_en = $conn->real_escape_string($input['location_en'] ?? $input['location'] ?? '');
    
    // Extract Arabic fields (optional)
    // IMPORTANT: Save Arabic fields even if empty, so they exist in the database
    $title_ar = isset($input['title_ar']) ? $conn->real_escape_string($input['title_ar']) : '';
    $description_ar = isset($input['description_ar']) ? $conn->real_escape_string($input['description_ar']) : '';
    $location_ar = isset($input['location_ar']) ? $conn->real_escape_string($input['location_ar']) : '';
    
    // Extract base fields
    $event_date = $input['event_date'] ?? date('Y-m-d');
    $event_time = $input['event_time'] ?? '10:00:00';
    $event_end_time = $input['event_end_time'] ?? '18:00:00';
    $end_date = $input['end_date'] ?? null;
    $cover_image = $conn->real_escape_string($input['cover_image'] ?? 'assest/img-4.png');
    $video_url = $conn->real_escape_string($input['video_url'] ?? '');
    $category = $conn->real_escape_string($input['category'] ?? 'exhibition');
    $is_featured = (int)($input['is_featured'] ?? 0);
    
    // Generate slug from English title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
    
    // Make slug unique by adding a number if it already exists
    $original_slug = $slug;
    $counter = 1;
    while (true) {
        $check_query = "SELECT id FROM events WHERE slug = '$slug' LIMIT 1";
        $result = $conn->query($check_query);
        if (!$result || $result->num_rows === 0) {
            break; // Slug is unique
        }
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    // Insert event into events table with bilingual columns
    $query = "
        INSERT INTO events (
            title, description, location, slug,
            title_en, description_en, location_en,
            title_ar, description_ar, location_ar,
            event_date, event_time, event_end_time, end_date,
            cover_image, video_url, is_featured, category
        ) VALUES (
            '$title_en', '$description_en', '$location_en', '$slug',
            '$title_en', '$description_en', '$location_en',
            '$title_ar', '$description_ar', '$location_ar',
            '$event_date', '$event_time', '$event_end_time', " . ($end_date ? "'$end_date'" : "NULL") . ",
            '$cover_image', '$video_url', $is_featured, '$category'
        )
    ";
    
    if (!$conn->query($query)) {
        throw new Exception('Insert failed: ' . $conn->error);
    }
    
    $event_id = $conn->insert_id;
    
    // Insert English translation into event_translations table
    $insertEnglishQuery = "
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES ($event_id, 'en', '$title_en', '$description_en', '$location_en')
        ON DUPLICATE KEY UPDATE
            title = '$title_en',
            description = '$description_en',
            location = '$location_en',
            updated_at = CURRENT_TIMESTAMP
    ";
    
    if (!$conn->query($insertEnglishQuery)) {
        error_log('Warning: English translation insert failed: ' . $conn->error);
    }
    
    // Insert Arabic translation if provided
    $arabicInserted = false;
    // ALWAYS try to save Arabic translation, even if fields are empty
    // This ensures the translation record exists in the database
    $insertArabicQuery = "
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES ($event_id, 'ar', '$title_ar', '$description_ar', '$location_ar')
        ON DUPLICATE KEY UPDATE
            title = '$title_ar',
            description = '$description_ar',
            location = '$location_ar',
            updated_at = CURRENT_TIMESTAMP
    ";
    
    if (!$conn->query($insertArabicQuery)) {
        error_log('Warning: Arabic translation insert failed: ' . $conn->error);
    } else {
        $arabicInserted = true;
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'event_id' => $event_id,
        'slug' => $slug,
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



