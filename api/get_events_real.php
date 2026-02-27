<?php
require_once __DIR__ . '/config.php';
/**
 * LAKUM Artspace - Get Events API (Real Data Only)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
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
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    $conn->close();
    
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

