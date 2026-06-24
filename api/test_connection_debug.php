<?php
/**
 * Test database connection with detailed error reporting
 */

header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load config
$config = require __DIR__ . '/../config.local.php';

$db_config = $config['db'];

echo json_encode([
    'test' => 'Database Connection Test',
    'config' => [
        'host' => $db_config['host'],
        'user' => $db_config['user'],
        'database' => $db_config['database'],
        'port' => $db_config['port'] ?? 3306,
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo "\n\n";

// Try to connect
$conn = new mysqli(
    $db_config['host'],
    $db_config['user'],
    $db_config['password'],
    $db_config['database'],
    $db_config['port'] ?? 3306
);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => $conn->connect_error,
        'errno' => $conn->connect_errno,
        'message' => 'Connection failed!'
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Connection successful!',
        'server_info' => $conn->server_info,
        'host_info' => $conn->host_info
    ], JSON_PRETTY_PRINT);
    
    // Test events table
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "\nEvents table has " . $row['count'] . " records\n";
    }
    
    // Check table schema
    $result = $conn->query("SHOW COLUMNS FROM events");
    echo "\nEvents table columns:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    $conn->close();
}

?>
