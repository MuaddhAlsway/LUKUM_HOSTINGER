<?php
/**
 * Get Exhibitions API
 */

require_once 'db.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$type = $_GET['type'] ?? 'all';
$limit = (int)($_GET['limit'] ?? 1000);

$sql = "SELECT * FROM exhibitions ORDER BY exhibition_date DESC LIMIT ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param('i', $limit);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $exhibitions = [];
    
    while ($row = $result->fetch_assoc()) {
        $exhibitions[] = $row;
    }
    
    die(json_encode([
        'success' => true,
        'data' => $exhibitions,
        'count' => count($exhibitions)
    ]));
} else {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]));
}

?>
