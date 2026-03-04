<?php
// Simple Event Sync - No dependencies

require_once __DIR__ . '/../config.php';

error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// Database connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');

$eventfileDir = __DIR__ . '/../eventfile';
$uploadDir = __DIR__ . '/../uploads/events';

// Create upload directory
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$results = [
    'success' => true,
    'totalFolders' => 0,
    'totalImages' => 0,
    'createdEvents' => 0,
    'updatedEvents' => 0,
    'uploadedImages' => 0,
    'events' => [],
    'errors' => []
];

// Check eventfile directory
if (!is_dir($eventfileDir)) {
    echo json_encode(['success' => false, 'message' => 'eventfile folder not found at: ' . $eventfileDir]);
    exit;
}

$folders = @scandir($eventfileDir);
if (!$folders) {
    echo json_encode(['success' => false, 'message' => 'Cannot read eventfile folder']);
    exit;
}

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') continue;
    
    $folderPath = $eventfileDir . '/' . $folder;
    if (!is_dir($folderPath)) continue;
    
    $results['totalFolders']++;
    $eventTitle = trim($folder);
    
    // Scan for images
    $files = @scandir($folderPath);
    if (!$files) continue;
    
    $imageFiles = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $folderPath . '/' . $file;
        if (!is_file($filePath)) continue;
        
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageFiles[] = ['name' => $file, 'path' => $filePath, 'ext' => $ext];
            $results['totalImages']++;
        }
    }
    
    if (empty($imageFiles)) {
        $results['errors'][] = "No images in: $folder";
        continue;
    }
    
    // Check if event exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE title = ?");
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eventId = null;
    $isNewEvent = false;
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventId = $row['id'];
        $results['updatedEvents']++;
    } else {
        // Create event
        $eventDate = date('Y-m-d');
        $eventTime = '10:00';
        $location = 'LAKUM Artspace';
        $category = 'exhibition';
        $isFeatured = 1;
        $description = 'Event: ' . $eventTitle;
        
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, category, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $eventTitle, $eventDate, $eventTime, $location, $category, $description, $isFeatured);
        
        if ($stmt->execute()) {
            $eventId = $conn->insert_id;
            $results['createdEvents']++;
            $isNewEvent = true;
        } else {
            $results['errors'][] = "Failed to create: $eventTitle";
            continue;
        }
    }
    
    // Upload images
    $imageCount = 0;
    $coverImageSet = false;
    
    foreach ($imageFiles as $imageFile) {
        $newFilename = $eventId . '_' . $imageCount . '.' . $imageFile['ext'];
        $newPath = $uploadDir . '/' . $newFilename;
        
        if (@copy($imageFile['path'], $newPath)) {
            if (!$coverImageSet) {
                $coverImage = 'uploads/events/' . $newFilename;
                $stmt = $conn->prepare("UPDATE events SET cover_image = ? WHERE id = ?");
                $stmt->bind_param("si", $coverImage, $eventId);
                $stmt->execute();
                $coverImageSet = true;
            }
            
            $imageCount++;
            $results['uploadedImages']++;
        } else {
            $results['errors'][] = "Failed to copy: " . $imageFile['name'];
        }
    }
    
    $results['events'][] = [
        'name' => $eventTitle,
        'id' => $eventId,
        'images' => $imageCount,
        'isNew' => $isNewEvent
    ];
}

$conn->close();
echo json_encode($results);
?>



