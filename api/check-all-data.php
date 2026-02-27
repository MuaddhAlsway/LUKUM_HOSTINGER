<?php
/**
 * Check all data in database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Count all tables
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    $events_count = $row['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $row = $result->fetch_assoc();
    $blogs_count = $row['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM press");
    $row = $result->fetch_assoc();
    $press_count = $row['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM pricing");
    $row = $result->fetch_assoc();
    $pricing_count = $row['count'];
    
    $conn->close();
    
    echo json_encode([
        'events' => $events_count,
        'blogs' => $blogs_count,
        'press' => $press_count,
        'pricing' => $pricing_count
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
