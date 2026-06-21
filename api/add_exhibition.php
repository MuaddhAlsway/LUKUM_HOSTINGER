<?php
/**
 * Add Exhibition API - SIMPLE & ROBUST VERSION
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any stray output
ob_start();

try {
    // Set JSON header first
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    // Simple database connection
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    // Set charset
    $conn->set_charset('utf8mb4');
    
    // Get JSON input
    $json_input = file_get_contents('php://input');
    $input = json_decode($json_input, true);
    
    if ($input === null) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Invalid JSON input'
        ]));
    }
    
    // Extract fields
    $title_en = isset($input['title_en']) ? trim($input['title_en']) : '';
    $title_ar = isset($input['title_ar']) ? trim($input['title_ar']) : '';
    $description_en = isset($input['description_en']) ? trim($input['description_en']) : '';
    $description_ar = isset($input['description_ar']) ? trim($input['description_ar']) : '';
    $location_en = isset($input['location_en']) ? trim($input['location_en']) : '';
    $location_ar = isset($input['location_ar']) ? trim($input['location_ar']) : '';
    $exhibition_date = isset($input['exhibition_date']) ? trim($input['exhibition_date']) : '';
    $exhibition_time = isset($input['exhibition_time']) ? trim($input['exhibition_time']) : '10:00:00';
    $exhibition_end_time = isset($input['exhibition_end_time']) ? trim($input['exhibition_end_time']) : '18:00:00';
    $end_date = isset($input['end_date']) && !empty($input['end_date']) ? trim($input['end_date']) : null;
    $cover_image = isset($input['cover_image']) ? trim($input['cover_image']) : 'assest/img-4.png';
    $category = 'exhibition';
    
    // Validate required fields
    if (empty($title_en)) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Exhibition title (English) is required'
        ]));
    }
    
    if (empty($exhibition_date)) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Exhibition date is required'
        ]));
    }
    
    if (empty($location_en)) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Location (English) is required'
        ]));
    }
    
    // Check if exhibitions table exists, if not create it
    $table_check = $conn->query("SHOW TABLES LIKE 'exhibitions'");
    
    if ($table_check->num_rows === 0) {
        // Table doesn't exist, create it
        $create_sql = "CREATE TABLE `exhibitions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title_en` VARCHAR(255) NOT NULL,
            `title_ar` VARCHAR(255),
            `description_en` LONGTEXT,
            `description_ar` LONGTEXT,
            `location_en` VARCHAR(255) NOT NULL,
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
            INDEX `idx_exhibition_date` (`exhibition_date`),
            INDEX `idx_category` (`category`),
            INDEX `idx_is_featured` (`is_featured`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($create_sql)) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'message' => 'Error creating table: ' . $conn->error
            ]));
        }
    }
    
    // Prepare insert statement
    $sql = "INSERT INTO exhibitions (
        title_en, title_ar, description_en, description_ar,
        location_en, location_ar, exhibition_date, exhibition_time,
        exhibition_end_time, end_date, cover_image, category
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Prepare error: ' . $conn->error
        ]));
    }
    
    // Bind parameters
    $bind_result = $stmt->bind_param(
        'ssssssssssss',
        $title_en, $title_ar, $description_en, $description_ar,
        $location_en, $location_ar, $exhibition_date, $exhibition_time,
        $exhibition_end_time, $end_date, $cover_image, $category
    );
    
    if (!$bind_result) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Bind error: ' . $stmt->error
        ]));
    }
    
    // Execute statement
    if ($stmt->execute()) {
        $exhibition_id = $stmt->insert_id;
        $stmt->close();
        
        // Clear output buffer
        ob_end_clean();
        
        // Return success
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Exhibition created successfully',
            'exhibition_id' => $exhibition_id,
            'data' => [
                'id' => $exhibition_id,
                'title_en' => $title_en,
                'exhibition_date' => $exhibition_date
            ]
        ]);
        exit;
    } else {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Execute error: ' . $stmt->error
        ]));
    }
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Exception: ' . $e->getMessage()
    ]);
    exit;
}

?>

