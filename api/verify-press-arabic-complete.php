<?php
/**
 * LAKUM Artspace - Complete Press Arabic Verification
 * Comprehensive verification that everything is working correctly
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    $verification = [
        'database_connected' => true,
        'checks' => []
    ];
    
    // CHECK 1: Count total press items
    $result = $conn->query("SELECT COUNT(*) as count FROM press");
    $row = $result->fetch_assoc();
    $verification['checks'][] = [
        'name' => 'Total Press Items',
        'value' => $row['count'],
        'status' => $row['count'] > 0 ? 'OK' : 'FAIL'
    ];
    
    // CHECK 2: Count published press items
    $result = $conn->query("SELECT COUNT(*) as count FROM press WHERE is_published = 1");
    $row = $result->fetch_assoc();
    $verification['checks'][] = [
        'name' => 'Published Press Items',
        'value' => $row['count'],
        'status' => $row['count'] > 0 ? 'OK' : 'FAIL'
    ];
    
    // CHECK 3: Count items with Arabic titles
    $result = $conn->query("SELECT COUNT(*) as count FROM press WHERE is_published = 1 AND title_ar IS NOT NULL AND title_ar != ''");
    $row = $result->fetch_assoc();
    $arabicTitleCount = $row['count'];
    $verification['checks'][] = [
        'name' => 'Press Items with Arabic Titles',
        'value' => $arabicTitleCount,
        'status' => $arabicTitleCount > 0 ? 'OK' : 'NEEDS_POPULATE'
    ];
    
    // CHECK 4: Count items with Arabic excerpts
    $result = $conn->query("SELECT COUNT(*) as count FROM press WHERE is_published = 1 AND excerpt_ar IS NOT NULL AND excerpt_ar != ''");
    $row = $result->fetch_assoc();
    $arabicExcerptCount = $row['count'];
    $verification['checks'][] = [
        'name' => 'Press Items with Arabic Excerpts',
        'value' => $arabicExcerptCount,
        'status' => $arabicExcerptCount > 0 ? 'OK' : 'NEEDS_POPULATE'
    ];
    
    // CHECK 5: Count items with Arabic content
    $result = $conn->query("SELECT COUNT(*) as count FROM press WHERE is_published = 1 AND content_ar IS NOT NULL AND content_ar != ''");
    $row = $result->fetch_assoc();
    $arabicContentCount = $row['count'];
    $verification['checks'][] = [
        'name' => 'Press Items with Arabic Content',
        'value' => $arabicContentCount,
        'status' => $arabicContentCount > 0 ? 'OK' : 'NEEDS_POPULATE'
    ];
    
    // CHECK 6: Sample press item with Arabic
    $result = $conn->query("
        SELECT id, title_en, title_ar, excerpt_en, excerpt_ar 
        FROM press 
        WHERE is_published = 1 AND title_ar IS NOT NULL AND title_ar != '' 
        LIMIT 1
    ");
    
    if ($result && $result->num_rows > 0) {
        $sample = $result->fetch_assoc();
        $verification['checks'][] = [
            'name' => 'Sample Arabic Press Item',
            'value' => [
                'id' => $sample['id'],
                'title_en' => $sample['title_en'],
                'title_ar' => $sample['title_ar'],
                'has_excerpt_ar' => !empty($sample['excerpt_ar'])
            ],
            'status' => 'OK'
        ];
    } else {
        $verification['checks'][] = [
            'name' => 'Sample Arabic Press Item',
            'value' => 'No Arabic press items found',
            'status' => 'NEEDS_POPULATE'
        ];
    }
    
    // CHECK 7: Test API response for English
    $apiTestEn = @file_get_contents('http://localhost/api/get_press.php?lang=en&limit=1');
    if ($apiTestEn) {
        $apiDataEn = json_decode($apiTestEn, true);
        $verification['checks'][] = [
            'name' => 'API English Response',
            'value' => $apiDataEn['success'] ? 'Working' : 'Error',
            'status' => $apiDataEn['success'] ? 'OK' : 'FAIL'
        ];
    }
    
    // CHECK 8: Test API response for Arabic
    $apiTestAr = @file_get_contents('http://localhost/api/get_press.php?lang=ar&limit=1');
    if ($apiTestAr) {
        $apiDataAr = json_decode($apiTestAr, true);
        $verification['checks'][] = [
            'name' => 'API Arabic Response',
            'value' => $apiDataAr['success'] ? 'Working' : 'Error',
            'status' => $apiDataAr['success'] ? 'OK' : 'FAIL'
        ];
    }
    
    // Overall status
    $needsPopulate = $arabicTitleCount === 0 || $arabicExcerptCount === 0;
    $verification['overall_status'] = $needsPopulate ? 'NEEDS_POPULATE' : 'READY';
    $verification['next_steps'] = $needsPopulate 
        ? 'Run: https://lakumartspace.com/api/populate-press-arabic-smart.php'
        : 'Clear browser cache and hard refresh to see Arabic content';
    
    http_response_code(200);
    echo json_encode($verification, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    error_log('Verify Press Arabic Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
