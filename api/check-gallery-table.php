<?php
/**
 * Check if event_gallery table exists and has data
 */

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if table exists
$tableCheckQuery = "SHOW TABLES LIKE 'event_gallery'";
$tableCheckResult = $conn->query($tableCheckQuery);
$tableExists = $tableCheckResult && $tableCheckResult->num_rows > 0;

$response = [
    'table_exists' => $tableExists
];

if ($tableExists) {
    // Get table structure
    $structureQuery = "DESCRIBE event_gallery";
    $structureResult = $conn->query($structureQuery);
    
    $structure = [];
    while ($row = $structureResult->fetch_assoc()) {
        $structure[] = $row;
    }
    
    $response['structure'] = $structure;
    
    // Get total images
    $countQuery = "SELECT COUNT(*) as count FROM event_gallery";
    $countResult = $conn->query($countQuery);
    $response['total_images'] = $countResult->fetch_assoc()['count'];
    
    // Get images by event
    $byEventQuery = "SELECT event_id, COUNT(*) as count FROM event_gallery GROUP BY event_id";
    $byEventResult = $conn->query($byEventQuery);
    
    $byEvent = [];
    while ($row = $byEventResult->fetch_assoc()) {
        $byEvent[] = $row;
    }
    
    $response['images_by_event'] = $byEvent;
    
    // Get sample images
    $sampleQuery = "SELECT * FROM event_gallery LIMIT 5";
    $sampleResult = $conn->query($sampleQuery);
    
    $samples = [];
    while ($row = $sampleResult->fetch_assoc()) {
        $samples[] = $row;
    }
    
    $response['sample_images'] = $samples;
} else {
    $response['message'] = 'event_gallery table does not exist';
}

$conn->close();

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

