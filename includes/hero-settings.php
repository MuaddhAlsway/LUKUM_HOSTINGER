<?php
/**
 * Hero Settings Helper
 * Loads hero image and text for a given page from data/hero_settings.json
 */

$_heroSettingsCache = null;

function getHeroSettings($page) {
    global $_heroSettingsCache;

    $defaults = [
        'image'       => 'heroImage/img-4.webp',
        'title_en'    => '',
        'title_ar'    => '',
        'subtitle_en' => '',
        'subtitle_ar' => '',
        'tags_en'     => '',
        'tags_ar'     => '',
    ];

    if ($_heroSettingsCache === null) {
        $file = __DIR__ . '/../data/hero_settings.json';
        $_heroSettingsCache = [];
        if (file_exists($file)) {
            $all = json_decode(file_get_contents($file), true);
            if ($all) $_heroSettingsCache = $all;
        }
    }

    if (isset($_heroSettingsCache[$page])) {
        $defaults = array_merge($defaults, $_heroSettingsCache[$page]);
    }
    return $defaults;
}

/**
 * Get hero title for a page — saved setting wins over translation fallback
 */
function getHeroTitle($page, $translationKey, $fallback = '') {
    $h    = getHeroSettings($page);
    $lang = function_exists('getCurrentLanguage') ? getCurrentLanguage() : 'en';
    $key  = ($lang === 'ar') ? 'title_ar' : 'title_en';
    $saved = trim($h[$key] ?? '');
    if ($saved !== '') return htmlspecialchars($saved);
    // Fall back to translation system
    return function_exists('t') ? t($translationKey, $fallback) : htmlspecialchars($fallback);
}

/**
 * Get hero subtitle for a page — saved setting wins over translation fallback
 */
function getHeroSubtitle($page, $translationKey, $fallback = '') {
    $h    = getHeroSettings($page);
    $lang = function_exists('getCurrentLanguage') ? getCurrentLanguage() : 'en';
    $key  = ($lang === 'ar') ? 'subtitle_ar' : 'subtitle_en';
    $saved = trim($h[$key] ?? '');
    if ($saved !== '') return htmlspecialchars($saved);
    return function_exists('t') ? t($translationKey, $fallback) : htmlspecialchars($fallback);
}

/**
 * Render the hero image wrapper HTML (image + overlay only)
 */
function renderHero($page, $altText = 'LAKUM Artspace') {
    $h    = getHeroSettings($page);
    $lang = function_exists('getCurrentLanguage') ? getCurrentLanguage() : 'en';
    $isAr = ($lang === 'ar');

    $image   = htmlspecialchars($h['image']);
    $tagsKey = $isAr ? 'tags_ar' : 'tags_en';
    $tagsRaw = $h[$tagsKey] ?? '';

    echo '<div class="lakum-hero__image-wrapper">';
    echo '<img src="' . $image . '" alt="' . htmlspecialchars($altText) . '" class="lakum-hero__image"';
    echo ' fetchpriority="high" loading="eager" decoding="async" width="1200" height="800"';
    echo ' style="width:100%;height:100%;object-fit:cover;display:block;">';
    echo '<div class="lakum-hero__overlay"></div>';

    // Spaces: inject tags via JS after DOM ready
    if ($page === 'spaces' && $tagsRaw) {
        $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw))));
        if (!empty($tags)) {
            $tagsJson = json_encode($tags, JSON_UNESCAPED_UNICODE);
            echo '<script>document.addEventListener("DOMContentLoaded",function(){';
            echo 'var list=document.querySelector(".lakum-spaces-hero__tags");';
            echo 'if(list){var tags=' . $tagsJson . ';';
            echo 'list.innerHTML=tags.map(function(t){return\'<li class="lakum-spaces-hero__tag">\'+t+\'</li>\';}).join("");}';
            echo '});</script>';
        }
    }

    echo '</div>';
}
?>
