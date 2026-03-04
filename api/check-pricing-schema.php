<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['error' => 'Not connected']);
        exit;
    }
    
    // Get pricing table structure
    $result = $db->query("DESCRIBE pricing");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    // Get sample data
    $result = $db->query("SELECT * FROM pricing LIMIT 1");
    $sample = $result->fetch_assoc();
    
    echo json_encode([
        'columns' => $columns,
        'sample' => $sample,
        'count' => $db->query("SELECT COUNT(*) as count FROM pricing")->fetch_assoc()['count']
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

