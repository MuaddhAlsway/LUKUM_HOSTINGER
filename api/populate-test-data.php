<?php
/**
 * Populate Test Data for Pricing and Legal Pages
 * This script ensures we have 6+ pricing items and Arabic legal pages
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
    $conn->set_charset('utf8mb4');
    
    // Test pricing data - 6 items
    $pricingData = [
        [
            'name_en' => 'Hall 1 - Full Day',
            'name_ar' => 'القاعة 1 - يوم كامل',
            'description_en' => 'Spacious gallery ideal for large-scale installations or receptions',
            'description_ar' => 'معرض واسع مثالي للتثبيتات واسعة النطاق أو الاستقبالات',
            'price' => 5000,
            'price_unit' => 'SAR/day',
            'price_unit_ar' => 'ريال سعودي / يوم',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 1
        ],
        [
            'name_en' => 'Hall 1 - Hourly',
            'name_ar' => 'القاعة 1 - بالساعة',
            'description_en' => 'Hourly rental for Hall 1',
            'description_ar' => 'إيجار بالساعة للقاعة 1',
            'price' => 500,
            'price_unit' => 'SAR/hour',
            'price_unit_ar' => 'ريال سعودي / ساعة',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 2
        ],
        [
            'name_en' => 'Hall 2 - Full Day',
            'name_ar' => 'القاعة 2 - يوم كامل',
            'description_en' => 'Intimate showcase space perfect for workshops and panel discussions',
            'description_ar' => 'مساحة عرض حميمية مثالية للورش والندوات',
            'price' => 3000,
            'price_unit' => 'SAR/day',
            'price_unit_ar' => 'ريال سعودي / يوم',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 3
        ],
        [
            'name_en' => 'Hall 2 - Hourly',
            'name_ar' => 'القاعة 2 - بالساعة',
            'description_en' => 'Hourly rental for Hall 2',
            'description_ar' => 'إيجار بالساعة للقاعة 2',
            'price' => 300,
            'price_unit' => 'SAR/hour',
            'price_unit_ar' => 'ريال سعودي / ساعة',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 4
        ],
        [
            'name_en' => 'Mezzanine Floor',
            'name_ar' => 'طابق الميزانين',
            'description_en' => 'Café, Library, and Shop area with public access',
            'description_ar' => 'منطقة المقهى والمكتبة والمتجر مع الوصول العام',
            'price' => 2000,
            'price_unit' => 'SAR/day',
            'price_unit_ar' => 'ريال سعودي / يوم',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 5
        ],
        [
            'name_en' => 'Meeting Room',
            'name_ar' => 'غرفة الاجتماعات',
            'description_en' => 'Compact meeting space for small groups and discussions',
            'description_ar' => 'مساحة اجتماعات صغيرة للمجموعات الصغيرة والنقاشات',
            'price' => 1000,
            'price_unit' => 'SAR/day',
            'price_unit_ar' => 'ريال سعودي / يوم',
            'vat_note' => '*(excluding VAT)',
            'vat_note_ar' => '*(غير شامل الضريبة)',
            'display_order' => 6
        ]
    ];
    
    $updated = 0;
    $errors = [];
    
    // Insert or update pricing items
    foreach ($pricingData as $item) {
        $query = "INSERT INTO pricing (name_en, name_ar, description_en, description_ar, price, price_unit, price_unit_ar, vat_note, vat_note_ar, display_order, is_active, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE 
                  name_en = ?, name_ar = ?, description_en = ?, description_ar = ?, price = ?, price_unit = ?, price_unit_ar = ?, vat_note = ?, vat_note_ar = ?, display_order = ?, updated_at = NOW()";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $errors[] = "Prepare failed for: " . $item['name_en'];
            continue;
        }
        
        $stmt->bind_param(
            'ssssiisssissssiissssi',
            $item['name_en'], $item['name_ar'], $item['description_en'], $item['description_ar'],
            $item['price'], $item['price_unit'], $item['price_unit_ar'], $item['vat_note'], $item['vat_note_ar'], $item['display_order'],
            $item['name_en'], $item['name_ar'], $item['description_en'], $item['description_ar'],
            $item['price'], $item['price_unit'], $item['price_unit_ar'], $item['vat_note'], $item['vat_note_ar'], $item['display_order']
        );
        
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for: " . $item['name_en'] . " - " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Populate legal pages with Arabic content
    $legalPages = [
        [
            'page_key' => 'terms',
            'language' => 'en',
            'title' => 'Terms & Conditions',
            'content' => '<h2>Lakum Artspace Terms of Use</h2><p>By accessing and using the LAKUM Artspace website and services, you accept and agree to be bound by these Terms and Conditions.</p>'
        ],
        [
            'page_key' => 'terms',
            'language' => 'ar',
            'title' => 'شروط وأحكام الاستخدام',
            'content' => '<h2>شروط استخدام لكم آرت سبيس</h2><p>بالوصول واستخدام موقع ويب لكم آرت سبيس والخدمات، فإنك توافق وتوافق على الالتزام بشروط وأحكام الاستخدام هذه.</p>'
        ],
        [
            'page_key' => 'privacy',
            'language' => 'en',
            'title' => 'Privacy Policy',
            'content' => '<h2>Privacy Policy</h2><p>LAKUM Artspace is committed to protecting your privacy.</p>'
        ],
        [
            'page_key' => 'privacy',
            'language' => 'ar',
            'title' => 'سياسة الخصوصية',
            'content' => '<h2>سياسة الخصوصية</h2><p>لكم آرت سبيس ملتزمة بحماية خصوصيتك.</p>'
        ]
    ];
    
    foreach ($legalPages as $page) {
        $query = "INSERT INTO legal_page_translations (page_key, language, title, content, created_at, last_updated) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $errors[] = "Prepare failed for legal page: " . $page['page_key'] . " (" . $page['language'] . ")";
            continue;
        }
        
        $stmt->bind_param('sssss', $page['page_key'], $page['language'], $page['title'], $page['content'], $page['title'], $page['content']);
        
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for legal page: " . $page['page_key'] . " - " . $stmt->error;
        }
        $stmt->close();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Test data populated successfully',
        'updated' => $updated,
        'errors' => $errors,
        'note' => 'Database now has 6 pricing items and Arabic legal pages'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Populate Test Data Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
