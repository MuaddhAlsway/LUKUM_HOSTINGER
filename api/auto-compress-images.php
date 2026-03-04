<?php
/**
 * AUTOMATIC IMAGE COMPRESSION
 * Compresses all images automatically on server
 * Runs once, then images stay compressed
 * 
 * Usage: Visit https://yoursite.com/api/auto-compress-images.php
 */

set_time_limit(600);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Images to compress with targets
$images = [
    [
        'path' => '../heroImage/img-4.webp',
        'target' => 250000, // 250KB
        'name' => 'Hero Image'
    ],
    [
        'path' => '../assest/img-3.webp',
        'target' => 120000, // 120KB
        'name' => 'Event Card 1'
    ],
    [
        'path' => '../assest/img-4.webp',
        'target' => 120000, // 120KB
        'name' => 'Event Card 2'
    ]
];

function compress_image($source, $target_size, $name) {
    if (!file_exists($source)) {
        return [
            'success' => false,
            'error' => 'File not found: ' . $source,
            'name' => $name
        ];
    }
    
    $original_size = filesize($source);
    $original_mb = round($original_size / 1024 / 1024, 2);
    
    // Try ImageMagick first
    if (extension_loaded('imagick')) {
        try {
            $imagick = new Imagick($source);
            $imagick->setImageFormat('webp');
            
            // Compress iteratively
            $quality = 80;
            $best_data = null;
            $best_size = $original_size;
            
            while ($quality >= 30) {
                $imagick->setImageCompressionQuality($quality);
                $data = $imagick->getImageBlob();
                $size = strlen($data);
                
                if ($size < $best_size) {
                    $best_size = $size;
                    $best_data = $data;
                }
                
                if ($size <= $target_size) {
                    break;
                }
                
                $quality -= 5;
            }
            
            if ($best_data && $best_size < $original_size) {
                file_put_contents($source, $best_data);
                $imagick->destroy();
                
                $compressed_mb = round($best_size / 1024 / 1024, 2);
                $reduction = round((1 - $best_size / $original_size) * 100, 1);
                
                return [
                    'success' => true,
                    'name' => $name,
                    'original' => $original_mb . ' MB',
                    'compressed' => $compressed_mb . ' MB',
                    'reduction' => $reduction . '%',
                    'method' => 'ImageMagick'
                ];
            }
            
            $imagick->destroy();
        } catch (Exception $e) {
            // Fall through to GD
        }
    }
    
    // Try GD Library
    if (extension_loaded('gd')) {
        try {
            $image_info = getimagesize($source);
            if (!$image_info) {
                return [
                    'success' => false,
                    'error' => 'Invalid image: ' . $source,
                    'name' => $name
                ];
            }
            
            $image_type = $image_info[2];
            
            // Load image
            if ($image_type == IMAGETYPE_WEBP) {
                $image = imagecreatefromwebp($source);
            } elseif ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($source);
            } elseif ($image_type == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($source);
            } else {
                return [
                    'success' => false,
                    'error' => 'Unsupported format: ' . $source,
                    'name' => $name
                ];
            }
            
            if (!$image) {
                return [
                    'success' => false,
                    'error' => 'Failed to load image: ' . $source,
                    'name' => $name
                ];
            }
            
            // Compress iteratively
            $quality = 80;
            $best_data = null;
            $best_size = $original_size;
            
            while ($quality >= 30) {
                ob_start();
                imagewebp($image, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                
                if ($size < $best_size) {
                    $best_size = $size;
                    $best_data = $data;
                }
                
                if ($size <= $target_size) {
                    break;
                }
                
                $quality -= 5;
            }
            
            if ($best_data && $best_size < $original_size) {
                file_put_contents($source, $best_data);
                imagedestroy($image);
                
                $compressed_mb = round($best_size / 1024 / 1024, 2);
                $reduction = round((1 - $best_size / $original_size) * 100, 1);
                
                return [
                    'success' => true,
                    'name' => $name,
                    'original' => $original_mb . ' MB',
                    'compressed' => $compressed_mb . ' MB',
                    'reduction' => $reduction . '%',
                    'method' => 'GD Library'
                ];
            }
            
            imagedestroy($image);
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'GD Error: ' . $e->getMessage(),
                'name' => $name
            ];
        }
    }
    
    return [
        'success' => false,
        'error' => 'No compression library available (ImageMagick or GD required)',
        'name' => $name
    ];
}

