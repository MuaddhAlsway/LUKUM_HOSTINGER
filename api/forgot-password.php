<?php
/**
 * LAKUM Artspace - Forgot Password API
 * Generates a reset token and sends it to info@lakumartspace.com
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

// ─── SMTP config (same as contact form) ──────────────────────────────────────
define('FP_SMTP_HOST', 'smtp.gmail.com');
define('FP_SMTP_PORT', 587);
define('FP_SMTP_USER', 'info@lakumartspace.com');
define('FP_SMTP_PASS', 'dqjgzyselkakvjsc');
define('ADMIN_EMAIL',  'info@lakumartspace.com');
// ─────────────────────────────────────────────────────────────────────────────

function sendResetEmail($resetLink) {
    $socket = fsockopen('tcp://' . FP_SMTP_HOST, FP_SMTP_PORT, $errno, $errstr, 30);
    if (!$socket) throw new Exception("SMTP connect failed: $errstr ($errno)");

    $read = function() use ($socket) {
        $r = '';
        while ($s = fgets($socket, 515)) { $r .= $s; if ($s[3] === ' ') break; }
        return $r;
    };
    $send = function($cmd) use ($socket, $read) {
        fputs($socket, $cmd . "\r\n");
        return $read();
    };

    $read(); // 220 greeting
    $send('EHLO ' . gethostname());
    $send('STARTTLS');
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    $send('EHLO ' . gethostname());
    $send('AUTH LOGIN');
    $send(base64_encode(FP_SMTP_USER));
    $r = $send(base64_encode(FP_SMTP_PASS));
    if (strpos($r, '235') === false) { fclose($socket); throw new Exception('SMTP auth failed'); }

    $send('MAIL FROM:<' . FP_SMTP_USER . '>');
    $send('RCPT TO:<' . ADMIN_EMAIL . '>');
    $send('DATA');

    $subject  = 'Reset Your LAKUM Artspace Admin Password';
    $htmlBody = '
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
                      border-radius:4px;font-size:15px;font-weight:400;display:inline-block;
                      letter-spacing:0.5px;">
              Reset Password
            </a>
          </div>
          <p style="color:#888;font-size:13px;line-height:1.6;">
            Or copy this link into your browser:<br>
            <a href="' . $resetLink . '" style="color:#1a1a1a;word-break:break-all;">' . $resetLink . '</a>
          </p>
          <p style="color:#bbb;font-size:12px;margin-top:28px;border-top:1px solid #eee;padding-top:18px;">
            If you did not request this, please ignore this email. Your password will not change.
          </p>
        </div>
      </div>
    </body></html>';

    $msg  = 'Date: ' . date('r') . "\r\n";
    $msg .= 'From: LAKUM Artspace <' . FP_SMTP_USER . ">\r\n";
    $msg .= 'To: Admin <' . ADMIN_EMAIL . ">\r\n";
    $msg .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
    $msg .= 'MIME-Version: 1.0' . "\r\n";
    $msg .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $msg .= 'Content-Transfer-Encoding: base64' . "\r\n";
    $msg .= "\r\n" . chunk_split(base64_encode($htmlBody)) . "\r\n.";

    $r = $send($msg);
    $send('QUIT');
    fclose($socket);

    if (strpos($r, '250') === false) throw new Exception('SMTP send failed: ' . $r);
    return true;
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $email = trim($input['email'] ?? $_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Only the admin email is allowed
    if (strtolower($email) !== strtolower(ADMIN_EMAIL)) {
        // Security: return same response to avoid revealing valid emails
        echo json_encode(['success' => true, 'message' => 'If this email is registered, you will receive a reset link shortly.']);
        exit;
    }

    // Connect to DB
    require_once __DIR__ . '/config.php';
    $db = Database::getInstance()->getConnection();

    // Create password_resets table if it doesn't exist
    $db->query("CREATE TABLE IF NOT EXISTS password_resets (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        email       VARCHAR(255) NOT NULL,
        token       VARCHAR(64)  NOT NULL UNIQUE,
        expires_at  DATETIME     NOT NULL,
        used        TINYINT(1)   DEFAULT 0,
        created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Delete any existing unused tokens for this email
    $del = $db->prepare("DELETE FROM password_resets WHERE email = ?");
    $del->bind_param('s', $email);
    $del->execute();
    $del->close();

    // Generate a secure token
    $token     = bin2hex(random_bytes(32)); // 64-char hex string
    $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

    $ins = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $ins->bind_param('sss', $email, $token, $expiresAt);
    $ins->execute();
    $ins->close();

    // Build reset link — points to the new reset-password page
    $siteUrl   = rtrim(SITE_URL, '/');
    $resetLink = $siteUrl . '/admin/reset-password.html?token=' . $token;

    sendResetEmail($resetLink);

    echo json_encode(['success' => true, 'message' => 'If this email is registered, you will receive a reset link shortly.']);

} catch (Exception $e) {
    error_log('Forgot Password Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send reset email. Please try again.']);
}
?>
