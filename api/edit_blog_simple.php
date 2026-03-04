<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Edit Blog API (Simple)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }
    
    $blog_id = (int)($input['id'] ?? 0);
    $author = $conn->real_escape_string($input['author'] ?? '');
    $category = $conn->real_escape_string($input['category'] ?? '');
    $cover_image = isset($input['cover_image']) ? $conn->real_escape_string($input['cover_image']) : null;
    
    $title_en = $conn->real_escape_string($input['title_en'] ?? '');
    $excerpt_en = $conn->real_escape_string($input['excerpt_en'] ?? '');
    $content_en = $conn->real_escape_string($input['content_en'] ?? '');
    
    $title_ar = $conn->real_escape_string($input['title_ar'] ?? '');
    $excerpt_ar = $conn->real_escape_string($input['excerpt_ar'] ?? '');
    $content_ar = $conn->real_escape_string($input['content_ar'] ?? '');
    
    if (!$blog_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Blog ID is required']);
        exit;
    }
    
    if (!$title_en || !$content_en) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'English title and content are required']);
        exit;
    }
    
    // Update base blog record
    $updateQuery = "UPDATE blogs SET author = '$author', category = '$category'";
    
    if ($cover_image) {
        $updateQuery .= ", cover_image = '$cover_image'";
    }
    
    $updateQuery .= " WHERE id = $blog_id";
    
    if (!$conn->query($updateQuery)) {
        throw new Exception('Update blog failed: ' . $conn->error);
    }
    
    // Update English translation
    if (!empty($title_en)) {
        $slug_en = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
        
        $transQuery = "
            INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug)
            VALUES ($blog_id, 'en', '$title_en', '$excerpt_en', '$content_en', '$slug_en')
            ON DUPLICATE KEY UPDATE
                title = '$title_en',
                excerpt = '$excerpt_en',
                content = '$content_en',
                slug = '$slug_en'
        ";
        
        if (!$conn->query($transQuery)) {
            throw new Exception('Update EN translation failed: ' . $conn->error);
        }
    }
    
    // Update Arabic translation
    if (!empty($title_ar)) {
        $slug_ar = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_ar), '-'));
        
        $transQuery = "
            INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug)
            VALUES ($blog_id, 'ar', '$title_ar', '$excerpt_ar', '$content_ar', '$slug_ar')
            ON DUPLICATE KEY UPDATE
                title = '$title_ar',
                excerpt = '$excerpt_ar',
                content = '$content_ar',
                slug = '$slug_ar'
        ";
        
        if (!$conn->query($transQuery)) {
            throw new Exception('Update AR translation failed: ' . $conn->error);
        }
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Blog updated successfully',
        'blog_id' => $blog_id
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


