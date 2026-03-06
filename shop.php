<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - LAKUM Artspace</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

    <!-- Preload LCP image (hero) - Mobile-first with responsive variants -->
    <link rel="preload" as="image" 
          href="heroImage/img-3.webp"
          imagesrcset="heroImage/img-3.webp 1200w"
          imagesizes="(max-width: 768px) 100vw, 650px"
          fetchpriority="high">
    <!-- Preload critical fonts -->
    <link rel="preload" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>

    <!-- Inline Critical CSS for Instant LCP -->
    <style>
        /* Critical CSS - Inline for instant rendering */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            background: #f6f6eb;
            color: #1a1a1a;
            overflow-x: hidden;
            line-height: 1.6
        }
        
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
        }

        .lakum-hero {
            position: relative;
            width: 100%;
            height: 85vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1a1a;
            contain: layout style paint
        }

        .lakum-hero__image-wrapper {
            position: absolute;
            inset: 0;
            z-index: 1;
            overflow: hidden
        }

        .lakum-hero__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            will-change: transform;
            transform: translateZ(0)
        }

        .lakum-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.65) 100%);
            z-index: 2
        }

        .lakum-hero__content {
            position: relative;
            z-index: 3;
            text-align: center;
            color: #fff;
            max-width: 1400px;
            width: 90%;
            padding: 0 20px
        }

        .lakum-hero__title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 300;
            line-height: 1.2;
            margin: 0 0 20px 0;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3)
        }

        .lakum-hero__subtitle {
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            font-weight: 300;
            line-height: 1.6;
            color: #fff;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3)
        }

        @media(max-width:768px) {
            .lakum-hero {
                height: 60vh;
                min-height: 450px
            }
        }

        @media(max-width:480px) {
            .lakum-hero {
                height: 50vh;
                min-height: 400px
            }
        }
    </style>

    <!-- Preload Hero Image (Critical for LCP) -->
    <link rel="preload" as="image" href="heroImage/img-3.webp" fetchpriority="high">

<!-- DNS Prefetch for external resources -->
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

<!-- Preconnect to external domains -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Preload critical assets -->
    <link rel="preload" href="critical-inline.css" as="style">
    <link rel="preload" href="global-styles.css" as="style">
    <link rel="preload" href="lakum-components.css" as="style">
    

    <!-- Preload critical fonts -->
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

    <!-- Global Stylesheets (Centralized) -->
    <?php include('includes/stylesheets.php'); ?>

    <!-- Page-specific styles -->
    <link rel="stylesheet" href="shop.css">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/shop.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/shop.php?lang=ar" /></head>
