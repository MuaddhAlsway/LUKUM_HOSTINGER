<?php
/**
 * Test Blog Slug Lookup
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Test slug from URL
    $test_slug = $_GET['slug'] ?? 'behind-the-scenes-curation';
    $lang = $_GET['lang'] ?? 'en';
    
    echo json_encode([
        'test_slug' => $test_slug,
        'lang' => $lang,
        'message' => 'Testing slug lookup...'
    ]);
    
    // Simple query to check if slug exists
    $query = "SELECT id, title_en, title_ar, slug FROM blogs WHERE slug = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param('s', $test_slug);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Query execution failed: ' . $stmt->error]);
        exit;
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Try to find similar slugs
        $similar_query = "SELECT id, title_en, slug FROM blogs LIMIT 5";
        $similar_result = $conn->query($similar_query);
        $similar_blogs = [];
        while ($row = $similar_result->fetch_assoc()) {
            $similar_blogs[] = $row;
        }
        
        echo json_encode([
            'success' => false,
            'error' => 'Blog with slug "' . $test_slug . '" not found',
            'similar_blogs' => $similar_blogs,
            'suggestion' => 'Check if the slug exists in the database'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        $blog = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'blog' => $blog,
            'message' => 'Blog found!'
        ], JSON_UNESCAPED_UNICODE);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
