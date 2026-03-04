<?php
/**
 * LAKUM Artspace - Insert Press Data
 * Inserts press articles from press.html into the database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Press data from press.html
    $pressData = [
        [
            'title' => 'Explore an interactive workshop By The Culture Mocktail at Lakum',
            'excerpt' => 'Time Out Riyadh features an interactive workshop hosted by The Culture Mocktail at LAKUM Artspace.',
            'content' => 'Time Out Riyadh features an interactive workshop hosted by The Culture Mocktail at LAKUM Artspace.',
            'source' => 'Time Out Riyadh',
            'category' => 'Workshop',
            'cover_image' => 'uploads/uploads/press/press_1_1765953905.jpg',
            'press_date' => '2025-12-16',
            'url' => 'https://www.timeoutriyadh.com/things-to-do/things-to-do-in-riyadh'
        ],
        [
            'title' => 'TikTok Creator Hub Awards Women Entrepreneurs in Riyadh (#HerAmbitions)',
            'excerpt' => 'Zawya covers the TikTok MENA Creator Hub awards celebrating women entrepreneurs in Riyadh at LAKUM Artspace.',
            'content' => 'Zawya covers the TikTok MENA Creator Hub awards celebrating women entrepreneurs in Riyadh at LAKUM Artspace.',
            'source' => 'Zawya',
            'category' => 'Award',
            'cover_image' => 'uploads/uploads/press/press_2_1765953905.jpg',
            'press_date' => '2024-02-28',
            'url' => 'https://www.zawya.com/en/press-release/events-and-conferences/tiktok-mena-creator-hub-awards-women-entrepreneurs-in-riyadh-ilt72tyv'
        ],
        [
            'title' => 'Pre-Ramadan pop-up show in Riyadh\'s Lakum Artspace',
            'excerpt' => 'KSA Directory highlights a special pre-Ramadan pop-up exhibition at LAKUM Artspace.',
            'content' => 'KSA Directory highlights a special pre-Ramadan pop-up exhibition at LAKUM Artspace.',
            'source' => 'KSA Directory',
            'category' => 'Exhibition',
            'cover_image' => 'uploads/uploads/press/press_3_1765953905.svg',
            'press_date' => '2023-03-21',
            'url' => 'https://www.ksa.directory/pre-ramadan-pop-up-show-in-riyadh-s-lakum-artspace-consists-of-curated-pop-up-shops/396/n'
        ],
        [
            'title' => 'Snap shows power of AR in transforming fashion, beauty in Saudi Arabia',
            'excerpt' => 'Arab News reports on Snap\'s augmented reality showcase at LAKUM Artspace, demonstrating AR\'s impact on fashion and beauty.',
            'content' => 'Arab News reports on Snap\'s augmented reality showcase at LAKUM Artspace, demonstrating AR\'s impact on fashion and beauty.',
            'source' => 'Arab News',
            'category' => 'Technology',
            'cover_image' => 'uploads/uploads/press/press_5_1765953905.png',
            'press_date' => '2022-10-08',
            'url' => 'https://www.arabnews.com/node/2177376/saudi-arabia'
        ],
        [
            'title' => 'Photographer Faisal bin Zarah\'s exhibition is a love letter to the Kingdom',
            'excerpt' => 'Arab News features photographer Faisal bin Zarah\'s exhibition at LAKUM Artspace, celebrating Saudi Arabia through photography.',
            'content' => 'Arab News features photographer Faisal bin Zarah\'s exhibition at LAKUM Artspace, celebrating Saudi Arabia through photography.',
            'source' => 'Arab News',
            'category' => 'Exhibition',
            'cover_image' => 'uploads/uploads/press/press_5_1765953905.png',
            'press_date' => '2022-09-15',
            'url' => 'https://www.arabnews.com/node/2174501/saudi-arabia'
        ],
        [
            'title' => 'Artworks of 20 Saudi women on display to mark International Women\'s Day',
            'excerpt' => 'Saudi Gazette covers an exhibition at LAKUM Artspace featuring artworks by 20 Saudi women artists for International Women\'s Day.',
            'content' => 'Saudi Gazette covers an exhibition at LAKUM Artspace featuring artworks by 20 Saudi women artists for International Women\'s Day.',
            'source' => 'Saudi Gazette',
            'category' => 'Exhibition',
            'cover_image' => 'uploads/uploads/press/press_6_1765953905.svg',
            'press_date' => '2022-03-08',
            'url' => 'https://www.saudigazette.com.sa/article/617910/SAUDI-ARABIA/Artworks-of-20-Saudi-women-are-on-display-in-Riyadh-to-mark-International-Womens-Day'
        ],
        [
            'title' => 'Ahmed Mater to inaugurate Lakum Artspace with Prognosis: 1979-2019',
            'excerpt' => 'GDN Life announces the inauguration of LAKUM Artspace with renowned artist Ahmed Mater\'s exhibition "Prognosis: 1979-2019".',
            'content' => 'GDN Life announces the inauguration of LAKUM Artspace with renowned artist Ahmed Mater\'s exhibition "Prognosis: 1979-2019".',
            'source' => 'GDN Life',
            'category' => 'Exhibition',
            'cover_image' => 'uploads/uploads/press/press_7_1765953905.png',
            'press_date' => '2021-11-24',
            'url' => 'https://www.gdnlife.com/Home/ArticleDetail?ArticleId=43966&category=6'
        ],
        [
            'title' => 'Lakum Artspace to open in December',
            'excerpt' => 'Time Out Riyadh announces the upcoming opening of LAKUM Artspace in December 2021.',
            'content' => 'Time Out Riyadh announces the upcoming opening of LAKUM Artspace in December 2021.',
            'source' => 'Time Out Riyadh',
            'category' => 'Announcement',
            'cover_image' => 'uploads/uploads/press/press_8_1765953905.jpg',
            'press_date' => '2021-10-28',
            'url' => 'https://www.timeoutriyadh.com/art/lakum-art-space-to-open-in-december'
        ]
    ];
    
    $query = 'INSERT INTO press (title, excerpt, content, source, category, cover_image, press_date, url, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)';
    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    $insertedCount = 0;
    
    foreach ($pressData as $press) {
        $stmt->bind_param(
            'ssssssss',
            $press['title'],
            $press['excerpt'],
            $press['content'],
            $press['source'],
            $press['category'],
            $press['cover_image'],
            $press['press_date'],
            $press['url']
        );
        
        if ($stmt->execute()) {
            $insertedCount++;
        } else {
            error_log('Insert failed for: ' . $press['title'] . ' - ' . $stmt->error);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Inserted $insertedCount press articles",
        'count' => $insertedCount
    ]);
    
} catch (Exception $e) {
    error_log('Insert Press Data Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


