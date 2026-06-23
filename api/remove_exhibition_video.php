<?php
/**
 * Remove video from exhibition - Simple dedicated endpoint
 */

header('Content-Type: application/json; charset=utf-8');

$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => $conn->connect_error]));
}

$conn->set_charset('utf8mb4');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$exhibition_id = isset($input['id']) ? intval($input['id']) : 0;

if (!$exhibition_id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Exhibition ID required']));
}

// Direct SQL - set event_video to NULL
$sql = "UPDATE exhibitions SET event_video = NULL WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param('i', $exhibition_id);

if (!$stmt->execute()) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]));
}

// Verify it was removed
$verify_sql = "SELECT event_video FROM exhibitions WHERE id = ?";
$verify_stmt = $conn->prepare($verify_sql);
$verify_stmt->bind_param('i', $exhibition_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();
$verify_row = $verify_result->fetch_assoc();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Video removed',
    'id' => $exhibition_id,
    'event_video_now' => $verify_row['event_video'],
    'is_null' => is_null($verify_row['event_video'])
]);

$stmt->close();
$verify_stmt->close();
$conn->close();
?>
