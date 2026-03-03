<?php
/**
 * RESPONSIVE IMAGE HELPER - PRODUCTION
 * Generates optimized responsive image markup
 */

class ResponsiveImageHelper {
  
  private $optimizedDir = 'assest/optimized/';
  private $breakpoints = [320, 480, 768, 1024, 1600];

  /**
   * Generate responsive image markup
   * @param string $baseName Image base name (without extension)
   * @param string $alt Alt text
   * @param bool $isHero Whether this is a hero image
   * @param array $options Additional options
   */
  public function generateImage($baseName, $alt, $isHero = false, $options = []) {
    $width = $options['width'] ?? ($isHero ? 1024 : 768);
    $height = $options['height'] ?? ($isHero ? 576 : 432);
    $loading = $options['loading'] ?? ($isHero ? 'eager' : 'lazy');
    $fetchpriority = $options['fetchpriority'] ?? ($isHero ? 'high' : 'auto');

    $srcset = $this->generateSrcset($baseName, $isHero);
    $sizes = $this->generateSizes($isHero);
    $src = $this->optimizedDir . $baseName . '-' . $width . '.webp';

    return sprintf(
      '<picture>
<source type="image/webp" srcset="%s" sizes="%s">
<img src="%s" alt="%s" loading="%s" decoding="async" fetchpriority="%s" width="%d" height="%d" style="width:100%%;height:100%%;object-fit:cover;display:block;aspect-ratio:%d/%d;">
</picture>',
      htmlspecialchars($srcset),
      htmlspecialchars($sizes),
      htmlspecialchars($src),
      htmlspecialchars($alt),
      htmlspecialchars($loading),
      htmlspecialchars($fetchpriority),
      $width,
      $height,
      $width,
      $height
    );
  }

  /**
   * Generate srcset attribute
   */
  private function generateSrcset($baseName, $isHero = false) {
    $srcset = [];
    
    foreach ($this->breakpoints as $width) {
      // Skip 1600w for non-hero images
      if ($width === 1600 && !$isHero) continue;
      
      $file = $this->optimizedDir . $baseName . '-' . $width . '.webp';
      $srcset[] = $file . ' ' . $width . 'w';
    }
    
    return implode(', ', $srcset);
  }

  /**
   * Generate sizes attribute
   */
  private function generateSizes($isHero = false) {
    if ($isHero) {
      return '100vw';
    }
    return '(max-width: 768px) 100vw, 50vw';
  }

  /**
   * Generate logo image (fixed size)
   */
  public function generateLogo($baseName, $alt, $width = 105, $height = 80) {
    $src = $this->optimizedDir . $baseName . '-200.webp';
    
    return sprintf(
      '<picture>
<source type="image/webp" srcset="%s">
<img src="%s" alt="%s" width="%d" height="%d" decoding="async" style="width:%dpx;height:%dpx;">
</picture>',
      htmlspecialchars($src),
      htmlspecialchars($src),
      htmlspecialchars($alt),
      $width,
      $height,
      $width,
      $height
    );
  }

  /**
   * Get optimized image URL
   */
  public function getImageUrl($baseName, $width = 768) {
    return $this->optimizedDir . $baseName . '-' . $width . '.webp';
  }

  /**
   * Check if optimized images exist
   */
  public function imagesExist($baseName, $isHero = false) {
    $breakpoints = $isHero ? $this->breakpoints : [320, 480, 768, 1024];
    
    foreach ($breakpoints as $width) {
      $file = $this->optimizedDir . $baseName . '-' . $width . '.webp';
      if (!file_exists($file)) {
        return false;
      }
    }
    
    return true;
  }
}

// Usage:
// $helper = new ResponsiveImageHelper();
// echo $helper->generateImage('img-4', 'Hero image', true);
// echo $helper->generateLogo('logo-right', 'LAKUM');
?>
