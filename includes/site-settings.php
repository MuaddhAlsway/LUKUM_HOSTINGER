<?php
/**
 * Site Settings Helper
 * Priority order:
 * 1. Database (site_settings table) - for dynamic settings like booking_link, shop_link
 * 2. JSON file (data/site_settings.json) - for page content settings
 * 3. Translation system t() - for fallback
 * 4. Hardcoded default - final fallback
 */

$_siteSettingsCache = null;
$_dbSettingsCache = null;

function _loadDatabaseSettings() {
    global $_dbSettingsCache;
    if ($_dbSettingsCache === null) {
        $_dbSettingsCache = [];
        try {
            // Try to load from database
            require_once __DIR__ . '/../api/config.php';
            $db = Database::getInstance();
            
            if ($db && $db->isConnected()) {
                $conn = $db->getConnection();
                $query = "SELECT setting_key, setting_value FROM site_settings 
                          WHERE setting_key IN ('booking_link', 'shop_link')";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $_dbSettingsCache[$row['setting_key']] = $row['setting_value'];
                    }
                }
            }
        } catch (Exception $e) {
            // Database not available, fall back to JSON
            error_log('Database settings unavailable: ' . $e->getMessage());
        }
    }
    return $_dbSettingsCache;
}

function _loadSiteSettings() {
    global $_siteSettingsCache;
    if ($_siteSettingsCache === null) {
        $file = __DIR__ . '/../data/site_settings.json';
        $_siteSettingsCache = [];
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) $_siteSettingsCache = $data;
        }
    }
    return $_siteSettingsCache;
}

/**
 * Get a site setting value.
 * Saved value wins; falls back to t() then to $fallback.
 *
 * @param string $page         Page key: home|about|spaces|contact|shop
 * @param string $key          Setting key (e.g. 'cta1_title_en')
 * @param string $tKey         Translation key fallback (e.g. 'cta_title')
 * @param string $fallback     Hard-coded English fallback
 * @return string              HTML-safe string
 */
function ss($page, $key, $tKey = '', $fallback = '') {
    $all  = _loadSiteSettings();
    $lang = function_exists('getCurrentLanguage') ? getCurrentLanguage() : 'en';

    // Auto-switch to Arabic key when language is AR and key ends in _en
    if ($lang === 'ar' && substr($key, -3) === '_en') {
        $arKey  = substr($key, 0, -3) . '_ar';
        $savedAr = trim($all[$page][$arKey] ?? '');
        if ($savedAr !== '') return htmlspecialchars($savedAr, ENT_QUOTES, 'UTF-8');
    }

    // Try the requested key
    $saved = trim($all[$page][$key] ?? '');
    if ($saved !== '') return htmlspecialchars($saved, ENT_QUOTES, 'UTF-8');

    // Fall back to translation system
    if ($tKey && function_exists('t')) return t($tKey, $fallback);

    return htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8');
}

/**
 * Get a raw (unescaped) site setting — use only for URLs/src attributes.
 * Priority:
 * 1. Database (for booking_link, shop_link)
 * 2. JSON file
 * 3. Fallback
 */
function ssRaw($page, $key, $fallback = '') {
    // Check database FIRST for critical dynamic settings
    if ($key === 'booking_link' || $key === 'shop_link') {
        $dbSettings = _loadDatabaseSettings();
        $dbValue = trim($dbSettings[$key] ?? '');
        if ($dbValue !== '') {
            return $dbValue;
        }
    }
    
    // Fall back to JSON file
    $all   = _loadSiteSettings();
    $saved = trim($all[$page][$key] ?? '');
    return $saved !== '' ? $saved : $fallback;
}
?>
