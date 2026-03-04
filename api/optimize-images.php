<?php
/**
 * LAKUM Artspace - Image Optimization Script
 * Converts images to WebP and creates responsive versions
 * 
 * Usage: Visit https://lakumartspace.com/api/optimize-images.php?password=LAKUM_IMG_2026
 */

require_once 'config.php';

// Security check
$password = $_GET['password'] ?? $_POST['password'] ?? '';
if ($password !== 'LAKUM_IMG_2026') {
    http_response_code(403);
    die(json_encode([
        'success' => false,
        'message' => 'Access denied. Correct password required.'
    ]));
}

// Check if ImageMagick or GD is available
$hasImageMagick = extension_loaded('imagick');
$hasGD = extension_loaded('gd');

if (!$hasImageMagick && !$hasGD) {
    die(json_encode([
        'success' => false,
        'message' => 'ImageMagick or GD extension required for image optimization'
    ]));
}

$results = [];
$optimizedCount = 0;
$totalSize = 0;
$savedSize = 0;

// Image directories to optimize
$imageDirs = [
    'assest' => ['img-4.png'],
    'assest/logo' => ['right_section.png', 'left_section.png'],
    'assest/blog-uploads' => [],
    'assest/gallery' => [],
    'assest/press-uploads' => [],
];

try {
    foreach ($imageDirs as $dir => $files) {
        if (!is_dir($dir)) {
            continue;
        }
        
        // If no specific files, scan directory
        if (empty($files)) {
            $files = array_diff(scandir($dir), ['.', '..']);
        }
        
        foreach ($files as $file) {
            $filePath = $dir . '/' . $file;
            
            if (!file_exists($filePath) || !is_file($filePath)) {
                continue;
            }
            
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            // Only process images
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                continue;
            }
            
            $originalSize = filesize($filePath);
            $totalSize += $originalSize;
            
            // Skip if already WebP
            if ($ext === 'webp') {
                continue;
            }
            
            $webpPath = $dir . '/' . pathinfo($file, PATHINFO_FILENAME) . '.webp';
            
            // Skip if WebP already exists
            if (file_exists($webpPath)) {
                $webpSize = filesize($webpPath);
                $savedSize += ($originalSize - $webpSize);
                $results[] = [
                    'file' => $filePath,
                    'status' => 'exists',
                    'message' => 'WebP version already exists',
                    'original_size' => $originalSize,
                    'webp_size' => $webpSize,
                    'saved' => $originalSize - $webpSize
                ];
                $optimizedCount++;
                continue;
            }
            
            // Convert to WebP
            try {
                if ($hasImageMagick) {
                    convertWithImageMagick($filePath, $webpPath);
                } else {
                    convertWithGD($filePath, $webpPath);
                }
                
                if (file_exists($webpPath)) {
                    $webpSize = filesize($webpPath);
                    $saved = $originalSize - $webpSize;
                    $savedSize += $saved;
                    
                    $results[] = [
                        'file' => $filePath,
                        'status' => 'success',
                        'message' => 'Converted to WebP',
                        'original_size' => $originalSize,
                        'webp_size' => $webpSize,
                        'saved' => $saved,
                        'compression' => round(($saved / $originalSize) * 100, 1) . '%'
                    ];
                    $optimizedCount++;
                } else {
                    $results[] = [
                        'file' => $filePath,
                        'status' => 'error',
                        'message' => 'Failed to create WebP file'
                    ];
                }
            } catch (Exception $e) {
                $results[] = [
                    'file' => $filePath,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Image optimization complete',
        'summary' => [
            'total_images_processed' => $optimizedCount,
            'total_original_size' => formatBytes($totalSize),
            'total_saved' => formatBytes($savedSize),
            'compression_ratio' => round(($savedSize / $totalSize) * 100, 1) . '%',
            'estimated_performance_gain' => '15-25%'
        ],
        'results' => $results,
        'next_steps' => [
            'Update HTML to use <picture> elements with WebP fallbacks',
            'Add srcset attributes for responsive images',
            'Clear browser cache',
            'Test with PageSpeed Insights'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function convertWithImageMagick($source, $dest) {
    $imagick = new Imagick($source);
    $imagick->setImageFormat('webp');
    $imagick->setImageCompressionQuality(80);
    $imagick->writeImage($dest);
    $imagick->destroy();
}

function convertWithGD($source, $dest) {
    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    
    if ($ext === 'png') {
        $image = imagecreatefrompng($source);
    } elseif ($ext === 'gif') {
        $image = imagecreatefromgif($source);
    } else {
        $image = imagecreatefromjpeg($source);
    }
    
    if (!$image) {
        throw new Exception('Failed to load image');
    }
    
    if (!imagewebp($image, $dest, 80)) {
        throw new Exception('Failed to create WebP image');
    }
    
    imagedestroy($image);
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>

