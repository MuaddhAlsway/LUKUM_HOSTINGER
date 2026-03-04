<?php
/**
 * LAKUM Artspace - Edit Press API
 * Handles press release updates with bilingual support and image upload
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get form data
    $press_id = $_POST['id'] ?? null;
    $title_en = $_POST['title_en'] ?? $_POST['title'] ?? null;
    $title_ar = $_POST['title_ar'] ?? null;
    $source = $_POST['source'] ?? null;
    $press_date = $_POST['press_date'] ?? null;
    $excerpt_en = $_POST['excerpt_en'] ?? $_POST['excerpt'] ?? null;
    $excerpt_ar = $_POST['excerpt_ar'] ?? null;
    $content_en = $_POST['content_en'] ?? $_POST['content'] ?? null;
    $content_ar = $_POST['content_ar'] ?? null;
    $url = $_POST['url'] ?? null;
    $slug_en = $_POST['slug_en'] ?? null;
    $slug_ar = $_POST['slug_ar'] ?? null;
    
    // Validate required fields
    if (!$press_id) {
        echo json_encode(['success' => false, 'message' => 'Press ID is required']);
        exit;
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Get existing press data
    $select_query = 'SELECT cover_image FROM press WHERE id = ?';
    $select_stmt = $db->prepare($select_query);
    $select_stmt->bind_param('i', $press_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $existing = $result->fetch_assoc();
    
    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'Press not found']);
        exit;
    }
    
    $cover_image = $existing['cover_image'];
    
    // Handle image upload if provided
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assest/press-uploads/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'press_' . time() . '_' . uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $file_path)) {
            // Delete old image if it exists
            if ($cover_image && file_exists(__DIR__ . '/../' . $cover_image)) {
                unlink(__DIR__ . '/../' . $cover_image);
            }
            $cover_image = 'assest/press-uploads/' . $file_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }
    }
    
    // Step 1: Update base press fields (only if provided)
    $updateFields = [];
    $updateParams = [];
    $updateTypes = '';
    
    if ($title_en !== null) {
        $updateFields[] = 'title = ?';
        $updateParams[] = $title_en;
        $updateTypes .= 's';
    }
    if ($source !== null) {
        $updateFields[] = 'source = ?';
        $updateParams[] = $source;
        $updateTypes .= 's';
    }
    if ($press_date !== null) {
        $updateFields[] = 'press_date = ?';
        $updateParams[] = $press_date;
        $updateTypes .= 's';
    }
    if ($url !== null) {
        $updateFields[] = 'url = ?';
        $updateParams[] = $url;
        $updateTypes .= 's';
    }
    if ($cover_image !== null) {
        $updateFields[] = 'cover_image = ?';
        $updateParams[] = $cover_image;
        $updateTypes .= 's';
    }
    
    if (!empty($updateFields)) {
        $updatePressQuery = 'UPDATE press SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
        $updateParams[] = $press_id;
        $updateTypes .= 'i';
        
        $stmt = $db->prepare($updatePressQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param($updateTypes, ...$updateParams);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
    }
    
    // Step 2: Update English translation (UPSERT - only if provided)
    if ($title_en !== null || $content_en !== null || $excerpt_en !== null || $slug_en !== null) {
        $updateEnglishQuery = '
            INSERT INTO press_translations (press_id, language, title, content, excerpt, slug)
            VALUES (?, "en", ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = COALESCE(VALUES(title), title),
                content = COALESCE(VALUES(content), content),
                excerpt = COALESCE(VALUES(excerpt), excerpt),
                slug = COALESCE(VALUES(slug), slug),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($updateEnglishQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('issss', $press_id, $title_en, $content_en, $excerpt_en, $slug_en);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
    }
    
    // Step 3: Update Arabic translation (UPSERT - only if provided)
    $arabicUpdated = false;
    if ($title_ar !== null || $content_ar !== null || $excerpt_ar !== null || $slug_ar !== null) {
        $updateArabicQuery = '
            INSERT INTO press_translations (press_id, language, title, content, excerpt, slug)
            VALUES (?, "ar", ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = COALESCE(VALUES(title), title),
                content = COALESCE(VALUES(content), content),
                excerpt = COALESCE(VALUES(excerpt), excerpt),
                slug = COALESCE(VALUES(slug), slug),
                updated_at = CURRENT_TIMESTAMP
        ';
        
        $stmt = $db->prepare($updateArabicQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $stmt->bind_param('issss', $press_id, $title_ar, $content_ar, $excerpt_ar, $slug_ar);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $arabicUpdated = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Press updated successfully',
        'press_id' => $press_id,
        'updates' => [
            'base_fields' => !empty($updateFields),
            'english' => ($title_en !== null || $content_en !== null || $excerpt_en !== null || $slug_en !== null),
            'arabic' => $arabicUpdated
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Edit Press Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

