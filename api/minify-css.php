<?php
/**
 * CSS Minifier
 * Minifies CSS files by removing whitespace, comments, and unnecessary characters
 * 
 * Usage:
 * 1. Place CSS file in same directory
 * 2. Call: php minify-css.php input.css output.min.css
 * 3. Or use web interface: minify-css.php?file=Home.css
 */

class CSSMinifier {
    /**
     * Minify CSS content
     */
    public static function minify($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove spaces around special characters
        $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
        
        // Remove trailing semicolons before closing braces
        $css = preg_replace('/;(?=\})/', '', $css);
        
        // Remove leading/trailing whitespace
        $css = trim($css);
        
        return $css;
    }

    /**
     * Minify CSS file
     */
    public static function minifyFile($inputFile, $outputFile = null) {
        if (!file_exists($inputFile)) {
            return ['success' => false, 'error' => 'Input file not found'];
        }

        $css = file_get_contents($inputFile);
        $minified = self::minify($css);

        if ($outputFile === null) {
            $outputFile = str_replace('.css', '.min.css', $inputFile);
        }

        if (file_put_contents($outputFile, $minified)) {
            $originalSize = strlen($css);
            $minifiedSize = strlen($minified);
            $reduction = round((1 - $minifiedSize / $originalSize) * 100, 2);

            return [
                'success' => true,
                'input' => $inputFile,
                'output' => $outputFile,
                'originalSize' => $originalSize,
                'minifiedSize' => $minifiedSize,
                'reduction' => $reduction . '%'
            ];
        } else {
            return ['success' => false, 'error' => 'Could not write output file'];
        }
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    if ($argc < 2) {
        echo "Usage: php minify-css.php <input.css> [output.min.css]\n";
        exit(1);
    }

    $inputFile = $argv[1];
    $outputFile = $argv[2] ?? null;

    $result = CSSMinifier::minifyFile($inputFile, $outputFile);

    if ($result['success']) {
        echo "✓ CSS Minified Successfully\n";
        echo "Input: {$result['input']}\n";
        echo "Output: {$result['output']}\n";
        echo "Original Size: " . number_format($result['originalSize']) . " bytes\n";
        echo "Minified Size: " . number_format($result['minifiedSize']) . " bytes\n";
        echo "Reduction: {$result['reduction']}\n";
    } else {
        echo "✗ Error: {$result['error']}\n";
        exit(1);
    }
    exit(0);
}

// Web Interface
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'] ?? null;

    if (!$file) {
        echo json_encode(['success' => false, 'error' => 'No file specified']);
        exit;
    }

    // Security: Only allow CSS files in current directory
    $file = basename($file);
    if (!preg_match('/\.css$/', $file)) {
        echo json_encode(['success' => false, 'error' => 'Only CSS files allowed']);
        exit;
    }

    $inputPath = __DIR__ . '/../' . $file;
    $outputPath = __DIR__ . '/../' . str_replace('.css', '.min.css', $file);

    $result = CSSMinifier::minifyFile($inputPath, $outputPath);
    echo json_encode($result);
    exit;
}

// GET request - show available CSS files
$cssFiles = glob(__DIR__ . '/../*.css');
$files = [];

foreach ($cssFiles as $file) {
    $basename = basename($file);
    if (!preg_match('/\.min\.css$/', $basename)) {
        $files[] = [
            'name' => $basename,
            'size' => filesize($file),
            'path' => $file
        ];
    }
}

echo json_encode([
    'success' => true,
    'message' => 'CSS Minifier - POST a file to minify',
    'cssFiles' => $files
]);
?>

