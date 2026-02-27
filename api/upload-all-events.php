<?php
// Upload all events from eventfile folder to database

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$eventfileDir = __DIR__ . '/../eventfile';
$uploadDir = __DIR__ . '/../uploads/events';

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadedEvents = [];
$uploadedImages = 0;
$errors = [];

// Scan eventfile directory
$folders = scandir($eventfileDir);

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') continue;
    
    $folderPath = $eventfileDir . '/' . $folder;
    if (!is_dir($folderPath)) continue;
    
    // Use folder name as event title
    $eventTitle = trim($folder);
    
    // Check if event already exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE title = ?");
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eventId = null;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventId = $row['id'];
    } else {
        // Create new event with default values
        $eventDate = date('Y-m-d'); // Today's date
        $eventTime = '10:00';
        $location = 'LAKUM Artspace';
        $category = 'exhibition';
        $isFeatured = 1;
        $description = 'Event: ' . $eventTitle;
        
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, category, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $eventTitle, $eventDate, $eventTime, $location, $category, $description, $isFeatured);
        
        if ($stmt->execute()) {
            $eventId = $conn->insert_id;
        } else {
            $errors[] = "Failed to create event: $eventTitle";
            continue;
        }
    }
    
    // Scan for images in folder
    $files = scandir($folderPath);
    $imageCount = 0;
    $coverImageSet = false;
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $folderPath . '/' . $file;
        if (!is_file($filePath)) continue;
        
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) continue;
        
        // Copy image to uploads folder
        $newFilename = $eventId . '_' . $imageCount . '.' . $ext;
        $newPath = $uploadDir . '/' . $newFilename;
        
        if (copy($filePath, $newPath)) {
            // Update event with cover image if first image
            if (!$coverImageSet) {
                $coverImage = 'uploads/events/' . $newFilename;
                $stmt = $conn->prepare("UPDATE events SET cover_image = ? WHERE id = ?");
                $stmt->bind_param("si", $coverImage, $eventId);
                $stmt->execute();
                $coverImageSet = true;
            }
            
            $imageCount++;
            $uploadedImages++;
        } else {
            $errors[] = "Failed to copy image: $file from $eventTitle";
        }
    }
    
    if ($imageCount > 0) {
        $uploadedEvents[] = [
            'name' => $eventTitle,
            'id' => $eventId,
            'images' => $imageCount
        ];
    }
}

// Generate response
$response = [
    'success' => true,
    'message' => 'Upload complete',
    'totalEvents' => count($uploadedEvents),
    'totalImages' => $uploadedImages,
    'events' => $uploadedEvents,
    'errors' => $errors
];

echo json_encode($response);
?>
