<?php
/**
 * Pricing Database Migration Script
 * Task 3: Remove Currency Text from Pricing Display
 * 
 * This script updates the pricing table to deprecate the price_unit_ar field
 * The frontend now uses getDurationLabel() to extract duration from price_unit
 * 
 * Usage: Run this script once to migrate the database
 * Access: http://yoursite.com/api/migrate-pricing-db.php
 */

// Include database configuration
require_once '../config.php';

// Check if this is a POST request (security measure)
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'localhost', '::1']);

// Allow execution from localhost or with POST request
if (!$isPost && !$isLocalhost) {
    http_response_code(403);
    die(json_encode([
        'success' => false,
        'message' => 'Access denied. This script can only be run from localhost or via POST request.'
    ]));
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Step 1: Update price_unit_ar to NULL (deprecated)
    $updateQuery = "UPDATE `pricing` SET `price_unit_ar` = NULL WHERE `price_unit_ar` IS NOT NULL";
    $conn->query($updateQuery);
    $updatedRows = $conn->affected_rows;

    // Step 2: Verify price_unit values
    $verifyQuery = "SELECT id, name_en, price_unit FROM `pricing` WHERE `price_unit` NOT IN ('SAR/day', 'SAR/hour', 'SAR', NULL)";
    $result = $conn->query($verifyQuery);
    $invalidRows = $result->num_rows;

    // Step 3: Get current pricing data
    $dataQuery = "SELECT id, name_en, name_ar, price, price_unit, price_unit_ar, price_sec, is_active FROM `pricing` ORDER BY display_order ASC";
    $dataResult = $conn->query($dataQuery);
    $pricingData = [];
    while ($row = $dataResult->fetch_assoc()) {
        $pricingData[] = $row;
    }

    // Commit transaction
    $conn->commit();

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pricing database migration completed successfully',
        'details' => [
            'updated_rows' => $updatedRows,
            'invalid_rows_found' => $invalidRows,
            'total_pricing_items' => count($pricingData),
            'pricing_data' => $pricingData
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Migration failed: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}

$conn->close();
?>
