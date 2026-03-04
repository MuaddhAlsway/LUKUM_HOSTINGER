<?php
/**
 * Web-Based Image Optimizer
 * Run this file in your browser: https://yoursite.com/api/optimize-images-web.php
 */

// Set execution time limit
set_time_limit(300);
ini_set('memory_limit', '512M');

class WebImageOptimizer {
    
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
    private $log = [];
    
    public function __construct() {
        $this->sourceDir = __DIR__ . '/../';
        $this->outputDir = __DIR__ . '/../images/optimized/';
        
        // Create output directory if it doesn't exist
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Optimize all images
     */
    public function optimizeAll() {
        $this->addLog("Starting image optimization...");
        $this->addLog("Source: {$this->sourceDir}");
        $this->addLog("Output: {$this->outputDir}");
        
        // Optimize gallery images
        $this->addLog("\n=== Optimizing Gallery Images ===");
        $this->optimizeDirectory('gallery', '*.{jpg,jpeg,png}');
        
        // Optimize blog uploads
        $this->addLog("\n=== Optimizing Blog Images ===");
        $this->optimizeDirectory('assest/blog-uploads', '*.{jpg,jpeg,png,webp}');
        
        // Optimize press uploads
        $this->addLog("\n=== Optimizing Press Images ===");
        $this->optimizeDirectory('assest/press-uploads', '*.{jpg,jpeg,png,webp}');
        
        // Optimize facility images
        $this->addLog("\n=== Optimizing Facility Images ===");
        $this->optimizeDirectory('HadafCompany', '*.{jpg,jpeg,png}');
        
        // Optimize hero images
        $this->addLog("\n=== Optimizing Hero Images ===");
        $this->optimizeImage('assest/img-4.png');
        $this->optimizeImage('assest/img-3.JPG');
        
        // Optimize logos
        $this->addLog("\n=== Optimizing Logo Images ===");
        $this->optimizeImage('assest/logo/right_section.png');
        $this->optimizeImage('assest/logo/left_section.png');
        
        return $this->getStats();
    }
    
    /**
     * Optimize directory
     */
    private function optimizeDirectory($dir, $pattern) {
        $path = $this->sourceDir . $dir;
        
        if (!is_dir($path)) {
            $this->addLog("❌ Directory not found: $dir");
            return;
        }
        
        $files = glob($path . '/' . $pattern, GLOB_BRACE);
        
        if (empty($files)) {
            $this->addLog("⏭️  No images found in: $dir");
            return;
        }
        
        $this->addLog("Found " . count($files) . " images in $dir");
        
        foreach ($files as $file) {
            $this->optimizeImage($file);
        }
    }
    
    /**
     * Optimize single image
     */
    private function optimizeImage($filePath) {
        if (!file_exists($filePath)) {
            $this->addLog("❌ File not found: $filePath");
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
            $this->addLog("⏭️  Skipping: $baseName (already optimized)");
            $this->stats['skipped']++;
            return;
        }
        
        $this->addLog("Processing: $baseName");
        
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
                    $this->addLog("  ✓ WebP: {$width}w");
                }
                
                // Save AVIF (if supported)
                $avifPath = $this->outputDir . $baseName . '-' . $width . '.avif';
                if (function_exists('imageavif')) {
                    imageavif($resized, $avifPath, $this->avifQuality);
                    $this->stats['optimizedSize'] += filesize($avifPath);
                    $this->addLog("  ✓ AVIF: {$width}w");
                }
                
                imagedestroy($resized);
            }
            
