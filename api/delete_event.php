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
    error_log('Data type: ' . gettype($data));
    
    // Validate data
    if ($data === null) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    if (!is_array($data)) {
        throw new Exception('Data is not an array, got: ' . gettype($data));
    }
    
    // Check for ID in multiple ways
    $event_id = null;
    
    if (isset($data['id']) && $data['id'] !== null) {
        $event_id = (int)$data['id'];
        error_log('Found ID in JSON: ' . $event_id . ' (raw: ' . var_export($data['id'], true) . ')');
    } elseif (isset($_POST['id'])) {
        $event_id = (int)$_POST['id'];
        error_log('Found ID in POST: ' . $event_id);
    } elseif (isset($_GET['id'])) {
        $event_id = (int)$_GET['id'];
        error_log('Found ID in GET: ' . $event_id);
    }
    
    error_log('Final event_id: ' . $event_id . ' (type: ' . gettype($event_id) . ')');
    
    if (!$event_id || $event_id <= 0) {
        error_log('ID validation failed: event_id=' . var_export($event_id, true));
        throw new Exception('Missing or invalid event ID');
    }
    
    // Get database connection
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    // Use prepared statements for safety
    // Delete gallery images first
    $gallery_query = "DELETE FROM event_gallery WHERE event_id = ?";
    $gallery_stmt = $conn->prepare($gallery_query);
    if (!$gallery_stmt) {
        throw new Exception('Prepare gallery delete failed: ' . $conn->error);
    }
    $gallery_stmt->bind_param('i', $event_id);
    if (!$gallery_stmt->execute()) {
        throw new Exception('Delete gallery failed: ' . $gallery_stmt->error);
    }
    $gallery_stmt->close();
    
    // Delete event
    $event_query = "DELETE FROM events WHERE id = ?";
    $event_stmt = $conn->prepare($event_query);
    if (!$event_stmt) {
        throw new Exception('Prepare event delete failed: ' . $conn->error);
    }
    $event_stmt->bind_param('i', $event_id);
    if (!$event_stmt->execute()) {
        throw new Exception('Delete event failed: ' . $event_stmt->error);
    }
    $event_stmt->close();
    
    $affectedRows = $event_stmt->affected_rows;
    
    if ($affectedRows === 0) {
        throw new Exception('Event not found or already deleted');
    }
    
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



