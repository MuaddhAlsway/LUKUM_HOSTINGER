<?php
/**
 * LAKUM Artspace - Get Blog Details API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    $blog_id = (int)($_GET['id'] ?? 0);
    
    if (!$blog_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Blog ID is required']);
        exit;
    }
    
    // Get blog with translations only
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.views,
            b.is_published,
            b.created_at,
            b.updated_at,
            MAX(CASE WHEN t.language = 'en' THEN t.title END) as title_en,
            MAX(CASE WHEN t.language = 'en' THEN t.excerpt END) as excerpt_en,
            MAX(CASE WHEN t.language = 'en' THEN t.content END) as content_en,
            MAX(CASE WHEN t.language = 'en' THEN t.slug END) as slug_en,
            MAX(CASE WHEN t.language = 'ar' THEN t.title END) as title_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.excerpt END) as excerpt_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.content END) as content_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.slug END) as slug_ar
        FROM blogs b
        LEFT JOIN blog_translations t ON b.id = t.blog_id
        WHERE b.id = ?
        GROUP BY b.id
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param('i', $blog_id);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
        exit;
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Blog not found']);
        exit;
    }
    
    $blog = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $blog
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
