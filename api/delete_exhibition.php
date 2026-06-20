<?php
/**
 * Delete Exhibition API
 */

require_once 'db.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid exhibition ID']));
}

$sql = "DELETE FROM exhibitions WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    die(json_encode([
        'success' => true,
        'message' => 'Exhibition deleted successfully'
    ]));
} else {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]));
}

?>
