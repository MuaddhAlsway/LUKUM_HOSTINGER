<?php
/**
 * LAKUM Artspace - Delete Press API
 * Deletes a press release from the database
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
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    
    // Flexible ID extraction
    $press_id = null;
    if (isset($data['id'])) {
        $press_id = (int)$data['id'];
    } elseif (isset($_POST['id'])) {
        $press_id = (int)$_POST['id'];
    } elseif (isset($_GET['id'])) {
        $press_id = (int)$_GET['id'];
    }
    
    // Validate ID
    if (!$press_id || $press_id <= 0) {
        throw new Exception('Missing or invalid press ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    // Delete press translations first (if they exist)
    // Check if table exists first to avoid error messages
    $tableCheckQuery = "SHOW TABLES LIKE 'press_translations'";
    $tableExists = $conn->query($tableCheckQuery) && $conn->query($tableCheckQuery)->num_rows > 0;
    
    if ($tableExists) {
        $query = "DELETE FROM press_translations WHERE press_id = $press_id";
        if (!$conn->query($query)) {
            error_log('Press translations delete note: ' . $conn->error);
        }
    }
    
    // Delete press
    $query = "DELETE FROM press WHERE id = $press_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete failed: ' . $conn->error);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Press release deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete Press Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

