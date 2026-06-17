<?php
require_once 'lang/loader.php';
if (file_exists('api/image-helper.php')) { require_once 'api/image-helper.php'; }
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('press_page_title', 'Press & Media | LAKUM Artspace Coverage'); ?></title>
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
    <link rel="stylesheet" href="press.css?v=2.1.0">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>


    <meta name="title" content="Press &amp; Media | LAKUM Artspace Coverage">
    <meta name="description" content="LAKUM Artspace press coverage, media mentions, and news. Download press kit and view our latest media appearances and cultural impact in Riyadh.">
    <meta name="keywords" content="art gallery Riyadh, cultural events Riyadh, art exhibitions Saudi Arabia, event space rental Riyadh, contemporary art gallery, cultural hub Riyadh, art workshops Riyadh, creative space Riyadh">
    <meta name="author" content="LAKUM Artspace">
    <meta name="language" content="<?php echo isArabic() ? "Arabic" : "English"; ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://lakumartspace.infinityfree.me/press.php">

    <!-- Alternate Language -->
    <link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/press.php?lang=en" />
    <link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/press.php?lang=ar" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://lakumartspace.infinityfree.me/press">
    <meta property="og:title" content="Press &amp; Media | LAKUM Artspace Coverage">
    <meta property="og:description" content="LAKUM Artspace press coverage, media mentions, and news. Download press kit and view our latest media appearances and cultural impact in Riyadh.">
    <meta property="og:image" content="https://lakumartspace.infinityfree.me/assest/img-4.webp">
    <meta property="og:site_name" content="LAKUM Artspace">
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="ar_SA">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://lakumartspace.infinityfree.me/press">
    <meta name="twitter:title" content="Press &amp; Media | LAKUM Artspace Coverage">
    <meta name="twitter:description" content="LAKUM Artspace press coverage, media mentions, and news. Download press kit and view our latest media appearances and cultural impact in Riyadh.">
    <meta name="twitter:image" content="https://lakumartspace.infinityfree.me/assest/img-4.webp">

    <!-- Additional SEO -->
    <meta name="theme-color" content="#1a1a1a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">

    <!-- Structured Data - Organization -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ArtGallery",
            "name": "LAKUM Artspace",
            "alternateName": "لكم آرت سبيس",
            "url": "https://lakumartspace.infinityfree.me",
            "logo": "https://lakumartspace.infinityfree.me/assest/favicon.png",
            "description": "A living space for art, connection, and cultural exchange in the heart of Riyadh",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Riyadh",
                "addressCountry": "SA"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "24.7136",
                "longitude": "46.6753"
            },
            "sameAs": [
                "https://www.instagram.com/lakum.artspace/",
                "https://twitter.com/lakumartspace"
            ]
        }
    </script>

</head>

