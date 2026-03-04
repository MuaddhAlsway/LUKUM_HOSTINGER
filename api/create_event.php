<?php
/**
 * LAKUM Artspace - Create Event API
 * Creates a new event in the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required = ['title', 'description', 'location', 'event_date', 'category'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $db->prepare('INSERT INTO events (title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $title = $data['title'];
    $description = $data['description'];
    $location = $data['location'];
    $event_date = $data['event_date'];
    $event_time = $data['event_time'] ?? '10:00';
    $event_end_time = $data['event_end_time'] ?? '18:00';
    $cover_image = $data['cover_image'] ?? 'assest/img-4.png';
    $is_featured = $data['is_featured'] ?? 0;
    $category = $data['category'];
    
    $stmt->bind_param('sssssssii', $title, $description, $location, $event_date, $event_time, $event_end_time, $cover_image, $is_featured, $category);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $event_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'data' => ['id' => $event_id]
    ]);
    
} catch (Exception $e) {
    error_log('Create Event Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


