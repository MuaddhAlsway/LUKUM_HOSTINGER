<?php
/**
 * LAKUM Artspace - Debug Script
 * Comprehensive debugging to find the issue
 */

header('Content-Type: application/json');

$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check 1: .env file
$debug['checks']['env_file'] = [
    'exists' => file_exists(__DIR__ . '/.env'),
    'path' => __DIR__ . '/.env'
];

if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    preg_match('/DB_NAME=(.+)/', $envContent, $matches);
    $debug['checks']['env_file']['DB_NAME'] = trim($matches[1] ?? 'not found');
}

// Check 2: Load config
$debug['checks']['config_load'] = 'ATTEMPTING';
try {
    require_once __DIR__ . '/config.php';
    $debug['checks']['config_load'] = 'SUCCESS';
    $debug['checks']['constants'] = [
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
        'DB_PORT' => defined('DB_PORT') ? DB_PORT : 'NOT DEFINED'
    ];
} catch (Exception $e) {
    $debug['checks']['config_load'] = 'FAILED: ' . $e->getMessage();
}

// Check 3: Database connection
$debug['checks']['database_connection'] = 'ATTEMPTING';
try {
    $db = Database::getInstance();
    
    if ($db->isConnected()) {
        $debug['checks']['database_connection'] = 'SUCCESS';
        
        // Get connection details
        $conn = $db->getConnection();
        $debug['checks']['connection_details'] = [
            'host_info' => $conn->host_info,
            'server_info' => $conn->server_info
        ];
        
        // Check tables
        $debug['checks']['tables'] = [];
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_row()) {
                $debug['checks']['tables'][] = $row[0];
            }
        }
        
        // Check data in each table
        $debug['checks']['table_data'] = [];
        
        foreach ($debug['checks']['tables'] as $table) {
            $countResult = $conn->query("SELECT COUNT(*) as count FROM `$table`");
            if ($countResult) {
                $row = $countResult->fetch_assoc();
                $debug['checks']['table_data'][$table] = (int)$row['count'];
            }
        }
        
        // Sample data from events table
        if (in_array('events', $debug['checks']['tables'])) {
            $debug['checks']['sample_events'] = [];
            $result = $conn->query("SELECT id, title, event_date FROM events LIMIT 3");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $debug['checks']['sample_events'][] = $row;
                }
            }
        }
        
    } else {
        $debug['checks']['database_connection'] = 'FAILED - NOT CONNECTED';
    }
} catch (Exception $e) {
    $debug['checks']['database_connection'] = 'ERROR: ' . $e->getMessage();
}

// Check 4: API endpoints
$debug['checks']['api_endpoints'] = [];
$endpoints = [
    'get_events.php',
    'create_event.php',
    'update_event.php',
    'delete_event.php',
    'get_blogs.php',
    'create_blog.php',
    'update_blog.php',
    'delete_blog.php',
    'get_press.php',
    'create_press.php',
    'update_press.php',
    'delete_press.php',
    'get_pricing.php',
    'create_pricing.php',
    'update_pricing.php',
    'delete_pricing.php'
];

foreach ($endpoints as $endpoint) {
    $debug['checks']['api_endpoints'][$endpoint] = file_exists(__DIR__ . '/' . $endpoint) ? 'EXISTS' : 'MISSING';
}

// Check 5: Test API call
$debug['checks']['api_test'] = 'ATTEMPTING';
try {
    // Simulate API call
    if (isset($db) && $db->isConnected()) {
        $conn = $db->getConnection();
        $result = $conn->query("SELECT id, title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category FROM events LIMIT 5");
        
        if ($result) {
            $debug['checks']['api_test'] = 'SUCCESS';
            $debug['checks']['api_test_data'] = [];
            while ($row = $result->fetch_assoc()) {
                $debug['checks']['api_test_data'][] = $row;
            }
        } else {
            $debug['checks']['api_test'] = 'FAILED - Query error: ' . $conn->error;
        }
    } else {
        $debug['checks']['api_test'] = 'SKIPPED - Database not connected';
    }
} catch (Exception $e) {
    $debug['checks']['api_test'] = 'ERROR: ' . $e->getMessage();
}

// Check 6: Error log
$debug['checks']['error_log'] = [];
$logFile = __DIR__ . '/../logs/error.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $debug['checks']['error_log'] = array_slice($lines, -10); // Last 10 lines
}

// Summary
$debug['summary'] = [
    'env_configured' => $debug['checks']['env_file']['exists'],
    'config_loaded' => $debug['checks']['config_load'] === 'SUCCESS',
    'database_connected' => $debug['checks']['database_connection'] === 'SUCCESS',
    'tables_exist' => count($debug['checks']['tables'] ?? []) > 0,
    'data_exists' => array_sum($debug['checks']['table_data'] ?? []) > 0,
    'api_endpoints_created' => count(array_filter($debug['checks']['api_endpoints'], fn($v) => $v === 'EXISTS')) === 16
];

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>


