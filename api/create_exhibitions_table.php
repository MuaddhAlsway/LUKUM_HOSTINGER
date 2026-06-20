<?php
/**
 * Create Exhibitions Table
 * Run this once to set up the exhibitions table
 */

require_once 'db.php';

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Create exhibitions table with same structure as events
$sql = "CREATE TABLE IF NOT EXISTS exhibitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(255) NOT NULL,
    title_ar VARCHAR(255),
    description_en LONGTEXT,
    description_ar LONGTEXT,
    location_en VARCHAR(255),
    location_ar VARCHAR(255),
    exhibition_date DATE NOT NULL,
    exhibition_time TIME,
    exhibition_end_time TIME,
    end_date DATE,
    cover_image VARCHAR(500),
    category VARCHAR(50) DEFAULT 'exhibition',
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (exhibition_date),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Exhibitions table created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
}

?>
