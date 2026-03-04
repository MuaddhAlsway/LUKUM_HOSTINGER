<?php
/**
 * Populate blog_translations table with English translations
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Get all blogs
    $result = $conn->query("SELECT id, title, excerpt, content, slug FROM blogs");
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $inserted = 0;
    
    while ($blog = $result->fetch_assoc()) {
        $blog_id = $blog['id'];
        $title = $conn->real_escape_string($blog['title']);
        $excerpt = $conn->real_escape_string($blog['excerpt']);
        $content = $conn->real_escape_string($blog['content']);
        $slug = $conn->real_escape_string($blog['slug'] ?? '');
        
        // Generate slug if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        }
        
        // Insert English translation
        $query = "
            INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug)
            VALUES ($blog_id, 'en', '$title', '$excerpt', '$content', '$slug')
            ON DUPLICATE KEY UPDATE 
                title = '$title',
                excerpt = '$excerpt',
                content = '$content',
                slug = '$slug'
        ";
        
        if ($conn->query($query)) {
            $inserted++;
        } else {
            throw new Exception('Insert failed for blog ' . $blog_id . ': ' . $conn->error);
        }
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog translations populated',
        'inserted' => $inserted
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>


