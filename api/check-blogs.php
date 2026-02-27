<?php
/**
 * Check if blogs exist in database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode([
            'success' => false,
            'message' => 'Database not connected',
            'connected' => false
        ]);
        exit;
    }
    
    // Check if blogs table exists
    $result = $db->query("SHOW TABLES LIKE 'blogs'");
    $tableExists = $result && $result->num_rows > 0;
    
    // Count blogs
    $countResult = $db->query("SELECT COUNT(*) as count FROM blogs");
    $countRow = $countResult->fetch_assoc();
    $blogCount = $countRow['count'] ?? 0;
    
    // Get sample blog
    $sampleResult = $db->query("SELECT * FROM blogs LIMIT 1");
    $sampleBlog = $sampleResult ? $sampleResult->fetch_assoc() : null;
    
    // Get blog with ID 6
    $blog6Result = $db->query("SELECT * FROM blogs WHERE id = 6");
    $blog6 = $blog6Result ? $blog6Result->fetch_assoc() : null;
    
    echo json_encode([
        'success' => true,
        'connected' => true,
        'table_exists' => $tableExists,
        'total_blogs' => $blogCount,
        'sample_blog' => $sampleBlog,
        'blog_id_6' => $blog6,
        'database' => defined('DB_NAME') ? DB_NAME : 'lakum_artspace'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'connected' => false
    ]);
}
?>

