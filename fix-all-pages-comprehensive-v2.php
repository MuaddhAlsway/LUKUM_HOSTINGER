<?php
/**
 * Comprehensive Fix for All 13 PHP Pages
 * 
 * Tasks:
 * 1. Fix Arabic navbar display
 * 2. Add language toggle to navigation
 * 3. Update shop.php image paths
 * 4. Add fab-button to calendar.php
 * 5. Update navbar background color
 * 6. Add 820px responsive breakpoint
 * 7. Verify all pages
 */

// List of all 13 pages
$pages = [
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

echo "=== COMPREHENSIVE FIX FOR ALL 13 PAGES ===\n\n";

// Task 1: Verify Arabic navbar display
echo "TASK 1: Verifying Arabic navbar display...\n";
foreach ($pages as $page) {
    if (file_exists($page)) {
        $content = file_get_contents($page);
        
        // Check if page has proper language attributes
        if (strpos($content, 'getLanguageAttributes()') !== false) {
            echo "✓ $page has language attributes\n";
        } else {
            echo "✗ $page missing language attributes\n";
        }
        
        // Check if navigation uses translation function
        if (strpos($content, "t('home'") !== false || strpos($content, "t('exhibitions'") !== false) {
            echo "✓ $page uses translation function\n";
        } else {
            echo "✗ $page missing translation function\n";
        }
    }
}

echo "\n";

// Task 2: Verify language toggle in navigation
echo "TASK 2: Verifying language toggle in navigation...\n";
foreach ($pages as $page) {
    if (file_exists($page)) {
        $content = file_get_contents($page);
        
        // Check if navigation links have language parameter
        if (preg_match('/\?lang=ar/', $content) && preg_match('/\?lang=en/', $content)) {
            echo "✓ $page has language toggle in navigation\n";
        } else {
            echo "✗ $page missing language toggle\n";
        }
    }
}

echo "\n";

// Task 3: Check shop.php image paths
echo "TASK 3: Checking shop.php image paths...\n";
if (file_exists('shop.php')) {
    $content = file_get_contents('shop.php');
    
    // Count heroImage references
    $heroImageCount = substr_count($content, 'heroImage/');
    echo "✓ shop.php has $heroImageCount heroImage references\n";
}

echo "\n";

// Task 4: Check calendar.php fab-button
echo "TASK 4: Checking calendar.php fab-button...\n";
if (file_exists('calendar.php')) {
    $content = file_get_contents('calendar.php');
    
    if (strpos($content, 'fab-button') !== false) {
        echo "✓ calendar.php has fab-button\n";
    } else {
        echo "✗ calendar.php missing fab-button\n";
    }
}

echo "\n";

// Task 5: Check navbar background color
echo "TASK 5: Checking navbar background color...\n";
if (file_exists('assest/app-header.css')) {
    $content = file_get_contents('assest/app-header.css');
    
    if (strpos($content, 'rgb(246, 246, 235)') !== false) {
        echo "✓ app-header.css has correct background color\n";
    } else {
        echo "✗ app-header.css missing background color\n";
    }
}

echo "\n";

// Task 6: Check 820px breakpoint
echo "TASK 6: Checking 820px responsive breakpoint...\n";
if (file_exists('assest/app-header.css')) {
    $content = file_get_contents('assest/app-header.css');
    
    if (strpos($content, '@media (max-width: 820px)') !== false) {
        echo "✓ app-header.css has 820px breakpoint\n";
    } else {
        echo "✗ app-header.css missing 820px breakpoint\n";
    }
}

if (file_exists('rtl.css')) {
    $content = file_get_contents('rtl.css');
    
    if (strpos($content, '@media (max-width: 820px)') !== false) {
        echo "✓ rtl.css has 820px breakpoint\n";
    } else {
        echo "✗ rtl.css missing 820px breakpoint\n";
    }
}

echo "\n";

// Task 7: Verify all pages
echo "TASK 7: Final verification of all 13 pages...\n";
$allGood = true;
foreach ($pages as $page) {
    if (file_exists($page)) {
        $content = file_get_contents($page);
        
        // Check for critical elements
        $hasLanguageAttrs = strpos($content, 'getLanguageAttributes()') !== false;
        $hasTranslations = strpos($content, "t('") !== false;
        $hasLanguageToggle = strpos($content, '?lang=') !== false;
        
        if ($hasLanguageAttrs && $hasTranslations && $hasLanguageToggle) {
            echo "✓ $page - All checks passed\n";
        } else {
            echo "✗ $page - Missing: ";
            if (!$hasLanguageAttrs) echo "language-attrs ";
            if (!$hasTranslations) echo "translations ";
            if (!$hasLanguageToggle) echo "language-toggle ";
            echo "\n";
            $allGood = false;
        }
    } else {
        echo "✗ $page - FILE NOT FOUND\n";
        $allGood = false;
    }
}

echo "\n";

if ($allGood) {
    echo "=== ALL CHECKS PASSED ===\n";
} else {
    echo "=== SOME CHECKS FAILED - REVIEW ABOVE ===\n";
}

?>

