<?php
/**
 * Respond to contact message
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id']) || empty($input['response'])) {
        echo json_encode(['success' => false, 'message' => 'Message ID and response are required']);
        exit;
    }
    
    $id = (int)$input['id'];
    $response = htmlspecialchars($input['response'], ENT_QUOTES, 'UTF-8');
    
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    $conn->set_charset('utf8mb4');
    
    // Get message details
    $query = 'SELECT email, name FROM contact_messages WHERE id = ?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    $stmt->close();
    
    if (!$message) {
        throw new Exception('Message not found');
    }
    
    // Update message with response
    $query = 'UPDATE contact_messages SET response_message = ?, status = ?, response_date = NOW() WHERE id = ?';
    $stmt = $conn->prepare($query);
    
    $status = 'responded';
    $stmt->bind_param('ssi', $response, $status, $id);
    $stmt->execute();
    $stmt->close();
    
    // Send response email to user
    $userEmail = $message['email'];
    $userName = $message['name'];
    $subject = 'Re: Your LAKUM Artspace Inquiry';
    
    $emailBody = "
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
                <h2>Response from LAKUM Artspace</h2>
            </div>
            <div class='content'>
                <p>Dear " . $userName . ",</p>
                <p>Thank you for your inquiry. Here is our response:</p>
                <div style='background: white; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                    " . nl2br($response) . "
                </div>
                <p>If you have any further questions, please don't hesitate to contact us.</p>
                <p>Best regards,<br>LAKUM Artspace Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: info@lakumartspace.com" . "\r\n";
    
    mail($userEmail, $subject, $emailBody, $headers);
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Response sent successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Respond Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

