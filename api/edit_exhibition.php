<?php
/**
 * Edit Exhibition API - Update exhibition
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

try {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    // Database credentials
    $db_host = 'localhost';
    $db_user = 'u812122863_neama';
    $db_pass = 'Nema202610!LakumDB';
    $db_name = 'u812122863_lakum_artspace';
    
    // Connect
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
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
    $id = isset($input['id']) ? intval($input['id']) : 0;
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
    $cover_image = isset($input['cover_image']) ? trim($input['cover_image']) : null;
    $event_video = isset($input['event_video']) && !empty($input['event_video']) ? trim($input['event_video']) : null;
    $gallery_images = isset($input['gallery_images']) && !empty($input['gallery_images']) ? trim($input['gallery_images']) : null;
    
    // Validate required fields
    if (!$id) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Exhibition ID is required'
        ]));
    }
    
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
    
    // Build update query - handle all combinations of optional fields
    $updateFields = [];
    $bindTypes = '';
    $bindParams = [];
    
    $updateFields[] = 'title_en = ?';
    $updateFields[] = 'title_ar = ?';
    $updateFields[] = 'description_en = ?';
    $updateFields[] = 'description_ar = ?';
    $updateFields[] = 'location_en = ?';
    $updateFields[] = 'location_ar = ?';
    $updateFields[] = 'exhibition_date = ?';
    $updateFields[] = 'exhibition_time = ?';
    $updateFields[] = 'exhibition_end_time = ?';
    $updateFields[] = 'end_date = ?';
    
    $bindTypes = 'ssssssssss';
    $bindParams = [&$title_en, &$title_ar, &$description_en, &$description_ar,
                   &$location_en, &$location_ar, &$exhibition_date, &$exhibition_time,
                   &$exhibition_end_time, &$end_date];
    
    if ($cover_image !== null) {
        $updateFields[] = 'cover_image = ?';
        $bindTypes .= 's';
        $bindParams[] = &$cover_image;
    }
    
    if ($event_video !== null) {
        $updateFields[] = 'event_video = ?';
        $bindTypes .= 's';
        $bindParams[] = &$event_video;
    }
    
    if ($gallery_images !== null) {
        $updateFields[] = 'gallery_images = ?';
        $bindTypes .= 's';
        $bindParams[] = &$gallery_images;
    }
    
    $updateFields[] = 'id = ?';
    $bindTypes .= 'i';
    $bindParams[] = &$id;
    
    $sql = "UPDATE exhibitions SET " . implode(', ', array_slice($updateFields, 0, -1)) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Prepare error: ' . $conn->error
        ]));
    }
    
    // Bind all parameters
    $bind_result = call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));
    
    if (!$bind_result) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Bind error: ' . $stmt->error
        ]));
    }
    
    // Execute statement
    if ($stmt->execute()) {
        $stmt->close();
        
        // Clear output buffer
        ob_end_clean();
        
        // Return success
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Exhibition updated successfully',
            'exhibition_id' => $id
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
