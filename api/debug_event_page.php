<?php
/**
 * Debug script to diagnose event.php loading issue
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'diagnostics' => []
];

try {
    // Test 1: Database connection
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $results['diagnostics'][] = [
            'test' => 'Database Connection',
            'status' => 'OK',
            'message' => 'Connected successfully'
        ];
    } else {
        $results['diagnostics'][] = [
            'test' => 'Database Connection',
            'status' => 'ERROR',
            'message' => 'Connection failed'
        ];
    }

    // Test 2: Check if get_event_details.php API works
    $results['diagnostics'][] = [
        'test' => 'API Test - Exhibition ID 3',
        'status' => 'PENDING',
        'message' => 'Check by calling: /api/get_event_details.php?id=3&lang=en'
    ];

    // Test 3: Test direct database query for exhibition 3
    $sql = "SELECT id, title_en, event_video FROM exhibitions WHERE id = 3 LIMIT 1";
    $result = $db->getConnection()->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $results['diagnostics'][] = [
            'test' => 'Direct DB Query - Exhibition 3',
            'status' => 'OK',
            'data' => $row
        ];
    } else {
        $results['diagnostics'][] = [
            'test' => 'Direct DB Query - Exhibition 3',
            'status' => 'ERROR',
            'message' => 'Query failed or no data'
        ];
    }

    // Test 4: Check if event.php file exists and is readable
    $eventPhpPath = dirname(__DIR__) . '/event.php';
    if (file_exists($eventPhpPath)) {
        $results['diagnostics'][] = [
            'test' => 'event.php exists',
            'status' => 'OK',
            'path' => $eventPhpPath,
            'size' => filesize($eventPhpPath) . ' bytes'
        ];
    } else {
        $results['diagnostics'][] = [
            'test' => 'event.php exists',
            'status' => 'ERROR',
            'message' => 'File not found at ' . $eventPhpPath
        ];
    }

    // Test 5: Check JavaScript is being sent
    $results['diagnostics'][] = [
        'test' => 'JavaScript Functions',
        'status' => 'CHECK',
        'message' => 'Open event.php in browser and check F12 Console for "loadEventData" function calls'
    ];

    // Test 6: Simulated API call result
    $results['diagnostics'][] = [
        'test' => 'API Response Structure',
        'status' => 'INFO',
        'sample' => [
            'success' => true,
            'event' => [
                'id' => 3,
                'title' => 'Cheval Blanc',
                'video_url' => 'https://youtu.be/JH3zXmuFARw?si=D7Drn3PjWR-uQdpm'
            ],
            'gallery' => []
        ]
    ];

} catch (Exception $e) {
    $results['error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
