<?php
/**
 * Batch Image Optimization Script
 * Converts images to WebP and AVIF formats with multiple sizes
 * 
 * Usage: php optimize-images-batch.php
 */

class ImageOptimizer {
    
    private $sourceDir;
    private $outputDir;
    private $widths = [400, 600, 800, 1200, 1600];
    private $webpQuality = 75;
    private $avifQuality = 55;
    private $stats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'originalSize' => 0,
        'optimizedSize' => 0
    ];
    
    public function __construct($sourceDir = '', $outputDir = '') {
        $this->sourceDir = $sourceDir ?: __DIR__ . '/../';
        $this->outputDir = $outputDir ?: __DIR__ . '/../images/optimized/';
        
        // Create output directory if it doesn't exist
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Optimize all images in directory
     */
    public function optimizeDirectory($pattern = '*.{jpg,jpeg,png,webp}', $recursive = true) {
        echo "Starting image optimization...\n";
        echo "Source: {$this->sourceDir}\n";
        echo "Output: {$this->outputDir}\n\n";
        
        $flags = $recursive ? GLOB_BRACE | GLOB_NOSORT : GLOB_BRACE;
        $pattern = $recursive ? '**/' . $pattern : $pattern;
        
        $files = glob($this->sourceDir . $pattern, $flags);
        
        if (empty($files)) {
            echo "No images found.\n";
            return;
        }
        
        echo "Found " . count($files) . " images to process.\n\n";
        
        foreach ($files as $file) {
            $this->optimizeImage($file);
        }
        
        $this->printStats();
    }
    
    /**
     * Optimize single image
     */
    public function optimizeImage($filePath) {
        if (!file_exists($filePath)) {
            echo "❌ File not found: $filePath\n";
            $this->stats['errors']++;
            return;
        }
        
        $fileSize = filesize($filePath);
        $this->stats['originalSize'] += $fileSize;
        
        $pathInfo = pathinfo($filePath);
        $baseName = $pathInfo['filename'];
        $ext = strtolower($pathInfo['extension']);
        
        // Skip if already optimized
        if ($ext === 'avif' || (strpos($baseName, '-') !== false && is_numeric(substr(strrchr($baseName, '-'), 1)))) {
            echo "⏭️  Skipping: $baseName (already optimized)\n";
            $this->stats['skipped']++;
            return;
        }
        
        echo "Processing: $baseName\n";
        
        try {
            // Load image
            $image = $this->loadImage($filePath);
            if (!$image) {
                throw new Exception("Failed to load image");
            }
            
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            
            // Generate multiple sizes
            foreach ($this->widths as $width) {
                if ($width > $originalWidth) {
                    continue; // Skip upscaling
                }
                
                $height = round($originalHeight * ($width / $originalWidth));
                
                // Create resized image
                $resized = imagecreatetruecolor($width, $height);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
                
                // Save WebP
                $webpPath = $this->outputDir . $baseName . '-' . $width . '.webp';
                if (function_exists('imagewebp')) {
                    imagewebp($resized, $webpPath, $this->webpQuality);
                    $this->stats['optimizedSize'] += filesize($webpPath);
                    echo "  ✓ WebP: {$width}w\n";
                }
                
                // Save AVIF (if supported)
                $avifPath = $this->outputDir . $baseName . '-' . $width . '.avif';
                if (function_exists('imageavif')) {
                    imageavif($resized, $avifPath, $this->avifQuality);
                    $this->stats['optimizedSize'] += filesize($avifPath);
                    echo "  ✓ AVIF: {$width}w\n";
                }
                
                imagedestroy($resized);
            }
            
            imagedestroy($image);
            $this->stats['processed']++;
            echo "✅ Completed: $baseName\n\n";
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            $this->stats['errors']++;
        }
    }
    
    /**
     * Load image from file
     */
    private function loadImage($filePath) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($filePath);
            case 'png':
                return imagecreatefrompng($filePath);
            case 'webp':
                return imagecreatefromwebp($filePath);
            case 'gif':
                return imagecreatefromgif($filePath);
            default:
                return null;
        }
    }
    
    /**
     * Print optimization statistics
     */
    private function printStats() {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "OPTIMIZATION COMPLETE\n";
        echo str_repeat("=", 50) . "\n";
        echo "Processed: {$this->stats['processed']}\n";
        echo "Skipped: {$this->stats['skipped']}\n";
        echo "Errors: {$this->stats['errors']}\n";
        echo "\nSize Reduction:\n";
        echo "Original: " . $this->formatBytes($this->stats['originalSize']) . "\n";
        echo "Optimized: " . $this->formatBytes($this->stats['optimizedSize']) . "\n";
        
        if ($this->stats['originalSize'] > 0) {
            $reduction = (1 - ($this->stats['optimizedSize'] / $this->stats['originalSize'])) * 100;
            echo "Savings: " . round($reduction, 2) . "%\n";
        }
        echo str_repeat("=", 50) . "\n";
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

// Run optimization if executed directly
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    $optimizer = new ImageOptimizer();
    
    // Optimize specific directories
    echo "Optimizing gallery images...\n";
    $optimizer->optimizeDirectory('gallery/*.{jpg,jpeg,png}');
    
    echo "\nOptimizing blog uploads...\n";
    $optimizer->optimizeDirectory('assest/blog-uploads/*.{jpg,jpeg,png,webp}');
    
    echo "\nOptimizing press uploads...\n";
    $optimizer->optimizeDirectory('assest/press-uploads/*.{jpg,jpeg,png,webp}');
    
    echo "\nOptimizing facility images...\n";
    $optimizer->optimizeDirectory('HadafCompany/*.{jpg,jpeg,png}');
}
?>

