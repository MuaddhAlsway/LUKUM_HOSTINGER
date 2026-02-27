<?php
/**
 * Test the simple API
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Check if blog_translations table exists
    $result = $conn->query("SHOW TABLES LIKE 'blog_translations'");
    if ($result->num_rows === 0) {
        throw new Exception('blog_translations table does not exist');
    }
    
    // Check if there's data in blog_translations
    $result = $conn->query("SELECT COUNT(*) as count FROM blog_translations");
    $row = $result->fetch_assoc();
    
    if ($row['count'] === 0) {
        throw new Exception('blog_translations table is empty');
    }
    
    // Try the actual query
    $lang = 'en';
    $limit = 100;
    $offset = 0;
    
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
        ORDER BY b.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'count' => count($blogs),
        'data' => $blogs
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
