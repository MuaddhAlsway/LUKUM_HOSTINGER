<?php
/**
 * DIAGNOSTIC SCRIPT - Check what's wrong
 */

header('Content-Type: application/json');
require_once 'config.php';

$diagnosis = [];

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    // Check pricing table
    $pricingCheck = $conn->query("SHOW TABLES LIKE 'pricing'");
    $diagnosis['pricing_table_exists'] = $pricingCheck->num_rows > 0;
    
    if ($diagnosis['pricing_table_exists']) {
        $pricingCount = $conn->query("SELECT COUNT(*) as count FROM `pricing`")->fetch_assoc()['count'];
        $diagnosis['pricing_items_total'] = $pricingCount;
        
        $pricingActive = $conn->query("SELECT COUNT(*) as count FROM `pricing` WHERE is_active = 1")->fetch_assoc()['count'];
        $diagnosis['pricing_items_active'] = $pricingActive;
        
        // Get sample pricing item
        $sample = $conn->query("SELECT * FROM `pricing` LIMIT 1")->fetch_assoc();
        $diagnosis['sample_pricing_item'] = $sample;
    }
    
    // Check legal pages table
    $legalCheck = $conn->query("SHOW TABLES LIKE 'legal_page_translations'");
    $diagnosis['legal_pages_table_exists'] = $legalCheck->num_rows > 0;
    
    if ($diagnosis['legal_pages_table_exists']) {
        $legalCount = $conn->query("SELECT COUNT(*) as count FROM `legal_page_translations`")->fetch_assoc()['count'];
        $diagnosis['legal_pages_total'] = $legalCount;
        
        $arabicCount = $conn->query("SELECT COUNT(*) as count FROM `legal_page_translations` WHERE language = 'ar'")->fetch_assoc()['count'];
        $diagnosis['legal_pages_arabic'] = $arabicCount;
        
        // Get sample legal page
        $sample = $conn->query("SELECT * FROM `legal_page_translations` LIMIT 1")->fetch_assoc();
        $diagnosis['sample_legal_page'] = $sample;
    }
    
    // Check database tables
    $tables = $conn->query("SHOW TABLES");
    $diagnosis['all_tables'] = [];
    while ($row = $tables->fetch_row()) {
        $diagnosis['all_tables'][] = $row[0];
    }
    
    echo json_encode([
        'success' => true,
        'diagnosis' => $diagnosis,
        'recommendation' => $diagnosis['pricing_items_total'] == 0 ? 'Run api/quick-fix.php' : 'Everything looks good'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
