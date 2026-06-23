<?php
/**
 * Add video to Event ID 76 (AMPM)
 * This will update the events table with a video
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Video URL to add
    $videoUrl = 'https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm';
    $eventId = 76;
    
    // First check if it's in events or exhibitions table
    $checkEvents = "SELECT id FROM events WHERE id = ? LIMIT 1";
    $stmt_check = $db->prepare($checkEvents);
    $stmt_check->bind_param('i', $eventId);
    $stmt_check->execute();
    $eventExists = $stmt_check->get_result()->fetch_assoc();
    
    if ($eventExists) {
        // Update events table with video
        $updateQuery = 'UPDATE events SET video_url = ? WHERE id = ? LIMIT 1';
        $stmt = $db->prepare($updateQuery);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('si', $videoUrl, $eventId);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Video added to Event ID 76',
                'table' => 'events',
                'event_id' => $eventId,
                'video_url' => $videoUrl,
                'affected_rows' => $stmt->affected_rows
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Event found but no changes made',
                'table' => 'events',
                'event_id' => $eventId
            ]);
        }
    } else {
        // Try exhibitions table
        $checkExhibitions = "SELECT id FROM exhibitions WHERE id = ? LIMIT 1";
        $stmt_check2 = $db->prepare($checkExhibitions);
        $stmt_check2->bind_param('i', $eventId);
        $stmt_check2->execute();
        $exhibitionExists = $stmt_check2->get_result()->fetch_assoc();
        
        if ($exhibitionExists) {
            // Update exhibitions table
            $updateQuery = 'UPDATE exhibitions SET event_video = ? WHERE id = ? LIMIT 1';
            $stmt = $db->prepare($updateQuery);
            
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $db->getConnection()->error);
            }
            
            $stmt->bind_param('si', $videoUrl, $eventId);
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Video added to Exhibition ID 76',
                    'table' => 'exhibitions',
                    'event_id' => $eventId,
                    'video_url' => $videoUrl,
                    'affected_rows' => $stmt->affected_rows
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Exhibition found but no changes made',
                    'table' => 'exhibitions',
                    'event_id' => $eventId
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Event ID 76 not found in either events or exhibitions table',
                'event_id' => $eventId
            ]);
        }
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

