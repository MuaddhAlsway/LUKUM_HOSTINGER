<?php
/**
 * Check pricing table structure
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Get table structure
    $result = $db->query("DESCRIBE pricing");
    
    if (!$result) {
        throw new Exception('Pricing table does not exist');
    }
    
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    
    // Get sample data
    $dataResult = $db->query("SELECT * FROM pricing LIMIT 1");
    $sampleData = $dataResult ? $dataResult->fetch_assoc() : null;
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'columns' => $columns,
        'sample_data' => $sampleData,
        'column_names' => array_column($columns, 'Field')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


