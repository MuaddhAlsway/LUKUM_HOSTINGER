<?php
/**
 * Hero Settings Helper
 * Loads hero image and text for a given page from data/hero_settings.json
 */
function getHeroSettings($page) {
    $defaults = [
        'image'       => 'heroImage/img-4.webp',
        'title_en'    => '',
        'title_ar'    => '',
        'subtitle_en' => '',
        'subtitle_ar' => '',
    ];

    $file = __DIR__ . '/../data/hero_settings.json';
    if (file_exists($file)) {
        $all = json_decode(file_get_contents($file), true);
        if ($all && isset($all[$page])) {
            $defaults = array_merge($defaults, $all[$page]);
        }
    }
    return $defaults;
}

/**
 * Render the hero image wrapper HTML
 */
function renderHero($page, $altText = 'LAKUM Artspace') {
    $h    = getHeroSettings($page);
    $lang = function_exists('getCurrentLanguage') ? getCurrentLanguage() : 'en';
    $isAr = ($lang === 'ar');

    $image    = htmlspecialchars($h['image']);
    $titleKey = $isAr ? 'title_ar' : 'title_en';
    $subKey   = $isAr ? 'subtitle_ar' : 'subtitle_en';
    $title    = htmlspecialchars($h[$titleKey]);
    $subtitle = htmlspecialchars($h[$subKey]);

    echo '<div class="lakum-hero__image-wrapper">';
    echo '<img src="' . $image . '" alt="' . htmlspecialchars($altText) . '" class="lakum-hero__image"';
    echo ' fetchpriority="high" loading="eager" decoding="async" width="1200" height="800"';
    echo ' style="width:100%;height:100%;object-fit:cover;display:block;">';
    echo '<div class="lakum-hero__overlay"></div>';
    if ($title || $subtitle) {
        echo '<div class="lakum-hero__content" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:white;text-align:center;padding:20px;">';
        if ($title)    echo '<h1 style="font-size:clamp(24px,4vw,52px);font-weight:300;letter-spacing:3px;margin-bottom:12px;">' . $title . '</h1>';
        if ($subtitle) echo '<p style="font-size:clamp(13px,1.5vw,18px);opacity:0.85;max-width:600px;">' . $subtitle . '</p>';
        echo '</div>';
    }
    echo '</div>';
}
?>
