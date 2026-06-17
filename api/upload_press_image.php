<?php
/**
 * LAKUM Artspace - Upload Press Image API
 * Dedicated press image uploader — saves to uploads/uploads/press/
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $code = $_FILES['file']['error'] ?? 'unknown';
        throw new Exception('No file uploaded or upload error: ' . $code);
    }

    $file = $_FILES['file'];

    // Validate extension
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        throw new Exception('Invalid file type. Allowed: JPG, PNG, GIF, WebP, HEIC, HEIF');
    }

    // Validate size (max 5 MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File size exceeds 5 MB limit');
    }

    // Save to assest/press-uploads/ — accessible folder on the server
    $upload_dir = __DIR__ . '/../assest/press-uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    $filename = 'press_' . time() . '_' . uniqid() . '.' . $ext;
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Return the path — normalizer in get_press.php handles display
    $relative_path = 'assest/press-uploads/' . $filename;

    echo json_encode([
        'success'  => true,
        'message'  => 'Image uploaded successfully',
        'path'     => $relative_path,
        'filename' => $filename
    ]);

} catch (Exception $e) {
    error_log('Press Image Upload Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
