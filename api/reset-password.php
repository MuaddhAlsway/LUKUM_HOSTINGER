<?php
/**
 * LAKUM Artspace - Reset Password API
 * Validates token and updates the admin password
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input       = json_decode(file_get_contents('php://input'), true) ?? [];
    $token       = trim($input['token']    ?? '');
    $newPassword = trim($input['password'] ?? '');
    $confirm     = trim($input['confirm']  ?? '');

    // Validate inputs
    if (empty($token))       { echo json_encode(['success' => false, 'message' => 'Reset token is missing.']);       exit; }
    if (empty($newPassword)) { echo json_encode(['success' => false, 'message' => 'Password is required.']);          exit; }
    if (strlen($newPassword) < 6) { echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']); exit; }
    if ($newPassword !== $confirm) { echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);  exit; }

    require_once __DIR__ . '/config.php';
    $db = Database::getInstance()->getConnection();

    // Look up the token
    $stmt = $db->prepare("SELECT id, email, expires_at, used FROM password_resets WHERE token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired reset link. Please request a new one.']);
        exit;
    }

    $row = $result->fetch_assoc();

    if ($row['used']) {
        echo json_encode(['success' => false, 'message' => 'This reset link has already been used. Please request a new one.']);
        exit;
    }

    if (strtotime($row['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'This reset link has expired. Please request a new one.']);
        exit;
    }

    $email          = $row['email'];

    // muaddhalsway@gmail.com is an authorized owner email but not the DB admin account.
    // Always apply the password change to the actual admin account in the DB.
    $adminDbEmail   = 'info@lakumartspace.com';
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update admin password using the DB admin email
    $upd = $db->prepare("UPDATE admins SET password = ? WHERE email = ?");
    $upd->bind_param('ss', $hashedPassword, $adminDbEmail);
    $upd->execute();

    if ($upd->affected_rows === 0) {
        $upd->close();
        echo json_encode(['success' => false, 'message' => 'Admin account not found.']);
        exit;
    }
    $upd->close();

    // Mark token as used
    $mark = $db->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
    $mark->bind_param('s', $token);
    $mark->execute();
    $mark->close();

    echo json_encode(['success' => true, 'message' => 'Password updated successfully. You can now log in.']);

} catch (Exception $e) {
    error_log('Reset Password Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
