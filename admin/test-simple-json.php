<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Get raw input
$rawInput = file_get_contents('php://input');

// Return exactly what we received
echo json_encode([
    'received' => true,
    'raw_input' => $rawInput,
    'raw_input_length' => strlen($rawInput),
    'raw_input_bytes' => array_values(unpack('C*', $rawInput)),
    'json_decode_result' => json_decode($rawInput, true),
    'json_error' => json_last_error(),
    'json_error_msg' => json_last_error_msg(),
    'method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'session_admin_id' => $_SESSION['admin_id'] ?? 'not set'
], JSON_PRETTY_PRINT);
?>

