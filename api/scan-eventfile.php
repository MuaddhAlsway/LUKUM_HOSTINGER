<?php
// Scan eventfile folder and generate eventcontainer.md

$eventfileDir = __DIR__ . '/../eventfile';

if (!is_dir($eventfileDir)) {
    echo json_encode(['success' => false, 'message' => 'eventfile folder not found']);
    exit;
}

$events = [];

// Scan all folders in eventfile
$folders = scandir($eventfileDir);

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') continue;
    
    $folderPath = $eventfileDir . '/' . $folder;
    if (!is_dir($folderPath)) continue;
    
    // Count images in folder
    $files = scandir($folderPath);
    $imageCount = 0;
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageCount++;
        }
    }
    
    if ($imageCount > 0) {
        $events[] = [
            'name' => $folder,
            'imageCount' => $imageCount,
            'path' => $folderPath
        ];
    }
}

// Sort events by name
usort($events, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Generate markdown content
$markdown = "# Event File Inventory\n\n";
$markdown .= "**Total Folders**: " . count($events) . "\n\n";
$markdown .= "| Folder Name | Images | Status |\n";
$markdown .= "|---|---|---|\n";

$totalImages = 0;
foreach ($events as $event) {
    $totalImages += $event['imageCount'];
    $markdown .= "| " . $event['name'] . " | " . $event['imageCount'] . " | ✓ Ready |\n";
}

$markdown .= "\n**Total Images**: " . $totalImages . "\n\n";

// Write to eventcontainer.md
$outputFile = __DIR__ . '/../eventcontainer.md';
file_put_contents($outputFile, $markdown);

echo json_encode([
    'success' => true,
    'message' => 'Event file inventory generated',
    'totalFolders' => count($events),
    'totalImages' => $totalImages,
    'events' => $events
]);
?>


