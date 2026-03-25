<?php
/**
 * LAKUM Language Loader
 * Server-side language detection and translation loading
 * 
 * Features:
 * - Detects language from URL parameter (?lang=en or ?lang=ar)
 * - Stores selection in session
 * - Defaults to English
 * - Loads JSON translation files
 * - Returns translations as PHP array
 * 
 * Usage:
 * require_once 'lang/loader.php';
 * echo $t('nav.home'); // Returns translated text
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============ LANGUAGE DETECTION ============

/**
 * Get current language
 * PRODUCTION ARCHITECTURE: URL is primary authority
 * Priority: URL parameter > Session > Default (English)
 * 
 * This ensures:
 * - URL parameter is always the source of truth
 * - Session remembers last valid selection
 * - No localStorage interference
 * - Query parameters are preserved
 * - Future-proof for pagination, filters, routing
 */
function getCurrentLanguage() {
    // 1️⃣ URL PARAMETER IS PRIMARY AUTHORITY
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
        return $lang;
    }
    
    // 2️⃣ SESSION FALLBACK (remembers last valid selection)
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['en', 'ar'])) {
        return $_SESSION['lang'];
    }
    
    // 3️⃣ DEFAULT TO ENGLISH
    $_SESSION['lang'] = 'en';
    return 'en';
}

// ============ TRANSLATION LOADING ============

/**
 * Load translation file
 */
function loadTranslations($file) {
    $lang = getCurrentLanguage();
    $filePath = __DIR__ . '/' . $lang . '/' . $file . '.json';
    
    if (!file_exists($filePath)) {
        // Fallback to English if translation file doesn't exist
        $filePath = __DIR__ . '/en/' . $file . '.json';
    }
    
    if (file_exists($filePath)) {
        $json = file_get_contents($filePath);
        return json_decode($json, true);
    }
    
    return [];
}

/**
 * Get translation value
 * Supports dot notation: 'nav.home' -> $translations['nav']['home']
 */
function getTranslation($key, $default = '') {
    global $translations;
    
    if (!isset($translations)) {
        return $default;
    }
    
    $keys = explode('.', $key);
    $value = $translations;
    
    foreach ($keys as $k) {
        if (is_array($value) && isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }
    
    return $value;
}

/**
 * Shorthand function for translation
 */
function t($key, $default = '') {
    return getTranslation($key, $default);
}

/**
 * Get current language code
 */
function getLang() {
    return getCurrentLanguage();
}

/**
 * Check if current language is Arabic
 */
function isArabic() {
    return getCurrentLanguage() === 'ar';
}

/**
 * Get language attributes for HTML tag
 */
function getLanguageAttributes() {
    $lang = getCurrentLanguage();
    $dir = $lang === 'ar' ? 'rtl' : 'ltr';
    return "lang=\"{$lang}\" dir=\"{$dir}\"";
}

/**
 * Get language class for body tag
 */
function getLanguageClass() {
    $lang = getCurrentLanguage();
    return "lang-{$lang} " . ($lang === 'ar' ? 'rtl' : 'ltr');
}

/**
 * Build language switcher URL preserving all query parameters
 * PRODUCTION ARCHITECTURE: Query-safe language switching
 * 
 * This ensures:
 * - Pagination parameters are preserved
 * - Filter parameters are preserved
 * - Search parameters are preserved
 * - Dynamic IDs are preserved
 * - Works in subfolders
 * - Future-proof for routing
 */
function buildLanguageSwitcherUrl($targetLang = null) {
    $currentPath = strtok($_SERVER['REQUEST_URI'], '?');
    $queryParams = $_GET;
    
    // If target language is specified, use it; otherwise toggle
    if ($targetLang && in_array($targetLang, ['en', 'ar'])) {
        $queryParams['lang'] = $targetLang;
    } else {
        // Toggle language (fallback for backward compatibility)
        $queryParams['lang'] = (getCurrentLanguage() === 'ar') ? 'en' : 'ar';
    }
    
    // Rebuild query string safely
    $newUrl = $currentPath . '?' . http_build_query($queryParams);
    
    return htmlspecialchars($newUrl);
}

// ============ PAGE DETECTION ============

/**
 * Get current page name from URL
 */
function getCurrentPage() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $filename = basename($path, '.php');
    
    // Map filenames to page names
    $pageMap = [
        'index' => 'home',
        'about' => 'about',
        'spaces' => 'spaces',
        'contact' => 'contact',
        'exhibitions' => 'exhibitions',
        'blog' => 'blog',
        'press' => 'press',
        'calendar' => 'calendar',
        'event' => 'event',
        'blogPageDetails' => 'blog',
        'privacy' => 'privacy',
        'terms' => 'terms',
        'shop' => 'shop'
    ];
    
    return isset($pageMap[$filename]) ? $pageMap[$filename] : 'home';
}

// ============ INITIALIZATION ============

// Get current language
$currentLang = getCurrentLanguage();
$currentPage = getCurrentPage();

// Load all translation files
$translations = [];

// Load common translations (always needed)
$commonFiles = ['nav', 'footer'];
foreach ($commonFiles as $file) {
    $filePath = __DIR__ . '/' . $currentLang . '/' . $file . '.json';
    if (file_exists($filePath)) {
        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        $translations = array_merge($translations, $data);
    }
}

// Load page-specific translations
$pageFile = __DIR__ . '/' . $currentLang . '/' . $currentPage . '.json';
if (file_exists($pageFile)) {
    $json = file_get_contents($pageFile);
    $data = json_decode($json, true);
    $translations = array_merge($translations, $data);
}

// Load extended translations for pages that have them
$extendedFile = __DIR__ . '/' . $currentLang . '/' . $currentPage . '-extended.json';
if (file_exists($extendedFile)) {
    $json = file_get_contents($extendedFile);
    $data = json_decode($json, true);
    $translations = array_merge($translations, $data);
}

// Fallback: Load all common page files if specific page not found
if (!file_exists($pageFile)) {
    $commonPageFiles = ['home', 'about', 'spaces', 'contact', 'exhibitions', 'blog', 'press', 'calendar'];
    foreach ($commonPageFiles as $file) {
        $filePath = __DIR__ . '/' . $currentLang . '/' . $file . '.json';
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $data = json_decode($json, true);
            $translations = array_merge($translations, $data);
        }
    }
}

?>


