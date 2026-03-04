<?php
/**
 * LAKUM Artspace - File Upload API
 * Handles file uploads for events, blogs, press, etc.
 */

require_once 'config.php';

// Check authentication
session_start();
if (!isset($_SESSION['admin_id'])) {
    die(Response::error('Unauthorized', 401));
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(Response::error('Method not allowed', 405));
}

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    die(Response::error('No file uploaded', 400));
}

$file = $_FILES['file'];
$type = $_POST['type'] ?? 'general'; // covers, galleries, press, etc.

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    die(Response::error($errors[$file['error']] ?? 'Unknown error', 400));
}

// Check file size
if ($file['size'] > MAX_UPLOAD_SIZE) {
    die(Response::error('File size exceeds maximum allowed size', 400));
}

// Get file extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validate extension
if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    die(Response::error('File type not allowed', 400));
}

try {
    // Create upload directory if it doesn't exist
    $upload_path = UPLOAD_DIR . $type . '/';
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $upload_path . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Set proper permissions
    chmod($filepath, 0644);
    
    // Log activity
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $log_stmt = $conn->prepare("
        INSERT INTO activity_logs (admin_id, action, entity_type, entity_id, details, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $action = 'upload_file';
    $entity_type = 'file';
    $entity_id = 0;
    $details = json_encode([
        'filename' => $filename,
        'original_name' => $file['name'],
        'size' => $file['size'],
        'type' => $type
    ]);
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $log_stmt->bind_param('issis', $_SESSION['admin_id'], $action, $entity_type, $entity_id, $details, $ip);
    $log_stmt->execute();
    
    // Return success with file path
    $file_url = UPLOAD_URL . $type . '/' . $filename;
    echo Response::success(
        [
            'filename' => $filename,
            'url' => $file_url,
            'path' => $type . '/' . $filename
        ],
        'File uploaded successfully',
        201
    );
    
} catch (Exception $e) {
    error_log($e->getMessage());
    echo Response::error('Failed to upload file: ' . $e->getMessage(), 500);
}


