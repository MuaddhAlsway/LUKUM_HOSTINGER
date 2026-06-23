<?php
/**
 * CRITICAL MIGRATION SCRIPT
 * 
 * Fixes: Add missing video columns to events and exhibitions tables
 * 
 * Problem: Old databases don't have video_url and event_video columns
 * Solution: Add columns if they don't exist
 * 
 * Run this ONCE via browser to update database:
 * https://yourdomain.com/api/MIGRATION_VIDEO_COLUMNS.php
 */

header('Content-Type: application/json; charset=utf-8');

$results = [];

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
    
    // ========== EXHIBITIONS TABLE ==========
    
    // Check if exhibitions table exists
    $exhibTableExists = $conn->query("SHOW TABLES LIKE 'exhibitions'")->num_rows > 0;
    
    if ($exhibTableExists) {
        // Check if event_video column exists
        $eventVideoExists = $conn->query(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_NAME = 'exhibitions' 
             AND TABLE_SCHEMA = '$db_name' 
             AND COLUMN_NAME = 'event_video'"
        )->num_rows > 0;
        
        if (!$eventVideoExists) {
            // Add event_video column
            $addEventVideoSQL = "ALTER TABLE exhibitions 
                                ADD COLUMN event_video VARCHAR(500) 
                                COMMENT 'Event video URL (YouTube or Vimeo)' 
                                AFTER cover_image";
            
            if ($conn->query($addEventVideoSQL)) {
                $results['exhibitions_event_video'] = [
                    'status' => 'added',
                    'message' => 'event_video column added to exhibitions table'
                ];
            } else {
                $results['exhibitions_event_video'] = [
                    'status' => 'error',
                    'message' => 'Failed to add event_video: ' . $conn->error
                ];
            }
        } else {
            $results['exhibitions_event_video'] = [
                'status' => 'exists',
                'message' => 'event_video column already exists in exhibitions table'
            ];
        }
    } else {
        $results['exhibitions_event_video'] = [
            'status' => 'table_missing',
            'message' => 'exhibitions table does not exist'
        ];
    }
    
    // ========== EVENTS TABLE ==========
    
    // Check if events table exists
    $eventsTableExists = $conn->query("SHOW TABLES LIKE 'events'")->num_rows > 0;
    
    if ($eventsTableExists) {
        // Check if video_url column exists
        $videoUrlExists = $conn->query(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_NAME = 'events' 
             AND TABLE_SCHEMA = '$db_name' 
             AND COLUMN_NAME = 'video_url'"
        )->num_rows > 0;
        
        if (!$videoUrlExists) {
            // Add video_url column
            $addVideoUrlSQL = "ALTER TABLE events 
                              ADD COLUMN video_url VARCHAR(500) 
                              COMMENT 'Event video URL (YouTube or Vimeo)' 
                              AFTER cover_image";
            
            if ($conn->query($addVideoUrlSQL)) {
                $results['events_video_url'] = [
                    'status' => 'added',
                    'message' => 'video_url column added to events table'
                ];
            } else {
                $results['events_video_url'] = [
                    'status' => 'error',
                    'message' => 'Failed to add video_url: ' . $conn->error
                ];
            }
        } else {
            $results['events_video_url'] = [
                'status' => 'exists',
                'message' => 'video_url column already exists in events table'
            ];
        }
    } else {
        $results['events_video_url'] = [
            'status' => 'table_missing',
            'message' => 'events table does not exist'
        ];
    }
    
    // ========== VERIFY BOTH COLUMNS NOW EXIST ==========
    
    $verifyExhibitions = $conn->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'exhibitions' 
         AND TABLE_SCHEMA = '$db_name' 
         AND COLUMN_NAME = 'event_video'"
    )->num_rows > 0;
    
    $verifyEvents = $conn->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'events' 
         AND TABLE_SCHEMA = '$db_name' 
         AND COLUMN_NAME = 'video_url'"
    )->num_rows > 0;
    
    $conn->close();
    
    // Final status
    $allGood = true;
    if ($exhibTableExists && !$verifyExhibitions) $allGood = false;
    if ($eventsTableExists && !$verifyEvents) $allGood = false;
    
    echo json_encode([
        'success' => $allGood,
        'message' => $allGood ? 'Migration completed successfully' : 'Migration completed with warnings',
        'details' => $results,
        'verification' => [
            'exhibitions_event_video_exists' => $verifyExhibitions,
            'events_video_url_exists' => $verifyEvents
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Migration failed',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
