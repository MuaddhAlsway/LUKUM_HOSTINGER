<?php
/**
 * SIMPLIFIED Get Event Details API - For editing events
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    require_once 'config.php';
    
    $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
    error_log("=== GET_EVENT_DETAILS_SIMPLE START ===");
    error_log("Requested ID: $eventId");
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database not connected');
    }
    
    error_log("Database connected");
    
    // First check what columns actually exist
    $columnsResult = $db->getConnection()->query("SHOW COLUMNS FROM events");
    $eventColumns = [];
    while ($col = $columnsResult->fetch_assoc()) {
        $eventColumns[] = $col['Field'];
    }
    error_log("Events table columns: " . json_encode($eventColumns));
    
    // Build dynamic query based on available columns
    $selectFields = ['id', 'event_date', 'video_url'];
    $bilingual = ['title', 'description', 'location'];
    
    foreach ($bilingual as $field) {
        if (in_array($field . '_en', $eventColumns)) {
            $selectFields[] = $field . '_en';
        } else {
            $selectFields[] = $field; // fallback to single language
        }
        
        if (in_array($field . '_ar', $eventColumns)) {
            $selectFields[] = $field . '_ar';
        }
    }
    
    // Add time fields if they exist
    foreach (['event_time', 'event_end_time', 'end_date', 'cover_image'] as $field) {
        if (in_array($field, $eventColumns)) {
            $selectFields[] = $field;
        }
    }
    
    $selectSQL = implode(', ', $selectFields);
    error_log("Select SQL: " . $selectSQL);
    
    $event = null;
    
    // Try exhibitions table first
    $exhibitionResult = $db->getConnection()->query(
        "SELECT id, title_en, title_ar, description_en, description_ar, location_en, location_ar, 
                exhibition_date, exhibition_time, exhibition_end_time, end_date, event_video, cover_image, gallery_images 
         FROM exhibitions WHERE id = $eventId LIMIT 1"
    );
    
    if ($exhibitionResult && $exhibitionResult->num_rows > 0) {
        error_log("Found in exhibitions table");
        $event = $exhibitionResult->fetch_assoc();
        
        $event = [
            'id' => $event['id'],
            'title' => $event['title_en'],
            'title_en' => $event['title_en'],
            'title_ar' => $event['title_ar'],
            'description' => $event['description_en'],
            'description_en' => $event['description_en'],
            'description_ar' => $event['description_ar'],
            'location' => $event['location_en'],
            'location_en' => $event['location_en'],
            'location_ar' => $event['location_ar'],
            'event_date' => $event['exhibition_date'],
            'event_time' => $event['exhibition_time'],
            'event_end_time' => $event['exhibition_end_time'],
            'end_date' => $event['end_date'],
            'video_url' => $event['event_video'],
            'event_video' => $event['event_video'],
            'cover_image' => $event['cover_image'],
            'gallery_images' => $event['gallery_images'],
            'category' => 'exhibition'
        ];
    } else {
        error_log("Not in exhibitions, trying events table");
        
        // Try events table with dynamic columns
        $eventResult = $db->getConnection()->query(
            "SELECT $selectSQL FROM events WHERE id = $eventId LIMIT 1"
        );
        
        if (!$eventResult) {
            error_log("Events query failed: " . $db->getConnection()->error);
            throw new Exception('Events query failed: ' . $db->getConnection()->error);
        }
        
        if ($eventResult->num_rows === 0) {
            error_log("Event not found");
            throw new Exception('Event/Exhibition not found with ID: ' . $eventId);
        }
        
        error_log("Found in events table");
        $event = $eventResult->fetch_assoc();
        
        // Normalize field names
        $event = [
            'id' => $event['id'],
            'title' => $event['title_en'] ?? $event['title'] ?? '',
            'title_en' => $event['title_en'] ?? $event['title'] ?? '',
            'title_ar' => $event['title_ar'] ?? '',
            'description' => $event['description_en'] ?? $event['description'] ?? '',
            'description_en' => $event['description_en'] ?? $event['description'] ?? '',
            'description_ar' => $event['description_ar'] ?? '',
            'location' => $event['location_en'] ?? $event['location'] ?? '',
            'location_en' => $event['location_en'] ?? $event['location'] ?? '',
            'location_ar' => $event['location_ar'] ?? '',
            'event_date' => $event['event_date'] ?? '',
            'event_time' => $event['event_time'] ?? '',
            'event_end_time' => $event['event_end_time'] ?? '',
            'end_date' => $event['end_date'] ?? '',
            'video_url' => $event['video_url'] ?? '',
            'event_video' => $event['video_url'] ?? '',
            'cover_image' => $event['cover_image'] ?? '',
            'gallery_images' => null,
            'category' => 'event'
        ];
    }
    
    error_log("Returning event: " . json_encode($event));
    error_log("=== GET_EVENT_DETAILS_SIMPLE END ===");
    
    // Get gallery images
    $gallery = [];
    
    // First, check if this is an exhibition with JSON gallery_images
    if (isset($event['category']) && $event['category'] === 'exhibition' && isset($event['gallery_images']) && !empty($event['gallery_images'])) {
        error_log("Attempting to parse gallery_images JSON from exhibition");
        try {
            $galleryImages = json_decode($event['gallery_images'], true);
            if (is_array($galleryImages)) {
                foreach ($galleryImages as $imagePath) {
                    $gallery[] = [
                        'id' => 0,
                        'event_id' => $eventId,
                        'image_url' => $imagePath
                    ];
                }
                error_log("Loaded " . count($gallery) . " gallery images from exhibition gallery_images JSON");
            }
        } catch (Exception $e) {
            error_log("Error parsing gallery_images JSON: " . $e->getMessage());
        }
    }
    
    // If no gallery from JSON, try event_gallery table
    if (empty($gallery)) {
        error_log("Querying event_gallery table for event ID: $eventId");
        $galleryQuery = 'SELECT id, event_id, image_url FROM event_gallery WHERE event_id = ? ORDER BY display_order ASC';
        $galleryStmt = $db->prepare($galleryQuery);
        
        if ($galleryStmt) {
            $galleryStmt->bind_param('i', $eventId);
            if ($galleryStmt->execute()) {
                $galleryResult = $galleryStmt->get_result();
                while ($row = $galleryResult->fetch_assoc()) {
                    $gallery[] = $row;
                }
                if (!empty($gallery)) {
                    error_log("Loaded " . count($gallery) . " gallery images from event_gallery table");
                }
            } else {
                error_log("Gallery query execution failed: " . $galleryStmt->error);
            }
        } else {
            error_log("Gallery query prepare failed");
        }
    }
    
    // If still no gallery, use cover image as fallback
    if (empty($gallery) && isset($event['cover_image']) && !empty($event['cover_image'])) {
        error_log("Using cover image as gallery fallback");
        $gallery[] = [
            'id' => 0,
            'event_id' => $eventId,
            'image_url' => $event['cover_image']
        ];
    }
    
    error_log("Total gallery images found: " . count($gallery));
    
    echo json_encode([
        'success' => true,
        'data' => $event,
        'event' => $event,
        'gallery' => $gallery,
        'source' => 'database'
    ]);
    
} catch (Exception $e) {
    error_log('ERROR: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}
?>
