<?php
/**
 * LAKUM Artspace - Create Pricing API
 * Creates a new pricing option in the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required = ['name', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field]) && $data[$field] !== 0) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $db->prepare('INSERT INTO pricing (name, description, price, currency, duration, features, is_popular, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $name = $data['name'];
    $description = $data['description'] ?? '';
    $price = (float)$data['price'];
    $currency = $data['currency'] ?? 'SAR';
    $duration = $data['duration'] ?? 'per month';
    $features = $data['features'] ?? '';
    $is_popular = $data['is_popular'] ?? 0;
    $is_active = $data['is_active'] ?? 1;
    
    $stmt->bind_param('ssdsssii', $name, $description, $price, $currency, $duration, $features, $is_popular, $is_active);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $pricing_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing created successfully',
        'data' => ['id' => $pricing_id]
    ]);
    
} catch (Exception $e) {
    error_log('Create Pricing Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

