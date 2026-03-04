<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

// Get the latest event
$eventQuery = "SELECT id FROM events ORDER BY id DESC LIMIT 1";
$eventResult = $conn->query($eventQuery);
$latestEvent = $eventResult->fetch_assoc();
$eventId = $latestEvent['id'] ?? 0;

// Get gallery images for this event
$galleryQuery = "SELECT * FROM event_gallery WHERE event_id = $eventId ORDER BY display_order";
$galleryResult = $conn->query($galleryQuery);

$images = [];
while ($row = $galleryResult->fetch_assoc()) {
    $images[] = $row;
}

// Check upload directory
$uploadDir = '../assest/gallery/';
$files = scandir($uploadDir);
$eventFiles = array_filter($files, function($f) use ($eventId) {
    return strpos($f, 'event_' . $eventId) === 0;
});

echo json_encode([
    'latest_event_id' => $eventId,
    'gallery_images_in_db' => count($images),
    'gallery_images' => $images,
    'files_in_upload_dir' => count($eventFiles),
    'files_list' => array_values($eventFiles)
]);

$conn->close();
?>

