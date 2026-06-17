<?php
/**
 * Save Settings API
 * Saves booking and shop links to the database
 * Automatically creates table if it doesn't exist
 */

header('Content-Type: application/json');
session_start();

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit();
    }
    
    $bookingLink = $input['booking_link'] ?? '';
    $shopLink = $input['shop_link'] ?? '';
    
    // Validate URLs
    if (!filter_var($bookingLink, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking link URL']);
        exit();
    }
    
    if (!filter_var($shopLink, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid shop link URL']);
        exit();
    }
    
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
    
    // Save or update settings
    $settings = [
        'booking_link' => $bookingLink,
        'shop_link' => $shopLink
    ];
    
    $allSuccess = true;
    
    foreach ($settings as $key => $value) {
        // Use INSERT ... ON DUPLICATE KEY UPDATE for more reliable upsert
        $upsertQuery = "INSERT INTO site_settings (setting_key, setting_value, updated_at) 
                        VALUES (?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        setting_value = VALUES(setting_value),
                        updated_at = NOW()";
        
        $stmt = $conn->prepare($upsertQuery);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $key, $value);
        
        if (!$stmt->execute()) {
            $allSuccess = false;
            error_log("Failed to upsert setting $key: " . $conn->error);
            break;
        }
        $stmt->close();
    }
    
    if ($allSuccess) {
        echo json_encode([
            'success' => true,
            'message' => 'Settings saved successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error saving settings: ' . $conn->error
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
