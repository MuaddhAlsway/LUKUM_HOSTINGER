<?php
// Complete Event Sync: Scan eventfile, read eventcontainer.md, upload images, update dates, delete extras

require_once __DIR__ . '/../config.php';

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Database connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$conn->set_charset('utf8mb4');

// Event data from eventcontainer.md
$eventData = [
    // Upcoming Events
    ['name' => 'AMPM', 'date' => '2026-10-21', 'time' => '17:53', 'folder' => 'ampm'],
    ['name' => 'YSL 2', 'date' => '2026-05-30', 'time' => '13:15', 'folder' => 'YSL2'],
    ['name' => 'Nana', 'date' => '2026-05-30', 'time' => '17:00', 'folder' => 'nana'],
    ['name' => 'Snapchat', 'date' => '2026-05-03', 'time' => '18:00', 'folder' => 'Snpchat'],
    ['name' => 'Cheval Blanc', 'date' => '2026-04-16', 'time' => '13:15', 'folder' => 'cHEVAL'],
    
    // Previous Events
    ['name' => 'YSL', 'date' => '2025-09-18', 'time' => '17:00', 'folder' => 'YSL'],
    ['name' => 'Maysar', 'date' => '2025-09-13', 'time' => '16:38', 'folder' => 'Maysar ,Maysar(1),Maysar(2)'],
    ['name' => 'Eyewa', 'date' => '2025-06-19', 'time' => '13:15', 'folder' => 'EYEWA'],
    ['name' => 'Anastasia Beverly Hills', 'date' => '2025-05-28', 'time' => '13:15', 'folder' => 'Anastasia'],
    ['name' => 'Dior', 'date' => '2025-05-12', 'time' => '13:15', 'folder' => 'Dior'],
    ['name' => 'Chalhoub Group', 'date' => '2025-05-11', 'time' => '13:15', 'folder' => 'Chalhoub Group'],
    ['name' => 'Huda Beauty', 'date' => '2025-04-29', 'time' => '13:15', 'folder' => 'Huda Beauty'],
    ['name' => 'Oscar De La Renta', 'date' => '2025-02-18', 'time' => '17:15', 'folder' => 'Oscar De La Renta'],
    ['name' => 'AMPM', 'date' => '2024-12-19', 'time' => '17:39', 'folder' => 'ampm'],
    ['name' => 'Namshi', 'date' => '2024-09-19', 'time' => '13:15', 'folder' => 'Namshi'],
    ['name' => 'Piaget', 'date' => '2024-05-15', 'time' => '13:15', 'folder' => 'Piagat'],
    ['name' => 'Mont Blanc', 'date' => '2024-04-30', 'time' => '16:00', 'folder' => 'Mont Blanc'],
    ['name' => 'Chador', 'date' => '2024-03-09', 'time' => '17:42', 'folder' => 'Chador'],
    ['name' => 'Tag Tagia', 'date' => '2024-03-07', 'time' => '17:43', 'folder' => 'Tag Tagia'],
    ['name' => 'Tiktok', 'date' => '2024-02-28', 'time' => '14:00', 'folder' => 'Tiktok'],
    ['name' => 'American Eagle', 'date' => '2023-09-14', 'time' => '16:45', 'folder' => 'American Eagle'],
    ['name' => 'Social Development Bank', 'date' => '2023-05-28', 'time' => '17:45', 'folder' => 'Social Development Bank'],
    ['name' => 'La Praire', 'date' => '2023-03-14', 'time' => '13:15', 'folder' => 'LaPraire'],
    ['name' => 'Messika', 'date' => '2022-11-14', 'time' => '13:15', 'folder' => 'Messik'],
    ['name' => 'Shiseido', 'date' => '2022-03-25', 'time' => '18:15', 'folder' => 'Shiseido'],
    ['name' => 'Boucheron', 'date' => '2021-10-04', 'time' => '13:15', 'folder' => 'Boucheron'],
];

$eventfileDir = __DIR__ . '/../eventfile';
$uploadDir = __DIR__ . '/../uploads/events';

// Create upload directory
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$results = [
    'success' => true,
    'created' => 0,
    'updated' => 0,
    'imagesUploaded' => 0,
    'deleted' => 0,
    'total' => count($eventData),
    'events' => [],
    'errors' => []
];

// Get list of valid event names from eventcontainer
$validEventNames = array_map(function($e) { return $e['name']; }, $eventData);

// Step 1: Delete events not in eventcontainer
$stmt = $conn->prepare("SELECT id, title FROM events");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['title'], $validEventNames)) {
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->bind_param("i", $row['id']);
        if ($deleteStmt->execute()) {
            $results['deleted']++;
        }
    }
}

// Step 2: Process each event from eventcontainer
foreach ($eventData as $event) {
    $name = $event['name'];
    $date = $event['date'];
    $time = $event['time'];
    $folderName = $event['folder'];
    
    // Check if event exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE title = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eventId = null;
    $isNew = false;
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventId = $row['id'];
        
        // Update event date and time
        $stmt = $conn->prepare("UPDATE events SET event_date = ?, event_time = ? WHERE id = ?");
        $stmt->bind_param("ssi", $date, $time, $eventId);
        $stmt->execute();
        $results['updated']++;
    } else {
        // Create new event
        $location = 'LAKUM Artspace';
        $category = 'exhibition';
        $isFeatured = 1;
        $description = 'Event: ' . $name;
        
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, category, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $date, $time, $location, $category, $description, $isFeatured);
        
        if ($stmt->execute()) {
            $eventId = $conn->insert_id;
            $results['created']++;
            $isNew = true;
        } else {
            $results['errors'][] = "Failed to create: $name";
            continue;
        }
    }
    
    // Step 3: Upload images from eventfile folder
    $folderPath = $eventfileDir . '/' . $folderName;
    
    if (is_dir($folderPath)) {
        $files = @scandir($folderPath);
        if ($files) {
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
                
                if (@copy($filePath, $newPath)) {
                    // Set first image as cover
                    if (!$coverImageSet) {
                        $coverImage = 'uploads/events/' . $newFilename;
                        $stmt = $conn->prepare("UPDATE events SET cover_image = ? WHERE id = ?");
                        $stmt->bind_param("si", $coverImage, $eventId);
                        $stmt->execute();
                        $coverImageSet = true;
                    }
                    
                    $imageCount++;
                    $results['imagesUploaded']++;
                } else {
                    $results['errors'][] = "Failed to copy: $file from $name";
                }
            }
            
            $results['events'][] = [
                'name' => $name,
                'date' => $date,
                'time' => $time,
                'images' => $imageCount,
                'action' => $isNew ? 'Created' : 'Updated'
            ];
        } else {
            $results['errors'][] = "Cannot read folder: $folderName for $name";
        }
    } else {
        $results['errors'][] = "Folder not found: $folderName for $name";
    }
}

$conn->close();
echo json_encode($results);
?>


