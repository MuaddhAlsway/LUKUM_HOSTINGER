<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - LAKUM Artspace</title>
    <link rel="icon" href="assest/favicon.png" type="image/png">

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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f6f6eb;
            color: #1a1a1a;
            overflow-x: hidden;
            line-height: 1.6
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
    <link rel="preload" as="image" href="assest/img-4.webp" fetchpriority="high">

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

    <!-- Core Styles - Critical CSS loaded synchronously -->
    <!-- Global Stylesheets (Centralized) -->
    <?php include('includes/stylesheets.php'); ?>

    <!-- Image Optimizer - Critical for performance -->
    <!-- Scripts - Defer non-critical JavaScript -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/language-link-preserver.js?v=1.0.0" defer></script>
    <script src="assest/fun-interactions.js" defer></script>
    <script>
        // Set language for JavaScript - Read from URL parameter or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        const storedLang = localStorage.getItem('lakum_language');
        window.LAKUM_LANG = urlLang || storedLang || 'en';
        window.LAKUM_DIR = window.LAKUM_LANG === 'ar' ? 'rtl' : 'ltr';
        
        // CRITICAL: Save language to localStorage whenever URL parameter is present
        // This ensures language persists across page navigation
        if (urlLang && ['en', 'ar'].includes(urlLang)) {
            localStorage.setItem('lakum_language', urlLang);
            console.log('Language saved to localStorage:', urlLang);
        }

        // Performance monitoring
        if ('PerformanceObserver' in window) {
            // Monitor Largest Contentful Paint
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                console.log('LCP:', lastEntry.renderTime || lastEntry.loadTime);
            });
            lcpObserver.observe({
                entryTypes: ['largest-contentful-paint']
            });

            // Monitor First Input Delay
            const fidObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach((entry) => {
                    console.log('FID:', entry.processingStart - entry.startTime);
                });
            });
            fidObserver.observe({
                entryTypes: ['first-input']
            });
        }
    </script>
    <style>
        .lakum-legal-hero {
            padding: clamp(120px, 15vw, 180px) 0 clamp(40px, 5vw, 60px) 0;
            background: #f6f6eb;
            text-align: center;
        }

        .lakum-legal-hero__title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 300;
            color: #1a1a1a;
            margin: 0;
        }

        .lakum-legal-content {
            padding: clamp(60px, 8vw, 100px) 0;
            background: #ffffff;
        }

        .lakum-legal-content__inner {
            max-width: 800px;
            margin: 0 auto;
        }

        .lakum-legal-content h2 {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            font-weight: 400;
            color: #1a1a1a;
            margin: clamp(32px, 4vw, 48px) 0 clamp(16px, 2vw, 20px) 0;
        }

        .lakum-legal-content h2:first-child {
            margin-top: 0;
        }

        .lakum-legal-content p {
            font-size: clamp(0.95rem, 1.3vw, 1.05rem);
            font-weight: 300;
            line-height: 1.8;
            color: #525252;
            margin: 0 0 16px 0;
        }

        .lakum-legal-content ul {
            margin: 0 0 16px 0;
            padding-left: 24px;
        }

        .lakum-legal-content li {
            font-size: clamp(0.95rem, 1.3vw, 1.05rem);
            font-weight: 300;
            line-height: 1.8;
            color: #525252;
            margin-bottom: 8px;
        }

        .lakum-legal-content__date {
            font-size: 0.9rem;
            color: #8a8a8a;
            margin-bottom: 32px;
        }
    </style>
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/terms.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/terms.php?lang=ar" />
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>
    
    <!-- CRITICAL FIX: Ensure dropdown works on this page -->
    <style>
        .lakum-nav { overflow: visible !important; }
        .lakum-nav__list { overflow: visible !important; }
        .lakum-nav__item--dropdown { overflow: visible !important; position: relative !important; }
        .lakum-nav__item--dropdown.active > .lakum-nav__dropdown,
        .lakum-nav__item--dropdown.active .lakum-nav__dropdown {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }
    </style>

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

    <!-- Hero Section -->
    <section class="lakum-calendar-hero">
        <div class="lakum-calendar-hero__content">
            <h1 class="lakum-calendar-hero__title" id="legal-page-title">Terms & Conditions</h1>
            
        </div>
    </section>

    <section class="lakum-legal-content">
        <div class="lakum-container">
            <div class="lakum-legal-content__inner">
                <p class="lakum-legal-content__date">Last Updated: <span id="legal-page-date">March 1, 2026</span></p>

                <div id="terms-content" data-translate="content">Lakum Artspace Terms of Use
By accessing and using the LAKUM Artspace website and services, you accept and agree to be bound by these Terms and Conditions.

1. Standard Operating Hours
I acknowledge that Lakum Artspace’s operating hours are 10:00 AM – 10:00 PM, and that additional hours will incur extra charges.

2. Liability for Damages
I am responsible for any damage to the venue, equipment, furniture, or accessories during the entire rental period, including setup and dismantling.

3. Official Damage Reporting
Any damage will be documented and reported by a Lakum Artspace representative during the event.

4. Surface Material Restrictions
I will not use or apply stickers, vinyl, or adhesive materials on any internal or external surfaces without prior approval from Lakum Artspace.

5. Event Promotion Policy
Lakum Artspace is not obligated to promote or advertise external events on its social media or marketing channels.

6. Branding and Logo Usage
Use of Lakum Artspace’s logo or branding elements is strictly prohibited unless formally approved.

7. Private Area Access
Access to private areas—including offices, the director’s office, and storage rooms—is not permitted.

