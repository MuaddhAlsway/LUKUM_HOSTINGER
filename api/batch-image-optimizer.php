<?php
/**
 * LAKUM Artspace - Batch Image Optimizer
 * Converts, resizes, and compresses all images to WebP format
 * Generates responsive variants (400w, 800w, 1200w)
 * 
 * Usage: php api/batch-image-optimizer.php
 */

set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');

class BatchImageOptimizer {
    private $sourceDir;
    private $outputDir;
    private $sizes = [400, 800, 1200];
    private $quality = 75;
    private $log = [];
    private $stats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'originalSize' => 0,
        'optimizedSize' => 0
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

        $this->addLog("Starting optimization of: $sourceDir");
        
        // Get all matching files
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
     * Optimize a single image
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
            // Get original size
            $originalSize = filesize($filePath);
            $this->stats['originalSize'] += $originalSize;

            // Create output subdirectory
            $relPath = str_replace(dirname(__DIR__), '', dirname($filePath));
            $outputSubDir = $this->outputDir . $relPath;
            if (!is_dir($outputSubDir)) {
                mkdir($outputSubDir, 0755, true);
            }

            // Generate responsive variants
            foreach ($this->sizes as $size) {
                $outputFile = $outputSubDir . '/' . $name . '-' . $size . 'w.webp';
                
                if ($this->resizeAndCompress($filePath, $outputFile, $size)) {
                    $optimizedSize = filesize($outputFile);
                    $this->stats['optimizedSize'] += $optimizedSize;
                    $this->addLog("✓ Created: $name-{$size}w.webp (" . $this->formatSize($optimizedSize) . ")");
                } else {
                    $this->stats['errors']++;
                    $this->addLog("✗ Failed: $name-{$size}w.webp");
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
     * Resize and compress image to WebP
     */
    private function resizeAndCompress($source, $destination, $width) {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            return $this->resizeWithImageMagick($source, $destination, $width);
        }

        try {
            // Load image
            $image = $this->loadImage($source);
            if (!$image) {
                return false;
            }

            // Get original dimensions
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            // Calculate new dimensions (maintain aspect ratio)
            $ratio = $origHeight / $origWidth;
            $newWidth = min($width, $origWidth);
            $newHeight = round($newWidth * $ratio);

            // Create new image
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if (strpos(mime_content_type($source), 'png') !== false) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            // Resize
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Save as WebP
            imagewebp($resized, $destination, $this->quality);
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
     * Fallback: Use ImageMagick if GD not available
     */
    private function resizeWithImageMagick($source, $destination, $width) {
        $cmd = sprintf(
            'convert "%s" -resize %dx -quality %d -strip "%s"',
            escapeshellarg($source),
            $width,
            $this->quality,
            escapeshellarg($destination)
        );

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        return $returnCode === 0 && file_exists($destination);
    }

    /**
     * Format file size for display
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
    echo "  LAKUM ARTSPACE - BATCH IMAGE OPTIMIZER\n";
    echo "================================================================================\n\n";

    $optimizer = new BatchImageOptimizer();

    // Optimize all image directories
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

    // Print statistics
    $stats = $optimizer->getStats();
    echo "\n================================================================================\n";
    echo "  OPTIMIZATION COMPLETE\n";
    echo "================================================================================\n";
    echo "Processed:      " . $stats['processed'] . " images\n";
    echo "Skipped:        " . $stats['skipped'] . " images\n";
    echo "Errors:         " . $stats['errors'] . " images\n";
    echo "Original Size:  " . $stats['originalSize'] . "\n";
    echo "Optimized Size: " . $stats['optimizedSize'] . "\n";
    echo "Reduction:      " . $stats['reduction'] . "\n";
    echo "================================================================================\n";
    echo "\nOptimized images saved to: optimized-images/\n";
    echo "Next: Update HTML to use responsive images with srcset and sizes\n";
}
?>


