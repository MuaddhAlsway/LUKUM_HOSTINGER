<?php
/**
 * Direct test of blogs API
 */

require_once __DIR__ . '/../config.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    echo json_encode(['step' => 'Starting test']);
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Simple query without prepared statement
    $result = $conn->query("SELECT * FROM blogs LIMIT 5");
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'count' => count($blogs),
        'data' => $blogs
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

