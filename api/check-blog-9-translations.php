<?php
/**
 * Check Blog 9 Translations Status
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
    
    // Check if blog 9 exists
    $blogQuery = "SELECT id, title, excerpt, content FROM blogs WHERE id = 9";
    $blogResult = $conn->query($blogQuery);
    
    $blogExists = $blogResult && $blogResult->num_rows > 0;
    $blogData = $blogExists ? $blogResult->fetch_assoc() : null;
    
    // Check English translation
    $enQuery = "SELECT id, title, excerpt, content FROM blog_translations WHERE blog_id = 9 AND language = 'en'";
    $enResult = $conn->query($enQuery);
    $enExists = $enResult && $enResult->num_rows > 0;
    $enData = $enExists ? $enResult->fetch_assoc() : null;
    
    // Check Arabic translation
    $arQuery = "SELECT id, title, excerpt, content FROM blog_translations WHERE blog_id = 9 AND language = 'ar'";
    $arResult = $conn->query($arQuery);
    $arExists = $arResult && $arResult->num_rows > 0;
    $arData = $arExists ? $arResult->fetch_assoc() : null;
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'blog_exists' => $blogExists,
        'blog_data' => $blogData,
        'english_translation_exists' => $enExists,
        'english_translation' => $enData,
        'arabic_translation_exists' => $arExists,
        'arabic_translation' => $arData,
        'status' => [
            'blog_9_in_blogs_table' => $blogExists ? '✓ YES' : '✗ NO',
            'english_translation_in_blog_translations' => $enExists ? '✓ YES' : '✗ NO',
            'arabic_translation_in_blog_translations' => $arExists ? '✓ YES' : '✗ NO'
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Check Blog 9 Translations Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

