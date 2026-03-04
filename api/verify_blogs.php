<?php
/**
 * Verify all blogs in database
 */

header('Content-Type: application/json');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Get all blogs
    $query = 'SELECT id, title, category FROM blogs ORDER BY id';
    $result = $db->query($query);
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    // Check for duplicates
    $titles = array_column($blogs, 'title');
    $duplicates = array_diff_assoc($titles, array_unique($titles));
    
    echo json_encode([
        'success' => true,
        'total_blogs' => count($blogs),
        'blogs' => $blogs,
        'duplicates_found' => count($duplicates) > 0 ? $duplicates : 'None',
        'message' => count($duplicates) > 0 ? 'Duplicates found!' : 'No duplicates found'
    ]);
    
} catch (Exception $e) {
    error_log('Verify Blogs Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

