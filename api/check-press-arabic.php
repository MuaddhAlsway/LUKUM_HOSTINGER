<?php
/**
 * LAKUM Artspace - Check Press Arabic Translations
 * Debug script to verify Arabic translations are in database
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Check what's actually in the database
    $query = "
        SELECT 
            id,
            title_en,
            title_ar,
            excerpt_en,
            excerpt_ar,
            content_en,
            content_ar
        FROM press 
        WHERE is_published = 1
        LIMIT 3
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $press = [];
    while ($row = $result->fetch_assoc()) {
        $press[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Database check - first 3 press items',
        'data' => $press,
        'note' => 'Check if title_ar, excerpt_ar, content_ar have values or are NULL/empty'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Check Press Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
