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
    
    // Build query based on language - avoid CASE with placeholders
    if ($lang === 'ar') {
        $query = "
            SELECT 
                id,
                author,
                category,
                cover_image,
                created_at,
                read_time,
                COALESCE(NULLIF(title_ar, ''), title_en, title) as title,
                COALESCE(NULLIF(excerpt_ar, ''), excerpt_en, excerpt) as excerpt,
                COALESCE(NULLIF(content_ar, ''), content_en, content) as content,
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
    } else {
        $query = "
            SELECT 
                id,
                author,
                category,
                cover_image,
                created_at,
                read_time,
                COALESCE(title_en, title) as title,
                COALESCE(excerpt_en, excerpt) as excerpt,
                COALESCE(content_en, content) as content,
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
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
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
