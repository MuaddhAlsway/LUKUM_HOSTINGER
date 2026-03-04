<?php
/**
 * LAKUM Artspace - Create Blog API
 * Creates a new blog post in the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required = ['title', 'content', 'author', 'category'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $db->prepare('INSERT INTO blogs (title, content, excerpt, author, cover_image, category, is_published) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $title = $data['title'];
    $content = $data['content'];
    $excerpt = $data['excerpt'] ?? substr($content, 0, 200);
    $author = $data['author'];
    $cover_image = $data['cover_image'] ?? 'assest/img-4.png';
    $category = $data['category'];
    $is_published = $data['is_published'] ?? 1;
    
    $stmt->bind_param('ssssssi', $title, $content, $excerpt, $author, $cover_image, $category, $is_published);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $blog_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog created successfully',
        'data' => ['id' => $blog_id]
    ]);
    
} catch (Exception $e) {
    error_log('Create Blog Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

