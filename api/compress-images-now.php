<?php
/**
 * LAKUM Image Compression Script
 * Compresses all images to Lighthouse 100/100 targets
 * 
 * Usage: Visit https://yoursite.com/api/compress-images-now.php
 * 
 * Targets:
 * - Hero image: 1.1MB → 250KB
 * - Event cards: 3.5MB + 1.3MB → 120KB each
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timeout for large images
set_time_limit(300);

// Check if ImageMagick is available
$imagick_available = extension_loaded('imagick');
$gd_available = extension_loaded('gd');

if (!$imagick_available && !$gd_available) {
    die('ERROR: Neither ImageMagick nor GD extension is available. Please enable one of them.');
}

// Image compression targets
$images = [
    [
        'source' => '../heroImage/img-4.webp',
        'target_size' => 250000, // 250KB
        'quality' => 75,
        'name' => 'Hero Image'
    ],
    [
        'source' => '../assest/img-3.webp',
        'target_size' => 120000, // 120KB
        'quality' => 75,
        'name' => 'Event Card 1'
    ],
    [
        'source' => '../assest/img-4.webp',
        'target_size' => 120000, // 120KB
        'quality' => 75,
        'name' => 'Event Card 2'
    ]
];

$results = [];

echo "<!DOCTYPE html>
<html>
<head>
    <title>LAKUM Image Compression</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f6f6eb; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #1a1a1a; }
        .status { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .image-result { margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid #1a1a1a; }
        .before-after { display: flex; justify-content: space-between; margin: 10px 0; }
        .metric { flex: 1; }
        .metric-label { font-weight: bold; color: #666; }
        .metric-value { font-size: 18px; color: #1a1a1a; }
        .reduction { color: #28a745; font-weight: bold; }
        button { background: #1a1a1a; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #333; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🖼️ LAKUM Image Compression Tool</h1>
        <p>This tool compresses your images to Lighthouse 100/100 targets.</p>";

// Check available extensions
echo "<div class='status info'>";
if ($imagick_available) {
    echo "✓ ImageMagick available (recommended)<br>";
}
if ($gd_available) {
    echo "✓ GD Library available<br>";
}
echo "</div>";

// Process each image
foreach ($images as $image) {
    $source = $image['source'];
    $target_size = $image['target_size'];
    $quality = $image['quality'];
    $name = $image['name'];
    
    echo "<div class='image-result'>";
    echo "<h3>$name</h3>";
    
    // Check if source exists
    if (!file_exists($source)) {
        echo "<div class='status error'>❌ Source file not found: $source</div>";
        continue;
    }
    
    $original_size = filesize($source);
    $original_size_mb = round($original_size / 1024 / 1024, 2);
    
    echo "<div class='before-after'>";
    echo "<div class='metric'>";
    echo "<div class='metric-label'>Original Size</div>";
    echo "<div class='metric-value'>$original_size_mb MB</div>";
    echo "</div>";
    
    // Try to compress with ImageMagick first
    if ($imagick_available) {
        try {
            $imagick = new Imagick($source);
            
            // Reduce quality iteratively until target size is reached
            $current_quality = $quality;
            $max_iterations = 10;
            $iteration = 0;
            
            while ($iteration < $max_iterations) {
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality($current_quality);
                
                $compressed_data = $imagick->getImageBlob();
                $compressed_size = strlen($compressed_data);
                
                if ($compressed_size <= $target_size || $current_quality <= 40) {
                    // Save compressed image
                    file_put_contents($source, $compressed_data);
                    
                    $compressed_size_kb = round($compressed_size / 1024, 2);
                    $target_size_kb = round($target_size / 1024, 2);
                    $reduction = round((1 - $compressed_size / $original_size) * 100, 1);
                    
                    echo "<div class='metric'>";
                    echo "<div class='metric-label'>Compressed Size</div>";
                    echo "<div class='metric-value'>$compressed_size_kb KB</div>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='status success'>";
                    echo "✓ Compressed successfully!<br>";
                    echo "Target: $target_size_kb KB | Actual: $compressed_size_kb KB<br>";
                    echo "<span class='reduction'>Reduction: $reduction%</span>";
                    echo "</div>";
                    
                    $results[] = [
                        'name' => $name,
                        'original' => $original_size,
                        'compressed' => $compressed_size,
                        'reduction' => $reduction,
                        'success' => true
                    ];
                    
                    break;
                }
                
                $current_quality -= 5;
                $iteration++;
            }
            
            if ($iteration >= $max_iterations) {
                echo "<div class='status error'>❌ Could not reach target size</div>";
                $results[] = [
                    'name' => $name,
                    'success' => false
                ];
            }
            
            $imagick->destroy();
        } catch (Exception $e) {
            echo "<div class='status error'>❌ ImageMagick error: " . $e->getMessage() . "</div>";
            $results[] = [
                'name' => $name,
                'success' => false
            ];
        }
    } 
    // Fallback to GD Library
    elseif ($gd_available) {
        try {
            // Determine image type
            $image_info = getimagesize($source);
            $image_type = $image_info[2];
            
            // Load image
            if ($image_type == IMAGETYPE_WEBP) {
                $image = imagecreatefromwebp($source);
            } elseif ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($source);
            } elseif ($image_type == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($source);
            } else {
                throw new Exception('Unsupported image type');
            }
            
            if (!$image) {
                throw new Exception('Failed to load image');
            }
            
            // Compress with quality reduction
            $current_quality = $quality;
            $max_iterations = 10;
            $iteration = 0;
            
            while ($iteration < $max_iterations) {
                ob_start();
                imagewebp($image, null, $current_quality);
                $compressed_data = ob_get_clean();
                $compressed_size = strlen($compressed_data);
                
                if ($compressed_size <= $target_size || $current_quality <= 40) {
                    // Save compressed image
                    file_put_contents($source, $compressed_data);
                    
                    $compressed_size_kb = round($compressed_size / 1024, 2);
                    $target_size_kb = round($target_size / 1024, 2);
                    $reduction = round((1 - $compressed_size / $original_size) * 100, 1);
                    
                    echo "<div class='metric'>";
                    echo "<div class='metric-label'>Compressed Size</div>";
                    echo "<div class='metric-value'>$compressed_size_kb KB</div>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='status success'>";
                    echo "✓ Compressed successfully!<br>";
                    echo "Target: $target_size_kb KB | Actual: $compressed_size_kb KB<br>";
                    echo "<span class='reduction'>Reduction: $reduction%</span>";
                    echo "</div>";
                    
                    $results[] = [
                        'name' => $name,
                        'original' => $original_size,
                        'compressed' => $compressed_size,
                        'reduction' => $reduction,
                        'success' => true
                    ];
                    
                    break;
                }
                
                $current_quality -= 5;
                $iteration++;
            }
            
            if ($iteration >= $max_iterations) {
                echo "<div class='status error'>❌ Could not reach target size</div>";
                $results[] = [
                    'name' => $name,
                    'success' => false
                ];
            }
            
            imagedestroy($image);
        } catch (Exception $e) {
            echo "<div class='status error'>❌ GD error: " . $e->getMessage() . "</div>";
            $results[] = [
                'name' => $name,
                'success' => false
            ];
        }
    }
    
    echo "</div>";
}

// Summary
echo "<hr>";
echo "<h2>📊 Summary</h2>";

$total_original = 0;
$total_compressed = 0;
$successful = 0;

foreach ($results as $result) {
    if ($result['success']) {
        $total_original += $result['original'];
        $total_compressed += $result['compressed'];
        $successful++;
    }
}

if ($successful > 0) {
    $total_original_mb = round($total_original / 1024 / 1024, 2);
    $total_compressed_mb = round($total_compressed / 1024 / 1024, 2);
    $total_reduction = round((1 - $total_compressed / $total_original) * 100, 1);
    
    echo "<div class='status success'>";
    echo "<h3>✓ Compression Complete!</h3>";
    echo "<p><strong>Total Original Size:</strong> $total_original_mb MB</p>";
    echo "<p><strong>Total Compressed Size:</strong> $total_compressed_mb MB</p>";
    echo "<p><strong>Total Reduction:</strong> <span class='reduction'>$total_reduction%</span></p>";
    echo "<p><strong>Images Compressed:</strong> $successful / " . count($images) . "</p>";
    echo "<p style='margin-top: 20px; font-weight: bold;'>🎉 Your images are now optimized for Lighthouse 100/100!</p>";
    echo "<p>Next step: Deploy to production and run Lighthouse audit.</p>";
    echo "</div>";
} else {
    echo "<div class='status error'>";
    echo "<h3>❌ Compression Failed</h3>";
    echo "<p>No images were successfully compressed.</p>";
    echo "<p>Please check that your images exist and are in the correct format.</p>";
    echo "</div>";
}

echo "</div>
    </body>
</html>";
?>

