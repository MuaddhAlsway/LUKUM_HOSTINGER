<?php
/**
 * Check exhibitions table structure
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Get exhibitions table columns
    $columnsResult = $db->getConnection()->query("SHOW COLUMNS FROM exhibitions");
    $columns = [];
    
    if ($columnsResult) {
        while ($col = $columnsResult->fetch_assoc()) {
            $columns[] = [
                'name' => $col['Field'],
                'type' => $col['Type'],
                'null' => $col['Null'],
                'key' => $col['Key'],
                'default' => $col['Default']
            ];
        }
    }
    
    // Check if event_video column exists
    $hasEventVideo = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'event_video') {
            $hasEventVideo = true;
            break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'table' => 'exhibitions',
        'columns' => $columns,
        'column_count' => count($columns),
        'has_event_video' => $hasEventVideo,
        'missing_columns' => $hasEventVideo ? [] : ['event_video'],
        'action' => $hasEventVideo ? 'Ready to migrate' : 'Need to add event_video column'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
