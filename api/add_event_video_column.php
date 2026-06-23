<?php
/**
 * Add event_video column to exhibitions table if missing
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Check if event_video column exists
    $checkColumn = $db->getConnection()->query("SHOW COLUMNS FROM exhibitions LIKE 'event_video'");
    
    if ($checkColumn && $checkColumn->num_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'event_video column already exists',
            'action_taken' => 'none'
        ]);
        exit;
    }
    
    // Add event_video column
    $addColumnSQL = "ALTER TABLE exhibitions ADD COLUMN event_video VARCHAR(500) NULL DEFAULT NULL AFTER cover_image";
    
    if ($db->getConnection()->query($addColumnSQL)) {
        echo json_encode([
            'success' => true,
            'message' => 'event_video column added successfully',
            'action_taken' => 'added_column'
        ]);
    } else {
        throw new Exception('Error adding column: ' . $db->getConnection()->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
