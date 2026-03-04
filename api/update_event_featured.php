<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Load DB config
require_once __DIR__ . '/../config.php';

// Ensure connection exists
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Turn on error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Helper function
function jsonResponse($success, $message = '', $data = null) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'], $input['is_featured'])) {
        http_response_code(400);
        jsonResponse(false, 'Missing parameters: id and is_featured');
    }

    $id = (int)$input['id'];
    $is_featured = (int)$input['is_featured'];

    // Check event exists
    $checkStmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
    if (!$checkStmt) jsonResponse(false, 'Prepare check failed: ' . $conn->error);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkRes = $checkStmt->get_result();
    if ($checkRes->num_rows === 0) {
        jsonResponse(false, 'Event not found');
    }
    $checkStmt->close();

    // Start transaction
    $conn->begin_transaction();

    // Unmark other featured events if marking this one
    if ($is_featured === 1) {
        $stmt = $conn->prepare("UPDATE events SET is_featured = 0 WHERE id != ?");
        if (!$stmt) throw new Exception('Prepare unmark failed: ' . $conn->error);
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) throw new Exception('Execute unmark failed: ' . $stmt->error);
        $stmt->close();
    }

    // Update this event
    $stmt = $conn->prepare("UPDATE events SET is_featured = ? WHERE id = ?");
    if (!$stmt) throw new Exception('Prepare update failed: ' . $conn->error);
    $stmt->bind_param('ii', $is_featured, $id);
    if (!$stmt->execute()) throw new Exception('Execute update failed: ' . $stmt->error);
    $stmt->close();

    $conn->commit();

    jsonResponse(true, 'Featured status updated successfully');

} catch (Exception $e) {
    if ($conn && $conn->in_transaction) $conn->rollback();
    http_response_code(500);
    error_log('update_event_featured.php error: ' . $e->getMessage());
    jsonResponse(false, 'Server error: ' . $e->getMessage());
}
?>

