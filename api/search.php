<?php
/**
 * LAKUM Artspace - Search API
 * Handles search across events, blogs, and press
 */

require_once 'config.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die(Response::error('Method not allowed', 405));
}

// Get search query
$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'all'; // all, events, blogs, press
$limit = (int)($_GET['limit'] ?? 10);
$offset = (int)($_GET['offset'] ?? 0);

// Validate query
if (strlen($query) < 2) {
    die(Response::error('Search query must be at least 2 characters', 400));
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $results = [];
    
    // Search events
    if ($type === 'all' || $type === 'events') {
        $search_query = '%' . $conn->real_escape_string($query) . '%';
        $stmt = $conn->prepare("
            SELECT id, title, description, event_date, cover_image, 'event' as type
            FROM events
            WHERE title LIKE ? OR description LIKE ?
            ORDER BY event_date DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param('ssii', $search_query, $search_query, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    
    // Search blogs
    if ($type === 'all' || $type === 'blogs') {
        $search_query = '%' . $conn->real_escape_string($query) . '%';
        $stmt = $conn->prepare("
            SELECT id, title, excerpt, created_at, cover_image, 'blog' as type
            FROM blogs
            WHERE title LIKE ? OR excerpt LIKE ? OR content LIKE ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param('sssii', $search_query, $search_query, $search_query, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    
    // Search press
    if ($type === 'all' || $type === 'press') {
        $search_query = '%' . $conn->real_escape_string($query) . '%';
        $stmt = $conn->prepare("
            SELECT id, title, content, date, image, 'press' as type
            FROM press
            WHERE title LIKE ? OR content LIKE ?
            ORDER BY date DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param('ssii', $search_query, $search_query, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    
    echo Response::success(
        [
            'query' => $query,
            'type' => $type,
            'count' => count($results),
            'results' => $results
        ],
        'Search completed successfully',
        200
    );
    
} catch (Exception $e) {
    error_log($e->getMessage());
    echo Response::error('Search failed: ' . $e->getMessage(), 500);
}


