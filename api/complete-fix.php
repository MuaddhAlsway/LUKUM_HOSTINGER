<?php
/**
 * COMPLETE FIX - Pricing + Legal Pages + Rollback
 * This script fixes everything in one go:
 * 1. Rolls back pricing database
 * 2. Populates missing pricing items
 * 3. Populates missing legal pages with Arabic
 * 4. Verifies everything is working
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
    
    $results = [
        'rollback' => [],
        'pricing_populated' => [],
        'legal_pages_populated' => [],
        'verification' => []
    ];
    
    // ============================================
    // STEP 1: ROLLBACK - Restore price_unit_ar
    // ============================================
    $rollbackQueries = [
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / يوم' WHERE `price_unit` = 'SAR/day' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي / ساعة' WHERE `price_unit` = 'SAR/hour' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `price_unit_ar` = 'ريال سعودي' WHERE `price_unit` = 'SAR' AND `price_unit_ar` IS NULL",
        "UPDATE `pricing` SET `is_active` = 1 WHERE `is_active` = 0"
    ];
    
    foreach ($rollbackQueries as $query) {
        if ($conn->query($query)) {
            $results['rollback'][] = [
                'query' => substr($query, 0, 60) . '...',
                'affected_rows' => $conn->affected_rows,
                'status' => 'success'
            ];
        } else {
            $results['rollback'][] = [
                'query' => substr($query, 0, 60) . '...',
                'error' => $conn->error,
                'status' => 'error'
            ];
        }
    }
    
    // ============================================
    // STEP 2: POPULATE PRICING ITEMS
    // ============================================
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
    
    $pricingCount = 0;
    foreach ($pricingData as $item) {
        $query = "INSERT INTO pricing (name_en, name_ar, description_en, description_ar, price, price_unit, price_unit_ar, vat_note, vat_note_ar, display_order, is_active, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE 
                  name_en = ?, name_ar = ?, description_en = ?, description_ar = ?, price = ?, price_unit = ?, price_unit_ar = ?, vat_note = ?, vat_note_ar = ?, display_order = ?, updated_at = NOW()";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param(
                'ssssiisssissssiissssi',
                $item['name_en'], $item['name_ar'], $item['description_en'], $item['description_ar'],
                $item['price'], $item['price_unit'], $item['price_unit_ar'], $item['vat_note'], $item['vat_note_ar'], $item['display_order'],
                $item['name_en'], $item['name_ar'], $item['description_en'], $item['description_ar'],
                $item['price'], $item['price_unit'], $item['price_unit_ar'], $item['vat_note'], $item['vat_note_ar'], $item['display_order']
            );
            
            if ($stmt->execute()) {
                $pricingCount++;
                $results['pricing_populated'][] = [
                    'item' => $item['name_en'],
                    'status' => 'success'
                ];
            } else {
                $results['pricing_populated'][] = [
                    'item' => $item['name_en'],
                    'error' => $stmt->error,
                    'status' => 'error'
                ];
            }
            $stmt->close();
        }
    }
    
    // ============================================
    // STEP 3: POPULATE LEGAL PAGES
    // ============================================
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
    
    $legalCount = 0;
    foreach ($legalPages as $page) {
        $query = "INSERT INTO legal_page_translations (page_key, language, title, content, created_at, last_updated) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sssss', $page['page_key'], $page['language'], $page['title'], $page['content'], $page['title'], $page['content']);
            
            if ($stmt->execute()) {
                $legalCount++;
                $results['legal_pages_populated'][] = [
                    'page' => $page['page_key'] . ' (' . $page['language'] . ')',
                    'status' => 'success'
                ];
            } else {
                $results['legal_pages_populated'][] = [
                    'page' => $page['page_key'] . ' (' . $page['language'] . ')',
                    'error' => $stmt->error,
                    'status' => 'error'
                ];
            }
            $stmt->close();
        }
    }
    
    // ============================================
    // STEP 4: VERIFICATION
    // ============================================
    $verifyQueries = [
        'total_active_pricing' => "SELECT COUNT(*) as count FROM `pricing` WHERE `is_active` = 1",
        'total_legal_pages' => "SELECT COUNT(*) as count FROM `legal_page_translations`",
        'arabic_legal_pages' => "SELECT COUNT(*) as count FROM `legal_page_translations` WHERE `language` = 'ar'"
    ];
    
    foreach ($verifyQueries as $key => $query) {
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $results['verification'][$key] = $row['count'];
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Complete fix executed successfully',
        'summary' => [
            'rollback_queries' => count($results['rollback']),
            'pricing_items_populated' => $pricingCount,
            'legal_pages_populated' => $legalCount,
            'total_active_pricing' => $results['verification']['total_active_pricing'] ?? 0,
            'total_legal_pages' => $results['verification']['total_legal_pages'] ?? 0,
            'arabic_legal_pages' => $results['verification']['arabic_legal_pages'] ?? 0
        ],
        'details' => $results,
        'next_steps' => [
            '1. Clear browser cache (Ctrl+Shift+Delete)',
            '2. Hard refresh (Ctrl+Shift+R)',
            '3. Visit spaces.php - should show 6 pricing items',
            '4. Visit terms.php?lang=ar - should show Arabic terms',
            '5. Visit privacy.php?lang=ar - should show Arabic privacy'
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Complete Fix Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
