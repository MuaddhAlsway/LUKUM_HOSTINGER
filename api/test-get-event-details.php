<?php
/**
 * Test get event details API
 */

header('Content-Type: application/json');

try {
    $event_id = 1; // Test with event ID 1
    
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Get event
    $query = "SELECT * FROM events WHERE id = $event_id";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    if ($result->num_rows === 0) {
        throw new Exception('Event not found');
    }
    
    $event = $result->fetch_assoc();
    
    // Get gallery images
    $query = "SELECT * FROM event_gallery WHERE event_id = $event_id ORDER BY display_order";
    $result = $conn->query($query);
    
    $gallery = [];
    while ($row = $result->fetch_assoc()) {
        $gallery[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'event_id' => $event_id,
        'event' => $event,
        'gallery_count' => count($gallery),
        'gallery' => $gallery
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>

