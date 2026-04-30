<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = '../data/site_settings.json';

$defaults = [
    'home' => [
        'hero_image'   => 'heroImage/img-4.webp',
        'title_en'     => 'Where Encounters Shape Culture',
        'title_ar'     => 'حيث تصوغ اللقاءات ملامح الثقافة',
        'subtitle_en'  => 'A living space for art, connection, and cultural exchange in the heart of Riyadh',
        'subtitle_ar'  => 'مساحة نابضة للفن والحوار والتبادل الثقافي في قلب الرياض',
    ],
    'blog' => [
        'hero_image'   => 'heroImage/img-4.webp',
        'title_en'     => 'Stories & Insights',
        'title_ar'     => 'القصص والرؤى',
        'subtitle_en'  => 'Explore the world of art, culture, and creativity through our curated collection of articles, interviews, and behind-the-scenes stories',
        'subtitle_ar'  => 'استكشف عالم الفن والثقافة والإبداع من خلال مجموعتنا المختارة من المقالات والمقابلات والقصص من وراء الكواليس',
    ],
    'spaces' => [
        'hero_image'   => 'heroImage/img-3.webp',
        'title_en'     => 'Discover Our Dynamic',
        'title_ar'     => 'اكتشف مساحتنا الديناميكية',
        'tags_en'      => 'Art, Gallery, Hub, Library, Shop, Café',
        'tags_ar'      => 'فن, معرض, مركز, مكتبة, متجر, مقهى',
    ],
    'exhibitions' => [
        'hero_image'   => 'heroImage/img-4.webp',
    ],
    'contact' => [
        'hero_image'   => 'heroImage/img-3.webp',
        'title_en'     => 'Get In Touch',
        'title_ar'     => 'تواصل معنا',
        'subtitle_en'  => "Have a question or want to collaborate? We'd love to hear from you.",
        'subtitle_ar'  => 'هل لديك سؤال أو تريد التعاون؟ نود أن نسمع منك.',
    ],
    'shop' => [
        'hero_image'   => 'heroImage/img-4.webp',
        'title_en'     => 'Discover Lakum Concept Shop',
        'title_ar'     => 'استكشف متجر لكم الفني',
    ],
];

$saved = [];
if (file_exists($file)) {
    $saved = json_decode(file_get_contents($file), true) ?: [];
}

// Deep merge saved over defaults
foreach ($defaults as $page => $vals) {
    if (isset($saved[$page])) {
        $defaults[$page] = array_merge($vals, $saved[$page]);
    }
}

echo json_encode(['success' => true, 'data' => $defaults]);
?>
