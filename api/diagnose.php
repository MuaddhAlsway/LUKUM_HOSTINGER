<?php
/**
 * LAKUM Artspace - Diagnostic Script
 * Checks database connection and configuration
 */

header('Content-Type: application/json');

$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'files' => [],
    'env_file' => [],
    'database' => [],
    'tables' => [],
    'data_counts' => [],
    'errors' => []
];

// Check .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $diagnostics['files']['.env'] = 'EXISTS';
    $envContent = file_get_contents($envFile);
    preg_match('/DB_HOST=(.+)/', $envContent, $host);
    preg_match('/DB_USER=(.+)/', $envContent, $user);
    preg_match('/DB_NAME=(.+)/', $envContent, $name);
    
    $diagnostics['env_file'] = [
        'DB_HOST' => trim($host[1] ?? 'not found'),
        'DB_USER' => trim($user[1] ?? 'not found'),
        'DB_NAME' => trim($name[1] ?? 'not found')
    ];
} else {
    $diagnostics['files']['.env'] = 'MISSING';
    $diagnostics['errors'][] = '.env file not found';
}

// Check config.php
if (file_exists(__DIR__ . '/config.php')) {
    $diagnostics['files']['config.php'] = 'EXISTS';
} else {
    $diagnostics['files']['config.php'] = 'MISSING';
    $diagnostics['errors'][] = 'config.php not found';
}

// Check db.php
if (file_exists(__DIR__ . '/db.php')) {
    $diagnostics['files']['db.php'] = 'EXISTS';
} else {
    $diagnostics['files']['db.php'] = 'MISSING';
    $diagnostics['errors'][] = 'db.php not found';
}

// Try to load config
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    
    // Check database connection
    try {
        $db = Database::getInstance();
        
        if ($db->isConnected()) {
            $diagnostics['database']['status'] = 'CONNECTED';
            $diagnostics['database']['host'] = defined('DB_HOST') ? DB_HOST : 'unknown';
            $diagnostics['database']['user'] = defined('DB_USER') ? DB_USER : 'unknown';
            $diagnostics['database']['name'] = defined('DB_NAME') ? DB_NAME : 'unknown';
            
            // Check tables
            $result = $db->query("SHOW TABLES");
            if ($result) {
                while ($row = $result->fetch_row()) {
                    $diagnostics['tables'][] = $row[0];
                }
            }
            
            // Count data
            if (in_array('events', $diagnostics['tables'])) {
                $result = $db->query("SELECT COUNT(*) as count FROM events");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $diagnostics['data_counts']['events'] = (int)$row['count'];
                }
            }
            
            if (in_array('blogs', $diagnostics['tables'])) {
                $result = $db->query("SELECT COUNT(*) as count FROM blogs");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $diagnostics['data_counts']['blogs'] = (int)$row['count'];
                }
            }
            
            if (in_array('press', $diagnostics['tables'])) {
                $result = $db->query("SELECT COUNT(*) as count FROM press");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $diagnostics['data_counts']['press'] = (int)$row['count'];
                }
            }
            
            if (in_array('pricing', $diagnostics['tables'])) {
                $result = $db->query("SELECT COUNT(*) as count FROM pricing");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $diagnostics['data_counts']['pricing'] = (int)$row['count'];
                }
            }
        } else {
            $diagnostics['database']['status'] = 'NOT CONNECTED';
            $diagnostics['database']['host'] = defined('DB_HOST') ? DB_HOST : 'unknown';
            $diagnostics['database']['user'] = defined('DB_USER') ? DB_USER : 'unknown';
            $diagnostics['database']['name'] = defined('DB_NAME') ? DB_NAME : 'unknown';
            $diagnostics['errors'][] = 'Database connection failed - database may not exist';
        }
    } catch (Exception $e) {
        $diagnostics['database']['status'] = 'ERROR';
        $diagnostics['errors'][] = $e->getMessage();
    }
} else {
    $diagnostics['errors'][] = 'Cannot load config.php';
}

// Check API endpoints
$diagnostics['api_endpoints'] = [
    'get_events' => file_exists(__DIR__ . '/get_events.php') ? 'EXISTS' : 'MISSING',
    'create_event' => file_exists(__DIR__ . '/create_event.php') ? 'EXISTS' : 'MISSING',
    'update_event' => file_exists(__DIR__ . '/update_event.php') ? 'EXISTS' : 'MISSING',
    'delete_event' => file_exists(__DIR__ . '/delete_event.php') ? 'EXISTS' : 'MISSING',
    'get_blogs' => file_exists(__DIR__ . '/get_blogs.php') ? 'EXISTS' : 'MISSING',
    'create_blog' => file_exists(__DIR__ . '/create_blog.php') ? 'EXISTS' : 'MISSING',
    'update_blog' => file_exists(__DIR__ . '/update_blog.php') ? 'EXISTS' : 'MISSING',
    'delete_blog' => file_exists(__DIR__ . '/delete_blog.php') ? 'EXISTS' : 'MISSING',
    'get_press' => file_exists(__DIR__ . '/get_press.php') ? 'EXISTS' : 'MISSING',
    'create_press' => file_exists(__DIR__ . '/create_press.php') ? 'EXISTS' : 'MISSING',
    'update_press' => file_exists(__DIR__ . '/update_press.php') ? 'EXISTS' : 'MISSING',
    'delete_press' => file_exists(__DIR__ . '/delete_press.php') ? 'EXISTS' : 'MISSING',
    'get_pricing' => file_exists(__DIR__ . '/get_pricing.php') ? 'EXISTS' : 'MISSING',
    'create_pricing' => file_exists(__DIR__ . '/create_pricing.php') ? 'EXISTS' : 'MISSING',
    'update_pricing' => file_exists(__DIR__ . '/update_pricing.php') ? 'EXISTS' : 'MISSING',
    'delete_pricing' => file_exists(__DIR__ . '/delete_pricing.php') ? 'EXISTS' : 'MISSING'
];

// Summary
$diagnostics['summary'] = [
    'configuration_complete' => !empty($diagnostics['env_file']),
    'database_connected' => $diagnostics['database']['status'] === 'CONNECTED',
    'tables_exist' => count($diagnostics['tables']) > 0,
    'data_exists' => array_sum($diagnostics['data_counts']) > 0,
    'api_endpoints_created' => count(array_filter($diagnostics['api_endpoints'], fn($v) => $v === 'EXISTS')) === 16
];

echo json_encode($diagnostics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
