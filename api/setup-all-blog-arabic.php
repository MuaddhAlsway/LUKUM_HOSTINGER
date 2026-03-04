<?php
/**
 * Setup Arabic Translations for ALL Blogs
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
    
    // Get all blogs that don't have Arabic translations
    $query = "SELECT id, title, excerpt, content FROM blogs WHERE id NOT IN (SELECT DISTINCT blog_id FROM blog_translations WHERE language = 'ar')";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $count = 0;
    $errors = [];
    $added_blogs = [];
    
    while ($blog = $result->fetch_assoc()) {
        // Create Arabic versions (using same content for now)
        $arabicTitle = $blog['title'] . " (عربي)";
        $arabicExcerpt = $blog['excerpt'] . " - محتوى عربي";
        $arabicContent = "محتوى عربي:\n\n" . $blog['content'];
        
        $insertQuery = "INSERT INTO blog_translations (blog_id, language, title, excerpt, content) VALUES (?, 'ar', ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        
        if (!$stmt) {
            $errors[] = "Error preparing statement for blog ID {$blog['id']}: " . $conn->error;
            continue;
        }
        
        $stmt->bind_param('isss', $blog['id'], $arabicTitle, $arabicExcerpt, $arabicContent);
        
        if ($stmt->execute()) {
            $count++;
            $added_blogs[] = $blog['id'];
        } else {
            $errors[] = "Error adding translation for blog ID {$blog['id']}: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "Arabic translations added for $count blogs",
        'count' => $count,
        'added_blogs' => $added_blogs,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log('Setup All Blog Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


