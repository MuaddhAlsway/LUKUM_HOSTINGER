<?php
/**
 * Debug Pricing System
 */

header('Content-Type: application/json');

require_once 'db.php';

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'database' => [],
    'pricing_table' => [],
    'sample_data' => null,
    'errors' => []
];

try {
    $db = Database::getInstance();
    $response['database']['connected'] = $db->isConnected();
    
    if (!$db->isConnected()) {
        $response['errors'][] = 'Database not connected';
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    // Get table info
    $result = $db->query('SHOW TABLES');
    $tables = [];
    if ($result) {
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
    }
    $response['database']['tables'] = $tables;
    $response['database']['pricing_table_exists'] = in_array('pricing', $tables);
    
    if (!in_array('pricing', $tables)) {
        $response['errors'][] = 'Pricing table does not exist';
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    // Get pricing table structure
    $result = $db->query('DESCRIBE pricing');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response['pricing_table'][] = $row;
        }
    }
    
    // Get sample data
    $result = $db->query('SELECT * FROM pricing LIMIT 1');
    if ($result) {
        $response['sample_data'] = $result->fetch_assoc();
    }
    
    // Count records
    $result = $db->query('SELECT COUNT(*) as count FROM pricing');
    if ($result) {
        $row = $result->fetch_assoc();
        $response['database']['pricing_count'] = $row['count'];
    }
    
} catch (Exception $e) {
    $response['errors'][] = $e->getMessage();
}

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
