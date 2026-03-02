<?php
/**
 * JavaScript Minifier
 * Minifies JS files by removing whitespace, comments, and unnecessary characters
 */

class JSMinifier {
    /**
     * Minify JavaScript content
     */
    public static function minify($js) {
        // Remove single-line comments
        $js = preg_replace('~//.*?$~m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('~/\*.*?\*/~s', '', $js);
        
        // Remove whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces around special characters
        $js = preg_replace('/\s*([{}:;,=()[\]<>+\-*/%&|^!?])\s*/', '$1', $js);
        
        // Remove trailing semicolons before closing braces
        $js = preg_replace('/;(?=\})/', '', $js);
        
        // Remove leading/trailing whitespace
        $js = trim($js);
        
        return $js;
    }

    /**
     * Minify JS file
     */
    public static function minifyFile($inputFile, $outputFile = null) {
        if (!file_exists($inputFile)) {
            return ['success' => false, 'error' => 'Input file not found'];
        }

        $js = file_get_contents($inputFile);
        $minified = self::minify($js);

        if ($outputFile === null) {
            $outputFile = str_replace('.js', '.min.js', $inputFile);
        }

        if (file_put_contents($outputFile, $minified)) {
            $originalSize = strlen($js);
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
        echo "Usage: php minify-js.php <input.js> [output.min.js]\n";
        exit(1);
    }

    $inputFile = $argv[1];
    $outputFile = $argv[2] ?? null;

    $result = JSMinifier::minifyFile($inputFile, $outputFile);

    if ($result['success']) {
        echo "✓ JS Minified Successfully\n";
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

    $file = basename($file);
    if (!preg_match('/\.js$/', $file)) {
        echo json_encode(['success' => false, 'error' => 'Only JS files allowed']);
        exit;
    }

    $inputPath = __DIR__ . '/../' . $file;
    $outputPath = __DIR__ . '/../' . str_replace('.js', '.min.js', $file);

    $result = JSMinifier::minifyFile($inputPath, $outputPath);
    echo json_encode($result);
    exit;
}

// GET request - show available JS files
$jsFiles = glob(__DIR__ . '/../**/*.js', GLOB_RECURSIVE);
$files = [];

foreach ($jsFiles as $file) {
    $basename = basename($file);
    if (!preg_match('/\.min\.js$/', $basename) && !preg_match('/node_modules/', $file)) {
        $files[] = [
            'name' => $basename,
            'size' => filesize($file),
            'path' => str_replace(__DIR__ . '/../', '', $file)
        ];
    }
}

echo json_encode([
    'success' => true,
    'message' => 'JS Minifier - POST a file to minify',
    'jsFiles' => $files
]);
?>
