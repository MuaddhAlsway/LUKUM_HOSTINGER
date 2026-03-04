<?php
/**
 * LAKUM Artspace - Mobile-First Batch Image Optimizer
 * Optimizes images specifically for mobile performance
 * Generates 400w (mobile), 800w (tablet), 1200w (desktop) variants
 * Mobile-optimized compression (quality 60-70)
 * 
 * Usage: php api/mobile-batch-optimizer.php
 */

set_time_limit(300);
ini_set('memory_limit', '512M');

class MobileBatchOptimizer {
    private $sourceDir;
    private $outputDir;
    private $sizes = [
        400 => 65,   // Mobile: quality 65
        800 => 70,   // Tablet: quality 70
        1200 => 75   // Desktop: quality 75
    ];
    private $log = [];
    private $stats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'originalSize' => 0,
        'optimizedSize' => 0,
        'mobileSize' => 0,
        'tabletSize' => 0,
        'desktopSize' => 0
    ];

    public function __construct() {
        $this->outputDir = __DIR__ . '/../optimized-images';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    /**
     * Optimize all images in a directory
     */
    public function optimizeDirectory($sourceDir, $pattern = '*.{jpg,jpeg,png,webp}') {
        $this->sourceDir = $sourceDir;
        
        if (!is_dir($sourceDir)) {
            $this->addLog("ERROR: Directory not found: $sourceDir");
            return false;
        }

        $this->addLog("Starting mobile optimization of: $sourceDir");
        
        $files = glob($sourceDir . '/' . $pattern, GLOB_BRACE);
        
        if (empty($files)) {
            $this->addLog("No images found in: $sourceDir");
            return false;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                $this->optimizeImage($file);
            }
        }

        return true;
    }

    /**
     * Optimize a single image for mobile
     */
    private function optimizeImage($filePath) {
        $filename = basename($filePath);
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $name = pathinfo($filePath, PATHINFO_FILENAME);
        
        // Skip if already optimized
        if ($ext === 'webp' && strpos($name, '-400w') !== false) {
            $this->stats['skipped']++;
            return;
        }

        try {
            $originalSize = filesize($filePath);
            $this->stats['originalSize'] += $originalSize;

            $relPath = str_replace(dirname(__DIR__), '', dirname($filePath));
            $outputSubDir = $this->outputDir . $relPath;
            if (!is_dir($outputSubDir)) {
                mkdir($outputSubDir, 0755, true);
            }

            // Generate mobile-first variants
            foreach ($this->sizes as $width => $quality) {
                $outputFile = $outputSubDir . '/' . $name . '-' . $width . 'w.webp';
                
                if ($this->resizeAndCompress($filePath, $outputFile, $width, $quality)) {
                    $optimizedSize = filesize($outputFile);
                    $this->stats['optimizedSize'] += $optimizedSize;
                    
                    // Track by device
                    if ($width === 400) {
                        $this->stats['mobileSize'] += $optimizedSize;
                    } elseif ($width === 800) {
                        $this->stats['tabletSize'] += $optimizedSize;
                    } else {
                        $this->stats['desktopSize'] += $optimizedSize;
                    }
                    
                    $this->addLog("✓ Mobile: $name-{$width}w.webp (" . $this->formatSize($optimizedSize) . ", Q$quality)");
                } else {
                    $this->stats['errors']++;
                    $this->addLog("✗ Failed: $name-{$width}w.webp");
                }
            }

            $this->stats['processed']++;
            $reduction = round((1 - ($this->stats['optimizedSize'] / $this->stats['originalSize'])) * 100);
            $this->addLog("Processed: $filename (Reduction: {$reduction}%)");

        } catch (Exception $e) {
            $this->stats['errors']++;
            $this->addLog("ERROR: " . $e->getMessage());
        }
    }

    /**
     * Resize and compress image to WebP with mobile-optimized quality
     */
    private function resizeAndCompress($source, $destination, $width, $quality) {
        if (!extension_loaded('gd')) {
            return $this->resizeWithImageMagick($source, $destination, $width, $quality);
        }

        try {
            $image = $this->loadImage($source);
            if (!$image) {
                return false;
            }

            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            $ratio = $origHeight / $origWidth;
            $newWidth = min($width, $origWidth);
            $newHeight = round($newWidth * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            
            if (strpos(mime_content_type($source), 'png') !== false) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Use mobile-optimized quality
            imagewebp($resized, $destination, $quality);
            imagedestroy($image);
            imagedestroy($resized);

            return file_exists($destination);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Load image based on type
     */
    private function loadImage($filePath) {
        $mime = mime_content_type($filePath);
        
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            default:
                return null;
        }
    }

    /**
     * Fallback: Use ImageMagick
     */
    private function resizeWithImageMagick($source, $destination, $width, $quality) {
        $cmd = sprintf(
            'convert "%s" -resize %dx -quality %d -strip "%s"',
            escapeshellarg($source),
            $width,
            $quality,
            escapeshellarg($destination)
        );

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        return $returnCode === 0 && file_exists($destination);
    }

    /**
     * Format file size
     */
    private function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Add log message
     */
    private function addLog($message) {
        $this->log[] = $message;
        echo $message . "\n";
    }

    /**
     * Get statistics
     */
    public function getStats() {
        return [
            'processed' => $this->stats['processed'],
            'skipped' => $this->stats['skipped'],
            'errors' => $this->stats['errors'],
            'originalSize' => $this->formatSize($this->stats['originalSize']),
            'optimizedSize' => $this->formatSize($this->stats['optimizedSize']),
            'mobileSize' => $this->formatSize($this->stats['mobileSize']),
            'tabletSize' => $this->formatSize($this->stats['tabletSize']),
            'desktopSize' => $this->formatSize($this->stats['desktopSize']),
            'reduction' => round((1 - ($this->stats['optimizedSize'] / max($this->stats['originalSize'], 1))) * 100) . '%'
        ];
    }

    /**
     * Get log
     */
    public function getLog() {
        return $this->log;
    }
}

// Run optimization
if (php_sapi_name() === 'cli') {
    echo "================================================================================\n";
    echo "  LAKUM ARTSPACE - MOBILE-FIRST BATCH IMAGE OPTIMIZER\n";
    echo "  Optimized for Mobile Performance (Lighthouse 85+)\n";
    echo "================================================================================\n\n";

    $optimizer = new MobileBatchOptimizer();

    $directories = [
        __DIR__ . '/../assest/gallery',
        __DIR__ . '/../assest/blog-uploads',
        __DIR__ . '/../assest/press-uploads',
        __DIR__ . '/../uploads/covers',
        __DIR__ . '/../assest',
        __DIR__ . '/../assest/logo',
        __DIR__ . '/../assest/Space'
    ];

    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            echo "\n--- Processing: $dir ---\n";
            $optimizer->optimizeDirectory($dir);
        }
    }

    $stats = $optimizer->getStats();
    echo "\n================================================================================\n";
    echo "  MOBILE OPTIMIZATION COMPLETE\n";
    echo "================================================================================\n";
    echo "Processed:      " . $stats['processed'] . " images\n";
    echo "Skipped:        " . $stats['skipped'] . " images\n";
    echo "Errors:         " . $stats['errors'] . " images\n";
    echo "\nPayload Breakdown:\n";
    echo "  Original:     " . $stats['originalSize'] . "\n";
    echo "  Mobile (400w): " . $stats['mobileSize'] . "\n";
    echo "  Tablet (800w): " . $stats['tabletSize'] . "\n";
    echo "  Desktop (1200w): " . $stats['desktopSize'] . "\n";
    echo "  Total:        " . $stats['optimizedSize'] . "\n";
    echo "  Reduction:    " . $stats['reduction'] . "\n";
    echo "================================================================================\n";
    echo "\nOptimized images saved to: optimized-images/\n";
    echo "Mobile users will download: " . $stats['mobileSize'] . " (vs " . $stats['originalSize'] . " before)\n";
    echo "Next: Update HTML to use mobile-first responsive images\n";
}
?>

