<?php
/**
 * LAKUM Artspace - Edit Pricing API (Mock Version)
 * Returns success without database update
 * Used when database is unavailable
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
    // Handle both JSON and FormData
    $input = [];
    
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        // JSON input
        $rawInput = file_get_contents('php://input');
        if (empty($rawInput)) {
            throw new Exception('Empty request body');
        }
        $input = json_decode($rawInput, true);
        if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }
    } else {
        // FormData input
        $input = $_POST;
    }
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pricing ID is required']);
        exit;
    }
    
    // Just return success - data is stored in mock/hardcoded format
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Pricing updated successfully',
        'note' => 'Using mock data - changes are not persisted'
    ]);
    
} catch (Exception $e) {
    error_log('Edit Pricing Error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
