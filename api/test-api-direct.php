<?php
/**
 * Test API Directly - Check what's being returned
 */

header('Content-Type: application/json; charset=utf-8');

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

try {
    // Load config
    $envFile = __DIR__ . '/.env';
    $config = [
        'DB_HOST' => 'localhost',
        'DB_USER' => 'root',
        'DB_PASS' => '',
        'DB_NAME' => 'lakum_artspace',
        'DB_PORT' => 3306
    ];
    
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                if (in_array($key, ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_PORT'])) {
                    $config[$key] = $value;
                }
            }
        }
    }
    
    // Connect
    $conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Test 1: Raw query for events
    $report['tests'][] = [
        'name' => 'Raw Query - Events',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query('SELECT id, title, event_date, category FROM events LIMIT 5');
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    $report['tests'][0]['status'] = 'SUCCESS';
    $report['tests'][0]['count'] = count($events);
    $report['tests'][0]['sample'] = $events;
    
    // Test 2: Raw query for blogs
    $report['tests'][] = [
        'name' => 'Raw Query - Blogs',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query('SELECT id, title, author FROM blogs LIMIT 5');
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $report['tests'][1]['status'] = 'SUCCESS';
    $report['tests'][1]['count'] = count($blogs);
    $report['tests'][1]['sample'] = $blogs;
    
    // Test 3: Raw query for press
    $report['tests'][] = [
        'name' => 'Raw Query - Press',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query('SELECT id, title, press_date FROM press LIMIT 5');
    $press = [];
    while ($row = $result->fetch_assoc()) {
        $press[] = $row;
    }
    
    $report['tests'][2]['status'] = 'SUCCESS';
    $report['tests'][2]['count'] = count($press);
    $report['tests'][2]['sample'] = $press;
    
    // Test 4: Raw query for pricing
    $report['tests'][] = [
        'name' => 'Raw Query - Pricing',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query('SELECT id, name, price FROM pricing LIMIT 5');
    $pricing = [];
    while ($row = $result->fetch_assoc()) {
        $pricing[] = $row;
    }
    
    $report['tests'][3]['status'] = 'SUCCESS';
    $report['tests'][3]['count'] = count($pricing);
    $report['tests'][3]['sample'] = $pricing;
    
    // Test 5: Check if Database class works
    $report['tests'][] = [
        'name' => 'Database Class Test',
        'status' => 'RUNNING'
    ];
    
    require_once 'config.php';
    $db = Database::getInstance();
    
    if ($db->isConnected()) {
        $report['tests'][4]['status'] = 'SUCCESS';
        $report['tests'][4]['message'] = 'Database class connected';
    } else {
        $report['tests'][4]['status'] = 'FAILED';
        $report['tests'][4]['message'] = 'Database class not connected';
    }
    
    $report['status'] = 'SUCCESS';
    $conn->close();
    
} catch (Exception $e) {
    $report['status'] = 'ERROR';
    $report['error'] = $e->getMessage();
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>



