<?php
require_once 'db.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Checking table structures...\n\n";

$tables = ['events', 'blogs', 'press', 'pricing'];

foreach ($tables as $table) {
    echo "Table: $table\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "  ERROR: " . $conn->error . "\n";
    }
    echo "\n";
}
?>


