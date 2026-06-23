<?php
/**
 * Add video to AMPM Exhibition (ID 76)
 * This will update the exhibition to have a video
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Video URL to add
    $videoUrl = 'https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm';
    $exhibitionId = 76;
    
    // Update exhibitions table with video
    $updateQuery = 'UPDATE exhibitions SET event_video = ? WHERE id = ? LIMIT 1';
    $stmt = $db->prepare($updateQuery);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('si', $videoUrl, $exhibitionId);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Video added to AMPM exhibition',
            'exhibition_id' => $exhibitionId,
            'video_url' => $videoUrl,
            'affected_rows' => $stmt->affected_rows
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Exhibition not found or no changes made',
            'exhibition_id' => $exhibitionId
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
