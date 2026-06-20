<?php
/**
 * Create Exhibitions Table
 * Run this file once to create the exhibitions table
 * Access via: http://localhost/admin/create_exhibitions_table.php
 */

require_once 'db.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$sql = "CREATE TABLE IF NOT EXISTS `exhibitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title_en` VARCHAR(255) NOT NULL,
    `title_ar` VARCHAR(255),
    `description_en` LONGTEXT,
    `description_ar` LONGTEXT,
    `location_en` VARCHAR(255),
    `location_ar` VARCHAR(255),
    `exhibition_date` DATE NOT NULL,
    `exhibition_time` TIME,
    `exhibition_end_time` TIME,
    `end_date` DATE,
    `cover_image` VARCHAR(500),
    `category` VARCHAR(50) DEFAULT 'exhibition',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_date` (`exhibition_date`),
    INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Exhibitions table created successfully'
    ]);
    exit;
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error creating table: ' . $conn->error
    ]);
    exit;
}

?>
