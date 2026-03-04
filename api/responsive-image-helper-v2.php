<?php
/**
 * LAKUM Artspace - Responsive Image Helper v2
 * Renders optimized responsive images with srcset, sizes, and lazy loading
 * 
 * Usage:
 *   echo ResponsiveImageHelper::render('gallery/img28', 'Gallery Image', 'gallery');
 *   echo ResponsiveImageHelper::renderHero('img-4', 'Hero Image');
 *   echo ResponsiveImageHelper::renderLogo('logo', 'Logo', 100, 50);
 */

class ResponsiveImageHelper {
    
    /**
     * Render responsive image with srcset and sizes
     * 
     * @param string $imageName - Image name without extension (e.g., 'gallery/img28')
     * @param string $alt - Alt text
     * @param string $category - Image category (gallery, blog, press, etc.)
     * @param array $options - Additional options
     * @return string - HTML img tag
     */
    public static function render($imageName, $alt, $category = 'gallery', $options = []) {
        $defaults = [
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 800px',
            'loading' => 'lazy',
            'decoding' => 'async',
            'width' => 800,
            'height' => 600,
            'class' => '',
            'id' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Build srcset
        $srcset = self::buildSrcset($imageName, [400, 800, 1200]);
        
        // Build HTML
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-800w.webp" ';
        $html .= 'srcset="' . $srcset . '" ';
        $html .= 'sizes="' . $options['sizes'] . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="' . $options['loading'] . '" ';
        $html .= 'decoding="' . $options['decoding'] . '" ';
        $html .= 'width="' . $options['width'] . '" ';
        $html .= 'height="' . $options['height'] . '" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        if ($options['id']) {
            $html .= 'id="' . $options['id'] . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }

    /**
     * Render hero/LCP image (no lazy loading, high priority)
     * 
     * @param string $imageName - Image name without extension
     * @param string $alt - Alt text
     * @param array $options - Additional options
     * @return array - Array with 'preload' and 'img' keys
     */
    public static function renderHero($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 1200px',
            'width' => 1200,
            'height' => 800,
            'class' => '',
            'id' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Build srcset
        $srcset = self::buildSrcset($imageName, [400, 800, 1200]);
        
        // Preload link for head
        $preload = '<link rel="preload" as="image" href="optimized-images/' . $imageName . '-1200w.webp" fetchpriority="high">';
        
        // Image tag
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-1200w.webp" ';
        $html .= 'srcset="' . $srcset . '" ';
        $html .= 'sizes="' . $options['sizes'] . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'fetchpriority="high" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . $options['width'] . '" ';
        $html .= 'height="' . $options['height'] . '" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        if ($options['id']) {
            $html .= 'id="' . $options['id'] . '" ';
        }
        
        $html .= '>';
        
        return [
            'preload' => $preload,
            'img' => $html
        ];
    }

    /**
     * Render logo (fixed size, no lazy loading)
     * 
     * @param string $imageName - Image name without extension
     * @param string $alt - Alt text
     * @param int $width - Logo width
     * @param int $height - Logo height
     * @param array $options - Additional options
     * @return string - HTML img tag
     */
    public static function renderLogo($imageName, $alt, $width = 100, $height = 50, $options = []) {
        $defaults = [
            'class' => '',
            'id' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '.webp" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'width="' . $width . '" ';
        $html .= 'height="' . $height . '" ';
        $html .= 'decoding="async" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        if ($options['id']) {
            $html .= 'id="' . $options['id'] . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }

    /**
     * Render with picture element for WebP fallback
     * 
     * @param string $imageName - Image name without extension
     * @param string $alt - Alt text
     * @param array $options - Additional options
     * @return string - HTML picture element
     */
    public static function renderWithFallback($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 800px',
            'loading' => 'lazy',
            'width' => 800,
            'height' => 600,
            'class' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Build srcset for WebP
        $webpSrcset = self::buildSrcset($imageName, [400, 800, 1200], 'webp');
        
        // Build srcset for JPG fallback
        $jpgSrcset = self::buildSrcset($imageName, [400, 800, 1200], 'jpg');
        
        $html = '<picture>';
        $html .= '<source srcset="' . $webpSrcset . '" sizes="' . $options['sizes'] . '" type="image/webp">';
        $html .= '<source srcset="' . $jpgSrcset . '" sizes="' . $options['sizes'] . '" type="image/jpeg">';
        $html .= '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-800w.jpg" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="' . $options['loading'] . '" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . $options['width'] . '" ';
        $html .= 'height="' . $options['height'] . '" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        
        $html .= '>';
        $html .= '</picture>';
        
        return $html;
    }

    /**
     * Build srcset string
     * 
     * @param string $imageName - Image name
     * @param array $sizes - Array of sizes (e.g., [400, 800, 1200])
     * @param string $format - Image format (webp, jpg, png)
     * @return string - srcset string
     */
    private static function buildSrcset($imageName, $sizes = [400, 800, 1200], $format = 'webp') {
        $srcset = [];
        
        foreach ($sizes as $size) {
            $srcset[] = 'optimized-images/' . $imageName . '-' . $size . 'w.' . $format . ' ' . $size . 'w';
        }
        
        return implode(', ', $srcset);
    }

    /**
     * Get image manifest (for debugging)
     * 
     * @param string $imageName - Image name
     * @return array - Array of available sizes
     */
    public static function getImageManifest($imageName) {
        $manifest = [];
        $sizes = [400, 800, 1200];
        
        foreach ($sizes as $size) {
            $webpPath = 'optimized-images/' . $imageName . '-' . $size . 'w.webp';
            $jpgPath = 'optimized-images/' . $imageName . '-' . $size . 'w.jpg';
            
            $manifest[$size] = [
                'webp' => file_exists($webpPath) ? $webpPath : null,
                'jpg' => file_exists($jpgPath) ? $jpgPath : null
            ];
        }
        
        return $manifest;
    }
}

// Example usage (for testing)
if (php_sapi_name() === 'cli') {
    echo "Responsive Image Helper v2 loaded successfully\n";
    echo "Usage:\n";
    echo "  echo ResponsiveImageHelper::render('gallery/img28', 'Gallery Image');\n";
    echo "  echo ResponsiveImageHelper::renderHero('img-4', 'Hero Image');\n";
    echo "  echo ResponsiveImageHelper::renderLogo('logo', 'Logo', 100, 50);\n";
}
?>


