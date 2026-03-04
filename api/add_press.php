<?php
/**
 * LAKUM Artspace - Add Press API
 * Handles press release creation with bilingual support and image upload
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get form data - handle both JSON and form-data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // If JSON parsing failed, try POST parameters
    if (!$data) {
        $data = $_POST;
    }
    
    // Get form data
    $title_en = $data['title_en'] ?? null;
    $title_ar = $data['title_ar'] ?? null;
    $source = $data['source'] ?? null;
    $press_date = $data['press_date'] ?? date('Y-m-d');
    $excerpt_en = $data['excerpt_en'] ?? null;
    $excerpt_ar = $data['excerpt_ar'] ?? null;
    $content_en = $data['content_en'] ?? null;
    $content_ar = $data['content_ar'] ?? null;
    $url = $data['url'] ?? null;
    $cover_image = $data['cover_image'] ?? null;
    $slug_en = $data['slug_en'] ?? generateSlug($title_en);
    
    // Only treat as Arabic translation if title_ar is provided and not empty
    $hasArabicTranslation = !empty($title_ar);
    
    $slug_ar = $data['slug_ar'] ?? ($hasArabicTranslation ? generateSlug($title_ar) : generateSlug($title_en));
    
    // Validate required fields
    if (!$title_en) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Title (English) is required']);
        exit;
    }
    
    if (!$title_ar) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Title (Arabic) is required']);
        exit;
    }
    
    // Content is optional - use excerpt as fallback
    if (empty($content_en)) {
        $content_en = $excerpt_en ?? '';
    }
    if (empty($content_ar)) {
        $content_ar = $excerpt_ar ?? '';
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Step 1: Insert into press table (base entity)
    $insertPressQuery = '
        INSERT INTO press (
            title, excerpt, content,
            source, cover_image, press_date, url, is_published
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ';
    
    $stmt = $db->prepare($insertPressQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('sssssss', $title_en, $excerpt_en, $content_en, $source, $cover_image, $press_date, $url);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $press_id = $db->getConnection()->insert_id;
    
    // Step 2: Insert English translation (UPSERT)
    $insertEnglishQuery = '
        INSERT INTO press_translations (press_id, language, title, content, excerpt, slug)
        VALUES (?, "en", ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            content = VALUES(content),
            excerpt = VALUES(excerpt),
            slug = VALUES(slug),
            updated_at = CURRENT_TIMESTAMP
    ';
    
    $stmt = $db->prepare($insertEnglishQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('issss', $press_id, $title_en, $content_en, $excerpt_en, $slug_en);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Step 3: Insert Arabic translation if provided (UPSERT)
    $arabicInserted = false;
    if ($hasArabicTranslation) {
        // Ensure slug_ar is always set
        if (empty($slug_ar)) {
            $slug_ar = generateSlug($title_ar);
        }
        if (empty($slug_ar)) {
            $slug_ar = generateSlug($title_en); // Fallback to English slug
        }
        
        $insertArabicQuery = '
            INSERT INTO press_translations (press_id, language, title, content, excerpt, slug)
            VALUES (?, "ar", ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                content = VALUES(content),
                excerpt = VALUES(excerpt),
                slug = VALUES(slug),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($insertArabicQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('issss', $press_id, $title_ar, $content_ar, $excerpt_ar, $slug_ar);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $arabicInserted = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Press created successfully',
        'press_id' => $press_id,
        'translations' => [
            'en' => true,
            'ar' => $arabicInserted
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Add Press Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Helper function to generate slug
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = preg_replace('~-+~', '-', $text);
    $text = trim($text, '-');
    return strtolower($text);
}
?>

