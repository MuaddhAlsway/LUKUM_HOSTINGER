<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$event_id = $_GET['id'] ?? 1;

// Check events table
$result = $conn->query("SELECT * FROM events WHERE id = $event_id");
$event = $result->fetch_assoc();

// Check event_translations table
$result = $conn->query("SELECT * FROM event_translations WHERE event_id = $event_id");
$translations = [];
while ($row = $result->fetch_assoc()) {
    $translations[] = $row;
}

// Check event_gallery table
$result = $conn->query("SELECT * FROM event_gallery WHERE event_id = $event_id");
$gallery = [];
while ($row = $result->fetch_assoc()) {
    $gallery[] = $row;
}

echo json_encode([
    'event_id' => $event_id,
    'events_table' => $event,
    'event_translations_table' => $translations,
    'event_gallery_table' => $gallery
], JSON_PRETTY_PRINT);

$conn->close();
?>

