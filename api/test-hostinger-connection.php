<?php
/**
 * Test Hostinger Database Connection - Enhanced Debugging
 * Verifies that the database credentials are correct
 */

header('Content-Type: application/json');

// Load configuration
require_once __DIR__ . '/config.php';

try {
    // Debug: Show what configuration is being used
    $debug_info = [
        'config_host' => DB_HOST,
        'config_user' => DB_USER,
        'config_database' => DB_NAME,
        'config_port' => DB_PORT,
        'php_version' => phpversion(),
        'mysqli_available' => extension_loaded('mysqli') ? 'Yes' : 'No'
    ];
    
    // Test connection using config constants
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Connection successful - get detailed info
    $conn->set_charset('utf8mb4');
    
    // Get database info
    $result = $conn->query("SELECT DATABASE() as db_name, VERSION() as version, USER() as current_user");
    $info = $result->fetch_assoc();
    
    // Get table count
    $tableResult = $conn->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE()");
    $tableInfo = $tableResult->fetch_assoc();
    
    // Get sample data from events table
    $eventsResult = $conn->query("SELECT COUNT(*) as event_count FROM events");
    $eventsInfo = $eventsResult ? $eventsResult->fetch_assoc() : ['event_count' => 'Table not found'];
    
    // Get sample data from blogs table
    $blogsResult = $conn->query("SELECT COUNT(*) as blog_count FROM blogs");
    $blogsInfo = $blogsResult ? $blogsResult->fetch_assoc() : ['blog_count' => 'Table not found'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully connected to Hostinger database',
        'connection_details' => [
            'host' => DB_HOST,
            'port' => DB_PORT,
            'database' => DB_NAME,
            'username' => DB_USER,
            'current_user' => $info['current_user']
        ],
        'database_info' => [
            'database_name' => $info['db_name'],
            'mysql_version' => $info['version'],
            'total_tables' => $tableInfo['table_count'],
            'events_count' => $eventsInfo['event_count'] ?? 0,
            'blogs_count' => $blogsInfo['blog_count'] ?? 0
        ],
        'debug_info' => $debug_info,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    
    // Get environment info for debugging
    $env_host = getenv('DB_HOST');
    $env_user = getenv('DB_USER');
    $env_db = getenv('DB_NAME');
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'connection_details' => [
            'host' => DB_HOST,
            'port' => DB_PORT,
            'database' => DB_NAME,
            'username' => DB_USER
        ],
        'environment_variables' => [
            'DB_HOST_env' => $env_host ? 'Set' : 'Not set',
            'DB_USER_env' => $env_user ? 'Set' : 'Not set',
            'DB_NAME_env' => $env_db ? 'Set' : 'Not set'
        ],
        'debug_info' => [
            'php_version' => phpversion(),
            'mysqli_available' => extension_loaded('mysqli') ? 'Yes' : 'No',
            'error_reporting' => ini_get('error_reporting')
        ],
        'troubleshooting' => [
            'step_1' => 'Check if IPv6 is removed from Hostinger whitelist',
            'step_2' => 'Verify IPv4 (178.73.75.172) is in whitelist',
            'step_3' => 'Wait 10-15 minutes for firewall to update',
            'step_4' => 'Contact Hostinger support if issue persists'
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>

