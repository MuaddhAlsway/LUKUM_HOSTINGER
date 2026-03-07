<?php
/**
 * LAKUM Artspace - Smart Populate Press Arabic Translations
 * INTELLIGENT: Automatically detects which press items need Arabic translations
 * and updates them with REAL Arabic content
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // REAL Arabic translations - comprehensive set
    $arabicTranslations = [
        // Map by title_en to find and update items
        'Explore an interactive workshop By The Culture Mocktail at Lakum' => [
            'title' => 'استكشف ورشة عمل تفاعلية من قبل The Culture Mocktail في Lakum',
            'excerpt' => 'تغطي Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في LAKUM Artspace.',
            'content' => 'تغطي Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في LAKUM Artspace. هذا حدث ثقافي مهم يجمع بين الفن والابتكار.'
        ],
        'TikTok Creator Hub Awards Women Entrepreneurs in Riyadh (#HerAmbitions)' => [
            'title' => 'جوائز منصة TikTok للمبدعين تكرم رائدات الأعمال في الرياض',
            'excerpt' => 'تغطي Zawya جوائز منصة TikTok MENA للمبدعين التي تكرم رائدات الأعمال في الرياض في LAKUM Artspace.',
            'content' => 'تغطي Zawya جوائز منصة TikTok MENA للمبدعين التي تكرم رائدات الأعمال في الرياض في LAKUM Artspace. هذا الحدث يسلط الضوء على دور المرأة في ريادة الأعمال.'
        ],
        'Pre-Ramadan pop-up show in Riyadh\'s Lakum Artspace' => [
            'title' => 'عرض منبثق قبل رمضان في Lakum Artspace بالرياض',
            'excerpt' => 'تسلط KSA Directory الضوء على معرض منبثق خاص قبل رمضان في LAKUM Artspace.',
            'content' => 'تسلط KSA Directory الضوء على معرض منبثق خاص قبل رمضان في LAKUM Artspace يضم متاجر منتقاة بعناية.'
        ],
        'Snap shows power of AR in transforming fashion, beauty in Saudi Arabia' => [
            'title' => 'Snap تعرض قوة الواقع المعزز في تحويل الموضة والجمال في المملكة العربية السعودية',
            'excerpt' => 'تقرر Arab News عن عرض Snap للواقع المعزز في LAKUM Artspace، مما يوضح تأثير AR على الموضة والجمال.',
            'content' => 'تقرر Arab News عن عرض Snap للواقع المعزز في LAKUM Artspace، مما يوضح تأثير تكنولوجيا الواقع المعزز على صناعة الموضة والجمال.'
        ],
        'Photographer Faisal bin Zarah\'s exhibition is a love letter to the Kingdom' => [
            'title' => 'معرض المصور فيصل بن زارة - رسالة حب للمملكة',
            'excerpt' => 'تعرض Arab News معرض المصور فيصل بن زارة في LAKUM Artspace، الذي يحتفي بالمملكة العربية السعودية من خلال التصوير الفوتوغرافي.',
            'content' => 'تعرض Arab News معرض المصور فيصل بن زارة في LAKUM Artspace، الذي يحتفي بالمملكة العربية السعودية من خلال التصوير الفوتوغرافي الفني.'
        ],
        'Artworks by 20 Saudi women on display for International Women\'s Day' => [
            'title' => 'أعمال فنية لـ 20 امرأة سعودية معروضة بمناسبة اليوم العالمي للمرأة',
            'excerpt' => 'تغطي Saudi Gazette معرضاً في LAKUM Artspace يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة.',
            'content' => 'تغطي Saudi Gazette معرضاً في LAKUM Artspace يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة، مما يسلط الضوء على الإبداع النسائي.'
        ],
        'Artist Ahmed Mater opens Lakum Artspace with "Diagnosis: 1979-2019" exhibition' => [
            'title' => 'الفنان أحمد ماطر يفتتح Lakum Artspace بمعرض "التشخيص: 1979-2019"',
            'excerpt' => 'تعلن GDN Life عن افتتاح LAKUM Artspace مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019".',
            'content' => 'تعلن GDN Life عن افتتاح LAKUM Artspace مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019"، وهو معرض فني مهم يستكشف الهوية والتاريخ.'
        ],
        'Lakum Artspace to open in December' => [
            'title' => 'Lakum Artspace ستفتتح في ديسمبر',
            'excerpt' => 'تعلن Time Out Riyadh عن افتتاح LAKUM Artspace في ديسمبر 2021.',
            'content' => 'تعلن Time Out Riyadh عن افتتاح LAKUM Artspace في ديسمبر 2021، وهي مساحة فنية جديدة مثيرة في الرياض.'
        ]
    ];
    
    // First, get all published press items
    $query = "SELECT id, title, title_en, title_ar FROM press WHERE is_published = 1 ORDER BY id ASC";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $updated = 0;
    $skipped = 0;
    $errors = [];
    $updatedItems = [];
    
    while ($row = $result->fetch_assoc()) {
        $pressId = $row['id'];
        $titleEn = $row['title_en'] ?? $row['title'];
        $titleAr = $row['title_ar'];
        
        // Check if this item already has Arabic translation
        if (!empty($titleAr) && $titleAr !== $titleEn) {
            $skipped++;
            continue;
        }
        
        // Look for matching English title in our translations
        $found = false;
        foreach ($arabicTranslations as $enTitle => $translations) {
            if (stripos($titleEn, substr($enTitle, 0, 30)) !== false || 
                stripos($enTitle, substr($titleEn, 0, 30)) !== false) {
                
                // Found a match - update this press item
                $update_query = "UPDATE press SET title_ar = ?, content_ar = ?, excerpt_ar = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                
                if (!$update_stmt) {
                    $errors[] = "Prepare failed for ID $pressId";
                    continue;
                }
                
                $title_ar = $translations['title'];
                $content_ar = $translations['content'];
                $excerpt_ar = $translations['excerpt'];
                
                $update_stmt->bind_param('sssi', $title_ar, $content_ar, $excerpt_ar, $pressId);
                
                if ($update_stmt->execute()) {
                    $updated++;
                    $updatedItems[] = [
                        'id' => $pressId,
                        'title_en' => $titleEn,
                        'title_ar' => $title_ar
                    ];
                    $found = true;
                    break;
                } else {
                    $errors[] = "Execute failed for ID $pressId: " . $update_stmt->error;
                }
            }
        }
        
        if (!$found) {
            $skipped++;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Press Arabic translations populated successfully',
        'updated' => $updated,
        'skipped' => $skipped,
        'updated_items' => $updatedItems,
        'errors' => $errors,
        'note' => 'Arabic translations have been added to the database. Press cards will now display Arabic content when ?lang=ar. Hard refresh your browser (Ctrl+Shift+R) to see changes.'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Populate Press Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
