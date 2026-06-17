<?php
/**
 * LAKUM Artspace - Get Press API (Fixed - Hybrid Columns)
 * Retrieves press releases from database with bilingual support
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Check if fetching a single press by slug or title
    $filterBySlug = isset($_GET['slug']) || isset($_GET['title']);
    $pressSlug = $_GET['slug'] ?? $_GET['title'] ?? null;
    
    // Query from press table with bilingual columns (hybrid approach)
    if ($filterBySlug && $pressSlug) {
        // Single press release by slug/title
        $query = "
            SELECT 
                p.id,
                p.source,
                p.press_date,
                p.url,
                p.category,
                p.cover_image,
                p.slug,
                p.is_published,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.title_ar, ''), p.title_en, p.title)
                    ELSE COALESCE(p.title_en, p.title)
                END as title,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.content_ar, ''), p.content_en, p.content)
                    ELSE COALESCE(p.content_en, p.content)
                END as content,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.excerpt_ar, ''), p.excerpt_en, p.excerpt)
                    ELSE COALESCE(p.excerpt_en, p.excerpt)
                END as excerpt,
                p.title_en,
                p.excerpt_en,
                p.content_en,
                p.title_ar,
                p.excerpt_ar,
                p.content_ar
            FROM press p
            WHERE (p.slug = ? OR p.title_en = ? OR p.title = ?) AND p.is_published = 1
            LIMIT 1
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param('sssss', $lang, $lang, $lang, $pressSlug, $pressSlug, $pressSlug);
    } else {
        // All press releases
        $query = "
            SELECT 
                p.id,
                p.source,
                p.press_date,
                p.url,
                p.category,
                p.cover_image,
                p.slug,
                p.is_published,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.title_ar, ''), p.title_en, p.title)
                    ELSE COALESCE(p.title_en, p.title)
                END as title,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.content_ar, ''), p.content_en, p.content)
                    ELSE COALESCE(p.content_en, p.content)
                END as content,
                CASE 
                    WHEN ? = 'ar' THEN COALESCE(NULLIF(p.excerpt_ar, ''), p.excerpt_en, p.excerpt)
                    ELSE COALESCE(p.excerpt_en, p.excerpt)
                END as excerpt,
                p.title_en,
                p.excerpt_en,
                p.content_en,
                p.title_ar,
                p.excerpt_ar,
                p.content_ar
            FROM press p
            WHERE p.is_published = 1
            ORDER BY p.press_date DESC
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param('sssii', $lang, $lang, $lang, $limit, $offset);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $press = [];
    while ($row = $result->fetch_assoc()) {
        // ── Fix image path ────────────────────────────────────────────────────
        // DB stores: "uploads/press/filename.jpg"
        // Files live at: "uploads/uploads/press/filename.jpg"
        // This normalizer handles all known path variants safely.
        if (!empty($row['cover_image'])) {
            $img = $row['cover_image'];

            // Already an absolute URL — leave it alone
            if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                // keep as-is
            }
            // Already has the correct double-uploads prefix
            elseif (strpos($img, 'uploads/uploads/press/') === 0) {
                // keep as-is
            }
            // Has single uploads/press/ prefix → add the extra uploads/
            elseif (strpos($img, 'uploads/press/') === 0) {
                $img = 'uploads/' . $img;
            }
            // Has assest/press-uploads/ prefix → normalise
            elseif (strpos($img, 'assest/press-uploads/') === 0) {
                $filename = basename($img);
                $img = 'uploads/uploads/press/' . $filename;
            }
            // Has assest/blog-uploads/ prefix → was saved to wrong folder, normalise
            elseif (strpos($img, 'assest/blog-uploads/') === 0) {
                $filename = basename($img);
                $img = 'uploads/uploads/press/' . $filename;
            }
            // Bare filename only
            elseif (strpos($img, '/') === false) {
                $img = 'uploads/uploads/press/' . $img;
            }

            $row['cover_image'] = $img;
        }
        // ─────────────────────────────────────────────────────────────────────

        $press[] = $row;
    }
    
    $stmt->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $press,
        'language' => $lang,
        'count' => count($press)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Press API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
