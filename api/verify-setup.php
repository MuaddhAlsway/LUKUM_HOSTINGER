<?php
/**
 * LAKUM Artspace - Setup Verification
 * Checks if everything is properly configured
 */

header('Content-Type: application/json');

$checks = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'files' => [],
    'database' => [],
    'tables' => [],
    'data' => [],
    'recommendations' => []
];

// Check required files
$requiredFiles = [
    'config.php' => 'Configuration file',
    'db.php' => 'Database class',
    'get_events.php' => 'Events API',
    '.env' => 'Environment variables (optional)'
];

foreach ($requiredFiles as $file => $description) {
    $path = __DIR__ . '/' . $file;
    $checks['files'][$file] = [
        'exists' => file_exists($path),
        'description' => $description,
        'readable' => file_exists($path) && is_readable($path)
    ];
}

// Load config
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} else {
    require_once __DIR__ . '/db.php';
}

// Check database connection
try {
    $db = Database::getInstance();
    $checks['database']['connected'] = $db->isConnected();
    
    if ($db->isConnected()) {
        $checks['database']['host'] = defined('DB_HOST') ? DB_HOST : 'unknown';
        $checks['database']['name'] = defined('DB_NAME') ? DB_NAME : 'unknown';
        $checks['database']['user'] = defined('DB_USER') ? DB_USER : 'unknown';
        
        // Check tables
        $result = $db->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_row()) {
                $checks['tables'][] = $row[0];
            }
        }
        
        // Check events table
        if (in_array('events', $checks['tables'])) {
            $result = $db->query("SELECT COUNT(*) as count FROM events");
            if ($result) {
                $row = $result->fetch_assoc();
                $checks['data']['events_count'] = (int)$row['count'];
            }
        }
        
        // Check blogs table
        if (in_array('blogs', $checks['tables'])) {
            $result = $db->query("SELECT COUNT(*) as count FROM blogs");
            if ($result) {
                $row = $result->fetch_assoc();
                $checks['data']['blogs_count'] = (int)$row['count'];
            }
        }
        
        // Check press table
        if (in_array('press', $checks['tables'])) {
            $result = $db->query("SELECT COUNT(*) as count FROM press");
            if ($result) {
                $row = $result->fetch_assoc();
                $checks['data']['press_count'] = (int)$row['count'];
            }
        }
        
        // Check pricing table
        if (in_array('pricing', $checks['tables'])) {
            $result = $db->query("SELECT COUNT(*) as count FROM pricing");
            if ($result) {
                $row = $result->fetch_assoc();
                $checks['data']['pricing_count'] = (int)$row['count'];
            }
        }
        
        if (empty($checks['tables'])) {
            $checks['recommendations'][] = 'No tables found. Run DATABASE_SETUP.sql to create tables.';
        }
    } else {
        $checks['database']['error'] = 'Failed to connect to database';
        $checks['recommendations'][] = 'Check database credentials in .env or config.php';
        $checks['recommendations'][] = 'Ensure MySQL is running and database exists';
    }
} catch (Exception $e) {
    $checks['database']['error'] = $e->getMessage();
    $checks['recommendations'][] = 'Database error: ' . $e->getMessage();
}

// Check API endpoints
$checks['api_endpoints'] = [
    'get_events' => '/api/get_events.php?type=all',
    'test_events' => '/api/test-events.php',
    'verify_setup' => '/api/verify-setup.php'
];

// Overall status
$checks['status'] = 'OK';
if (!$checks['database']['connected']) {
    $checks['status'] = 'WARNING - Database not connected';
}
if (empty($checks['data']['events_count'])) {
    $checks['status'] = 'WARNING - No events in database';
}

echo json_encode($checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