<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <script>
        // Set current language from PHP (respects URL parameter ?lang=en or ?lang=ar)
        window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
        console.log('Initial language set to:', window.LAKUM_LANG);
        
        // Listen for language changes and reload page to apply RTL/LTR to navigation
        window.addEventListener('storage', (e) => {
            if (e.key === 'lakum_language' && e.newValue) {
                console.log('Language changed to:', e.newValue);
                // Reload page to apply language changes to navigation and press grid
                window.location.href = window.location.pathname + '?lang=' + e.newValue;
            }
        });
    </script>

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
    <section class="lakum-press-hero">
        <div class="lakum-container">
            <h1 class="lakum-press-hero__title"><?php echo t('press_heading', 'Press & Media'); ?></h1>
            <p class="lakum-press-hero__subtitle"><?php echo t('press_subtitle', 'Read about LAKUM Artspace in the news'); ?></p>
        </div>
    </section>

    <!-- Press Releases Grid -->
    <section class="lakum-press-content">
        <div class="lakum-container">
            <div class="lakum-press-grid" id="pressGrid">
                <!-- Press cards will be loaded dynamically from API -->
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">Loading press releases...</div>
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

    <!-- Load Press Data from API -->
    <script>
        async function loadPressReleases() {
            try {
                // Use language from PHP (respects URL parameter ?lang=en or ?lang=ar)
                // Falls back to LanguageManager if window.LAKUM_LANG not set
                const lang = window.LAKUM_LANG || (typeof LanguageManager !== 'undefined' ? LanguageManager.getLanguage() : 'en') || 'en';
                
                console.log('Loading press from API with lang:', lang);
                const response = await fetch(`/api/get_press.php?lang=${lang}`);
                const data = await response.json();
                
                console.log('Press API response:', data);
                console.log('Language:', lang);
                console.log('First item:', data.data ? data.data[0] : 'No data');
                
                if (data.success && data.data && data.data.length > 0) {
                    displayPressReleases(data.data, lang);
                } else {
                    console.warn('No press data available:', data);
                    displayNoPressMessage();
                }
            } catch (error) {
                console.error('Error loading press releases:', error);
                displayNoPressMessage();
            }
        }

        function displayPressReleases(pressItems, lang) {
            const pressGrid = document.getElementById('pressGrid');
            pressGrid.innerHTML = '';

            pressItems.forEach(item => {
                const pressCard = document.createElement('a');
                
                // Use clean URL for internal press detail pages
                // CRITICAL: Only use window.LAKUM_LANG (set from PHP)
                // Do NOT use localStorage as it contains the previous page's language
                const currentLang = lang || window.LAKUM_LANG || 'en';
                const slug = item.slug || item.id;
                pressCard.href = `press/${slug}?lang=${currentLang}`;
                
                pressCard.className = 'lakum-press-card';
                pressCard.setAttribute('data-press-id', item.id);

                const formattedDate = new Date(item.press_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                // Get bilingual content from database - CRITICAL: Use title_ar/title_en directly
                // The API returns these fields based on language parameter
                const title = item.title || 'Untitled';
                const excerpt = item.excerpt || '';
                const source = item.source || 'LAKUM Press';
                
                // Add RTL direction for Arabic
                const contentDir = currentLang === 'ar' ? 'dir="rtl"' : '';

                console.log('Press item:', {
                    id: item.id,
                    title: title,
                    excerpt: excerpt,
                    lang: currentLang
                });

                pressCard.innerHTML = `
                    <div class="lakum-press-card__image">
                        <img src="${item.cover_image || 'assest/img-4.webp'}" 
                             alt="${source}" 
                             loading="eager" 
                             decoding="async"
                             onerror="this.onerror=null;this.src='assest/img-4.webp';">
                    </div>
                    <div class="lakum-press-card__content" ${contentDir}>
                        <span class="lakum-press-card__source">${source}</span>
                        <h3 class="lakum-press-card__title">${title}</h3>
                        <p class="lakum-press-card__description">${excerpt || ''}</p>
                        <div class="lakum-press-card__footer">
                            <span class="lakum-press-card__date">${formattedDate}</span>
                            <span class="lakum-press-card__link">
                                ${currentLang === 'ar' ? 'اقرأ المقال' : 'Read Article'} <i class="ri-external-link-line"></i>
                            </span>
                        </div>
                    </div>
                `;

                pressGrid.appendChild(pressCard);
            });
        }

        function displayNoPressMessage() {
            const pressGrid = document.getElementById('pressGrid');
            pressGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">No press releases available</div>';
        }

        // Load press releases when page loads
        function initPressPage() {
            console.log('Initializing press page...');
            loadPressReleases();
        }
        
        // Load immediately when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPressPage);
        } else {
            // DOM already loaded
            setTimeout(initPressPage, 100);
        }

        // Listen for language changes and reload press data
        window.addEventListener('storage', (e) => {
            if (e.key === 'lakum_language' && e.newValue) {
                console.log('Language changed to:', e.newValue);
                window.LAKUM_LANG = e.newValue;
                loadPressReleases();
            }
        });
    </script>

    <script>
        // Wait for translationHelper to be available
        function initializeTranslation() {
            if (typeof translationHelper === 'undefined') {
                // translationHelper not loaded yet, try again
                setTimeout(initializeTranslation, 100);
                return;
            }

            // Translate press releases if needed
            if (translationHelper.needsTranslation()) {
                const pressReleases = [{
                "id": "1",
                "title": "Explore an interactive workshop By The Culture Mocktail at Lakum",
                "publication": "Time Out Riyadh",
                "excerpt": "Time Out Riyadh features an interactive workshop hosted by The Culture Mocktail at LAKUM Artspace.",
                "url": "https:\/\/www.timeoutriyadh.com\/things-to-do\/things-to-do-in-riyadh",
                "publish_date": "2025-12-16",
                "logo_path": "uploads\/press\/press_1_1765953905.jpg",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-16 22:45:05"
            }, {
                "id": "2",
                "title": "TikTok Creator Hub Awards Women Entrepreneurs in Riyadh (#HerAmbitions)",
                "publication": "Zawya",
                "excerpt": "Zawya covers the TikTok MENA Creator Hub awards celebrating women entrepreneurs in Riyadh at LAKUM Artspace.",
                "url": "https:\/\/www.zawya.com\/en\/press-release\/events-and-conferences\/tiktok-mena-creator-hub-awards-women-entrepreneurs-in-riyadh-ilt72tyv",
                "publish_date": "2024-02-28",
                "logo_path": "uploads\/press\/press_2_1765953905.jpg",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-21 04:45:24"
            }, {
                "id": "3",
                "title": "Pre-Ramadan pop-up show in Riyadh's Lakum Artspace",
                "publication": "KSA Directory",
                "excerpt": "KSA Directory highlights a special pre-Ramadan pop-up exhibition at LAKUM Artspace.",
                "url": "https:\/\/www.ksa.directory\/pre-ramadan-pop-up-show-in-riyadh-s-lakum-artspace-consists-of-curated-pop-up-shops\/396\/n",
                "publish_date": "2023-03-21",
                "logo_path": "uploads\/press\/press_3_1765953905.svg",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-21 04:45:53"
            }, {
                "id": "4",
                "title": "Snap shows power of AR in transforming fashion, beauty in Saudi Arabia",
                "publication": "Arab News",
                "excerpt": "Arab News reports on Snap's augmented reality showcase at LAKUM Artspace, demonstrating AR's impact on fashion and beauty.",
                "url": "https:\/\/www.arabnews.com\/node\/2177376\/saudi-arabia",
                "publish_date": "2022-10-08",
                "logo_path": "uploads\/press\/press_5_1765953905.png",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-21 04:46:32"
            }, {
                "id": "5",
                "title": "Photographer Faisal bin Zarah's exhibition is a love letter to the Kingdom",
                "publication": "Arab News",
                "excerpt": "Arab News features photographer Faisal bin Zarah's exhibition at LAKUM Artspace, celebrating Saudi Arabia through photography.",
                "url": "https:\/\/www.arabnews.com\/node\/2174501\/saudi-arabia",
                "publish_date": "2022-09-15",
                "logo_path": "uploads\/press\/press_5_1765953905.png",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-21 04:51:07"
            }, {
                "id": "6",
                "title": "Artworks of 20 Saudi women on display to mark International Women's Day",
                "publication": "Saudi Gazette",
                "excerpt": "Saudi Gazette covers an exhibition at LAKUM Artspace featuring artworks by 20 Saudi women artists for International Women's Day.",
                "url": "https:\/\/www.saudigazette.com.sa\/article\/617910\/SAUDI-ARABIA\/Artworks-of-20-Saudi-women-are-on-display-in-Riyadh-to-mark-International-Womens-Day",
                "publish_date": "2022-03-08",
                "logo_path": "uploads\/press\/press_6_1765953905.svg",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-16 22:45:05"
            }, {
                "id": "7",
                "title": "Ahmed Mater to inaugurate Lakum Artspace with Prognosis: 1979-2019",
                "publication": "GDN Life",
                "excerpt": "GDN Life announces the inauguration of LAKUM Artspace with renowned artist Ahmed Mater's exhibition \"Prognosis: 1979-2019\".",
                "url": "https:\/\/www.gdnlife.com\/Home\/ArticleDetail?ArticleId=43966&category=6",
                "publish_date": "2021-11-24",
                "logo_path": "uploads\/press\/press_7_1765953905.png",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-16 22:45:05"
            }, {
                "id": "8",
                "title": "Lakum Artspace to open in December",
                "publication": "Time Out Riyadh",
                "excerpt": "Time Out Riyadh announces the upcoming opening of LAKUM Artspace in December 2021.",
                "url": "https:\/\/www.timeoutriyadh.com\/art\/lakum-art-space-to-open-in-december",
                "publish_date": "2021-10-28",
                "logo_path": "uploads\/press\/press_8_1765953905.jpg",
                "created_at": "2025-12-16 22:45:05",
                "updated_at": "2025-12-16 22:45:05"
            }];
            const pressCards = document.querySelectorAll('.lakum-press-card');

            translationHelper.translateArrayProgressive(
                pressReleases, ['title', 'publication', 'excerpt'],
                (translated, index) => {
                    const card = pressCards[index];
                    if (card) {
                        const titleEl = card.querySelector('.lakum-press-card__title');
                        const sourceEl = card.querySelector('.lakum-press-card__source');
                        const descEl = card.querySelector('.lakum-press-card__description');

                        if (titleEl) titleEl.textContent = translated.title;
                        if (sourceEl && translated.publication) sourceEl.textContent = translated.publication;
                        if (descEl && translated.excerpt) {
                            const truncated = translated.excerpt.length > 150 ?
                                translated.excerpt.substring(0, 150) + '...' :
                                translated.excerpt;
                            descEl.textContent = truncated;
                        }
                    }
                },
                'ar'
            );
            }
        }
        
        // Initialize translation when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeTranslation);
        } else {
            initializeTranslation();
        }
    </script>

    <script>
        // Listen for dynamic content loaded event and render press releases based on language
        document.addEventListener('lakum-content-loaded', (e) => {
            if (e.detail.contentType === 'press') {
                const pressReleases = e.detail.content;
                const pressGrid = document.getElementById('pressGrid');
                
                if (!pressGrid || !pressReleases || pressReleases.length === 0) return;
                
                // Clear existing content
                pressGrid.innerHTML = '';
                
                // Render press releases
                pressReleases.forEach((item) => {
                    const pressCard = document.createElement('a');
                    
                    // Use clean URL for internal press detail pages
                    // CRITICAL: Only use window.LAKUM_LANG (set from PHP)
                    // Do NOT use localStorage as it contains the previous page's language
                    const lang = window.LAKUM_LANG || 'en';
                    const slug = item.slug || item.id;
                    pressCard.href = `press/${slug}?lang=${lang}`;
                    
                    pressCard.className = 'lakum-press-card';
                    pressCard.setAttribute('data-press-id', item.id);
                    
                    const formattedDate = new Date(item.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    
                    pressCard.innerHTML = `
                        <div class="lakum-press-card__image">
                            <img src="${item.cover_image || 'assest/img-4.png'}" alt="${item.source || 'Press Release'}" loading="eager" decoding="async">
                        </div>
                        <div class="lakum-press-card__content">
                            <span class="lakum-press-card__source">${item.source || 'LAKUM Press'}</span>
                            <h3 class="lakum-press-card__title">${item.title}</h3>
                            <p class="lakum-press-card__description">${item.content ? item.content.substring(0, 150) + '...' : ''}</p>
                            <div class="lakum-press-card__footer">
                                <span class="lakum-press-card__date">${formattedDate}</span>
                                <span class="lakum-press-card__link">
                                    Read Article <i class="ri-external-link-line"></i>
                                </span>
                            </div>
                        </div>
                    `;
                    
                    pressGrid.appendChild(pressCard);
                });
            }
        });
    </script>

    <script src="assest/popup-notification.js?v=5.0.0" defer></script>

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
        fetch(`/api/get-translations.php?lang=${lang}`)
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

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
</body>

</html>
























