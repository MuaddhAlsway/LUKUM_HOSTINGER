<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit;
}

// Direct DB connection — no dependency on db.php or config.php
$conn = new mysqli('localhost', 'u812122863_neama', 'Nema202610!LakumDB', 'u812122863_lakum_artspace');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}
$conn->set_charset('utf8mb4');

$input = json_decode(file_get_contents('php://input'), true);
$imageId = (int)($input['id'] ?? 0);

if (!$imageId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Image ID is required']);
    exit;
}

// Get image path before deleting
$stmt = $conn->prepare('SELECT image_url FROM event_gallery WHERE id = ?');
$stmt->bind_param('i', $imageId);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
$stmt->close();

if (!$image) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Image not found']);
    exit;
}

// Delete physical file if it exists
$filePath = __DIR__ . '/../' . ltrim($image['image_url'], '/');
if (file_exists($filePath)) {
    unlink($filePath);
}

// Delete from database
$del = $conn->prepare('DELETE FROM event_gallery WHERE id = ?');
$del->bind_param('i', $imageId);

if ($del->execute()) {
    echo json_encode(['success' => true, 'message' => 'Image deleted']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $del->error]);
}

$del->close();
$conn->close();
?>
