<?php
header('Content-Type: application/json');

$event_id = (int)($_GET['id'] ?? 0);

if (!$event_id) {
    echo json_encode(['error' => 'Event ID required']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get event from events table
$eventQuery = "SELECT * FROM events WHERE id = $event_id";
$eventResult = $conn->query($eventQuery);

$response = [
    'event_id' => $event_id,
    'event_exists' => $eventResult && $eventResult->num_rows > 0
];

if ($response['event_exists']) {
    $response['event'] = $eventResult->fetch_assoc();
}

// Check translations table
$tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
$tableCheckResult = $conn->query($tableCheckQuery);
$translationsTableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;

$response['translations_table_exists'] = $translationsTableExists;

if ($translationsTableExists) {
    // Get all translations for this event
    $transQuery = "SELECT * FROM event_translations WHERE event_id = $event_id";
    $transResult = $conn->query($transQuery);
    
    $translations = [];
    while ($row = $transResult->fetch_assoc()) {
        $translations[] = $row;
    }
    
    $response['translations'] = $translations;
    $response['translation_count'] = count($translations);
}

$conn->close();

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>


