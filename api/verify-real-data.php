<?php
/**
 * Verify that data is real from database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Get database info
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    $database = $row[0];
    
    // Count blogs
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $row = $result->fetch_assoc();
    $blog_count = $row['count'];
    
    // Count blog translations
    $result = $conn->query("SELECT COUNT(*) as count FROM blog_translations");
    $row = $result->fetch_assoc();
    $translation_count = $row['count'];
    
    // Get all blogs with their translations
    $result = $conn->query("
        SELECT 
            b.id,
            b.title,
            b.author,
            b.category,
            b.created_at,
            COUNT(t.id) as translation_count
        FROM blogs b
        LEFT JOIN blog_translations t ON b.id = t.blog_id
        GROUP BY b.id
        ORDER BY b.id
    ");
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'status' => 'REAL DATA VERIFIED',
        'database' => $database,
        'source' => 'MySQL Database (lakum_artspace)',
        'total_blogs' => $blog_count,
        'total_translations' => $translation_count,
        'blogs_list' => $blogs,
        'verification' => 'This data is stored in your MySQL database, NOT mock data'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
