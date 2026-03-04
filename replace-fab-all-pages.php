<?php
/**
 * Replace all old FAB buttons with new clean FAB button
 * Run this once to update all PHP files
 */

$newFabHtml = <<<'HTML'
<!-- Floating Action Button (FAB) - Contact Menu -->
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
HTML;

$phpFiles = [
    'about.php',
    'blog.php',
    'blogPageDetails.php',
    'calendar.php',
    'contact.php',
    'event.php',
    'exhibitions.php',
    'index.php',
    'press.php',
    'privacy.php',
    'shop.php',
    'spaces.php',
    'terms.php',
];

$oldFabPattern = '/<div class="lakum-contact-fab"[^>]*id="lakumContactFab"[^>]*>.*?<\/div>\s*<\/div>/s';

foreach ($phpFiles as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    
    // Replace old FAB with new FAB
    $newContent = preg_replace($oldFabPattern, $newFabHtml, $content);
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "✓ Updated: $file\n";
    } else {
        echo "✗ No changes: $file\n";
    }
}

echo "\nDone! All FAB buttons have been replaced.\n";
?>

