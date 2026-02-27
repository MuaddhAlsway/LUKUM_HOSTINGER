<?php
/**
 * Check Pricing Database Status
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        echo json_encode([
            'database_connected' => false,
            'error' => $conn->connect_error
        ]);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if pricing table exists
    $result = $conn->query("SHOW TABLES LIKE 'pricing'");
    $table_exists = $result->num_rows > 0;
    
    $pricing_data = [];
    $row_count = 0;
    
    if ($table_exists) {
        // Get all pricing data
        $result = $conn->query("SELECT * FROM pricing ORDER BY id ASC");
        $row_count = $result->num_rows;
        
        while ($row = $result->fetch_assoc()) {
            $pricing_data[] = $row;
        }
    }
    
    $conn->close();
    
    echo json_encode([
        'database_connected' => true,
        'table_exists' => $table_exists,
        'row_count' => $row_count,
        'pricing_data' => $pricing_data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>

