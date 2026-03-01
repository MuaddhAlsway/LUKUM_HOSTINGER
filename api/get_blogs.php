<?php
/**
 * LAKUM Artspace - Get Blogs API (Bilingual)
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        error_log('Database connection failed in get_blogs.php');
        echo json_encode(['success' => false, 'error' => 'Database connection failed', 'data' => []]);
        exit;
    }
    
    $conn = $db->getConnection();
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    // Check if fetching a single blog by ID
    if (isset($_GET['id'])) {
        $blog_id = (int)$_GET['id'];
        
        $query = "
            SELECT 
                b.id,
                b.author,
                b.category,
                b.cover_image,
                b.views,
                b.is_published,
                b.created_at,
                COALESCE(t.title, b.title) as title,
                COALESCE(t.excerpt, b.excerpt) as excerpt,
                COALESCE(t.content, b.content) as content,
                COALESCE(t.slug, b.slug) as slug
            FROM blogs b
            LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = ?
            WHERE b.id = ?
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Prepare: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param('si', $lang, $blog_id);
        
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Execute: ' . $stmt->error]);
            exit;
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Blog not found']);
            exit;
        }
        
        $blog = $result->fetch_assoc();
        $stmt->close();
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
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.views,
            b.is_published,
            b.created_at,
            COALESCE(t.title, b.title) as title,
            COALESCE(t.excerpt, b.excerpt) as excerpt,
            COALESCE(t.content, b.content) as content,
            COALESCE(t.slug, b.slug) as slug
        FROM blogs b
        LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = ?
        ORDER BY b.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Prepare: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param('sii', $lang, $limit, $offset);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Execute: ' . $stmt->error]);
        exit;
    }
    
    $result = $stmt->get_result();
    $blogs = [];
    
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'language' => $lang
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
