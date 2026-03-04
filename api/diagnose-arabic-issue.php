<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$event_id = $_GET['id'] ?? 22;

// Check if tables exist
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Check events table structure
$eventStructure = [];
$result = $conn->query("DESCRIBE events");
while ($row = $result->fetch_assoc()) {
    $eventStructure[] = $row;
}

// Check event_translations table structure
$translationStructure = [];
$result = $conn->query("DESCRIBE event_translations");
while ($row = $result->fetch_assoc()) {
    $translationStructure[] = $row;
}

// Get event data
$eventData = null;
$result = $conn->query("SELECT * FROM events WHERE id = $event_id");
if ($result && $result->num_rows > 0) {
    $eventData = $result->fetch_assoc();
}

// Get translations for this event
$translations = [];
$result = $conn->query("SELECT * FROM event_translations WHERE event_id = $event_id");
while ($row = $result->fetch_assoc()) {
    $translations[] = $row;
}

// Get gallery images
$gallery = [];
$result = $conn->query("SELECT * FROM event_gallery WHERE event_id = $event_id");
while ($row = $result->fetch_assoc()) {
    $gallery[] = $row;
}

echo json_encode([
    'tables_exist' => $tables,
    'event_id' => $event_id,
    'events_table_structure' => $eventStructure,
    'event_translations_table_structure' => $translationStructure,
    'event_data' => $eventData,
    'translations' => $translations,
    'gallery_images' => $gallery
], JSON_PRETTY_PRINT);

$conn->close();
?>


