<?php
/**
 * Mark message as read
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Message ID is required']);
        exit;
    }
    
    $id = (int)$input['id'];
    
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    $conn->set_charset('utf8mb4');
    
    $query = 'UPDATE contact_messages SET status = ? WHERE id = ? AND status = ?';
    $stmt = $conn->prepare($query);
    
    $status_new = 'new';
    $status_read = 'read';
    
    $stmt->bind_param('sis', $status_read, $id, $status_new);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>



