<?php
/**
 * Check what translations exist for blog 5
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Get all translations for blog 5
    $result = $conn->query("SELECT * FROM blog_translations WHERE blog_id = 5");
    
    $translations = [];
    while ($row = $result->fetch_assoc()) {
        $translations[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'blog_id' => 5,
        'translations_count' => count($translations),
        'translations' => $translations
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>


