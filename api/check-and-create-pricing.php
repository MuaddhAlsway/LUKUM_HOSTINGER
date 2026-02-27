<?php
/**
 * Check and Create Pricing Table if needed
 */

header('Content-Type: application/json');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'Database not connected'
        ]);
        exit;
    }
    
    // Check if pricing table exists
    $result = $db->query("SHOW TABLES LIKE 'pricing'");
    $tableExists = $result && $result->num_rows > 0;
    
    $response = [
        'success' => true,
        'table_exists' => $tableExists,
        'actions_taken' => []
    ];
    
    if (!$tableExists) {
        // Create pricing table
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS pricing (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                price INT,
                price_unit VARCHAR(50),
                price_sec VARCHAR(100),
                vat_note VARCHAR(255),
                content LONGTEXT,
                display_order INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_order (display_order),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        if ($db->query($createTableSQL)) {
            $response['actions_taken'][] = 'Created pricing table';
        } else {
            throw new Exception('Failed to create pricing table');
        }
    }
    
    // Check table structure
    $result = $db->query('DESCRIBE pricing');
    $columns = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    
    $response['columns'] = $columns;
    
    // Check record count
    $result = $db->query('SELECT COUNT(*) as count FROM pricing');
    if ($result) {
        $row = $result->fetch_assoc();
        $response['record_count'] = $row['count'];
    }
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
