<?php
/**
 * LAKUM Artspace - Get Blog Details API (Fixed)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $blog_id = (int)($_GET['id'] ?? 0);
    
    if (!$blog_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Blog ID is required']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Get blog data from blogs table with bilingual columns
    $query = "SELECT * FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $blog_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Blog not found']);
        exit;
    }
    
    $blog = $result->fetch_assoc();
    $stmt->close();
    
    // Build response with all fields from the blogs table
    $response = [
        'id' => $blog['id'],
        'author' => $blog['author'] ?? '',
        'category' => $blog['category'] ?? '',
        'cover_image' => $blog['cover_image'] ?? '',
        'views' => $blog['views'] ?? 0,
        'is_published' => $blog['is_published'] ?? 1,
        'created_at' => $blog['created_at'] ?? '',
        'updated_at' => $blog['updated_at'] ?? '',
        'title_en' => $blog['title_en'] ?? '',
        'excerpt_en' => $blog['excerpt_en'] ?? '',
        'content_en' => $blog['content_en'] ?? '',
        'title_ar' => $blog['title_ar'] ?? '',
        'excerpt_ar' => $blog['excerpt_ar'] ?? '',
        'content_ar' => $blog['content_ar'] ?? ''
    ];
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);
    
} catch (Exception $e) {
    error_log('Get Blog Details Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


