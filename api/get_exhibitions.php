<?php
/**
 * Get Exhibitions API - Get all exhibitions with language support
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Database credentials
$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

// Connect
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');

$type = $_GET['type'] ?? 'all';
$limit = (int)($_GET['limit'] ?? 1000);
$lang = $_GET['lang'] ?? 'en';

if (!in_array($lang, ['en', 'ar'])) {
    $lang = 'en';
}

$today = date('Y-m-d');

// Build SQL query with bilingual support
$sql = "SELECT 
    id,
    exhibition_date,
    exhibition_time,
    exhibition_end_time,
    end_date,
    cover_image,
    event_video,
    gallery_images,
    CASE 
        WHEN ? = 'ar' AND title_ar IS NOT NULL AND title_ar != '' THEN title_ar
        ELSE COALESCE(title_en, '')
    END as title,
    CASE 
        WHEN ? = 'ar' AND description_ar IS NOT NULL AND description_ar != '' THEN description_ar
        ELSE COALESCE(description_en, '')
    END as description,
    CASE 
        WHEN ? = 'ar' AND location_ar IS NOT NULL AND location_ar != '' THEN location_ar
        ELSE COALESCE(location_en, '')
    END as location,
    title_en,
    description_en,
    location_en,
    title_ar,
    description_ar,
    location_ar,
    is_featured
FROM exhibitions
WHERE 1=1";

$bindTypes = 'sss';
$bindParams = [&$lang, &$lang, &$lang];

// Filter by type
if ($type === 'upcoming') {
    $sql .= " AND exhibition_date >= ?";
    $bindTypes .= 's';
    $bindParams[] = &$today;
} elseif ($type === 'past') {
    $sql .= " AND exhibition_date < ?";
    $bindTypes .= 's';
    $bindParams[] = &$today;
}

// Sort order
if ($type === 'past') {
    $sql .= " ORDER BY exhibition_date DESC";
} else {
    $sql .= " ORDER BY exhibition_date ASC";
}

$sql .= " LIMIT ?";
$bindTypes .= 'i';
$bindParams[] = &$limit;

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error
    ]));
}

// Bind parameters dynamically
call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $exhibitions = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add computed fields for frontend compatibility
        $eventDate = new DateTime($row['exhibition_date']);
        $row['day'] = $eventDate->format('d');
        $row['month'] = $eventDate->format('F');
        $row['month_short'] = $eventDate->format('M');
        $row['year'] = $eventDate->format('Y');
        $row['event_date'] = $row['exhibition_date'];  // For compatibility with event.php
        $row['video_url'] = $row['event_video'];  // For compatibility with event.php
        
        $exhibitions[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $exhibitions,
        'count' => count($exhibitions),
        'language' => $lang
    ]);
    exit;
} else {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]));
}

$conn->close();

?>

