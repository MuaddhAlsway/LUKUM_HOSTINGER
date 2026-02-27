<?php
header('Content-Type: application/json');

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['page_key']) || !isset($data['content_en'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: page_key and content_en are required']);
    exit;
}

$pageKey = $data['page_key']; // 'terms' or 'privacy'
$titleEn = $data['title_en'] ?? '';
$contentEn = $data['content_en'];
$titleAr = $data['title_ar'] ?? null;
$contentAr = $data['content_ar'] ?? null;

// Validate page key
if (!in_array($pageKey, ['terms', 'privacy'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid page key']);
    exit;
}

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    $today = date('Y-m-d');
    
    // Step 1: Insert/Update English legal page (UPSERT)
    $insertEnglishQuery = '
        INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
        VALUES (?, "en", ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            content = VALUES(content),
            last_updated = VALUES(last_updated),
            updated_at = CURRENT_TIMESTAMP
    ';
    
    $stmt = $conn->prepare($insertEnglishQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssss', $pageKey, $titleEn, $contentEn, $today);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Step 2: Insert/Update Arabic legal page if provided (UPSERT)
    $arabicUpdated = false;
    if ($titleAr && $contentAr) {
        $insertArabicQuery = '
            INSERT INTO legal_page_translations (page_key, language, title, content, last_updated)
            VALUES (?, "ar", ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                content = VALUES(content),
                last_updated = VALUES(last_updated),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $conn->prepare($insertArabicQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('ssss', $pageKey, $titleAr, $contentAr, $today);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $arabicUpdated = true;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => ucfirst($pageKey) . ' page updated successfully',
        'page_key' => $pageKey,
        'translations' => [
            'en' => true,
            'ar' => $arabicUpdated
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Save Legal Page Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
