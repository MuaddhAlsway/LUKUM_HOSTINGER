<?php
/**
 * Diagnostic script to check blog API issues
 */

header('Content-Type: application/json');

$diagnostics = [
    'php_version' => phpversion(),
    'mysqli_available' => extension_loaded('mysqli'),
    'database_connection' => null,
    'database_selected' => null,
    'tables_exist' => [],
    'sample_query' => null,
    'error' => null
];

try {
    // Test connection
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        $diagnostics['error'] = 'Connection failed: ' . $conn->connect_error;
        echo json_encode($diagnostics);
        exit;
    }
    
    $diagnostics['database_connection'] = 'SUCCESS';
    
    // Check if database is selected
    $result = $conn->query("SELECT DATABASE()");
    if ($result) {
        $row = $result->fetch_row();
        $diagnostics['database_selected'] = $row[0];
    }
    
    // Check tables
    $tables = ['blogs', 'blog_translations', 'events', 'press', 'pricing', 'admins'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        $diagnostics['tables_exist'][$table] = $result && $result->num_rows > 0 ? 'EXISTS' : 'MISSING';
    }
    
    // Try sample query
    $query = "SELECT COUNT(*) as count FROM blogs";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $diagnostics['sample_query'] = [
            'query' => $query,
            'result' => $row,
            'status' => 'SUCCESS'
        ];
    } else {
        $diagnostics['sample_query'] = [
            'query' => $query,
            'error' => $conn->error,
            'status' => 'FAILED'
        ];
    }
    
    // Try the actual get_blogs query
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
        LEFT JOIN blog_translations t ON b.id = t.blog_id AND t.language = ?
        ORDER BY b.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $diagnostics['get_blogs_query'] = [
            'status' => 'PREPARE_FAILED',
            'error' => $conn->error
        ];
    } else {
        $stmt->bind_param('sii', $lang, $limit, $offset);
        if (!$stmt->execute()) {
            $diagnostics['get_blogs_query'] = [
                'status' => 'EXECUTE_FAILED',
                'error' => $stmt->error
            ];
        } else {
            $result = $stmt->get_result();
            $diagnostics['get_blogs_query'] = [
                'status' => 'SUCCESS',
                'rows_returned' => $result->num_rows,
                'sample_data' => $result->num_rows > 0 ? $result->fetch_assoc() : null
            ];
        }
        $stmt->close();
    }
    
    $conn->close();
    
} catch (Exception $e) {
    $diagnostics['error'] = $e->getMessage();
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>


