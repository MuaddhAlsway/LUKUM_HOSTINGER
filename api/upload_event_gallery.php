<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Upload Event Gallery Images API
 * Handles uploading and storing event gallery images
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Configuration
$uploadDir = '../assest/gallery/';
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

error_log('=== GALLERY UPLOAD API CALLED ===');
error_log('POST data: ' . json_encode($_POST));
error_log('FILES keys: ' . json_encode(array_keys($_FILES)));
error_log('FILES[images] structure: ' . json_encode($_FILES['images'] ?? 'NOT SET'));
error_log('REQUEST METHOD: ' . $_SERVER['REQUEST_METHOD']);
error_log('CONTENT TYPE: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
error_log('Upload dir: ' . $uploadDir);
error_log('Upload dir exists: ' . (is_dir($uploadDir) ? 'yes' : 'no'));
error_log('Upload dir writable: ' . (is_writable($uploadDir) ? 'yes' : 'no'));

try {
    $eventId = (int)($_POST['event_id'] ?? 0);
    
    error_log('Event ID: ' . $eventId);
    
    if (!$eventId) {
        throw new Exception('Event ID is required');
    }
    
    if (!isset($_FILES['images'])) {
        error_log('No images in FILES');
        throw new Exception('No images provided');
    }
    
    error_log('Images count: ' . (is_array($_FILES['images']['name']) ? count($_FILES['images']['name']) : 1));
    
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Verify event exists
    $checkQuery = "SELECT id FROM events WHERE id = $eventId";
    $checkResult = $conn->query($checkQuery);
    
    if (!$checkResult || $checkResult->num_rows === 0) {
        throw new Exception('Event not found');
    }
    
    $uploadedImages = [];
    $files = $_FILES['images'];
    
    // Handle single or multiple files
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;
    
    error_log('Processing ' . $fileCount . ' files');
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $fileTmp = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $fileType = is_array($files['type']) ? $files['type'][$i] : $files['type'];
        $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
        $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        
        error_log('File ' . $i . ': ' . $fileName . ' (type: ' . $fileType . ', size: ' . $fileSize . ', error: ' . $fileError . ')');
        
        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            error_log('File error: ' . $fileError);
            continue;
        }
        
        if ($fileSize > $maxFileSize) {
            error_log('File too large: ' . $fileSize);
            continue;
        }
        
        if (!in_array($fileType, $allowedTypes)) {
            error_log('Invalid file type: ' . $fileType);
            continue;
        }
        
        // Generate unique filename
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'event_' . $eventId . '_' . round(microtime(true) * 1000) . '_' . uniqid() . '.' . $ext;
        $uploadPath = $uploadDir . $newFileName;
        
        error_log('Uploading to: ' . $uploadPath);
        
        // Move uploaded file
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $imageUrl = 'assest/gallery/' . $newFileName;
            $caption = $conn->real_escape_string(pathinfo($fileName, PATHINFO_FILENAME));
            $displayOrder = count($uploadedImages) + 1;
            
            // Save to database
            $insertQuery = "INSERT INTO event_gallery (event_id, image_url, caption, display_order) 
                           VALUES ($eventId, '$imageUrl', '$caption', $displayOrder)";
            
            error_log('Insert query: ' . $insertQuery);
            
            if ($conn->query($insertQuery)) {
                error_log('Image saved to database: ' . $imageUrl);
                $uploadedImages[] = [
                    'url' => $imageUrl,
                    'name' => $newFileName
                ];
            } else {
                error_log('Database insert failed: ' . $conn->error);
            }
        } else {
            error_log('File move failed for: ' . $fileName);
        }
    }
    
    $conn->close();
    
    error_log('Upload complete. Images uploaded: ' . count($uploadedImages));
    
    if (empty($uploadedImages)) {
        throw new Exception('No images were uploaded successfully');
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($uploadedImages) . ' image(s) uploaded successfully',
        'images' => $uploadedImages
    ]);
    
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

