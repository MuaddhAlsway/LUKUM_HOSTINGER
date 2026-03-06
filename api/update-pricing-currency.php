<?php
/**
 * LAKUM Artspace - Update Pricing with Currency Image
 * This script adds currency_image column and populates it with RS/OIP.webp
 */

header('Content-Type: application/json');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Step 1: Check if currency_image column exists
    $checkColumnQuery = "SHOW COLUMNS FROM pricing LIKE 'currency_image'";
    $result = $conn->query($checkColumnQuery);
    
    if ($result->num_rows === 0) {
        // Column doesn't exist, add it
        $addColumnQuery = "ALTER TABLE pricing ADD COLUMN currency_image VARCHAR(255) DEFAULT NULL AFTER price_unit_ar";
        if (!$conn->query($addColumnQuery)) {
            throw new Exception('Failed to add currency_image column: ' . $conn->error);
        }
        echo json_encode(['success' => true, 'message' => 'Column added successfully']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Column already exists']);
    }
    
    // Step 2: Update all active pricing records with currency image
    $updateQuery = "UPDATE pricing SET currency_image = 'RS/OIP.webp' WHERE is_active = 1 AND (currency_image IS NULL OR currency_image = '')";
    if (!$conn->query($updateQuery)) {
        throw new Exception('Failed to update pricing records: ' . $conn->error);
    }
    
    $affectedRows = $conn->affected_rows;
    
    // Step 3: Verify the update
    $verifyQuery = "SELECT COUNT(*) as total, SUM(CASE WHEN currency_image = 'RS/OIP.webp' THEN 1 ELSE 0 END) as updated FROM pricing WHERE is_active = 1";
    $verifyResult = $conn->query($verifyQuery);
    $verifyData = $verifyResult->fetch_assoc();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pricing currency image updated successfully',
        'affected_rows' => $affectedRows,
        'verification' => [
            'total_active_pricing' => (int)$verifyData['total'],
            'updated_with_currency_image' => (int)$verifyData['updated']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
