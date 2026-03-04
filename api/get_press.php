<?php
/**
 * LAKUM Artspace - Get Press API
 * Retrieves press releases from database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Get current language from URL parameter or session
    $lang = $_GET['lang'] ?? $_SESSION['language'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $query = '
        SELECT 
            p.id,
            p.source,
            p.press_date,
            p.url,
            p.category,
            p.cover_image,
            COALESCE(t_current.title, t_en.title) as title,
            COALESCE(t_current.content, t_en.content) as content,
            COALESCE(t_current.excerpt, t_en.excerpt) as excerpt,
            COALESCE(t_current.slug, t_en.slug) as slug
        FROM press p
        LEFT JOIN press_translations t_current ON p.id = t_current.press_id AND t_current.language = ?
        LEFT JOIN press_translations t_en ON p.id = t_en.press_id AND t_en.language = "en"
        WHERE p.is_published = 1
        ORDER BY p.press_date DESC
        LIMIT ? OFFSET ?
    ';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('sii', $lang, $limit, $offset);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $press = [];
    
    while ($row = $result->fetch_assoc()) {
        $press[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $press, 'source' => 'database', 'language' => $lang]);
    
} catch (Exception $e) {
    error_log('Press API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


