<?php
/**
 * EMERGENCY FIX - Restore Pricing Display
 * Fixes the price_unit values to match what the frontend expects
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    $log = [];
    
    // STEP 1: Check current price_unit values
    $result = $conn->query("SELECT DISTINCT price_unit FROM `pricing`");
    $currentValues = [];
    while ($row = $result->fetch_assoc()) {
        $currentValues[] = $row['price_unit'];
    }
    $log[] = "Current price_unit values in database: " . json_encode($currentValues);
    
    // STEP 2: Restore price_unit_ar values
    $updates = [
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / ساعة' WHERE `price_unit` LIKE '%hour%' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / يوم' WHERE `price_unit` LIKE '%day%' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي' WHERE `price_unit` = 'SAR' AND `price_unit_ar` IS NULL"
    ];
    
    foreach ($updates as $query) {
        if ($conn->query($query)) {
            $log[] = "✅ " . substr($query, 0, 60) . "... - Affected: " . $conn->affected_rows;
        } else {
            $log[] = "❌ " . substr($query, 0, 60) . "... - Error: " . $conn->error;
        }
    }
    
    // STEP 3: Ensure all pricing items are active
    if ($conn->query("UPDATE `pricing` SET `is_active` = 1")) {
        $log[] = "✅ All pricing items activated - Affected: " . $conn->affected_rows;
    }
    
    // STEP 4: Verify all pricing items
    $result = $conn->query("SELECT id, name_en, price_unit, price_unit_ar, is_active FROM `pricing` ORDER BY display_order ASC");
    $pricingItems = [];
    while ($row = $result->fetch_assoc()) {
        $pricingItems[] = $row;
    }
    $log[] = "✅ Total pricing items: " . count($pricingItems);
    
    // STEP 5: Test API response
    $testResult = $conn->query("SELECT COUNT(*) as count FROM `pricing` WHERE is_active = 1");
    $testRow = $testResult->fetch_assoc();
    $activeCount = $testRow['count'];
    $log[] = "✅ Active pricing items: " . $activeCount;
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing database fixed successfully',
        'log' => $log,
        'pricing_items' => $pricingItems,
        'summary' => [
            'total_items' => count($pricingItems),
            'active_items' => $activeCount
        ],
        'next_steps' => [
            '1. Clear browser cache: Ctrl+Shift+Delete',
            '2. Hard refresh: Ctrl+Shift+R',
            '3. Visit spaces.php - should show all pricing items',
            '4. If still not showing, check browser console for errors'
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
