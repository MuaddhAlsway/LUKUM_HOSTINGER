<?php
/**
 * URL-Based Language Router
 * Converts language system from localStorage to URL parameters
 * 
 * Usage at top of every PHP page:
 * <?php require_once 'lang/url-router.php'; ?>
 * 
 * Then use:
 * - $lang variable for current language
 * - getLanguageUrl($url) to add language parameter to links
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============ LANGUAGE DETECTION ============

/**
 * Detect language from URL parameter
 * Priority: URL parameter (?lang=en or ?lang=ar) > Default (English)
 */
function detectLanguage() {
    // Check URL parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
        return $lang;
    }
    
    // Check session
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['en', 'ar'])) {
        return $_SESSION['lang'];
    }
    
    // Default to English
    $_SESSION['lang'] = 'en';
    return 'en';
}

// Get current language
$lang = detectLanguage();

// ============ HELPER FUNCTIONS ============

/**
 * Get current language
 */
function getLang() {
    global $lang;
    return $lang;
}

/**
 * Check if current language is Arabic
 */
function isArabic() {
    global $lang;
    return $lang === 'ar';
}

/**
 * Get HTML direction attribute
 */
function getDir() {
    return isArabic() ? 'rtl' : 'ltr';
}

/**
 * Get HTML lang attribute
 */
function getHtmlLang() {
    return getLang();
}

/**
 * Get HTML class for language
 */
function getHtmlClass() {
    $lang = getLang();
    return $lang === 'ar' ? 'lang-ar rtl' : 'lang-en ltr';
}

/**
 * Add language parameter to URL
 * Preserves existing query parameters
 */
function getLanguageUrl($url, $newLang = null) {
    global $lang;
    
    if ($newLang === null) {
        $newLang = $lang;
    }
    
    // Parse URL
    $parts = parse_url($url);
    $path = isset($parts['path']) ? $parts['path'] : '';
    $query = isset($parts['query']) ? $parts['query'] : '';
    
    // Parse existing query parameters
    parse_str($query, $params);
    
    // Set language parameter
    $params['lang'] = $newLang;
    
    // Rebuild URL
    $newQuery = http_build_query($params);
    $result = $path;
    if ($newQuery) {
        $result .= '?' . $newQuery;
    }
    
    return $result;
}

/**
 * Get current page URL with language parameter
 */
function getCurrentPageUrl($newLang = null) {
    global $lang;
    
    if ($newLang === null) {
        $newLang = $lang;
    }
    
    // Get current page path
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Get query parameters
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    parse_str($query, $params);
    
    // Set language parameter
    $params['lang'] = $newLang;
    
    // Rebuild URL
    $newQuery = http_build_query($params);
    $result = $path;
    if ($newQuery) {
        $result .= '?' . $newQuery;
    }
    
    return $result;
}

/**
 * Load translations from JSON file
 */
function loadTranslations($file) {
    global $lang;
    
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
 * Get translation value using dot notation
 * Example: t('nav.home') returns $translations['nav']['home']
 */
function t($key, $default = '') {
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


