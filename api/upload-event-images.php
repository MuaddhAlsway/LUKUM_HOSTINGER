<?php
// Event Image Uploader - Matches images from eventfile folder to database events

require_once 'config.php';

// Event mapping from eventcontainer.md
$eventMapping = [
    // Upcoming Events
    'AMPM' => ['date' => '2026-10-21', 'time' => '17:53', 'status' => 'upcoming'],
    'YSL 2' => ['date' => '2026-05-30', 'time' => '13:15', 'status' => 'upcoming'],
    'Nana' => ['date' => '2026-05-30', 'time' => '17:00', 'status' => 'upcoming'],
    'Snapchat' => ['date' => '2026-05-03', 'time' => '18:00', 'status' => 'upcoming'],
    'Cheval Blanc' => ['date' => '2026-04-16', 'time' => '13:15', 'status' => 'upcoming'],
    
    // Previous Events
    'YSL' => ['date' => '2025-09-18', 'time' => '17:00', 'status' => 'previous'],
    'Maysar' => ['date' => '2025-09-13', 'time' => '16:38', 'status' => 'previous'],
    'Eyewa' => ['date' => '2025-06-19', 'time' => '13:15', 'status' => 'previous'],
    'Anastasia Beverly Hills' => ['date' => '2025-05-28', 'time' => '13:15', 'status' => 'previous'],
    'Dior' => ['date' => '2025-05-12', 'time' => '13:15', 'status' => 'previous'],
    'Chalhoub Group' => ['date' => '2025-05-11', 'time' => '13:15', 'status' => 'previous'],
    'Huda Beauty' => ['date' => '2025-04-29', 'time' => '13:15', 'status' => 'previous'],
    'Oscar De La Renta' => ['date' => '2025-02-18', 'time' => '17:15', 'status' => 'previous'],
    'AMPM' => ['date' => '2024-12-19', 'time' => '17:39', 'status' => 'previous'],
    'Namshi' => ['date' => '2024-09-19', 'time' => '13:15', 'status' => 'previous'],
    'Piaget' => ['date' => '2024-05-15', 'time' => '13:15', 'status' => 'previous'],
    'Mont Blanc' => ['date' => '2024-04-30', 'time' => '16:00', 'status' => 'previous'],
    'Chador' => ['date' => '2024-03-09', 'time' => '17:42', 'status' => 'previous'],
    'Tag Tagia' => ['date' => '2024-03-07', 'time' => '17:43', 'status' => 'previous'],
    'Tiktok' => ['date' => '2024-02-28', 'time' => '14:00', 'status' => 'previous'],
    'American Eagle' => ['date' => '2023-09-14', 'time' => '16:45', 'status' => 'previous'],
    'Social Development Bank' => ['date' => '2023-05-28', 'time' => '17:45', 'status' => 'previous'],
    'La Praire' => ['date' => '2023-03-14', 'time' => '13:15', 'status' => 'previous'],
    'Messika' => ['date' => '2022-11-14', 'time' => '13:15', 'status' => 'previous'],
    'Shiseido' => ['date' => '2022-03-25', 'time' => '18:15', 'status' => 'previous'],
    'Boucheron' => ['date' => '2021-10-04', 'time' => '13:15', 'status' => 'previous'],
];

// Folder mapping
$folderMapping = [
    'ampm' => 'AMPM',
    'ysl' => 'YSL',
    'ysl2' => 'YSL 2',
    'snapchat' => 'Snapchat',
    'snpchat' => 'Snapchat',
    'cheval' => 'Cheval Blanc',
    'chevalblanc' => 'Cheval Blanc',
    'nana' => 'Nana',
    'maysar' => 'Maysar',
    'eyewa' => 'Eyewa',
    'huda' => 'Huda Beauty',
    'dior' => 'Dior',
    'chalhoub' => 'Chalhoub Group',
    'oscar' => 'Oscar De La Renta',
    'namshi' => 'Namshi',
    'piagat' => 'Piaget',
    'piaget' => 'Piaget',
    'mont' => 'Mont Blanc',
    'chador' => 'Chador',
    'tag' => 'Tag Tagia',
    'tiktok' => 'Tiktok',
    'american' => 'American Eagle',
    'social' => 'Social Development Bank',
    'lapraire' => 'La Praire',
    'messik' => 'Messika',
    'shiseido' => 'Shiseido',
    'boucheron' => 'Boucheron',
];

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

$uploadedCount = 0;
$errors = [];

// Scan eventfile directory
$folders = scandir($eventfileDir);

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') continue;
    
    $folderPath = $eventfileDir . '/' . $folder;
    if (!is_dir($folderPath)) continue;
    
    // Find matching event name
    $eventName = null;
    $folderLower = strtolower($folder);
    
    foreach ($folderMapping as $key => $name) {
        if (strpos($folderLower, $key) !== false) {
            $eventName = $name;
            break;
        }
    }
    
    if (!$eventName || !isset($eventMapping[$eventName])) {
        $errors[] = "Could not map folder: $folder";
        continue;
    }
    
    $eventInfo = $eventMapping[$eventName];
    
    // Find or create event in database
    $stmt = $conn->prepare("SELECT id FROM events WHERE title = ? AND event_date = ?");
    $stmt->bind_param("ss", $eventName, $eventInfo['date']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eventId = null;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventId = $row['id'];
    } else {
        // Create new event
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, category, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
        $location = 'LAKUM Artspace';
        $category = 'exhibition';
        $isFeatured = 1;
        $stmt->bind_param("sssssi", $eventName, $eventInfo['date'], $eventInfo['time'], $location, $category, $isFeatured);
        $stmt->execute();
        $eventId = $conn->insert_id;
    }
    
    // Scan for images in folder
    $files = scandir($folderPath);
    $imageCount = 0;
    
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
            if ($imageCount === 0) {
                $coverImage = 'uploads/events/' . $newFilename;
                $stmt = $conn->prepare("UPDATE events SET cover_image = ? WHERE id = ?");
                $stmt->bind_param("si", $coverImage, $eventId);
                $stmt->execute();
            }
            
            $imageCount++;
            $uploadedCount++;
        } else {
            $errors[] = "Failed to copy: $file from $folder";
        }
    }
    
    if ($imageCount > 0) {
        echo "✓ $eventName: $imageCount images uploaded<br>";
    }
}

echo "<br><strong>Summary:</strong><br>";
echo "Total images uploaded: $uploadedCount<br>";
if (!empty($errors)) {
    echo "Errors:<br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
}

echo "<br><a href='../admin/dashboard.html'>Back to Dashboard</a>";
?>
