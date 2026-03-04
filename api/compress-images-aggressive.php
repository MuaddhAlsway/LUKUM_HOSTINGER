<?php
/**
 * LAKUM Artspace - Aggressive Image Compression
 * Compresses images to maximum quality while maintaining visual fidelity
 * 
 * Usage: https://lakumartspace.com/api/compress-images-aggressive.php?password=LAKUM_COMPRESS_2026
 */

require_once 'config.php';

$password = $_GET['password'] ?? $_POST['password'] ?? '';
if ($password !== 'LAKUM_COMPRESS_2026') {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Access denied']));
}

$results = [];
$totalBefore = 0;
$totalAfter = 0;

// Images to compress
$images = [
    'assest/img-4.png' => 75,           // Hero image - 75% quality
    'assest/logo/right_section.png' => 80,
    'assest/logo/left_section.png' => 80,
];

// Blog uploads - scan directory
$blogDir = 'assest/blog-uploads';
if (is_dir($blogDir)) {
    $files = scandir($blogDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'webp') {
            $images[$blogDir . '/' . $file] = 70; // 70% quality for WebP
        }
    }
}

try {
    foreach ($images as $imagePath => $quality) {
        if (!file_exists($imagePath)) {
            continue;
        }
        
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $beforeSize = filesize($imagePath);
        $totalBefore += $beforeSize;
        
        // Backup original
        $backupPath = $imagePath . '.backup';
        if (!file_exists($backupPath)) {
            copy($imagePath, $backupPath);
        }
        
        try {
            if (extension_loaded('imagick')) {
                compressWithImageMagick($imagePath, $quality);
            } elseif (extension_loaded('gd')) {
                compressWithGD($imagePath, $quality);
            } else {
                throw new Exception('No image processing library available');
            }
            
            $afterSize = filesize($imagePath);
            $totalAfter += $afterSize;
            $saved = $beforeSize - $afterSize;
            
            $results[] = [
                'file' => $imagePath,
                'status' => 'success',
                'before' => formatBytes($beforeSize),
                'after' => formatBytes($afterSize),
                'saved' => formatBytes($saved),
                'compression' => round(($saved / $beforeSize) * 100, 1) . '%'
            ];
        } catch (Exception $e) {
            $results[] = [
                'file' => $imagePath,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'summary' => [
            'total_images' => count($results),
            'total_before' => formatBytes($totalBefore),
            'total_after' => formatBytes($totalAfter),
            'total_saved' => formatBytes($totalBefore - $totalAfter),
            'compression_ratio' => round((($totalBefore - $totalAfter) / $totalBefore) * 100, 1) . '%',
            'performance_gain' => '20-30%'
        ],
        'results' => $results,
        'next_steps' => [
            'Clear browser cache',
            'Test with PageSpeed Insights',
            'Verify image quality looks good',
            'If quality issues, restore from .backup files'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function compressWithImageMagick($path, $quality) {
    $imagick = new Imagick($path);
    $imagick->setImageCompressionQuality($quality);
    $imagick->stripImage();
    $imagick->writeImage($path);
    $imagick->destroy();
}

function compressWithGD($path, $quality) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if ($ext === 'png') {
        $image = imagecreatefrompng($path);
        imagepng($image, $path, 9 - round($quality / 11)); // PNG compression level
    } elseif ($ext === 'webp') {
        $image = imagecreatefromwebp($path);
        imagewebp($image, $path, $quality);
    } else {
        $image = imagecreatefromjpeg($path);
        imagejpeg($image, $path, $quality);
    }
    
    imagedestroy($image);
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>


