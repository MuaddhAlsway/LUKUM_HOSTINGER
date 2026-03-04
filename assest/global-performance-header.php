<?php
/**
 * GLOBAL PERFORMANCE OPTIMIZATION HEADER
 * Include this in ALL pages for automatic optimization
 * 
 * Usage: Add to top of every page:
 * <?php include 'assest/global-performance-header.php'; ?>
 */

// Enable compression
if (!ob_get_level()) {
    ob_start('ob_gzhandler');
}

// Set cache headers for static assets
header('Cache-Control: public, max-age=31536000, immutable');
header('Vary: Accept-Encoding');

// Enable GZIP compression
if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', 6);
}

// Optimize PHP execution
ini_set('memory_limit', '256M');
set_time_limit(30);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Performance headers
header('Link: <global-styles.css>; rel=preload; as=style', false);
header('Link: <lakum-components.css>; rel=preload; as=style', false);
header('Link: <assest/remixicon-minimal.css>; rel=preload; as=style', false);

// Early hints (if supported)
if (function_exists('header')) {
    // Preconnect to CDN
    header('Link: <https://cdn.jsdelivr.net>; rel=preconnect; crossorigin', false);
}

// Aggressive image optimization
function optimize_image_urls($html) {
    // Add loading="lazy" to all images
    $html = preg_replace('/<img([^>]*?)>/i', '<img$1 loading="lazy" decoding="async">', $html);
    
    // Add width/height if missing
    $html = preg_replace_callback('/<img([^>]*?)src="([^"]*?)"([^>]*?)>/i', function($matches) {
        $tag = $matches[1];
        $src = $matches[2];
        $rest = $matches[3];
        
        // Check if width/height already present
        if (strpos($tag . $rest, 'width=') === false && strpos($tag . $rest, 'height=') === false) {
            // Add default dimensions
            return '<img' . $tag . 'src="' . $src . '" width="800" height="600"' . $rest . '>';
        }
        
        return $matches[0];
    }, $html);
    
    return $html;
}

// Output buffering callback
function optimize_html_output($buffer) {
    // Minify HTML
    $buffer = preg_replace('/\s+/', ' ', $buffer);
    $buffer = preg_replace('/>\s+</', '><', $buffer);
    
    // Optimize images
    $buffer = optimize_image_urls($buffer);
    
    // Remove comments (except IE conditionals)
    $buffer = preg_replace('/<!--(?!\[if).*?-->/s', '', $buffer);
    
    return $buffer;
}

// Register output buffer callback
ob_start('optimize_html_output');

// Ensure buffer is flushed on shutdown
register_shutdown_function(function() {
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
});

// Aggressive image compression on-the-fly
function serve_optimized_image($image_path) {
    if (!file_exists($image_path)) {
        return false;
    }
    
    $file_size = filesize($image_path);
    
    // If image is larger than 500KB, compress it
    if ($file_size > 500000 && extension_loaded('gd')) {
        $image_info = getimagesize($image_path);
        $image_type = $image_info[2];
        
        if ($image_type == IMAGETYPE_WEBP || $image_type == IMAGETYPE_JPEG || $image_type == IMAGETYPE_PNG) {
            // Load image
            if ($image_type == IMAGETYPE_WEBP) {
                $image = imagecreatefromwebp($image_path);
            } elseif ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($image_path);
            } else {
                $image = imagecreatefrompng($image_path);
            }
            
            if ($image) {
                // Compress with quality 70
                ob_start();
                imagewebp($image, null, 70);
                $compressed = ob_get_clean();
                imagedestroy($image);
                
                // Save compressed version
                $compressed_path = $image_path . '.compressed';
                file_put_contents($compressed_path, $compressed);
                
                // Serve compressed if smaller
                if (filesize($compressed_path) < $file_size) {
                    return $compressed_path;
                } else {
                    unlink($compressed_path);
                }
            }
        }
    }
    
    return $image_path;
}

// API response optimization
function optimize_api_response($data) {
    // Remove unnecessary fields
    if (is_array($data)) {
        foreach ($data as &$item) {
            if (is_array($item)) {
                // Remove debug fields
                unset($item['debug']);
                unset($item['_debug']);
                unset($item['temp']);
            }
        }
    }
    
    return $data;
}

// Database query optimization
function optimize_database_queries() {
    // This would be implemented in your database layer
    // Add indexes, use prepared statements, cache results
}

// CSS optimization
function get_optimized_css() {
    $css_files = [
        'global-styles.css',
        'lakum-components.css',
        'assest/remixicon-minimal.css'
    ];
    
    $output = '';
    foreach ($css_files as $file) {
        if (file_exists($file)) {
            $output .= file_get_contents($file);
        }
    }
    
    // Minify CSS
    $output = preg_replace('/\/\*.*?\*\//s', '', $output);
    $output = preg_replace('/\s+/', ' ', $output);
    $output = preg_replace('/\s*([{}:;,])\s*/', '$1', $output);
    
    return $output;
}

// JavaScript optimization
function get_optimized_js() {
    $js_files = [
        'assest/aggressive-optimization.js'
    ];
    
    $output = '';
    foreach ($js_files as $file) {
        if (file_exists($file)) {
            $output .= file_get_contents($file);
        }
    }
    
    return $output;
}

// Export functions for use in pages
if (!function_exists('get_optimized_css')) {
    function get_optimized_css() {
        return '';
    }
}

if (!function_exists('get_optimized_js')) {
    function get_optimized_js() {
        return '';
    }
}

if (!function_exists('optimize_api_response')) {
    function optimize_api_response($data) {
        return $data;
    }
}

if (!function_exists('serve_optimized_image')) {
    function serve_optimized_image($path) {
        return $path;
    }
}
?>

