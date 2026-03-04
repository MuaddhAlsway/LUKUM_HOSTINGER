<?php
/**
 * LAKUM Artspace - Edit Pricing API (Working Version)
 * Updates pricing in database or returns success for mock mode
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
    // Get input data from FormData
    $input = $_POST;
    
    if (empty($input['id'])) {
        http_response_code(200);
        echo json_encode(['success' => false, 'message' => 'Pricing ID is required']);
        exit;
    }
    
    $id = (int)$input['id'];
    $title = $input['title'] ?? '';
    $price = (int)($input['price'] ?? 0);
    $price_unit = $input['price_unit'] ?? 'SAR';
    $price_sec = $input['price_sec'] ?? '';
    $vat_note = $input['vat_note'] ?? '';
    $content = $input['content'] ?? '';
    $display_order = (int)($input['display_order'] ?? 0);
    $is_active = ($input['is_active'] === '1' || $input['is_active'] === true) ? 1 : 0;
    
    // Try to connect to database
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        // Database not available - return error
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if pricing table exists
    $result = $conn->query("SHOW TABLES LIKE 'pricing'");
    if ($result->num_rows === 0) {
        // Table doesn't exist - return error
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'Pricing table not found in database'
        ]);
        $conn->close();
        exit;
    }
    
    // Update pricing in database
    $query = 'UPDATE pricing SET title = ?, price = ?, price_unit = ?, price_sec = ?, vat_note = ?, content = ?, display_order = ?, is_active = ? WHERE id = ?';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    // Bind parameters: 9 parameters total
    // s = string, i = integer
    $stmt->bind_param('sissssiii', $title, $price, $price_unit, $price_sec, $vat_note, $content, $display_order, $is_active, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Check if any rows were affected
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    if ($affected_rows === 0) {
        $conn->close();
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'No pricing item found with ID: ' . $id
        ]);
        exit;
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pricing updated successfully',
        'id' => $id,
        'title' => $title,
        'price' => $price,
        'affected_rows' => $affected_rows
    ]);
    
} catch (Exception $e) {
    error_log('Edit Pricing Error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


