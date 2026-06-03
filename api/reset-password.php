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
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Try by id=1 first (most reliable — doesn't depend on email value in DB)
    $upd = $db->prepare("UPDATE admins SET password = ? WHERE id = 1");
    $upd->bind_param('s', $hashedPassword);
    $upd->execute();

    if ($upd->affected_rows === 0) {
        $upd->close();
        // Fallback: update the first admin row regardless of id or email
        $upd2 = $db->prepare("UPDATE admins SET password = ? LIMIT 1");
        $upd2->bind_param('s', $hashedPassword);
        $upd2->execute();

        if ($upd2->affected_rows === 0) {
            $upd2->close();
            echo json_encode(['success' => false, 'message' => 'Admin account not found. Please contact support.']);
            exit;
        }
        $upd2->close();
    } else {
        $upd->close();
    }

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
