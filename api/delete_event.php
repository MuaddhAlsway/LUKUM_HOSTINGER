<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Delete Event API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['id'])) {
        throw new Exception('Missing event ID');
    }
    
    $event_id = (int)$data['id'];
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed in delete_event.php');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Delete gallery images first (foreign key constraint)
    $query = "DELETE FROM event_gallery WHERE event_id = $event_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete event
    $query = "DELETE FROM events WHERE id = $event_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete event failed: ' . $conn->error);
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete Event Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

