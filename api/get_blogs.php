<?php
/**
 * Blogs API - Returns bilingual content based on language parameter
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    // Query with bilingual support - returns appropriate language based on parameter
    $query = "
        SELECT 
            id,
            author,
            category,
            cover_image,
            created_at,
            read_time,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(title_ar, ''), title_en, title)
                ELSE COALESCE(title_en, title)
            END as title,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(excerpt_ar, ''), excerpt_en, excerpt)
                ELSE COALESCE(excerpt_en, excerpt)
            END as excerpt,
            CASE 
                WHEN ? = 'ar' THEN COALESCE(NULLIF(content_ar, ''), content_en, content)
                ELSE COALESCE(content_en, content)
            END as content,
            title_en,
            excerpt_en,
            content_en,
            title_ar,
            excerpt_ar,
            content_ar
        FROM blogs 
        ORDER BY created_at DESC 
        LIMIT 100
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
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'language' => $lang,
        'count' => count($blogs)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
