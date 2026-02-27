<?php
/**
 * LAKUM Artspace - Get Blogs API (Debug Version)
 */

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_log('=== GET_BLOGS_DEBUG START ===');
error_log('GET params: ' . json_encode($_GET));

try {
    error_log('Connecting to database...');
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        error_log('Connection error: ' . $conn->connect_error);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    error_log('Connected successfully');
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    error_log('Language: ' . $lang);
    
    // Fetch all blogs
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    error_log('Limit: ' . $limit . ', Offset: ' . $offset);
    
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
    
    error_log('Query: ' . $query);
    
    $result = $conn->query($query);
    
    if (!$result) {
        error_log('Query error: ' . $conn->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    error_log('Query executed successfully');
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    error_log('Blogs fetched: ' . count($blogs));
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'language' => $lang,
        'count' => count($blogs)
    ]);
    
    error_log('Response sent successfully');
    
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

error_log('=== GET_BLOGS_DEBUG END ===');
?>
