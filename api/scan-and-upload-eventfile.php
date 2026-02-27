<?php
/**
 * LAKUM Artspace - Scan and Upload Event Images
 * Scans eventfile folder and uploads all images to gallery
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $eventfileDir = '../eventfile';
    $uploadDir = '../uploads/events';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedImages = [];
    $errors = [];
    $totalScanned = 0;
    
    // Scan all subdirectories in eventfile
    $folders = scandir($eventfileDir);
    
    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..') continue;
        
        $folderPath = $eventfileDir . '/' . $folder;
        
        if (!is_dir($folderPath)) continue;
        
        // Scan images in this folder
        $files = scandir($folderPath);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $filePath = $folderPath . '/' . $file;
            
            // Check if it's an image file
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $imageExtensions)) continue;
            
            $totalScanned++;
            
            // Generate unique filename
            $timestamp = time();
            $randomStr = substr(md5(rand()), 0, 8);
            $newFilename = 'event-' . $timestamp . '-' . $randomStr . '.' . $fileExtension;
            $destinationPath = $uploadDir . '/' . $newFilename;
            
            // Copy file
            if (copy($filePath, $destinationPath)) {
                // Convert to webp if not already
                if ($fileExtension !== 'webp') {
                    $webpPath = $uploadDir . '/' . pathinfo($newFilename, PATHINFO_FILENAME) . '.webp';
                    convertToWebp($destinationPath, $webpPath);
                    if (file_exists($webpPath)) {
                        unlink($destinationPath);
                        $newFilename = pathinfo($webpPath, PATHINFO_BASENAME);
                    }
                }
                
                $uploadedImages[] = [
                    'filename' => $newFilename,
                    'source_folder' => $folder,
                    'source_file' => $file,
                    'path' => 'uploads/events/' . $newFilename
                ];
            } else {
                $errors[] = "Failed to copy: $folder/$file";
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Scan and upload complete',
        'total_scanned' => $totalScanned,
        'total_uploaded' => count($uploadedImages),
        'uploaded_images' => $uploadedImages,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log('Scan Upload Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Convert image to WebP format
 */
function convertToWebp($sourcePath, $destinationPath) {
    try {
        if (extension_loaded('imagick')) {
            $imagick = new Imagick($sourcePath);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(80);
            $imagick->writeImage($destinationPath);
            $imagick->destroy();
            return true;
        } elseif (extension_loaded('gd')) {
            $imageInfo = getimagesize($sourcePath);
            $mime = $imageInfo['mime'];
            
            if ($mime === 'image/jpeg') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mime === 'image/png') {
                $image = imagecreatefrompng($sourcePath);
            } elseif ($mime === 'image/gif') {
                $image = imagecreatefromgif($sourcePath);
            } else {
                return false;
            }
            
            if ($image) {
                imagewebp($image, $destinationPath, 80);
                imagedestroy($image);
                return true;
            }
        }
        return false;
    } catch (Exception $e) {
        error_log('WebP conversion error: ' . $e->getMessage());
        return false;
    }
}
?>