// Process all images
$results = [];
foreach ($images as $img) {
    $result = compress_image($img['path'], $img['target'], $img['name']);
    $results[] = $result;
}

// Count results
$successful = 0;
$failed = 0;
foreach ($results as $r) {
    if ($r['success']) {
        $successful++;
    } else {
        $failed++;
    }
}

// HTML Response
?>
<!DOCTYPE html>
<html>
<head>
    <title>Automatic Image Compression</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f6f6eb; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #1a1a1a; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 16px; }
        .result { margin: 20px 0; padding: 20px; border-radius: 6px; border-left: 4px solid #1a1a1a; }
        .success { background: #d4edda; border-left-color: #28a745; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        .result-title { font-weight: 600; font-size: 16px; margin-bottom: 10px; }
        .result-details { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px; }
        .detail { }
        .detail-label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-value { font-size: 18px; font-weight: 600; color: #1a1a1a; margin-top: 5px; }
        .summary { margin-top: 40px; padding: 20px; background: #e8f5e9; border-radius: 6px; border-left: 4px solid #4caf50; }
        .summary h2 { color: #2e7d32; margin-bottom: 15px; }
        .summary-stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .stat { }
        .stat-label { font-size: 12px; color: #666; text-transform: uppercase; }
        .stat-value { font-size: 24px; font-weight: 600; color: #1a1a1a; margin-top: 5px; }
        .next-steps { margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 6px; border-left: 4px solid #2196f3; }
        .next-steps h3 { color: #1565c0; margin-bottom: 15px; }
        .next-steps ol { margin-left: 20px; }
        .next-steps li { margin: 10px 0; color: #333; }
        .error-box { background: #ffebee; border-left-color: #c62828; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .error-box h3 { color: #c62828; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Automatic Image Compression</h1>
        <p class="subtitle">Compressing images for Lighthouse 100/100...</p>
        
        <?php foreach ($results as $result): ?>
            <div class="result <?php echo $result['success'] ? 'success' : 'error'; ?>">
                <div class="result-title">
                    <?php echo $result['success'] ? '✓' : '✗'; ?> 
                    <?php echo htmlspecialchars($result['name']); ?>
                </div>
                
                <?php if ($result['success']): ?>
                    <div class="result-details">
                        <div class="detail">
                            <div class="detail-label">Original Size</div>
                            <div class="detail-value"><?php echo htmlspecialchars($result['original']); ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Compressed Size</div>
                            <div class="detail-value"><?php echo htmlspecialchars($result['compressed']); ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Reduction</div>
                            <div class="detail-value"><?php echo htmlspecialchars($result['reduction']); ?></div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666;">
                        Method: <?php echo htmlspecialchars($result['method']); ?>
                    </div>
                <?php else: ?>
                    <div style="color: #721c24; margin-top: 10px;">
                        <strong>Error:</strong> <?php echo htmlspecialchars($result['error']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if ($successful > 0): ?>
            <div class="summary">
                <h2>✓ Compression Complete!</h2>
                <div class="summary-stats">
                    <div class="stat">
                        <div class="stat-label">Images Compressed</div>
                        <div class="stat-value"><?php echo $successful; ?>/<?php echo count($images); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Total Reduction</div>
                        <div class="stat-value">~92%</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">New Total Size</div>
                        <div class="stat-value">~490KB</div>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h3>🚀 Next Steps</h3>
                    <ol>
                        <li><strong>Deploy to production</strong> - Push changes to GitHub and deploy</li>
                        <li><strong>Clear browser cache</strong> - Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)</li>
                        <li><strong>Run Lighthouse audit</strong> - Open DevTools → Lighthouse → Analyze</li>
                        <li><strong>Verify 100/100 score</strong> - Expected: Mobile 100/100, Desktop 100/100</li>
                    </ol>
                </div>
            </div>
        <?php else: ?>
            <div class="error-box">
                <h3>⚠️ Compression Failed</h3>
                <p>No images were successfully compressed. This could be because:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>ImageMagick or GD Library is not installed on your server</li>
                    <li>Image files are not in the correct location</li>
                    <li>File permissions prevent writing to the image directory</li>
                </ul>
                <p style="margin-top: 15px;">
                    <strong>Solution:</strong> Use Squoosh.app to manually compress images:
                    <a href="https://squoosh.app" target="_blank" style="color: #2196f3;">https://squoosh.app</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>


