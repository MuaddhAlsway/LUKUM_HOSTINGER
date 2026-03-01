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

try {
    // Get raw input
    $rawInput = file_get_contents('php://input');
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
    
    // Check for ID in multiple ways
    $event_id = null;
    
    if (isset($data['id'])) {
        $event_id = (int)$data['id'];
        error_log('Found ID in JSON: ' . $event_id);
    } elseif (isset($_POST['id'])) {
        $event_id = (int)$_POST['id'];
        error_log('Found ID in POST: ' . $event_id);
    } elseif (isset($_GET['id'])) {
        $event_id = (int)$_GET['id'];
        error_log('Found ID in GET: ' . $event_id);
    }
    
    error_log('Final event_id: ' . $event_id);
    
    if (!$event_id || $event_id <= 0) {
        throw new Exception('Missing or invalid event ID');
    }
    
    // Get database connection
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Delete gallery images first
    $query = "DELETE FROM event_gallery WHERE event_id = $event_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete gallery failed: ' . $conn->error);
    }
    
    // Delete event
    $query = "DELETE FROM events WHERE id = $event_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete event failed: ' . $conn->error);
    }
    
    $affectedRows = $conn->affected_rows;
    
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

