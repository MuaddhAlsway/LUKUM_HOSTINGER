<?php
header('Content-Type: application/json');

$settingsFile = '../data/settings.json';

// Default settings
$defaultSettings = [
    'booking_link' => 'https://form.typeform.com/to/d6ltE0yW',
    'shop_link' => 'https://souvenirs.sa/ar/category/oyajz'
];

if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true);
    if ($settings) {
        echo json_encode(['success' => true, 'data' => $settings]);
    } else {
        echo json_encode(['success' => true, 'data' => $defaultSettings]);
    }
} else {
    echo json_encode(['success' => true, 'data' => $defaultSettings]);
}
?>


