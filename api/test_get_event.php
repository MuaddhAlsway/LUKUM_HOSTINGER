<?php
/**
 * Test get_event_details_simple.php API
 */

require_once 'config.php';

$db = Database::getInstance();

// Get all events/exhibitions
echo "=== TESTING EVENT RETRIEVAL ===\n\n";

// Check events table
echo "1. EVENTS TABLE:\n";
$eventsResult = $db->getConnection()->query("SELECT id, title FROM events LIMIT 3");
if ($eventsResult) {
    echo "   Found " . $eventsResult->num_rows . " events\n";
    while ($row = $eventsResult->fetch_assoc()) {
        echo "   - ID: " . $row['id'] . ", Title: " . $row['title'] . "\n";
    }
} else {
    echo "   Error: " . $db->getConnection()->error . "\n";
}

echo "\n2. EXHIBITIONS TABLE:\n";
$exResult = $db->getConnection()->query("SELECT id, title_en FROM exhibitions LIMIT 3");
if ($exResult) {
    echo "   Found " . $exResult->num_rows . " exhibitions\n";
    while ($row = $exResult->fetch_assoc()) {
        echo "   - ID: " . $row['id'] . ", Title: " . $row['title_en'] . "\n";
    }
} else {
    echo "   Error: " . $db->getConnection()->error . "\n";
}

echo "\n3. TESTING API RESPONSE:\n";
if ($eventsResult && $eventsResult->num_rows > 0) {
    // Get first event ID
    $eventsResult->data_seek(0);
    $firstEvent = $eventsResult->fetch_assoc();
    $testId = $firstEvent['id'];
    
    echo "   Testing with ID: " . $testId . "\n";
    
    // Simulate what the API does
    $eventQuery = "SELECT id, title, description, location, event_date, video_url FROM events WHERE id = ? LIMIT 1";
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->bind_param('i', $testId);
    $eventStmt->execute();
    $eventResult = $eventStmt->get_result();
    $event = $eventResult->fetch_assoc();
    
    if ($event) {
        echo "   ✅ Found event:\n";
        print_r($event);
        
        echo "\n   API would return:\n";
        $response = [
            'success' => true,
            'data' => $event,
            'event' => $event
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        echo "   ❌ Event not found!\n";
    }
}

?>
