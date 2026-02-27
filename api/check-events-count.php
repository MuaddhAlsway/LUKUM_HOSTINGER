<?php
/**
 * Check how many events exist in database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Count events
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    $event_count = $row['count'];
    
    // Count gallery
    $result = $conn->query("SELECT COUNT(*) as count FROM event_gallery");
    $row = $result->fetch_assoc();
    $gallery_count = $row['count'];
    
    // Get all events
    $result = $conn->query("SELECT id, title FROM events LIMIT 10");
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'event_count' => $event_count,
        'gallery_count' => $gallery_count,
        'events' => $events
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
