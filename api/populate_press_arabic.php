<?php
/**
 * LAKUM Artspace - Populate Press Arabic Translations
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Arabic translations for press items
    $translations = [
        12 => [
            'title_ar' => 'معرض المصور فيصل بن زراع - رسالة حب للمملكة',
            'excerpt_ar' => 'تغطي جريدة عرب نيوز معرض المصور فيصل بن زراع في لاكوم آرت سبيس، يحتفي بالمملكة من خلال التصوير الفوتوغرافي.',
            'content_ar' => 'تغطي جريدة عرب نيوز معرض المصور فيصل بن زراع في لاكوم آرت سبيس، يحتفي بالمملكة من خلال التصوير الفوتوغرافي.'
        ],
        13 => [
            'title_ar' => 'جوائز مركز منشئي المحتوى على تيك توك تكرم رائدات الأعمال في الرياض',
            'excerpt_ar' => 'تغطي زاوية جوائز مركز منشئي المحتوى على تيك توك الذي يكرم رائدات الأعمال في الرياض في لاكوم آرت سبيس',
            'content_ar' => 'تغطي زاوية جوائز مركز منشئي المحتوى على تيك توك الذي يكرم رائدات الأعمال في الرياض في لاكوم آرت سبيس'
        ],
        14 => [
            'title_ar' => 'استكشف ورشة عمل تفاعلية من قبل The Culture Mocktail في لاكوم',
            'excerpt_ar' => 'تغطي مجلة Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في لاكوم آرت سبيس.',
            'content_ar' => 'تغطي مجلة Time Out Riyadh ورشة عمل تفاعلية تستضيفها The Culture Mocktail في لاكوم آرت سبيس.'
        ],
        15 => [
            'title_ar' => 'عرض منبثق قبل رمضان في لاكوم آرت سبيس بالرياض',
            'excerpt_ar' => 'تسلط دليل المملكة الضوء على معرض منبثق خاص قبل رمضان في لاكوم آرت سبيس.',
            'content_ar' => 'تسلط دليل المملكة الضوء على معرض منبثق خاص قبل رمضان في لاكوم آرت سبيس.'
        ],
        16 => [
            'title_ar' => 'سناب تُظهر قوة الواقع المعزز في تحويل الموضة والجمال في المملكة',
            'excerpt_ar' => 'تغطي جريدة عرب نيوز عرض الواقع المعزز من سناب في لاكوم آرت سبيس، مما يوضح تأثير الواقع المعزز على الموضة والجمال.',
            'content_ar' => 'تغطي جريدة عرب نيوز عرض الواقع المعزز من سناب في لاكوم آرت سبيس، مما يوضح تأثير الواقع المعزز على الموضة والجمال.'
        ],
        17 => [
            'title_ar' => 'أعمال فنية لـ 20 امرأة سعودية معروضة بمناسبة اليوم العالمي للمرأة',
            'excerpt_ar' => 'تغطي جريدة الجزيرة معرضاً في لاكوم آرت سبيس يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة.',
            'content_ar' => 'تغطي جريدة الجزيرة معرضاً في لاكوم آرت سبيس يضم أعمالاً فنية لـ 20 فنانة سعودية بمناسبة اليوم العالمي للمرأة.'
        ],
        18 => [
            'title_ar' => 'أحمد ماطر يفتتح لاكوم آرت سبيس بمعرض "التشخيص: 1979-2019"',
            'excerpt_ar' => 'تعلن مجلة GDN Life عن افتتاح لاكوم آرت سبيس مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019".',
            'content_ar' => 'تعلن مجلة GDN Life عن افتتاح لاكوم آرت سبيس مع معرض الفنان الشهير أحمد ماطر "التشخيص: 1979-2019".'
        ],
        19 => [
            'title_ar' => 'لاكوم آرت سبيس ستفتتح في ديسمبر',
            'excerpt_ar' => 'تعلن مجلة Time Out Riyadh عن افتتاح لاكوم آرت سبيس في ديسمبر 2021.',
            'content_ar' => 'تعلن مجلة Time Out Riyadh عن افتتاح لاكوم آرت سبيس في ديسمبر 2021.'
        ]
    ];
    
    $updated = 0;
    
    foreach ($translations as $id => $trans) {
        $query = "UPDATE press SET title_ar = ?, excerpt_ar = ?, content_ar = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('sssi', $trans['title_ar'], $trans['excerpt_ar'], $trans['content_ar'], $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $updated++;
        $stmt->close();
    }
    
    // Verify
    $verifyQuery = "SELECT id, title_en, title_ar FROM press ORDER BY id";
    $verifyResult = $conn->query($verifyQuery);
    
    $press = [];
    while ($row = $verifyResult->fetch_assoc()) {
        $press[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Press Arabic translations populated successfully',
        'updated' => $updated,
        'press' => $press
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