<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <script>
    // Intelligent Page Loader - Proper Implementation
    (function() {
        const loader = document.getElementById('pageLoader');
        if (!loader) return;
        
        // Hide loader immediately on page load
        hideLoader();
        
        function showLoader() {
            loader.classList.add('lakum-page-loader--active');
        }
        
        function hideLoader() {
            loader.classList.remove('lakum-page-loader--active');
        }
        
        // Detect if link will open in new tab
        function willOpenInNewTab(event, link) {
            // Check if link has target attribute
            if (link.target && link.target !== '_self') {
                return true;
            }
            
            // Check for modifier keys (Ctrl, Cmd, Shift)
            if (event.ctrlKey || event.metaKey || event.shiftKey) {
                return true;
            }
            
            // Check for middle mouse button
            if (event.button === 1) {
                return true;
            }
            
            return false;
        }
        
        // Check if link is valid for loader
        function shouldShowLoader(event, link) {
            // No href
            if (!link.href) return false;
            
            // JavaScript link
            if (link.href.startsWith('javascript:')) return false;
            
            // External link
            try {
                const linkUrl = new URL(link.href);
                const currentUrl = new URL(window.location.href);
                
                // Different domain
                if (linkUrl.hostname !== currentUrl.hostname) return false;
                
                // Same page (including hash)
                if (linkUrl.pathname === currentUrl.pathname) return false;
                
            } catch (e) {
                return false;
            }
            
            // Will open in new tab
            if (willOpenInNewTab(event, link)) return false;
            
            return true;
        }
        
        // Handle link clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            if (shouldShowLoader(e, link)) {
                showLoader();
            }
        }, true); // Use capture phase
        
        // Handle middle mouse button
        document.addEventListener('auxclick', function(e) {
            // Middle click (button 1) should not show loader
            if (e.button === 1) {
                hideLoader();
            }
        });
        
        // Hide loader when page becomes visible (handles all load scenarios)
        function handlePageLoad() {
            hideLoader();
        }
        
        // Multiple events to ensure loader hides
        window.addEventListener('pageshow', handlePageLoad);
        window.addEventListener('load', handlePageLoad);
        document.addEventListener('DOMContentLoaded', handlePageLoad);
        
        // Handle back/forward navigation
        window.addEventListener('popstate', handlePageLoad);
        
        // Hide if user switches tabs
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                hideLoader();
            }
        });
        
        // Failsafe: hide after 5 seconds
        let loaderTimeout;
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && shouldShowLoader(e, link)) {
                clearTimeout(loaderTimeout);
                loaderTimeout = setTimeout(hideLoader, 5000);
            }
        }, true);
        
        // Clear timeout on page load
        window.addEventListener('pageshow', function() {
            clearTimeout(loaderTimeout);
        });
        
    })();
    </script>

    <!-- Shop Hero Section -->
    <section class="lakum-hero">
        <div class="lakum-hero__image-wrapper">
            <img src="heroImage/img-4.webp" alt="LAKUM Shop" class="lakum-hero__image" loading="eager" fetchpriority="high" decoding="async" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo t('hero_title', 'Discover Lakum Concept Shop'); ?></h1>
            <p class="lakum-hero__subtitle">
                <a href="https://souvenirs.sa/ar/category/oyajz" class="lakum-btn lakum-btn--primary" target="_blank" rel="noopener noreferrer" data-link-type="shop">
                    <?php echo t('hero_button', 'Explore Shop'); ?>
                    <i class="ri-arrow-right-line"></i>
                </a>
            </p>
        </div>
    </section>

    <footer class="lakum-footer">
    <div class="lakum-footer__container">
        <div class="lakum-footer__content">
            <div class="lakum-footer__brand">
                <div class="lakum-footer__logo">
                                            <!-- English: Swapped -->
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" width="105" height="80" decoding="async">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" width="105" height="80" decoding="async">
                                    </div>
                <p class="lakum-footer__tagline"><?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?></p>
            </div>
            
            <nav class="lakum-footer__nav">
                <h4 class="lakum-footer__nav-title"><?php echo t('footer_navigate', 'Navigate'); ?></h4>
                <ul class="lakum-footer__nav-list">
                    <li><a href="index.php" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                    <li><a href="about.php" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                    <li><a href="spaces.php" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                    <li><a href="exhibitions.php" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                </ul>
            </nav>
            
            <nav class="lakum-footer__nav">
                <h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4>
                <ul class="lakum-footer__nav-list">
                    <li><a href="calendar.php" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                    <li><a href="blog.php" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li>
                    <li><a href="press.php" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li>
                    <li><a href="contact.php" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                </ul>
            </nav>
            
            <div class="lakum-footer__social">
                <h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4>
                <div class="lakum-footer__social-links">
                    <a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram">
                        <i class="ri-instagram-fill"></i>
                    </a>
                    <a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter">
                        <i class="ri-twitter-x-fill"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="lakum-footer__bottom">
            <p class="lakum-footer__copyright"><?php echo t('footer_copyright', '� 2025 - 2027 LAKUM Artspace. All rights reserved.'); ?></p>
            <div class="lakum-footer__legal">
                <a href="terms.php" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                <span class="lakum-footer__legal-divider">|</span>
                <a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
            </div>
        </div>
    </div>
</footer>

<!-- Global Scripts (Centralized) -->
<?php include('includes/scripts.php'); ?>
<script>
// Mobile menu toggle
(function() {
    const toggle = document.querySelector('.app-header__menu-toggle');
    const nav = document.querySelector('.app-nav');
    const header = document.querySelector('.app-header');
    
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            toggle.classList.toggle('app-header__menu-toggle--active');
            nav.classList.toggle('app-nav--active');
            header.classList.toggle('app-header--menu-open');
            document.body.style.overflow = nav.classList.contains('app-nav--active') ? 'hidden' : '';
        });
        
        // Close menu when clicking nav link
        const navLinks = document.querySelectorAll('.app-nav__link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                toggle.classList.remove('app-header__menu-toggle--active');
                nav.classList.remove('app-nav--active');
                header.classList.remove('app-header--menu-open');
                document.body.style.overflow = '';
            });
        });
    }
})();
</script>

