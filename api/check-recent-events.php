<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get recent events
$query = "SELECT id, title, created_at FROM events ORDER BY id DESC LIMIT 10";
$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// For each event, check if it has translations
$tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
$tableCheckResult = $conn->query($tableCheckQuery);
$translationsTableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;

if ($translationsTableExists) {
    foreach ($events as &$event) {
        $transQuery = "SELECT language, title, description, location FROM event_translations WHERE event_id = " . $event['id'];
        $transResult = $conn->query($transQuery);
        
        $translations = [];
        while ($row = $transResult->fetch_assoc()) {
            $translations[$row['language']] = [
                'title' => $row['title'],
                'description' => $row['description'],
                'location' => $row['location']
            ];
        }
        
        $event['translations'] = $translations;
    }
}

$conn->close();

echo json_encode([
    'translations_table_exists' => $translationsTableExists,
    'recent_events' => $events
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
