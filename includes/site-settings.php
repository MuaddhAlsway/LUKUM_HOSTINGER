<?php
/**
 * Site Settings Helper
 * Reads from data/site_settings.json — saved via admin/site-settings.html
 * Falls back to t() translation system if no saved value exists.
 */

$_siteSettingsCache = null;

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
 */
function ssRaw($page, $key, $fallback = '') {
    $all   = _loadSiteSettings();
    $saved = trim($all[$page][$key] ?? '');
    return $saved !== '' ? $saved : $fallback;
}
?>
