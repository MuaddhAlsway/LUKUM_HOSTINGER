<?php
/**
 * LAKUM Artspace - Change Password API
 * Allows admins to change their password
 * Accepts both JSON and form data
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = null;
    
    // Try to get input from JSON first
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        // JSON input
        $rawInput = file_get_contents('php://input');
        
        if (empty($rawInput)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Request body is empty']);
            exit;
        }
        
        // Remove BOM if present
        if (substr($rawInput, 0, 3) === pack('CCC', 0xef, 0xbb, 0xbf)) {
            $rawInput = substr($rawInput, 3);
        }
        
        // Trim whitespace
        $rawInput = trim($rawInput);
        
        // Try to decode JSON
        $input = json_decode($rawInput, true);
        
        if ($input === null) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid JSON: ' . json_last_error_msg()
            ]);
            exit;
        }
    } else {
        // Try form data
        $input = $_POST;
    }
    
    // Validate inputs exist
    if (!isset($input['old_password']) || !isset($input['new_password']) || !isset($input['confirm_password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Get values
    $oldPassword = $input['old_password'];
    $newPassword = $input['new_password'];
    $confirmPassword = $input['confirm_password'];

    // Validate not empty
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Validate new password length
    if (strlen($newPassword) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
        exit;
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }

    // Check if old password is same as new password
    if ($oldPassword === $newPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New password must be different from current password']);
        exit;
    }

    // Load auth class
    require_once '../api/config.php';
    require_once '../api/auth.php';

    $auth = new Auth();
    $adminId = $_SESSION['admin_id'];

    // Change password
    $result = $auth->changePassword($adminId, $oldPassword, $newPassword);

    if ($result['success']) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }

} catch (Exception $e) {
    error_log('Change Password Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>


