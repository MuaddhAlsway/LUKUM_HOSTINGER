<?php
/**
 * Debug gallery images - check what's in the database
 */

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

// Check if table exists
$tableCheck = "SHOW TABLES LIKE 'event_gallery'";
$tableResult = $conn->query($tableCheck);
$tableExists = $tableResult && $tableResult->num_rows > 0;

// Get all gallery images
$allImages = [];
if ($tableExists) {
    $query = "SELECT * FROM event_gallery ORDER BY event_id DESC LIMIT 20";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $allImages[] = $row;
    }
}

// Get images for event 38
$event38Images = [];
if ($tableExists) {
    $query = "SELECT * FROM event_gallery WHERE event_id = 38";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $event38Images[] = $row;
    }
}

// Check upload directory
$uploadDir = '../assest/gallery/';
$dirExists = is_dir($uploadDir);
$files = [];
if ($dirExists) {
    $files = array_diff(scandir($uploadDir), ['.', '..']);
}

$conn->close();

echo json_encode([
    'table_exists' => $tableExists,
    'total_images_in_db' => count($allImages),
    'all_images' => $allImages,
    'event_38_images' => $event38Images,
    'upload_dir_exists' => $dirExists,
    'files_in_upload_dir' => array_values($files)
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

