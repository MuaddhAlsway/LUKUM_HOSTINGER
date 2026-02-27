<?php
/**
 * LAKUM Artspace - Delete Pricing API
 * Deletes a pricing option from the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['id'])) {
        throw new Exception('Missing pricing ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $id = $data['id'];
    
    $stmt = $db->prepare('DELETE FROM pricing WHERE id = ?');
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
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
