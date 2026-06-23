<?php
/**
 * Debug script to test get_event_details.php API response
 */

header('Content-Type: application/json; charset=utf-8');

// Test both exhibitions and events
$testIds = [3, 5, 74];  // 3 & 5 are exhibitions, 74 is an event

$results = [];

foreach ($testIds as $id) {
    // Use curl to simulate API call
    $url = 'http://localhost/api/get_event_details.php?id=' . $id . '&lang=en';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    $results[] = [
        'id' => $id,
        'http_code' => $httpCode,
        'success' => $data['success'] ?? false,
        'event' => $data['event'] ?? null,
        'video_url' => $data['event']['video_url'] ?? 'NOT FOUND',
        'event_video' => $data['event']['event_video'] ?? 'NOT FOUND'
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
