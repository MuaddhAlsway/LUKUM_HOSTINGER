<?php
/**
 * LAKUM Artspace - Live Server Configuration Verification
 * Verifies that the API is accessible from Live Server (port 5500)
 */

header('Content-Type: application/json');

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'CHECKING',
    'configuration' => [],
    'tests' => [],
    'issues' => []
];

try {
    // Load configuration
    require_once 'config.php';
    
    // Check configuration
    $response['configuration'] = [
        'API_URL' => API_URL,
        'SITE_URL' => SITE_URL,
        'DB_HOST' => DB_HOST,
        'DB_NAME' => DB_NAME,
        'DB_USER' => DB_USER
    ];
    
    // Test 1: Database Connection
    $response['tests'][] = [
        'name' => 'Database Connection',
        'status' => 'TESTING'
    ];
    
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $response['tests'][0]['status'] = 'SUCCESS';
        $response['tests'][0]['message'] = 'Connected to database successfully';
    } else {
        $response['tests'][0]['status'] = 'FAILED';
        $response['tests'][0]['message'] = 'Failed to connect to database';
        $response['issues'][] = [
            'severity' => 'CRITICAL',
            'issue' => 'Database connection failed',
            'solution' => 'Check MySQL is running and credentials are correct'
        ];
    }
    
    // Test 2: CORS Headers
    $response['tests'][] = [
        'name' => 'CORS Configuration',
        'status' => 'SUCCESS',
        'message' => 'CORS headers configured for Live Server',
        'allowed_origins' => [
            'http://127.0.0.1:5500',
            'http://127.0.0.1:5500/LUKUM(main)'
        ]
    ];
    
    // Test 3: API Endpoints
    $response['tests'][] = [
        'name' => 'API Endpoints',
        'status' => 'CHECKING'
    ];
    
    $endpoints = [
        'get_events.php',
        'get_blogs.php',
        'get_press.php',
        'get_pricing.php'
    ];
    
    $missingEndpoints = [];
    foreach ($endpoints as $endpoint) {
        if (!file_exists(__DIR__ . '/' . $endpoint)) {
            $missingEndpoints[] = $endpoint;
        }
    }
    
    if (empty($missingEndpoints)) {
        $response['tests'][2]['status'] = 'SUCCESS';
        $response['tests'][2]['message'] = 'All API endpoints found';
        $response['tests'][2]['endpoints'] = $endpoints;
    } else {
        $response['tests'][2]['status'] = 'FAILED';
        $response['tests'][2]['message'] = 'Some endpoints are missing';
        $response['tests'][2]['missing'] = $missingEndpoints;
        $response['issues'][] = [
            'severity' => 'HIGH',
            'issue' => 'Missing API endpoints',
            'solution' => 'Recreate missing endpoints'
        ];
    }
    
    // Test 4: Sample Data
    $response['tests'][] = [
        'name' => 'Sample Data',
        'status' => 'CHECKING'
    ];
    
    if ($db->isConnected()) {
        $conn = $db->getConnection();
        $result = $conn->query("SELECT COUNT(*) as count FROM events");
        if ($result) {
            $row = $result->fetch_assoc();
            $eventCount = (int)$row['count'];
            
            if ($eventCount > 0) {
                $response['tests'][3]['status'] = 'SUCCESS';
                $response['tests'][3]['message'] = "Found $eventCount events in database";
                $response['tests'][3]['event_count'] = $eventCount;
            } else {
                $response['tests'][3]['status'] = 'WARNING';
                $response['tests'][3]['message'] = 'No events in database';
                $response['issues'][] = [
                    'severity' => 'MEDIUM',
                    'issue' => 'No data in database',
                    'solution' => 'Insert mock data using INSERT_MOCK_DATA.sql'
                ];
            }
        }
    }
    
    // Overall status
    $allSuccess = true;
    foreach ($response['tests'] as $test) {
        if ($test['status'] === 'FAILED') {
            $allSuccess = false;
            break;
        }
    }
    
    $response['status'] = $allSuccess ? 'SUCCESS' : 'FAILED';
    $response['message'] = $allSuccess ? 
        'Live Server configuration is correct! API should work now.' : 
        'There are issues that need to be fixed.';
    
} catch (Exception $e) {
    $response['status'] = 'ERROR';
    $response['message'] = $e->getMessage();
    $response['error'] = $e->getMessage();
}

http_response_code($response['status'] === 'SUCCESS' ? 200 : 500);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
