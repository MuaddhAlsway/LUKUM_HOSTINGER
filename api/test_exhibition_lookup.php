<?php
/**
 * TEST: Exhibition Lookup - Verify exhibitions are found correctly
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Get all exhibitions
    $exhibitionsQuery = "SELECT id, title_en, exhibition_date FROM exhibitions ORDER BY id DESC LIMIT 5";
    $exhibitionsResult = $db->getConnection()->query($exhibitionsQuery);
    $exhibitions = [];
    
    if ($exhibitionsResult) {
        while ($row = $exhibitionsResult->fetch_assoc()) {
            $exhibitions[] = $row;
        }
    }
    
    // Get all events
    $eventsQuery = "SELECT id, title, event_date FROM events ORDER BY id DESC LIMIT 5";
    $eventsResult = $db->getConnection()->query($eventsQuery);
    $events = [];
    
    if ($eventsResult) {
        while ($row = $eventsResult->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'exhibitions' => [
            'count' => count($exhibitions),
            'data' => $exhibitions
        ],
        'events' => [
            'count' => count($events),
            'data' => $events
        ],
        'message' => 'Use these IDs to test the API: GET /api/get_event_details.php?id=X'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
