<?php
/**
 * Add all missing columns to exhibitions table
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    $addedColumns = [];
    $errors = [];
    
    // List of required columns with their definitions
    $requiredColumns = [
        'event_video' => "VARCHAR(500) NULL DEFAULT NULL AFTER cover_image",
        'gallery_images' => "LONGTEXT NULL DEFAULT NULL AFTER event_video"
    ];
    
    // Check and add each column
    foreach ($requiredColumns as $columnName => $columnDef) {
        $checkColumn = $db->getConnection()->query("SHOW COLUMNS FROM exhibitions LIKE '$columnName'");
        
        if ($checkColumn && $checkColumn->num_rows > 0) {
            // Column exists, skip
            continue;
        }
        
        // Column doesn't exist, add it
        $addSQL = "ALTER TABLE exhibitions ADD COLUMN $columnName $columnDef";
        
        if ($db->getConnection()->query($addSQL)) {
            $addedColumns[] = $columnName;
        } else {
            $errors[] = "Error adding $columnName: " . $db->getConnection()->error;
        }
    }
    
    // Now verify all columns exist
    $verifyResult = $db->getConnection()->query("SHOW COLUMNS FROM exhibitions");
    $allColumns = [];
    
    while ($col = $verifyResult->fetch_assoc()) {
        $allColumns[] = $col['Field'];
    }
    
    echo json_encode([
        'success' => true,
        'added_columns' => $addedColumns,
        'errors' => $errors,
        'all_columns' => $allColumns,
        'message' => count($addedColumns) > 0 
            ? 'Successfully added ' . count($addedColumns) . ' column(s)'
            : 'All required columns already exist',
        'next_step' => count($errors) === 0 ? 'Run migration: /api/migrate_exhibitions.php' : 'Fix errors first'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
