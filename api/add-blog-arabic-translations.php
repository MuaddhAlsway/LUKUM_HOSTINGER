<?php
/**
 * Add Arabic Translations for Blogs API
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
    
    // Check if blog_translations table exists
    $tableCheckQuery = "SHOW TABLES LIKE 'blog_translations'";
    $tableCheckResult = $conn->query($tableCheckQuery);
    
    if (!$tableCheckResult || $tableCheckResult->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'blog_translations table does not exist']);
        exit;
    }
    
    // Get all blogs that don't have Arabic translations
    $query = "SELECT id, title, excerpt, content FROM blogs WHERE id NOT IN (SELECT DISTINCT blog_id FROM blog_translations WHERE language = 'ar')";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $count = 0;
    $errors = [];
    
    while ($blog = $result->fetch_assoc()) {
        // Insert Arabic translation (using same content as English for now)
        $insertQuery = "INSERT INTO blog_translations (blog_id, language, title, excerpt, content) VALUES (?, 'ar', ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        
        if (!$stmt) {
            $errors[] = "Error preparing statement for blog ID {$blog['id']}: " . $conn->error;
            continue;
        }
        
        $stmt->bind_param('isss', $blog['id'], $blog['title'], $blog['excerpt'], $blog['content']);
        
        if ($stmt->execute()) {
            $count++;
        } else {
            $errors[] = "Error adding translation for blog ID {$blog['id']}: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Arabic translations added successfully',
        'count' => $count,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log('Add Blog Arabic Translations Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


