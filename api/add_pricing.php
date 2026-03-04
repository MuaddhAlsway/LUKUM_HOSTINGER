<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['name_en']) || empty($input['name_ar'])) {
        echo json_encode(['success' => false, 'message' => 'Price name in both English and Arabic is required']);
        exit;
    }
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    $title = $input['name_en'] ?? '';
    $name_en = $input['name_en'] ?? '';
    $name_ar = $input['name_ar'] ?? '';
    $description_en = $input['description_en'] ?? '';
    $description_ar = $input['description_ar'] ?? '';
    $price = (int)($input['price'] ?? 0);
    $price_unit = $input['price_unit'] ?? 'SAR';
    $price_unit_ar = $input['price_unit_ar'] ?? 'ر.س';
    $price_sec = $input['price_sec'] ?? '';
    $vat_note = $input['vat_note'] ?? '';
    $vat_note_ar = $input['vat_note_ar'] ?? '';
    $display_order = (int)($input['display_order'] ?? 0);
    $is_active = ($input['is_active'] === '1' || $input['is_active'] === true) ? 1 : 0;
    
    $query = 'INSERT INTO pricing (
        title,
        name_en, 
        name_ar, 
        description_en, 
        description_ar, 
        price, 
        price_unit, 
        price_unit_ar, 
        price_sec, 
        vat_note, 
        vat_note_ar, 
        display_order, 
        is_active
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssssssissssii', 
        $title,
        $name_en, 
        $name_ar, 
        $description_en, 
        $description_ar, 
        $price, 
        $price_unit, 
        $price_unit_ar, 
        $price_sec, 
        $vat_note, 
        $vat_note_ar, 
        $display_order, 
        $is_active
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $new_id = $conn->insert_id;
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing created successfully',
        'id' => $new_id
    ]);
    
} catch (Exception $e) {
    error_log('Add Pricing Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>



