<?php
require_once __DIR__ . '/config.php';

/**
 * LAKUM Artspace - Get Events API (Real Data Only)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

try {
    // Get database connection using singleton
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed: Access denied for user \'' . DB_USER . '\'@\'localhost\' (using password: YES)');
    }
    
    $type = $_GET['type'] ?? 'all';
    $limit = (int)($_GET['limit'] ?? 1000);
    $lang = $_GET['lang'] ?? 'en';
    
    $query = "SELECT * FROM events";
    
    if ($type === 'upcoming') {
        $query .= " WHERE event_date >= CURDATE()";
    } elseif ($type === 'past') {
        $query .= " WHERE event_date < CURDATE()";
    }
    
    $query .= " ORDER BY event_date DESC LIMIT $limit";
    
    $result = $db->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $db->getConnection()->error);
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $events,
        'source' => 'database',
        'count' => count($events)
    ]);
    
} catch (Exception $e) {
    error_log('Events API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

