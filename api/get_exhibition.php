<?php
/**
 * Get Single Exhibition API
 */

require_once 'db.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid exhibition ID']));
}

$sql = "SELECT * FROM exhibitions WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $exhibition = $result->fetch_assoc();
    
    if ($exhibition) {
        die(json_encode([
            'success' => true,
            'data' => $exhibition
        ]));
    } else {
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'Exhibition not found']));
    }
} else {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]));
}

?>
