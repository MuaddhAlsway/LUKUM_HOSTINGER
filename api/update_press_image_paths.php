<?php
/**
 * LAKUM Artspace - Update Press Image Paths
 * Updates press table to use correct image paths from public press page
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Image path mappings from public press page
    $updates = [
        1 => 'uploads/uploads/press/press_1_1765953905.jpg',
        2 => 'uploads/uploads/press/press_2_1765953905.jpg',
        3 => 'uploads/uploads/press/press_3_1765953905.svg',
        4 => 'uploads/uploads/press/press_5_1765953905.png',
        5 => 'uploads/uploads/press/press_5_1765953905.png',
        6 => 'uploads/uploads/press/press_6_1765953905.svg',
        7 => 'uploads/uploads/press/press_7_1765953905.png',
        8 => 'uploads/uploads/press/press_8_1765953905.jpg'
    ];
    
    $updated_count = 0;
    
    foreach ($updates as $id => $image_path) {
        $query = 'UPDATE press SET cover_image = ? WHERE id = ?';
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('si', $image_path, $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed for ID ' . $id . ': ' . $stmt->error);
        }
        
        $updated_count++;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Press image paths updated successfully',
        'updated_count' => $updated_count
    ]);
    
} catch (Exception $e) {
    error_log('Update Press Image Paths Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
