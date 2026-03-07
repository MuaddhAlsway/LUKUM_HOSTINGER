<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if blogs table exists
    $tableCheck = "SHOW TABLES LIKE 'blogs'";
    $tableResult = $conn->query($tableCheck);
    
    if (!$tableResult || $tableResult->num_rows === 0) {
        echo json_encode(['error' => 'blogs table does not exist']);
        exit;
    }
    
    // Get all blogs
    $query = "SELECT id, title, title_en, title_ar, slug FROM blogs LIMIT 10";
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['error' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'total' => count($blogs),
        'blogs' => $blogs
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
