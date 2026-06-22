<?php
// Test script to verify video URL saving

$conn = new mysqli('localhost', 'u812122863_neama', 'Nema202610!LakumDB', 'u812122863_lakum_artspace');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Check exhibition 54
echo "=== CHECKING EXHIBITION 54 ===\n";
$result = $conn->query("SELECT id, title_en, event_video FROM exhibitions WHERE id = 54");
if ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title_en'] . "\n";
    echo "Event Video: " . ($row['event_video'] ? $row['event_video'] : 'NULL') . "\n";
} else {
    echo "Exhibition not found\n";
}

// Show all exhibitions with video URLs
echo "\n=== ALL EXHIBITIONS WITH VIDEO URLs ===\n";
$result = $conn->query("SELECT id, title_en, event_video FROM exhibitions WHERE event_video IS NOT NULL AND event_video != ''");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Title: " . $row['title_en'] . ", Video: " . $row['event_video'] . "\n";
    }
} else {
    echo "No exhibitions with video URLs found\n";
}

// Show table structure
echo "\n=== EXHIBITIONS TABLE STRUCTURE ===\n";
$result = $conn->query("DESC exhibitions");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

$conn->close();
?>