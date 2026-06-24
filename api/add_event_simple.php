<?php
// Enable output buffering to catch any stray output
ob_start();

// CRITICAL: Prevent session from being auto-started
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Session not started yet, safe to configure
} else {
    // Session already started, just log it
    error_log('Warning: Session already started before config.php included');
}

// Set JSON header FIRST before anything else
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Set error handler to prevent HTML output (includes fatal errors)
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean(); // Clear any buffered output
    error_log("PHP Error [$errno]: $errstr in $errfile:$errline");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $errstr,
        'error_code' => 'PHP_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}, E_ALL);

// Also set exception handler for any exceptions that slip through
set_exception_handler(function($exception) {
    ob_end_clean(); // Clear any buffered output
    error_log("PHP Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $exception->getMessage(),
        'error_code' => 'PHP_EXCEPTION',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
});

require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Add Event API (Simple)
 * Handles bilingual event creation with Arabic translations
 * Uses prepared statements for security
 */

try {
    // Get raw input
    $rawInput = file_get_contents('php://input');
    error_log('Add Event - Raw input: ' . substr($rawInput, 0, 200));
    
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }
    
    // Validate required fields - support both title and title_en
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    
    if (empty($title_en)) {
        throw new Exception('Event title is required');
    }
    
    error_log('Add Event - Processing event: ' . $title_en);
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        $conn = $db->getConnection();
        $connError = $conn ? $conn->connect_error : 'Connection object is null';
        error_log('Add Event - DB Connection Error: ' . $connError);
        throw new Exception('Database connection failed: ' . $connError);
    }
    
    error_log('Add Event - Database connected successfully');
    
    $conn = $db->getConnection();
    
    // Extract English fields
    $description_en = $input['description_en'] ?? $input['description'] ?? '';
    $location_en = $input['location_en'] ?? $input['location'] ?? '';
    
    // Extract Arabic fields (optional)
    $title_ar = $input['title_ar'] ?? '';
    $description_ar = $input['description_ar'] ?? '';
    $location_ar = $input['location_ar'] ?? '';
    
    // Extract base fields
    $event_date = $input['event_date'] ?? date('Y-m-d');
    $event_time = $input['event_time'] ?? '10:00:00';
    $event_end_time = $input['event_end_time'] ?? '18:00:00';
    $end_date = empty($input['end_date']) ? null : $input['end_date'];
    $cover_image = $input['cover_image'] ?? 'assest/img-4.png';
    $video_url = $input['video_url'] ?? '';
    $category = $input['category'] ?? 'event';
    $is_featured = (int)($input['is_featured'] ?? 0);
    
    // Check which columns exist in the events table
    $columnsCheckQuery = "SHOW COLUMNS FROM events";
    $columnsResult = $conn->query($columnsCheckQuery);
    if (!$columnsResult) {
        throw new Exception('Failed to check table schema: ' . $conn->error);
    }
    
    $existingColumns = [];
    while ($col = $columnsResult->fetch_assoc()) {
        $existingColumns[] = $col['Field'];
    }
    
    error_log('Add Event - Existing columns: ' . implode(', ', $existingColumns));
    
    // Check if bilingual columns exist
    $hasBilingualColumns = in_array('title_en', $existingColumns);
    $hasSlugColumn = in_array('slug', $existingColumns);
    
    error_log('Add Event - Has bilingual columns: ' . ($hasBilingualColumns ? 'YES' : 'NO'));
    error_log('Add Event - Has slug column: ' . ($hasSlugColumn ? 'YES' : 'NO'));
    
    // Generate slug if column exists
    $slug = null;
    if ($hasSlugColumn) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
        error_log('Add Event - Generated initial slug: ' . $slug);
        
        // Make slug unique by adding a number if it already exists using prepared statement
        $original_slug = $slug;
        $counter = 1;
        while (true) {
            $check_query = "SELECT id FROM events WHERE slug = ? LIMIT 1";
            $check_stmt = $conn->prepare($check_query);
            if (!$check_stmt) {
                throw new Exception('Prepare slug check failed: ' . $conn->error);
            }
            $check_stmt->bind_param('s', $slug);
            if (!$check_stmt->execute()) {
                throw new Exception('Execute slug check failed: ' . $check_stmt->error);
            }
            $result = $check_stmt->get_result();
            if ($result->num_rows === 0) {
                $check_stmt->close();
                error_log('Add Event - Final slug: ' . $slug);
                break; // Slug is unique
            }
            $check_stmt->close();
            $slug = $original_slug . '-' . $counter;
            error_log('Add Event - Slug already exists, trying: ' . $slug);
            $counter++;
        }
    }
    
    // Build INSERT query based on available columns
    if ($hasBilingualColumns) {
        // Full bilingual support
        $query = "
            INSERT INTO events (
                title, description, location, " . ($hasSlugColumn ? 'slug,' : '') . "
                title_en, description_en, location_en,
                title_ar, description_ar, location_ar,
                event_date, event_time, event_end_time, end_date,
                cover_image, video_url, is_featured, category
            ) VALUES (
                ?, ?, ?, " . ($hasSlugColumn ? '?,' : '') . "
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?
            )
        ";
        
        error_log('Add Event - Preparing insert statement (bilingual)');
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Insert prepare failed: ' . $conn->error);
        }
        
        error_log('Add Event - Binding parameters (bilingual)');
        
        if ($hasSlugColumn) {
            // With slug
            $stmt->bind_param(
                'sssssssssssssssis',
                $title_en,          // title (copy of title_en)
                $description_en,    // description (copy of description_en)
                $location_en,       // location (copy of location_en)
                $slug,              // slug
                $title_en,          // title_en
                $description_en,    // description_en
                $location_en,       // location_en
                $title_ar,          // title_ar
                $description_ar,    // description_ar
                $location_ar,       // location_ar
                $event_date,        // event_date
                $event_time,        // event_time
                $event_end_time,    // event_end_time
                $end_date,          // end_date
                $cover_image,       // cover_image
                $video_url,         // video_url
                $is_featured,       // is_featured (integer)
                $category           // category
            );
        } else {
            // Without slug
            $stmt->bind_param(
                'ssssssssssssssis',
                $title_en,          // title (copy of title_en)
                $description_en,    // description (copy of description_en)
                $location_en,       // location (copy of location_en)
                $title_en,          // title_en
                $description_en,    // description_en
                $location_en,       // location_en
                $title_ar,          // title_ar
                $description_ar,    // description_ar
                $location_ar,       // location_ar
                $event_date,        // event_date
                $event_time,        // event_time
                $event_end_time,    // event_end_time
                $end_date,          // end_date
                $cover_image,       // cover_image
                $video_url,         // video_url
                $is_featured,       // is_featured (integer)
                $category           // category
            );
        }
    } else {
        // Legacy support - just title, description, location
        $query = "
            INSERT INTO events (
                title, description, location, " . ($hasSlugColumn ? 'slug,' : '') . "
                event_date, event_time, event_end_time, end_date,
                cover_image, video_url, is_featured, category
            ) VALUES (
                ?, ?, ?, " . ($hasSlugColumn ? '?,' : '') . "
                ?, ?, ?, ?,
                ?, ?, ?, ?
            )
        ";
        
        error_log('Add Event - Preparing insert statement (legacy)');
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Insert prepare failed: ' . $conn->error);
        }
        
        error_log('Add Event - Binding parameters (legacy)');
        
        if ($hasSlugColumn) {
            // With slug
            $stmt->bind_param(
                'sssssssssssis',
                $title_en,          // title
                $description_en,    // description
                $location_en,       // location
                $slug,              // slug
                $event_date,        // event_date
                $event_time,        // event_time
                $event_end_time,    // event_end_time
                $end_date,          // end_date
                $cover_image,       // cover_image
                $video_url,         // video_url
                $is_featured,       // is_featured (integer)
                $category           // category
            );
        } else {
            // Without slug
            $stmt->bind_param(
                'ssssssssssis',
                $title_en,          // title
                $description_en,    // description
                $location_en,       // location
                $event_date,        // event_date
                $event_time,        // event_time
                $event_end_time,    // event_end_time
                $end_date,          // end_date
                $cover_image,       // cover_image
                $video_url,         // video_url
                $is_featured,       // is_featured (integer)
                $category           // category
            );
        }
    }
    
    error_log('Add Event - Executing insert');
    if (!$stmt->execute()) {
        throw new Exception('Insert execute failed: ' . $stmt->error);
    }
    
    error_log('Add Event - Insert successful');
    $event_id = $conn->insert_id;
    $stmt->close();
    
    if (!$event_id) {
        throw new Exception('Failed to get inserted event ID');
    }
    
    error_log('Add Event - Event created successfully with ID: ' . $event_id);
    
    ob_end_clean(); // Clear output buffer before sending response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'event_id' => $event_id,
        'slug' => $slug,
        'event' => [
            'id' => $event_id,
            'title_en' => $title_en,
            'title_ar' => $title_ar,
            'description_en' => $description_en,
            'description_ar' => $description_ar,
            'location_en' => $location_en,
            'location_ar' => $location_ar,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'event_end_time' => $event_end_time,
            'end_date' => $end_date,
            'cover_image' => $cover_image,
            'video_url' => $video_url,
            'slug' => $slug
        ]
    ]);
    ob_end_flush(); // Send the response
    
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    error_log('Add Event Error: ' . $errorMsg);
    error_log('Add Event Error Trace: ' . $e->getTraceAsString());
    
    ob_end_clean(); // Clear output buffer before sending error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'error_code' => 'ADD_EVENT_FAILED',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}
?>
