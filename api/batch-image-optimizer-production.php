<?php
/**
 * BATCH IMAGE OPTIMIZER - PRODUCTION READY
 * Compresses all images to responsive sizes
 * Run: php api/batch-image-optimizer-production.php
 */

set_time_limit(300);
ini_set('memory_limit', '512M');

class BatchImageOptimizer {
  private $sourceDir = 'assest/';
  private $heroDir = 'heroImage/';
  private $outputDir = 'assest/optimized/';
  private $log = [];

  public function run() {
    echo "Starting batch image optimization...\n";
    
    // Create output directory
    if (!is_dir($this->outputDir)) {
      mkdir($this->outputDir, 0755, true);
    }

    // Process hero images
    $this->processHeroImages();
    
    // Process regular images
    $this->processRegularImages();
    
    // Generate report
    $this->generateReport();
  }

  private function processHeroImages() {
    echo "\n=== Processing Hero Images ===\n";
    
    $files = glob($this->heroDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
    
    foreach ($files as $file) {
      if (is_file($file)) {
        $this->optimizeImage($file, true);
      }
    }
  }

  private function processRegularImages() {
    echo "\n=== Processing Regular Images ===\n";
    
    $files = glob($this->sourceDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
    
    foreach ($files as $file) {
      if (is_file($file)) {
        $this->optimizeImage($file, false);
      }
    }
  }

  private function optimizeImage($sourceFile, $isHero = false) {
    $baseName = basename($sourceFile);
    $name = pathinfo($baseName, PATHINFO_FILENAME);
    
    echo "Processing: $baseName\n";
    
    $breakpoints = $isHero 
      ? [320, 480, 768, 1024, 1600]
      : [320, 480, 768, 1024];
    
    foreach ($breakpoints as $width) {
      $quality = $this->getQuality($width, $isHero);
      $outputFile = $this->outputDir . $name . '-' . $width . '.webp';
      
      $this->compressToWebP($sourceFile, $outputFile, $width, $quality);
    }
  }

  private function compressToWebP($source, $output, $width, $quality) {
    // ImageMagick command
    $cmd = sprintf(
      'convert "%s" -resize %dx -quality %d -strip -interlace Plane "%s"',
      escapeshellarg($source),
      $width,
      $quality,
      escapeshellarg($output)
    );

    exec($cmd, $output, $returnCode);

    if ($returnCode === 0 && file_exists($output)) {
      $size = filesize($output) / 1024;
      echo "  ✓ $width: " . round($size, 1) . "KB\n";
      
      $this->log[] = [
        'file' => basename($output),
        'size' => round($size, 1),
        'width' => $width,
      ];
    } else {
      echo "  ✗ $width: Failed\n";
    }
  }

  private function getQuality($width, $isHero) {
    $qualities = [
      320 => 60,
      480 => 65,
      768 => 68,
      1024 => 70,
      1600 => 72,
    ];
    
    return $qualities[$width] ?? 70;
  }

  private function generateReport() {
    echo "\n=== Optimization Report ===\n";
    echo "Total images processed: " . count($this->log) . "\n";
    
    $totalSize = array_sum(array_column($this->log, 'size'));
    echo "Total size: " . round($totalSize, 1) . "KB\n";
    
    echo "\nImages by size:\n";
    foreach ($this->log as $item) {
      echo "  {$item['file']}: {$item['size']}KB ({$item['width']}w)\n";
    }
  }
}

$optimizer = new BatchImageOptimizer();
$optimizer->run();
?>

