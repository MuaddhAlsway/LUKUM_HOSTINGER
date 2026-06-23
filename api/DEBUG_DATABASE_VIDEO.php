<?php
/**
 * DEBUG SCRIPT - Check what's actually in the database
 * This shows:
 * 1. All exhibitions with their video URLs
 * 2. All events with their video URLs
 * 3. Confirms data is being saved
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // Database connection
    $db_host = 'localhost';
    $db_user = 'u812122863_neama';
    $db_pass = 'Nema202610!LakumDB';
    $db_name = 'u812122863_lakum_artspace';
    
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // ========== CHECK EXHIBITIONS TABLE ==========
    
    $exhibitionsResult = $conn->query(
        "SELECT id, title_en, exhibition_date, event_video, cover_image 
         FROM exhibitions 
         ORDER BY id DESC 
         LIMIT 10"
    );
    
    $exhibitions = [];
    if ($exhibitionsResult) {
        while ($row = $exhibitionsResult->fetch_assoc()) {
            $exhibitions[] = $row;
        }
    }
    
    // ========== CHECK EVENTS TABLE ==========
    
    $eventsResult = $conn->query(
        "SELECT id, title, event_date, video_url, cover_image 
         FROM events 
         ORDER BY id DESC 
         LIMIT 10"
    );
    
    $events = [];
    if ($eventsResult) {
        while ($row = $eventsResult->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    // ========== DETAILED CHECK FOR SPECIFIC ID ==========
    
    $specificCheckResult = $conn->query(
        "SELECT 
            'exhibitions' as table_name,
            id, title_en as title, exhibition_date as event_date, 
            event_video as video_url, cover_image
         FROM exhibitions
         UNION ALL
         SELECT 
            'events' as table_name,
            id, title, event_date, video_url, cover_image
         FROM events
         ORDER BY id DESC
         LIMIT 20"
    );
    
    $allRecords = [];
    if ($specificCheckResult) {
        while ($row = $specificCheckResult->fetch_assoc()) {
            $allRecords[] = $row;
        }
    }
    
    $conn->close();
    
    // ========== BUILD RESPONSE ==========
    
    $response = [
        'success' => true,
        'message' => 'Database query results',
        'exhibitions_count' => count($exhibitions),
        'events_count' => count($events),
        'exhibitions' => $exhibitions,
        'events' => $events,
        'all_records' => $allRecords,
        'analysis' => []
    ];
    
    // Analyze what we found
    $exhibitionsWithVideo = 0;
    $exhibitionsWithoutVideo = 0;
    
    foreach ($exhibitions as $ex) {
        if (!empty($ex['event_video'])) {
            $exhibitionsWithVideo++;
        } else {
            $exhibitionsWithoutVideo++;
        }
    }
    
    $eventsWithVideo = 0;
    $eventsWithoutVideo = 0;
    
    foreach ($events as $ev) {
        if (!empty($ev['video_url'])) {
            $eventsWithVideo++;
        } else {
            $eventsWithoutVideo++;
        }
    }
    
    $response['analysis'] = [
        'exhibitions' => [
            'total' => count($exhibitions),
            'with_video' => $exhibitionsWithVideo,
            'without_video' => $exhibitionsWithoutVideo,
            'status' => $exhibitionsWithVideo > 0 ? '✓ Videos are being saved!' : '✗ No videos in exhibitions'
        ],
        'events' => [
            'total' => count($events),
            'with_video' => $eventsWithVideo,
            'without_video' => $eventsWithoutVideo,
            'status' => $eventsWithVideo > 0 ? '✓ Videos are being saved!' : '✗ No videos in events'
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
