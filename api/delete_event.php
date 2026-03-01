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
    // Get raw input
    $rawInput = file_get_contents('php://input');
    error_log('Delete Event - Raw input: ' . $rawInput);
    
    $data = json_decode($rawInput, true);
    error_log('Delete Event - Decoded data: ' . json_encode($data));
    
    if (!$data) {
        throw new Exception('Invalid JSON received');
    }
    
    if (!isset($data['id']) || empty($data['id'])) {
        error_log('Delete Event - Missing ID. Data: ' . json_encode($data));
        throw new Exception('Missing event ID');
    }
    
    $event_id = (int)$data['id'];
    error_log('Delete Event - Event ID: ' . $event_id);
    
    if ($event_id <= 0) {
        throw new Exception('Invalid event ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed in delete_event.php');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Delete gallery images first (foreign key constraint)
    $query = "DELETE FROM event_gallery WHERE event_id = $event_id";
    error_log('Delete Event - Gallery query: ' . $query);
    if (!$conn->query($query)) {
        error_log('Delete Event - Gallery delete failed: ' . $conn->error);
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete event
    $query = "DELETE FROM events WHERE id = $event_id";
    error_log('Delete Event - Event query: ' . $query);
    if (!$conn->query($query)) {
        error_log('Delete Event - Event delete failed: ' . $conn->error);
        throw new Exception('Delete event failed: ' . $conn->error);
    }
    
    $affectedRows = $conn->affected_rows;
    error_log('Delete Event - Affected rows: ' . $affectedRows);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event deleted successfully',
        'affected_rows' => $affectedRows
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

