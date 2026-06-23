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
    
    error_log('EDIT_EXHIBITION: Received input: ' . json_encode($input));
    
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
    
    // Handle event_video - convert empty string to NULL for proper removal
    $event_video = null;
    if (isset($input['event_video'])) {
        $video_trimmed = trim($input['event_video']);
        $event_video = ($video_trimmed !== '') ? $video_trimmed : null;
        error_log('EDIT_EXHIBITION: event_video input: "' . $video_trimmed . '" -> converted to: ' . var_export($event_video, true));
    }
    
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
    
    // Build SQL with proper NULL handling for video
    $sql = "UPDATE exhibitions SET 
        title_en = ?,
        title_ar = ?,
        description_en = ?,
        description_ar = ?,
        location_en = ?,
        location_ar = ?,
        exhibition_date = ?,
        exhibition_time = ?,
        exhibition_end_time = ?,
        end_date = ?,
        event_video = ";
    
    // Handle video NULL vs string
    if ($event_video === null) {
        $sql .= "NULL";
    } else {
        $sql .= "?";
    }
    
    // Add optional fields
    $bindTypes = 'ssssssssss';
    $bindParams = [
        &$title_en, &$title_ar, &$description_en, &$description_ar,
        &$location_en, &$location_ar, &$exhibition_date, &$exhibition_time,
        &$exhibition_end_time, &$end_date
    ];
    
    // Add event_video to binding if it's not NULL
    if ($event_video !== null) {
        $bindTypes .= 's';
        $bindParams[] = &$event_video;
    }
    
    // Add optional cover_image
    if ($cover_image !== null) {
        $sql .= ", cover_image = ?";
        $bindTypes .= 's';
        $bindParams[] = &$cover_image;
    }
    
    // Add optional gallery_images
    if ($gallery_images !== null) {
        $sql .= ", gallery_images = ?";
        $bindTypes .= 's';
        $bindParams[] = &$gallery_images;
    }
    
    // WHERE clause
    $sql .= " WHERE id = ?";
    $bindTypes .= 'i';
    $bindParams[] = &$id;
    
    error_log('EDIT_EXHIBITION: SQL Query: ' . $sql);
    error_log('EDIT_EXHIBITION: Bind Types: ' . $bindTypes);
    error_log('EDIT_EXHIBITION: event_video value being bound: ' . var_export($event_video, true));
    
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
        
        // Verify the update by querying the database
        $verify_sql = "SELECT event_video FROM exhibitions WHERE id = ?";
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param('i', $id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        $verify_row = $verify_result->fetch_assoc();
        error_log('EDIT_EXHIBITION: After update, event_video in DB is: ' . var_export($verify_row['event_video'], true));
        $verify_stmt->close();
        
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
