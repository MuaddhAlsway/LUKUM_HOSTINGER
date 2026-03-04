<?php
/**
 * LAKUM Artspace - Get Event Details API
 * Retrieves single event with gallery images and bilingual translations from database
 * Supports both numeric ID and slug-based lookups
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config.php';
require_once 'slug-utils.php';

try {
    $db = Database::getInstance();
    $eventIdParam = $_GET['id'] ?? 1;
    
    // Get current language from URL parameter or session
    $lang = $_GET['lang'] ?? $_SESSION['language'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Check if event_translations table exists
    $tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
    $tableResult = $db->getConnection()->query($tableCheckQuery);
    $translationsTableExists = $tableResult && $tableResult->num_rows > 0;
    
    // Determine if ID is numeric or slug
    $eventId = null;
    $isNumeric = is_numeric($eventIdParam);
    
    if ($isNumeric) {
        $eventId = (int)$eventIdParam;
    } else {
        // Try to find event by slug from base table
        $slugParam = strtolower(str_replace(' ', '-', $eventIdParam));
        
        // Check if slug column exists in events table
        $columnCheckQuery = "SHOW COLUMNS FROM events LIKE 'slug'";
        $columnResult = $db->getConnection()->query($columnCheckQuery);
        $slugColumnExists = $columnResult && $columnResult->num_rows > 0;
        
        if ($slugColumnExists) {
            // Query slug from events table (language-independent)
            $slugQuery = 'SELECT id FROM events WHERE slug = ? LIMIT 1';
            $stmt = $db->prepare($slugQuery);
            if ($stmt) {
                $stmt->bind_param('s', $slugParam);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $eventId = (int)$row['id'];
                    }
                }
            }
        } else {
            // Fallback: search by title if slug column doesn't exist yet
            $titleQuery = '
                SELECT id FROM events 
                WHERE LOWER(REPLACE(REPLACE(REPLACE(title, " ", "-"), ".", ""), ",", "")) = ?
                LIMIT 1
            ';
            $stmt = $db->prepare($titleQuery);
            if ($stmt) {
                $stmt->bind_param('s', $slugParam);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $eventId = (int)$row['id'];
                    }
                }
            }
        }
    }
    
    // If no event found by slug, use numeric ID or default to 1
    if ($eventId === null) {
        $eventId = $isNumeric ? (int)$eventIdParam : 1;
    }
    
    // Get event details with translation support
    // Query without translations table (fallback - using bilingual columns)
    $eventQuery = '
        SELECT 
            e.id,
            e.event_date,
            e.event_time,
            e.event_end_time,
            e.end_date,
            e.cover_image,
            e.video_url,
            e.category,
            COALESCE(e.title_en, e.title) as title,
            COALESCE(e.description_en, e.description) as description,
            COALESCE(e.location_en, e.location) as location,
            COALESCE(e.title_en, "") as title_en,
            COALESCE(e.description_en, "") as description_en,
            COALESCE(e.location_en, "") as location_en,
            COALESCE(e.title_ar, "") as title_ar,
            COALESCE(e.description_ar, "") as description_ar,
            COALESCE(e.location_ar, "") as location_ar
        FROM events e
        WHERE e.id = ?
        LIMIT 1
    ';
    
    $stmt = $db->prepare($eventQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $eventId);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    if (!$event) {
        throw new Exception('Event not found with ID: ' . $eventId);
    }
    
    // Get gallery images for this event
    $galleryQuery = 'SELECT id, event_id, image_url FROM event_gallery WHERE event_id = ? ORDER BY display_order ASC';
    $galleryStmt = $db->prepare($galleryQuery);
    
    if (!$galleryStmt) {
        throw new Exception('Prepare gallery failed: ' . $db->getConnection()->error);
    }
    
    $galleryStmt->bind_param('i', $eventId);
    if (!$galleryStmt->execute()) {
        throw new Exception('Execute gallery failed: ' . $galleryStmt->error);
    }
    
    $galleryResult = $galleryStmt->get_result();
    $gallery = [];
    
    while ($row = $galleryResult->fetch_assoc()) {
        $gallery[] = $row;
    }
    
    // If no gallery images, use cover image as fallback
    if (empty($gallery) && $event['cover_image']) {
        $gallery[] = [
            'id' => 0,
            'event_id' => $eventId,
            'image_url' => $event['cover_image']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'event' => $event,
        'gallery' => $gallery,
        'source' => 'database',
        'language' => $lang
    ]);
    
} catch (Exception $e) {
    error_log('Event Details API Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'source' => 'error'
    ]);
}
?>


