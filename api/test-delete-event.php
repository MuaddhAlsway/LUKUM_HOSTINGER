<?php
require_once __DIR__ . '/config.php';

/**
 * Test Delete Event API
 * Direct test without JSON parsing issues
 */

header('Content-Type: application/json');

try {
    // Test 1: Check if we can get the raw input
    echo "Test 1: Raw Input\n";
    $rawInput = file_get_contents('php://input');
    echo "Raw input: " . $rawInput . "\n";
    echo "Raw input length: " . strlen($rawInput) . "\n\n";
    
    // Test 2: Try to decode JSON
    echo "Test 2: JSON Decode\n";
    $data = json_decode($rawInput, true);
    echo "Decoded: " . json_encode($data) . "\n";
    echo "JSON error: " . json_last_error_msg() . "\n\n";
    
    // Test 3: Check $_POST
    echo "Test 3: POST Data\n";
    echo "POST: " . json_encode($_POST) . "\n\n";
    
    // Test 4: Check headers
    echo "Test 4: Headers\n";
    echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";
    echo "Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'Not set') . "\n\n";
    
    // Test 5: Try manual test with ID 1
    echo "Test 5: Manual Test\n";
    $testData = ['id' => 1];
    echo "Test data: " . json_encode($testData) . "\n";
    echo "Test ID: " . $testData['id'] . "\n";
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Test completed',
        'raw_input' => $rawInput,
        'decoded_data' => $data,
        'post_data' => $_POST
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
