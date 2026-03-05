<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - LAKUM Artspace</title>
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
    <link rel="stylesheet" href="critical-inline.css">
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    <link rel="stylesheet" href="assest/mobile-menu.css">
    <link rel="stylesheet" href="assest/fab-button.css">
    <link rel="stylesheet" href="assest/lakum-header.css">
    <link rel="stylesheet" href="Home.min.css">

    <!-- RTL Styles -->
    <link rel="stylesheet" href="rtl.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="fonts/greta-arabic.css">

    <!-- Icons - Defer non-critical icon loading -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>

    <!-- Image Optimizer - Critical for performance -->
    <!-- Scripts - Defer non-critical JavaScript -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/language-link-preserver.js?v=1.0.0" defer></script>
    <script src="assest/fun-interactions.js" defer></script>
    <script>
        // Set language for JavaScript - Inline critical config
        window.LAKUM_LANG = 'en';
        window.LAKUM_DIR = 'ltr';

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
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/privacy.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/privacy.php?lang=ar" />
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">
<div class="lakum-page-loader" id="pageLoader"><div class="lakum-page-loader__content"><div class="lakum-page-loader__spinner"></div></div></div>
<header class="lakum-header" role="banner"><div class="lakum-header__container"><div class="lakum-header__logo"><a href="./" class="lakum-logo"><img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left"><img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right"></a></div><nav class="lakum-nav"><ul class="lakum-nav__list"><li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link"><?php echo t('home', 'Home'); ?></a></li><li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link"><?php echo t('about', 'About'); ?></a></li><li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces', 'Spaces'); ?></a></li><li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li><li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar', 'Calendar'); ?></a></li><li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog', 'Blog'); ?></a></li><li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press', 'Press'); ?></a></li><li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us', 'Contact'); ?></a></li><li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop', 'Shop'); ?></a></li></ul></nav><div class="lakum-language-switcher"><a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>"><i class="ri-global-line"></i><span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span></a></div><button class="lakum-header__mobile-toggle" aria-label="Toggle menu"><span class="lakum-header__mobile-icon" aria-hidden="true"></span></button></div></header>

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

    <section class="lakum-legal-hero">
        <div class="lakum-container">
            <h1 class="lakum-legal-hero__title" id="legal-page-title">Privacy Policy</h1>
        </div>
    </section>

    <section class="lakum-legal-content">
        <div class="lakum-container">
            <div class="lakum-legal-content__inner">
                <p class="lakum-legal-content__date">Last Updated: <span id="legal-page-date">March 1, 2026</span></p>

                <div id="privacy-content" data-translate="content">
                    <h1>Privacy Policy & Data Protection Statement</h1>

                    <p>Lakum Artspace is committed to safeguarding the personal information of our clients, visitors, and artists. This policy outlines how we collect, use, and protect your data in accordance with the <span class="highlight">Saudi Arabian Personal Data Protection Law (PDPL)</span>                        and international best practices.</p>

                    <hr>

                    <h2>1. Information Collection</h2>
                    <p>We collect personal information necessary to provide our services, including but not limited to:</p>
                    <ul>
                        <li><strong>Contact Details:</strong> Name, email address, phone number, and physical address.</li>
                        <li><strong>Transaction Data:</strong> Securely processed payment information and purchase history.</li>
                        <li><strong>Event Records:</strong> Venue bookings, exhibition submissions, and visitor logs.</li>
                        <li><strong>Visual Data:</strong> Digital images and video footage captured via on-site surveillance.</li>
                    </ul>

                    <h2>2. CCTV and Video Surveillance</h2>
                    <p>For the safety of our visitors, staff, and the protection of high-value artworks, Lakum Artspace operates a <strong>Closed-Circuit Television (CCTV)</strong> system.</p>
                    <ul>
                        <li><strong>Purpose:</strong> Monitoring is conducted solely for security, crime prevention, and public safety.</li>
                        <li><strong>Placement:</strong> Cameras are positioned in public areas, entrances, and galleries. No cameras are placed in private areas such as restrooms.</li>
                        <li><strong>Storage:</strong> Footage is stored securely and is automatically overwritten after a set period, unless required for legal investigations.</li>
                    </ul>

                    <h2>3. Purpose of Data Processing</h2>
                    <p>Your data is used exclusively to:</p>
                    <ul>
                        <li>Manage venue rentals and coordinate event logistics.</li>
                        <li>Process artwork sales and exhibition applications.</li>
                        <li>Ensure the physical security of the premises via CCTV.</li>
                        <li>Comply with regulatory requirements within the Kingdom of Saudi Arabia.</li>
                    </ul>

                    <h2>4. Data Retention and Security</h2>
                    <p>Lakum Artspace implements robust technical and organizational measures to prevent unauthorized access, disclosure, or loss of data. Personal information and surveillance footage are retained only for as long as necessary to fulfill
                        the purpose for which it was collected or as required by Saudi law.</p>

                    <h2>5. Third-Party Sharing</h2>
                    <p>We do not sell, trade, or lease your personal data. Information is only shared with:</p>
                    <ul>
                        <li><strong>Service Providers:</strong> Authorized partners (e.g., payment processors) strictly to facilitate our services.</li>
                        <li><strong>Legal Authorities:</strong> Data or footage may be disclosed to Saudi law enforcement agencies if required by law or for safety investigations.</li>
                    </ul>

                    <h2>6. Your Rights</h2>
                    <p>Under the PDPL, you have the right to access, correct, or request the destruction of your personal data. You may also inquire about the specific use of surveillance footage concerning your presence on the premises.</p>

                    <h2>7. Legal Jurisdiction</h2>
                    <p>This Privacy Policy is governed by the laws of the <strong>Kingdom of Saudi Arabia</strong>. Any disputes arising from these practices shall be subject to the exclusive jurisdiction of the Saudi Arabian courts.</p>


                    <br>
                    <hr>

                    <p><b>This Privacy Policy constitutes a binding commitment to data transparency and security, ensuring that all personal information and surveillance data are managed with the highest level of integrity in full alignment with the regulatory framework of the Kingdom of Saudi Arabia.</b></p>


                    <div class="footer">&copy; 2025 Lakum Artspace. All rights reserved.</div>
                </div>
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
                    <h4 class="lakum-footer__nav-title">Navigate</h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="index.php" class="lakum-footer__link">Home</a></li>
                        <li><a href="about.php" class="lakum-footer__link">About</a></li>
                        <li><a href="spaces.php" class="lakum-footer__link">Spaces</a></li>
                        <li><a href="exhibitions.php" class="lakum-footer__link">Exhibitions</a></li>
                    </ul>
                </nav>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title">Explore</h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="calendar.php" class="lakum-footer__link">Calendar</a></li>
                        <li><a href="blog.php" class="lakum-footer__link">Blog</a></li>
                        <li><a href="press.php" class="lakum-footer__link">Press</a></li>
                        <li><a href="contact.php" class="lakum-footer__link">Contact</a></li>
                    </ul>
                </nav>

                <div class="lakum-footer__social">
                    <h4 class="lakum-footer__nav-title">Connect</h4>
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
                    <a href="terms.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Expandable Floating Contact Button -->
    <div class="fab-button" id="fabButton"><button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false"><i class="ri-mail-line fab-button__icon"></i><i class="ri-close-line fab-button__close"></i></button><div class="fab-button__menu" id="fabMenu" role="menu"><a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div><script src="assest/fab-button.js" defer></script>
    <script src="assest/navbar-mobile-toggle.js" defer></script>
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
        // Load legal page content dynamically based on current language
        document.addEventListener('DOMContentLoaded', function() {
            // Get current language
            const urlParams = new URLSearchParams(window.location.search);
            const lang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
            
            // Set page direction based on language
            if (lang === 'ar') {
                document.documentElement.dir = 'rtl';
                document.documentElement.lang = 'ar';
            } else {
                document.documentElement.dir = 'ltr';
                document.documentElement.lang = 'en';
            }
        });
    </script>

    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
    <script src="assest/lakum-header.js" defer></script>

    <script>
        // Load legal page content dynamically based on current language
        let currentLang = 'en';

        document.addEventListener('DOMContentLoaded', function() {
            loadLegalPageContent();
        });

        // Watch for language changes via URL parameter
        window.addEventListener('popstate', function() {
            loadLegalPageContent();
        });

        async function loadLegalPageContent() {
            try {
                // Get current language from URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const lang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
                
                console.log('Loading privacy content for language:', lang);
                
                // Fetch content from API
                const response = await fetch(`api/get_legal_page.php?page_key=privacy&lang=${lang}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    const contentDiv = document.getElementById('privacy-content');
                    const titleDiv = document.getElementById('legal-page-title');
                    const dateDiv = document.getElementById('legal-page-date');
                    
                    if (contentDiv) {
                        // Update content with fetched data
                        contentDiv.innerHTML = `<h2>${data.data.title || 'Privacy Policy'}</h2>${data.data.content || ''}`;
                        console.log('Privacy content loaded successfully for language:', lang);
                    }
                    
                    // Update title
                    if (titleDiv) {
                        titleDiv.textContent = data.data.title || 'Privacy Policy';
                    }
                    
                    // Update date if available
                    if (dateDiv && data.data.last_updated) {
                        const updateDate = new Date(data.data.last_updated);
                        const formattedDate = updateDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        dateDiv.textContent = formattedDate;
                    }
                    
                    // Update page direction based on language
                    if (lang === 'ar') {
                        document.documentElement.dir = 'rtl';
                        document.documentElement.lang = 'ar';
                    } else {
                        document.documentElement.dir = 'ltr';
                        document.documentElement.lang = 'en';
                    }
                } else {
                    console.log('No privacy content found in database, using default content');
                }
            } catch (error) {
                console.error('Error loading privacy content:', error);
                // Keep default content if API fails
            }
        }
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
</body>

</html>

















