<?php
/**
 * Direct SQL update to remove video from exhibition
 */

header('Content-Type: application/json; charset=utf-8');

$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

$conn->set_charset('utf8mb4');

// Get exhibition ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 76;

// BEFORE: Check current value
$before_sql = "SELECT id, title_en, event_video FROM exhibitions WHERE id = ?";
$before_stmt = $conn->prepare($before_sql);
$before_stmt->bind_param('i', $id);
$before_stmt->execute();
$before_result = $before_stmt->get_result();
$before_row = $before_result->fetch_assoc();

echo json_encode([
    'id' => $id,
    'before' => $before_row,
    'action' => 'Setting event_video to NULL',
    'updating' => true
]);

// UPDATE: Set to NULL
$update_sql = "UPDATE exhibitions SET event_video = NULL WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('i', $id);
$update_success = $update_stmt->execute();

// AFTER: Check new value
$after_sql = "SELECT id, title_en, event_video FROM exhibitions WHERE id = ?";
$after_stmt = $conn->prepare($after_sql);
$after_stmt->bind_param('i', $id);
$after_stmt->execute();
$after_result = $after_stmt->get_result();
$after_row = $after_result->fetch_assoc();

echo json_encode([
    'id' => $id,
    'before' => $before_row,
    'after' => $after_row,
    'update_success' => $update_success,
    'affected_rows' => $update_stmt->affected_rows
]);

$conn->close();
?>
