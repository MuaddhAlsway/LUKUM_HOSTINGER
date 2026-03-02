<?php
/**
 * CSS Minifier - Production Ready
 * Removes whitespace, comments, and unnecessary characters
 * Usage: php api/minify-css.php
 */

class CSSMinifier {
    
    /**
     * Minify CSS content
     */
    public static function minify($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace around special characters
        $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
        
        // Remove leading/trailing whitespace
        $css = trim($css);
        
        // Remove unnecessary semicolons before closing braces
        $css = str_replace(';}', '}', $css);
        
        // Remove multiple spaces
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove spaces around operators in media queries
        $css = preg_replace('/\s*(and|or)\s+/', '$1 ', $css);
        
        return $css;
    }
    
    /**
     * Minify a CSS file
     */
    public static function minifyFile($inputFile, $outputFile) {
        if (!file_exists($inputFile)) {
            return ['success' => false, 'error' => "Input file not found: $inputFile"];
        }
        
        $css = file_get_contents($inputFile);
        $minified = self::minify($css);
        
        $originalSize = strlen($css);
        $minifiedSize = strlen($minified);
        $savings = round((1 - ($minifiedSize / $originalSize)) * 100, 2);
        
        if (file_put_contents($outputFile, $minified)) {
            return [
                'success' => true,
                'input' => $inputFile,
                'output' => $outputFile,
                'originalSize' => $originalSize,
                'minifiedSize' => $minifiedSize,
                'savings' => $savings . '%',
                'message' => "✅ CSS minified successfully!"
            ];
        } else {
            return ['success' => false, 'error' => "Failed to write output file: $outputFile"];
        }
    }
}

// Run if executed directly
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    echo "=== CSS Minifier ===\n\n";
    
    $result = CSSMinifier::minifyFile('Home.css', 'Home.min.css');
    
    if ($result['success']) {
        echo "✅ " . $result['message'] . "\n";
        echo "Input: " . $result['input'] . " (" . $result['originalSize'] . " bytes)\n";
        echo "Output: " . $result['output'] . " (" . $result['minifiedSize'] . " bytes)\n";
        echo "Savings: " . $result['savings'] . "\n";
    } else {
        echo "❌ Error: " . $result['error'] . "\n";
    }
}

// Also allow web access for minification
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['minify'])) {
    header('Content-Type: application/json');
    
    $result = CSSMinifier::minifyFile('Home.css', 'Home.min.css');
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}
?>
