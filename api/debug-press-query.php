<?php
/**
 * LAKUM Artspace - Debug Press Query
 * Shows exactly what the API query returns
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    $lang = $_GET['lang'] ?? 'ar';
    
    // Exact query from get_press.php
    $query = "
        SELECT 
            p.id,
            p.source,
            p.press_date,
            p.url,
            p.category,
            p.cover_image,
            p.slug,
            p.is_published,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(p.title_ar, ''), p.title_en, p.title)
                ELSE COALESCE(p.title_en, p.title)
            END as title,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(p.content_ar, ''), p.content_en, p.content)
                ELSE COALESCE(p.content_en, p.content)
            END as content,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(p.excerpt_ar, ''), p.excerpt_en, p.excerpt)
                ELSE COALESCE(p.excerpt_en, p.excerpt)
            END as excerpt,
            p.title_en,
            p.excerpt_en,
            p.content_en,
            p.title_ar,
            p.excerpt_ar,
            p.content_ar
        FROM press p
        WHERE p.is_published = 1
        ORDER BY p.press_date DESC
        LIMIT 3
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . $conn->error);
    }
    
    $stmt->bind_param('sss', $lang, $lang, $lang);
    
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $press = [];
    while ($row = $result->fetch_assoc()) {
        $press[] = $row;
    }
    
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'language' => $lang,
        'message' => 'Debug query result - first 3 items',
        'data' => $press,
        'note' => 'Check if title field contains Arabic or English'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Debug Press Query Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
