<?php
/**
 * LAKUM Artspace - Forgot Password API
 * Generates a reset token and sends it via PHPMailer + Gmail SMTP
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

// ─── PHPMailer ────────────────────────────────────────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

define('FP_GMAIL_USER', 'info@lakumartspace.com');
define('FP_GMAIL_PASS', 'dqjgzyselkakvjsc');
// ─────────────────────────────────────────────────────────────────────────────

function sendResetEmail($toEmail, $resetLink) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = FP_GMAIL_USER;
    $mail->Password   = FP_GMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(FP_GMAIL_USER, 'LAKUM Artspace');
    $mail->addAddress($toEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Reset Your LAKUM Artspace Admin Password';
    $mail->Body    = '
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"></head>
    <body style="margin:0;padding:0;background:#f6f6eb;font-family:Arial,sans-serif;">
      <div style="max-width:520px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <div style="background:#1a1a1a;padding:30px 24px;text-align:center;">
          <h2 style="color:#fff;margin:0;font-weight:300;font-size:22px;letter-spacing:1px;">LAKUM Artspace</h2>
          <p style="color:#aaa;margin:8px 0 0;font-size:13px;">Admin Password Reset</p>
        </div>
        <div style="padding:36px 32px;">
          <p style="color:#1a1a1a;font-size:15px;line-height:1.7;margin-bottom:20px;">
            You requested a password reset for the LAKUM Artspace admin panel.<br>
            Click the button below to set a new password. This link is valid for <strong>1 hour</strong>.
          </p>
          <div style="text-align:center;margin:32px 0;">
            <a href="' . $resetLink . '"
               style="background:#1a1a1a;color:#fff;padding:14px 32px;text-decoration:none;
                      border-radius:4px;font-size:15px;display:inline-block;letter-spacing:0.5px;">
              Reset Password
            </a>
          </div>
          <p style="color:#888;font-size:13px;line-height:1.6;">
            Or copy this link:<br>
            <a href="' . $resetLink . '" style="color:#1a1a1a;word-break:break-all;">' . $resetLink . '</a>
          </p>
          <p style="color:#bbb;font-size:12px;margin-top:28px;border-top:1px solid #eee;padding-top:18px;">
            If you did not request this, please ignore this email.
          </p>
        </div>
      </div>
    </body></html>';
    $mail->AltBody = 'Reset your LAKUM Artspace admin password by visiting: ' . $resetLink;

    $mail->send();
    return true;
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $email = strtolower(trim($input['email'] ?? $_POST['email'] ?? ''));

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Only authorized emails allowed
    $allowedEmails = ['info@lakumartspace.com', 'muaddhalsway@gmail.com'];
    if (!in_array($email, $allowedEmails)) {
        echo json_encode(['success' => true, 'message' => 'If this email is registered, you will receive a reset link shortly.']);
        exit;
    }

    require_once __DIR__ . '/config.php';
    $db = Database::getInstance()->getConnection();

    // Create password_resets table if needed
    $db->query("CREATE TABLE IF NOT EXISTS password_resets (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        email      VARCHAR(255) NOT NULL,
        token      VARCHAR(64)  NOT NULL UNIQUE,
        expires_at DATETIME     NOT NULL,
        used       TINYINT(1)   DEFAULT 0,
        created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Delete old tokens for this email
    $del = $db->prepare("DELETE FROM password_resets WHERE email = ?");
    $del->bind_param('s', $email);
    $del->execute();
    $del->close();

    // Generate token
    $token     = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    $ins = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $ins->bind_param('sss', $email, $token, $expiresAt);
    $ins->execute();
    $ins->close();

    $siteUrl   = rtrim(SITE_URL, '/');
    $resetLink = $siteUrl . '/admin/reset-password.html?token=' . $token;

    sendResetEmail($email, $resetLink);

    echo json_encode(['success' => true, 'message' => 'Reset link sent! Check your inbox.']);

} catch (Exception $e) {
    error_log('Forgot Password Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send reset email. Please try again.',
        'debug'   => $e->getMessage()
    ]);
}
?>
