<?php
/**
 * LAKUM Artspace - Populate Press Arabic Translations
 * This script adds REAL Arabic translations to press releases
 * CRITICAL: Updates the main press table columns (title_ar, content_ar, excerpt_ar)
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
    
    // REAL Arabic translations for press releases
    $arabicTranslations = [
        1 => [
            'title' => 'استكشف ورشة عمل تفاعلية من قبل The Culture Mocktail في Lakum',
            'excerpt' => 'تغطي Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في LAKUM Artspace.',
            'content' => 'تغطي Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في LAKUM Artspace. هذا حدث ثقافي مهم يجمع بين الفن والابتكار.'
        ],
        2 => [
            'title' => 'جوائز منصة TikTok للمبدعين تكرم رائدات الأعمال في الرياض',
            'excerpt' => 'تغطي Zawya جوائز منصة TikTok MENA للمبدعين التي تكرم رائدات الأعمال في الرياض في LAKUM Artspace.',
            'content' => 'تغطي Zawya جوائز منصة TikTok MENA للمبدعين التي تكرم رائدات الأعمال في الرياض في LAKUM Artspace. هذا الحدث يسلط الضوء على دور المرأة في ريادة الأعمال.'
        ],
        3 => [
            'title' => 'عرض منبثق قبل رمضان في Lakum Artspace بالرياض',
            'excerpt' => 'تسلط KSA Directory الضوء على معرض منبثق خاص قبل رمضان في LAKUM Artspace.',
            'content' => 'تسلط KSA Directory الضوء على معرض منبثق خاص قبل رمضان في LAKUM Artspace يضم متاجر منتقاة بعناية.'
        ],
        4 => [
            'title' => 'Snap تعرض قوة الواقع المعزز في تحويل الموضة والجمال في المملكة العربية السعودية',
            'excerpt' => 'تقرر Arab News عن عرض Snap للواقع المعزز في LAKUM Artspace، مما يوضح تأثير AR على الموضة والجمال.',
            'content' => 'تقرر Arab News عن عرض Snap للواقع المعزز في LAKUM Artspace، مما يوضح تأثير تكنولوجيا الواقع المعزز على صناعة الموضة والجمال.'
        ],
        5 => [
            'title' => 'معرض المصور فيصل بن زارة - رسالة حب للمملكة',
            'excerpt' => 'تعرض Arab News معرض المصور فيصل بن زارة في LAKUM Artspace، الذي يحتفي بالمملكة العربية السعودية من خلال التصوير الفوتوغرافي.',
            'content' => 'تعرض Arab News معرض المصور فيصل بن زارة في LAKUM Artspace، الذي يحتفي بالمملكة العربية السعودية من خلال التصوير الفوتوغرافي الفني.'
        ],
        6 => [
            'title' => 'أعمال فنية لـ 20 امرأة سعودية معروضة بمناسبة اليوم العالمي للمرأة',
            'excerpt' => 'تغطي Saudi Gazette معرضاً في LAKUM Artspace يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة.',
            'content' => 'تغطي Saudi Gazette معرضاً في LAKUM Artspace يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة، مما يسلط الضوء على الإبداع النسائي.'
        ],
        7 => [
            'title' => 'الفنان أحمد ماطر يفتتح Lakum Artspace بمعرض "التشخيص: 1979-2019"',
            'excerpt' => 'تعلن GDN Life عن افتتاح LAKUM Artspace مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019".',
            'content' => 'تعلن GDN Life عن افتتاح LAKUM Artspace مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019"، وهو معرض فني مهم يستكشف الهوية والتاريخ.'
        ],
        8 => [
            'title' => 'Lakum Artspace ستفتتح في ديسمبر',
            'excerpt' => 'تعلن Time Out Riyadh عن افتتاح LAKUM Artspace في ديسمبر 2021.',
            'content' => 'تعلن Time Out Riyadh عن افتتاح LAKUM Artspace في ديسمبر 2021، وهي مساحة فنية جديدة مثيرة في الرياض.'
        ]
    ];
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($arabicTranslations as $press_id => $translations) {
        // Check if Arabic translations already exist
        $check_query = "SELECT title_ar FROM press WHERE id = ? AND title_ar IS NOT NULL AND title_ar != ''";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('i', $press_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $skipped++;
            continue;
        }
        
        // UPDATE press table with REAL Arabic translations
        $update_query = "
            UPDATE press 
            SET title_ar = ?, content_ar = ?, excerpt_ar = ?
            WHERE id = ?
        ";
        
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $title_ar = $translations['title'];
        $content_ar = $translations['content'];
        $excerpt_ar = $translations['excerpt'];
        
        $update_stmt->bind_param('sssi', $title_ar, $content_ar, $excerpt_ar, $press_id);
        
        if ($update_stmt->execute()) {
            $updated++;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Press Arabic translations populated successfully',
        'updated' => $updated,
        'skipped' => $skipped,
        'note' => 'Arabic translations have been added to the database. Press cards will now display Arabic content when ?lang=ar'
    ]);
    
} catch (Exception $e) {
    error_log('Populate Press Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
