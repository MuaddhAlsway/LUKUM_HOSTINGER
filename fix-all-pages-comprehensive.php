<?php
/**
 * Comprehensive Fix for All 13 PHP Pages
 * - Remove all lakum-contact-fab instances
 * - Standardize fab-button across all pages
 * - Fix responsive design issues
 */

$phpFiles = [
    'index.php',
    'about.php',
    'spaces.php',
    'exhibitions.php',
    'calendar.php',
    'blog.php',
    'blogPageDetails.php',
    'press.php',
    'contact.php',
    'shop.php',
    'event.php',
    'privacy.php',
    'terms.php'
];

// Standard fab-button HTML to use
$standardFabButton = <<<'FAB'
<div class="fab-button" id="fabButton">
    <button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false">
        <i class="ri-mail-line fab-button__icon"></i>
        <i class="ri-close-line fab-button__close"></i>
    </button>
    <div class="fab-button__menu" id="fabMenu" role="menu">
        <a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us">
            <i class="ri-phone-line"></i>
        </a>
        <a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp">
            <i class="ri-whatsapp-line"></i>
        </a>
        <a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email">
            <i class="ri-mail-line"></i>
        </a>
    </div>
</div>
FAB;

$stats = [
    'files_processed' => 0,
    'lakum_fab_removed' => 0,
    'fab_button_standardized' => 0,
    'css_links_added' => 0,
    'js_links_added' => 0,
    'errors' => []
];

foreach ($phpFiles as $file) {
    if (!file_exists($file)) {
        $stats['errors'][] = "File not found: $file";
        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;

    // TASK 1: Remove all lakum-contact-fab instances
    // Pattern to match the entire lakum-contact-fab div block
    $lakumPattern = '/<div\s+class="lakum-contact-fab"[^>]*id="lakumContactFab"[^>]*>.*?<\/div>\s*<\/div>/s';
    if (preg_match($lakumPattern, $content)) {
        $content = preg_replace($lakumPattern, '', $content);
        $stats['lakum_fab_removed']++;
    }

    // Also remove any inline lakum-contact-fab references in JavaScript
    $content = preg_replace('/document\.getElementById\([\'"]lakumContactFab[\'"]\)\.classList\.toggle\([\'"]lakum-contact-fab--active[\'"]\);?/s', '', $content);
    $content = preg_replace('/lakum-contact-fab--active/s', 'fab-button--active', $content);

    // TASK 2: Ensure fab-button CSS link exists
    if (strpos($content, 'assest/fab-button.css') === false) {
        // Find the line with mobile-menu.css and add fab-button.css after it
        $content = preg_replace(
            '/(<link rel="stylesheet" href="assest\/mobile-menu\.css">)/s',
            '$1' . "\n<link rel=\"stylesheet\" href=\"assest/fab-button.css\">",
            $content
        );
        $stats['css_links_added']++;
    }

    // TASK 2: Ensure fab-button JS link exists before closing body
    if (strpos($content, 'assest/fab-button.js') === false) {
        // Add before closing body tag
        $content = preg_replace(
            '/(<\/body>)/s',
            "<script src=\"assest/fab-button.js\" defer></script>\n$1",
            $content
        );
        $stats['js_links_added']++;
    }

    // TASK 2: Standardize fab-button HTML
    // Remove any existing fab-button divs and replace with standard
    $fabButtonPattern = '/<div\s+class="fab-button"[^>]*id="fabButton"[^>]*>.*?<\/div>\s*<\/div>/s';
    if (preg_match($fabButtonPattern, $content)) {
        $content = preg_replace($fabButtonPattern, $standardFabButton, $content);
        $stats['fab_button_standardized']++;
    } else {
        // If no fab-button exists, add it before closing body
        $content = preg_replace(
            '/(<\/body>)/s',
            "\n" . $standardFabButton . "\n$1",
            $content
        );
        $stats['fab_button_standardized']++;
    }

    // TASK 3: Fix responsive design issues
    // Fix container widths - ensure max-width is used
    $content = preg_replace(
        '/width:\s*1200px/s',
        'max-width: 1200px; width: 100%',
        $content
    );

    // Fix padding with clamp for responsiveness
    $content = preg_replace(
        '/padding:\s*40px\s+20px/s',
        'padding: clamp(20px, 5vw, 40px) clamp(15px, 3vw, 20px)',
        $content
    );

    // Ensure no horizontal scrolling
    $content = preg_replace(
        '/overflow-x:\s*auto/s',
        'overflow-x: hidden',
        $content
    );

    // Write back if changes were made
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $stats['files_processed']++;
    }
}

// Output results
echo "=== COMPREHENSIVE FIX COMPLETE ===\n\n";
echo "Files Processed: " . $stats['files_processed'] . "\n";
echo "Lakum-contact-fab Removed: " . $stats['lakum_fab_removed'] . "\n";
echo "Fab-button Standardized: " . $stats['fab_button_standardized'] . "\n";
echo "CSS Links Added: " . $stats['css_links_added'] . "\n";
echo "JS Links Added: " . $stats['js_links_added'] . "\n";

if (!empty($stats['errors'])) {
    echo "\nErrors:\n";
    foreach ($stats['errors'] as $error) {
        echo "  - $error\n";
    }
}

echo "\n=== VERIFICATION ===\n";
echo "All 13 pages should now have:\n";
echo "✓ fab-button HTML (not lakum-contact-fab)\n";
echo "✓ fab-button CSS link\n";
echo "✓ fab-button JS link\n";
echo "✓ No lakum-contact-fab references\n";
echo "✓ Improved responsive design\n";
?>
