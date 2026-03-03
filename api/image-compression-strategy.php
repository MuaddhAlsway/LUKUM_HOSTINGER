<?php
/**
 * IMAGE COMPRESSION STRATEGY
 * 
 * Target sizes per breakpoint:
 * - 320w: 320px width, ≤50KB
 * - 480w: 480px width, ≤80KB
 * - 768w: 768px width, ≤120KB
 * - 1024w: 1024px width, ≤150KB
 * - 1600w: 1600px width, ≤250KB (hero only)
 * 
 * Quality settings:
 * - Normal images: WebP quality 65-70
 * - Hero images: WebP quality 70-75
 * - Thumbnails: WebP quality 60
 */

class ImageCompressionStrategy {
  
  private $sourceDir = 'assest/';
  private $outputDir = 'assest/optimized/';
  private $heroDir = 'heroImage/';
  
  private $breakpoints = [
    320 => ['quality' => 60, 'maxSize' => 50],
    480 => ['quality' => 65, 'maxSize' => 80],
    768 => ['quality' => 68, 'maxSize' => 120],
    1024 => ['quality' => 70, 'maxSize' => 150],
    1600 => ['quality' => 72, 'maxSize' => 250],
  ];

  public function generateResponsiveImages($sourceFile, $baseName, $isHero = false) {
    $results = [];
    
    foreach ($this->breakpoints as $width => $config) {
      // Skip 1600w for non-hero images
      if ($width === 1600 && !$isHero) continue;
      
      $outputFile = $this->getOutputPath($baseName, $width);
      $result = $this->compressImage(
        $sourceFile,
        $outputFile,
        $width,
        $config['quality']
      );
      
      $results[$width] = $result;
    }
    
    return $results;
  }

  private function compressImage($source, $output, $width, $quality) {
    // Ensure output directory exists
    $outputDir = dirname($output);
    if (!is_dir($outputDir)) {
      mkdir($outputDir, 0755, true);
    }

    // Use ImageMagick or GD to resize and compress
    $cmd = sprintf(
      'convert "%s" -resize %dx -quality %d -strip "%s"',
      escapeshellarg($source),
      $width,
      $quality,
      escapeshellarg($output)
    );

    exec($cmd, $output, $returnCode);

    if ($returnCode === 0 && file_exists($output)) {
      $fileSize = filesize($output) / 1024; // KB
      return [
        'success' => true,
        'file' => $output,
        'size' => round($fileSize, 2),
        'width' => $width,
      ];
    }

    return [
      'success' => false,
      'error' => 'Compression failed',
    ];
  }

  private function getOutputPath($baseName, $width) {
    $name = pathinfo($baseName, PATHINFO_FILENAME);
    return $this->outputDir . $name . '-' . $width . '.webp';
  }

  public function generateSrcset($baseName, $isHero = false) {
    $srcset = [];
    
    foreach ($this->breakpoints as $width => $config) {
      if ($width === 1600 && !$isHero) continue;
      
      $file = $this->getOutputPath($baseName, $width);
      $srcset[] = $file . ' ' . $width . 'w';
    }
    
    return implode(', ', $srcset);
  }

  public function generateSizes($isHero = false) {
    if ($isHero) {
      return '100vw';
    }
    return '(max-width: 768px) 100vw, 50vw';
  }
}

// Usage example:
// $strategy = new ImageCompressionStrategy();
// $results = $strategy->generateResponsiveImages('source.jpg', 'image', false);
// $srcset = $strategy->generateSrcset('image', false);
// $sizes = $strategy->generateSizes(false);
?>
