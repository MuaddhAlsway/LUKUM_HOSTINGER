<?php
/**
 * Get Settings API
 * Retrieves booking and shop links from the database
 * Automatically creates table if it doesn't exist
 */

header('Content-Type: application/json');
session_start();

try {
    // Include database configuration
    require_once 'config.php';
    
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if (!$conn || !$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Auto-create table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `site_settings` (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableQuery)) {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Insert default settings if they don't exist
    $insertDefaults = "INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
        ('booking_link', ''),
        ('shop_link', '')";
    
    $conn->query($insertDefaults);
    
    // Query to get settings from site_settings table
    $query = "SELECT setting_key, setting_value FROM site_settings 
              WHERE setting_key IN ('booking_link', 'shop_link')";
    
    $result = $conn->query($query);
    $settings = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'booking_link' => $settings['booking_link'] ?? '',
            'shop_link' => $settings['shop_link'] ?? ''
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving settings: ' . $e->getMessage()
    ]);
}
