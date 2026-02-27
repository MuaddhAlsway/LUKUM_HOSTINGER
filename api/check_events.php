<?php
/**
 * Check Events - Diagnostic API
 * Lists all events in database with their IDs
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Get all events
    $query = '
        SELECT 
            e.id,
            e.title,
            e.event_date,
            t_en.title as title_en,
            t_ar.title as title_ar
        FROM events e
        LEFT JOIN event_translations t_en ON e.id = t_en.event_id AND t_en.language = "en"
        LEFT JOIN event_translations t_ar ON e.id = t_ar.event_id AND t_ar.language = "ar"
        ORDER BY e.id ASC
    ';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $events = [];
    
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'total' => count($events),
        'events' => $events
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
