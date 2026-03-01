<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: public, max-age=3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Load configuration
    require_once __DIR__ . '/config.php';
    
    $pageKey = $_GET['page_key'] ?? null;
    $lang = $_GET['lang'] ?? 'en';
    
    if (!$pageKey || !in_array($pageKey, ['terms', 'privacy'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid page key']);
        exit;
    }
    
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    // Get database connection using singleton
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn->set_charset('utf8mb4');
    
    $query = 'SELECT id, page_key, language, title, content, last_updated, created_at FROM legal_page_translations WHERE page_key = ? AND language = ?';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ss', $pageKey, $lang);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    
    if ($row) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => (int)$row['id'],
                'page_key' => $row['page_key'],
                'language' => $row['language'],
                'title' => $row['title'],
                'content' => $row['content'],
                'last_updated' => $row['last_updated'],
                'created_at' => $row['created_at']
            ]
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'Legal page not found',
            'page_key' => $pageKey,
            'language' => $lang
        ]);
    }
    
} catch (Exception $e) {
    error_log('Get Legal Page Error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
