<?php
/**
 * AGGRESSIVE IMAGE OPTIMIZATION
 * Compresses images in-place without breaking anything
 * Works on all pages automatically
 */

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$images_to_optimize = [
    'heroImage/img-4.webp' => 250000,
    'assest/img-3.webp' => 120000,
    'assest/img-4.webp' => 120000,
];

function compress_image_aggressive($source, $target_size) {
    if (!file_exists($source)) {
        return ['success' => false, 'error' => 'File not found'];
    }
    
    $original_size = filesize($source);
    
    // Try with GD Library
    if (extension_loaded('gd')) {
        try {
            $image_info = getimagesize($source);
            $image_type = $image_info[2];
            
            if ($image_type == IMAGETYPE_WEBP) {
                $image = imagecreatefromwebp($source);
            } elseif ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($source);
            } elseif ($image_type == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($source);
            } else {
                return ['success' => false, 'error' => 'Unsupported format'];
            }
            
            if (!$image) {
                return ['success' => false, 'error' => 'Failed to load image'];
            }
            
            // Aggressive compression
            $quality = 70;
            $best_data = null;
            $best_size = $original_size;
            
            while ($quality >= 40) {
                ob_start();
                imagewebp($image, null, $quality);
                $compressed = ob_get_clean();
                $size = strlen($compressed);
                
                if ($size < $best_size) {
                    $best_size = $size;
                    $best_data = $compressed;
                }
                
                if ($size <= $target_size) {
                    break;
                }
                
                $quality -= 5;
            }
            
            if ($best_data) {
                file_put_contents($source, $best_data);
                imagedestroy($image);
                
                return [
                    'success' => true,
                    'original' => $original_size,
                    'compressed' => $best_size,
                    'reduction' => round((1 - $best_size / $original_size) * 100, 1)
                ];
            }
            
            imagedestroy($image);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    return ['success' => false, 'error' => 'GD Library not available'];
}

// Process all images
$results = [];
foreach ($images_to_optimize as $image => $target) {
    $result = compress_image_aggressive($image, $target);
    $results[$image] = $result;
}

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'results' => $results,
    'message' => 'Images optimized successfully'
]);
?>

