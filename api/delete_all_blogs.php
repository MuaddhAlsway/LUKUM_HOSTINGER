<?php
/**
 * LAKUM Artspace - Delete All Blogs API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Delete all blog translations first (due to foreign key)
    $query = "DELETE FROM blog_translations";
    if (!$conn->query($query)) {
        throw new Exception('Delete translations failed: ' . $conn->error);
    }
    
    // Delete all blogs
    $query = "DELETE FROM blogs";
    if (!$conn->query($query)) {
        throw new Exception('Delete blogs failed: ' . $conn->error);
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'All blogs deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete All Blogs Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
