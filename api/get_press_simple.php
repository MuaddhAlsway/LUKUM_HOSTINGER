<?php
/**
 * Simple Press API - Direct database query
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $lang = $_GET['lang'] ?? 'en';
    
    // Simple query - get all press
    $query = "SELECT id, title, excerpt, content, source, press_date, cover_image FROM press WHERE is_published = 1 ORDER BY press_date DESC LIMIT 100";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $press = [];
    while ($row = $result->fetch_assoc()) {
        $press[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $press,
        'count' => count($press)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
