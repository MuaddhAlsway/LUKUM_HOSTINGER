<?php
/**
 * LAKUM Artspace - Mobile Image Helper
 * Renders mobile-first responsive images
 * Prioritizes mobile (400w) as primary size
 * 
 * Usage:
 *   echo MobileImageHelper::renderHero('img-4', 'Hero Image');
 *   echo MobileImageHelper::render('gallery/img28', 'Gallery Image');
 *   echo MobileImageHelper::renderLogo('logo/right_section', 'Logo', 105, 80);
 */

class MobileImageHelper {
    
    /**
     * Render mobile-first hero image (LCP)
     * Mobile gets 400w, tablet gets 800w, desktop gets 1200w
     */
    public static function renderHero($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 768px) 100vw, 650px',
            'width' => 1200,
            'height' => 800,
            'class' => '',
            'id' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Mobile-first: start with 400w
        $srcset = 'optimized-images/' . $imageName . '-400w.webp 400w, ' .
                  'optimized-images/' . $imageName . '-800w.webp 800w, ' .
                  'optimized-images/' . $imageName . '-1200w.webp 1200w';
        
        // Preload for mobile
        $preload = '<link rel="preload" as="image" href="optimized-images/' . $imageName . '-400w.webp" fetchpriority="high">';
        
        // Image tag - mobile gets 400w
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-400w.webp" ';
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
     * Render mobile-first regular image with lazy loading
     * Mobile gets 400w, tablet gets 800w, desktop gets 1200w
     */
    public static function render($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 90vw, 800px',
            'loading' => 'lazy',
            'decoding' => 'async',
            'width' => 800,
            'height' => 600,
            'class' => '',
            'id' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        // Mobile-first: start with 400w
        $srcset = 'optimized-images/' . $imageName . '-400w.webp 400w, ' .
                  'optimized-images/' . $imageName . '-800w.webp 800w, ' .
                  'optimized-images/' . $imageName . '-1200w.webp 1200w';
        
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-400w.webp" ';
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
     * Render mobile-first logo (fixed size, no lazy loading)
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
     * Render mobile-first gallery image (grid layout)
     */
    public static function renderGallery($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 480px) 100vw, (max-width: 768px) 50vw, (max-width: 1024px) 33vw, 400px',
            'loading' => 'lazy',
            'width' => 400,
            'height' => 300,
            'class' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        $srcset = 'optimized-images/' . $imageName . '-400w.webp 400w, ' .
                  'optimized-images/' . $imageName . '-800w.webp 800w, ' .
                  'optimized-images/' . $imageName . '-1200w.webp 1200w';
        
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-400w.webp" ';
        $html .= 'srcset="' . $srcset . '" ';
        $html .= 'sizes="' . $options['sizes'] . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="' . $options['loading'] . '" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . $options['width'] . '" ';
        $html .= 'height="' . $options['height'] . '" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }

    /**
     * Render mobile-first card image (featured cards)
     */
    public static function renderCard($imageName, $alt, $options = []) {
        $defaults = [
            'sizes' => '(max-width: 480px) 100vw, (max-width: 768px) 100vw, (max-width: 1024px) 50vw, 400px',
            'loading' => 'lazy',
            'width' => 400,
            'height' => 300,
            'class' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        $srcset = 'optimized-images/' . $imageName . '-400w.webp 400w, ' .
                  'optimized-images/' . $imageName . '-800w.webp 800w, ' .
                  'optimized-images/' . $imageName . '-1200w.webp 1200w';
        
        $html = '<img ';
        $html .= 'src="optimized-images/' . $imageName . '-400w.webp" ';
        $html .= 'srcset="' . $srcset . '" ';
        $html .= 'sizes="' . $options['sizes'] . '" ';
        $html .= 'alt="' . htmlspecialchars($alt) . '" ';
        $html .= 'loading="' . $options['loading'] . '" ';
        $html .= 'decoding="async" ';
        $html .= 'width="' . $options['width'] . '" ';
        $html .= 'height="' . $options['height'] . '" ';
        
        if ($options['class']) {
            $html .= 'class="' . $options['class'] . '" ';
        }
        
        $html .= '>';
        
        return $html;
    }

    /**
     * Get mobile payload estimate
     */
    public static function getMobilePayloadEstimate($imageName) {
        $mobilePath = 'optimized-images/' . $imageName . '-400w.webp';
        if (file_exists($mobilePath)) {
            return filesize($mobilePath);
        }
        return 0;
    }

    /**
     * Get total mobile payload for all images
     */
    public static function getTotalMobilePayload($directory = 'optimized-images') {
        $total = 0;
        $files = glob($directory . '/*-400w.webp', GLOB_RECURSIVE);
        foreach ($files as $file) {
            $total += filesize($file);
        }
        return $total;
    }
}

// Example usage
if (php_sapi_name() === 'cli') {
    echo "Mobile Image Helper loaded successfully\n";
    echo "Usage:\n";
    echo "  \$hero = MobileImageHelper::renderHero('img-4', 'Hero Image');\n";
    echo "  echo MobileImageHelper::render('gallery/img28', 'Gallery Image');\n";
    echo "  echo MobileImageHelper::renderLogo('logo/right_section', 'Logo', 105, 80);\n";
}
?>
