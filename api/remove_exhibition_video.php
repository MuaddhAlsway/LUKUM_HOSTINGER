<?php
/**
 * Remove video from exhibition - Update both event_video and video_url fields
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Database connection failed']));
    }
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $exhibition_id = isset($input['id']) ? intval($input['id']) : 0;
    
    if (!$exhibition_id) {
        http_response_code(400);
        die(json_encode(['success' => false, 'error' => 'Exhibition ID required']));
    }
    
    error_log("DEBUG: Removing video from exhibition ID: $exhibition_id");
    
    // Update exhibitions table - set event_video to NULL
    $sql = "UPDATE exhibitions SET event_video = NULL WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Prepare failed: ' . $db->getConnection()->error]));
    }
    
    $stmt->bind_param('i', $exhibition_id);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]));
    }
    
    $affected_rows = $stmt->affected_rows;
    error_log("DEBUG: UPDATE exhibitions - affected rows: $affected_rows");
    $stmt->close();
    
    // Also update events table video_url if exists (for cross-table consistency)
    $events_sql = "UPDATE events SET video_url = NULL WHERE id = ?";
    $events_stmt = $db->prepare($events_sql);
    
    if ($events_stmt) {
        $events_stmt->bind_param('i', $exhibition_id);
        $events_stmt->execute();
        $events_affected = $events_stmt->affected_rows;
        error_log("DEBUG: UPDATE events - affected rows: $events_affected");
        $events_stmt->close();
    }
    
    // Verify deletion from exhibitions
    $verify_sql = "SELECT event_video FROM exhibitions WHERE id = ?";
    $verify_stmt = $db->prepare($verify_sql);
    $verify_stmt->bind_param('i', $exhibition_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $verify_row = $verify_result->fetch_assoc();
    
    error_log("DEBUG: VERIFIED - event_video in DB is now: " . var_export($verify_row['event_video'], true));
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Video removed successfully',
        'id' => $exhibition_id,
        'event_video_now' => $verify_row['event_video'],
        'is_null' => is_null($verify_row['event_video']),
        'affected_rows' => $affected_rows
    ]);
    
    $verify_stmt->close();
    
} catch (Exception $e) {
    error_log('Error in remove_exhibition_video: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
