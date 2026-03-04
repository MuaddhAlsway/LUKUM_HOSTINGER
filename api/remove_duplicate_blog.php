<?php
/**
 * Remove duplicate "Materials and technique" blog
 */

header('Content-Type: application/json');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Find all blogs with "Materials and technique" title
    $query = 'SELECT id, title, created_at FROM blogs WHERE title LIKE "%Materials and technique%" ORDER BY id';
    $result = $db->query($query);
    
    $duplicates = [];
    while ($row = $result->fetch_assoc()) {
        $duplicates[] = $row;
    }
    
    if (count($duplicates) <= 1) {
        echo json_encode([
            'success' => false,
            'message' => 'No duplicates found. Found ' . count($duplicates) . ' blog(s) with "Materials and technique" title',
            'blogs' => $duplicates
        ]);
        exit;
    }
    
    // Keep the first one, delete the rest
    $keep_id = $duplicates[0]['id'];
    $delete_ids = array_slice(array_column($duplicates, 'id'), 1);
    
    $deleted_count = 0;
    foreach ($delete_ids as $id) {
        $delete_query = 'DELETE FROM blogs WHERE id = ?';
        $stmt = $db->prepare($delete_query);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $deleted_count++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Removed ' . $deleted_count . ' duplicate(s)',
        'kept_id' => $keep_id,
        'deleted_ids' => $delete_ids,
        'all_duplicates' => $duplicates
    ]);
    
} catch (Exception $e) {
    error_log('Remove Duplicate Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


