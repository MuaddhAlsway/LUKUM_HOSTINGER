<?php
/**
 * Save Settings API
 * Saves booking and shop links to the database
 * Automatically creates table if it doesn't exist
 */

header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

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
    
    // Include database connection
    require_once 'db-connect.php';
    
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
        // Check if setting exists
        $checkQuery = "SELECT id FROM site_settings WHERE setting_key = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Update existing setting
            $updateQuery = "UPDATE site_settings SET setting_value = ?, updated_at = NOW() 
                           WHERE setting_key = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $value, $key);
        } else {
            // Insert new setting
            $insertQuery = "INSERT INTO site_settings (setting_key, setting_value, created_at, updated_at) 
                           VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $key, $value);
        }
        
        if (!$stmt->execute()) {
            $allSuccess = false;
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
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
