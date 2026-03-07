<?php
/**
 * LAKUM Artspace - Diagnose Press Database
 * Comprehensive diagnostic to identify actual press IDs and their current state
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
    
    // Get ALL press items to see what we're working with
    $query = "
        SELECT 
            id,
            title,
            title_en,
            title_ar,
            excerpt,
            excerpt_en,
            excerpt_ar,
            is_published,
            press_date
        FROM press 
        ORDER BY id ASC
    ";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $allPress = [];
    $publishedIds = [];
    $nullTitleIds = [];
    $englishOnlyIds = [];
    
    while ($row = $result->fetch_assoc()) {
        $allPress[] = $row;
        
        if ($row['is_published'] == 1) {
            $publishedIds[] = $row['id'];
        }
        
        if (empty($row['title']) && empty($row['title_en']) && empty($row['title_ar'])) {
            $nullTitleIds[] = $row['id'];
        }
        
        if (!empty($row['title_en']) && empty($row['title_ar'])) {
            $englishOnlyIds[] = $row['id'];
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Press database diagnostic complete',
        'total_press_items' => count($allPress),
        'published_ids' => $publishedIds,
        'null_title_ids' => $nullTitleIds,
        'english_only_ids' => $englishOnlyIds,
        'all_press' => $allPress,
        'note' => 'Use published_ids for populate script. These are the actual IDs in your database.'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Diagnose Press Database Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
