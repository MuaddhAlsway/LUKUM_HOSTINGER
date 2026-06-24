<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Edit Event API (Simple)
 * Handles bilingual event updates with prepared statements for security
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['id'])) {
        throw new Exception('Event ID is required');
    }
    
    $event_id = (int)$input['id'];
    
    error_log('=== EDIT EVENT API ===');
    error_log('Event ID: ' . $event_id);
    error_log('Input data: ' . json_encode($input));
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Extract English fields
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    $description_en = $input['description_en'] ?? $input['description'] ?? '';
    $location_en = $input['location_en'] ?? $input['location'] ?? '';
    
    // Extract Arabic fields
    $title_ar = $input['title_ar'] ?? '';
    $description_ar = $input['description_ar'] ?? '';
    $location_ar = $input['location_ar'] ?? '';
    
    // Extract other fields
    $event_date = $input['event_date'] ?? null;
    $event_time = $input['event_time'] ?? null;
    $event_end_time = $input['event_end_time'] ?? null;
    $end_date = $input['end_date'] ?? null;
    $cover_image = isset($input['cover_image']) ? $input['cover_image'] : null;
    $video_url = $input['video_url'] ?? '';
    $category = $input['category'] ?? 'exhibition';
    $is_featured = isset($input['is_featured']) ? (int)$input['is_featured'] : null;
    
    // Build update query for events table with bilingual columns using prepared statement
    $updates = [];
    $params = [];
    $types = '';
    
    if (!empty($title_en)) {
        $updates[] = "title = ?";
        $updates[] = "title_en = ?";
        $params[] = $title_en;
        $params[] = $title_en;
        $types .= 'ss';
    }
    if (!empty($description_en)) {
        $updates[] = "description = ?";
        $updates[] = "description_en = ?";
        $params[] = $description_en;
        $params[] = $description_en;
        $types .= 'ss';
    }
    if (!empty($location_en)) {
        $updates[] = "location = ?";
        $updates[] = "location_en = ?";
        $params[] = $location_en;
        $params[] = $location_en;
        $types .= 'ss';
    }
    
    // Update Arabic columns if provided
    if (!empty($title_ar)) {
        $updates[] = "title_ar = ?";
        $params[] = $title_ar;
        $types .= 's';
    }
    if (!empty($description_ar)) {
        $updates[] = "description_ar = ?";
        $params[] = $description_ar;
        $types .= 's';
    }
    if (!empty($location_ar)) {
        $updates[] = "location_ar = ?";
        $params[] = $location_ar;
        $types .= 's';
    }
    
    if (!empty($event_date)) {
        $updates[] = "event_date = ?";
        $params[] = $event_date;
        $types .= 's';
    }
    if (!empty($event_time)) {
        $updates[] = "event_time = ?";
        $params[] = $event_time;
        $types .= 's';
    }
    if (!empty($event_end_time)) {
        $updates[] = "event_end_time = ?";
        $params[] = $event_end_time;
        $types .= 's';
    }
    if (!empty($end_date)) {
        $updates[] = "end_date = ?";
        $params[] = $end_date;
        $types .= 's';
    }
    if ($cover_image !== null) {
        $updates[] = "cover_image = ?";
        $params[] = $cover_image;
        $types .= 's';
    }
    
    // Handle video_url - allow clearing it (empty string or NULL)
    if (isset($input['video_url'])) {
        if ($video_url === '') {
            $updates[] = "video_url = NULL";
        } else {
            $updates[] = "video_url = ?";
            $params[] = $video_url;
            $types .= 's';
        }
    }
    
    if (!empty($category)) {
        $updates[] = "category = ?";
        $params[] = $category;
        $types .= 's';
    }
    if ($is_featured !== null) {
        $updates[] = "is_featured = ?";
        $params[] = $is_featured;
        $types .= 'i';
    }
    
    if (!empty($updates)) {
        // Add event_id to params for WHERE clause
        $params[] = $event_id;
        $types .= 'i';
        
        $query = "UPDATE events SET " . implode(", ", $updates) . " WHERE id = ?";
        error_log('Update query: ' . $query);
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        // Dynamically bind parameters
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Update failed: ' . $stmt->error);
        }
        $stmt->close();
    }
    
    // Update translations table with English and Arabic
    // Only update if we have at least one field to update
    if (!empty($title_en) || !empty($description_en) || !empty($location_en)) {
        // Update or insert English translation
        $translationQuery = "
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES (?, 'en', ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                location = VALUES(location),
                updated_at = CURRENT_TIMESTAMP
        ";
        
        error_log('English translation query: ' . $translationQuery);
        
        $translationStmt = $conn->prepare($translationQuery);
        if (!$translationStmt) {
            error_log('Warning: Prepare English translation failed: ' . $conn->error);
        } else {
            $translationStmt->bind_param('isss', $event_id, $title_en, $description_en, $location_en);
            if (!$translationStmt->execute()) {
                error_log('Warning: English translation update failed: ' . $translationStmt->error);
            }
            $translationStmt->close();
        }
    }
    
    // Update Arabic translation if provided
    if (!empty($title_ar) || !empty($description_ar) || !empty($location_ar)) {
        $translationQuery = "
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES (?, 'ar', ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                location = VALUES(location),
                updated_at = CURRENT_TIMESTAMP
        ";
        
        error_log('Arabic translation query: ' . $translationQuery);
        
        $translationStmt = $conn->prepare($translationQuery);
        if (!$translationStmt) {
            error_log('Warning: Prepare Arabic translation failed: ' . $conn->error);
        } else {
            $translationStmt->bind_param('isss', $event_id, $title_ar, $description_ar, $location_ar);
            if (!$translationStmt->execute()) {
                error_log('Warning: Arabic translation update failed: ' . $translationStmt->error);
            }
            $translationStmt->close();
        }
    }
    
    error_log('Event updated successfully');
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event updated successfully',
        'event_id' => $event_id
    ]);
    
} catch (Exception $e) {
    error_log('Edit Event Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>



