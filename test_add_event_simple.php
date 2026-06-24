<?php
/**
 * Simple test for add_event_simple.php API
 */

// Set up a test request
$testData = json_encode([
    'title_en' => 'Test Event from API Test',
    'description_en' => 'This is a test event created from the API test script',
    'location_en' => 'Hall 1',
    'event_date' => date('Y-m-d'),
    'event_time' => '10:00:00',
    'event_end_time' => '18:00:00',
    'category' => 'test'
]);

// Call the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/add_event_simple.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<pre>";
echo "HTTP Status: " . $httpCode . "\n";
echo "Raw Response:\n";
var_dump($response);
echo "\nError (if any):\n";
var_dump($error);

if ($response) {
    echo "\n\nParsed JSON:\n";
    var_dump(json_decode($response, true));
}
?>
