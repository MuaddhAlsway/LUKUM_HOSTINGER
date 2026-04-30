<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$settingsFile = '../data/hero_settings.json';
$heroImageDir = '../heroImage/';

$validPages = ['index', 'blog', 'spaces', 'exhibitions', 'contact', 'shop'];

// ── Handle image upload ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_image'])) {
    $page = isset($_POST['page']) ? trim($_POST['page']) : '';

    if (!in_array($page, $validPages)) {
        echo json_encode(['success' => false, 'message' => 'Invalid page']);
        exit;
    }

    $file = $_FILES['hero_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed']);
        exit;
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File too large (max 10MB)']);
        exit;
    }

    if (!is_dir($heroImageDir)) {
        mkdir($heroImageDir, 0755, true);
    }

    $filename = 'hero_' . $page . '_' . time() . '.' . $ext;
    $dest = $heroImageDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save image']);
        exit;
    }

    // Update settings file with new image path
    $settings = [];
    if (file_exists($settingsFile)) {
        $settings = json_decode(file_get_contents($settingsFile), true) ?: [];
    }
    if (!isset($settings[$page])) $settings[$page] = [];
    $settings[$page]['image'] = 'heroImage/' . $filename;

    $dir = dirname($settingsFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode(['success' => true, 'image' => 'heroImage/' . $filename]);
    exit;
}

// ── Handle text/settings save ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['page'])) {
        echo json_encode(['success' => false, 'message' => 'No data provided']);
        exit;
    }

    $page = trim($data['page']);
    if (!in_array($page, $validPages)) {
        echo json_encode(['success' => false, 'message' => 'Invalid page']);
        exit;
    }

    $settings = [];
    if (file_exists($settingsFile)) {
        $settings = json_decode(file_get_contents($settingsFile), true) ?: [];
    }

    if (!isset($settings[$page])) $settings[$page] = [];

    // Only update text fields — image is handled separately
    foreach (['title_en', 'title_ar', 'subtitle_en', 'subtitle_ar'] as $field) {
        if (isset($data[$field])) {
            $settings[$page][$field] = trim($data[$field]);
        }
    }

    $dir = dirname($settingsFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        echo json_encode(['success' => true, 'message' => 'Saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to write settings file']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
