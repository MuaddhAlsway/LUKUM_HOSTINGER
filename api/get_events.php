<?php
/**
 * LAKUM Artspace - Get Events API (Fixed - Hybrid Columns)
 * Retrieves events from database with bilingual support
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    $type = $_GET['type'] ?? 'all';
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $today = date('Y-m-d');
    
    error_log('=== GET EVENTS API ===');
    error_log('Type: ' . $type);
    error_log('Today: ' . $today);
    error_log('Language: ' . $lang);
    
    // Check if fetching a single event by slug or ID
    if (isset($_GET['slug']) || isset($_GET['id'])) {
        $event_slug = $_GET['slug'] ?? null;
        $event_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        error_log('Fetching single event - Slug: ' . ($event_slug ?? 'null') . ', ID: ' . ($event_id ?? 'null'));
        
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
                e.slug,
                CASE 
                    WHEN ? = "ar" AND e.title_ar IS NOT NULL AND e.title_ar != "" THEN e.title_ar
                    ELSE COALESCE(e.title_en, e.title)
                END as title,
                CASE 
                    WHEN ? = "ar" AND e.description_ar IS NOT NULL AND e.description_ar != "" THEN e.description_ar
                    ELSE COALESCE(e.description_en, e.description)
                END as description,
                CASE 
                    WHEN ? = "ar" AND e.location_ar IS NOT NULL AND e.location_ar != "" THEN e.location_ar
                    ELSE COALESCE(e.location_en, e.location)
                END as location,
                e.title_en,
                e.description_en,
                e.location_en,
                e.title_ar,
                e.description_ar,
                e.location_ar
            FROM events e
            WHERE ';
        
        if ($event_slug !== null) {
            $query .= 'e.slug = ?';
            $stmt = $db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $db->getConnection()->error);
            }
            $stmt->bind_param('ssss', $lang, $lang, $lang, $event_slug);
        } else {
            $query .= 'e.id = ?';
            $stmt = $db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $db->getConnection()->error);
            }
            $stmt->bind_param('sssi', $lang, $lang, $lang, $event_id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Event not found']);
            exit;
        }
        
        $event = $result->fetch_assoc();
        
        // Add computed fields for frontend
        $eventDate = new DateTime($event['event_date']);
        $event['day'] = $eventDate->format('d');
        $event['month'] = $eventDate->format('F');
        $event['month_short'] = $eventDate->format('M');
        $event['year'] = $eventDate->format('Y');
        
        // Fetch gallery images for this event
        $galleryQuery = "SELECT * FROM event_gallery WHERE event_id = ? ORDER BY display_order";
        $galleryStmt = $db->prepare($galleryQuery);
        if ($galleryStmt) {
            $galleryStmt->bind_param('i', $event['id']);
            $galleryStmt->execute();
            $galleryResult = $galleryStmt->get_result();
            
            $gallery = [];
            while ($row = $galleryResult->fetch_assoc()) {
                $gallery[] = $row;
            }
            $galleryStmt->close();
        } else {
            $gallery = [];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $event,
            'gallery' => $gallery,
            'language' => $lang
        ]);
        exit;
    }
    
    // Query to get events list with bilingual support (hybrid columns)
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
            e.slug,
            CASE 
                WHEN ? = "ar" AND e.title_ar IS NOT NULL AND e.title_ar != "" THEN e.title_ar
                ELSE COALESCE(e.title_en, e.title)
            END as title,
            CASE 
                WHEN ? = "ar" AND e.description_ar IS NOT NULL AND e.description_ar != "" THEN e.description_ar
                ELSE COALESCE(e.description_en, e.description)
            END as description,
            CASE 
                WHEN ? = "ar" AND e.location_ar IS NOT NULL AND e.location_ar != "" THEN e.location_ar
                ELSE COALESCE(e.location_en, e.location)
            END as location,
            e.title_en,
            e.description_en,
            e.location_en,
            e.title_ar,
            e.description_ar,
            e.location_ar
        FROM events e
        WHERE 1=1
    ';
    
    $bindTypes = '';
    $bindParams = [];
    
    // Filter by type based on event_date
    if ($type === 'upcoming') {
        $query .= ' AND e.event_date >= ?';
        $bindTypes = 's';
        $bindParams[] = &$today;
    } elseif ($type === 'past') {
        $query .= ' AND e.event_date < ?';
        $bindTypes = 's';
        $bindParams[] = &$today;
    }
    
    // Sort order
    if ($type === 'past') {
        $query .= ' ORDER BY e.event_date DESC';
    } else {
        $query .= ' ORDER BY e.event_date ASC';
    }
    
    $query .= ' LIMIT ? OFFSET ?';
    $bindTypes .= 'ii';
    $bindParams[] = &$limit;
    $bindParams[] = &$offset;
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    // Bind language parameters first
    $langParam1 = $lang;
    $langParam2 = $lang;
    $langParam3 = $lang;
    
    $allBindTypes = 'sss' . $bindTypes;
    $allBindParams = [&$langParam1, &$langParam2, &$langParam3];
    $allBindParams = array_merge($allBindParams, $bindParams);
    
    call_user_func_array([$stmt, 'bind_param'], array_merge([$allBindTypes], $allBindParams));
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $events = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add computed fields for frontend
        $eventDate = new DateTime($row['event_date']);
        $row['day'] = $eventDate->format('d');
        $row['month'] = $eventDate->format('F');
        $row['month_short'] = $eventDate->format('M');
        $row['year'] = $eventDate->format('Y');
        
        $events[] = $row;
    }
    
    error_log('Events found: ' . count($events));
    
    echo json_encode([
        'success' => true,
        'data' => $events,
        'type' => $type,
        'language' => $lang,
        'count' => count($events)
    ]);
    
} catch (Exception $e) {
    error_log('Events API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>


