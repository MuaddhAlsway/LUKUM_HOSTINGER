<?php
/**
 * Check Pricing Table Status
 */

header('Content-Type: application/json');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    $response = [
        'database_connected' => $db->isConnected(),
        'tables' => [],
        'pricing_table' => null,
        'pricing_count' => 0
    ];
    
    if (!$db->isConnected()) {
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    // Get all tables
    $result = $db->query('SHOW TABLES');
    if ($result) {
        while ($row = $result->fetch_row()) {
            $response['tables'][] = $row[0];
        }
    }
    
    // Check pricing table structure
    $result = $db->query('DESCRIBE pricing');
    if ($result) {
        $response['pricing_table'] = [];
        while ($row = $result->fetch_assoc()) {
            $response['pricing_table'][] = $row;
        }
    }
    
    // Count pricing records
    $result = $db->query('SELECT COUNT(*) as count FROM pricing');
    if ($result) {
        $row = $result->fetch_assoc();
        $response['pricing_count'] = $row['count'];
    }
    
    // Get sample pricing data
    $result = $db->query('SELECT * FROM pricing LIMIT 1');
    if ($result) {
        $response['sample_pricing'] = $result->fetch_assoc();
    }
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'error' => $e->getMessage(),
        'database_connected' => false
    ]);
}
?>


