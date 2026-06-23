<?php
/**
 * Test API Response for exhibitions and events
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'api/config.php';

$db = Database::getInstance();

// Test Exhibition ID 3
echo "=== TESTING EXHIBITION ID 3 ===\n";
$sql3 = "SELECT ex.id, ex.title_en, ex.event_video, 
         ex.event_video as video_url, 
         ex.event_video 
         FROM exhibitions ex WHERE ex.id = 3 LIMIT 1";
$result3 = $db->getConnection()->query($sql3);
$row3 = $result3->fetch_assoc();
echo json_encode($row3, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Test Exhibition ID 5
echo "=== TESTING EXHIBITION ID 5 ===\n";
$sql5 = "SELECT ex.id, ex.title_en, ex.event_video, 
         ex.event_video as video_url, 
         ex.event_video 
         FROM exhibitions ex WHERE ex.id = 5 LIMIT 1";
$result5 = $db->getConnection()->query($sql5);
$row5 = $result5->fetch_assoc();
echo json_encode($row5, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Test Event ID 74
echo "=== TESTING EVENT ID 74 ===\n";
$sql74 = "SELECT e.id, e.title, e.video_url, 
          e.video_url as event_video 
          FROM events e WHERE e.id = 74 LIMIT 1";
$result74 = $db->getConnection()->query($sql74);
$row74 = $result74->fetch_assoc();
echo json_encode($row74, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Now test through actual API
echo "=== API CALL FOR EXHIBITION 3 ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/get_event_details.php?id=3&lang=en');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);
echo json_decode($response, true)['event'] ? json_encode(json_decode($response, true)['event'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $response;
echo "\n";
?>
