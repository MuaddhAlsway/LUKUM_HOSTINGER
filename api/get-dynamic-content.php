<?php
/**
 * Get Dynamic Content API
 * Returns bilingual content based on language preference
 * 
 * Usage: GET /api/get-dynamic-content.php?type=blog&id=1&lang=ar
 * Returns: JSON object with content in requested language
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once 'config.php';

// Get parameters
$type = isset($_GET['type']) ? $_GET['type'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

// Validate language
$allowed_langs = ['en', 'ar'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'en';
}

// Validate type
$allowed_types = ['blog', 'event', 'press', 'pricing'];
if (!in_array($type, $allowed_types)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid content type',
        'type' => $type
    ]);
    exit;
}

try {
    // Get database connection
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Determine language suffix
    $lang_suffix = ($lang === 'ar') ? '_ar' : '_en';

    // Build query based on type
    $query = '';
    $params = [];
    $param_types = '';

    if ($type === 'blog') {
        if ($id) {
            // Get single blog post
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, `excerpt" . $lang_suffix . "` as excerpt, author, category, cover_image, created_at FROM blogs WHERE id = ?";
            $params = [$id];
            $param_types = 'i';
        } else {
            // Get all blog posts
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, `excerpt" . $lang_suffix . "` as excerpt, author, category, cover_image, created_at FROM blogs ORDER BY created_at DESC";
        }
    } elseif ($type === 'event') {
        if ($id) {
            // Get single event
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `description" . $lang_suffix . "` as description, date, time, location FROM events WHERE id = ?";
            $params = [$id];
            $param_types = 'i';
        } else {
            // Get all events
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `description" . $lang_suffix . "` as description, date, time, location FROM events ORDER BY date DESC";
        }
    } elseif ($type === 'press') {
        if ($id) {
            // Get single press release
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, publication, date FROM press WHERE id = ?";
            $params = [$id];
            $param_types = 'i';
        } else {
            // Get all press releases
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, publication, date FROM press ORDER BY date DESC";
        }
    } elseif ($type === 'pricing') {
        if ($id) {
            // Get single pricing item
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, price FROM pricing WHERE id = ?";
            $params = [$id];
            $param_types = 'i';
        } else {
            // Get all pricing items
            $query = "SELECT id, `title" . $lang_suffix . "` as title, `content" . $lang_suffix . "` as content, price FROM pricing ORDER BY price ASC";
        }
    }

    // Execute query
    $result = null;
    if (empty($params)) {
        $result = $conn->query($query);
        if (!$result) {
            throw new Exception('Query execution failed: ' . $conn->error);
        }
    } else {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param($param_types, ...$params);
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }
        $result = $stmt->get_result();
    }

    // Fetch results
    $content = [];
    while ($row = $result->fetch_assoc()) {
        $content[] = $row;
    }

    // Return single item if ID was specified
    if ($id && count($content) > 0) {
        $content = $content[0];
    }

    echo json_encode([
        'success' => true,
        'type' => $type,
        'lang' => $lang,
        'content' => $content,
        'count' => is_array($content) ? count($content) : 1
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'type' => $type,
        'lang' => $lang
    ]);
}


