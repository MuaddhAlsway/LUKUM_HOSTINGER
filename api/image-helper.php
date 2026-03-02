<?php
/**
 * Image Helper - Responsive Image System
 * Generates optimized responsive images with multiple formats
 * Supports: AVIF, WebP, JPG/PNG fallback
 */

class ImageHelper {
    
    private static $baseUrl = '';
    private static $imagePath = '';
    
    /**
     * Initialize image helper with base paths
     */
    public static function init($baseUrl = '', $imagePath = '') {
        self::$baseUrl = $baseUrl ?: $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        self::$imagePath = $imagePath ?: __DIR__ . '/../';
    }
    
    /**
     * Render responsive image with picture element
     * 
     * @param string $imagePath Path to image (relative to root)
     * @param string $alt Alt text
     * @param string $type Image type: 'hero', 'blog', 'gallery', 'logo'
     * @param array $options Additional options
     * @return string HTML picture element
     */
    public static function render($imagePath, $alt, $type = 'blog', $options = []) {
        $config = self::getConfig($type);
        
        // Merge with custom options
        $config = array_merge($config, $options);
        
        // Get image info
        $imageInfo = self::getImageInfo($imagePath);
        if (!$imageInfo) {
            return self::fallbackImage($imagePath, $alt, $config);
        }
        
        // Generate responsive sources
        $sources = self::generateSources($imagePath, $imageInfo, $config);
        
        // Build picture element
        $html = '<picture>';
        
        // AVIF sources
        foreach ($sources['avif'] as $source) {
            $html .= sprintf(
                '<source type="image/avif" srcset="%s" sizes="%s">',
                $source['srcset'],
                $config['sizes']
            );
        }
        
        // WebP sources
        foreach ($sources['webp'] as $source) {
            $html .= sprintf(
                '<source type="image/webp" srcset="%s" sizes="%s">',
                $source['srcset'],
                $config['sizes']
            );
        }
        
        // Fallback image
        $fallbackSrc = $sources['fallback'][0]['src'] ?? $imagePath;
        $lazyAttr = $config['lazy'] ? 'loading="lazy"' : '';
        $decodingAttr = $config['decoding'] ? 'decoding="async"' : '';
        $fetchPriorityAttr = $config['fetchpriority'] ? 'fetchpriority="high"' : '';
        
        $html .= sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" %s %s %s style="aspect-ratio: %s">',
            htmlspecialchars($fallbackSrc),
            htmlspecialchars($alt),
            $config['width'],
            $config['height'],
            $lazyAttr,
            $decodingAttr,
            $fetchPriorityAttr,
            $config['aspectRatio']
        );
        
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Get configuration for image type
     */
    private static function getConfig($type) {
        $configs = [
            'hero' => [
                'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 1600px',
                'widths' => [800, 1200, 1600],
                'width' => 1600,
                'height' => 900,
                'aspectRatio' => '16/9',
                'lazy' => false,
                'decoding' => true,
                'fetchpriority' => true,
                'quality' => 75
            ],
            'blog' => [
                'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px',
                'widths' => [400, 600, 800],
                'width' => 400,
                'height' => 533,
                'aspectRatio' => '3/4',
                'lazy' => true,
                'decoding' => true,
                'fetchpriority' => false,
                'quality' => 75
            ],
            'gallery' => [
                'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 600px',
                'widths' => [400, 600, 800],
                'width' => 600,
                'height' => 400,
                'aspectRatio' => '3/2',
                'lazy' => true,
                'decoding' => true,
                'fetchpriority' => false,
                'quality' => 75
            ],
            'logo' => [
                'sizes' => '(max-width: 640px) 50px, 60px',
                'widths' => [60, 120],
                'width' => 60,
                'height' => 60,
                'aspectRatio' => '1/1',
                'lazy' => false,
                'decoding' => true,
                'fetchpriority' => false,
                'quality' => 85
            ]
        ];
        
        return $configs[$type] ?? $configs['blog'];
    }
    
    /**
     * Get image information
     */
    private static function getImageInfo($imagePath) {
        $fullPath = self::$imagePath . $imagePath;
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $info = getimagesize($fullPath);
        if (!$info) {
            return null;
        }
        
        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'path' => $imagePath,
            'fullPath' => $fullPath
        ];
    }
    
    /**
     * Generate responsive image sources
     */
    private static function generateSources($imagePath, $imageInfo, $config) {
        $sources = [
            'avif' => [],
            'webp' => [],
            'fallback' => []
        ];
        
        $pathInfo = pathinfo($imagePath);
        $basePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        
        // Generate srcset for each width
        $srcsetAvif = [];
        $srcsetWebp = [];
        $srcsetFallback = [];
        
        foreach ($config['widths'] as $width) {
            // AVIF
            $avifPath = $basePath . '-' . $width . '.avif';
            if (file_exists(self::$imagePath . $avifPath)) {
                $srcsetAvif[] = self::$baseUrl . '/' . $avifPath . ' ' . $width . 'w';
            }
            
            // WebP
            $webpPath = $basePath . '-' . $width . '.webp';
            if (file_exists(self::$imagePath . $webpPath)) {
                $srcsetWebp[] = self::$baseUrl . '/' . $webpPath . ' ' . $width . 'w';
            }
            
            // Fallback (JPG/PNG)
            $fallbackPath = $basePath . '-' . $width . '.' . $pathInfo['extension'];
            if (file_exists(self::$imagePath . $fallbackPath)) {
                $srcsetFallback[] = self::$baseUrl . '/' . $fallbackPath . ' ' . $width . 'w';
            }
        }
        
        // Add original as fallback if no sized versions exist
        if (empty($srcsetFallback)) {
            $srcsetFallback[] = self::$baseUrl . '/' . $imagePath;
        }
        
        if (!empty($srcsetAvif)) {
            $sources['avif'][] = ['srcset' => implode(', ', $srcsetAvif)];
        }
        
        if (!empty($srcsetWebp)) {
            $sources['webp'][] = ['srcset' => implode(', ', $srcsetWebp)];
        }
        
        $sources['fallback'][] = ['src' => $srcsetFallback[0] ?? $imagePath];
        
        return $sources;
    }
    
    /**
     * Fallback for missing images
     */
    private static function fallbackImage($imagePath, $alt, $config) {
        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" style="aspect-ratio: %s">',
            htmlspecialchars($imagePath),
            htmlspecialchars($alt),
            $config['width'],
            $config['height'],
            $config['aspectRatio']
        );
    }
    
    /**
     * Get image URL with optional size
     */
    public static function getUrl($imagePath, $width = null) {
        if (!$width) {
            return self::$baseUrl . '/' . $imagePath;
        }
        
        $pathInfo = pathinfo($imagePath);
        $basePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $webpPath = $basePath . '-' . $width . '.webp';
        
        if (file_exists(self::$imagePath . $webpPath)) {
            return self::$baseUrl . '/' . $webpPath;
        }
        
        return self::$baseUrl . '/' . $imagePath;
    }
}

// Initialize on include
ImageHelper::init();
?>
