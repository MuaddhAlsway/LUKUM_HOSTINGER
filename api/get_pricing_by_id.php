<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

try {
    $id = (int)($_GET['id'] ?? 0);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Pricing ID is required']);
        exit;
    }

    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $query = 'SELECT id, title, price, price_unit, price_sec, vat_note, content, display_order, is_active, name_en, name_ar, description_en, description_ar, duration_en, duration_ar, features_en, features_ar FROM pricing WHERE id = ?';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pricing not found']);
        exit;
    }

    $pricing = [
        'id' => (int)$row['id'],
        'title' => $row['title'] ?? '',
        'name_en' => $row['name_en'] ?? $row['title'] ?? '',
        'name_ar' => $row['name_ar'] ?? '',
        'content' => $row['content'] ?? '',
        'description_en' => $row['description_en'] ?? '',
        'description_ar' => $row['description_ar'] ?? '',
        'duration_en' => $row['duration_en'] ?? '',
        'duration_ar' => $row['duration_ar'] ?? '',
        'features_en' => $row['features_en'] ?? '',
        'features_ar' => $row['features_ar'] ?? '',
        'price' => (int)($row['price'] ?? 0),
        'price_unit' => $row['price_unit'] ?? 'SAR',
        'price_secondary' => $row['price_sec'] ?? '',
        'vat_note' => $row['vat_note'] ?? '',
        'display_order' => (int)($row['display_order'] ?? 0),
        'is_active' => (int)($row['is_active'] ?? 1)
    ];

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $pricing]);
    
} catch (Exception $e) {
    error_log('Get Pricing Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


