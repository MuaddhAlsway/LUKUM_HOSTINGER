<?php
header('Content-Type: application/json');

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit;
}

// Validate URLs
$booking_link = isset($data['booking_link']) ? trim($data['booking_link']) : '';
$shop_link = isset($data['shop_link']) ? trim($data['shop_link']) : '';

if (!$booking_link || !$shop_link) {
    echo json_encode(['success' => false, 'message' => 'Both links are required']);
    exit;
}

// Validate URLs format
if (!filter_var($booking_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking link URL']);
    exit;
}

if (!filter_var($shop_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid shop link URL']);
    exit;
}

// Create settings file if it doesn't exist
$settingsFile = '../data/settings.json';
$settingsDir = dirname($settingsFile);

if (!is_dir($settingsDir)) {
    mkdir($settingsDir, 0755, true);
}

// Prepare settings data
$settings = [
    'booking_link' => $booking_link,
    'shop_link' => $shop_link,
    'updated_at' => date('Y-m-d H:i:s')
];

// Save to JSON file
if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
    echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
}
?>

