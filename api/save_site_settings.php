<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$settingsFile = '../data/site_settings.json';
$heroImageDir = '../heroImage/';
$validPages   = ['home', 'blog', 'spaces', 'exhibitions', 'contact', 'shop'];
$textFields   = ['title_en', 'title_ar', 'subtitle_en', 'subtitle_ar', 'tags_en', 'tags_ar'];

// ── Image upload ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_image'])) {
    $page = trim($_POST['page'] ?? '');
    if (!in_array($page, $validPages)) { echo json_encode(['success'=>false,'message'=>'Invalid page']); exit; }

    $file = $_FILES['hero_image'];
    if ($file['error'] !== UPLOAD_ERR_OK) { echo json_encode(['success'=>false,'message'=>'Upload error']); exit; }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) { echo json_encode(['success'=>false,'message'=>'File type not allowed']); exit; }
    if ($file['size'] > 10*1024*1024) { echo json_encode(['success'=>false,'message'=>'Max 10MB']); exit; }

    if (!is_dir($heroImageDir)) mkdir($heroImageDir, 0755, true);
    $filename = 'hero_' . $page . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $heroImageDir . $filename)) {
        echo json_encode(['success'=>false,'message'=>'Failed to save image']); exit;
    }

    $settings = file_exists($settingsFile) ? (json_decode(file_get_contents($settingsFile), true) ?: []) : [];
    if (!isset($settings[$page])) $settings[$page] = [];
    $settings[$page]['hero_image'] = 'heroImage/' . $filename;

    $dir = dirname($settingsFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Sync to hero_settings.json
    $heroFile = '../data/hero_settings.json';
    $heroSettings = file_exists($heroFile) ? (json_decode(file_get_contents($heroFile), true) ?: []) : [];
    $heroKey = ($page === 'home') ? 'index' : $page;
    if (!isset($heroSettings[$heroKey])) $heroSettings[$heroKey] = [];
    $heroSettings[$heroKey]['image'] = 'heroImage/' . $filename;
    file_put_contents($heroFile, json_encode($heroSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    echo json_encode(['success'=>true, 'image'=>'heroImage/'.$filename]);
    exit;
}

// ── Text save ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['page'])) { echo json_encode(['success'=>false,'message'=>'No data']); exit; }

    $page = trim($data['page']);
    if (!in_array($page, $validPages)) { echo json_encode(['success'=>false,'message'=>'Invalid page']); exit; }

    $settings = file_exists($settingsFile) ? (json_decode(file_get_contents($settingsFile), true) ?: []) : [];
    if (!isset($settings[$page])) $settings[$page] = [];

    foreach ($textFields as $f) {
        if (array_key_exists($f, $data)) $settings[$page][$f] = trim($data[$f]);
    }

    $dir = dirname($settingsFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))) {
        // Also sync to hero_settings.json so renderHero() picks up changes
        $heroFile = '../data/hero_settings.json';
        $heroSettings = file_exists($heroFile) ? (json_decode(file_get_contents($heroFile), true) ?: []) : [];
        $heroKey = ($page === 'home') ? 'index' : $page;
        if (!isset($heroSettings[$heroKey])) $heroSettings[$heroKey] = [];
        foreach ($textFields as $f) {
            if (array_key_exists($f, $data)) $heroSettings[$heroKey][$f] = trim($data[$f]);
        }
        file_put_contents($heroFile, json_encode($heroSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        echo json_encode(['success'=>true,'message'=>'Saved']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Write failed']);
    }
    exit;
}

echo json_encode(['success'=>false,'message'=>'Invalid request']);
?>
