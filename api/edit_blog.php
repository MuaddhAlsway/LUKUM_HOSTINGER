<?php
/**
 * LAKUM Artspace - Edit Blog API (Bilingual)
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        error_log('Database connection failed in edit_blog.php');
        echo json_encode(['success' => false, 'error' => 'Database connection failed', 'data' => []]);
        exit;
    }
    
    $conn = $db->getConnection();
    
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
    $author = $input['author'] ?? '';
    $category = $input['category'] ?? '';
    $cover_image = $input['cover_image'] ?? null;
    
    $title_en = $input['title_en'] ?? '';
    $excerpt_en = $input['excerpt_en'] ?? '';
    $content_en = $input['content_en'] ?? '';
    
    $title_ar = $input['title_ar'] ?? '';
    $excerpt_ar = $input['excerpt_ar'] ?? '';
    $content_ar = $input['content_ar'] ?? '';
    
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
    $updateQuery = "UPDATE blogs SET author = ?, category = ?";
    $params = [$author, $category];
    $types = 'ss';
    
    if ($cover_image) {
        $updateQuery .= ", cover_image = ?";
        $params[] = $cover_image;
        $types .= 's';
    }
    
    $updateQuery .= " WHERE id = ?";
    $params[] = $blog_id;
    $types .= 'i';
    
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        throw new Exception('Prepare update: ' . $conn->error);
    }
    
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        throw new Exception('Execute update: ' . $stmt->error);
    }
    $stmt->close();
    
    // Update English translation
    if (!empty($title_en)) {
        $slug_en = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
        
        $transQuery = "
            INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug)
            VALUES (?, 'en', ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = ?,
                excerpt = ?,
                content = ?,
                slug = ?
        ";
        
        $stmt = $conn->prepare($transQuery);
        if (!$stmt) {
            throw new Exception('Prepare EN translation: ' . $conn->error);
        }
        
        $stmt->bind_param('isssssss', $blog_id, $title_en, $excerpt_en, $content_en, $slug_en, $title_en, $excerpt_en, $content_en, $slug_en);
        if (!$stmt->execute()) {
            throw new Exception('Execute EN translation: ' . $stmt->error);
        }
        $stmt->close();
    }
    
    // Update Arabic translation
    if (!empty($title_ar)) {
        $slug_ar = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_ar), '-'));
        
        $transQuery = "
            INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug)
            VALUES (?, 'ar', ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = ?,
                excerpt = ?,
                content = ?,
                slug = ?
        ";
        
        $stmt = $conn->prepare($transQuery);
        if (!$stmt) {
            throw new Exception('Prepare AR translation: ' . $conn->error);
        }
        
        $stmt->bind_param('isssssss', $blog_id, $title_ar, $excerpt_ar, $content_ar, $slug_ar, $title_ar, $excerpt_ar, $content_ar, $slug_ar);
        if (!$stmt->execute()) {
            throw new Exception('Execute AR translation: ' . $stmt->error);
        }
        $stmt->close();
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

