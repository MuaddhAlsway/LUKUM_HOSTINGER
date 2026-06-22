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
$langParam = '?lang=' . $currentLang;
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
                <!-- HOME with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="index.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('home', 'Home'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Home submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="index.php<?php echo $langParam; ?>#upcoming-exhibitions" class="lakum-nav__dropdown-link"><?php echo t('nav_upcoming_exhibitions', 'Upcoming Exhibitions'); ?></a></li>
                        <li><a href="index.php<?php echo $langParam; ?>#past-exhibitions" class="lakum-nav__dropdown-link"><?php echo t('nav_past_exhibitions', 'Past Exhibitions'); ?></a></li>
                        <li><a href="index.php<?php echo $langParam; ?>#create-event" class="lakum-nav__dropdown-link"><?php echo t('nav_create_event', 'Create Your Event'); ?></a></li>
                    </ul>
                </li>

                <!-- ABOUT with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="about.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('about', 'About'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle About submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="about.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_about_us', 'Who We Are'); ?></a></li>
                        <li><a href="about.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_about_space', 'About Lakum Space'); ?></a></li>
                    </ul>
                </li>

                <!-- EXHIBITIONS (SPACES) with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="spaces.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'spaces.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('spaces', 'Exhibitions'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Exhibitions submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="spaces.php<?php echo $langParam; ?>#venue-intro" class="lakum-nav__dropdown-link"><?php echo t('nav_lakum_venue', 'Lakum Artspace Venue'); ?></a></li>
                        <li><a href="spaces.php<?php echo $langParam; ?>#facilities" class="lakum-nav__dropdown-link"><?php echo t('nav_our_facilities', 'Our Facilities'); ?></a></li>
                        <li><a href="spaces.php<?php echo $langParam; ?>#floor-maps" class="lakum-nav__dropdown-link"><?php echo t('nav_floor_maps', 'Floor Maps'); ?></a></li>
                        <li><a href="spaces.php<?php echo $langParam; ?>#pricing" class="lakum-nav__dropdown-link"><?php echo t('nav_space_pricing', 'Space Pricing'); ?></a></li>
                        <li><a href="spaces.php<?php echo $langParam; ?>#booking-form" class="lakum-nav__dropdown-link"><?php echo t('nav_book_event', 'Book Your Event'); ?></a></li>
                    </ul>
                </li>

                <!-- EVENTS with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="exhibitions.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'exhibitions.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('exhibitions', 'Events'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Events submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="exhibitions.php<?php echo $langParam; ?>#upcoming" class="lakum-nav__dropdown-link"><?php echo t('nav_upcoming_events', 'Upcoming'); ?></a></li>
                        <li><a href="exhibitions.php<?php echo $langParam; ?>#workshops" class="lakum-nav__dropdown-link"><?php echo t('nav_workshops', 'Workshops'); ?></a></li>
                    </ul>
                </li>

                <!-- VENUE HIRE with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="calendar.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'calendar.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('calendar', 'Venue Hire'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Venue Hire submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="calendar.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_events_calendar', 'Events Calendar'); ?></a></li>
                    </ul>
                </li>

                <!-- BLOG with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="blog.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'blog.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('blog', 'Blog'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Blog submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="blog.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_lakum_blog', 'Lakum Artspace Blog'); ?></a></li>
                    </ul>
                </li>

                <!-- PRESS with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="press.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'press.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('press', 'Press'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Press submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="press.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_read_about', 'Read About Lakum Artspace'); ?></a></li>
                    </ul>
                </li>

                <!-- CONTACT with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="contact.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('contact_us', 'Contact'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Contact submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="contact.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_get_in_touch', 'Get in Touch'); ?></a></li>
                    </ul>
                </li>

                <!-- SHOP with Dropdown -->
                <li class="lakum-nav__item lakum-nav__item--dropdown">
                    <a href="shop.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('shop', 'Shop'); ?></a>
                    <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Shop submenu" aria-expanded="false">
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="lakum-nav__dropdown">
                        <li><a href="shop.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_concept_store', 'Concept Store'); ?></a></li>
                    </ul>
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
        <!-- HOME with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="index.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('home', 'Home'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Home submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="index.php<?php echo $langParam; ?>#upcoming-exhibitions" class="lakum-nav__dropdown-link"><?php echo t('nav_upcoming_exhibitions', 'Upcoming Exhibitions'); ?></a></li>
                <li><a href="index.php<?php echo $langParam; ?>#past-exhibitions" class="lakum-nav__dropdown-link"><?php echo t('nav_past_exhibitions', 'Past Exhibitions'); ?></a></li>
                <li><a href="index.php<?php echo $langParam; ?>#create-event" class="lakum-nav__dropdown-link"><?php echo t('nav_create_event', 'Create Your Event'); ?></a></li>
            </ul>
        </li>

        <!-- ABOUT with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="about.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('about', 'About'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle About submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="about.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_about_us', 'Who We Are'); ?></a></li>
                <li><a href="about.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_about_space', 'About Lakum Space'); ?></a></li>
            </ul>
        </li>

        <!-- EXHIBITIONS (SPACES) with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="spaces.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'spaces.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('spaces', 'Exhibitions'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Exhibitions submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="spaces.php<?php echo $langParam; ?>#venue-intro" class="lakum-nav__dropdown-link"><?php echo t('nav_lakum_venue', 'Lakum Artspace Venue'); ?></a></li>
                <li><a href="spaces.php<?php echo $langParam; ?>#facilities" class="lakum-nav__dropdown-link"><?php echo t('nav_our_facilities', 'Our Facilities'); ?></a></li>
                <li><a href="spaces.php<?php echo $langParam; ?>#floor-maps" class="lakum-nav__dropdown-link"><?php echo t('nav_floor_maps', 'Floor Maps'); ?></a></li>
                <li><a href="spaces.php<?php echo $langParam; ?>#pricing" class="lakum-nav__dropdown-link"><?php echo t('nav_space_pricing', 'Space Pricing'); ?></a></li>
                <li><a href="spaces.php<?php echo $langParam; ?>#booking-form" class="lakum-nav__dropdown-link"><?php echo t('nav_book_event', 'Book Your Event'); ?></a></li>
            </ul>
        </li>

        <!-- EVENTS with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="exhibitions.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'exhibitions.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('exhibitions', 'Events'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Events submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="exhibitions.php<?php echo $langParam; ?>#upcoming" class="lakum-nav__dropdown-link"><?php echo t('nav_upcoming_events', 'Upcoming'); ?></a></li>
                <li><a href="exhibitions.php<?php echo $langParam; ?>#workshops" class="lakum-nav__dropdown-link"><?php echo t('nav_workshops', 'Workshops'); ?></a></li>
            </ul>
        </li>

        <!-- VENUE HIRE with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="calendar.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'calendar.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('calendar', 'Venue Hire'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Venue Hire submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="calendar.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_events_calendar', 'Events Calendar'); ?></a></li>
            </ul>
        </li>

        <!-- BLOG with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="blog.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'blog.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('blog', 'Blog'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Blog submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="blog.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_lakum_blog', 'Lakum Artspace Blog'); ?></a></li>
            </ul>
        </li>

        <!-- PRESS with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="press.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'press.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('press', 'Press'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Press submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="press.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_read_about', 'Read About Lakum Artspace'); ?></a></li>
            </ul>
        </li>

        <!-- CONTACT with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="contact.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('contact_us', 'Contact'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Contact submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="contact.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_get_in_touch', 'Get in Touch'); ?></a></li>
            </ul>
        </li>

        <!-- SHOP with Dropdown -->
        <li class="lakum-nav__item lakum-nav__item--dropdown">
            <a href="shop.php<?php echo $langParam; ?>" class="lakum-nav__link <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'lakum-nav__link--active' : ''; ?>"><?php echo t('shop', 'Shop'); ?></a>
            <button class="lakum-nav__dropdown-toggle" aria-label="Toggle Shop submenu" aria-expanded="false">
                <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="lakum-nav__dropdown">
                <li><a href="shop.php<?php echo $langParam; ?>" class="lakum-nav__dropdown-link"><?php echo t('nav_concept_store', 'Concept Store'); ?></a></li>
            </ul>
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

<!-- Dropdown Navigation Handler -->
<script src="js/lakum-header-dropdowns.js?v=2.0.0" defer></script>
