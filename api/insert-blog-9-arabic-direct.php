<?php
/**
 * Direct Insert Arabic Translation for Blog 9
 */

header('Content-Type: application/json');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // First, get blog 9 data
    $getQuery = "SELECT id, title, excerpt, content FROM blogs WHERE id = 9";
    $result = $conn->query($getQuery);
    
    if (!$result || $result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Blog 9 not found']);
        exit;
    }
    
    $blog = $result->fetch_assoc();
    
    // Arabic translations
    $arabicTitle = "مدونة LAKUM - المحتوى الفني والثقافي";
    $arabicExcerpt = "اكتشف أحدث المحتوى الفني والثقافي من LAKUM Artspace";
    $arabicContent = "مرحبا بك في مدونة LAKUM Artspace. هنا نشارك أحدث الأخبار والمقالات والمحتوى الفني. استكشف عالم الفن والثقافة معنا.";
    
    // Check if translation already exists
    $checkQuery = "SELECT id FROM blog_translations WHERE blog_id = 9 AND language = 'ar'";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        // Update existing
        $updateQuery = "UPDATE blog_translations SET title = ?, excerpt = ?, content = ? WHERE blog_id = 9 AND language = 'ar'";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sss', $arabicTitle, $arabicExcerpt, $arabicContent);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Arabic translation for blog 9 updated successfully',
                'blog_id' => 9,
                'language' => 'ar',
                'title' => $arabicTitle
            ]);
        } else {
            throw new Exception('Update failed: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        // Insert new
        $insertQuery = "INSERT INTO blog_translations (blog_id, language, title, excerpt, content) VALUES (9, 'ar', ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('sss', $arabicTitle, $arabicExcerpt, $arabicContent);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Arabic translation for blog 9 added successfully',
                'blog_id' => 9,
                'language' => 'ar',
                'title' => $arabicTitle
            ]);
        } else {
            throw new Exception('Insert failed: ' . $stmt->error);
        }
        $stmt->close();
    }
    
} catch (Exception $e) {
    error_log('Insert Blog 9 Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

