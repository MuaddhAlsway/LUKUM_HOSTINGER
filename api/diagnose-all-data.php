<?php
/**
 * Diagnostic script to check all data in database
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Check events
    $eventsQuery = "SELECT COUNT(*) as count FROM events";
    $eventsResult = $conn->query($eventsQuery);
    $eventsCount = $eventsResult->fetch_assoc()['count'];
    
    // Get sample events
    $sampleEventsQuery = "SELECT id, title, event_date FROM events LIMIT 5";
    $sampleEventsResult = $conn->query($sampleEventsQuery);
    $sampleEvents = [];
    while ($row = $sampleEventsResult->fetch_assoc()) {
        $sampleEvents[] = $row;
    }
    
    // Check blogs
    $blogsQuery = "SELECT COUNT(*) as count FROM blogs";
    $blogsResult = $conn->query($blogsQuery);
    $blogsCount = $blogsResult->fetch_assoc()['count'];
    
    // Get sample blogs
    $sampleBlogsQuery = "SELECT id, title, created_at FROM blogs LIMIT 5";
    $sampleBlogsResult = $conn->query($sampleBlogsQuery);
    $sampleBlogs = [];
    while ($row = $sampleBlogsResult->fetch_assoc()) {
        $sampleBlogs[] = $row;
    }
    
    // Check press
    $pressQuery = "SELECT COUNT(*) as count FROM press";
    $pressResult = $conn->query($pressQuery);
    $pressCount = $pressResult->fetch_assoc()['count'];
    
    // Get sample press
    $samplePressQuery = "SELECT id, title, press_date FROM press LIMIT 5";
    $samplePressResult = $conn->query($samplePressQuery);
    $samplePress = [];
    while ($row = $samplePressResult->fetch_assoc()) {
        $samplePress[] = $row;
    }
    
    // Check event 72 specifically
    $event72Query = "SELECT * FROM events WHERE id = 72";
    $event72Result = $conn->query($event72Query);
    $event72 = $event72Result->fetch_assoc();
    
    echo json_encode([
        'events_total' => $eventsCount,
        'events_sample' => $sampleEvents,
        'event_72' => $event72,
        'blogs_total' => $blogsCount,
        'blogs_sample' => $sampleBlogs,
        'press_total' => $pressCount,
        'press_sample' => $samplePress
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
