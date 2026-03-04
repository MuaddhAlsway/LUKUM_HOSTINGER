<?php
/**
 * Get all contact messages
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'contact_messages'");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Messages table not found']);
        exit;
    }
    
    // Get all messages ordered by date
    $query = 'SELECT * FROM contact_messages ORDER BY created_at DESC';
    $result = $conn->query($query);
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>




