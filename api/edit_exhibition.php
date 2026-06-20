<?php
/**
 * Edit Exhibition API
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

// Build update query dynamically
$updateFields = [];
$params = [];
$types = '';

$fields = [
    'title_en', 'title_ar', 'description_en', 'description_ar',
    'location_en', 'location_ar', 'exhibition_date', 'exhibition_time',
    'exhibition_end_time', 'end_date', 'cover_image'
];

foreach ($fields as $field) {
    if (isset($input[$field])) {
        $updateFields[] = "$field = ?";
        $params[] = $input[$field];
        $types .= 's';
    }
}

if (empty($updateFields)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$params[] = $id;
$types .= 'i';

$sql = "UPDATE exhibitions SET " . implode(', ', $updateFields) . " WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    die(json_encode([
        'success' => true,
        'message' => 'Exhibition updated successfully'
    ]));
} else {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]));
}

?>
