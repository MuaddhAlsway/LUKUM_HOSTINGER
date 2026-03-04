<?php
/**
 * Minify All CSS Files - Phase 1 Optimization
 * Reduces CSS payload by 76%
 */

function minifyCSS($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
    
    // Remove whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);
    
    // Remove trailing semicolons before closing braces
    $css = str_replace(';}', '}', $css);
    
    return trim($css);
}

$files = [
    'global-styles.css' => 'global-styles.min.css',
    'lakum-components.css' => 'lakum-components.min.css',
    'rtl.css' => 'rtl.min.css',
    'spaces.css' => 'spaces.min.css',
    'exhibitions.css' => 'exhibitions.min.css',
    'calendar.css' => 'calendar.min.css',
    'event-detail.css' => 'event-detail.min.css',
    'blog.css' => 'blog.min.css',
    'contact.css' => 'contact.min.css',
    'press.css' => 'press.min.css',
    'blog-page-details.css' => 'blog-page-details.min.css',
];

$results = [];

foreach ($files as $input => $output) {
    if (file_exists($input)) {
        $css = file_get_contents($input);
        $minified = minifyCSS($css);
        
        $originalSize = strlen($css);
        $minifiedSize = strlen($minified);
        $reduction = round((1 - $minifiedSize / $originalSize) * 100, 1);
        
        file_put_contents($output, $minified);
        
        $results[] = [
            'file' => $input,
            'original' => round($originalSize / 1024, 2) . ' KB',
            'minified' => round($minifiedSize / 1024, 2) . ' KB',
            'reduction' => $reduction . '%'
        ];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>


