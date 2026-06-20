<?php
/**
 * Add Exhibition API
 */

require_once 'db.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if JSON decode failed
if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

// Validate required fields
$title_en = $input['title_en'] ?? '';
$exhibition_date = $input['exhibition_date'] ?? '';
$location_en = $input['location_en'] ?? '';

if (!$title_en || !$exhibition_date || !$location_en) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields: title_en, exhibition_date, location_en']);
    exit;
}

$title_ar = $input['title_ar'] ?? '';
$description_en = $input['description_en'] ?? '';
$description_ar = $input['description_ar'] ?? '';
$location_ar = $input['location_ar'] ?? '';
$exhibition_time = $input['exhibition_time'] ?? '10:00:00';
$exhibition_end_time = $input['exhibition_end_time'] ?? '18:00:00';
$end_date = $input['end_date'] ?? null;
$cover_image = $input['cover_image'] ?? 'assest/img-4.png';
$category = 'exhibition';

// Insert into database
$sql = "INSERT INTO exhibitions (
    title_en, title_ar, description_en, description_ar,
    location_en, location_ar, exhibition_date, exhibition_time,
    exhibition_end_time, end_date, cover_image, category
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    'ssssssssssss',
    $title_en, $title_ar, $description_en, $description_ar,
    $location_en, $location_ar, $exhibition_date, $exhibition_time,
    $exhibition_end_time, $end_date, $cover_image, $category
);

if ($stmt->execute()) {
    $exhibition_id = $stmt->insert_id;
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Exhibition created successfully',
        'exhibition_id' => $exhibition_id
    ]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database execution failed: ' . $stmt->error]);
    exit;
}

?>
