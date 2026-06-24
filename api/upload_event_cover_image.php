<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Upload Event Cover Image API
 * Handles image file uploads for event cover images
 * Uses Database::getInstance() and prepared statements
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error: ' . ($_FILES['file']['error'] ?? 'unknown'));
    }
    
    $file = $_FILES['file'];
    
    // Validate file type - check both MIME type and extension
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check extension first (more reliable than MIME type)
    if (!in_array($ext, $allowed_extensions)) {
        throw new Exception('Invalid file type. Allowed: JPEG, PNG, GIF, WebP, HEIC, HEIF');
    }
    
    // Also check MIME type if available
    if (!in_array($file['type'], $allowed_types) && !empty($file['type'])) {
        // Log but don't fail - MIME type detection can be unreliable
        error_log('Warning: Unexpected MIME type for ' . $file['name'] . ': ' . $file['type']);
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        throw new Exception('File size exceeds 5MB limit');
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . '/../assest/event-uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Verify directory is writable
    if (!is_writable($upload_dir)) {
        throw new Exception('Upload directory is not writable');
    }
    
    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'event-cover-' . time() . '-' . uniqid() . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Return relative path for database storage
    $relative_path = 'assest/event-uploads/' . $filename;
    
    error_log('Event cover image uploaded successfully: ' . $relative_path);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'path' => $relative_path,
        'filename' => $filename
    ]);
    
} catch (Exception $e) {
    error_log('Event Cover Image Upload Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
