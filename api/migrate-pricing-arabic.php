<?php
/**
 * Migrate pricing table to support Arabic fields
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if columns already exist
    $result = $conn->query("SHOW COLUMNS FROM pricing LIKE 'name_en'");
    if ($result && $result->num_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Arabic fields already exist',
            'migrated' => false
        ]);
        $conn->close();
        exit;
    }
    
    // Add new columns for bilingual support
    $alterSQL = "
        ALTER TABLE pricing
        ADD COLUMN name_en VARCHAR(255) AFTER title,
        ADD COLUMN name_ar VARCHAR(255) AFTER name_en,
        ADD COLUMN description_en LONGTEXT AFTER name_ar,
        ADD COLUMN description_ar LONGTEXT AFTER description_en,
        ADD COLUMN duration_en VARCHAR(255) AFTER description_ar,
        ADD COLUMN duration_ar VARCHAR(255) AFTER duration_en,
        ADD COLUMN features_en LONGTEXT AFTER duration_ar,
        ADD COLUMN features_ar LONGTEXT AFTER features_en
    ";
    
    if (!$conn->query($alterSQL)) {
        throw new Exception('Alter table failed: ' . $conn->error);
    }
    
    // Migrate existing data: copy title to name_en
    $updateSQL = "UPDATE pricing SET name_en = title WHERE name_en IS NULL OR name_en = ''";
    if (!$conn->query($updateSQL)) {
        throw new Exception('Update failed: ' . $conn->error);
    }
    
    // Copy content to description_en
    $updateSQL2 = "UPDATE pricing SET description_en = content WHERE description_en IS NULL OR description_en = ''";
    if (!$conn->query($updateSQL2)) {
        throw new Exception('Update failed: ' . $conn->error);
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pricing table migrated to support Arabic fields',
        'migrated' => true
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


