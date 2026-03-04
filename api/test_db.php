<?php
/**
 * Database Connection Test
 * Verifies that the singleton pattern works correctly
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed',
            'details' => 'Could not connect to ' . DB_HOST
        ]);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Test simple query
    $result = $conn->query("SELECT 1 as test");
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'SUCCESS - CONNECTED',
        'database' => DB_NAME,
        'host' => DB_HOST,
        'user' => DB_USER
    ]);
    
} catch (Exception $e) {
    error_log('Test DB Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

