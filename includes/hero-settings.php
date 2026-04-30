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
    $tagsKey  = $isAr ? 'tags_ar' : 'tags_en';
    $title    = htmlspecialchars($h[$titleKey] ?? '');
    $subtitle = htmlspecialchars($h[$subKey] ?? '');
    $tagsRaw  = $h[$tagsKey] ?? '';

    echo '<div class="lakum-hero__image-wrapper">';
    echo '<img src="' . $image . '" alt="' . htmlspecialchars($altText) . '" class="lakum-hero__image"';
    echo ' fetchpriority="high" loading="eager" decoding="async" width="1200" height="800"';
    echo ' style="width:100%;height:100%;object-fit:cover;display:block;">';
    echo '<div class="lakum-hero__overlay"></div>';

    // Spaces page: render tags instead of subtitle
    if ($page === 'spaces' && $tagsRaw) {
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
        if (!empty($tags)) {
            // Inject tags into the existing .lakum-spaces-hero__tags list via data attribute
            // We output a script that replaces the tag list after DOM ready
            $tagsJson = json_encode(array_values($tags));
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
