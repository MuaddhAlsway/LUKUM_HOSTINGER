# Simple header replacement script
$files = @('about.php', 'blog.php', 'blogPageDetails.php', 'calendar.php', 'contact.php', 'event.php', 'exhibitions.php', 'index.php', 'press.php', 'privacy.php', 'shop.php', 'spaces.php', 'terms.php')

$newHeader = @'
<header class="app-header" role="banner">
    <div class="app-header__container">
        <div class="app-header__logo">
            <a href="index.php" class="app-logo">
                <img src="assest/logo/right_section.png" alt="LAKUM" class="app-logo__left" width="105" height="80" decoding="async">
                <img src="assest/logo/left_section.png" alt="Artspace" class="app-logo__right" width="105" height="80" decoding="async">
            </a>
        </div>

        <nav class="app-nav" id="appNav" role="navigation">
            <ul class="app-nav__list">
                <li class="app-nav__item"><a href="index.php" class="app-nav__link">Home</a></li>
                <li class="app-nav__item"><a href="about.php" class="app-nav__link">About</a></li>
                <li class="app-nav__item"><a href="spaces.php" class="app-nav__link">Spaces</a></li>
                <li class="app-nav__item"><a href="exhibitions.php" class="app-nav__link">Exhibitions</a></li>
                <li class="app-nav__item"><a href="calendar.php" class="app-nav__link">Calendar</a></li>
                <li class="app-nav__item"><a href="blog.php" class="app-nav__link">Blog</a></li>
                <li class="app-nav__item"><a href="press.php" class="app-nav__link">Press</a></li>
                <li class="app-nav__item"><a href="contact.php" class="app-nav__link">Contact</a></li>
                <li class="app-nav__item"><a href="shop.php" class="app-nav__link">Shop</a></li>
            </ul>
        </nav>

        <div class="app-header__controls">
            <div class="app-language-switcher">
                <a href="<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '/exhibitions.php?lang=en' : '/exhibitions.php?lang=ar'; ?>" class="app-lang-link" title="<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'Language: English' : 'Language: العربية'; ?>">
                    <i class="ri-global-line"></i>
                    <span class="app-lang-text"><?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'En' : 'Ar'; ?></span>
                </a>
            </div>

            <button class="app-header__menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="appNav">
                <span class="app-header__menu-icon"></span>
            </button>
        </div>
    </div>
</header>
'@

foreach ($file in $files) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        
        # Simple replacement - find <header class="lakum-header" and replace until </header>
        $pattern = '<header class="lakum-header"[^>]*>.*?</header>'
        
        if ($content -match $pattern) {
            $content = $content -replace $pattern, $newHeader
            Set-Content $file $content
            Write-Host "✓ Updated: $file"
        } else {
            Write-Host "⚠ Pattern not found in: $file"
        }
    } else {
        Write-Host "✗ File not found: $file"
    }
}

Write-Host "`n✓ All headers replaced!"
