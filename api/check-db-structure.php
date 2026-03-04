<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check events table structure
$result = $conn->query("DESCRIBE events");
$events_columns = [];
while ($row = $result->fetch_assoc()) {
    $events_columns[] = $row['Field'];
}

// Check if event_translations table exists
$result = $conn->query("SHOW TABLES LIKE 'event_translations'");
$translations_exists = $result->num_rows > 0;

// Check event_gallery table
$result = $conn->query("SHOW TABLES LIKE 'event_gallery'");
$gallery_exists = $result->num_rows > 0;

// Get sample event with translations
$result = $conn->query("SELECT * FROM events LIMIT 1");
$sample_event = $result->fetch_assoc();

$result = $conn->query("SELECT * FROM event_translations LIMIT 1");
$sample_translation = $result->fetch_assoc();

$result = $conn->query("SELECT * FROM event_gallery LIMIT 1");
$sample_gallery = $result->fetch_assoc();

echo json_encode([
    'events_columns' => $events_columns,
    'translations_table_exists' => $translations_exists,
    'gallery_table_exists' => $gallery_exists,
    'sample_event' => $sample_event,
    'sample_translation' => $sample_translation,
    'sample_gallery' => $sample_gallery
], JSON_PRETTY_PRINT);

$conn->close();
?>


