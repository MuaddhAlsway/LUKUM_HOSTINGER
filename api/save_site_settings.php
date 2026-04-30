<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$settingsFile = '../data/site_settings.json';
$validPages   = ['home','about','spaces','contact','shop'];

function loadSettings($file) {
    return file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
}
function writeSettings($file, $data) {
    $dir = dirname($file);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
}

// ── Image upload ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $page = trim($_POST['page'] ?? '');
    $key  = trim($_POST['key']  ?? '');

    if (!in_array($page, $validPages) || !$key) {
        echo json_encode(['success'=>false,'message'=>'Invalid page or key']); exit;
    }

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) { echo json_encode(['success'=>false,'message'=>'Upload error']); exit; }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) { echo json_encode(['success'=>false,'message'=>'File type not allowed']); exit; }
    if ($file['size'] > 10*1024*1024) { echo json_encode(['success'=>false,'message'=>'Max 10MB']); exit; }

    // Determine upload directory based on key
    if (strpos($key, 'floor_plan') !== false) {
        $uploadDir = '../assest/';
        $prefix    = 'floor_';
    } elseif (strpos($key, 'hall') !== false) {
        $uploadDir = '../HADAFCompany/';
        $prefix    = '';
    } else {
        $uploadDir = '../heroImage/';
        $prefix    = 'hero_';
    }

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $filename = $prefix . $page . '_' . $key . '_' . time() . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success'=>false,'message'=>'Failed to save image']); exit;
    }

    // Determine relative path
    if (strpos($key, 'floor_plan') !== false) $imgPath = 'assest/' . $filename;
    elseif (strpos($key, 'hall') !== false)   $imgPath = 'HADAFCompany/' . $filename;
    else                                       $imgPath = 'heroImage/' . $filename;

    $settings = loadSettings($settingsFile);
    if (!isset($settings[$page])) $settings[$page] = [];
    $settings[$page][$key] = $imgPath;
    writeSettings($settingsFile, $settings);

    // Sync hero image to hero_settings.json
    if ($key === 'hero_image') {
        $heroFile = '../data/hero_settings.json';
        $heroSettings = loadSettings($heroFile);
        $heroKey = ($page === 'home') ? 'index' : $page;
        if (!isset($heroSettings[$heroKey])) $heroSettings[$heroKey] = [];
        $heroSettings[$heroKey]['image'] = $imgPath;
        writeSettings($heroFile, $heroSettings);
    }

    echo json_encode(['success'=>true,'image'=>$imgPath]);
    exit;
}

// ── Text save ─────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['page'])) { echo json_encode(['success'=>false,'message'=>'No data']); exit; }

    $page = trim($data['page']);
    if (!in_array($page, $validPages)) { echo json_encode(['success'=>false,'message'=>'Invalid page']); exit; }

    $settings = loadSettings($settingsFile);
    if (!isset($settings[$page])) $settings[$page] = [];

    // Save all fields except meta keys
    $skip = ['page','section'];
    foreach ($data as $k => $v) {
        if (!in_array($k, $skip)) $settings[$page][$k] = trim($v);
    }

    if (writeSettings($settingsFile, $settings)) {
        // Sync hero text to hero_settings.json
        $heroFile = '../data/hero_settings.json';
        $heroSettings = loadSettings($heroFile);
        $heroKey = ($page === 'home') ? 'index' : $page;
        if (!isset($heroSettings[$heroKey])) $heroSettings[$heroKey] = [];

        $heroMap = [
            'hero_title_en'    => 'title_en',
            'hero_title_ar'    => 'title_ar',
            'hero_subtitle_en' => 'subtitle_en',
            'hero_subtitle_ar' => 'subtitle_ar',
            'hero_tags_en'     => 'tags_en',
            'hero_tags_ar'     => 'tags_ar',
        ];
        foreach ($heroMap as $siteKey => $heroSettingKey) {
            if (isset($data[$siteKey])) $heroSettings[$heroKey][$heroSettingKey] = trim($data[$siteKey]);
        }
        writeSettings($heroFile, $heroSettings);

        echo json_encode(['success'=>true,'message'=>'Saved']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Write failed']);
    }
    exit;
}

echo json_encode(['success'=>false,'message'=>'Invalid request']);
?>
