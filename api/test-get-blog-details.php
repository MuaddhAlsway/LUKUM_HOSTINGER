<?php
/**
 * Test get_blog_details API
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $blog_id = 5;
    
    // Test 1: Get base blog data
    $result = $conn->query("SELECT * FROM blogs WHERE id = $blog_id");
    $blog_base = $result->fetch_assoc();
    
    // Test 2: Get translations
    $result = $conn->query("SELECT * FROM blog_translations WHERE blog_id = $blog_id");
    $translations = [];
    while ($row = $result->fetch_assoc()) {
        $translations[] = $row;
    }
    
    // Test 3: Run the actual query from get_blog_details.php
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.views,
            b.is_published,
            b.created_at,
            b.updated_at,
            MAX(CASE WHEN t.language = 'en' THEN t.title END) as title_en,
            MAX(CASE WHEN t.language = 'en' THEN t.excerpt END) as excerpt_en,
            MAX(CASE WHEN t.language = 'en' THEN t.content END) as content_en,
            MAX(CASE WHEN t.language = 'en' THEN t.slug END) as slug_en,
            MAX(CASE WHEN t.language = 'ar' THEN t.title END) as title_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.excerpt END) as excerpt_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.content END) as content_ar,
            MAX(CASE WHEN t.language = 'ar' THEN t.slug END) as slug_ar
        FROM blogs b
        LEFT JOIN blog_translations t ON b.id = t.blog_id
        WHERE b.id = $blog_id
        GROUP BY b.id
    ";
    
    $result = $conn->query($query);
    $blog_with_translations = $result->fetch_assoc();
    
    $conn->close();
    
    echo json_encode([
        'blog_id' => $blog_id,
        'base_blog_data' => $blog_base,
        'translations_in_db' => $translations,
        'query_result' => $blog_with_translations
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>

