<?php
/**
 * SIMPLIFIED Get Event Details API - Debug Version
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    require_once 'config.php';
    
    $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
    error_log("SIMPLE API: Requested ID = $eventId");
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database not connected');
    }
    
    error_log("SIMPLE API: Database connected");
    
    // Check exhibitions table first
    $exhibitionQuery = "SELECT id, title_en, title_ar, description_en, description_ar, location_en, location_ar, exhibition_date, exhibition_time, exhibition_end_time, end_date, event_video, gallery_images FROM exhibitions WHERE id = ? LIMIT 1";
    $exhibitionStmt = $db->prepare($exhibitionQuery);
    
    if (!$exhibitionStmt) {
        error_log("SIMPLE API: Exhibition prepare failed: " . $db->getConnection()->error);
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $exhibitionStmt->bind_param('i', $eventId);
    
    if (!$exhibitionStmt->execute()) {
        error_log("SIMPLE API: Exhibition execute failed: " . $exhibitionStmt->error);
        throw new Exception('Execute failed: ' . $exhibitionStmt->error);
    }
    
    $exhibitionResult = $exhibitionStmt->get_result();
    $event = $exhibitionResult->fetch_assoc();
    
    if ($event) {
        error_log("SIMPLE API: Found in exhibitions table");
        
        // Format exhibition data like events
        $event = array(
            'id' => $event['id'],
            'title' => $event['title_en'],
            'title_en' => $event['title_en'],
            'title_ar' => $event['title_ar'],
            'description' => $event['description_en'],
            'description_en' => $event['description_en'],
            'description_ar' => $event['description_ar'],
            'location' => $event['location_en'],
            'location_en' => $event['location_en'],
            'location_ar' => $event['location_ar'],
            'event_date' => $event['exhibition_date'],
            'event_time' => $event['exhibition_time'],
            'event_end_time' => $event['exhibition_end_time'],
            'end_date' => $event['end_date'],
            'video_url' => $event['event_video'],
            'event_video' => $event['event_video'],
            'gallery_images' => $event['gallery_images'],
            'category' => 'exhibition'
        );
    } else {
        error_log("SIMPLE API: Not in exhibitions, checking events table");
        
        // Check events table
        $eventQuery = "SELECT id, title, title_en, title_ar, description, description_en, description_ar, location, location_en, location_ar, event_date, event_time, event_end_time, end_date, video_url FROM events WHERE id = ? LIMIT 1";
        $eventStmt = $db->prepare($eventQuery);
        
        if (!$eventStmt) {
            error_log("SIMPLE API: Event prepare failed: " . $db->getConnection()->error);
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $eventStmt->bind_param('i', $eventId);
        
        if (!$eventStmt->execute()) {
            error_log("SIMPLE API: Event execute failed: " . $eventStmt->error);
            throw new Exception('Execute failed: ' . $eventStmt->error);
        }
        
        $eventResult = $eventStmt->get_result();
        $event = $eventResult->fetch_assoc();
        
        if (!$event) {
            error_log("SIMPLE API: Not found in either table");
            throw new Exception('Event/Exhibition not found with ID: ' . $eventId);
        }
        
        error_log("SIMPLE API: Found in events table");
        
        // Normalize event data - ensure all expected fields exist
        $event = array(
            'id' => $event['id'],
            'title' => $event['title_en'] ?? $event['title'] ?? '',
            'title_en' => $event['title_en'] ?? $event['title'] ?? '',
            'title_ar' => $event['title_ar'] ?? '',
            'description' => $event['description_en'] ?? $event['description'] ?? '',
            'description_en' => $event['description_en'] ?? $event['description'] ?? '',
            'description_ar' => $event['description_ar'] ?? '',
            'location' => $event['location_en'] ?? $event['location'] ?? '',
            'location_en' => $event['location_en'] ?? $event['location'] ?? '',
            'location_ar' => $event['location_ar'] ?? '',
            'event_date' => $event['event_date'] ?? '',
            'event_time' => $event['event_time'] ?? '',
            'event_end_time' => $event['event_end_time'] ?? '',
            'end_date' => $event['end_date'] ?? '',
            'video_url' => $event['video_url'] ?? '',
            'event_video' => $event['video_url'] ?? ''
        );
    }
    
    error_log("SIMPLE API: Returning event: " . json_encode($event));
    
    echo json_encode([
        'success' => true,
        'data' => $event,
        'event' => $event,
        'gallery' => [],
        'source' => 'database'
    ]);
    
} catch (Exception $e) {
    error_log('SIMPLE API Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
