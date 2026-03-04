<?php
/**
 * Test delete blog API
 */

header('Content-Type: application/json');

try {
    $blog_id = 5;
    
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Test 1: Check if blog exists
    $result = $conn->query("SELECT * FROM blogs WHERE id = $blog_id");
    $blog_exists = $result && $result->num_rows > 0;
    
    // Test 2: Check translations
    $result = $conn->query("SELECT COUNT(*) as count FROM blog_translations WHERE blog_id = $blog_id");
    $row = $result->fetch_assoc();
    $translation_count = $row['count'];
    
    $conn->close();
    
    echo json_encode([
        'blog_id' => $blog_id,
        'blog_exists' => $blog_exists,
        'translation_count' => $translation_count,
        'can_delete' => true
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>


