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
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    // Simple query - get all blogs with all columns
    $query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 100";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        // Process language-specific content
        if ($lang === 'ar') {
            // Use Arabic if available, fallback to English
            $row['title'] = !empty($row['title_ar']) ? $row['title_ar'] : (!empty($row['title_en']) ? $row['title_en'] : $row['title']);
            $row['excerpt'] = !empty($row['excerpt_ar']) ? $row['excerpt_ar'] : (!empty($row['excerpt_en']) ? $row['excerpt_en'] : $row['excerpt']);
            $row['content'] = !empty($row['content_ar']) ? $row['content_ar'] : (!empty($row['content_en']) ? $row['content_en'] : $row['content']);
        } else {
            // Use English if available, fallback to default
            $row['title'] = !empty($row['title_en']) ? $row['title_en'] : $row['title'];
            $row['excerpt'] = !empty($row['excerpt_en']) ? $row['excerpt_en'] : $row['excerpt'];
            $row['content'] = !empty($row['content_en']) ? $row['content_en'] : $row['content'];
        }
        
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
