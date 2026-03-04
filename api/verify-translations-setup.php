<?php
/**
 * Verify translations table setup
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

// Get total events
$eventsCountQuery = "SELECT COUNT(*) as count FROM events";
$eventsCountResult = $conn->query($eventsCountQuery);
$total_events = $eventsCountResult->fetch_assoc()['count'];

// Get total translations
$translationsCountQuery = "SELECT COUNT(*) as count FROM event_translations";
$translationsCountResult = $conn->query($translationsCountQuery);
$total_translations = $tableExists ? $translationsCountResult->fetch_assoc()['count'] : 0;

// Get events with translations
$eventsWithTransQuery = "
    SELECT COUNT(DISTINCT event_id) as count 
    FROM event_translations
";
$eventsWithTransResult = $conn->query($eventsWithTransQuery);
$events_with_translations = $tableExists ? $eventsWithTransResult->fetch_assoc()['count'] : 0;

// Get sample data
$sampleQuery = "
    SELECT 
        e.id,
        e.title as title_en,
        et_en.title as trans_title_en,
        et_ar.title as trans_title_ar
    FROM events e
    LEFT JOIN event_translations et_en ON e.id = et_en.event_id AND et_en.language = 'en'
    LEFT JOIN event_translations et_ar ON e.id = et_ar.event_id AND et_ar.language = 'ar'
    LIMIT 5
";
$sampleResult = $conn->query($sampleQuery);
$samples = [];
while ($row = $sampleResult->fetch_assoc()) {
    $samples[] = $row;
}

$conn->close();

echo json_encode([
    'table_exists' => $tableExists,
    'total_events' => $total_events,
    'total_translations' => $total_translations,
    'events_with_translations' => $events_with_translations,
    'sample_data' => $samples
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

