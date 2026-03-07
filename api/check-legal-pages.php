<?php
/**
 * Check Legal Pages in Database
 * Diagnostic script to verify Terms and Privacy content exists
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check what's in the legal_page_translations table
    $query = 'SELECT id, page_key, language, title, LEFT(content, 100) as content_preview, last_updated FROM legal_page_translations ORDER BY page_key, language';
    
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Query failed: ' . $conn->error]);
        exit;
    }
    
    $pages = [];
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }
    
    // Also check for each specific page/language combination
    $checks = [
        ['page_key' => 'privacy', 'language' => 'en'],
        ['page_key' => 'privacy', 'language' => 'ar'],
        ['page_key' => 'terms', 'language' => 'en'],
        ['page_key' => 'terms', 'language' => 'ar']
    ];
    
    $status = [];
    foreach ($checks as $check) {
        $query = 'SELECT id, title FROM legal_page_translations WHERE page_key = ? AND language = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $check['page_key'], $check['language']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        $status[$check['page_key'] . '_' . $check['language']] = $row ? 'EXISTS' : 'MISSING';
    }
    
    echo json_encode([
        'success' => true,
        'all_pages' => $pages,
        'status' => $status,
        'total_records' => count($pages)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
