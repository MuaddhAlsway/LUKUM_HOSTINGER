<?php
/**
 * Check what data exists - events vs exhibitions
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Check events table
    $eventsResult = $db->getConnection()->query("SELECT id, title, category FROM events ORDER BY id DESC LIMIT 10");
    $events = [];
    if ($eventsResult) {
        while ($row = $eventsResult->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    // Check exhibitions table
    $exhibitionsResult = $db->getConnection()->query("SELECT id, title_en FROM exhibitions ORDER BY id DESC LIMIT 10");
    $exhibitions = [];
    if ($exhibitionsResult) {
        while ($row = $exhibitionsResult->fetch_assoc()) {
            $exhibitions[] = $row;
        }
    }
    
    // Count totals
    $eventCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
    $exhibitionCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM exhibitions")->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'events' => [
                'total_count' => $eventCount,
                'sample' => $events
            ],
            'exhibitions' => [
                'total_count' => $exhibitionCount,
                'sample' => $exhibitions
            ]
        ],
        'diagnosis' => [
            'exhibitions_empty' => $exhibitionCount === 0,
            'events_exist' => $eventCount > 0,
            'next_action' => $exhibitionCount === 0 ? 'Need to populate exhibitions table or migrate events to exhibitions' : 'Exhibitions exist'
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
