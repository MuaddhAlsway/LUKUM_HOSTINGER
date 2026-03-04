<?php
/**
 * Test Blogs API - Debug version
 */

header('Content-Type: application/json');

require_once 'config.php';

$response = [
    'status' => 'testing',
    'checks' => []
];

try {
    // Check 1: Database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        $response['checks'][] = ['name' => 'Database Connection', 'status' => 'FAILED', 'error' => 'No connection'];
        echo json_encode($response);
        exit;
    }
    $response['checks'][] = ['name' => 'Database Connection', 'status' => 'OK'];
    
    // Check 2: Check if blogs table exists
    $result = $conn->query("SHOW TABLES LIKE 'blogs'");
    if (!$result || $result->num_rows === 0) {
        $response['checks'][] = ['name' => 'Blogs Table', 'status' => 'FAILED', 'error' => 'Table does not exist'];
        echo json_encode($response);
        exit;
    }
    $response['checks'][] = ['name' => 'Blogs Table', 'status' => 'OK'];
    
    // Check 3: Check if blog_translations table exists
    $result = $conn->query("SHOW TABLES LIKE 'blog_translations'");
    $translationsTableExists = $result && $result->num_rows > 0;
    $response['checks'][] = ['name' => 'Blog Translations Table', 'status' => $translationsTableExists ? 'EXISTS' : 'DOES NOT EXIST'];
    
    // Check 4: Count blogs
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $row = $result->fetch_assoc();
    $blogCount = $row['count'];
    $response['checks'][] = ['name' => 'Blog Count', 'status' => 'OK', 'count' => $blogCount];
    
    // Check 5: Get blog columns
    $result = $conn->query("SHOW COLUMNS FROM blogs");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    $response['checks'][] = ['name' => 'Blog Columns', 'status' => 'OK', 'columns' => $columns];
    
    // Check 6: Try the actual query
    $lang = 'ar';
    $limit = 100;
    $offset = 0;
    
    if ($translationsTableExists) {
        $query = "
            SELECT 
                b.id,
                b.author,
                b.category,
                b.cover_image,
                b.created_at,
                COALESCE(t.title, b.title) as title,
                COALESCE(t.excerpt, b.excerpt) as excerpt,
                COALESCE(t.content, b.content) as content
            FROM blogs b
            LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = ?
            ORDER BY b.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $response['checks'][] = ['name' => 'Query Preparation', 'status' => 'FAILED', 'error' => $conn->error];
            echo json_encode($response);
            exit;
        }
        
        $stmt->bind_param('sii', $lang, $limit, $offset);
        if (!$stmt->execute()) {
            $response['checks'][] = ['name' => 'Query Execution', 'status' => 'FAILED', 'error' => $stmt->error];
            echo json_encode($response);
            exit;
        }
        
        $result = $stmt->get_result();
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }
        $stmt->close();
        
        $response['checks'][] = ['name' => 'Query Execution', 'status' => 'OK', 'blogs_fetched' => count($blogs)];
        $response['sample_blogs'] = array_slice($blogs, 0, 2);
    } else {
        $response['checks'][] = ['name' => 'Query Execution', 'status' => 'SKIPPED', 'reason' => 'blog_translations table does not exist'];
    }
    
    $response['status'] = 'success';
    
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);


