<?php
/**
 * Test direct SQL execution
 */

header('Content-Type: application/json; charset=utf-8');

$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$conn->set_charset('utf8mb4');

// BEFORE
$before = $conn->query("SELECT id, title_en, event_video FROM exhibitions WHERE id = 2")->fetch_assoc();

// UPDATE using direct non-prepared query first
$update_result = $conn->query("UPDATE exhibitions SET event_video = NULL WHERE id = 2");

// AFTER
$after = $conn->query("SELECT id, title_en, event_video FROM exhibitions WHERE id = 2")->fetch_assoc();

echo json_encode([
    'before' => $before,
    'update_result' => $update_result ? 'SUCCESS' : 'FAILED',
    'after' => $after,
    'is_now_null' => is_null($after['event_video']),
    'affected_rows' => $conn->affected_rows
]);

$conn->close();
?>
