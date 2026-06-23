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
    // Accept both 'id', 'slug', and 'title' parameters
    $eventIdParam = $_GET['id'] ?? $_GET['slug'] ?? $_GET['title'] ?? 1;
    
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
        // Normalize slug: lowercase, replace spaces with hyphens, remove special chars
        $slugParam = strtolower(trim($eventIdParam));
        $slugParam = preg_replace('/[^a-z0-9-]/', '', $slugParam);
        $slugParam = preg_replace('/-+/', '-', $slugParam);
        $slugParam = trim($slugParam, '-');
        
        error_log("DEBUG: Looking for slug: '$slugParam' from param: '$eventIdParam'");
        
        // Check if slug column exists in events table
        $columnCheckQuery = "SHOW COLUMNS FROM events LIKE 'slug'";
        $columnResult = $db->getConnection()->query($columnCheckQuery);
        $slugColumnExists = $columnResult && $columnResult->num_rows > 0;
        
        error_log("DEBUG: Slug column exists in events: " . ($slugColumnExists ? 'yes' : 'no'));
        
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
                        error_log("DEBUG: Found event ID: $eventId for slug: $slugParam");
                    } else {
                        error_log("DEBUG: No event found for slug: $slugParam in events table");
                        // Try case-insensitive search in events
                        $slugQuery2 = 'SELECT id FROM events WHERE LOWER(slug) = LOWER(?) LIMIT 1';
                        $stmt2 = $db->prepare($slugQuery2);
                        if ($stmt2) {
                            $stmt2->bind_param('s', $slugParam);
                            if ($stmt2->execute()) {
                                $result2 = $stmt2->get_result();
                                if ($row2 = $result2->fetch_assoc()) {
                                    $eventId = (int)$row2['id'];
                                    error_log("DEBUG: Found event ID (case-insensitive): $eventId for slug: $slugParam");
                                }
                            }
                        }
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
                        error_log("DEBUG: Found event ID (title fallback): $eventId for slug: $slugParam");
                    }
                }
            }
        }
        
        // If not found in events table, try exhibitions table by title_en
        if ($eventId === null) {
            error_log("DEBUG: Event not found in events table, trying exhibitions table with slug: $slugParam");
            
            $exhibitionTitleQuery = '
                SELECT id FROM exhibitions 
                WHERE LOWER(REPLACE(REPLACE(REPLACE(title_en, " ", "-"), ".", ""), ",", "")) = ?
                LIMIT 1
            ';
            $exhibitionStmt = $db->prepare($exhibitionTitleQuery);
            if ($exhibitionStmt) {
                $exhibitionStmt->bind_param('s', $slugParam);
                if ($exhibitionStmt->execute()) {
                    $exhibitionResult = $exhibitionStmt->get_result();
                    if ($exhibitionRow = $exhibitionResult->fetch_assoc()) {
                        $eventId = (int)$exhibitionRow['id'];
                        error_log("DEBUG: Found exhibition ID: $eventId for slug: $slugParam");
                    }
                }
            }
        }
    }
    
    // If no event found by slug, throw error instead of defaulting to 1
    if ($eventId === null) {
        if ($isNumeric) {
            $eventId = (int)$eventIdParam;
        } else {
            throw new Exception("Event/Exhibition not found with slug: $eventIdParam");
        }
    }
    
    // Get event details with translation support
    // Query without translations table (fallback - using bilingual columns)
    // Note: events table has video_url, exhibitions table has event_video
    // We return BOTH fields for compatibility
    $eventQuery = '
        SELECT 
            e.id,
            e.event_date,
            e.event_time,
            e.event_end_time,
            e.end_date,
            e.cover_image,
            COALESCE(e.video_url, "") as video_url,
            COALESCE(e.video_url, "") as event_video,
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
    
    // If not found in events table, try exhibitions table
    if (!$event) {
        error_log("DEBUG: Event not found in events table, trying exhibitions table with ID: $eventId");
        
        $exhibitionQuery = '
            SELECT 
                ex.id,
                ex.exhibition_date as event_date,
                ex.exhibition_time as event_time,
                ex.exhibition_end_time as event_end_time,
                ex.end_date,
                ex.cover_image,
                COALESCE(ex.event_video, "") as video_url,
                COALESCE(ex.event_video, "") as event_video,
                ex.gallery_images,
                "exhibition" as category,
                COALESCE(ex.title_en, ex.title) as title,
                COALESCE(ex.description_en, ex.description) as description,
                COALESCE(ex.location_en, ex.location) as location,
                COALESCE(ex.title_en, "") as title_en,
                COALESCE(ex.description_en, "") as description_en,
                COALESCE(ex.location_en, "") as location_en,
                COALESCE(ex.title_ar, "") as title_ar,
                COALESCE(ex.description_ar, "") as description_ar,
                COALESCE(ex.location_ar, "") as location_ar
            FROM exhibitions ex
            WHERE ex.id = ?
            LIMIT 1
        ';
        
        $exhibitionStmt = $db->prepare($exhibitionQuery);
        if ($exhibitionStmt) {
            $exhibitionStmt->bind_param('i', $eventId);
            if ($exhibitionStmt->execute()) {
                $exhibitionResult = $exhibitionStmt->get_result();
                $event = $exhibitionResult->fetch_assoc();
                
                if ($event) {
                    error_log("DEBUG: Found exhibition with ID: $eventId");
                }
            }
        }
    }
    
    if (!$event) {
        throw new Exception('Event/Exhibition not found with ID: ' . $eventId);
    }
    
    // Get gallery images - handle both events (event_gallery table) and exhibitions (gallery_images JSON)
    $gallery = [];
    
    // First, check if this is an exhibition (has gallery_images JSON field)
    if (isset($event['category']) && $event['category'] === 'exhibition' && isset($event['gallery_images']) && !empty($event['gallery_images'])) {
        // Parse JSON gallery images from exhibitions table
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
                error_log("DEBUG: Loaded " . count($gallery) . " gallery images from exhibition gallery_images JSON");
            }
        } catch (Exception $e) {
            error_log("DEBUG: Error parsing gallery_images JSON: " . $e->getMessage());
        }
    }
    
    // If no gallery from JSON, try event_gallery table (for events and exhibitions without JSON gallery)
    if (empty($gallery)) {
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
                    error_log("DEBUG: Loaded " . count($gallery) . " gallery images from event_gallery table");
                }
            }
        }
    }
    
    // If no gallery images from JSON or table, use cover image as fallback
    if (empty($gallery) && $event['cover_image']) {
        $gallery[] = [
            'id' => 0,
            'event_id' => $eventId,
            'image_url' => $event['cover_image']
        ];
        error_log("DEBUG: Using cover image as fallback gallery");
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



