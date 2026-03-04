<?php
/**
 * Force Add Arabic Translations for ALL Blogs
 * This will add Arabic translations regardless of existing data
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
    
    // Get ALL blogs
    $query = "SELECT id, title, excerpt, content FROM blogs";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $count = 0;
    $updated = 0;
    $errors = [];
    $added_blogs = [];
    
    while ($blog = $result->fetch_assoc()) {
        // Create Arabic versions
        $arabicTitle = "مدونة: " . $blog['title'];
        $arabicExcerpt = "ملخص عربي: " . $blog['excerpt'];
        $arabicContent = "محتوى عربي:\n\n" . $blog['content'];
        
        // First, delete any existing Arabic translation
        $deleteQuery = "DELETE FROM blog_translations WHERE blog_id = ? AND language = 'ar'";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $blog['id']);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Now insert new Arabic translation
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
        'errors' => $errors,
        'next_step' => 'Clear browser cache and visit blog.php?lang=ar'
    ]);
    
} catch (Exception $e) {
    error_log('Force Add Blog Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

