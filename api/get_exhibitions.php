<?php
/**
 * Get Exhibitions API - Get all exhibitions
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

$type = $_GET['type'] ?? 'all';
$limit = (int)($_GET['limit'] ?? 1000);

// Get all exhibitions
$sql = "SELECT * FROM exhibitions ORDER BY exhibition_date DESC LIMIT ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error
    ]));
}

$stmt->bind_param('i', $limit);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $exhibitions = [];
    
    while ($row = $result->fetch_assoc()) {
        $exhibitions[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $exhibitions,
        'count' => count($exhibitions),
        'debug' => [
            'limit' => $limit,
            'type' => $type,
            'today' => date('Y-m-d')
        ]
    ]);
    exit;
} else {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]));
}

?>

