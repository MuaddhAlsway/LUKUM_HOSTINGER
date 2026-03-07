<?php
/**
 * Check blogs table structure
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if (!$conn) {
        echo json_encode(['error' => 'No database connection']);
        exit;
    }
    
    // Check if table exists
    $checkTable = "SHOW TABLES LIKE 'blogs'";
    $tableResult = $conn->query($checkTable);
    
    if (!$tableResult || $tableResult->num_rows === 0) {
        echo json_encode(['error' => 'blogs table does not exist']);
        exit;
    }
    
    // Get table structure
    $structureQuery = "DESCRIBE blogs";
    $structureResult = $conn->query($structureQuery);
    
    if (!$structureResult) {
        echo json_encode(['error' => 'Cannot describe table: ' . $conn->error]);
        exit;
    }
    
    $columns = [];
    while ($row = $structureResult->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    // Get sample data
    $sampleQuery = "SELECT * FROM blogs LIMIT 1";
    $sampleResult = $conn->query($sampleQuery);
    $sample = $sampleResult ? $sampleResult->fetch_assoc() : null;
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'columns' => $columns,
        'sample_data' => $sample,
        'required_columns' => [
            'id', 'title', 'title_en', 'title_ar', 
            'excerpt', 'excerpt_en', 'excerpt_ar',
            'content', 'content_en', 'content_ar',
            'author', 'category', 'cover_image', 'created_at'
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
