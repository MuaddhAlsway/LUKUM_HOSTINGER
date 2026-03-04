<?php
/**
 * LAKUM Artspace - Delete All Events API
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
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


