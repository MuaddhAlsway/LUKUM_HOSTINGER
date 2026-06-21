<?php
/**
 * Get Single Exhibition API - Get exhibition by ID
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

// Get ID from query parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'Exhibition ID is required'
    ]));
}

// Get exhibition
$sql = "SELECT * FROM exhibitions WHERE id = ?";

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
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $exhibition = $result->fetch_assoc();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $exhibition
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Exhibition not found'
        ]);
    }
    
    $stmt->close();
    exit;
} else {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]));
}

$conn->close();

?>
