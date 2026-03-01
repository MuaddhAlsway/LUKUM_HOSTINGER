<?php
/**
 * LAKUM Artspace - Get Press by ID API
 * Retrieves a single press release from database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Press ID is required']);
        exit;
    }
    
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
    
    // First get the base press data
    $base_query = '
        SELECT 
            p.id,
            p.source,
            p.press_date,
            p.url,
            p.category,
            p.cover_image
        FROM press p
        WHERE p.id = ?
        LIMIT 1
    ';
    
    $stmt = $db->prepare($base_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $press = $result->fetch_assoc();
    
    if (!$press) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Press not found']);
        exit;
    }
    
    // Now get all translations
    $trans_query = '
        SELECT language, title, content, excerpt, slug
        FROM press_translations
        WHERE press_id = ?
    ';
    
    $stmt = $db->prepare($trans_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $trans_result = $stmt->get_result();
    
    // Initialize translation fields
    $press['title_en'] = '';
    $press['content_en'] = '';
    $press['excerpt_en'] = '';
    $press['slug_en'] = '';
    $press['title_ar'] = '';
    $press['content_ar'] = '';
    $press['excerpt_ar'] = '';
    $press['slug_ar'] = '';
    
    // Populate translations
    while ($trans = $trans_result->fetch_assoc()) {
        if ($trans['language'] === 'en') {
            $press['title_en'] = $trans['title'];
            $press['content_en'] = $trans['content'];
            $press['excerpt_en'] = $trans['excerpt'];
            $press['slug_en'] = $trans['slug'];
        } elseif ($trans['language'] === 'ar') {
            $press['title_ar'] = $trans['title'];
            $press['content_ar'] = $trans['content'];
            $press['excerpt_ar'] = $trans['excerpt'];
            $press['slug_ar'] = $trans['slug'];
        }
    }
    
    // Set display fields based on current language
    $press['title'] = ($lang === 'ar' && $press['title_ar']) ? $press['title_ar'] : $press['title_en'];
    $press['content'] = ($lang === 'ar' && $press['content_ar']) ? $press['content_ar'] : $press['content_en'];
    $press['excerpt'] = ($lang === 'ar' && $press['excerpt_ar']) ? $press['excerpt_ar'] : $press['excerpt_en'];
    $press['slug'] = ($lang === 'ar' && $press['slug_ar']) ? $press['slug_ar'] : $press['slug_en'];
    
    // Keep image paths as relative - let frontend handle URL construction
    // This ensures compatibility across different environments (local, staging, production)
    
    echo json_encode(['success' => true, 'data' => $press, 'language' => $lang, 'source' => 'database']);
    
} catch (Exception $e) {
    error_log('Press API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

