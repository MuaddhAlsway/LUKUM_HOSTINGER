<?php
/**
 * Sync Messika images from eventfile to database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database not connected');
    }
    
    $eventId = 51;
    $eventTitle = 'Messika';
    $sourceFolder = '../eventfile/Messik';
    $uploadFolder = '../uploads/events';
    
    // Create upload folder if it doesn't exist
    if (!is_dir($uploadFolder)) {
        mkdir($uploadFolder, 0755, true);
    }
    
    // Get all images from Messik folder
    $images = glob($sourceFolder . '/*.{webp,jpg,jpeg,png}', GLOB_BRACE);
    
    if (empty($images)) {
        throw new Exception('No images found in ' . $sourceFolder);
    }
    
    $uploadedImages = [];
    
    foreach ($images as $index => $imagePath) {
        $filename = basename($imagePath);
        $newFilename = 'messika-' . ($index + 1) . '-' . time() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
        $destPath = $uploadFolder . '/' . $newFilename;
        
        // Copy image to uploads folder
        if (copy($imagePath, $destPath)) {
            $dbPath = 'uploads/events/' . $newFilename;
            $uploadedImages[] = $dbPath;
            
            // If first image, set as cover image
            if ($index === 0) {
                $updateCover = 'UPDATE events SET cover_image = ? WHERE id = ?';
                $stmt = $db->prepare($updateCover);
                $stmt->bind_param('si', $dbPath, $eventId);
                $stmt->execute();
            }
            
            // Add to gallery
            $insertGallery = 'INSERT INTO event_gallery (event_id, image_url, display_order) VALUES (?, ?, ?)';
            $stmt = $db->prepare($insertGallery);
            $displayOrder = $index + 1;
            $stmt->bind_param('isi', $eventId, $dbPath, $displayOrder);
            $stmt->execute();
        }
    }
    
    // Clear old mock images from gallery
    $deleteMock = 'DELETE FROM event_gallery WHERE event_id = ? AND image_url = ?';
    $stmt = $db->prepare($deleteMock);
    $mockImage = 'assest/img-4.png';
    $stmt->bind_param('is', $eventId, $mockImage);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Messika images synced successfully',
        'images_uploaded' => count($uploadedImages),
        'images' => $uploadedImages
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

