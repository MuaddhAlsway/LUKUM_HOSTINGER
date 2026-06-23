<?php
/**
 * Migration: Add event_video column to exhibitions table
 * This script ensures the event_video column exists in the exhibitions table
 * Run this ONCE to update any existing databases
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // Database connection
    $db_host = 'localhost';
    $db_user = 'u812122863_neama';
    $db_pass = 'Nema202610!LakumDB';
    $db_name = 'u812122863_lakum_artspace';
    
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if exhibitions table exists
    $tableCheckResult = $conn->query("SHOW TABLES LIKE 'exhibitions'");
    
    if ($tableCheckResult->num_rows === 0) {
        throw new Exception('Exhibitions table does not exist');
    }
    
    // Check if event_video column already exists
    $columnCheckResult = $conn->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'exhibitions' 
         AND TABLE_SCHEMA = '$db_name' 
         AND COLUMN_NAME = 'event_video'"
    );
    
    if ($columnCheckResult->num_rows > 0) {
        // Column already exists
        echo json_encode([
            'success' => true,
            'message' => 'event_video column already exists',
            'status' => 'already_exists'
        ]);
        $conn->close();
        exit;
    }
    
    // Add the column
    $alterSql = "ALTER TABLE exhibitions 
                 ADD COLUMN event_video VARCHAR(500) 
                 COMMENT 'Event video URL (YouTube or Vimeo)' 
                 AFTER cover_image";
    
    if (!$conn->query($alterSql)) {
        throw new Exception('Failed to add event_video column: ' . $conn->error);
    }
    
    // Verify column was added
    $verifyResult = $conn->query(
        "SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'exhibitions' 
         AND TABLE_SCHEMA = '$db_name' 
         AND COLUMN_NAME = 'event_video'"
    );
    
    if ($verifyResult->num_rows === 0) {
        throw new Exception('Column verification failed - column was not added');
    }
    
    $colInfo = $verifyResult->fetch_assoc();
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'event_video column added successfully',
        'status' => 'column_added',
        'column_info' => [
            'name' => $colInfo['COLUMN_NAME'],
            'type' => $colInfo['COLUMN_TYPE']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'status' => 'error'
    ]);
}
?>
