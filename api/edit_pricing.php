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
    
    if (empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Pricing ID is required']);
        exit;
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        error_log('Database connection failed in edit_pricing.php');
        throw new Exception('Database connection failed');
    }
    
    $conn = $db->getConnection();
    
    $conn->set_charset('utf8mb4');
    
    $id = (int)$input['id'];
    $name_en = $input['name_en'] ?? '';
    $name_ar = $input['name_ar'] ?? '';
    $description_en = $input['description_en'] ?? '';
    $description_ar = $input['description_ar'] ?? '';
    $duration_en = $input['duration_en'] ?? '';
    $duration_ar = $input['duration_ar'] ?? '';
    $features_en = $input['features_en'] ?? '';
    $features_ar = $input['features_ar'] ?? '';
    $price = (int)($input['price'] ?? 0);
    $price_unit = $input['price_unit'] ?? 'SAR';
    $price_sec = $input['price_sec'] ?? '';
    $vat_note = $input['vat_note'] ?? '';
    $display_order = (int)($input['display_order'] ?? 0);
    $is_active = ($input['is_active'] === '1' || $input['is_active'] === true) ? 1 : 0;
    
    $query = 'UPDATE pricing SET 
        name_en = ?, 
        name_ar = ?, 
        description_en = ?, 
        description_ar = ?, 
        duration_en = ?, 
        duration_ar = ?, 
        features_en = ?, 
        features_ar = ?, 
        price = ?, 
        price_unit = ?, 
        price_sec = ?, 
        vat_note = ?, 
        display_order = ?, 
        is_active = ? 
    WHERE id = ?';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssssssssisssiii', 
        $name_en, 
        $name_ar, 
        $description_en, 
        $description_ar, 
        $duration_en, 
        $duration_ar, 
        $features_en, 
        $features_ar, 
        $price, 
        $price_unit, 
        $price_sec, 
        $vat_note, 
        $display_order, 
        $is_active, 
        $id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing updated successfully',
        'id' => $id
    ]);
    
} catch (Exception $e) {
    error_log('Edit Pricing Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

