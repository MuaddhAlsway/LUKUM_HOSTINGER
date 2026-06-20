<?php
/**
 * Add Exhibition API
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$db->isConnected()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if JSON decode failed
if ($input === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Validate required fields
$title_en = isset($input['title_en']) ? trim($input['title_en']) : '';
$exhibition_date = isset($input['exhibition_date']) ? trim($input['exhibition_date']) : '';
$location_en = isset($input['location_en']) ? trim($input['location_en']) : '';

if (!$title_en || !$exhibition_date || !$location_en) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$title_ar = isset($input['title_ar']) ? trim($input['title_ar']) : '';
$description_en = isset($input['description_en']) ? trim($input['description_en']) : '';
$description_ar = isset($input['description_ar']) ? trim($input['description_ar']) : '';
$location_ar = isset($input['location_ar']) ? trim($input['location_ar']) : '';
$exhibition_time = isset($input['exhibition_time']) ? trim($input['exhibition_time']) : '10:00:00';
$exhibition_end_time = isset($input['exhibition_end_time']) ? trim($input['exhibition_end_time']) : '18:00:00';
$end_date = isset($input['end_date']) && !empty($input['end_date']) ? trim($input['end_date']) : null;
$cover_image = isset($input['cover_image']) ? trim($input['cover_image']) : 'assest/img-4.png';
$category = 'exhibition';

// Ensure times have seconds if missing
if (strlen($exhibition_time) == 5) $exhibition_time .= ':00';
if (strlen($exhibition_end_time) == 5) $exhibition_end_time .= ':00';

// First, check if table exists
$tableCheckSql = "SHOW TABLES LIKE 'exhibitions'";
$tableResult = $conn->query($tableCheckSql);

if (!$tableResult || $tableResult->num_rows === 0) {
    // Table doesn't exist, try to create it
    $createTableSql = "CREATE TABLE IF NOT EXISTS `exhibitions` (
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
    
    if (!$conn->query($createTableSql)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating exhibitions table: ' . $conn->error]);
        exit;
    }
}

// Insert into database
$sql = "INSERT INTO exhibitions (
    title_en, title_ar, description_en, description_ar,
    location_en, location_ar, exhibition_date, exhibition_time,
    exhibition_end_time, end_date, cover_image, category
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$bind_result = $stmt->bind_param(
    'ssssssssssss',
    $title_en, $title_ar, $description_en, $description_ar,
    $location_en, $location_ar, $exhibition_date, $exhibition_time,
    $exhibition_end_time, $end_date, $cover_image, $category
);

if (!$bind_result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Binding error: ' . $stmt->error]);
    exit;
}

if ($stmt->execute()) {
    $exhibition_id = $stmt->insert_id;
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Exhibition created successfully',
        'exhibition_id' => $exhibition_id
    ]);
    $stmt->close();
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database execution error: ' . $stmt->error]);
    $stmt->close();
    exit;
}

?>

