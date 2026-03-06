<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Get all events with their slugs
    $query = 'SELECT id, title, slug FROM events ORDER BY id LIMIT 10';
    $result = $db->getConnection()->query($query);
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events,
        'total' => count($events)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
