<?php
// Sync all events from eventfile folder to database

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once 'config.php';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}

$eventfileDir = __DIR__ . '/../eventfile';
$uploadDir = __DIR__ . '/../uploads/events';

// Create upload directory if it doesn't exist
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

// Check if eventfile directory exists
if (!is_dir($eventfileDir)) {
    echo json_encode(['success' => false, 'message' => 'eventfile folder not found']);
    exit;
}

// Scan eventfile directory
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
    
    // Use folder name as event title
    $eventTitle = trim($folder);
    
    // Scan for images first to count them
    $files = scandir($folderPath);
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
        $results['errors'][] = "No images found in: $folder";
        continue;
    }
    
    // Check if event already exists
    $stmt = $conn->prepare("SELECT id, cover_image FROM events WHERE title = ?");
    if (!$stmt) {
        $results['errors'][] = "Database error for $eventTitle: " . $conn->error;
        continue;
    }
    
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
        // Create new event with default values
        $eventDate = date('Y-m-d');
        $eventTime = '10:00';
        $location = 'LAKUM Artspace';
        $category = 'exhibition';
        $isFeatured = 1;
        $description = 'Event: ' . $eventTitle;
        
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, category, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $results['errors'][] = "Database error creating $eventTitle: " . $conn->error;
            continue;
        }
        
        $stmt->bind_param("ssssssi", $eventTitle, $eventDate, $eventTime, $location, $category, $description, $isFeatured);
        
        if ($stmt->execute()) {
            $eventId = $conn->insert_id;
            $results['createdEvents']++;
            $isNewEvent = true;
        } else {
            $results['errors'][] = "Failed to create event: $eventTitle - " . $stmt->error;
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
            // Update event with cover image if first image
            if (!$coverImageSet) {
                $coverImage = 'uploads/events/' . $newFilename;
                $stmt = $conn->prepare("UPDATE events SET cover_image = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $coverImage, $eventId);
                    $stmt->execute();
                }
                $coverImageSet = true;
            }
            
            $imageCount++;
            $results['uploadedImages']++;
        } else {
            $results['errors'][] = "Failed to copy image: " . $imageFile['name'] . " from $eventTitle";
        }
    }
    
    // Add to results
    $results['events'][] = [
        'name' => $eventTitle,
        'id' => $eventId,
        'images' => $imageCount,
        'isNew' => $isNewEvent
    ];
}

echo json_encode($results);
?>
