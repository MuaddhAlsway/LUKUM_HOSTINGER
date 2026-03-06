<?php
/**
 * Fix Blog Slugs - Populate missing slugs in database
 * This script will generate and update slugs for all blogs
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Step 1: Check if slug column exists
    $checkColumn = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_NAME = 'blogs' AND COLUMN_NAME = 'slug'";
    $result = $conn->query($checkColumn);
    
    if ($result->num_rows === 0) {
        // Add slug column if it doesn't exist
        $addColumn = "ALTER TABLE blogs ADD COLUMN slug VARCHAR(255) UNIQUE AFTER title";
        if (!$conn->query($addColumn)) {
            throw new Exception('Failed to add slug column: ' . $conn->error);
        }
    }
    
    // Step 2: Get all blogs without slugs
    $query = "SELECT id, title_en, title FROM blogs WHERE slug IS NULL OR slug = ''";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $updated = 0;
    $blogs = [];
    
    while ($row = $result->fetch_assoc()) {
        $title = $row['title_en'] ?: $row['title'];
        
        // Generate slug from title
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $blogs[] = [
            'id' => $row['id'],
            'title' => $title,
            'slug' => $slug
        ];
        
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
        
        $updated++;
        $stmt->close();
    }
    
    // Step 3: Get all blogs with their slugs
    $allBlogsQuery = "SELECT id, title_en, title, slug FROM blogs ORDER BY id DESC";
    $allBlogsResult = $conn->query($allBlogsQuery);
    
    $allBlogs = [];
    while ($row = $allBlogsResult->fetch_assoc()) {
        $allBlogs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Updated $updated blogs with slugs",
        'updated_count' => $updated,
        'updated_blogs' => $blogs,
        'all_blogs' => $allBlogs
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
