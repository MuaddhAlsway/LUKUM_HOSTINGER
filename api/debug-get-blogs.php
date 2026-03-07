<?php
/**
 * Debug Blogs API - Check what's happening
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
    
    $lang = $_GET['lang'] ?? 'en';
    
    // Simple test query first
    $testQuery = "SELECT COUNT(*) as count FROM blogs";
    $testResult = $conn->query($testQuery);
    
    if (!$testResult) {
        echo json_encode(['error' => 'Test query failed: ' . $conn->error]);
        exit;
    }
    
    $testRow = $testResult->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'debug' => [
            'database_connected' => true,
            'blogs_count' => $testRow['count'],
            'language_requested' => $lang,
            'message' => 'Database is working'
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
