<?php
/**
 * LAKUM Artspace - Delete Pricing API
 * Deletes a pricing option from the database
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
    $pricing_id = null;
    if (isset($data['id'])) {
        $pricing_id = (int)$data['id'];
    } elseif (isset($_POST['id'])) {
        $pricing_id = (int)$_POST['id'];
    } elseif (isset($_GET['id'])) {
        $pricing_id = (int)$_GET['id'];
    }
    
    // Validate ID
    if (!$pricing_id || $pricing_id <= 0) {
        throw new Exception('Missing or invalid pricing ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    // Delete pricing translations first (if they exist)
    // Check if table exists first to avoid error messages
    $tableCheckQuery = "SHOW TABLES LIKE 'pricing_translations'";
    $tableExists = $conn->query($tableCheckQuery) && $conn->query($tableCheckQuery)->num_rows > 0;
    
    if ($tableExists) {
        $query = "DELETE FROM pricing_translations WHERE pricing_id = $pricing_id";
        if (!$conn->query($query)) {
            error_log('Pricing translations delete note: ' . $conn->error);
        }
    }
    
    // Delete pricing
    $query = "DELETE FROM pricing WHERE id = $pricing_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete failed: ' . $conn->error);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pricing deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete Pricing Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
