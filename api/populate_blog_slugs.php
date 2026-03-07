<?php
/**
 * LAKUM Artspace - Populate Blog Slugs
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Get all blogs without slugs
    $query = "SELECT id, title_en, title FROM blogs WHERE slug IS NULL OR slug = ''";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $updated = 0;
    $blogs = [];
    
    while ($row = $result->fetch_assoc()) {
        // Generate slug from title_en or title
        $title = $row['title_en'] ?: $row['title'];
        
        // Normalize slug
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        if (empty($slug)) {
            $slug = 'blog-' . $row['id'];
        }
        
        // Update blog with slug
        $updateQuery = "UPDATE blogs SET slug = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('si', $slug, $row['id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $blogs[] = [
            'id' => $row['id'],
            'title' => $title,
            'slug' => $slug
        ];
        
        $updated++;
        $stmt->close();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog slugs populated successfully',
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
