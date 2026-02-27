<?php
/**
 * Create Contact Messages Table
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $conn->connect_error
        ]);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(255) NOT NULL,
        message LONGTEXT NOT NULL,
        status ENUM('new', 'read', 'responded') DEFAULT 'new',
        response_message LONGTEXT,
        response_date DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Contact messages table created successfully'
        ]);
    } else {
        throw new Exception('Error creating table: ' . $conn->error);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

