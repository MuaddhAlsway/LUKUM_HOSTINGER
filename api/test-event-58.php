<?php
/**
 * Test Event 58 - Check what's in the database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $eventId = 58;
    
    // Get base event
    $eventQuery = 'SELECT * FROM events WHERE id = ?';
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->bind_param('i', $eventId);
    $eventStmt->execute();
    $eventResult = $eventStmt->get_result();
    $event = $eventResult->fetch_assoc();
    
    // Get all translations
    $transQuery = 'SELECT * FROM event_translations WHERE event_id = ? ORDER BY language';
    $transStmt = $db->prepare($transQuery);
    $transStmt->bind_param('i', $eventId);
    $transStmt->execute();
    $transResult = $transStmt->get_result();
    $translations = [];
    while ($row = $transResult->fetch_assoc()) {
        $translations[] = $row;
    }
    
    // Get with language parameter (like the API does)
    $langQuery = '
        SELECT 
            e.id,
            e.title as base_title,
            COALESCE(t_ar.title, t_en.title, e.title) as title_ar,
            COALESCE(t_ar.description, t_en.description, e.description) as description_ar,
            COALESCE(t_ar.location, t_en.location, e.location) as location_ar,
            t_ar.title as raw_title_ar,
            t_en.title as raw_title_en
        FROM events e
        LEFT JOIN event_translations t_ar ON e.id = t_ar.event_id AND t_ar.language = "ar"
        LEFT JOIN event_translations t_en ON e.id = t_en.event_id AND t_en.language = "en"
        WHERE e.id = ?
    ';
    $langStmt = $db->prepare($langQuery);
    $langStmt->bind_param('i', $eventId);
    $langStmt->execute();
    $langResult = $langStmt->get_result();
    $langData = $langResult->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'event_id' => $eventId,
        'base_event' => $event,
        'all_translations' => $translations,
        'translation_count' => count($translations),
        'language_query_result' => $langData,
        'database_status' => 'OK'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_trace' => $e->getTraceAsString()
    ]);
}
?>

