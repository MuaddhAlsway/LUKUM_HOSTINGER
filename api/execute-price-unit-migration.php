<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Check if columns already exist
    $checkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='pricing' AND COLUMN_NAME='price_unit_ar'";
    $result = $conn->query($checkQuery);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Columns already exist']);
        exit;
    }
    
    // Add columns
    $queries = [
        "ALTER TABLE pricing ADD COLUMN price_unit_ar VARCHAR(50) DEFAULT 'ريال سعودي' AFTER price_unit",
        "ALTER TABLE pricing ADD COLUMN vat_note_ar VARCHAR(255) DEFAULT '*(غير شامل الضريبة)' AFTER vat_note",
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي / يوم' WHERE price_unit = 'SAR/day'",
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي / ساعة' WHERE price_unit = 'SAR/hour'",
        "UPDATE pricing SET price_unit_ar = 'ريال سعودي' WHERE price_unit = 'SAR'",
        "UPDATE pricing SET vat_note_ar = '*(غير شامل الضريبة)' WHERE vat_note = '*(excluding VAT)'"
    ];
    
    foreach ($queries as $query) {
        if (!$conn->query($query)) {
            throw new Exception('Query failed: ' . $conn->error);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Migration completed successfully']);
    
} catch (Exception $e) {
    error_log('Migration Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

