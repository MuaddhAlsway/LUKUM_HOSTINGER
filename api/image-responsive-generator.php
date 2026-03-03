<?php
/**
 * LAKUM Responsive Image Generator
 * Generates optimized srcset attributes for all images
 * Sizes: 320w, 480w, 768w, 1024w, 1600w
 */

class ResponsiveImageGenerator {
    private $sizes = [320, 480, 768, 1024, 1600];
    private $quality = 70;
    private $heroQuality = 75;
    
    /**
     * Generate srcset for regular images
     * @param string $imagePath Path to image (without extension)
     * @param string $ext File extension (webp, jpg, etc)
     * @return string srcset attribute value
     */
    public function generateSrcset($imagePath, $ext = 'webp') {
        $srcset = [];
        foreach ($this->sizes as $size) {
            $srcset[] = "{$imagePath}-{$size}.{$ext} {$size}w";
        }
        return implode(', ', $srcset);
    }
    
    /**
     * Generate sizes attribute for responsive images
     * @param string $type 'hero', 'featured', 'card', 'gallery'
     * @return string sizes attribute value
     */
    public function generateSizes($type = 'card') {
        $sizes = [
            'hero' => '100vw',
            'featured' => '(max-width: 768px) 100vw, 50vw',
            'card' => '(max-width: 480px) 100vw, (max-width: 768px) 50vw, (max-width: 1024px) 33vw, 25vw',
            'gallery' => '(max-width: 480px) 100vw, (max-width: 768px) 50vw, (max-width: 1024px) 33vw, 25vw',
            'thumbnail' => '(max-width: 480px) 80px, 100px',
        ];
        return $sizes[$type] ?? $sizes['card'];
    }
    
    /**
     * Generate complete picture element
     */
    public function generatePicture($imagePath, $alt, $type = 'card', $width = 800, $height = 600) {
        $srcset = $this->generateSrcset($imagePath);
        $sizes = $this->generateSizes($type);
        $fallback = "{$imagePath}-768.webp";
        
        $loading = ($type === 'hero') ? 'eager' : 'lazy';
        $fetchpriority = ($type === 'hero') ? 'high' : 'auto';
        
        $html = '<picture>';
        $html .= "<source type=\"image/webp\" srcset=\"{$srcset}\" sizes=\"{$sizes}\">";
        $html .= "<img src=\"{$fallback}\" alt=\"{$alt}\" loading=\"{$loading}\" ";
        $html .= "fetchpriority=\"{$fetchpriority}\" decoding=\"async\" ";
        $html .= "width=\"{$width}\" height=\"{$height}\" ";
        $html .= "style=\"width:100%;height:auto;object-fit:cover;display:block;\">";
        $html .= '</picture>';
        
        return $html;
    }
}

// Helper functions
function responsiveImage($path, $alt, $type = 'card', $width = 800, $height = 600) {
    $gen = new ResponsiveImageGenerator();
    return $gen->generatePicture($path, $alt, $type, $width, $height);
}

function responsiveSrcset($path) {
    $gen = new ResponsiveImageGenerator();
    return $gen->generateSrcset($path);
}

function responsiveSizes($type = 'card') {
    $gen = new ResponsiveImageGenerator();
    return $gen->generateSizes($type);
}
?>
