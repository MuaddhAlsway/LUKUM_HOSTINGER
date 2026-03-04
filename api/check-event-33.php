<?php
/**
 * Check if Event 33 exists in database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        echo json_encode([
            'database_connected' => false,
            'error' => $conn->connect_error
        ]);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if events table exists
    $result = $conn->query("SHOW TABLES LIKE 'events'");
    $table_exists = $result->num_rows > 0;
    
    $event_33 = null;
    $all_events = [];
    
    if ($table_exists) {
        // Get event 33
        $result = $conn->query("SELECT * FROM events WHERE id = 33");
        if ($result && $result->num_rows > 0) {
            $event_33 = $result->fetch_assoc();
        }
        
        // Get all events
        $result = $conn->query("SELECT id, title, cover_image FROM events ORDER BY id DESC LIMIT 10");
        while ($row = $result->fetch_assoc()) {
            $all_events[] = $row;
        }
    }
    
    $conn->close();
    
    echo json_encode([
        'database_connected' => true,
        'table_exists' => $table_exists,
        'event_33' => $event_33,
        'recent_events' => $all_events
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>


