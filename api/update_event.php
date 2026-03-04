<?php
/**
 * LAKUM Artspace - Update Event API
 * Updates an existing event in the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['id'])) {
        throw new Exception('Missing event ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $id = $data['id'];
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
    $location = $data['location'] ?? null;
    $event_date = $data['event_date'] ?? null;
    $event_time = $data['event_time'] ?? null;
    $event_end_time = $data['event_end_time'] ?? null;
    $cover_image = $data['cover_image'] ?? null;
    $is_featured = $data['is_featured'] ?? null;
    $category = $data['category'] ?? null;
    
    $updates = [];
    $params = [];
    $types = '';
    
    if ($title !== null) { $updates[] = 'title = ?'; $params[] = $title; $types .= 's'; }
    if ($description !== null) { $updates[] = 'description = ?'; $params[] = $description; $types .= 's'; }
    if ($location !== null) { $updates[] = 'location = ?'; $params[] = $location; $types .= 's'; }
    if ($event_date !== null) { $updates[] = 'event_date = ?'; $params[] = $event_date; $types .= 's'; }
    if ($event_time !== null) { $updates[] = 'event_time = ?'; $params[] = $event_time; $types .= 's'; }
    if ($event_end_time !== null) { $updates[] = 'event_end_time = ?'; $params[] = $event_end_time; $types .= 's'; }
    if ($cover_image !== null) { $updates[] = 'cover_image = ?'; $params[] = $cover_image; $types .= 's'; }
    if ($is_featured !== null) { $updates[] = 'is_featured = ?'; $params[] = $is_featured; $types .= 'i'; }
    if ($category !== null) { $updates[] = 'category = ?'; $params[] = $category; $types .= 's'; }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $id;
    $types .= 'i';
    
    $query = 'UPDATE events SET ' . implode(', ', $updates) . ' WHERE id = ?';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Event updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Update Event Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


