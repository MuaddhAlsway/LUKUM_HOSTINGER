<?php
/**
 * Test Arabic blog retrieval
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $blog_id = 5;
    $lang = 'ar';
    
    // Test query
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.views,
            b.is_published,
            b.created_at,
            t.title,
            t.excerpt,
            t.content,
            t.slug
        FROM blogs b
        LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = '$lang'
        WHERE b.id = $blog_id
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $blog = $result->fetch_assoc();
    
    $conn->close();
    
    echo json_encode([
        'query' => $query,
        'blog_id' => $blog_id,
        'language' => $lang,
        'result' => $blog
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>

