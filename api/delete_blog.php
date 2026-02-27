<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Delete Blog API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['id'])) {
        throw new Exception('Missing blog ID');
    }
    
    $blog_id = (int)$data['id'];
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed in delete_blog.php');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Delete blog translations first (due to foreign key)
    $query = "DELETE FROM blog_translations WHERE blog_id = $blog_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete translations failed: ' . $conn->error);
    }
    
    // Delete blog
    $query = "DELETE FROM blogs WHERE id = $blog_id";
    if (!$conn->query($query)) {
        throw new Exception('Delete blog failed: ' . $conn->error);
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Blog deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete Blog Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

