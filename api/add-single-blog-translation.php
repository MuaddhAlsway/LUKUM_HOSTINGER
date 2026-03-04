<?php
/**
 * Add Single Blog Translation API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Get JSON data from request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }
    
    $blog_id = (int)($input['blog_id'] ?? 0);
    $language = $input['language'] ?? 'ar';
    $title = $input['title'] ?? '';
    $excerpt = $input['excerpt'] ?? '';
    $content = $input['content'] ?? '';
    
    if (!$blog_id || !$title) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Blog ID and title are required']);
        exit;
    }
    
    // Check if blog exists
    $checkQuery = "SELECT id FROM blogs WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('i', $blog_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Blog not found']);
        exit;
    }
    
    $checkStmt->close();
    
    // Check if translation already exists
    $existsQuery = "SELECT id FROM blog_translations WHERE blog_id = ? AND language = ?";
    $existsStmt = $conn->prepare($existsQuery);
    $existsStmt->bind_param('is', $blog_id, $language);
    $existsStmt->execute();
    $existsResult = $existsStmt->get_result();
    $translationExists = $existsResult->num_rows > 0;
    $existsStmt->close();
    
    if ($translationExists) {
        // Update existing translation
        $updateQuery = "UPDATE blog_translations SET title = ?, excerpt = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE blog_id = ? AND language = ?";
        $stmt = $conn->prepare($updateQuery);
        
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param('ssssi', $title, $excerpt, $content, $blog_id, $language);
        
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }
        
        $stmt->close();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Arabic translation for blog $blog_id updated successfully",
            'blog_id' => $blog_id,
            'language' => $language
        ]);
    } else {
        // Insert new translation
        $insertQuery = "INSERT INTO blog_translations (blog_id, language, title, excerpt, content) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param('issss', $blog_id, $language, $title, $excerpt, $content);
        
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }
        
        $stmt->close();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => "Arabic translation for blog $blog_id added successfully",
            'blog_id' => $blog_id,
            'language' => $language
        ]);
    }
    
} catch (Exception $e) {
    error_log('Add Single Blog Translation Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


