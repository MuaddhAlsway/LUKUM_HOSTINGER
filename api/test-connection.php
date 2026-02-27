<?php
header('Content-Type: application/json');

// Test 1: Basic connection
$conn = @new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    die(json_encode([
        'status' => 'ERROR',
        'message' => 'Connection failed: ' . $conn->connect_error
    ]));
}

// Test 2: Check blogs table
$result = $conn->query("SELECT COUNT(*) as count FROM blogs");
if (!$result) {
    die(json_encode([
        'status' => 'ERROR',
        'message' => 'Query failed: ' . $conn->error
    ]));
}

$row = $result->fetch_assoc();

echo json_encode([
    'status' => 'SUCCESS',
    'database' => 'lakum_artspace',
    'blogs_count' => $row['count'],
    'connection' => 'OK'
]);

$conn->close();
?>
