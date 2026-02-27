<?php
/**
 * Populate event_translations table with existing event data
 */

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if table exists
$tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
$tableCheckResult = $conn->query($tableCheckQuery);
$tableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;

if (!$tableExists) {
    echo json_encode(['error' => 'event_translations table does not exist. Create it first.']);
    exit;
}

// Get all events
$eventsQuery = "SELECT id, title, description, location FROM events";
$eventsResult = $conn->query($eventsQuery);

if (!$eventsResult) {
    echo json_encode(['error' => 'Failed to fetch events: ' . $conn->error]);
    exit;
}

$events_processed = 0;
$translations_created = 0;

while ($event = $eventsResult->fetch_assoc()) {
    $event_id = $event['id'];
    $title = $conn->real_escape_string($event['title']);
    $description = $conn->real_escape_string($event['description']);
    $location = $conn->real_escape_string($event['location']);
    
    // Insert English translation
    $insertEnglishQuery = "
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES ($event_id, 'en', '$title', '$description', '$location')
        ON DUPLICATE KEY UPDATE
            title = '$title',
            description = '$description',
            location = '$location'
    ";
    
    if ($conn->query($insertEnglishQuery)) {
        $translations_created++;
    }
    
    // Insert empty Arabic translation (user can fill in later)
    $insertArabicQuery = "
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES ($event_id, 'ar', '', '', '')
        ON DUPLICATE KEY UPDATE
            title = '',
            description = '',
            location = ''
    ";
    
    if ($conn->query($insertArabicQuery)) {
        $translations_created++;
    }
    
    $events_processed++;
}

$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Translations populated successfully',
    'events_processed' => $events_processed,
    'translations_created' => $translations_created
]);
?>
