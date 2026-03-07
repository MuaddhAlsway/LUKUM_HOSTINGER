<?php
/**
 * Run SQL to update all blog slugs
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Update slugs
    $updates = [
        [14, 'lakum-impact-on-local-artists'],
        [16, 'behind-the-scenes-curating-an-exhibition'],
        [17, 'the-art-of-contemporary-expression']
    ];
    
    $updated = 0;
    
    foreach ($updates as [$id, $slug]) {
        $query = "UPDATE blogs SET slug = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('si', $slug, $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $updated++;
        $stmt->close();
    }
    
    // Verify
    $verifyQuery = "SELECT id, title_en, slug FROM blogs ORDER BY id";
    $verifyResult = $conn->query($verifyQuery);
    
    $blogs = [];
    while ($row = $verifyResult->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'All blog slugs updated successfully',
        'updated' => $updated,
        'blogs' => $blogs
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
