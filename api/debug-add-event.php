<?php
/**
 * Debug Add Event - Log exactly what's being received and saved
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Create logs directory if it doesn't exist
if (!is_dir('../logs')) {
    mkdir('../logs', 0755, true);
}

$logFile = '../logs/add_event_debug.log';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

try {
    writeLog('=== ADD EVENT DEBUG START ===');
    
    $rawInput = file_get_contents('php://input');
    writeLog('Raw Input: ' . $rawInput);
    
    $input = json_decode($rawInput, true);
    writeLog('Decoded Input: ' . json_encode($input));
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Check what we received
    writeLog('title_en: ' . ($input['title_en'] ?? 'NOT SET'));
    writeLog('title_ar: ' . ($input['title_ar'] ?? 'NOT SET'));
    writeLog('description_ar: ' . ($input['description_ar'] ?? 'NOT SET'));
    writeLog('location_ar: ' . ($input['location_ar'] ?? 'NOT SET'));
    
    // Validate required fields
    $title_en = $input['title_en'] ?? $input['title'] ?? '';
    
    if (empty($title_en)) {
        throw new Exception('Event title is required');
    }
    
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Extract English fields
    $description_en = $conn->real_escape_string($input['description_en'] ?? $input['description'] ?? '');
    $location_en = $conn->real_escape_string($input['location_en'] ?? $input['location'] ?? '');
    
    writeLog("English: title='$title_en', desc='$description_en', loc='$location_en'");
    
    // Extract Arabic fields (optional)
    $title_ar = isset($input['title_ar']) && !empty($input['title_ar']) ? $conn->real_escape_string($input['title_ar']) : null;
    $description_ar = isset($input['description_ar']) && !empty($input['description_ar']) ? $conn->real_escape_string($input['description_ar']) : null;
    $location_ar = isset($input['location_ar']) && !empty($input['location_ar']) ? $conn->real_escape_string($input['location_ar']) : null;
    
    writeLog("Arabic: title_ar=" . ($title_ar ? "YES: $title_ar" : "NULL") . ", desc_ar=" . ($description_ar ? "YES" : "NULL") . ", loc_ar=" . ($location_ar ? "YES: $location_ar" : "NULL"));
    
    // Extract base fields
    $event_date = $input['event_date'] ?? date('Y-m-d');
    $event_time = $input['event_time'] ?? '10:00:00';
    $event_end_time = $input['event_end_time'] ?? '18:00:00';
    $end_date = $input['end_date'] ?? null;
    $cover_image = $conn->real_escape_string($input['cover_image'] ?? 'assest/img-4.png');
    $video_url = $conn->real_escape_string($input['video_url'] ?? '');
    $category = $conn->real_escape_string($input['category'] ?? 'exhibition');
    $is_featured = (int)($input['is_featured'] ?? 0);
    
    // Generate slug from English title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title_en), '-'));
    
    // Make slug unique
    $original_slug = $slug;
    $counter = 1;
    while (true) {
        $check_query = "SELECT id FROM events WHERE slug = '$slug' LIMIT 1";
        $result = $conn->query($check_query);
        if (!$result || $result->num_rows === 0) {
            break;
        }
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    writeLog("Generated slug: $slug");
    
    // Insert event into events table
    $query = "
        INSERT INTO events (
            title, description, location, slug,
            event_date, event_time, event_end_time, end_date,
            cover_image, video_url, is_featured, category
        ) VALUES (
            '$title_en', '$description_en', '$location_en', '$slug',
            '$event_date', '$event_time', '$event_end_time', " . ($end_date ? "'$end_date'" : "NULL") . ",
            '$cover_image', '$video_url', $is_featured, '$category'
        )
    ";
    
    writeLog("Insert query: $query");
    
    if (!$conn->query($query)) {
        throw new Exception('Insert failed: ' . $conn->error);
    }
    
    $event_id = $conn->insert_id;
    writeLog("Event inserted with ID: $event_id");
    
    // Insert English translation into event_translations table
    $insertEnglishQuery = "
        INSERT INTO event_translations (event_id, language, title, description, location)
        VALUES ($event_id, 'en', '$title_en', '$description_en', '$location_en')
        ON DUPLICATE KEY UPDATE
            title = '$title_en',
            description = '$description_en',
            location = '$location_en',
            updated_at = CURRENT_TIMESTAMP
    ";
    
    writeLog("English translation query: $insertEnglishQuery");
    
    if (!$conn->query($insertEnglishQuery)) {
        writeLog("ERROR: English translation insert failed: " . $conn->error);
    } else {
        writeLog("English translation inserted successfully");
    }
    
    // Insert Arabic translation if provided
    $arabicInserted = false;
    if ($title_ar || $description_ar || $location_ar) {
        writeLog("Arabic data detected - attempting to save");
        
        $insertArabicQuery = "
            INSERT INTO event_translations (event_id, language, title, description, location)
            VALUES ($event_id, 'ar', " . 
            ($title_ar ? "'$title_ar'" : "NULL") . ", " .
            ($description_ar ? "'$description_ar'" : "NULL") . ", " .
            ($location_ar ? "'$location_ar'" : "NULL") . "
            )
            ON DUPLICATE KEY UPDATE
                title = " . ($title_ar ? "'$title_ar'" : "NULL") . ",
                description = " . ($description_ar ? "'$description_ar'" : "NULL") . ",
                location = " . ($location_ar ? "'$location_ar'" : "NULL") . ",
                updated_at = CURRENT_TIMESTAMP
        ";
        
        writeLog("Arabic translation query: $insertArabicQuery");
        
        if (!$conn->query($insertArabicQuery)) {
            writeLog("ERROR: Arabic translation insert failed: " . $conn->error);
        } else {
            writeLog("Arabic translation inserted successfully");
            $arabicInserted = true;
        }
    } else {
        writeLog("No Arabic data provided - skipping Arabic translation insert");
    }
    
    // Verify what was saved
    $verifyQuery = "SELECT * FROM event_translations WHERE event_id = $event_id";
    $verifyResult = $conn->query($verifyQuery);
    $translations = [];
    while ($row = $verifyResult->fetch_assoc()) {
        $translations[] = $row;
        writeLog("Verified translation: " . json_encode($row));
    }
    
    $conn->close();
    
    writeLog('=== ADD EVENT DEBUG END ===');
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'event_id' => $event_id,
        'slug' => $slug,
        'translations' => [
            'en' => true,
            'ar' => $arabicInserted
        ]
    ]);
    
} catch (Exception $e) {
    writeLog('ERROR: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


