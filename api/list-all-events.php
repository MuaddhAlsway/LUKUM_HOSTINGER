<?php
/**
 * List all events in database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Get all events
    $result = $conn->query("SELECT id, title FROM events ORDER BY id");
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'total_events' => count($events),
        'events' => $events
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>


