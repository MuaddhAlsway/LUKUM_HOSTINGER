<?php
/**
 * Delete Exhibition API - Delete exhibition by ID
 */

header('Content-Type: application/json; charset=utf-8');

// Database credentials
$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

// Connect
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');

// Get JSON input
$json_input = file_get_contents('php://input');
$input = json_decode($json_input, true);

if ($input === null) {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'Invalid JSON input'
    ]));
}

// Get ID
$id = isset($input['id']) ? intval($input['id']) : 0;

if (!$id) {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'Exhibition ID is required'
    ]));
}

// Delete exhibition
$sql = "DELETE FROM exhibitions WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error
    ]));
}

$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Exhibition deleted successfully'
    ]);
} else {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Delete failed: ' . $stmt->error
    ]));
}

$conn->close();

?>
