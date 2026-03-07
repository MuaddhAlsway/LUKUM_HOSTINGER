<?php
/**
 * QUICK FIX - Direct Database Fix
 * Creates pricing table if missing and populates with data
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    $log = [];
    
    // STEP 1: Check if pricing table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'pricing'");
    if ($tableCheck->num_rows == 0) {
        $log[] = "Creating pricing table...";
        
        $createTable = "CREATE TABLE IF NOT EXISTS `pricing` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255),
            `name_en` VARCHAR(255) NOT NULL,
            `name_ar` VARCHAR(255),
            `description_en` LONGTEXT,
            `description_ar` LONGTEXT,
            `price` INT,
            `price_unit` VARCHAR(50),
            `price_unit_ar` VARCHAR(50),
            `price_sec` VARCHAR(255),
            `vat_note` VARCHAR(255),
            `vat_note_ar` VARCHAR(255),
            `currency_image` VARCHAR(255),
            `content` LONGTEXT,
            `display_order` INT DEFAULT 0,
            `is_active` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($createTable)) {
            $log[] = "✅ Pricing table created";
        } else {
            $log[] = "❌ Failed to create pricing table: " . $conn->error;
        }
    } else {
        $log[] = "✅ Pricing table exists";
    }
    
    // STEP 2: Clear existing pricing data
    $conn->query("DELETE FROM `pricing`");
    $log[] = "✅ Cleared existing pricing data";
    
    // STEP 3: Insert 6 pricing items
    $pricingData = [
        ['Hall 1 - Full Day', 'القاعة 1 - يوم كامل', 'Spacious gallery ideal for large-scale installations', 'معرض واسع مثالي للتثبيتات', 5000, 'SAR/day', 'ريال سعودي / يوم', '*(excluding VAT)', '*(غير شامل الضريبة)', 1],
        ['Hall 1 - Hourly', 'القاعة 1 - بالساعة', 'Hourly rental for Hall 1', 'إيجار بالساعة للقاعة 1', 500, 'SAR/hour', 'ريال سعودي / ساعة', '*(excluding VAT)', '*(غير شامل الضريبة)', 2],
        ['Hall 2 - Full Day', 'القاعة 2 - يوم كامل', 'Intimate showcase space perfect for workshops', 'مساحة عرض حميمية مثالية للورش', 3000, 'SAR/day', 'ريال سعودي / يوم', '*(excluding VAT)', '*(غير شامل الضريبة)', 3],
        ['Hall 2 - Hourly', 'القاعة 2 - بالساعة', 'Hourly rental for Hall 2', 'إيجار بالساعة للقاعة 2', 300, 'SAR/hour', 'ريال سعودي / ساعة', '*(excluding VAT)', '*(غير شامل الضريبة)', 4],
        ['Mezzanine Floor', 'طابق الميزانين', 'Café, Library, and Shop area', 'منطقة المقهى والمكتبة والمتجر', 2000, 'SAR/day', 'ريال سعودي / يوم', '*(excluding VAT)', '*(غير شامل الضريبة)', 5],
        ['Meeting Room', 'غرفة الاجتماعات', 'Compact meeting space for small groups', 'مساحة اجتماعات صغيرة', 1000, 'SAR/day', 'ريال سعودي / يوم', '*(excluding VAT)', '*(غير شامل الضريبة)', 6]
    ];
    
    $inserted = 0;
    foreach ($pricingData as $item) {
        $query = "INSERT INTO `pricing` (name_en, name_ar, description_en, description_ar, price, price_unit, price_unit_ar, vat_note, vat_note_ar, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssssiisssi', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], $item[8], $item[9]);
            if ($stmt->execute()) {
                $inserted++;
            }
            $stmt->close();
        }
    }
    $log[] = "✅ Inserted $inserted pricing items";
    
    // STEP 4: Check legal_page_translations table
    $legalCheck = $conn->query("SHOW TABLES LIKE 'legal_page_translations'");
    if ($legalCheck->num_rows == 0) {
        $log[] = "Creating legal_page_translations table...";
        
        $createLegal = "CREATE TABLE IF NOT EXISTS `legal_page_translations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `page_key` VARCHAR(50) NOT NULL,
            `language` VARCHAR(10) NOT NULL,
            `title` VARCHAR(255),
            `content` LONGTEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_page_lang` (`page_key`, `language`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($createLegal)) {
            $log[] = "✅ Legal pages table created";
        } else {
            $log[] = "❌ Failed to create legal pages table: " . $conn->error;
        }
    } else {
        $log[] = "✅ Legal pages table exists";
    }
    
    // STEP 5: Insert legal pages
    $legalPages = [
        ['terms', 'en', 'Terms & Conditions', '<h2>Terms & Conditions</h2><p>By accessing and using LAKUM Artspace, you agree to these terms.</p>'],
        ['terms', 'ar', 'شروط وأحكام الاستخدام', '<h2>شروط وأحكام الاستخدام</h2><p>بالوصول واستخدام لكم آرت سبيس، توافق على هذه الشروط.</p>'],
        ['privacy', 'en', 'Privacy Policy', '<h2>Privacy Policy</h2><p>LAKUM Artspace is committed to protecting your privacy.</p>'],
        ['privacy', 'ar', 'سياسة الخصوصية', '<h2>سياسة الخصوصية</h2><p>لكم آرت سبيس ملتزمة بحماية خصوصيتك.</p>']
    ];
    
    $legalInserted = 0;
    foreach ($legalPages as $page) {
        $query = "INSERT INTO `legal_page_translations` (page_key, language, title, content, created_at, last_updated) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssssss', $page[0], $page[1], $page[2], $page[3], $page[2], $page[3]);
            if ($stmt->execute()) {
                $legalInserted++;
            }
            $stmt->close();
        }
    }
    $log[] = "✅ Inserted $legalInserted legal pages";
    
    // STEP 6: Verify
    $pricingCount = $conn->query("SELECT COUNT(*) as count FROM `pricing` WHERE is_active = 1")->fetch_assoc()['count'];
    $legalCount = $conn->query("SELECT COUNT(*) as count FROM `legal_page_translations`")->fetch_assoc()['count'];
    
    $log[] = "✅ Verification: $pricingCount active pricing items, $legalCount legal pages";
    
    echo json_encode([
        'success' => true,
        'message' => 'Database fixed successfully',
        'log' => $log,
        'summary' => [
            'pricing_items' => $pricingCount,
            'legal_pages' => $legalCount
        ],
        'next_steps' => [
            '1. Clear browser cache: Ctrl+Shift+Delete',
            '2. Hard refresh: Ctrl+Shift+R',
            '3. Visit spaces.php - should show 6 pricing items',
            '4. Visit terms.php?lang=ar - should show Arabic terms'
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getTraceAsString()
    ]);
}
?>
