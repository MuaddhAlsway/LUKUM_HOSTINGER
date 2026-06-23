<?php
/**
 * Test video removal - check what's actually in the database
 */

header('Content-Type: application/json; charset=utf-8');

$db_host = 'localhost';
$db_user = 'u812122863_neama';
$db_pass = 'Nema202610!LakumDB';
$db_name = 'u812122863_lakum_artspace';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

$conn->set_charset('utf8mb4');

// Get all exhibitions and check their video field
$sql = "SELECT id, title_en, event_video, 
        CASE 
            WHEN event_video IS NULL THEN 'NULL' 
            WHEN event_video = '' THEN 'EMPTY_STRING'
            ELSE event_video 
        END as video_status
        FROM exhibitions 
        ORDER BY id DESC 
        LIMIT 20";

$result = $conn->query($sql);
$exhibitions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $exhibitions[] = [
            'id' => $row['id'],
            'title' => $row['title_en'],
            'event_video_raw' => $row['event_video'],
            'event_video_status' => $row['video_status']
        ];
    }
}

echo json_encode([
    'success' => true,
    'exhibitions' => $exhibitions,
    'total' => count($exhibitions)
]);

$conn->close();
?>
