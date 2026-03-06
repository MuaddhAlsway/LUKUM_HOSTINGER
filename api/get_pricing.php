<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: public, max-age=3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(200);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $query = 'SELECT id, title, name_en, name_ar, price, price_unit, price_unit_ar, price_sec, vat_note, vat_note_ar, currency_image, content, description_en, description_ar, display_order, is_active FROM pricing WHERE is_active = 1 ORDER BY display_order ASC LIMIT ? OFFSET ?';
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $stmt->bind_param('ii', $limit, $offset);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $pricing = [];
    
    while ($row = $result->fetch_assoc()) {
        $item = [
            'id' => (int)$row['id'],
            'title' => $row['title'] ?? '',
            'name_en' => $row['name_en'] ?? $row['title'] ?? '',
            'name_ar' => $row['name_ar'] ?? '',
            'price' => (int)$row['price'] ?? 0,
            'price_unit' => $row['price_unit'] ?? 'SAR',
            'price_unit_ar' => $row['price_unit_ar'] ?? 'ر.س',
            'price_sec' => $row['price_sec'] ?? '',
            'vat_note' => $row['vat_note'] ?? '',
            'vat_note_ar' => $row['vat_note_ar'] ?? '',
            'currency_image' => $row['currency_image'] ?? null,
            'content' => $row['content'] ?? '',
            'description_en' => $row['description_en'] ?? '',
            'description_ar' => $row['description_ar'] ?? '',
            'display_order' => (int)$row['display_order'] ?? 0,
            'is_active' => (int)$row['is_active'] ?? 1
        ];
        $pricing[] = $item;
    }
    
    if (empty($pricing)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => [], 'message' => 'No pricing data found']);
        exit;
    }
    
    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $pricing, 'source' => 'database']);
    
} catch (Exception $e) {
    error_log('Pricing API Error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


