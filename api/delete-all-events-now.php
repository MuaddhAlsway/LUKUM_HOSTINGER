<?php
/**
 * Delete all events immediately
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Delete all event gallery images first
    $result1 = $conn->query("DELETE FROM event_gallery");
    if (!$result1) {
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete all events
    $result2 = $conn->query("DELETE FROM events");
    if (!$result2) {
        throw new Exception('Delete events failed: ' . $conn->error);
    }
    
    // Verify deletion
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    $remaining = $row['count'];
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'All events deleted successfully',
        'remaining_events' => $remaining
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
