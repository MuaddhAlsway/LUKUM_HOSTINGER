<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed'
        ]);
        exit;
    }
    
    // Update all blog cover images to assest/img-4.png
    $sql = "UPDATE blogs SET cover_image = 'assest/img-4.png' WHERE 1=1";
    
    if ($db->query($sql)) {
        $affectedRows = $db->affectedRows();
        echo json_encode([
            'success' => true,
            'message' => "Updated $affectedRows blog cover images to assest/img-4.png"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error updating blogs'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

