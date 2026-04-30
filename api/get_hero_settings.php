<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$settingsFile = '../data/hero_settings.json';

$defaults = [
    'index'       => ['image' => 'heroImage/img-4.webp', 'title_en' => '', 'title_ar' => '', 'subtitle_en' => '', 'subtitle_ar' => ''],
    'blog'        => ['image' => 'heroImage/img-4.webp', 'title_en' => '', 'title_ar' => '', 'subtitle_en' => '', 'subtitle_ar' => ''],
    'spaces'      => ['image' => 'heroImage/img-3.webp', 'title_en' => 'Discover Our Dynamic', 'title_ar' => 'اكتشف مساحتنا الديناميكية', 'subtitle_en' => '', 'subtitle_ar' => '', 'tags_en' => 'Art, Gallery, Hub, Library, Shop, Café', 'tags_ar' => 'فن, معرض, مركز, مكتبة, متجر, مقهى'],
    'exhibitions' => ['image' => 'heroImage/img-4.webp', 'title_en' => '', 'title_ar' => '', 'subtitle_en' => '', 'subtitle_ar' => ''],
    'contact'     => ['image' => 'heroImage/img-4.webp', 'title_en' => '', 'title_ar' => '', 'subtitle_en' => '', 'subtitle_ar' => ''],
    'shop'        => ['image' => 'heroImage/img-4.webp', 'title_en' => '', 'title_ar' => '', 'subtitle_en' => '', 'subtitle_ar' => ''],
];

if (file_exists($settingsFile)) {
    $saved = json_decode(file_get_contents($settingsFile), true);
    if ($saved) {
        $defaults = array_merge($defaults, $saved);
    }
}

echo json_encode(['success' => true, 'data' => $defaults]);
?>
