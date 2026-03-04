<?php
/**
 * Auto-migration script for pricing Arabic columns
 * This script automatically runs on deployment to add price_unit_ar and vat_note_ar columns
 * It checks if columns exist before adding them to avoid errors
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    $results = [
        'price_unit_ar_added' => false,
        'vat_note_ar_added' => false,
        'price_unit_ar_populated' => false,
        'vat_note_ar_populated' => false,
        'messages' => []
    ];
    
    // Check if price_unit_ar column exists
    $checkPriceUnitAr = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='pricing' AND COLUMN_NAME='price_unit_ar'";
    $result = $conn->query($checkPriceUnitAr);
    
    if (!$result || $result->num_rows === 0) {
        // Add price_unit_ar column
        $addPriceUnitAr = "ALTER TABLE pricing ADD COLUMN price_unit_ar VARCHAR(50) DEFAULT 'ريال سعودي' AFTER price_unit";
        if ($conn->query($addPriceUnitAr)) {
            $results['price_unit_ar_added'] = true;
            $results['messages'][] = 'Added price_unit_ar column successfully';
        } else {
            throw new Exception('Failed to add price_unit_ar column: ' . $conn->error);
        }
    } else {
        $results['messages'][] = 'price_unit_ar column already exists';
    }
    
    // Check if vat_note_ar column exists
    $checkVatNoteAr = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='pricing' AND COLUMN_NAME='vat_note_ar'";
    $result = $conn->query($checkVatNoteAr);
    
    if (!$result || $result->num_rows === 0) {
        // Add vat_note_ar column
        $addVatNoteAr = "ALTER TABLE pricing ADD COLUMN vat_note_ar VARCHAR(255) DEFAULT '*(غير شامل الضريبة)' AFTER vat_note";
        if ($conn->query($addVatNoteAr)) {
            $results['vat_note_ar_added'] = true;
            $results['messages'][] = 'Added vat_note_ar column successfully';
        } else {
            throw new Exception('Failed to add vat_note_ar column: ' . $conn->error);
        }
    } else {
        $results['messages'][] = 'vat_note_ar column already exists';
    }
    
    // Populate price_unit_ar for existing records
    $populateQueries = [
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي / يوم' WHERE price_unit = 'SAR/day' AND (price_unit_ar IS NULL OR price_unit_ar = '')",
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي / ساعة' WHERE price_unit = 'SAR/hour' AND (price_unit_ar IS NULL OR price_unit_ar = '')",
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي' WHERE price_unit = 'SAR' AND (price_unit_ar IS NULL OR price_unit_ar = '')",
        "UPDATE pricing SET vat_note_ar = '*(غير شامل الضريبة)' WHERE vat_note = '*(excluding VAT)' AND (vat_note_ar IS NULL OR vat_note_ar = '')"
    ];
    
    foreach ($populateQueries as $query) {
        if (!$conn->query($query)) {
            throw new Exception('Population query failed: ' . $conn->error);
        }
    }
    
    $results['price_unit_ar_populated'] = true;
    $results['vat_note_ar_populated'] = true;
    $results['messages'][] = 'Populated Arabic price units and VAT notes successfully';
    
    // Get summary
    $summaryQuery = "SELECT COUNT(*) as total, 
                           SUM(CASE WHEN price_unit_ar IS NOT NULL AND price_unit_ar != '' THEN 1 ELSE 0 END) as with_ar_unit,
                           SUM(CASE WHEN vat_note_ar IS NOT NULL AND vat_note_ar != '' THEN 1 ELSE 0 END) as with_ar_vat
                    FROM pricing";
    $summaryResult = $conn->query($summaryQuery);
    $summary = $summaryResult->fetch_assoc();
    
    $results['summary'] = $summary;
    $results['success'] = true;
    
    echo json_encode($results);
    
} catch (Exception $e) {
    error_log('Auto-migration Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}
?>


