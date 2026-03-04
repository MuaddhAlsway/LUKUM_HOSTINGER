<?php
/**
 * Responsive Image Helper
 * Generates optimized responsive image HTML with WebP, srcset, and lazy loading
 * 
 * Usage:
 * echo ResponsiveImage::render('gallery/img28', 'Gallery Image', 'gallery');
 * echo ResponsiveImage::renderHero('assest/img-4', 'Hero Image');
 */

class ResponsiveImage {
    
    /**
     * Render responsive image with lazy loading
     * 
     * @param string $imageName Image name without extension (e.g., 'gallery/img28')
     * @param string $alt Alt text
     * @param string $category Image category (gallery, facilities, blog, etc.)
     * @param array $options Additional options
     * @return string HTML img tag
     */
    public static function render($imageName, $alt, $category = 'gallery', $options = []) {
        $defaults = [
            'width' => 1200,
            'height' => 800,
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 1200px',
            'class' => '',
            'style' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        $basePath = 'optimized-images/' . $category . '/' . basename($imageName);
        
        $html = '<img ';
        $html .= 'src="' . htmlspecialchars($basePath . '-1200w.webp') . '" ';
        $html .= 'srcset="' . htmlspecialchars(
            $basePath . '-400w.webp 400w, ' .
            $basePath . '-800w.webp 800w, ' .
            $basePath . '-1200w.webp 1200w'
        ) . '" ';
        $html .= 'sizes="' . htmlspecialchars($options['sizes']) . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="lazy" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . intval($options['width']) . '" ';
        $html .= 'height="' . intval($options['height']) . '" ';
        
        if (!empty($options['class'])) {
            $html .= 'class="' . htmlspecialchars($options['class']) . '" ';
        }
        
        if (!empty($options['style'])) {
            $html .= 'style="' . htmlspecialchars($options['style']) . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }
    
    /**
     * Render LCP (hero) image with preload and fetchpriority
     * 
     * @param string $imageName Image name without extension
     * @param string $alt Alt text
     * @param array $options Additional options
     * @return array ['preload' => preload tag, 'img' => img tag]
     */
    public static function renderHero($imageName, $alt, $options = []) {
        $defaults = [
            'width' => 1200,
            'height' => 800,
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 1200px',
            'class' => '',
            'style' => 'aspect-ratio: 16/9'
        ];
        
        $options = array_merge($defaults, $options);
        
        $basePath = 'optimized-images/assest/' . basename($imageName);
        
        // Preload tag for <head>
        $preload = '<link rel="preload" as="image" href="' . htmlspecialchars($basePath . '-1200w.webp') . '" fetchpriority="high">';
        
        // Image tag
        $html = '<img ';
        $html .= 'src="' . htmlspecialchars($basePath . '-1200w.webp') . '" ';
        $html .= 'srcset="' . htmlspecialchars(
            $basePath . '-400w.webp 400w, ' .
            $basePath . '-800w.webp 800w, ' .
            $basePath . '-1200w.webp 1200w'
        ) . '" ';
        $html .= 'sizes="' . htmlspecialchars($options['sizes']) . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'fetchpriority="high" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . intval($options['width']) . '" ';
        $html .= 'height="' . intval($options['height']) . '" ';
        
        if (!empty($options['class'])) {
            $html .= 'class="' . htmlspecialchars($options['class']) . '" ';
        }
        
        if (!empty($options['style'])) {
            $html .= 'style="' . htmlspecialchars($options['style']) . '" ';
        }
        
        $html .= '>';
        
        return [
            'preload' => $preload,
            'img' => $html
        ];
    }
    
    /**
     * Render logo image (small, no lazy loading)
     * 
     * @param string $imageName Image name without extension
     * @param string $alt Alt text
     * @param int $width Logo width
     * @param int $height Logo height
     * @return string HTML img tag
     */
    public static function renderLogo($imageName, $alt, $width = 100, $height = 50) {
        $basePath = 'optimized-images/assest/logo/' . basename($imageName);
        
        $html = '<img ';
        $html .= 'src="' . htmlspecialchars($basePath . '-' . $width . 'w.webp') . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'width="' . intval($width) . '" ';
        $html .= 'height="' . intval($height) . '" ';
        $html .= 'decoding="async" ';
        $html .= '>';
        
        return $html;
    }
    
    /**
     * Render with picture element for better fallback support
     * 
     * @param string $imageName Image name without extension
     * @param string $alt Alt text
     * @param string $category Image category
     * @param array $options Additional options
     * @return string HTML picture element
     */
    public static function renderWithFallback($imageName, $alt, $category = 'gallery', $options = []) {
        $defaults = [
            'width' => 1200,
            'height' => 800,
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 1200px',
            'class' => '',
            'style' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        $basePath = 'optimized-images/' . $category . '/' . basename($imageName);
        
        $html = '<picture>';
        
        // WebP source
        $html .= '<source ';
        $html .= 'srcset="' . htmlspecialchars(
            $basePath . '-400w.webp 400w, ' .
            $basePath . '-800w.webp 800w, ' .
            $basePath . '-1200w.webp 1200w'
        ) . '" ';
        $html .= 'sizes="' . htmlspecialchars($options['sizes']) . '" ';
        $html .= 'type="image/webp">';
        
        // JPG fallback
        $html .= '<source ';
        $html .= 'srcset="' . htmlspecialchars(
            $basePath . '-400w.jpg 400w, ' .
            $basePath . '-800w.jpg 800w, ' .
            $basePath . '-1200w.jpg 1200w'
        ) . '" ';
        $html .= 'sizes="' . htmlspecialchars($options['sizes']) . '" ';
        $html .= 'type="image/jpeg">';
        
        // Fallback img
        $html .= '<img ';
        $html .= 'src="' . htmlspecialchars($basePath . '-1200w.jpg') . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="lazy" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . intval($options['width']) . '" ';
        $html .= 'height="' . intval($options['height']) . '" ';
        
        if (!empty($options['class'])) {
            $html .= 'class="' . htmlspecialchars($options['class']) . '" ';
        }
        
        if (!empty($options['style'])) {
            $html .= 'style="' . htmlspecialchars($options['style']) . '" ';
        }
        
        $html .= '>';
        
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Get image srcset string
     * 
     * @param string $basePath Base path to image
     * @param array $sizes Sizes to generate
     * @return string srcset attribute value
     */
    public static function getSrcset($basePath, $sizes = [400, 800, 1200]) {
        $srcset = [];
        foreach ($sizes as $size) {
            $srcset[] = htmlspecialchars($basePath . '-' . $size . 'w.webp') . ' ' . $size . 'w';
        }
        return implode(', ', $srcset);
    }
}

// Example usage:
/*
// In PHP file:
<?php require_once 'api/responsive-image-helper.php'; ?>

// Render regular image
<?php echo ResponsiveImage::render('gallery/img28', 'Gallery Image', 'gallery'); ?>

// Render hero image
<?php 
$hero = ResponsiveImage::renderHero('img-4', 'LAKUM Artspace');
// Add preload to <head>: <?php echo $hero['preload']; ?>
// Add image to body: <?php echo $hero['img']; ?>
?>

// Render logo
<?php echo ResponsiveImage::renderLogo('right_section', 'LAKUM Logo', 100, 50); ?>

// Render with fallback
<?php echo ResponsiveImage::renderWithFallback('gallery/img28', 'Gallery Image', 'gallery'); ?>
*/
?>

