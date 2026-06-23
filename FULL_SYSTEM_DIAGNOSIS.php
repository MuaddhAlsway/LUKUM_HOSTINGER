<?php
/**
 * FULL SYSTEM DIAGNOSIS
 * Checks every part of the video system to find where it breaks
 * Run: http://yourdomain.com/FULL_SYSTEM_DIAGNOSIS.php
 */

header('Content-Type: application/json; charset=utf-8');

$diagnosis = [
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

try {
    // ========== CHECK 1: DATABASE CONNECTION ==========
    
    $db_host = 'localhost';
    $db_user = 'u812122863_neama';
    $db_pass = 'Nema202610!LakumDB';
    $db_name = 'u812122863_lakum_artspace';
    
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    $diagnosis['checks'][] = [
        'name' => 'Database Connection',
        'status' => 'OK',
        'message' => 'Connected to ' . $db_name
    ];
    
    // ========== CHECK 2: TABLE EXISTENCE ==========
    
    $exhibTableExists = $conn->query("SHOW TABLES LIKE 'exhibitions'")->num_rows > 0;
    $eventsTableExists = $conn->query("SHOW TABLES LIKE 'events'")->num_rows > 0;
    
    $diagnosis['checks'][] = [
        'name' => 'Table: exhibitions',
        'status' => $exhibTableExists ? 'OK' : 'MISSING',
        'message' => $exhibTableExists ? 'Table exists' : 'Table does not exist'
    ];
    
    $diagnosis['checks'][] = [
        'name' => 'Table: events',
        'status' => $eventsTableExists ? 'OK' : 'MISSING',
        'message' => $eventsTableExists ? 'Table exists' : 'Table does not exist'
    ];
    
    // ========== CHECK 3: COLUMN EXISTENCE ==========
    
    $eventVideoCol = $conn->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'exhibitions' AND COLUMN_NAME = 'event_video' AND TABLE_SCHEMA = '$db_name'"
    )->num_rows > 0;
    
    $videoUrlCol = $conn->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'events' AND COLUMN_NAME = 'video_url' AND TABLE_SCHEMA = '$db_name'"
    )->num_rows > 0;
    
    $diagnosis['checks'][] = [
        'name' => 'Column: exhibitions.event_video',
        'status' => $eventVideoCol ? 'OK' : 'MISSING',
        'message' => $eventVideoCol ? 'Column exists' : 'Column missing - must add!'
    ];
    
    $diagnosis['checks'][] = [
        'name' => 'Column: events.video_url',
        'status' => $videoUrlCol ? 'OK' : 'MISSING',
        'message' => $videoUrlCol ? 'Column exists' : 'Column missing - must add!'
    ];
    
    // ========== CHECK 4: DATA IN DATABASE ==========
    
    $exhibitionCount = $conn->query("SELECT COUNT(*) as cnt FROM exhibitions")->fetch_assoc()['cnt'];
    $eventCount = $conn->query("SELECT COUNT(*) as cnt FROM events")->fetch_assoc()['cnt'];
    
    $diagnosis['checks'][] = [
        'name' => 'Data: Total exhibitions',
        'status' => $exhibitionCount > 0 ? 'OK' : 'EMPTY',
        'message' => "Total: $exhibitionCount records"
    ];
    
    $diagnosis['checks'][] = [
        'name' => 'Data: Total events',
        'status' => $eventCount > 0 ? 'OK' : 'EMPTY',
        'message' => "Total: $eventCount records"
    ];
    
    // ========== CHECK 5: VIDEOS IN DATABASE ==========
    
    $exhibitionsWithVideo = $conn->query(
        "SELECT COUNT(*) as cnt FROM exhibitions WHERE event_video IS NOT NULL AND event_video != ''"
    )->fetch_assoc()['cnt'];
    
    $eventsWithVideo = $conn->query(
        "SELECT COUNT(*) as cnt FROM events WHERE video_url IS NOT NULL AND video_url != ''"
    )->fetch_assoc()['cnt'];
    
    $diagnosis['checks'][] = [
        'name' => 'Videos: Exhibitions with video',
        'status' => $exhibitionsWithVideo > 0 ? 'OK' : 'NONE',
        'message' => "Count: $exhibitionsWithVideo / $exhibitionCount have videos"
    ];
    
    $diagnosis['checks'][] = [
        'name' => 'Videos: Events with video',
        'status' => $eventsWithVideo > 0 ? 'OK' : 'NONE',
        'message' => "Count: $eventsWithVideo / $eventCount have videos"
    ];
    
    // ========== CHECK 6: SAMPLE DATA ==========
    
    $sampleExhibition = $conn->query(
        "SELECT id, title_en, event_video FROM exhibitions WHERE event_video IS NOT NULL LIMIT 1"
    )->fetch_assoc();
    
    $sampleEvent = $conn->query(
        "SELECT id, title, video_url FROM events WHERE video_url IS NOT NULL LIMIT 1"
    )->fetch_assoc();
    
    if ($sampleExhibition) {
        $diagnosis['checks'][] = [
            'name' => 'Sample Exhibition with Video',
            'status' => 'FOUND',
            'data' => [
                'id' => $sampleExhibition['id'],
                'title' => $sampleExhibition['title_en'],
                'video_url' => $sampleExhibition['event_video']
            ]
        ];
    } else {
        $diagnosis['checks'][] = [
            'name' => 'Sample Exhibition with Video',
            'status' => 'NOT FOUND',
            'message' => 'No exhibitions have video URLs saved'
        ];
    }
    
    if ($sampleEvent) {
        $diagnosis['checks'][] = [
            'name' => 'Sample Event with Video',
            'status' => 'FOUND',
            'data' => [
                'id' => $sampleEvent['id'],
                'title' => $sampleEvent['title'],
                'video_url' => $sampleEvent['video_url']
            ]
        ];
    } else {
        $diagnosis['checks'][] = [
            'name' => 'Sample Event with Video',
            'status' => 'NOT FOUND',
            'message' => 'No events have video URLs saved'
        ];
    }
    
    // ========== CHECK 7: API RESPONSE TEST ==========
    
    if ($sampleExhibition) {
        $apiTestResult = $conn->query(
            "SELECT 
                id, 
                title_en, 
                event_video as video_url,
                event_video
             FROM exhibitions 
             WHERE id = " . intval($sampleExhibition['id'])
        )->fetch_assoc();
        
        $diagnosis['checks'][] = [
            'name' => 'API Response Mapping',
            'status' => 'OK',
            'message' => 'Testing if API would return both fields',
            'sample_response' => [
                'video_url' => $apiTestResult['video_url'] ?? null,
                'event_video' => $apiTestResult['event_video'] ?? null
            ]
        ];
    }
    
    // ========== CHECK 8: FILE EXISTENCE ==========
    
    $filesCheck = [
        'event.php' => file_exists('../event.php'),
        'api/get_event_details.php' => file_exists('./get_event_details.php'),
        'api/add_exhibition.php' => file_exists('./add_exhibition.php'),
        'api/add_event.php' => file_exists('./add_event.php'),
        'admin/add-exhibition.html' => file_exists('../admin/add-exhibition.html'),
        'admin/add-event.html' => file_exists('../admin/add-event.html')
    ];
    
    foreach ($filesCheck as $file => $exists) {
        $diagnosis['checks'][] = [
            'name' => "File: $file",
            'status' => $exists ? 'OK' : 'MISSING',
            'message' => $exists ? 'File exists' : 'File not found'
        ];
    }
    
    $conn->close();
    
    // ========== FINAL DIAGNOSIS ==========
    
    $allOK = true;
    $problems = [];
    
    foreach ($diagnosis['checks'] as $check) {
        if ($check['status'] !== 'OK' && $check['status'] !== 'FOUND') {
            $allOK = false;
            $problems[] = $check['name'] . ': ' . $check['message'];
        }
    }
    
    $diagnosis['overall_status'] = $allOK ? 'ALL SYSTEMS GO ✅' : 'ISSUES FOUND ⚠️';
    $diagnosis['problems'] = $problems;
    $diagnosis['success'] = $allOK;
    
} catch (Exception $e) {
    $diagnosis['error'] = $e->getMessage();
    $diagnosis['success'] = false;
}

echo json_encode($diagnosis, JSON_PRETTY_PRINT);
?>
