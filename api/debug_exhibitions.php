<?php
/**
 * Debug Exhibitions - Check what's in the database
 */

header('Content-Type: application/json; charset=utf-8');

// Database credentials
$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

// Connect
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');

// Get all exhibitions
$result = $conn->query("SELECT * FROM exhibitions ORDER BY exhibition_date DESC");

$exhibitions = [];
while ($row = $result->fetch_assoc()) {
    $exhibitions[] = $row;
}

// Today's date
$today = date('Y-m-d');

// Check which are past
$past = array_filter($exhibitions, function($e) {
    $exDate = substr($e['exhibition_date'], 0, 10);
    $today = date('Y-m-d');
    return $exDate <= $today;
});

echo json_encode([
    'success' => true,
    'today' => $today,
    'total_exhibitions' => count($exhibitions),
    'past_exhibitions' => count($past),
    'all_exhibitions' => $exhibitions,
    'debug' => [
        'database' => $db_name,
        'user' => $db_user,
        'server_time' => date('Y-m-d H:i:s'),
        'php_time' => time()
    ]
], JSON_PRETTY_PRINT);

$conn->close();

?>
