<?php
/**
 * Debug JSON Input
 * Logs exactly what is being received
 */

session_start();
header('Content-Type: application/json');

// Get raw input
$rawInput = file_get_contents('php://input');

// Log everything
$debug = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'not set',
    'raw_input_length' => strlen($rawInput),
    'raw_input' => $rawInput,
    'raw_input_hex' => bin2hex($rawInput),
    'session_id' => session_id(),
    'session_admin_id' => $_SESSION['admin_id'] ?? 'not set'
];

// Try to decode
$decoded = json_decode($rawInput, true);
$debug['json_decode_result'] = $decoded;
$debug['json_error'] = json_last_error_msg();

// Log to file
file_put_contents('../logs/json_debug.log', date('Y-m-d H:i:s') . ' - ' . json_encode($debug) . "\n", FILE_APPEND);

echo json_encode($debug, JSON_PRETTY_PRINT);
?>


