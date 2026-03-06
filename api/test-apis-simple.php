<?php
/**
 * Simple API test - shows what each API returns
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>API Test Results</h1>";
echo "<p>Testing all APIs to see what data they return...</p>";

// Test 1: Blogs API
echo "<h2>1. Blogs API</h2>";
echo "<p><strong>URL:</strong> /api/get_blogs_working.php?lang=en</p>";
$blogsJson = @file_get_contents('http://localhost/api/get_blogs_working.php?lang=en');
if ($blogsJson) {
    $blogsData = json_decode($blogsJson, true);
    echo "<p><strong>Success:</strong> " . ($blogsData['success'] ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Count:</strong> " . count($blogsData['data'] ?? []) . "</p>";
    if (!empty($blogsData['data'])) {
        echo "<p><strong>First blog:</strong> " . $blogsData['data'][0]['title'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> No blogs in database</p>";
    }
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> Could not reach API</p>";
}

// Test 2: Press API
echo "<h2>2. Press API</h2>";
echo "<p><strong>URL:</strong> /api/get_press.php?lang=en</p>";
$pressJson = @file_get_contents('http://localhost/api/get_press.php?lang=en');
if ($pressJson) {
    $pressData = json_decode($pressJson, true);
    echo "<p><strong>Success:</strong> " . ($pressData['success'] ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Count:</strong> " . count($pressData['data'] ?? []) . "</p>";
    if (!empty($pressData['data'])) {
        echo "<p><strong>First press:</strong> " . $pressData['data'][0]['title'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> No press in database</p>";
    }
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> Could not reach API</p>";
}

// Test 3: Event API
echo "<h2>3. Event API (ID 72)</h2>";
echo "<p><strong>URL:</strong> /api/get_event_details.php?id=72&lang=en</p>";
$eventJson = @file_get_contents('http://localhost/api/get_event_details.php?id=72&lang=en');
if ($eventJson) {
    $eventData = json_decode($eventJson, true);
    echo "<p><strong>Success:</strong> " . ($eventData['success'] ? 'YES' : 'NO') . "</p>";
    if ($eventData['success']) {
        echo "<p><strong>Event:</strong> " . $eventData['event']['title'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> " . $eventData['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> Could not reach API</p>";
}

// Test 4: Get all events
echo "<h2>4. All Events API</h2>";
echo "<p><strong>URL:</strong> /api/get_events.php?lang=en</p>";
$eventsJson = @file_get_contents('http://localhost/api/get_events.php?lang=en');
if ($eventsJson) {
    $eventsData = json_decode($eventsJson, true);
    echo "<p><strong>Success:</strong> " . ($eventsData['success'] ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Count:</strong> " . count($eventsData['data'] ?? []) . "</p>";
    if (!empty($eventsData['data'])) {
        echo "<p><strong>First event:</strong> ID " . $eventsData['data'][0]['id'] . " - " . $eventsData['data'][0]['title'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> No events in database</p>";
    }
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> Could not reach API</p>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>If all APIs show data, the issue is with the frontend JavaScript</li>";
echo "<li>If APIs show no data, the issue is with the database or API queries</li>";
echo "<li>If APIs can't be reached, check server configuration</li>";
echo "</ul>";
?>
