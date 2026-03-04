<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if event_translations table exists
$tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
$tableCheckResult = $conn->query($tableCheckQuery);
$translationsTableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;

$response = [
    'event_translations_exists' => $translationsTableExists
];

if ($translationsTableExists) {
    // Get table structure
    $structureQuery = "DESCRIBE event_translations";
    $structureResult = $conn->query($structureQuery);
    
    $structure = [];
    while ($row = $structureResult->fetch_assoc()) {
        $structure[] = $row;
    }
    
    $response['event_translations_structure'] = $structure;
    
    // Get sample data
    $sampleQuery = "SELECT * FROM event_translations LIMIT 5";
    $sampleResult = $conn->query($sampleQuery);
    
    $samples = [];
    while ($row = $sampleResult->fetch_assoc()) {
        $samples[] = $row;
    }
    
    $response['sample_translations'] = $samples;
    $response['total_translations'] = $conn->query("SELECT COUNT(*) as count FROM event_translations")->fetch_assoc()['count'];
}

// Also check events table
$eventsStructureQuery = "DESCRIBE events";
$eventsStructureResult = $conn->query($eventsStructureQuery);

$eventsStructure = [];
while ($row = $eventsStructureResult->fetch_assoc()) {
    $eventsStructure[] = $row;
}

$response['events_structure'] = $eventsStructure;
$response['total_events'] = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];

$conn->close();

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>


