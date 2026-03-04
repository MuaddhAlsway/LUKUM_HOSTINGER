<?php
/**
 * LAKUM Artspace - Add Blog API (Bilingual)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Use config.php for database connection
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }
    
    // Extract base fields
    $author = $input['author'] ?? 'Admin';
    $category = $input['category'] ?? 'General';
    $cover_image = $input['cover_image'] ?? 'assest/img-4.png';
    
    // Extract English fields
    $title_en = $input['title_en'] ?? '';
    $content_en = $input['content_en'] ?? '';
    $excerpt_en = $input['excerpt_en'] ?? '';
    
    // Extract Arabic fields
    $title_ar = $input['title_ar'] ?? '';
    $content_ar = $input['content_ar'] ?? '';
    $excerpt_ar = $input['excerpt_ar'] ?? '';
    
    if (empty($title_en) && empty($title_ar)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'At least one title (EN or AR) is required']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Use title_en as default title for backward compatibility
    $title = $title_en ?: $title_ar;
    $excerpt = $excerpt_en ?: $excerpt_ar;
    $content = $content_en ?: $content_ar;
    
    // Insert blog record with bilingual columns
    $query = "INSERT INTO blogs (title, title_en, title_ar, excerpt, excerpt_en, excerpt_ar, content, content_en, content_ar, author, category, cover_image, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssssssssssss', $title, $title_en, $title_ar, $excerpt, $excerpt_en, $excerpt_ar, $content, $content_en, $content_ar, $author, $category, $cover_image);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $blog_id = $conn->insert_id;
    $stmt->close();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Blog created successfully',
        'blog_id' => $blog_id
    ]);
    
} catch (Exception $e) {
    error_log('Add Blog API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

