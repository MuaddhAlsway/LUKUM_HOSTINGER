<?php
/**
 * Debug API - Check all blogs and their slugs
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Get all blogs with their slugs
    $query = "
        SELECT 
            id,
            title_en,
            title_ar,
            title,
            slug,
            created_at
        FROM blogs
        ORDER BY id DESC
        LIMIT 20
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'total_blogs' => count($blogs),
        'blogs' => $blogs,
        'test_slug' => 'behind-the-scenes-curation',
        'test_url' => '/api/get_blogs_working.php?slug=behind-the-scenes-curation&lang=en'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
