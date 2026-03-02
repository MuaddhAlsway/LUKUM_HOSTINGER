<?php
/**
 * Production Image Optimizer
 * Converts images to WebP, generates responsive sizes, applies aggressive compression
 * 
 * Usage: php api/image-optimizer-production.php
 * Or: https://yourdomain.com/api/image-optimizer-production.php
 */

class ProductionImageOptimizer {
    private $sourceDir = '';
    private $outputDir = '';
    private $quality = 70; // 60-75 range
    private $sizes = [400, 800, 1200]; // Responsive breakpoints
    private $results = [];
    
    public function __construct() {
        $this->sourceDir = __DIR__ . '/../';
        $this->outputDir = __DIR__ . '/../optimized-images/';
        
        // Create output directory
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Optimize all images in directory
     */
    public function optimizeAll() {
        $imageDirs = [
            'gallery' => 'gallery',
            'HADAFCompany' => 'HADAFCompany',
            'assest' => 'assest',
            'assest/logo' => 'assest/logo',
            'assest/blog-uploads' => 'assest/blog-uploads',
            'assest/press-uploads' => 'assest/press-uploads'
        ];
        
        foreach ($imageDirs as $dir => $label) {
            $this->optimizeDirectory($dir, $label);
        }
        
        return $this->results;
    }
    
    /**
     * Optimize images in a directory
     */
    private function optimizeDirectory($dir, $label) {
        $fullPath = $this->sourceDir . $dir;
        
        if (!is_dir($fullPath)) {
            return;
        }
        
        $files = glob($fullPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $this->optimizeImage($file, $label);
        }
    }
    
    /**
     * Optimize single image
     */
    private function optimizeImage($filePath, $label) {
        $filename = basename($filePath);
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        
        // Get image dimensions
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $originalSize = filesize($filePath);
        
        // Create output subdirectory
        $outputSubDir = $this->outputDir . $label . '/';
        if (!is_dir($outputSubDir)) {
            mkdir($outputSubDir, 0755, true);
        }
        
        $result = [
            'file' => $filename,
            'label' => $label,
            'originalSize' => $originalSize,
            'originalDimensions' => "{$originalWidth}x{$originalHeight}",
            'optimized' => [],
            'totalSavings' => 0
        ];
        
        // Generate responsive sizes
        foreach ($this->sizes as $width) {
            // Skip if original is smaller
            if ($originalWidth <= $width) {
                continue;
            }
            
            $height = round($originalHeight * ($width / $originalWidth));
            $outputFile = $outputSubDir . $nameWithoutExt . '-' . $width . 'w.webp';
            
            // Convert to WebP
            $this->convertToWebP($filePath, $outputFile, $width, $height);
            
            if (file_exists($outputFile)) {
                $optimizedSize = filesize($outputFile);
                $savings = $originalSize - $optimizedSize;
                $savingsPercent = round(($savings / $originalSize) * 100, 1);
                
                $result['optimized'][] = [
                    'size' => $width . 'w',
                    'dimensions' => "{$width}x{$height}",
                    'file' => basename($outputFile),
                    'fileSize' => $optimizedSize,
                    'savings' => $savings,
                    'savingsPercent' => $savingsPercent
                ];
                
                $result['totalSavings'] += $savings;
            }
        }
        
        $this->results[] = $result;
    }
    
    /**
     * Convert image to WebP with aggressive compression
     */
    private function convertToWebP($source, $destination, $width, $height) {
        // Check if ImageMagick is available
        if (extension_loaded('imagick')) {
            $this->convertWithImageMagick($source, $destination, $width, $height);
        } elseif (extension_loaded('gd')) {
            $this->convertWithGD($source, $destination, $width, $height);
        } else {
            // Fallback: copy original
            copy($source, $destination);
        }
    }
    
    /**
     * Convert using ImageMagick (better quality)
     */
    private function convertWithImageMagick($source, $destination, $width, $height) {
        try {
            $image = new Imagick($source);
            $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($this->quality);
            $image->writeImage($destination);
            $image->destroy();
        } catch (Exception $e) {
            // Fallback to GD
            $this->convertWithGD($source, $destination, $width, $height);
        }
    }
    
    /**
     * Convert using GD (fallback)
     */
    private function convertWithGD($source, $destination, $width, $height) {
        $imageType = exif_imagetype($source);
        
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            default:
                return;
        }
        
        if (!$image) {
            return;
        }
        
        // Create resized image
        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, 
                          imagesx($image), imagesy($image));
        
        // Save as WebP
        imagewebp($resized, $destination, $this->quality);
        
        imagedestroy($image);
        imagedestroy($resized);
    }
    
    /**
     * Get optimization results
     */
    public function getResults() {
        return $this->results;
    }
    
    /**
     * Generate HTML report
     */
    public function generateReport() {
        $totalOriginal = 0;
        $totalOptimized = 0;
        $totalSavings = 0;
        
        foreach ($this->results as $result) {
            $totalOriginal += $result['originalSize'];
            foreach ($result['optimized'] as $opt) {
                $totalOptimized += $opt['fileSize'];
            }
            $totalSavings += $result['totalSavings'];
        }
        
        $html = '<h2>Image Optimization Report</h2>';
        $html .= '<p><strong>Total Original Size:</strong> ' . $this->formatBytes($totalOriginal) . '</p>';
        $html .= '<p><strong>Total Optimized Size:</strong> ' . $this->formatBytes($totalOptimized) . '</p>';
        $html .= '<p><strong>Total Savings:</strong> ' . $this->formatBytes($totalSavings) . ' (' . 
                 round(($totalSavings / $totalOriginal) * 100, 1) . '%)</p>';
        
        $html .= '<table border="1" cellpadding="10">';
        $html .= '<tr><th>File</th><th>Original</th><th>Optimized Sizes</th><th>Savings</th></tr>';
        
        foreach ($this->results as $result) {
            $html .= '<tr>';
            $html .= '<td>' . $result['file'] . '</td>';
            $html .= '<td>' . $this->formatBytes($result['originalSize']) . '<br>' . 
                     $result['originalDimensions'] . '</td>';
            $html .= '<td>';
            foreach ($result['optimized'] as $opt) {
                $html .= $opt['size'] . ': ' . $this->formatBytes($opt['fileSize']) . '<br>';
            }
            $html .= '</td>';
            $html .= '<td>' . $this->formatBytes($result['totalSavings']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        return $html;
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

// CLI or Web execution
if (php_sapi_name() === 'cli') {
    echo "Starting image optimization...\n";
    $optimizer = new ProductionImageOptimizer();
    $optimizer->optimizeAll();
    echo $optimizer->generateReport();
} else {
    header('Content-Type: text/html; charset=utf-8');
    $optimizer = new ProductionImageOptimizer();
    $optimizer->optimizeAll();
    echo $optimizer->generateReport();
}
?>
