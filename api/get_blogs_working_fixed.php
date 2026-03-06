<?php
/**
 * LAKUM Artspace - Get Blogs API (Fixed - Fallback to Title Search)
 * If slug not found, searches by title
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
    
    // Check if fetching a single blog by slug, ID, or title
    if (isset($_GET['slug']) || isset($_GET['id']) || isset($_GET['title'])) {
        $blog_id = null;
        $blog_slug = null;
        $blog_title = null;
        
        // Determine which parameter to use (slug > id > title)
        if (isset($_GET['slug'])) {
            $blog_slug = $_GET['slug'];
        } elseif (isset($_GET['id'])) {
            $blog_id = (int)$_GET['id'];
        } elseif (isset($_GET['title'])) {
            $blog_title = $_GET['title'];
        }
        
        // Query from blogs table with bilingual columns
        $query = "
            SELECT 
                b.id,
                b.author,
                b.category,
                b.cover_image,
                b.created_at,
                b.slug,
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
            WHERE ";
        
        if ($blog_slug !== null) {
            // Try slug first
            $query .= "b.slug = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception('Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param('ssss', $lang, $lang, $lang, $blog_slug);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // If slug not found, try searching by title containing the slug
            if ($result->num_rows === 0) {
                $stmt->close();
                
                // Convert slug back to title-like search
                $searchTerm = str_replace('-', '%', $blog_slug);
                $query = "
                    SELECT 
                        b.id,
                        b.author,
                        b.category,
                        b.cover_image,
                        b.created_at,
                        b.slug,
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
                    WHERE b.title_en LIKE ? OR b.title LIKE ? OR b.slug LIKE ?
                    LIMIT 1
                ";
                
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    throw new Exception('Query preparation failed: ' . $conn->error);
                }
                
                $searchPattern = '%' . $searchTerm . '%';
                $stmt->bind_param('sssss', $lang, $lang, $lang, $searchPattern, $searchPattern, $searchPattern);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        } elseif ($blog_id !== null) {
            $query .= "b.id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception('Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param('sssi', $lang, $lang, $lang, $blog_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query .= "b.title_en = ? OR b.title = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception('Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param('sssss', $lang, $lang, $lang, $blog_title, $blog_title);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
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
            $galleryStmt->bind_param('i', $blog['id']);
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
    
    // If no specific blog requested, return all blogs
    $query = "
        SELECT 
            b.id,
            b.author,
            b.category,
            b.cover_image,
            b.created_at,
            b.slug,
            CASE 
                WHEN ? = 'ar' AND b.title_ar IS NOT NULL AND b.title_ar != '' THEN b.title_ar
                ELSE COALESCE(b.title_en, b.title)
            END as title,
            CASE 
                WHEN ? = 'ar' AND b.excerpt_ar IS NOT NULL AND b.excerpt_ar != '' THEN b.excerpt_ar
                ELSE COALESCE(b.excerpt_en, b.excerpt)
            END as excerpt,
            b.title_en,
            b.title_ar
        FROM blogs b
        ORDER BY b.created_at DESC
        LIMIT 100
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ss', $lang, $lang);
    $stmt->execute();
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
        'language' => $lang
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
