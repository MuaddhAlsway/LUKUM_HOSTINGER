<?php
/**
 * LAKUM Artspace - Submit Contact Message API
 * Saves contact form submissions to database and sends email via Gmail SMTP
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ─── Gmail SMTP Configuration ────────────────────────────────────────────────
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'info@lakumartspace.com');   // Gmail address
define('SMTP_PASS',     'dqjgzyselkakvjsc');          // App password (no spaces)
define('SMTP_FROM',     'info@lakumartspace.com');
define('SMTP_FROM_NAME','LAKUM Artspace');
define('ADMIN_EMAIL',   'info@lakumartspace.com');
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Send email via Gmail SMTP using raw socket (no library needed)
 */
function sendSmtpMail($to, $toName, $subject, $htmlBody, $replyTo = '') {
    $host     = SMTP_HOST;
    $port     = SMTP_PORT;
    $user     = SMTP_USER;
    $pass     = SMTP_PASS;
    $from     = SMTP_FROM;
    $fromName = SMTP_FROM_NAME;

    // Connect
    $socket = fsockopen('tcp://' . $host, $port, $errno, $errstr, 30);
    if (!$socket) {
        throw new Exception("SMTP connect failed: $errstr ($errno)");
    }

    $boundary = md5(uniqid(time()));

    $read = function() use ($socket) {
        $response = '';
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) === ' ') break;
        }
        return $response;
    };

    $send = function($cmd) use ($socket, $read) {
        fputs($socket, $cmd . "\r\n");
        return $read();
    };

    // SMTP handshake
    $read(); // 220 greeting
    $send('EHLO ' . gethostname());
    $send('STARTTLS');

    // Upgrade to TLS
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

    $send('EHLO ' . gethostname());

    // Auth
    $send('AUTH LOGIN');
    $send(base64_encode($user));
    $r = $send(base64_encode($pass));

    if (strpos($r, '235') === false) {
        fclose($socket);
        throw new Exception('SMTP authentication failed. Check your app password.');
    }

    // Envelope
    $send('MAIL FROM:<' . $from . '>');
    $send('RCPT TO:<' . $to . '>');
    $send('DATA');

    // Build message headers
    $headers  = 'Date: ' . date('r') . "\r\n";
    $headers .= 'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . ">\r\n";
    $headers .= 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $to . ">\r\n";
    if ($replyTo) {
        $headers .= 'Reply-To: ' . $replyTo . "\r\n";
    }
    $headers .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
    $headers .= "\r\n";
    $headers .= chunk_split(base64_encode($htmlBody));
    $headers .= "\r\n.";

    $r = $send($headers);
    $send('QUIT');
    fclose($socket);

    if (strpos($r, '250') === false) {
        throw new Exception('SMTP message send failed: ' . $r);
    }

    return true;
}

