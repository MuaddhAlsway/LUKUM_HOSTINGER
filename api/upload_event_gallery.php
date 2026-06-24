<?php
ob_start();

require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Upload Event Gallery Images API
 * Handles uploading and storing event gallery images
 * Uses Database::getInstance() and prepared statements for security
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration
$uploadDir = __DIR__ . '/../assest/event-gallery/';
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'];

try {
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Verify directory is writable
    if (!is_writable($uploadDir)) {
        error_log('ERROR: Upload directory is not writable: ' . $uploadDir);
        throw new Exception('Upload directory is not writable');
    }
    
    error_log('=== GALLERY UPLOAD API CALLED ===');
    error_log('REQUEST METHOD: ' . $_SERVER['REQUEST_METHOD']);
    error_log('POST keys: ' . json_encode(array_keys($_POST)));
    error_log('FILES keys: ' . json_encode(array_keys($_FILES)));
    
    $eventId = (int)($_POST['event_id'] ?? 0);
    error_log('Event ID: ' . $eventId);
    
    if (!$eventId || $eventId <= 0) {
        throw new Exception('Event ID is required and must be a positive integer');
    }
    
    // Check for images - handle both 'images' and 'images[]' keys
    $hasImages = false;
    $imagesKey = null;
    
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'])) {
        $hasImages = true;
        $imagesKey = 'images';
    } elseif (isset($_FILES['images[]']) && !empty($_FILES['images[]']['name'])) {
        $hasImages = true;
        $imagesKey = 'images[]';
    }
    
    if (!$hasImages) {
        throw new Exception('No images provided');
    }
    
    error_log('Images key: ' . $imagesKey);
    error_log('Images count: ' . (is_array($_FILES[$imagesKey]['name']) ? count($_FILES[$imagesKey]['name']) : 1));
    
    // Get database connection
    $db = Database::getInstance();
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    // Verify event exists using prepared statement
    $checkQuery = "SELECT id FROM events WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception('Prepare check query failed: ' . $conn->error);
    }
    $checkStmt->bind_param('i', $eventId);
    if (!$checkStmt->execute()) {
        throw new Exception('Execute check query failed: ' . $checkStmt->error);
    }
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows === 0) {
        throw new Exception('Event not found with ID: ' . $eventId);
    }
    $checkStmt->close();
    
    error_log('Event found with ID: ' . $eventId);
    
    $uploadedImages = [];
    $files = $_FILES[$imagesKey];
    
    // Handle single or multiple files
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;
    error_log('Processing ' . $fileCount . ' files');
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $fileTmp = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $fileType = is_array($files['type']) ? $files['type'][$i] : $files['type'];
        $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
        $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        
        error_log('File ' . $i . ': ' . $fileName . ' (size: ' . $fileSize . ', error: ' . $fileError . ', tmp: ' . $fileTmp . ')');
        
        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            error_log('File error: ' . $fileError);
            continue;
        }
        
        // Verify temp file exists
        if (!file_exists($fileTmp)) {
            error_log('ERROR: Temp file does not exist: ' . $fileTmp);
            continue;
        }
        
        if ($fileSize > $maxFileSize) {
            error_log('File too large: ' . $fileSize);
            continue;
        }
        
        // Check file extension (more reliable than MIME type)
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            error_log('Invalid file extension: ' . $ext);
            continue;
        }
        
        // Generate unique filename
        $newFileName = 'event_' . $eventId . '_' . round(microtime(true) * 1000) . '_' . uniqid() . '.' . $ext;
        $uploadPath = $uploadDir . $newFileName;
        
        error_log('Uploading to: ' . $uploadPath);
        
        // Move uploaded file
        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            error_log('File move failed for: ' . $fileName . ' from ' . $fileTmp);
            continue;
        }
        
        // Verify file was moved
        if (!file_exists($uploadPath)) {
            error_log('ERROR: File not found after move: ' . $uploadPath);
            continue;
        }
        
        $imageUrl = 'assest/event-gallery/' . $newFileName;
        $caption = pathinfo($fileName, PATHINFO_FILENAME);
        $displayOrder = count($uploadedImages) + 1;
        
        // Save to database using prepared statement
        $insertQuery = "INSERT INTO event_gallery (event_id, image_url, caption, display_order) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            error_log('Prepare insert query failed: ' . $conn->error);
            continue;
        }
        
        $insertStmt->bind_param('issi', $eventId, $imageUrl, $caption, $displayOrder);
        if (!$insertStmt->execute()) {
            error_log('Database insert failed: ' . $insertStmt->error);
            $insertStmt->close();
            continue;
        }
        $insertStmt->close();
        
        error_log('Image saved to database: ' . $imageUrl);
        $uploadedImages[] = [
            'url' => $imageUrl,
            'name' => $newFileName
        ];
    }
    
    error_log('Upload complete. Images uploaded: ' . count($uploadedImages));
    
    if (empty($uploadedImages)) {
        throw new Exception('No images were uploaded successfully');
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => count($uploadedImages) . ' image(s) uploaded successfully',
        'images' => $uploadedImages,
        'count' => count($uploadedImages)
    ]);
    
} catch (Exception $e) {
    error_log('Gallery Upload Exception: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
