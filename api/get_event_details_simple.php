<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Get Event Details API (Simple)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $event_id = (int)($_GET['id'] ?? 0);
    $lang = $_GET['lang'] ?? 'en';
    
    if (!$event_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Event ID is required']);
        exit;
    }
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    // Check if event_translations table exists
    $tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
    $tableCheckResult = $conn->query($tableCheckQuery);
    $translationsTableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;
    
    // Get event with translations if table exists
    if ($translationsTableExists) {
        $query = "
            SELECT 
                e.*,
                COALESCE(et_en.title, e.title) as title_en,
                COALESCE(et_en.description, e.description) as description_en,
                COALESCE(et_en.location, e.location) as location_en,
                COALESCE(et_ar.title, '') as title_ar,
                COALESCE(et_ar.description, '') as description_ar,
                COALESCE(et_ar.location, '') as location_ar
            FROM events e
            LEFT JOIN event_translations et_en ON e.id = et_en.event_id AND et_en.language = 'en'
            LEFT JOIN event_translations et_ar ON e.id = et_ar.event_id AND et_ar.language = 'ar'
            WHERE e.id = $event_id
        ";
    } else {
        // Fallback: just get from events table
        $query = "SELECT * FROM events WHERE id = $event_id";
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Event not found']);
        exit;
    }
    
    $event = $result->fetch_assoc();
    
    // Debug: log what we got
    error_log('EVENT_DETAILS: ' . json_encode([
        'event_id' => $event_id,
        'has_title_ar' => !empty($event['title_ar']),
        'has_description_ar' => !empty($event['description_ar']),
        'has_location_ar' => !empty($event['location_ar']),
        'translations_table_exists' => $translationsTableExists
    ]));
    
    // Get gallery images
    $galleryQuery = "SELECT * FROM event_gallery WHERE event_id = $event_id ORDER BY display_order";
    $galleryResult = $conn->query($galleryQuery);
    
    $gallery = [];
    if ($galleryResult) {
        while ($row = $galleryResult->fetch_assoc()) {
            $gallery[] = $row;
        }
    }
    
    error_log('GALLERY_COUNT: ' . count($gallery));
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $event,
        'gallery' => $gallery,
        'translations_table_exists' => $translationsTableExists
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>