8. Public Access Areas
The mezzanine floor, including the shop and café, will remain open to the public during regular hours unless reserved as private for the event.

9. On-Site Staff Presence
Lakum Artspace staff—male and female—will be present throughout the event.

10. Exhibition Approval Process
Personal or group exhibitions by artists will not be considered unless formally submitted and approved by the Lakum Artspace jury.


Compliance with these terms ensures the preservation of Lakum Artspace’s professional standards and physical integrity, establishing a clear framework for operational hours, property liability, and brand usage to which all parties are strictly bound throughout the duration of the engagement.</div>
            </div>
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
                        <li><a href="index.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                        <li><a href="about.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                        <li><a href="spaces.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                        <li><a href="exhibitions.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    </ul>
                </nav>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="calendar.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                        <li><a href="blog.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li>
                        <li><a href="press.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li>
                        <li><a href="contact.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
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
                        <a href="https://www.snapchat.com/add/lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Snapchat">
                        <i class="ri-snapchat-line"></i>
                    </a>
                        <a href="https://www.tiktok.com/@lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="TikTok">
                        <i class="ri-tiktok-fill"></i>
                    </a>
                    </div>
                </div>
            </div>

            <div class="lakum-footer__bottom">
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright_prefix', '� 2025 - '); ?><span id="year"></span><?php echo t('footer_copyright_suffix', ' LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Expandable Floating Contact Button -->
    <div class="fab-button" id="fabButton"><button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false"><i class="ri-mail-line fab-button__icon"></i><i class="ri-close-line fab-button__close"></i></button><div class="fab-button__menu" id="fabMenu" role="menu"><a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div>
    <script src="assest/navbar-mobile-toggle.js" defer></script>

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>

    <script>
        // ===== TERMS PAGE: LANGUAGE SWITCHER =====
        // Single clean implementation. No duplicates.

        // 1. Read lang from URL (?lang=en or ?lang=ar). Default: 'en'
        function getCurrentLang() {
            var lang = new URLSearchParams(window.location.search).get('lang');
            return (lang === 'ar' || lang === 'en') ? lang : 'en';
        }

        // 2. Add ?lang= to every internal <a> tag on the page (skip language switcher)
        function updateLinksWithLang(lang) {
            document.querySelectorAll('a[href]').forEach(function(link) {
                // Skip language switcher buttons — they already have correct hrefs from PHP
                if (link.closest('.lakum-language-switcher')) return;

                var href = link.getAttribute('href');
                if (!href || href.startsWith('http') || href.startsWith('#') ||
                    href.startsWith('javascript') || href.startsWith('mailto') ||
                    href.startsWith('tel') || href.indexOf('wa.me') !== -1) return;
                try {
                    var url = new URL(href, window.location.origin);
                    url.searchParams.set('lang', lang);
                    link.setAttribute('href', url.pathname + '?' + url.searchParams.toString());
                } catch(e) {}
            });
        }

        // 3. Fetch page content from API
        async function fetchContent(lang) {
            try {
                var res = await fetch('api/get_legal_page.php?page_key=terms&lang=' + lang);
                var data = await res.json();
                return data.success ? data.data : null;
            } catch(e) {
                console.error('API fetch failed:', e);
                return null;
            }
        }

        // 4. Render fetched content into the page
        function renderContent(content, lang) {
            if (!content) return;

            var contentDiv = document.getElementById('terms-content');
            if (contentDiv) contentDiv.innerHTML = content.content || '';

            var titleEl = document.getElementById('legal-page-title');
            if (titleEl) titleEl.textContent = content.title || 'Terms & Conditions';

            var dateEl = document.getElementById('legal-page-date');
            if (dateEl && content.last_updated) {
                dateEl.textContent = new Date(content.last_updated).toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
            }

            document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.lang = lang;
        }

        // 5. Update footer text using footer.json translations
        async function updateFooter(lang) {
            try {
                var res = await fetch('lang/' + lang + '/footer.json');
                if (!res.ok) return;
                var t = await res.json();

                var tagline = document.querySelector('.lakum-footer__tagline');
                if (tagline) tagline.textContent = t.footer_tagline || t.tagline || '';

                var navTitles = document.querySelectorAll('.lakum-footer__nav-title');
                if (navTitles[0]) navTitles[0].textContent = t.footer_navigate || t.navigate || '';
                if (navTitles[1]) navTitles[1].textContent = t.footer_explore || t.explore || '';
                if (navTitles[2]) navTitles[2].textContent = t.footer_connect || t.connect || '';

                document.querySelectorAll('.lakum-footer__legal-link').forEach(function(link) {
                    var href = link.getAttribute('href') || '';
                    if (href.indexOf('terms.php') !== -1) {
                        link.textContent = t.footer_terms || t.terms || '';
                    } else if (href.indexOf('privacy.php') !== -1) {
                        link.textContent = t.footer_privacy || t.privacy || '';
                    }
                });
            } catch(e) {
                console.error('Footer translation fetch failed:', e);
            }
        }

        // 6. Main init: runs on page load
        async function initPage() {
            var lang = getCurrentLang();

            // Set direction immediately
            document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.lang = lang;

            // Update year
            var yearEl = document.getElementById('year');
            if (yearEl) yearEl.textContent = new Date().getFullYear();

            // Fetch and render API content
            var content = await fetchContent(lang);
            renderContent(content, lang);

            // Update all links to include ?lang=
            updateLinksWithLang(lang);

            // Update footer translations
            await updateFooter(lang);
        }

        document.addEventListener('DOMContentLoaded', initPage);
    </script>



</body>

</html>














