<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Delete Event API
 */

// Set headers first
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log request details
error_log('=== DELETE EVENT REQUEST ===');
error_log('Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));
error_log('Content-Length: ' . ($_SERVER['CONTENT_LENGTH'] ?? 'Not set'));

try {
    // Get raw input
    $rawInput = file_get_contents('php://input');
    error_log('Raw input length: ' . strlen($rawInput));
    error_log('Raw input: ' . $rawInput);
    
    // Decode JSON
    $data = json_decode($rawInput, true);
    error_log('Decoded data: ' . json_encode($data));
    error_log('JSON error: ' . json_last_error_msg());
    
    // Validate data
    if ($data === null) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    if (!is_array($data)) {
        throw new Exception('Data is not an array');
    }
    
    if (!isset($data['id'])) {
        error_log('Missing id key. Available keys: ' . implode(', ', array_keys($data)));
        throw new Exception('Missing event ID');
    }
    
    $event_id = (int)$data['id'];
    error_log('Event ID: ' . $event_id);
    
    if ($event_id <= 0) {
        throw new Exception('Invalid event ID: ' . $event_id);
    }
    
    // Get database connection
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Delete gallery images first
    $query = "DELETE FROM event_gallery WHERE event_id = $event_id";
    error_log('Gallery delete query: ' . $query);
    if (!$conn->query($query)) {
        error_log('Gallery delete failed: ' . $conn->error);
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete event
    $query = "DELETE FROM events WHERE id = $event_id";
    error_log('Event delete query: ' . $query);
    if (!$conn->query($query)) {
        error_log('Event delete failed: ' . $conn->error);
        throw new Exception('Delete event failed: ' . $conn->error);
    }
    
    $affectedRows = $conn->affected_rows;
    error_log('Affected rows: ' . $affectedRows);
    
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

