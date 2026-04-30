<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = '../data/site_settings.json';
$saved = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

// Deep merge saved over defaults per page
foreach ($saved as $page => $vals) {
    // just pass through — frontend has defaults
}

echo json_encode(['success'=>true,'data'=>$saved]);
?>
