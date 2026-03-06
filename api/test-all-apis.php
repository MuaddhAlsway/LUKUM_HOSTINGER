<?php
/**
 * Test all APIs and show results
 */

header('Content-Type: text/html; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        die('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    echo "<h1>LAKUM API Test Report</h1>";
    
    // Test 1: Events
    echo "<h2>1. Events Table</h2>";
    $eventsQuery = "SELECT COUNT(*) as count FROM events";
    $eventsResult = $conn->query($eventsQuery);
    $eventsCount = $eventsResult->fetch_assoc()['count'];
    echo "<p>Total events: <strong>$eventsCount</strong></p>";
    
    if ($eventsCount > 0) {
        echo "<h3>Sample Events:</h3>";
        $sampleQuery = "SELECT id, title, event_date FROM events LIMIT 5";
        $sampleResult = $conn->query($sampleQuery);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th></tr>";
        while ($row = $sampleResult->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['event_date']}</td></tr>";
        }
        echo "</table>";
        
        // Test event 72
        echo "<h3>Event ID 72:</h3>";
        $event72Query = "SELECT * FROM events WHERE id = 72";
        $event72Result = $conn->query($event72Query);
        if ($event72Result->num_rows > 0) {
            $event72 = $event72Result->fetch_assoc();
            echo "<pre>";
            print_r($event72);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Event 72 NOT FOUND</p>";
        }
    }
    
    // Test 2: Blogs
    echo "<h2>2. Blogs Table</h2>";
    $blogsQuery = "SELECT COUNT(*) as count FROM blogs";
    $blogsResult = $conn->query($blogsQuery);
    $blogsCount = $blogsResult->fetch_assoc()['count'];
    echo "<p>Total blogs: <strong>$blogsCount</strong></p>";
    
    if ($blogsCount > 0) {
        echo "<h3>Sample Blogs:</h3>";
        $sampleQuery = "SELECT id, title, created_at FROM blogs LIMIT 5";
        $sampleResult = $conn->query($sampleQuery);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Created</th></tr>";
        while ($row = $sampleResult->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['created_at']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>NO BLOGS FOUND</p>";
    }
    
    // Test 3: Press
    echo "<h2>3. Press Table</h2>";
    $pressQuery = "SELECT COUNT(*) as count FROM press";
    $pressResult = $conn->query($pressQuery);
    $pressCount = $pressResult->fetch_assoc()['count'];
    echo "<p>Total press: <strong>$pressCount</strong></p>";
    
    if ($pressCount > 0) {
        echo "<h3>Sample Press:</h3>";
        $sampleQuery = "SELECT id, title, press_date FROM press LIMIT 5";
        $sampleResult = $conn->query($sampleQuery);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th></tr>";
        while ($row = $sampleResult->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['press_date']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>NO PRESS FOUND</p>";
    }
    
    // Test API endpoints
    echo "<h2>4. API Endpoint Tests</h2>";
    
    echo "<h3>Test: GET /api/get_blogs_working.php?lang=en</h3>";
    $blogsJson = file_get_contents('http://localhost/api/get_blogs_working.php?lang=en');
    $blogsData = json_decode($blogsJson, true);
    echo "<pre>";
    echo "Success: " . ($blogsData['success'] ? 'YES' : 'NO') . "\n";
    echo "Count: " . count($blogsData['data'] ?? []) . "\n";
    if (!empty($blogsData['data'])) {
        echo "First blog: " . $blogsData['data'][0]['title'] . "\n";
    }
    echo "</pre>";
    
    echo "<h3>Test: GET /api/get_press.php?lang=en</h3>";
    $pressJson = file_get_contents('http://localhost/api/get_press.php?lang=en');
    $pressData = json_decode($pressJson, true);
    echo "<pre>";
    echo "Success: " . ($pressData['success'] ? 'YES' : 'NO') . "\n";
    echo "Count: " . count($pressData['data'] ?? []) . "\n";
    if (!empty($pressData['data'])) {
        echo "First press: " . $pressData['data'][0]['title'] . "\n";
    }
    echo "</pre>";
    
    echo "<h3>Test: GET /api/get_event_details.php?id=72&lang=en</h3>";
    $eventJson = file_get_contents('http://localhost/api/get_event_details.php?id=72&lang=en');
    $eventData = json_decode($eventJson, true);
    echo "<pre>";
    echo "Success: " . ($eventData['success'] ? 'YES' : 'NO') . "\n";
    if ($eventData['success']) {
        echo "Event: " . $eventData['event']['title'] . "\n";
    } else {
        echo "Error: " . $eventData['message'] . "\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
