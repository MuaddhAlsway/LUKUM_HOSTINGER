<?php
/**
 * Test script to verify event ID 55 exists in database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }
    
    // Get event 55
    $query = 'SELECT id, title, description, location, event_date, event_time, event_end_time, end_date, cover_image, video_url, category FROM events WHERE id = 55';
    $result = $db->getConnection()->query($query);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Query failed: ' . $db->getConnection()->error
        ]);
        exit;
    }
    
    $event = $result->fetch_assoc();
    
    if (!$event) {
        echo json_encode([
            'success' => false,
            'message' => 'Event ID 55 not found in database'
        ]);
        exit;
    }
    
    // Get gallery images for event 55
    $galleryQuery = 'SELECT id, event_id, image_url FROM event_gallery WHERE event_id = 55 ORDER BY display_order ASC';
    $galleryResult = $db->getConnection()->query($galleryQuery);
    
    $gallery = [];
    if ($galleryResult) {
        while ($row = $galleryResult->fetch_assoc()) {
            $gallery[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Event 55 found in database',
        'event' => $event,
        'gallery' => $gallery,
        'gallery_count' => count($gallery)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

