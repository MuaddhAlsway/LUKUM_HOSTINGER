<?php
/**
 * Create event_gallery table if it doesn't exist
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

if ($tableExists) {
    echo json_encode([
        'success' => true,
        'message' => 'Table already exists',
        'table_exists' => true
    ]);
    $conn->close();
    exit;
}

// Create the table
$createTableQuery = "
CREATE TABLE event_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)
";

if ($conn->query($createTableQuery)) {
    echo json_encode([
        'success' => true,
        'message' => 'Table created successfully',
        'table_exists' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create table: ' . $conn->error
    ]);
}

$conn->close();
?>
