<?php
/**
 * Test Events API - Debug Database Connection
 */

header('Content-Type: application/json');

// Load config
if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    require_once 'db.php';
}

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'config' => [
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
    ],
    'database' => [],
    'events' => []
];

try {
    $db = Database::getInstance();
    
    $response['database']['connected'] = $db->isConnected();
    
    if ($db->isConnected()) {
        // Check if events table exists
        $result = $db->query("SHOW TABLES LIKE 'events'");
        $response['database']['events_table_exists'] = $result && $result->num_rows > 0;
        
        // Count events
        $result = $db->query("SELECT COUNT(*) as count FROM events");
        if ($result) {
            $row = $result->fetch_assoc();
            $response['database']['total_events'] = $row['count'];
        }
        
        // Get sample events
        $result = $db->query("SELECT id, title, event_date, location FROM events LIMIT 5");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['events'][] = $row;
            }
        }
    } else {
        $response['database']['error'] = 'Not connected to database';
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>