try {
    // ── Validate input ────────────────────────────────────────────────────────
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $name    = htmlspecialchars(trim($_POST['name']),    ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['email']),         FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars(trim($_POST['subject']), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // ── Save to database ──────────────────────────────────────────────────────
    $messageId = null;
    try {
        require_once __DIR__ . '/config.php';
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn->connect_error) {
            $conn->set_charset('utf8mb4');
            $result = $conn->query("SHOW TABLES LIKE 'contact_messages'");
            if ($result && $result->num_rows > 0) {
                $stmt = $conn->prepare(
                    'INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)'
                );
                if ($stmt) {
                    $status = 'new';
                    $stmt->bind_param('ssssss', $name, $email, $phone, $subject, $message, $status);
                    $stmt->execute();
                    $messageId = $stmt->insert_id;
                    $stmt->close();
                }
            }
            $conn->close();
        }
    } catch (Exception $dbEx) {
        // DB save failure is non-fatal; email will still be sent
        error_log('Contact DB error: ' . $dbEx->getMessage());
    }

    // ── Admin notification email ──────────────────────────────────────────────
    $adminSubject = 'New Contact Form Message: ' . $subject;
    $adminBody = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"></head>
    <body style="margin:0;padding:0;background:#f6f6eb;font-family:Arial,sans-serif;">
      <div style="max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <div style="background:#1a1a1a;padding:30px 24px;text-align:center;">
          <h2 style="color:#fff;margin:0;font-weight:300;font-size:22px;">New Contact Form Submission</h2>
        </div>
        <div style="padding:30px 24px;">
          <table style="width:100%;border-collapse:collapse;">
            <tr><td style="padding:10px 0;border-bottom:1px solid #eee;width:30%;color:#888;font-size:13px;">Name</td>
                <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;font-weight:600;">' . $name . '</td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Email</td>
                <td style="padding:10px 0;border-bottom:1px solid #eee;"><a href="mailto:' . $email . '" style="color:#1a1a1a;">' . $email . '</a></td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Phone</td>
                <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;">' . ($phone ?: 'Not provided') . '</td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Subject</td>
                <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;">' . $subject . '</td></tr>
            <tr><td style="padding:10px 0;vertical-align:top;color:#888;font-size:13px;">Message</td>
                <td style="padding:10px 0;color:#1a1a1a;line-height:1.7;">' . nl2br($message) . '</td></tr>
          </table>
          ' . ($messageId ? '<p style="margin-top:20px;color:#888;font-size:12px;">Message ID: #' . $messageId . '</p>' : '') . '
        </div>
        <div style="background:#f6f6eb;padding:16px 24px;text-align:center;">
          <p style="color:#888;font-size:12px;margin:0;">LAKUM Artspace &mdash; Contact Form</p>
        </div>
      </div>
    </body>
    </html>';

    sendSmtpMail(ADMIN_EMAIL, 'LAKUM Artspace', $adminSubject, $adminBody, $email);

    // ── Confirmation email to client ──────────────────────────────────────────
    $clientSubject = 'We received your message — LAKUM Artspace';
    $clientBody = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"></head>
    <body style="margin:0;padding:0;background:#f6f6eb;font-family:Arial,sans-serif;">
      <div style="max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <div style="background:#1a1a1a;padding:30px 24px;text-align:center;">
          <h2 style="color:#fff;margin:0;font-weight:300;font-size:22px;">Thank You for Reaching Out</h2>
        </div>
        <div style="padding:30px 24px;">
          <p style="color:#1a1a1a;font-size:15px;line-height:1.7;">Dear <strong>' . $name . '</strong>,</p>
          <p style="color:#555;font-size:15px;line-height:1.7;">Thank you for contacting LAKUM Artspace. We have received your message and will get back to you as soon as possible.</p>
          <div style="background:#f6f6eb;border-left:3px solid #1a1a1a;padding:16px 20px;margin:24px 0;border-radius:0 6px 6px 0;">
            <p style="margin:0;color:#888;font-size:13px;">Your subject: <strong style="color:#1a1a1a;">' . $subject . '</strong></p>
          </div>
          <p style="color:#555;font-size:15px;line-height:1.7;">We appreciate your interest in LAKUM Artspace and look forward to connecting with you.</p>
          <p style="color:#1a1a1a;font-size:15px;margin-top:30px;">Best regards,<br><strong>LAKUM Artspace Team</strong></p>
        </div>
        <div style="background:#f6f6eb;padding:16px 24px;text-align:center;">
          <p style="color:#888;font-size:12px;margin:0;">
            <a href="mailto:info@lakumartspace.com" style="color:#888;">info@lakumartspace.com</a>
          </p>
        </div>
      </div>
    </body>
    </html>';

    sendSmtpMail($email, $name, $clientSubject, $clientBody);

    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'id'      => $messageId
    ]);

} catch (Exception $e) {
    error_log('Contact Submission Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again or email us directly at info@lakumartspace.com'
    ]);
}
?>
