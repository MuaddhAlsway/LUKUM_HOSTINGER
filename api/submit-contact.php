<?php
/**
 * LAKUM Artspace - Submit Contact Message API
 * Saves contact form submissions to database and sends email
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

try {
    // Get form data
    $input = $_POST;
    
    // Validate required fields
    if (empty($input['name']) || empty($input['email']) || empty($input['subject']) || empty($input['message'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    $name = htmlspecialchars($input['name'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($input['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($input['subject'], ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($input['message'], ENT_QUOTES, 'UTF-8');
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'contact_messages'");
    if ($result->num_rows === 0) {
        throw new Exception('Contact messages table not found. Please run create-contact-table.php first.');
    }
    
    // Insert message into database
    $query = 'INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $status = 'new';
    $stmt->bind_param('ssssss', $name, $email, $phone, $subject, $message, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $messageId = $stmt->insert_id;
    $stmt->close();
    
    // Send email notification to admin
    $adminEmail = 'info@lakumartspace.com';
    $emailSubject = 'New Contact Form Submission: ' . $subject;
    
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: white; padding: 20px; text-align: center; }
            .content { background: #f6f6eb; padding: 20px; }
            .field { margin: 15px 0; }
            .label { font-weight: bold; color: #1a1a1a; }
            .value { color: #666; margin-top: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>" . $name . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'><a href='mailto:" . $email . "'>" . $email . "</a></div>
                </div>
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>" . ($phone ? $phone : 'Not provided') . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Subject:</div>
                    <div class='value'>" . $subject . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br($message) . "</div>
                </div>
                <div class='field' style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    <p><strong>Message ID:</strong> #" . $messageId . "</p>
                    <p><a href='http://localhost/LUKUM(main)/admin/messages.html?id=" . $messageId . "' style='background: #1a1a1a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;'>View & Respond</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email using mail() function
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    
    mail($adminEmail, $emailSubject, $emailBody, $headers);
    
    // Send confirmation email to user
    $userSubject = 'We received your message - LAKUM Artspace';
    $userBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: white; padding: 20px; text-align: center; }
            .content { background: #f6f6eb; padding: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Thank You for Contacting LAKUM Artspace</h2>
            </div>
            <div class='content'>
                <p>Dear " . $name . ",</p>
                <p>Thank you for reaching out to us. We have received your message and will get back to you as soon as possible.</p>
                <p><strong>Your Message ID:</strong> #" . $messageId . "</p>
                <p>We appreciate your interest in LAKUM Artspace and look forward to connecting with you.</p>
                <p>Best regards,<br>LAKUM Artspace Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $userHeaders = "MIME-Version: 1.0" . "\r\n";
    $userHeaders .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $userHeaders .= "From: info@lakumartspace.com" . "\r\n";
    
    mail($email, $userSubject, $userBody, $userHeaders);
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Message submitted successfully',
        'id' => $messageId
    ]);
    
} catch (Exception $e) {
    error_log('Contact Submission Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

