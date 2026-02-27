<?php
/**
 * LAKUM Artspace - Delete All Events API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Delete all event gallery images first (due to foreign key)
    $query = "DELETE FROM event_gallery";
    if (!$conn->query($query)) {
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete all events
    $query = "DELETE FROM events";
    if (!$conn->query($query)) {
        throw new Exception('Delete events failed: ' . $conn->error);
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'All events deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete All Events Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
