<?php
/**
 * LAKUM Artspace - Create Press API
 * Creates a new press release in the database
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
    $required = ['title', 'content', 'category'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $db->prepare('INSERT INTO press (title, content, excerpt, source, cover_image, press_date, category, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $title = $data['title'];
    $content = $data['content'];
    $excerpt = $data['excerpt'] ?? substr($content, 0, 200);
    $source = $data['source'] ?? 'LAKUM Press';
    $cover_image = $data['cover_image'] ?? 'assest/img-4.png';
    $press_date = $data['press_date'] ?? date('Y-m-d');
    $category = $data['category'];
    $is_published = $data['is_published'] ?? 1;
    
    $stmt->bind_param('sssssssi', $title, $content, $excerpt, $source, $cover_image, $press_date, $category, $is_published);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $press_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Press release created successfully',
        'data' => ['id' => $press_id]
    ]);
    
} catch (Exception $e) {
    error_log('Create Press Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
