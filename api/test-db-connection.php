<?php
/**
 * Test Database Connection
 */

header('Content-Type: application/json');

require_once 'db.php';

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'connection' => [],
    'tables' => [],
    'pricing' => [],
    'errors' => []
];

try {
    $db = Database::getInstance();
    $response['connection']['connected'] = $db->isConnected();
    
    if (!$db->isConnected()) {
        $response['errors'][] = 'Database not connected';
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    // Get connection info
    $conn = $db->getConnection();
    $response['connection']['host'] = $conn->get_server_info();
    $response['connection']['database'] = $conn->get_charset();
    
    // Get all tables
    $result = $db->query('SHOW TABLES');
    if ($result) {
        while ($row = $result->fetch_row()) {
            $response['tables'][] = $row[0];
        }
    }
    
    // Check pricing table
    if (in_array('pricing', $response['tables'])) {
        $response['pricing']['exists'] = true;
        
        // Get column info
        $result = $db->query('DESCRIBE pricing');
        if ($result) {
            $response['pricing']['columns'] = [];
            while ($row = $result->fetch_assoc()) {
                $response['pricing']['columns'][] = $row['Field'];
            }
        }
        
        // Count records
        $result = $db->query('SELECT COUNT(*) as count FROM pricing');
        if ($result) {
            $row = $result->fetch_assoc();
            $response['pricing']['record_count'] = $row['count'];
        }
        
        // Get sample
        $result = $db->query('SELECT * FROM pricing LIMIT 1');
        if ($result) {
            $response['pricing']['sample'] = $result->fetch_assoc();
        }
    } else {
        $response['pricing']['exists'] = false;
        $response['errors'][] = 'Pricing table does not exist';
    }
    
} catch (Exception $e) {
    $response['errors'][] = $e->getMessage();
}

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>


