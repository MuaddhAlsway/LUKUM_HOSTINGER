<?php
/**
 * LAKUM Artspace - Update Blog API
 * Updates an existing blog post in the database
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
        throw new Exception('Missing blog ID');
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $id = $data['id'];
    $title = $data['title'] ?? null;
    $content = $data['content'] ?? null;
    $excerpt = $data['excerpt'] ?? null;
    $author = $data['author'] ?? null;
    $cover_image = $data['cover_image'] ?? null;
    $category = $data['category'] ?? null;
    $is_published = $data['is_published'] ?? null;
    
    $updates = [];
    $params = [];
    $types = '';
    
    if ($title !== null) { $updates[] = 'title = ?'; $params[] = $title; $types .= 's'; }
    if ($content !== null) { $updates[] = 'content = ?'; $params[] = $content; $types .= 's'; }
    if ($excerpt !== null) { $updates[] = 'excerpt = ?'; $params[] = $excerpt; $types .= 's'; }
    if ($author !== null) { $updates[] = 'author = ?'; $params[] = $author; $types .= 's'; }
    if ($cover_image !== null) { $updates[] = 'cover_image = ?'; $params[] = $cover_image; $types .= 's'; }
    if ($category !== null) { $updates[] = 'category = ?'; $params[] = $category; $types .= 's'; }
    if ($is_published !== null) { $updates[] = 'is_published = ?'; $params[] = $is_published; $types .= 'i'; }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $id;
    $types .= 'i';
    
    $query = 'UPDATE blogs SET ' . implode(', ', $updates) . ' WHERE id = ?';
    
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
        'message' => 'Blog updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Update Blog Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


