<?php
/**
 * Fix pricing currency field - set all price_unit to 'SAR'
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }

    // Update all pricing items to have SAR as currency
    $query = "UPDATE pricing SET price_unit = 'SAR' WHERE is_active = 1";
    
    if (!$db->getConnection()->query($query)) {
        throw new Exception('Update failed: ' . $db->getConnection()->error);
    }

    $affectedRows = $db->getConnection()->affected_rows;

    echo json_encode([
        'success' => true,
        'message' => 'Currency field fixed',
        'rows_affected' => $affectedRows
    ]);

} catch (Exception $e) {
    error_log('Fix Pricing Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

