<?php
/**
 * Debug script to check what's being saved to database
 */

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the most recent event
$query = "SELECT * FROM events ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['error' => 'No events found']);
    exit;
}

$event = $result->fetch_assoc();
$event_id = $event['id'];

// Get translations for this event
$transQuery = "SELECT * FROM event_translations WHERE event_id = $event_id";
$transResult = $conn->query($transQuery);

$translations = [];
while ($row = $transResult->fetch_assoc()) {
    $translations[] = $row;
}

// Get gallery images
$galleryQuery = "SELECT * FROM event_gallery WHERE event_id = $event_id";
$galleryResult = $conn->query($galleryQuery);

$gallery = [];
while ($row = $galleryResult->fetch_assoc()) {
    $gallery[] = $row;
}

$conn->close();

echo json_encode([
    'event_id' => $event_id,
    'event' => $event,
    'translations' => $translations,
    'gallery_count' => count($gallery),
    'gallery' => $gallery
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>


