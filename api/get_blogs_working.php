<?php
/**
 * LAKUM Artspace - Get Blogs API (Working Version - Fixed for Bilingual Columns)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    $lang = $_GET['lang'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    // Check if fetching a single blog by ID
    if (isset($_GET['id'])) {
        $blog_id = (int)$_GET['id'];
        
        // Query from blogs table with bilingual columns
        $query = "
            SELECT 
                b.id,
                b.author,
                b.category,
                b.cover_image,
                b.created_at,
                CASE 
                    WHEN ? = 'ar' AND b.title_ar IS NOT NULL AND b.title_ar != '' THEN b.title_ar
                    ELSE COALESCE(b.title_en, b.title)
                END as title,
                CASE 
                    WHEN ? = 'ar' AND b.excerpt_ar IS NOT NULL AND b.excerpt_ar != '' THEN b.excerpt_ar
                    ELSE COALESCE(b.excerpt_en, b.excerpt)
                END as excerpt,
                CASE 
                    WHEN ? = 'ar' AND b.content_ar IS NOT NULL AND b.content_ar != '' THEN b.content_ar
                    ELSE COALESCE(b.content_en, b.content)
                END as content,
                b.title_en,
                b.excerpt_en,
                b.content_en,
                b.title_ar,
                b.excerpt_ar,
                b.content_ar
            FROM blogs b
            WHERE b.id = ?
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param('sssi', $lang, $lang, $lang, $blog_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Blog not found']);
            exit;
        }
        
        $blog = $result->fetch_assoc();
        $stmt->close();
        
        // Fetch gallery images for this blog
        $galleryQuery = "SELECT * FROM blog_gallery WHERE blog_id = ? ORDER BY display_order";
        $galleryStmt = $conn->prepare($galleryQuery);
        if ($galleryStmt) {
            $galleryStmt->bind_param('i', $blog_id);
            $galleryStmt->execute();
            $galleryResult = $galleryStmt->get_result();
            
            $gallery = [];
            while ($row = $galleryResult->fetch_assoc()) {
                $gallery[] = $row;
            }
            $galleryStmt->close();
        } else {
            $gallery = [];
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $blog,
            'gallery' => $gallery,
            'language' => $lang
        ]);
        exit;
    }
    
    // Fetch all blogs
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Query from blogs table with bilingual columns
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.created_at,
            CASE 
                WHEN ? = 'ar' AND b.title_ar IS NOT NULL AND b.title_ar != '' THEN b.title_ar
                ELSE COALESCE(b.title_en, b.title)
            END as title,
            CASE 
                WHEN ? = 'ar' AND b.excerpt_ar IS NOT NULL AND b.excerpt_ar != '' THEN b.excerpt_ar
                ELSE COALESCE(b.excerpt_en, b.excerpt)
            END as excerpt,
            CASE 
                WHEN ? = 'ar' AND b.content_ar IS NOT NULL AND b.content_ar != '' THEN b.content_ar
                ELSE COALESCE(b.content_en, b.content)
            END as content
        FROM blogs b
        ORDER BY b.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . $conn->error);
    }
    
    $stmt->bind_param('sssii', $lang, $lang, $lang, $limit, $offset);
    
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $blogs = [];
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'language' => $lang,
        'count' => count($blogs)
    ]);
    
} catch (Exception $e) {
    error_log('Get Blogs API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
