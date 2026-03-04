<?php
/**
 * Create event_translations table if it doesn't exist
 */

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'lakum_artspace');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if table exists
$tableCheckQuery = "SHOW TABLES LIKE 'event_translations'";
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
CREATE TABLE event_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    language VARCHAR(10) NOT NULL,
    title VARCHAR(255),
    description LONGTEXT,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_event_lang (event_id, language),
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

