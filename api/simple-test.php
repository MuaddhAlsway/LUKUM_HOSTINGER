<?php
header('Content-Type: application/json');

// Direct connection test
$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Test 1: Check if blogs table exists
$result = $conn->query("SHOW TABLES LIKE 'blogs'");
if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit;
}

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Blogs table does not exist']);
    exit;
}

// Test 2: Get blogs
$result = $conn->query("SELECT id, title, excerpt, content, author, category, cover_image, created_at FROM blogs LIMIT 5");

if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit;
}

$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($blogs),
    'blogs' => $blogs
]);

$conn->close();

