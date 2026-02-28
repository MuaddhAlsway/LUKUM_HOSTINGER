<?php
/**
 * LAKUM Artspace - Main Configuration Loader
 * 
 * This file loads configuration from config.local.php
 * config.local.php is NOT committed to Git and contains actual credentials
 * 
 * SECURITY: All sensitive data is in config.local.php (ignored by Git)
 */

// Load local configuration (contains actual credentials)
$configPath = __DIR__ . '/config.local.php';

if (!file_exists($configPath)) {
    die(json_encode([
        'success' => false,
        'message' => 'Configuration file not found. Please create config.local.php from config.local.php.example'
    ]));
}

// Load configuration
$config = require $configPath;

// Validate configuration
if (!isset($config['db']) || !isset($config['db']['host']) || !isset($config['db']['user']) || !isset($config['db']['password']) || !isset($config['db']['database'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Invalid configuration. Please check config.local.php'
    ]));
}

// Extract database configuration
$host = $config['db']['host'];
$user = $config['db']['user'];
$pass = $config['db']['password'];
$db = $config['db']['database'];
$port = $config['db']['port'] ?? 3306;
$charset = $config['db']['charset'] ?? 'utf8mb4';

// Create database connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    error_log('Database Connection Error: ' . $conn->connect_error);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed. Check error logs.'
    ]));
}

// Set charset
$conn->set_charset($charset);

// Make config available globally
global $APP_CONFIG;
$APP_CONFIG = $config;
?>
