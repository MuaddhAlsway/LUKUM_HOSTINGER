<?php
/**
 * LAKUM Artspace - Add Blog API (Simple - No Auth)
 * Creates a new blog post without authentication
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields - support both bilingual and single language
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    $title_ar = $input['title_ar'] ?? '';
    $content_en = $input['content_en'] ?? $input['content'] ?? '';
    $content_ar = $input['content_ar'] ?? '';
    
    if (empty($title_en) || empty($content_en)) {
        echo json_encode(['success' => false, 'message' => 'Title and content (English) are required']);
        exit;
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Sanitize inputs
    $excerpt_en = $input['excerpt_en'] ?? $input['excerpt'] ?? '';
    $excerpt_ar = $input['excerpt_ar'] ?? '';
    $author = $input['author'] ?? 'LAKUM Team';
    $category = $input['category'] ?? 'News';
    $cover_image = $input['cover_image'] ?? 'assest/img-4.png';
    $read_time = (int)($input['read_time'] ?? 5);
    
    $query = 'INSERT INTO blogs (title, title_en, title_ar, excerpt, excerpt_en, excerpt_ar, content, content_en, content_ar, author, category, cover_image, read_time, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    // Use title_en as default title for backward compatibility
    $stmt->bind_param('ssssssssssssi', $title_en, $title_en, $title_ar, $excerpt_en, $excerpt_en, $excerpt_ar, $content_en, $content_en, $content_ar, $author, $category, $cover_image, $read_time);
    
    if ($stmt->execute()) {
        $newId = $db->getConnection()->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Blog created successfully',
            'id' => $newId
        ]);
    } else {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log('Add Blog Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

