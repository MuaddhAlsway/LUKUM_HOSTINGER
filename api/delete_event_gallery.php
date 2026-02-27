<?php
/**
 * LAKUM Artspace - Delete Event Gallery Image API
 * Handles deleting event gallery images
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $imageId = (int)($input['id'] ?? 0);
    
    if (!$imageId) {
        throw new Exception('Image ID is required');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Get image details
    $stmt = $db->prepare('SELECT image_url FROM event_gallery WHERE id = ?');
    $stmt->bind_param('i', $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    
    if (!$image) {
        throw new Exception('Image not found');
    }
    
    // Delete file from server
    $filePath = '../' . $image['image_url'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Delete from database
    $deleteStmt = $db->prepare('DELETE FROM event_gallery WHERE id = ?');
    $deleteStmt->bind_param('i', $imageId);
    
    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to delete image from database');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
