<?php
/**
 * LAKUM Artspace - Get Event Details API
 * Retrieves single event with gallery images and bilingual translations from database
 * Supports both numeric ID and slug-based lookups
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: -1');
header('Date: ' . date('r'));
header('X-Robots-Tag: noindex, nofollow');

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
        
        // Try to extract numeric ID from slug (e.g., "ex-3-ampm" -> 3)
        if (preg_match('/ex-(\d+)-/', $slugParam, $matches)) {
            $potentialId = (int)$matches[1];
            error_log("DEBUG: Extracted potential ID from slug: $potentialId");
            
            // Check if this ID exists in exhibitions table
            $checkExhibition = "SELECT id FROM exhibitions WHERE id = ? LIMIT 1";
            $checkStmt = $db->prepare($checkExhibition);
            if ($checkStmt) {
                $checkStmt->bind_param('i', $potentialId);
                if ($checkStmt->execute()) {
                    $checkResult = $checkStmt->get_result();
                    if ($checkResult->num_rows > 0) {
                        $eventId = $potentialId;
                        error_log("DEBUG: ID $eventId found in exhibitions table");
                    } else {
                        error_log("DEBUG: ID $potentialId NOT found in exhibitions table");
                    }
                }
                $checkStmt->close();
            }
        }
        
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
            // Try direct slug match on exhibition title
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
                $exhibitionStmt->close();
            }
        }
        
        // If still not found, try partial title match
        if ($eventId === null) {
            error_log("DEBUG: Slug match failed, trying partial title match for: $eventIdParam");
            $partialQuery = "SELECT id FROM exhibitions WHERE LOWER(title_en) LIKE LOWER(?) LIMIT 1";
            $partialStmt = $db->prepare($partialQuery);
            if ($partialStmt) {
                $searchTerm = "%" . $eventIdParam . "%";
                $partialStmt->bind_param('s', $searchTerm);
                if ($partialStmt->execute()) {
                    $partialResult = $partialStmt->get_result();
                    if ($partialResult->num_rows > 0) {
                        $partialRow = $partialResult->fetch_assoc();
                        $eventId = (int)$partialRow['id'];
                        error_log("DEBUG: Found exhibition by partial title match: ID $eventId for search: $eventIdParam");
                    }
                }
                $partialStmt->close();
            }
        }
        
        // If still not found, try partial title search (extract title from slug like "ampm" from "ex-3-ampm")
        if ($eventId === null && strpos($slugParam, '-') !== false) {
            $slugParts = explode('-', $slugParam);
            if (count($slugParts) >= 2) {
                // Get last part as potential title
                $titlePart = end($slugParts);
                error_log("DEBUG: Extracted title part from slug: '$titlePart'");
                
                $titlePartQuery = "SELECT id FROM exhibitions WHERE LOWER(title_en) LIKE LOWER(?) LIMIT 1";
                $titlePartStmt = $db->prepare($titlePartQuery);
                if ($titlePartStmt) {
                    $titleSearch = "%" . $titlePart . "%";
                    $titlePartStmt->bind_param('s', $titleSearch);
                    if ($titlePartStmt->execute()) {
                        $titlePartResult = $titlePartStmt->get_result();
                        if ($titlePartResult->num_rows > 0) {
                            $titlePartRow = $titlePartResult->fetch_assoc();
                            $eventId = (int)$titlePartRow['id'];
                            error_log("DEBUG: Found exhibition by title part match: ID $eventId for search: $titlePart");
                        }
                    }
                    $titlePartStmt->close();
                }
            }
        }
    }
    
    // If no event found by slug, try to find ANY exhibition as last resort
    if ($eventId === null) {
        error_log("DEBUG: No event/exhibition found, checking if ANY exhibitions exist as fallback");
        
        $fallbackQuery = "SELECT id FROM exhibitions ORDER BY id ASC LIMIT 1";
        $fallbackResult = $db->getConnection()->query($fallbackQuery);
        
        if ($fallbackResult && $fallbackResult->num_rows > 0) {
            $fallbackRow = $fallbackResult->fetch_assoc();
            $eventId = (int)$fallbackRow['id'];
            error_log("DEBUG: No match found, using first available exhibition as fallback: ID $eventId");
        } else {
            error_log("DEBUG: No exhibitions exist in database at all");
            throw new Exception('No exhibitions found in database');
        }
    }
    
    // Get event details with translation support
    
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



