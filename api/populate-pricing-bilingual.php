<?php
/**
 * Populate bilingual pricing fields
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Bilingual pricing data
    $pricingData = [
        1 => [
            'name_en' => 'Hall 1',
            'name_ar' => 'القاعة 1'
        ],
        2 => [
            'name_en' => 'Hall 2',
            'name_ar' => 'القاعة 2'
        ],
        3 => [
            'name_en' => 'Hourly Rate',
            'name_ar' => 'السعر بالساعة'
        ],
        4 => [
            'name_en' => 'Set up/Dismantle Day',
            'name_ar' => 'يوم الإعداد/الفك'
        ],
        5 => [
            'name_en' => 'Café',
            'name_ar' => 'المقهى'
        ],
        6 => [
            'name_en' => 'Meeting Room',
            'name_ar' => 'غرفة الاجتماعات'
        ]
    ];
    
    $updated = 0;
    foreach ($pricingData as $id => $data) {
        $query = 'UPDATE pricing SET name_en = ?, name_ar = ? WHERE id = ?';
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('ssi', $data['name_en'], $data['name_ar'], $id);
        
        if ($stmt->execute()) {
            $updated++;
        }
        $stmt->close();
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => "Updated $updated pricing items with bilingual names",
        'updated' => $updated
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
