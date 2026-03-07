<?php
/**
 * TEST PRICING API
 * Simple test to verify pricing data is being returned correctly
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Test 1: Check if pricing table exists
    $tableCheck = $db->getConnection()->query("SHOW TABLES LIKE 'pricing'");
    $tableExists = $tableCheck->num_rows > 0;
    
    // Test 2: Count total pricing items
    $totalCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM `pricing`")->fetch_assoc()['count'];
    
    // Test 3: Count active pricing items
    $activeCount = $db->getConnection()->query("SELECT COUNT(*) as count FROM `pricing` WHERE is_active = 1")->fetch_assoc()['count'];
    
    // Test 4: Get all pricing items
    $result = $db->getConnection()->query("SELECT id, name_en, name_ar, price, price_unit, is_active FROM `pricing` ORDER BY display_order ASC");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    // Test 5: Call the actual API
    $apiResponse = file_get_contents('http://localhost/api/get_pricing.php');
    $apiData = json_decode($apiResponse, true);
    
    echo json_encode([
        'success' => true,
        'tests' => [
            'pricing_table_exists' => $tableExists,
            'total_pricing_items' => $totalCount,
            'active_pricing_items' => $activeCount,
            'pricing_items' => $items,
            'api_response' => $apiData
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
