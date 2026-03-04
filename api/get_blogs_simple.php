<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Get Blogs API (Simple Version)
 */

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
    if ($lang !== 'en' && $lang !== 'ar') {
        $lang = 'en';
    }
    
    // Check if fetching a single blog by ID
    if (isset($_GET['id'])) {
        $blog_id = intval($_GET['id']);
        
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
            LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = '" . $conn->real_escape_string($lang) . "'
            WHERE b.id = " . $blog_id;
        
        $result = $conn->query($query);
        
        if (!$result) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
            exit;
        }
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Blog not found']);
            exit;
        }
        
        $blog = $result->fetch_assoc();
        $conn->close();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $blog,
            'language' => $lang
        ]);
        exit;
    }
    
    // Fetch all blogs
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    if ($limit > 1000) $limit = 1000;
    if ($offset < 0) $offset = 0;
    
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
        LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = '" . $conn->real_escape_string($lang) . "'
        ORDER BY b.created_at DESC
        LIMIT " . $limit . " OFFSET " . $offset;
    
    $result = $conn->query($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    $blogs = array();
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'language' => $lang,
        'count' => count($blogs)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


