<?php
/**
 * Apply Standardized Header Structure to All Pages
 * This script updates all main pages with the new header structure
 */

// List of pages to update
$pages = [
    'about.php',
    'blog.php',
    'blogPageDetails.php',
    'calendar.php',
    'contact.php',
    'event.php',
    'exhibitions.php',
    'press.php',
    'privacy.php',
    'shop.php',
    'spaces.php',
    'terms.php'
];

// The standardized header structure to insert
$standardHeader = <<<'HEADER'
<header class="lakum-header" role="banner">
    <div class="lakum-header__container">
        <div class="lakum-header__logo">
            <a href="./" class="lakum-logo">
                <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
            </a>
        </div>
        <nav class="lakum-nav">
            <ul class="lakum-nav__list">
                <li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link"><?php echo t('home', 'Home'); ?></a></li>
                <li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link"><?php echo t('about', 'About'); ?></a></li>
                <li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                <li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                <li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                <li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog', 'Blog'); ?></a></li>
                <li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press', 'Press'); ?></a></li>
                <li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                <li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop', 'Shop'); ?></a></li>
            </ul>
        </nav>
        <div class="lakum-language-switcher">
            <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
                <i class="ri-global-line"></i>
                <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
            </a>
        </div>
        <button class="lakum-header__mobile-toggle" aria-label="Toggle menu">
            <span class="lakum-header__mobile-icon" aria-hidden="true"></span>
        </button>
    </div>
</header>
HEADER;

// The page loader structure
$pageLoader = <<<'LOADER'
<div class="lakum-page-loader" id="pageLoader">
    <div class="lakum-page-loader__content">
        <div class="lakum-page-loader__spinner"></div>
    </div>
</div>
LOADER;

$results = [];

foreach ($pages as $page) {
    if (!file_exists($page)) {
        $results[$page] = "❌ File not found";
        continue;
    }

    $content = file_get_contents($page);
    
    // Check if page already has the new header structure
    if (strpos($content, 'class="lakum-header"') !== false) {
        $results[$page] = "✓ Already has new header structure";
        continue;
    }

    // Find the body tag
    if (preg_match('/<body[^>]*>/', $content, $matches)) {
        $bodyTag = $matches[0];
        
        // Find position after body tag
        $bodyPos = strpos($content, $bodyTag) + strlen($bodyTag);
        
        // Find the page loader
        $loaderPattern = '/<div class="lakum-page-loader"[^>]*>.*?<\/div>\s*<\/div>\s*<\/div>/s';
        
        if (preg_match($loaderPattern, $content, $loaderMatches)) {
            $loaderPos = strpos($content, $loaderMatches[0]);
            $loaderEnd = $loaderPos + strlen($loaderMatches[0]);
            
            // Remove old loader
            $content = substr($content, 0, $loaderPos) . substr($content, $loaderEnd);
            
            // Insert new loader and header after body tag
            $bodyPos = strpos($content, $bodyTag) + strlen($bodyTag);
            $newContent = substr($content, 0, $bodyPos) . "\n" . $pageLoader . "\n" . $standardHeader . "\n" . substr($content, $bodyPos);
        } else {
            // No loader found, just insert after body tag
            $newContent = substr($content, 0, $bodyPos) . "\n" . $pageLoader . "\n" . $standardHeader . "\n" . substr($content, $bodyPos);
        }
        
        // Write back to file
        if (file_put_contents($page, $newContent)) {
            $results[$page] = "✓ Updated successfully";
        } else {
            $results[$page] = "❌ Failed to write file";
        }
    } else {
        $results[$page] = "❌ Could not find body tag";
    }
}

// Display results
echo "<!DOCTYPE html>
<html>
<head>
    <title>Header Structure Update Results</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>✨ Header Structure Update Results</h1>";

foreach ($results as $page => $status) {
    $class = 'info';
    if (strpos($status, '✓') === 0) {
        $class = 'success';
    } elseif (strpos($status, '❌') === 0) {
        $class = 'error';
    }
    echo "<div class='result $class'><strong>$page:</strong> $status</div>";
}

echo "    </div>
</body>
</html>";
?>
