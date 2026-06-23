<?php
/**
 * Check which table contains event ID 76
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $eventId = 76;
    
    $results = [
        'event_id' => $eventId,
        'timestamp' => date('Y-m-d H:i:s'),
        'checks' => []
    ];
    
    // Check in events table
    $eventsQuery = "SELECT id, title, video_url, category FROM events WHERE id = ? LIMIT 1";
    $stmt1 = $db->prepare($eventsQuery);
    if ($stmt1) {
        $stmt1->bind_param('i', $eventId);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        if ($row = $result1->fetch_assoc()) {
            $results['checks'][] = [
                'table' => 'events',
                'found' => true,
                'data' => $row
            ];
        } else {
            $results['checks'][] = [
                'table' => 'events',
                'found' => false
            ];
        }
    }
    
    // Check in exhibitions table
    $exhibitionsQuery = "SELECT id, title_en, event_video, category FROM exhibitions WHERE id = ? LIMIT 1";
    $stmt2 = $db->prepare($exhibitionsQuery);
    if ($stmt2) {
        $stmt2->bind_param('i', $eventId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($row = $result2->fetch_assoc()) {
            $results['checks'][] = [
                'table' => 'exhibitions',
                'found' => true,
                'data' => $row
            ];
        } else {
            $results['checks'][] = [
                'table' => 'exhibitions',
                'found' => false
            ];
        }
    }
    
    // Count total records in both tables
    $eventsCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
    $exhibitionsCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM exhibitions")->fetch_assoc()['count'];
    
    $results['table_counts'] = [
        'events' => $eventsCount,
        'exhibitions' => $exhibitionsCount
    ];
    
    // Show schema of both tables
    $eventsSchema = [];
    $scheResult = $db->getConnection()->query("SHOW COLUMNS FROM events");
    while ($col = $scheResult->fetch_assoc()) {
        $eventsSchema[] = $col['Field'] . ' (' . $col['Type'] . ')';
    }
    
    $exhibitionsSchema = [];
    $scheResult2 = $db->getConnection()->query("SHOW COLUMNS FROM exhibitions");
    while ($col = $scheResult2->fetch_assoc()) {
        $exhibitionsSchema[] = $col['Field'] . ' (' . $col['Type'] . ')';
    }
    
    $results['schemas'] = [
        'events_columns' => $eventsSchema,
        'exhibitions_columns' => $exhibitionsSchema
    ];
    
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
