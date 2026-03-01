<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Delete Blog API
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    
    // Flexible ID extraction (like delete_event.php)
    $blog_id = null;
    if (isset($data['id'])) {
        $blog_id = (int)$data['id'];
    } elseif (isset($_POST['id'])) {
        $blog_id = (int)$_POST['id'];
    } elseif (isset($_GET['id'])) {
        $blog_id = (int)$_GET['id'];
    }
    
    // Validate ID
    if (!$blog_id || $blog_id <= 0) {
        throw new Exception('Missing or invalid blog ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed in delete_blog.php');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
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

