<?php
/**
 * Create legal_page_translations table for storing legal pages
 */

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

$sql = "CREATE TABLE IF NOT EXISTS legal_page_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL,
    language VARCHAR(10) NOT NULL,
    title VARCHAR(255),
    content LONGTEXT,
    last_updated DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_page_lang (page_key, language),
    INDEX idx_page_key (page_key),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'legal_page_translations table created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
}

$conn->close();
?>