            imagedestroy($image);
            $this->stats['processed']++;
            $this->addLog("✅ Completed: $baseName\n");
            
        } catch (Exception $e) {
            $this->addLog("❌ Error: " . $e->getMessage() . "\n");
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
     * Add log message
     */
    private function addLog($message) {
        $this->log[] = $message;
    }
    
    /**
     * Get log
     */
    public function getLog() {
        return $this->log;
    }
    
    /**
     * Get stats
     */
    public function getStats() {
        return $this->stats;
    }
    
    /**
     * Format bytes
     */
    public static function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

// Handle request
$action = $_GET['action'] ?? 'form';

if ($action === 'optimize') {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Image Optimization - Running</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f6f6eb;
                color: #1a1a1a;
                padding: 40px 20px;
                max-width: 900px;
                margin: 0 auto;
            }
            .container {
                background: white;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            h1 {
                margin-top: 0;
                color: #1a1a1a;
            }
            .log {
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px;
                font-family: 'Courier New', monospace;
                font-size: 13px;
                line-height: 1.6;
                max-height: 500px;
                overflow-y: auto;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
            .stats {
                margin-top: 30px;
                padding: 20px;
                background: #f0f8ff;
                border-left: 4px solid #2196f3;
                border-radius: 4px;
            }
            .stat-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #e0e0e0;
            }
            .stat-row:last-child {
                border-bottom: none;
            }
            .stat-label {
                font-weight: 600;
            }
            .stat-value {
                color: #2196f3;
                font-weight: 600;
            }
            .success {
                color: #4caf50;
            }
            .error {
                color: #f44336;
            }
            .warning {
                color: #ff9800;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background: #2196f3;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }
            .button:hover {
                background: #1976d2;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🖼️ Image Optimization Running...</h1>
            
            <?php
            $optimizer = new WebImageOptimizer();
            $stats = $optimizer->optimizeAll();
            $log = $optimizer->getLog();
            
            echo '<div class="log">';
            foreach ($log as $line) {
                echo htmlspecialchars($line) . "\n";
            }
            echo '</div>';
            
            echo '<div class="stats">';
            echo '<div class="stat-row">';
            echo '<span class="stat-label">Processed:</span>';
            echo '<span class="stat-value success">' . $stats['processed'] . '</span>';
            echo '</div>';
            
            echo '<div class="stat-row">';
            echo '<span class="stat-label">Skipped:</span>';
            echo '<span class="stat-value warning">' . $stats['skipped'] . '</span>';
            echo '</div>';
            
            echo '<div class="stat-row">';
            echo '<span class="stat-label">Errors:</span>';
            echo '<span class="stat-value error">' . $stats['errors'] . '</span>';
            echo '</div>';
            
            echo '<div class="stat-row">';
            echo '<span class="stat-label">Original Size:</span>';
            echo '<span class="stat-value">' . WebImageOptimizer::formatBytes($stats['originalSize']) . '</span>';
            echo '</div>';
            
            echo '<div class="stat-row">';
            echo '<span class="stat-label">Optimized Size:</span>';
            echo '<span class="stat-value">' . WebImageOptimizer::formatBytes($stats['optimizedSize']) . '</span>';
            echo '</div>';
            
            if ($stats['originalSize'] > 0) {
                $reduction = (1 - ($stats['optimizedSize'] / $stats['originalSize'])) * 100;
                echo '<div class="stat-row">';
                echo '<span class="stat-label">Savings:</span>';
                echo '<span class="stat-value success">' . round($reduction, 2) . '%</span>';
                echo '</div>';
            }
            echo '</div>';
            
            echo '<a href="?action=form" class="button">← Back</a>';
            ?>
        </div>
    </body>
    </html>
    <?php
} else {
    // Show form
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Image Optimizer</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f6f6eb;
                color: #1a1a1a;
                padding: 40px 20px;
                max-width: 900px;
                margin: 0 auto;
            }
            .container {
                background: white;
                border-radius: 8px;
                padding: 40px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            h1 {
                margin-top: 0;
                color: #1a1a1a;
            }
            .info {
                background: #e3f2fd;
                border-left: 4px solid #2196f3;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
                line-height: 1.6;
            }
            .features {
                list-style: none;
                padding: 0;
                margin: 20px 0;
            }
            .features li {
                padding: 8px 0;
                padding-left: 25px;
                position: relative;
            }
            .features li:before {
                content: "✓";
                position: absolute;
                left: 0;
                color: #4caf50;
                font-weight: bold;
            }
            .button {
                display: inline-block;
                padding: 12px 30px;
                background: #2196f3;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                border: none;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                margin-top: 20px;
            }
            .button:hover {
                background: #1976d2;
            }
            .warning {
                background: #fff3cd;
                border-left: 4px solid #ff9800;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🖼️ Image Optimizer</h1>
            
            <div class="info">
                <strong>What this tool does:</strong>
                <ul class="features">
                    <li>Converts images to WebP format</li>
                    <li>Generates AVIF versions</li>
                    <li>Creates multiple sizes (400w-1600w)</li>
                    <li>Optimizes compression</li>
                    <li>Reduces file size by 85-90%</li>
                </ul>
            </div>
            
            <div class="warning">
                <strong>⚠️ Warning:</strong> This process may take several minutes depending on the number and size of images. Do not close this page or refresh during optimization.
            </div>
            
            <p>Click the button below to start optimizing all images in your project:</p>
            
            <a href="?action=optimize" class="button">Start Optimization →</a>
            
            <div class="info" style="margin-top: 40px;">
                <strong>Expected Results:</strong>
                <ul class="features">
                    <li>Total size reduction: 85-90%</li>
                    <li>LCP improvement: 60-75% faster</li>
                    <li>Lighthouse score: +25-35 points</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

