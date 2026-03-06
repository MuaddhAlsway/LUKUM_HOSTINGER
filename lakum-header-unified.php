<?php
/**
 * LAKUM ARTSPACE - Unified Header Structure (PHP Version)
 * Complete header template for all pages
 * This is the single source of truth for all page headers
 */

// Get current language
$currentLang = getCurrentLanguage();
$isArabic = isArabic();
$htmlDir = $isArabic ? 'rtl' : 'ltr';
?>
<!-- ========================================
     LAKUM ARTSPACE - Unified Header Structure
     Complete PHP template for all pages
     ======================================== -->

<!-- ===== SKIP TO MAIN CONTENT (Accessibility - Disabled) ===== -->
<!-- Removed per user request -->

<!-- ===== HEADER ===== -->
<header class="lakum-header" role="banner">
    <div class="lakum-header__container">
        
        <!-- Logo -->
        <div class="lakum-header__logo">
            <a href="./" class="lakum-logo" aria-label="<?php echo t('lakum_home', 'Lakum Artspace Home'); ?>">
                <img 
                    src="assest/logo/right_section.png" 
                    alt="LAKUM" 
                    class="lakum-logo__left"
                    loading="eager"
                    decoding="async"
                >
                <img 
                    src="assest/logo/left_section.png" 
                    alt="Artspace" 
                    class="lakum-logo__right"
                    loading="eager"
                    decoding="async"
                >
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="lakum-nav" role="navigation" aria-label="<?php echo t('main_navigation', 'Main navigation'); ?>">
            <ul class="lakum-nav__list">
                <li class="lakum-nav__item">
                    <a href="index.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('home', 'Home'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="about.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('about', 'About'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="spaces.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'spaces.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('spaces', 'Spaces'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="exhibitions.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'exhibitions.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('exhibitions', 'Exhibitions'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="calendar.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'calendar.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('calendar', 'Calendar'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="blog.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'blog.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('blog', 'Blog'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="press.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'press.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('press', 'Press'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="contact.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('contact_us', 'Contact'); ?></a>
                </li>
                <li class="lakum-nav__item">
                    <a href="shop.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('shop', 'Shop'); ?></a>
                </li>
            </ul>
        </nav>

        <!-- Language Switcher -->
        <div class="lakum-language-switcher" role="region" aria-label="<?php echo t('language_selection', 'Language selection'); ?>">
            <a href="<?php echo buildLanguageSwitcherUrl('en'); ?>" class="lakum-lang-link <?php echo !$isArabic ? 'lakum-lang-link--active' : ''; ?>" aria-label="<?php echo t('switch_english', 'Switch to English'); ?>">
                <span class="lakum-lang-text">EN</span>
            </a>
            <a href="<?php echo buildLanguageSwitcherUrl('ar'); ?>" class="lakum-lang-link <?php echo $isArabic ? 'lakum-lang-link--active' : ''; ?>" aria-label="<?php echo t('switch_arabic', 'Switch to Arabic'); ?>">
                <span class="lakum-lang-text">AR</span>
            </a>
        </div>

        <!-- Mobile Toggle Button -->
        <button 
            class="lakum-header__mobile-toggle" 
            aria-expanded="false"
            aria-label="<?php echo t('toggle_menu', 'Toggle navigation menu'); ?>"
            aria-controls="lakum-nav-mobile"
        >
            <span class="lakum-header__mobile-icon"></span>
        </button>
    </div>
</header>

<!-- ===== MOBILE NAVIGATION (Off-canvas) ===== -->
<nav class="lakum-nav--mobile" id="lakum-nav-mobile" role="navigation" aria-label="<?php echo t('mobile_navigation', 'Mobile navigation'); ?>">
    <ul class="lakum-nav__list">
        <li class="lakum-nav__item">
            <a href="index.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('home', 'Home'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="about.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('about', 'About'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="spaces.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'spaces.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('spaces', 'Spaces'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="exhibitions.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'exhibitions.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('exhibitions', 'Exhibitions'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="calendar.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'calendar.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('calendar', 'Calendar'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="blog.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'blog.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('blog', 'Blog'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="press.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'press.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('press', 'Press'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="contact.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('contact_us', 'Contact'); ?></a>
        </li>
        <li class="lakum-nav__item">
            <a href="shop.php" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('shop', 'Shop'); ?></a>
        </li>
    </ul>
</nav>

<!-- ===== IMPLEMENTATION NOTES =====

1. CSS LOADING ORDER:
   - Load lakum-header-unified.css FIRST (before any other CSS)
   - This ensures all header styles override previous conflicting CSS
   - All rules use !important to guarantee override

2. MOBILE NAVIGATION:
   - Desktop nav (.lakum-nav) is hidden on mobile via CSS
   - Mobile nav (.lakum-nav--mobile) is shown on mobile via CSS
   - Both contain identical nav items for consistency

3. LANGUAGE SWITCHER:
   - Positioned in header on desktop
   - Positioned absolutely on mobile (top-right)
   - Supports RTL (right-to-left) languages

4. ACCESSIBILITY:
   - aria-expanded on toggle button (true/false)
   - aria-label on all interactive elements
   - aria-current="page" on active nav link
   - Skip to main content link
   - Semantic HTML (header, nav, main, footer)
   - Keyboard navigation (ESC to close nav)

5. RTL SUPPORT:
   - Add dir="rtl" to <html> tag for Arabic
   - CSS automatically handles RTL layout
   - Language switcher repositions correctly

6. RESPONSIVE BREAKPOINTS:
   - Desktop: > 1024px (80px header)
   - Tablet: 768px - 1024px (70px header)
   - Mobile: < 768px (60px header)
   - Small mobile: < 480px (60px header, smaller fonts)

7. Z-INDEX HIERARCHY (CRITICAL):
   - Header: 1000
   - Overlay: 9999
   - Nav content: 10000
   - Toggle: 10001
   - Never conflicts with other page elements

8. JAVASCRIPT FEATURES:
   - Mobile nav toggle with aria-expanded
   - Close on outside click (overlay click)
   - Close on ESC key
   - Prevent body scroll when nav open
   - Auto-set active nav link
   - Public API: window.LakumHeader

9. BODY PADDING:
   - Automatically added via CSS
   - Prevents content from hiding under fixed header
   - Responsive: changes with header height

10. FUTURE-PROOF:
    - No dependencies on previous CSS files
    - Works even if old CSS is removed
    - All styles scoped to .lakum-* classes
    - No conflicts with page-specific CSS
    - Extensible via CSS custom properties

===== END IMPLEMENTATION NOTES ===== -->

<!-- Header JavaScript Initialization -->
<script src="js/lakum-header-init.js" defer></script>
