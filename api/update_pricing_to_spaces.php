<?php
/**
 * LAKUM Artspace - Update Pricing to Spaces
 * Replaces old workshop pricing with space rental pricing
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }

    // Start transaction
    $db->getConnection()->begin_transaction();

    // Delete old pricing
    $deleteQuery = 'DELETE FROM pricing WHERE is_active = 1';
    if (!$db->getConnection()->query($deleteQuery)) {
        throw new Exception('Failed to delete old pricing: ' . $db->getConnection()->error);
    }

    // Insert new space rental pricing
    $insertQuery = 'INSERT INTO pricing (title, content, price, price_unit, price_sec, vat_note, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)';
    
    $stmt = $db->prepare($insertQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }

    $pricingData = [
        ['Hall 1', 'Main exhibition hall with full technical support', 12000, 'SAR', 'per day', '*(excluding VAT)', 1],
        ['Hall 2', 'Secondary exhibition hall with projector and screen', 7200, 'SAR', 'per day', '*(excluding VAT)', 2],
        ['Hourly Rate', 'Hourly booking for short-format experiences', 1000, 'SAR', 'per hour', '*(excluding VAT)', 3],
        ['Set up/Dismantle Day', 'Dedicated day for setup or dismantling', 3400, 'SAR', 'per day', '*(excluding VAT)', 4],
        ['Café', 'Café rental with redeemable credit', 3400, 'SAR', 'per day', '*(excluding VAT)', 5],
        ['Meeting Room', 'Private meeting room with refreshments and tech', 60, 'SAR', 'per hour', '*(excluding VAT)', 6]
    ];

    $rowsAffected = 0;
    foreach ($pricingData as $item) {
        $stmt->bind_param('ssiissi', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6]);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $rowsAffected++;
    }

    // Commit transaction
    $db->getConnection()->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pricing updated successfully to space rental rates',
        'rows_affected' => $rowsAffected
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($db) && $db->isConnected()) {
        $db->getConnection()->rollback();
    }
    
    error_log('Pricing Update Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

