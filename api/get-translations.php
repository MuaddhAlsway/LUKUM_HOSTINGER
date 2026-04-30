<?php
/**
 * Get Translations API
 * Serves JSON translation files for static content
 * 
 * Usage: GET /api/get-translations.php?lang=ar
 * Returns: JSON object with all translations for requested language
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate'); // No caching

// Get language parameter
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

// Validate language
$allowed_langs = ['en', 'ar'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'en';
}

// Build file path
$file_path = __DIR__ . '/../translations/' . $lang . '.json';

// Check if file exists
if (!file_exists($file_path)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Translation file not found',
        'lang' => $lang
    ]);
    exit;
}

// Read and return file
try {
    $content = file_get_contents($file_path);
    $translations = json_decode($content, true);
    
    if ($translations === null) {
        throw new Exception('Invalid JSON in translation file');
    }
    
    echo json_encode([
        'success' => true,
        'lang' => $lang,
        'translations' => $translations,
        'count' => count($translations)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'lang' => $lang
    ]);
}
?>


