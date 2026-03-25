<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - LAKUM Artspace</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

    <!-- Preload LCP image (hero) - Mobile-first with responsive variants -->
    <link rel="preload" as="image" 
          href="heroImage/img-4.webp"
          imagesrcset="heroImage/img-4.webp 1200w"
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
    <link rel="stylesheet" href="privacy.css">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>

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

    <!-- Hero Section -->
    <section class="lakum-calendar-hero">
        <div class="lakum-calendar-hero__content">
            <h1 class="lakum-calendar-hero__title" id="legal-page-title">Privacy Policy</h1>
         
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
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright_prefix', '� 2025 - '); ?><span id="year"></span><?php echo t('footer_copyright_suffix', ' LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms.php<?php echo isset($_GET['lang']) ? '?lang=' . htmlspecialchars($_GET['lang']) : ''; ?>" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php<?php echo isset($_GET['lang']) ? '?lang=' . htmlspecialchars($_GET['lang']) : ''; ?>" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
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
        // ===== PRIVACY PAGE: LANGUAGE SWITCHER =====
        // Single clean implementation. No duplicates.

        // 1. Read lang from URL (?lang=en or ?lang=ar). Default: 'en'
        function getCurrentLang() {
            var lang = new URLSearchParams(window.location.search).get('lang');
            return (lang === 'ar' || lang === 'en') ? lang : 'en';
        }

        // 2. Add ?lang= to every internal <a> tag on the page
        function updateLinksWithLang(lang) {
            document.querySelectorAll('a[href]').forEach(function(link) {
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
                var res = await fetch('api/get_legal_page.php?page_key=privacy&lang=' + lang);
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

            var contentDiv = document.getElementById('privacy-content');
            if (contentDiv) contentDiv.innerHTML = content.content || '';

            var titleEl = document.getElementById('legal-page-title');
            if (titleEl) titleEl.textContent = content.title || 'Privacy Policy';

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



















