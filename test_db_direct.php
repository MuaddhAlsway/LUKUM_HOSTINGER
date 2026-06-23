<?php
/**
 * Direct database test - no curl, just database queries
 */

require_once 'api/config.php';

$db = Database::getInstance();

echo "=== DATABASE TEST ===\n\n";

// Test Exhibition ID 3
echo "TEST 1: Exhibition ID 3 Direct Query\n";
$sql = "SELECT id, title_en, event_video FROM exhibitions WHERE id = 3";
$result = $db->getConnection()->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Result: " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "Query Error: " . $db->getConnection()->error . "\n\n";
}

// Test Exhibition ID 5
echo "TEST 2: Exhibition ID 5 Direct Query\n";
$sql = "SELECT id, title_en, event_video FROM exhibitions WHERE id = 5";
$result = $db->getConnection()->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Result: " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "Query Error: " . $db->getConnection()->error . "\n\n";
}

// Test Event ID 74
echo "TEST 3: Event ID 74 Direct Query\n";
$sql = "SELECT id, title, video_url FROM events WHERE id = 74";
$result = $db->getConnection()->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Result: " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "Query Error: " . $db->getConnection()->error . "\n\n";
}

// Test full query from get_event_details.php for Exhibition 3
echo "TEST 4: Full Exhibition Query (like get_event_details.php) for ID 3\n";
$sql = '
    SELECT 
        ex.id,
        ex.exhibition_date as event_date,
        ex.event_video as video_url,
        ex.event_video,
        COALESCE(ex.title_en, ex.title) as title
    FROM exhibitions ex
    WHERE ex.id = 3
    LIMIT 1
';
$result = $db->getConnection()->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Result: " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "Query Error: " . $db->getConnection()->error . "\n\n";
}

// Test full query from get_event_details.php for Event 74
echo "TEST 5: Full Event Query (like get_event_details.php) for ID 74\n";
$sql = '
    SELECT 
        e.id,
        e.event_date,
        e.video_url,
        e.video_url as event_video,
        COALESCE(e.title_en, e.title) as title
    FROM events e
    WHERE e.id = 74
    LIMIT 1
';
$result = $db->getConnection()->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Result: " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "Query Error: " . $db->getConnection()->error . "\n\n";
}

?>
