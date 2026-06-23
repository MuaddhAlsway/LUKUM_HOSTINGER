<?php
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    // Check all exhibitions
    $query = "SELECT id, title_en FROM exhibitions ORDER BY id ASC";
    $result = $db->getConnection()->query($query);
    
    echo json_encode([
        'success' => true,
        'exhibitions' => $result->fetch_all(MYSQLI_ASSOC),
        'total' => $result->num_rows
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
