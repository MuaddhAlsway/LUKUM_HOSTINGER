<?php
/**
 * Create blog_translations table
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `blog_translations` (
        id INT PRIMARY KEY AUTO_INCREMENT,
        blog_id INT NOT NULL,
        language VARCHAR(5) NOT NULL,
        title VARCHAR(255),
        excerpt VARCHAR(500),
        content LONGTEXT,
        slug VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
        UNIQUE KEY unique_blog_lang (blog_id, language),
        INDEX idx_language (language)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'blog_translations table created successfully'
        ]);
    } else {
        throw new Exception('Create table failed: ' . $conn->error);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
