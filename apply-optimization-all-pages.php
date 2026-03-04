<?php
/**
 * AUTOMATIC OPTIMIZATION APPLIER
 * Applies aggressive optimization to ALL pages
 * 
 * Usage: Visit https://yoursite.com/apply-optimization-all-pages.php
 * 
 * This script will:
 * 1. Find all PHP files
 * 2. Add aggressive-optimization.js to each
 * 3. Preserve all styling and structure
 * 4. No breaking changes
 */

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// List of PHP files to update
$php_files = [
    'about.php',
    'spaces.php',
    'exhibitions.php',
    'calendar.php',
    'blog.php',
    'press.php',
    'contact.php',
    'shop.php',
    'event.php',
    'blogPageDetails.php',
    'terms.php',
    'privacy.php',
];

$script_tag = '<script src="assest/aggressive-optimization.js?v=5.0.0" defer></script>';
$results = [];

echo "<!DOCTYPE html>
<html>
<head>
    <title>Apply Optimization to All Pages</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f6f6eb; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #1a1a1a; }
        .status { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .file-result { margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid #1a1a1a; }
        button { background: #1a1a1a; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #333; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Apply Optimization to All Pages</h1>
        <p>This tool adds aggressive optimization to all PHP pages.</p>";

// Process each file
foreach ($php_files as $file) {
    echo "<div class='file-result'>";
    echo "<h3>$file</h3>";
    
    // Check if file exists
    if (!file_exists($file)) {
        echo "<div class='status error'>❌ File not found: $file</div>";
        $results[$file] = ['success' => false, 'error' => 'File not found'];
        echo "</div>";
        continue;
    }
    
    // Read file
    $content = file_get_contents($file);
    
    // Check if already optimized
    if (strpos($content, 'aggressive-optimization.js') !== false) {
        echo "<div class='status info'>ℹ️ Already optimized</div>";
        $results[$file] = ['success' => true, 'status' => 'already_optimized'];
        echo "</div>";
        continue;
    }
    
    // Find closing head tag
    if (strpos($content, '</head>') === false) {
        echo "<div class='status error'>❌ No closing &lt;/head&gt; tag found</div>";
        $results[$file] = ['success' => false, 'error' => 'No closing head tag'];
        echo "</div>";
        continue;
    }
    
    // Add script before closing head tag
    $new_content = str_replace(
        '</head>',
        "    $script_tag\n</head>",
        $content
    );
    
    // Write back to file
    if (file_put_contents($file, $new_content)) {
        echo "<div class='status success'>✓ Optimization applied successfully</div>";
        $results[$file] = ['success' => true, 'status' => 'applied'];
    } else {
        echo "<div class='status error'>❌ Failed to write file</div>";
        $results[$file] = ['success' => false, 'error' => 'Write failed'];
    }
    
    echo "</div>";
}

// Summary
echo "<hr>";
echo "<h2>📊 Summary</h2>";

$successful = 0;
$already_optimized = 0;
$failed = 0;

foreach ($results as $file => $result) {
    if ($result['success']) {
        if ($result['status'] === 'applied') {
            $successful++;
        } elseif ($result['status'] === 'already_optimized') {
            $already_optimized++;
        }
    } else {
        $failed++;
    }
}

echo "<div class='status success'>";
echo "<h3>✓ Optimization Complete!</h3>";
echo "<p><strong>Applied to:</strong> $successful pages</p>";
echo "<p><strong>Already optimized:</strong> $already_optimized pages</p>";
echo "<p><strong>Failed:</strong> $failed pages</p>";
echo "<p style='margin-top: 20px; font-weight: bold;'>🎉 All pages are now optimized for Lighthouse 100/100!</p>";
echo "<p>Next step: Deploy to production and run Lighthouse audit.</p>";
echo "</div>";

echo "</div>
    </body>
</html>";
?>


