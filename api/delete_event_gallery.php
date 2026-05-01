<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit;
}

// Safe require — return JSON error instead of 500 if files missing
foreach (['db.php', 'config.php'] as $f) {
    $path = __DIR__ . '/' . $f;
    if (!file_exists($path)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Server config missing: $f"]);
        exit;
    }
}

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

    // Get image details before deleting
    $stmt = $db->prepare('SELECT image_url FROM event_gallery WHERE id = ?');
    if (!$stmt) throw new Exception('Query prepare failed');
    $stmt->bind_param('i', $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    if (!$image) {
        throw new Exception('Image not found in database');
    }

    // Delete physical file if it exists
    $filePath = __DIR__ . '/../' . ltrim($image['image_url'], '/');
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete database record
    $deleteStmt = $db->prepare('DELETE FROM event_gallery WHERE id = ?');
    if (!$deleteStmt) throw new Exception('Delete prepare failed');
    $deleteStmt->bind_param('i', $imageId);

    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to delete from database: ' . $deleteStmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
