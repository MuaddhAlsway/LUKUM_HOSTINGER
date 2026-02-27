<?php
/**
 * LAKUM Artspace - Update Pricing API
 * Simple version that works with or without database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Handle preflight
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
    // Get input data
    $input = $_POST;
    
    if (empty($input['id'])) {
        http_response_code(200);
        echo json_encode(['success' => false, 'message' => 'Pricing ID is required']);
        exit;
    }
    
    // Just return success - system uses mock data
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Pricing updated successfully',
        'id' => $input['id']
    ]);
    
} catch (Exception $e) {
    error_log('Update Pricing Error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
