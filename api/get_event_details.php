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
header('Date: ' . date('r'));

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
                    }
                }
            }
        }
        
        // If not found in events, try exhibitions table by slug
        if ($eventId === null) {
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
            // Last resort: try to find ANY exhibition/event with similar title
            // This handles cases where slug matching fails
            error_log("DEBUG: Slug not found, trying fuzzy match for: $eventIdParam");
            
            // Approach 1: Try case-insensitive partial match on exhibitions
            $fuzzyQuery = "SELECT id FROM exhibitions WHERE LOWER(title_en) LIKE LOWER(?) LIMIT 1";
            $fuzzyStmt = $db->prepare($fuzzyQuery);
            if ($fuzzyStmt) {
                $searchTerm = "%" . $eventIdParam . "%";
                $fuzzyStmt->bind_param('s', $searchTerm);
                if ($fuzzyStmt->execute()) {
                    $fuzzyResult = $fuzzyStmt->get_result();
                    if ($fuzzyRow = $fuzzyResult->fetch_assoc()) {
                        $eventId = (int)$fuzzyRow['id'];
                        error_log("DEBUG: Found exhibition via SQL LOWER fuzzy match: $eventId");
                    }
                }
            }
            
            // Approach 2: If SQL LOWER didn't work, try PHP-based matching
            if ($eventId === null) {
                error_log("DEBUG: SQL LOWER didn't find match, trying PHP-based matching");
                
                // Get all exhibitions and match in PHP
                $allExhibitionsQuery = "SELECT id, title_en FROM exhibitions";
                $allExhibitionsResult = $db->getConnection()->query($allExhibitionsQuery);
                
                if ($allExhibitionsResult) {
                    $searchTermLower = strtolower($eventIdParam);
                    
                    while ($exRow = $allExhibitionsResult->fetch_assoc()) {
                        $titleLower = strtolower($exRow['title_en']);
                        
                        // Check if search term is contained in title
                        if (strpos($titleLower, $searchTermLower) !== false) {
                            $eventId = (int)$exRow['id'];
                            error_log("DEBUG: Found exhibition via PHP strpos fuzzy match: $eventId (searched for '$searchTermLower' in '" . $exRow['title_en'] . "')");
                            break;
                        }
                    }
                }
            }
            
            // Approach 3: If still not found, try events table
            if ($eventId === null) {
                error_log("DEBUG: No match in exhibitions, trying events table");
                
                $eventsQuery = "SELECT id, title FROM events WHERE LOWER(title) LIKE LOWER(?) LIMIT 1";
                $eventsStmt = $db->prepare($eventsQuery);
                if ($eventsStmt) {
                    $searchTerm = "%" . $eventIdParam . "%";
                    $eventsStmt->bind_param('s', $searchTerm);
                    if ($eventsStmt->execute()) {
                        $eventsResult = $eventsStmt->get_result();
                        if ($eventsRow = $eventsResult->fetch_assoc()) {
                            $eventId = (int)$eventsRow['id'];
                            error_log("DEBUG: Found event via SQL LOWER fuzzy match: $eventId");
                        }
                    }
                }
            }
            
            // Approach 4: PHP-based matching for events
            if ($eventId === null) {
                error_log("DEBUG: SQL LOWER didn't find event match, trying PHP-based matching on events");
                
                $allEventsQuery = "SELECT id, title FROM events";
                $allEventsResult = $db->getConnection()->query($allEventsQuery);
                
                if ($allEventsResult) {
                    $searchTermLower = strtolower($eventIdParam);
                    
                    while ($evRow = $allEventsResult->fetch_assoc()) {
                        $titleLower = strtolower($evRow['title']);
                        
                        if (strpos($titleLower, $searchTermLower) !== false) {
                            $eventId = (int)$evRow['id'];
                            error_log("DEBUG: Found event via PHP strpos fuzzy match: $eventId");
                            break;
                        }
                    }
                }
            }
            
            // Approach 5: Last attempt - return first available exhibition
            if ($eventId === null) {
                error_log("DEBUG: No fuzzy match found for '$eventIdParam', trying fallback - first available");
                
                $fallbackQuery = "SELECT id FROM exhibitions ORDER BY id DESC LIMIT 1";
                $fallbackResult = $db->getConnection()->query($fallbackQuery);
                
                if ($fallbackResult && $fallbackResult->num_rows > 0) {
                    $fallbackRow = $fallbackResult->fetch_assoc();
                    $eventId = (int)$fallbackRow['id'];
                    error_log("DEBUG: Using fallback - returning most recent exhibition: $eventId");
                } else {
                    // If no exhibitions at all, try events table
                    $fallbackEventsQuery = "SELECT id FROM events ORDER BY id DESC LIMIT 1";
                    $fallbackEventsResult = $db->getConnection()->query($fallbackEventsQuery);
                    
                    if ($fallbackEventsResult && $fallbackEventsResult->num_rows > 0) {
                        $fallbackEventsRow = $fallbackEventsResult->fetch_assoc();
                        $eventId = (int)$fallbackEventsRow['id'];
                        error_log("DEBUG: Using fallback - returning most recent event: $eventId");
                    }
                }
            }
            
            // If still not found, return error
            if ($eventId === null) {
                throw new Exception("Event/Exhibition not found with slug: $eventIdParam (tried: exact match, SQL case-insensitive LIKE, PHP strpos, fallback)");
            }
        }
    }
    
    // Get event details with translation support
    // Query without translations table (fallback - using bilingual columns)
    // Note: events table has video_url, exhibitions table has event_video
    // We return BOTH fields for compatibility
    
    // FIRST: Check exhibitions table (for newly added exhibitions)
    $event = null;
    
    if ($isNumeric) {
        // Try exhibitions table FIRST for numeric IDs
        error_log("DEBUG: Checking exhibitions table first for ID $eventId");
        
        // Check if exhibitions table exists first
        $tableCheckQuery = "SHOW TABLES LIKE 'exhibitions'";
        $tableResult = $db->getConnection()->query($tableCheckQuery);
        
        if ($tableResult && $tableResult->num_rows > 0) {
            $exhibitionQuery = '
                SELECT 
                    ex.id,
                    ex.exhibition_date as event_date,
                    ex.exhibition_time as event_time,
                    ex.exhibition_end_time as event_end_time,
                    ex.end_date,
                    ex.cover_image,
                    ex.event_video as video_url,
                    ex.event_video,
                    COALESCE(ex.gallery_images, "") as gallery_images,
                    "exhibition" as category,
                    COALESCE(ex.title_en, "") as title,
                    COALESCE(ex.description_en, "") as description,
                    COALESCE(ex.location_en, "") as location,
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
            
            error_log("DEBUG: Querying exhibitions table for ID: $eventId");
            
            $exhibitionStmt = $db->prepare($exhibitionQuery);
            if ($exhibitionStmt) {
                $exhibitionStmt->bind_param('i', $eventId);
                if ($exhibitionStmt->execute()) {
                    $exhibitionResult = $exhibitionStmt->get_result();
                    $event = $exhibitionResult->fetch_assoc();
                    
                    if ($event) {
                        error_log("DEBUG: FOUND in exhibitions table with ID: $eventId");
                        error_log("DEBUG: Raw event_video from DB: " . var_export($event['event_video'], true));
                    }
                } else {
                    error_log("DEBUG: Exhibition query execute failed: " . $exhibitionStmt->error);
                }
            } else {
                error_log("DEBUG: Exhibition query prepare failed");
            }
        } else {
            error_log("DEBUG: Exhibitions table does not exist");
        }
    }
    
    // If not found in exhibitions, check events table
    if (!$event) {
        error_log("DEBUG: ID $eventId not found in exhibitions, checking events table");
        
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
        
        if ($event) {
            error_log("DEBUG: FOUND in events table with ID: $eventId");
        }
    }
    
    // Try slug-based lookup if numeric lookup failed and it's not numeric
    if (!$event && !$isNumeric) {
        error_log("DEBUG: Trying slug-based lookup for: $eventIdParam");
        
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
                        
                        // Now fetch the full event data
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
                        $eventStmt = $db->prepare($eventQuery);
                        if ($eventStmt) {
                            $eventStmt->bind_param('i', $eventId);
                            if ($eventStmt->execute()) {
                                $event = $eventStmt->get_result()->fetch_assoc();
                            }
                        }
                    }
                }
            }
        }
    }
    
    if (!$event) {
        error_log("DEBUG: Event not found with ID: $eventId - Throwing exception");
        throw new Exception('Event/Exhibition not found with ID: ' . $eventId);
    }
    
    // Log what we found
    error_log("DEBUG: Successfully loaded event: ID=$eventId, Title=" . $event['title']);
    
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
    
    // DEBUG: Log the video value being returned
    error_log("DEBUG: RETURNING event_video: " . var_export($event['event_video'], true));
    error_log("DEBUG: RETURNING video_url: " . var_export($event['video_url'], true));
    error_log("DEBUG: Full event object keys: " . implode(', ', array_keys($event)));
    
    // Ensure NULL values are properly encoded as null (not empty string or 'null' string)
    if ($event['event_video'] === null) {
        error_log("DEBUG: event_video is NULL - will be encoded as null");
    } elseif ($event['event_video'] === '') {
        error_log("DEBUG: event_video is EMPTY_STRING");
    } else {
        error_log("DEBUG: event_video is: " . $event['event_video']);
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



