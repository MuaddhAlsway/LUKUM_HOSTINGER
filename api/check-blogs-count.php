<?php
/**
 * Check how many blogs exist in database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Count blogs
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $row = $result->fetch_assoc();
    $blog_count = $row['count'];
    
    // Count translations
    $result = $conn->query("SELECT COUNT(*) as count FROM blog_translations");
    $row = $result->fetch_assoc();
    $translation_count = $row['count'];
    
    // Get all blogs
    $result = $conn->query("SELECT id, title FROM blogs");
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'blog_count' => $blog_count,
        'translation_count' => $translation_count,
        'blogs' => $blogs
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>


