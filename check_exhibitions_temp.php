<?php
require_once 'api/config.php';
$db = Database::getInstance();
$result = $db->getConnection()->query("SELECT id, title_en FROM exhibitions LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Title: " . $row['title_en'] . "\n";
    }
} else {
    echo "No exhibitions or error: " . $db->getConnection()->error . "\n";
}
?>