<script>
    // Translation strings for JavaScript
    const translations = {
        nav_home: "<?php echo t('home', 'Home'); ?>",
        nav_about: "<?php echo t('about', 'About'); ?>",
        nav_spaces: "<?php echo t('spaces', 'Spaces'); ?>",
        nav_exhibitions: "<?php echo t('exhibitions', 'Exhibitions'); ?>",
        nav_calendar: "<?php echo t('calendar', 'Calendar'); ?>",
        nav_blog: "<?php echo t('blog', 'Blog'); ?>",
        nav_press: "<?php echo t('press', 'Press'); ?>",
        nav_contact: "<?php echo t('contact_us', 'Contact'); ?>",
        nav_shop: "<?php echo t('shop', 'Shop'); ?>",
        footer_tagline: "<?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?>",
        footer_navigate: "<?php echo t('footer_navigate', 'Navigate'); ?>",
        footer_explore: "<?php echo t('footer_explore', 'Explore'); ?>",
        footer_connect: "<?php echo t('footer_connect', 'Connect'); ?>",
        footer_copyright: "<?php echo t('footer_copyright', '� 2026 LAKUM Artspace. All rights reserved.'); ?>",
        footer_terms: "<?php echo t('footer_terms', 'Terms & Conditions'); ?>",
        footer_privacy: "<?php echo t('footer_privacy', 'Privacy Policy'); ?>"
    };

    // Update navbar and footer text when language changes
    function updateNavbarFooterLanguage() {
        // Navbar links
        const navLinks = {
            'home': translations.nav_home || 'Home',
            'about': translations.nav_about || 'About',
            'spaces': translations.nav_spaces || 'Spaces',
            'exhibitions': translations.nav_exhibitions || 'Exhibitions',
            'calendar': translations.nav_calendar || 'Calendar',
            'blog': translations.nav_blog || 'Blog',
            'press': translations.nav_press || 'Press',
            'contact_us': translations.nav_contact || 'Contact',
            'shop': translations.nav_shop || 'Shop'
        };

        // Update navbar
        const navItems = document.querySelectorAll('.app-nav__link');
        navItems.forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'index.php') link.textContent = navLinks.home;
            else if (href === 'about.php') link.textContent = navLinks.about;
            else if (href === 'spaces.php') link.textContent = navLinks.spaces;
            else if (href === 'exhibitions.php') link.textContent = navLinks.exhibitions;
            else if (href === 'calendar.php') link.textContent = navLinks.calendar;
            else if (href === 'blog.php') link.textContent = navLinks.blog;
            else if (href === 'press.php') link.textContent = navLinks.press;
            else if (href === 'contact.php') link.textContent = navLinks.contact_us;
            else if (href === 'shop.php') link.textContent = navLinks.shop;
        });

        // Update footer tagline
        const footerTagline = document.querySelector('.lakum-footer__tagline');
        if (footerTagline) {
            footerTagline.textContent = translations.footer_tagline || 'Where Encounters Shape Culture';
        }

        // Update footer navigation titles
        const footerNavTitles = document.querySelectorAll('.lakum-footer__nav-title');
        if (footerNavTitles.length >= 3) {
            footerNavTitles[0].textContent = translations.footer_navigate || 'Navigate';
            footerNavTitles[1].textContent = translations.footer_explore || 'Explore';
            footerNavTitles[2].textContent = translations.footer_connect || 'Connect';
        }

        // Update footer links
        const footerLinks = document.querySelectorAll('.lakum-footer__link');
        footerLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'index.php') link.textContent = navLinks.home;
            else if (href === 'about.php') link.textContent = navLinks.about;
            else if (href === 'spaces.php') link.textContent = navLinks.spaces;
            else if (href === 'exhibitions.php') link.textContent = navLinks.exhibitions;
            else if (href === 'calendar.php') link.textContent = navLinks.calendar;
            else if (href === 'blog.php') link.textContent = navLinks.blog;
            else if (href === 'press.php') link.textContent = navLinks.press;
            else if (href === 'contact.php') link.textContent = navLinks.contact_us;
        });

        // Update footer bottom
        const footerCopyright = document.querySelector('.lakum-footer__copyright');
        if (footerCopyright) {
            footerCopyright.textContent = translations.footer_copyright || '� 2026 LAKUM Artspace. All rights reserved.';
        }

        const footerTermsLink = document.querySelector('.lakum-footer__legal-link:first-child');
        if (footerTermsLink) {
            footerTermsLink.textContent = translations.footer_terms || 'Terms & Conditions';
        }

        const footerPrivacyLink = document.querySelector('.lakum-footer__legal-link:last-child');
        if (footerPrivacyLink) {
            footerPrivacyLink.textContent = translations.footer_privacy || 'Privacy Policy';
        }
    }

    // Listen for language changes
    document.addEventListener('lakum-language-changed', (e) => {
        const lang = e.detail?.lang || document.documentElement.lang;
        // Reload translations for the new language
        fetch(`api/get-translations.php?lang=${lang}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.translations) {
                    // Update translations object
                    Object.assign(translations, data.translations);
                    // Update navbar and footer
                    updateNavbarFooterLanguage();
                }
            })
            .catch(err => console.log('Language update skipped'));
    });

    // Call on page load
    updateNavbarFooterLanguage();
</script>

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

<script src="assest/navbar-mobile-toggle.js" defer></script>
    
    </body>
</html>
















