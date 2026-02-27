<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Edit Event API (Simple)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['id'])) {
        throw new Exception('Event ID is required');
    }
    
    $event_id = (int)$input['id'];
    
    error_log('=== EDIT EVENT API ===');
    error_log('Event ID: ' . $event_id);
    error_log('Input data: ' . json_encode($input));
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Extract English fields
    $title_en = $conn->real_escape_string($input['title_en'] ?? $input['title'] ?? '');
    $description_en = $conn->real_escape_string($input['description_en'] ?? $input['description'] ?? '');
    $location_en = $conn->real_escape_string($input['location_en'] ?? $input['location'] ?? '');
    
    // Extract Arabic fields
    $title_ar = isset($input['title_ar']) ? $conn->real_escape_string($input['title_ar']) : '';
    $description_ar = isset($input['description_ar']) ? $conn->real_escape_string($input['description_ar']) : '';
    $location_ar = isset($input['location_ar']) ? $conn->real_escape_string($input['location_ar']) : '';
    
    // Extract other fields
    $event_date = $input['event_date'] ?? null;
    $event_time = $input['event_time'] ?? null;
    $event_end_time = $input['event_end_time'] ?? null;
    $end_date = $input['end_date'] ?? null;
    $cover_image = isset($input['cover_image']) ? $conn->real_escape_string($input['cover_image']) : null;
    $video_url = $conn->real_escape_string($input['video_url'] ?? '');
    $category = $conn->real_escape_string($input['category'] ?? '');
    $is_featured = isset($input['is_featured']) ? (int)$input['is_featured'] : null;
    
    // Build update query for events table with bilingual columns
    $updates = [];
    if (!empty($title_en)) {
        $updates[] = "title = '$title_en'";
        $updates[] = "title_en = '$title_en'";
    }
    if (!empty($description_en)) {
        $updates[] = "description = '$description_en'";
        $updates[] = "description_en = '$description_en'";
    }
    if (!empty($location_en)) {
        $updates[] = "location = '$location_en'";
        $updates[] = "location_en = '$location_en'";
    }
    
    // Update Arabic columns if provided
    if (isset($input['title_ar'])) $updates[] = "title_ar = '$title_ar'";
    if (isset($input['description_ar'])) $updates[] = "description_ar = '$description_ar'";
    if (isset($input['location_ar'])) $updates[] = "location_ar = '$location_ar'";
    
    if ($event_date) $updates[] = "event_date = '$event_date'";
    if ($event_time) $updates[] = "event_time = '$event_time'";
    if ($event_end_time) $updates[] = "event_end_time = '$event_end_time'";
    if ($end_date) $updates[] = "end_date = '$end_date'";
    if ($cover_image) $updates[] = "cover_image = '$cover_image'";
    
    // Handle video_url - allow clearing it (empty string)
    if (isset($input['video_url'])) {
        $updates[] = "video_url = " . ($video_url === '' ? "NULL" : "'$video_url'");
    }
    
    if (!empty($category)) $updates[] = "category = '$category'";
    if ($is_featured !== null) $updates[] = "is_featured = $is_featured";
    
    if (!empty($updates)) {
        $query = "UPDATE events SET " . implode(", ", $updates) . " WHERE id = $event_id";
        error_log('Update query: ' . $query);
        
        if (!$conn->query($query)) {
            throw new Exception('Update failed: ' . $conn->error);
        }
    }
    
    // Update English translation in event_translations table
    if (!empty($title_en) || !empty($description_en) || !empty($location_en)) {
        $insertEnglishQuery = "
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES ($event_id, 'en', '$title_en', '$description_en', '$location_en')
            ON DUPLICATE KEY UPDATE
                title = '$title_en',
                description = '$description_en',
                location = '$location_en',
                updated_at = CURRENT_TIMESTAMP
        ";
        
        error_log('English translation query: ' . $insertEnglishQuery);
        
        if (!$conn->query($insertEnglishQuery)) {
            error_log('Warning: English translation update failed: ' . $conn->error);
        }
    }
    
    // Update Arabic translation in event_translations table if provided
    if (isset($input['title_ar']) || isset($input['description_ar']) || isset($input['location_ar'])) {
        $insertArabicQuery = "
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES ($event_id, 'ar', '$title_ar', '$description_ar', '$location_ar')
            ON DUPLICATE KEY UPDATE
                title = '$title_ar',
                description = '$description_ar',
                location = '$location_ar',
                updated_at = CURRENT_TIMESTAMP
        ";
        
        error_log('Arabic translation query: ' . $insertArabicQuery);
        
        if (!$conn->query($insertArabicQuery)) {
            error_log('Warning: Arabic translation update failed: ' . $conn->error);
        }
    }
    
    error_log('Event updated successfully');
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event updated successfully',
        'event_id' => $event_id
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

