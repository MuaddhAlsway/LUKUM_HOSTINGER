<?php
/**
 * LAKUM Artspace - Populate Press Arabic Translations
 * This script adds Arabic translations to press releases from database
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Get all press releases
    $query = "SELECT id, title_en, excerpt_en, content_en FROM press WHERE is_published = 1";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $updated = 0;
    $skipped = 0;
    
    while ($row = $result->fetch_assoc()) {
        $press_id = $row['id'];
        $title_en = $row['title_en'];
        $excerpt_en = $row['excerpt_en'];
        $content_en = $row['content_en'];
        
        // Check if Arabic translations already exist
        $check_query = "SELECT id FROM press_translations WHERE press_id = ? AND language = 'ar'";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('i', $press_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $skipped++;
            continue;
        }
        
        // Insert Arabic translation (using English as placeholder for now)
        // In production, you would use a translation API or manual translations
        $insert_query = "
            INSERT INTO press_translations (press_id, language, title, content, excerpt)
            VALUES (?, 'ar', ?, ?, ?)
        ";
        
        $insert_stmt = $conn->prepare($insert_query);
        if (!$insert_stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $insert_stmt->bind_param('isss', $press_id, $title_en, $content_en, $excerpt_en);
        
        if ($insert_stmt->execute()) {
            $updated++;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Press Arabic translations populated',
        'updated' => $updated,
        'skipped' => $skipped,
        'note' => 'Arabic translations are currently using English text as placeholder. Please update with actual Arabic translations in the admin panel.'
    ]);
    
} catch (Exception $e) {
    error_log('Populate Press Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
