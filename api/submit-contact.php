<?php
/**
 * LAKUM Artspace - Submit Contact Message API
 * Saves contact form submissions to database and sends email via PHPMailer + Gmail SMTP
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

// ─── Load PHPMailer ───────────────────────────────────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

// ─── Gmail SMTP Configuration ─────────────────────────────────────────────────
define('GMAIL_USER',  'info@lakumartspace.com');
define('GMAIL_PASS',  'dqjgzyselkakvjsc');        // Gmail app password (no spaces)
define('ADMIN_TO',    'info@lakumartspace.com');
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Send an HTML email via PHPMailer + Gmail SMTP
 */
function sendMail($toEmail, $toName, $subject, $htmlBody, $replyToEmail = '') {
    $mail = new PHPMailer(true); // true = throw exceptions

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = GMAIL_USER;
    $mail->Password   = GMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Sender
    $mail->setFrom(GMAIL_USER, 'LAKUM Artspace');

    // Reply-to (so admin can reply directly to visitor)
    if (!empty($replyToEmail)) {
        $mail->addReplyTo($replyToEmail);
    }

    // Recipient
    $mail->addAddress($toEmail, $toName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody); // Plain text fallback

    $mail->send();
    return true;
}

try {
    // ── Validate input ────────────────────────────────────────────────────────
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $name    = htmlspecialchars(trim($_POST['name']),        ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['email']),             FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars(trim($_POST['subject']),     ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message']),     ENT_QUOTES, 'UTF-8');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // ── Save to database (non-fatal) ──────────────────────────────────────────
    $messageId = null;
    try {
        require_once __DIR__ . '/config.php';
        $db = Database::getInstance()->getConnection();
        if ($db) {
            // Create table if it doesn't exist
            $db->query("CREATE TABLE IF NOT EXISTS contact_messages (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(255) NOT NULL,
                email      VARCHAR(255) NOT NULL,
                phone      VARCHAR(50)  DEFAULT '',
                subject    VARCHAR(255) NOT NULL,
                message    TEXT         NOT NULL,
                status     VARCHAR(20)  DEFAULT 'new',
                created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $stmt = $db->prepare(
                'INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)'
            );
            if ($stmt) {
                $status = 'new';
                $stmt->bind_param('ssssss', $name, $email, $phone, $subject, $message, $status);
                $stmt->execute();
                $messageId = $db->insert_id;
                $stmt->close();
            }
        }
    } catch (Exception $dbEx) {
        error_log('Contact DB error: ' . $dbEx->getMessage());
    }

    // ── Admin notification email ──────────────────────────────────────────────
    $adminSubject = 'New Contact Form Message: ' . $subject;
    $adminBody    = '
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
            <tr>
              <td style="padding:10px 0;border-bottom:1px solid #eee;width:30%;color:#888;font-size:13px;">Name</td>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;font-weight:600;">' . $name . '</td>
            </tr>
            <tr>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Email</td>
              <td style="padding:10px 0;border-bottom:1px solid #eee;">
                <a href="mailto:' . $email . '" style="color:#1a1a1a;">' . $email . '</a>
              </td>
            </tr>
            <tr>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Phone</td>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;">' . ($phone ?: 'Not provided') . '</td>
            </tr>
            <tr>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;">Subject</td>
              <td style="padding:10px 0;border-bottom:1px solid #eee;color:#1a1a1a;">' . $subject . '</td>
            </tr>
            <tr>
              <td style="padding:10px 0;vertical-align:top;color:#888;font-size:13px;">Message</td>
              <td style="padding:10px 0;color:#1a1a1a;line-height:1.7;">' . nl2br($message) . '</td>
            </tr>
          </table>
          ' . ($messageId ? '<p style="margin-top:20px;color:#888;font-size:12px;">Message ID: #' . $messageId . '</p>' : '') . '
        </div>
        <div style="background:#f6f6eb;padding:16px 24px;text-align:center;">
          <p style="color:#888;font-size:12px;margin:0;">LAKUM Artspace &mdash; Contact Form</p>
        </div>
      </div>
    </body>
    </html>';

    // Send to admin — reply-to is the visitor's email so admin can reply directly
    sendMail(ADMIN_TO, 'LAKUM Artspace', $adminSubject, $adminBody, $email);

    // ── Confirmation email to visitor ─────────────────────────────────────────
    $clientSubject = 'We received your message — LAKUM Artspace';
    $clientBody    = '
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

    sendMail($email, $name, $clientSubject, $clientBody);

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
        'message' => 'Failed to send message. Please try again or email us directly at info@lakumartspace.com',
        'debug'   => $e->getMessage() // remove this line after confirming it works
    ]);
}
?>
