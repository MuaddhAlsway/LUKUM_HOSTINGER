<?php
/**
 * Quick database check - see what's actually in the database
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check events
    $eventsCount = $conn->query("SELECT COUNT(*) as c FROM events")->fetch_assoc()['c'];
    $events = $conn->query("SELECT id, title FROM events LIMIT 3")->fetch_all(MYSQLI_ASSOC);
    
    // Check blogs
    $blogsCount = $conn->query("SELECT COUNT(*) as c FROM blogs")->fetch_assoc()['c'];
    $blogs = $conn->query("SELECT id, title FROM blogs LIMIT 3")->fetch_all(MYSQLI_ASSOC);
    
    // Check press
    $pressCount = $conn->query("SELECT COUNT(*) as c FROM press")->fetch_assoc()['c'];
    $press = $conn->query("SELECT id, title FROM press LIMIT 3")->fetch_all(MYSQLI_ASSOC);
    
    // Check event 72 specifically
    $event72 = $conn->query("SELECT * FROM events WHERE id = 72")->fetch_assoc();
    
    echo json_encode([
        'events_total' => $eventsCount,
        'events_sample' => $events,
        'event_72_exists' => $event72 ? true : false,
        'event_72' => $event72,
        'blogs_total' => $blogsCount,
        'blogs_sample' => $blogs,
        'press_total' => $pressCount,
        'press_sample' => $press,
        'database_connected' => true
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'database_connected' => false
    ]);
}
?>
