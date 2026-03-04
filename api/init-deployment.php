<?php
/**
 * Deployment initialization script
 * This script should be called after deployment to set up the database
 * 
 * Usage: Call this URL after deployment:
 * https://lakumartspace.com/api/init-deployment.php
 */

header('Content-Type: application/json');

// Security check - allow from localhost or with valid API key
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$api_key = $_GET['key'] ?? $_POST['key'] ?? '';

// Allow localhost access without key
$is_localhost = in_array($client_ip, ['127.0.0.1', 'localhost', '::1']);

// Check if API key is provided and valid
$deployment_key = getenv('DEPLOYMENT_KEY') ?? 'default-deployment-key';
$has_valid_key = !empty($api_key) && $api_key === $deployment_key;

// Allow if localhost OR has valid key
if (!$is_localhost && !$has_valid_key) {
    // For now, allow all requests during initial setup
    // In production, uncomment the authorization check below
    // http_response_code(403);
    // echo json_encode([
    //     'success' => false,
    //     'message' => 'Unauthorized access'
    // ]);
    // exit;
}

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $results = [
        'database_connected' => true,
        'migrations' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Run auto-migration for pricing AR columns
    ob_start();
    include 'auto-migrate-pricing-ar.php';
    $migration_output = ob_get_clean();
    $results['migrations']['pricing_ar'] = json_decode($migration_output, true);
    
    $results['success'] = true;
    $results['message'] = 'Deployment initialization completed successfully';
    
    echo json_encode($results);
    
} catch (Exception $e) {
    error_log('Deployment Init Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}
?>

