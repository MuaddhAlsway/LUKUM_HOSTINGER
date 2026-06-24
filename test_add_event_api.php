<?php
/**
 * Test Add Event API
 * This script tests if add_event_simple.php works
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== ADD EVENT API TEST ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if config can be loaded
echo "TEST 1: Loading config...\n";
try {
    require_once __DIR__ . '/api/config.php';
    echo "✓ Config loaded successfully\n\n";
} catch (Exception $e) {
    echo "✗ Config load failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check database connection
echo "TEST 2: Checking database connection...\n";
$db = Database::getInstance();
if ($db->isConnected()) {
    echo "✓ Database connected successfully\n\n";
} else {
    echo "✗ Database connection failed\n";
    $conn = $db->getConnection();
    if ($conn) {
        echo "  Error: " . $conn->connect_error . "\n\n";
    } else {
        echo "  Connection object is null\n\n";
    }
}

// Test 3: Test events table exists
echo "TEST 3: Checking if events table exists...\n";
$tableQuery = "SHOW TABLES LIKE 'events'";
$result = $db->query($tableQuery);
if ($result && $result->num_rows > 0) {
    echo "✓ Events table found\n\n";
} else {
    echo "✗ Events table not found\n\n";
}

// Test 4: Test a simple insert
echo "TEST 4: Testing simple insert...\n";
$testData = [
    'title_en' => 'Test Event ' . time(),
    'description_en' => 'Test Description',
    'location_en' => 'Test Location',
    'event_date' => date('Y-m-d'),
    'event_time' => '10:00:00',
    'event_end_time' => '18:00:00',
    'cover_image' => 'assest/img-4.png',
    'category' => 'event'
];

// Build the JSON
$json = json_encode($testData);
echo "JSON: " . $json . "\n\n";

// Simulate the API call
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/api/add_event_simple.php';

// Mock file_get_contents for php://input
$mockInput = $json;

// Try to insert
try {
    $input = json_decode($mockInput, true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $title_en = $input['title_en'] ?? '';
    if (empty($title_en)) {
        throw new Exception('Event title is required');
    }
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    $description_en = $input['description_en'] ?? '';
    $location_en = $input['location_en'] ?? '';
    $title_ar = $input['title_ar'] ?? '';
    $description_ar = $input['description_ar'] ?? '';
    $location_ar = $input['location_ar'] ?? '';
    $event_date = $input['event_date'] ?? date('Y-m-d');
    $event_time = $input['event_time'] ?? '10:00:00';
    $event_end_time = $input['event_end_time'] ?? '18:00:00';
    $end_date = empty($input['end_date']) ? null : $input['end_date'];
    $cover_image = $input['cover_image'] ?? 'assest/img-4.png';
    $video_url = $input['video_url'] ?? '';
    $category = $input['category'] ?? 'event';
    $is_featured = (int)($input['is_featured'] ?? 0);
    
    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
    
    // Make slug unique
    $original_slug = $slug;
    $counter = 1;
    while (true) {
        $check_query = "SELECT id FROM events WHERE slug = ? LIMIT 1";
        $check_stmt = $conn->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception('Prepare slug check failed: ' . $conn->error);
        }
        $check_stmt->bind_param('s', $slug);
        if (!$check_stmt->execute()) {
            throw new Exception('Execute slug check failed: ' . $check_stmt->error);
        }
        $checkResult = $check_stmt->get_result();
        if ($checkResult->num_rows === 0) {
            $check_stmt->close();
            break;
        }
        $check_stmt->close();
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    echo "Generated slug: " . $slug . "\n";
    
    // Insert
    $query = "
        INSERT INTO events (
            title, description, location, slug,
            title_en, description_en, location_en,
            title_ar, description_ar, location_ar,
            event_date, event_time, event_end_time, end_date,
            cover_image, video_url, is_featured, category
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?
        )
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Insert prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'sssssssssssssssis',
        $title_en, $description_en, $location_en, $slug,
        $title_en, $description_en, $location_en,
        $title_ar, $description_ar, $location_ar,
        $event_date, $event_time, $event_end_time, $end_date,
        $cover_image, $video_url, $is_featured, $category
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Insert execute failed: ' . $stmt->error);
    }
    
    $event_id = $conn->insert_id;
    $stmt->close();
    
    if (!$event_id) {
        throw new Exception('Failed to get inserted event ID');
    }
    
    echo "✓ Test insert successful! Event ID: " . $event_id . "\n\n";
    
} catch (Exception $e) {
    echo "✗ Test insert failed: " . $e->getMessage() . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
?>
