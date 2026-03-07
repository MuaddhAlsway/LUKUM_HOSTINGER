<?php
/**
 * PRICING DATABASE ROLLBACK - Execute Automatically
 * This script restores the pricing table to working state
 * Fixes: Restores price_unit_ar values and activates all pricing items
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    $results = [];
    $errors = [];
    
    // STEP 1: Restore price_unit_ar values
    $queries = [
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / يوم' WHERE `price_unit` = 'SAR/day' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / ساعة' WHERE `price_unit` = 'SAR/hour' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي' WHERE `price_unit` = 'SAR' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `is_active` = 1 WHERE `is_active` = 0"
    ];
    
    foreach ($queries as $query) {
        if ($conn->query($query)) {
            $results[] = [
                'query' => substr($query, 0, 50) . '...',
                'affected_rows' => $conn->affected_rows,
                'status' => 'success'
            ];
        } else {
            $errors[] = [
                'query' => substr($query, 0, 50) . '...',
                'error' => $conn->error
            ];
        }
    }
    
    // STEP 2: Verify the rollback
    $verifyQuery = "SELECT COUNT(*) as total FROM `pricing` WHERE `is_active` = 1";
    $result = $conn->query($verifyQuery);
    $row = $result->fetch_assoc();
    $totalActive = $row['total'];
    
    // STEP 3: Get all pricing items
    $allPricingQuery = "SELECT id, name_en, name_ar, price, price_unit, price_unit_ar, is_active FROM `pricing` ORDER BY display_order ASC";
    $allPricingResult = $conn->query($allPricingQuery);
    $pricingItems = [];
    
    while ($item = $allPricingResult->fetch_assoc()) {
        $pricingItems[] = $item;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pricing database rollback completed successfully',
        'total_active_pricing' => $totalActive,
        'pricing_items' => $pricingItems,
        'queries_executed' => count($results),
        'errors' => $errors,
        'details' => $results,
        'next_steps' => [
            '1. Clear browser cache (Ctrl+Shift+Delete)',
            '2. Hard refresh (Ctrl+Shift+R)',
            '3. Visit spaces.php to verify pricing displays correctly',
            '4. Check that all pricing items are visible'
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Pricing Rollback Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>
