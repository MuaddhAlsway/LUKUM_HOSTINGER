<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// First, check if blog exists
$checkQuery = "SELECT id FROM blogs WHERE id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param('i', $blog_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

$exists = $checkResult->num_rows > 0;

// Get all blogs to see what's in the database
$allQuery = "SELECT id, title FROM blogs ORDER BY id";
$allResult = $conn->query($allQuery);
$allBlogs = [];
while ($row = $allResult->fetch_assoc()) {
    $allBlogs[] = $row;
}

// Try to get the specific blog
$query = "SELECT * FROM blogs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->num_rows > 0 ? $result->fetch_assoc() : null;

echo json_encode([
    'requested_id' => $blog_id,
    'blog_exists' => $exists,
    'blog_data' => $blog,
    'all_blogs' => $allBlogs,
    'total_blogs' => count($allBlogs)
]);

$conn->close();
?>


