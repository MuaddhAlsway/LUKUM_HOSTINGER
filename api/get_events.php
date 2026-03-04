<?php
/**
 * LAKUM Artspace - Get Events API
 * Retrieves events from database - REAL DATA ONLY
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Use config.php for database connection
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Get current language from URL parameter or session
    $lang = $_GET['lang'] ?? $_SESSION['language'] ?? 'en';
    if (!in_array($lang, ['en', 'ar'])) {
        $lang = 'en';
    }
    
    $type = $_GET['type'] ?? 'all';
    $limit = (int)($_GET['limit'] ?? 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Get today's date in the correct format
    $today = date('Y-m-d');
    
    error_log('=== GET EVENTS API ===');
    error_log('Type: ' . $type);
    error_log('Today: ' . $today);
    error_log('Language: ' . $lang);
    
    // Query to get events with bilingual support
    $query = '
        SELECT 
            e.id,
            e.event_date,
            e.event_time,
            e.event_end_time,
            e.end_date,
            e.cover_image,
            e.video_url,
            e.is_featured,
            e.category,
            e.slug,
            COALESCE(NULLIF(e.title_en, ""), e.title) as title,
            COALESCE(NULLIF(e.description_en, ""), e.description) as description,
            COALESCE(NULLIF(e.location_en, ""), e.location) as location,
            e.title_ar,
            e.description_ar,
            e.location_ar
        FROM events e
        WHERE 1=1
    ';
    
    $bindTypes = '';
    $bindParams = [];
    
    // Filter by type based on event_date
    if ($type === 'upcoming') {
        $query .= ' AND e.event_date >= ?';
        $bindTypes = 's';
        $bindParams[] = &$today;
    } elseif ($type === 'past') {
        $query .= ' AND e.event_date < ?';
        $bindTypes = 's';
        $bindParams[] = &$today;
    }
    
    // Sort order
    if ($type === 'past') {
        $query .= ' ORDER BY e.event_date DESC';
    } else {
        $query .= ' ORDER BY e.event_date ASC';
    }
    
    $query .= ' LIMIT ? OFFSET ?';
    $bindTypes .= 'ii';
    $bindParams[] = &$limit;
    $bindParams[] = &$offset;
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->getConnection()->error);
    }
    
    // Bind parameters dynamically
    if (!empty($bindParams)) {
        call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $events = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add computed fields for frontend
        $eventDate = new DateTime($row['event_date']);
        $row['day'] = $eventDate->format('d');
        $row['month'] = $eventDate->format('F');
        $row['month_short'] = $eventDate->format('M');
        $row['year'] = $eventDate->format('Y');
        
        // Use Arabic fields if language is Arabic and they exist
        if ($lang === 'ar') {
            if ($row['title_ar']) $row['title'] = $row['title_ar'];
            if ($row['description_ar']) $row['description'] = $row['description_ar'];
            if ($row['location_ar']) $row['location'] = $row['location_ar'];
        }
        
        $events[] = $row;
    }
    
    error_log('Events found: ' . count($events));
    
    echo json_encode([
        'success' => true,
        'data' => $events,
        'type' => $type,
        'language' => $lang,
        'count' => count($events)
    ]);
    
} catch (Exception $e) {
    error_log('Events API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>

