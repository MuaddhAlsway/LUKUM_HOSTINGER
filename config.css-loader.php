<?php
/**
 * CSS Loader Configuration
 * Automatically loads minified CSS in production, readable CSS in development
 * 
 * Usage in your HTML/PHP:
 * <?php require_once 'config.css-loader.php'; ?>
 * <link rel="stylesheet" href="<?php echo getCSSFile('Home'); ?>">
 */

// Detect environment
// Set to 'production' on live server, 'development' locally
$environment = getenv('APP_ENV') ?: 'development';

// Alternative: Auto-detect based on domain
if (empty(getenv('APP_ENV'))) {
    $isDevelopment = in_array($_SERVER['HTTP_HOST'] ?? '', [
        'localhost',
        '127.0.0.1',
        'localhost:8000',
        'localhost:3000'
    ]);
    $environment = $isDevelopment ? 'development' : 'production';
}

/**
 * Get CSS file path based on environment
 * 
 * @param string $cssName CSS file name without extension (e.g., 'Home')
 * @return string Path to CSS file
 */
function getCSSFile($cssName) {
    global $environment;
    
    if ($environment === 'production') {
        // Use minified CSS in production
        $file = $cssName . '.min.css';
    } else {
        // Use readable CSS in development
        $file = $cssName . '.css';
    }
    
    // Verify file exists, fallback to original if minified doesn't exist
    if (!file_exists($file) && $environment === 'production') {
        $file = $cssName . '.css';
    }
    
    return $file;
}

/**
 * Get current environment
 */
function getEnvironment() {
    global $environment;
    return $environment;
}

/**
 * Check if in production
 */
function isProduction() {
    return getEnvironment() === 'production';
}

/**
 * Check if in development
 */
function isDevelopment() {
    return getEnvironment() === 'development';
}
?>
