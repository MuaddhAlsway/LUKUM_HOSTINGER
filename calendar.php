<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('calendar_page_title', 'Events Calendar | LAKUM Artspace Riyadh'); ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

    <!-- Preload LCP image (calendar hero) - Mobile-first -->
    <link rel="preload" as="image" href="heroImage/img-4.webp" fetchpriority="high">

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

    <!-- Preload Hero Image (Critical for LCP) - Mobile-first with responsive variants -->
    <link rel="preload" as="image" 
          href="heroImage/img-4.webp"
          imagesrcset="heroImage/img-4.webp 1200w"
          imagesizes="(max-width: 768px) 100vw, 650px"
          fetchpriority="high">
    <!-- Preload critical fonts -->
    <link rel="preload" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="assest/fonts/GretaTextArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="assest/fonts/GretaTextArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

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
    

    <!-- Global Stylesheets (Centralized) -->
    <?php include('includes/stylesheets.php'); ?>

    <!-- Image Optimizer - Critical for performance -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/fab-button.js" defer></script>

    <!-- Scripts - Defer non-critical JavaScript -->
    <script src="assest/fun-interactions.js" defer></script>

    <!-- Scripts - Defer non-critical JavaScript -->
    <script>
        // Set language for JavaScript - Read from URL parameter or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        const storedLang = localStorage.getItem('lakum_language');
        
        // Priority: URL parameter > localStorage > default to 'en'
        window.LAKUM_LANG = (urlLang && ['en', 'ar'].includes(urlLang)) ? urlLang : (storedLang && ['en', 'ar'].includes(storedLang) ? storedLang : 'en');
        window.LAKUM_DIR = window.LAKUM_LANG === 'ar' ? 'rtl' : 'ltr';
        
        // CRITICAL: Save language to localStorage whenever URL parameter is present
        // This ensures language persists across page navigation
        if (urlLang && ['en', 'ar'].includes(urlLang)) {
            localStorage.setItem('lakum_language', urlLang);
            console.log('Language saved to localStorage:', urlLang);
        }
        
        // Debug logging
        console.log('Language Detection:', {
            urlLang: urlLang,
            storedLang: storedLang,
            finalLang: window.LAKUM_LANG,
            urlSearch: window.location.search
        });

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

    <!-- Primary Meta Tags -->
    <meta name="title" content="Events Calendar | LAKUM Artspace Riyadh">
    <meta name="description" content="View LAKUM Artspace&#039;s complete events calendar. Find upcoming art exhibitions, workshops, cultural events, and creative sessions in Riyadh.">
    <meta name="keywords" content="art gallery Riyadh, cultural events Riyadh, art exhibitions Saudi Arabia, event space rental Riyadh, contemporary art gallery, cultural hub Riyadh, art workshops Riyadh, creative space Riyadh">
    <meta name="author" content="LAKUM Artspace">
    <meta name="language" content="<?php echo isArabic() ? "Arabic" : "English"; ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://lakumartspace.infinityfree.me/calendar.php">

    <!-- Alternate Language -->
    <link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/calendar.php?lang=en" />
    <link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/calendar.php?lang=ar" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://lakumartspace.infinityfree.me/calendar">
    <meta property="og:title" content="Events Calendar | LAKUM Artspace Riyadh">
    <meta property="og:description" content="View LAKUM Artspace&#039;s complete events calendar. Find upcoming art exhibitions, workshops, cultural events, and creative sessions in Riyadh.">
    <meta property="og:image" content="https://lakumartspace.infinityfree.me/heroImage/img-4.webp">
    <meta property="og:site_name" content="LAKUM Artspace">
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="ar_SA">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://lakumartspace.infinityfree.me/calendar">
    <meta name="twitter:title" content="Events Calendar | LAKUM Artspace Riyadh">
    <meta name="twitter:description" content="View LAKUM Artspace&#039;s complete events calendar. Find upcoming art exhibitions, workshops, cultural events, and creative sessions in Riyadh.">
    <meta name="twitter:image" content="https://lakumartspace.infinityfree.me/heroImage/img-4.webp">

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

    <link rel="stylesheet" href="calendar.css">
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Centralized) -->
    <?php include('includes/header.php'); ?>

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
    <section class="lakum-hero" style="aspect-ratio: 16/9">
        <div class="lakum-hero__image-wrapper">
            <img src="heroImage/img-4.webp"
                 alt="Calendar"
                 fetchpriority="high"
                 loading="eager"
                 decoding="async"
                 width="1200"
                 height="800"
                 class="lakum-hero__image"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo t('calendar_heading', 'Event Calendar'); ?></h1>
            <p class="lakum-hero__subtitle"><?php echo t('calendar_subtitle', 'Explore our upcoming exhibitions, workshops, and cultural programs'); ?></p>
        </div>
    </section>

    <!-- Calendar Section -->
    <section class="lakum-calendar-main">
        <div class="lakum-container">
            <div class="lakum-calendar-layout">
                <!-- Months Sidebar -->
                <aside class="lakum-calendar-sidebar">
                    <h3 class="lakum-calendar-sidebar__title"><?php echo t('calendar_filter_by_month', 'Filter by Month'); ?></h3>
                    <ul class="lakum-calendar-sidebar__list" id="monthsList">
                        <!-- Months will be loaded dynamically -->
                        <li class="lakum-skeleton-month"></li>
                        <li class="lakum-skeleton-month"></li>
                        <li class="lakum-skeleton-month"></li>
                    </ul>
                </aside>

                <!-- Events Grid -->
                <div class="lakum-calendar-events">
                    <div class="lakum-calendar-events__grid" id="eventsList">
                        <!-- Events will be loaded dynamically -->
                        <div class="lakum-skeleton-card"></div>
                        <div class="lakum-skeleton-card"></div>
                        <div class="lakum-skeleton-card"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Upcoming Events -->
    <section class="lakum-calendar-featured">
        <div class="lakum-container">
            <h2 class="lakum-calendar-featured__title"><?php echo t('calendar_coming_soon', 'Coming Soon'); ?></h2>
            <div class="lakum-calendar-featured__grid" id="featuredEvents">
                <!-- Featured events will be loaded here -->
                <div class="lakum-skeleton-featured"></div>
                <div class="lakum-skeleton-featured"></div>
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

    <script src="js/LanguageManager.js?v=1.0.0" defer></script>
    <script>
        // Translation strings for JavaScript
        const translations = {
            noEventsFound: '<?php echo t("calendar_no_events_found", "No events found"); ?>',
            noEvents: '<?php echo t("calendar_no_events", "No Events"); ?>',
            checkBackSoon: '<?php echo t("calendar_check_back_soon", "Check back soon to see our previous exhibitions and events"); ?>',
            unableToLoad: '<?php echo t("calendar_unable_to_load", "Unable to Load Events"); ?>',
            refreshPage: '<?php echo t("calendar_refresh_page", "Please refresh the page or try again later"); ?>',
            tryDifferentMonth: '<?php echo t("calendar_try_different_month", "Try selecting a different month"); ?>',
            pastEvent: '<?php echo t("calendar_past_event", "Past Event"); ?>',
            noUpcomingEvents: '<?php echo t("calendar_no_upcoming_events", "No upcoming events at this time"); ?>',
            checkBackExhibitions: '<?php echo t("calendar_check_back_exhibitions", "Check back soon for new exhibitions"); ?>',
            discoverMore: '<?php echo t("discover_more", "Discover More"); ?>',
            // Month names for translations
            january: '<?php echo t("january", "January"); ?>',
            february: '<?php echo t("february", "February"); ?>',
            march: '<?php echo t("march", "March"); ?>',
            april: '<?php echo t("april", "April"); ?>',
            may: '<?php echo t("may", "May"); ?>',
            june: '<?php echo t("june", "June"); ?>',
            july: '<?php echo t("july", "July"); ?>',
            august: '<?php echo t("august", "August"); ?>',
            september: '<?php echo t("september", "September"); ?>',
            october: '<?php echo t("october", "October"); ?>',
            november: '<?php echo t("november", "November"); ?>',
            december: '<?php echo t("december", "December"); ?>'
        };

        let allEvents = [];
        let groupedEvents = {};

        // Function to translate month names
        function translateMonthName(englishMonth) {
            const monthMap = {
                'January': translations.january,
                'February': translations.february,
                'March': translations.march,
                'April': translations.april,
                'May': translations.may,
                'June': translations.june,
                'July': translations.july,
                'August': translations.august,
                'September': translations.september,
                'October': translations.october,
                'November': translations.november,
                'December': translations.december
            };
            return monthMap[englishMonth] || englishMonth;
        }

        // Helper function to convert 24h to 12h format
        function convertTo12Hour(timeRange) {
            if (!timeRange) return '';
            const times = timeRange.split('-').map(t => t.trim());

            function formatTime(t) {
                let [hours, minutes] = t.split(':').map(Number);
                const suffix = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                return `${hours}:${minutes.toString().padStart(2, '0')} ${suffix}`;
            }

            if (times.length === 2) {
                return `${formatTime(times[0])} - ${formatTime(times[1])}`;
            } else if (times.length === 1) {
                return formatTime(times[0]);
            }
            return timeRange;
        }

        // Helper function to get event URL (slug or id)
        const getEventUrl = (event) => {
            console.log('getEventUrl called with event:', event);
            const slug = event.slug || event.id;
            console.log('Using slug/id:', slug);
            const lang = window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            const url = `event.php?id=${slug}&lang=${lang}`;
            console.log('Generated URL:', url);
            return url;
        };

        // Load ALL events (including past) - real database data only
        function loadAllEventsFromDatabase() {
            const lang = LanguageManager.getLanguage();
            const timestamp = new Date().getTime();
            fetch(`api/get_events.php?type=all&lang=${lang}&t=${timestamp}`, {
                cache: 'no-store'
            })
            .then(response => response.json())
            .then(apiResponse => {
                // Extract data from API response
                let events = apiResponse.data || apiResponse || [];
                
                // Ensure we have an array
                if (!Array.isArray(events)) {
                    events = [];
                }
                
                console.log('Loaded events from database:', events.length);
                
                // Sort events: UPCOMING first (closest to now), then PAST (most recent first)
                const now = new Date();
                events.sort((a, b) => {
                    const dateA = new Date(a.event_date + ' ' + (a.event_time || '00:00:00'));
                    const dateB = new Date(b.event_date + ' ' + (b.event_time || '00:00:00'));

                    const isAUpcoming = dateA >= now;
                    const isBUpcoming = dateB >= now;

                    // If one is upcoming and one is past, upcoming comes first
                    if (isAUpcoming && !isBUpcoming) return -1;
                    if (!isAUpcoming && isBUpcoming) return 1;

                    // Both upcoming: sort by closest (soonest first)
                    if (isAUpcoming && isBUpcoming) {
                        return dateA - dateB;
                    }

                    // Both past: sort by most recent first (reverse chronological)
                    return dateB - dateA;
                });

                allEvents = events;

                if (!events || events.length === 0) {
                    document.getElementById('monthsList').innerHTML = `<li class="lakum-calendar-sidebar__empty">${translations.noEventsFound}</li>`;
                    document.getElementById('eventsList').innerHTML = `
                        <div class="lakum-empty-state">
                            <i class="ri-calendar-line lakum-empty-state__icon"></i>
                            <h3 class="lakum-empty-state__title">${translations.noEvents}</h3>
                            <p class="lakum-empty-state__text">${translations.checkBackSoon}</p>
                        </div>
                    `;
                    return;
                }

                // Group events by year and month
                groupedEvents = {};
                events.forEach(event => {
                    const year = event.year;
                    const month = event.month;
                    const key = `${year}-${month}`;

                    if (!groupedEvents[year]) {
                        groupedEvents[year] = {};
                    }
                    if (!groupedEvents[year][month]) {
                        groupedEvents[year][month] = [];
                    }
                    groupedEvents[year][month].push(event);
                });

                // Get current month and year
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear().toString();
                const currentMonthName = currentDate.toLocaleString('en-US', {
                    month: 'long'
                });

                // Populate sidebar with years and months
                const monthsList = document.getElementById('monthsList');
                monthsList.innerHTML = '';

                const years = Object.keys(groupedEvents).sort((a, b) => b - a); // Sort years descending
                let currentMonthElement = null;
                const monthElements = [];

                years.forEach(year => {
                    // Add year header
                    const yearHeader = document.createElement('li');
                    yearHeader.className = 'lakum-calendar-sidebar__year';
                    yearHeader.textContent = year;
                    monthsList.appendChild(yearHeader);

                    // Add months for this year
                    const months = Object.keys(groupedEvents[year]);
                    months.forEach((month, index) => {
                        const li = document.createElement('li');
                        li.className = 'lakum-calendar-sidebar__item';
                        li.textContent = translateMonthName(month);
                        li.dataset.year = year;
                        li.dataset.month = month;
                        li.dataset.originalMonth = month; // Store original English month

                        // Check if this is the current month
                        if (year === currentYear && month === currentMonthName) {
                            currentMonthElement = li;
                        }

                        li.addEventListener('click', () => {
                            filterByYearMonth(year, month, li);
                        });
                        monthsList.appendChild(li);
                        monthElements.push({
                            element: li,
                            month: month
                        });
                    });
                });

                // Translate month names if needed
                if (window.translationHelper && window.translationHelper.needsTranslation()) {
                    monthElements.forEach(async ({
                        element,
                        month
                    }) => {
                        const translated = await window.translationHelper.translateText(month, 'ar', 'en');
                        element.textContent = translated;
                    });
                }

                // Always default to current month if it exists, otherwise show first available
                if (currentMonthElement) {
                    currentMonthElement.classList.add('active');
                    filterByYearMonth(currentYear, currentMonthName, currentMonthElement);

                    // Scroll to current month in sidebar
                    setTimeout(() => {
                        currentMonthElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 100);
                } else if (years.length > 0) {
                    // Fallback: show first available month
                    const firstYear = years[0];
                    const firstMonth = Object.keys(groupedEvents[firstYear])[0];
                    const firstElement = document.querySelector(`[data-year="${firstYear}"][data-month="${firstMonth}"]`);
                    if (firstElement) {
                        firstElement.classList.add('active');
                        filterByYearMonth(firstYear, firstMonth, firstElement);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading events:', error);
                // Show empty state on error - no mock data
                loadEventsData([]);
            });
        }

        // Function to process and display events
        function loadEventsData(events) {
            // Sort events: UPCOMING first (closest to now), then PAST (most recent first)
            const now = new Date();
            events.sort((a, b) => {
                const dateA = new Date(a.event_date + ' ' + (a.event_time || '00:00:00'));
                const dateB = new Date(b.event_date + ' ' + (b.event_time || '00:00:00'));

                const isAUpcoming = dateA >= now;
                const isBUpcoming = dateB >= now;

                if (isAUpcoming && !isBUpcoming) return -1;
                if (!isAUpcoming && isBUpcoming) return 1;

                if (isAUpcoming && isBUpcoming) {
                    return dateA - dateB;
                }

                return dateB - dateA;
            });

            allEvents = events;

            if (!events || events.length === 0) {
                document.getElementById('monthsList').innerHTML = `<li class="lakum-calendar-sidebar__empty">${translations.noEventsFound}</li>`;
                document.getElementById('eventsList').innerHTML = `
                    <div class="lakum-empty-state">
                        <i class="ri-calendar-line lakum-empty-state__icon"></i>
                        <h3 class="lakum-empty-state__title">${translations.noEvents}</h3>
                        <p class="lakum-empty-state__text">${translations.checkBackSoon}</p>
                    </div>
                `;
                return;
            }

            // Group events by year and month
            groupedEvents = {};
            events.forEach(event => {
                const year = event.year;
                const month = event.month;
                const key = `${year}-${month}`;

                if (!groupedEvents[year]) {
                    groupedEvents[year] = {};
                }
                if (!groupedEvents[year][month]) {
                    groupedEvents[year][month] = [];
                }
                groupedEvents[year][month].push(event);
            });

            // Get current month and year
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear().toString();
            const currentMonthName = currentDate.toLocaleString('en-US', {
                month: 'long'
            });

            // Populate sidebar with years and months
            const monthsList = document.getElementById('monthsList');
            monthsList.innerHTML = '';

            const years = Object.keys(groupedEvents).sort((a, b) => b - a);
            let currentMonthElement = null;
            const monthElements = [];

            years.forEach(year => {
                const yearHeader = document.createElement('li');
                yearHeader.className = 'lakum-calendar-sidebar__year';
                yearHeader.textContent = year;
                monthsList.appendChild(yearHeader);

                const months = Object.keys(groupedEvents[year]);
                months.forEach((month, index) => {
                    const li = document.createElement('li');
                    li.className = 'lakum-calendar-sidebar__item';
                    li.textContent = translateMonthName(month);
                    li.dataset.year = year;
                    li.dataset.month = month;
                    li.dataset.originalMonth = month;

                    if (year === currentYear && month === currentMonthName) {
                        currentMonthElement = li;
                    }

                    li.addEventListener('click', () => {
                        filterByYearMonth(year, month, li);
                    });
                    monthsList.appendChild(li);
                    monthElements.push({
                        element: li,
                        month: month
                    });
                });
            });

            if (currentMonthElement) {
                currentMonthElement.classList.add('active');
                filterByYearMonth(currentYear, currentMonthName, currentMonthElement);

                setTimeout(() => {
                    currentMonthElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            } else if (years.length > 0) {
                const firstYear = years[0];
                const firstMonth = Object.keys(groupedEvents[firstYear])[0];
                const firstElement = document.querySelector(`[data-year="${firstYear}"][data-month="${firstMonth}"]`);
                if (firstElement) {
                    firstElement.classList.add('active');
                    filterByYearMonth(firstYear, firstMonth, firstElement);
                }
            }
        }

        async function filterByYearMonth(year, month, clickedElement) {
            // Update active state
            document.querySelectorAll('.lakum-calendar-sidebar__item').forEach(li => li.classList.remove('active'));
            if (clickedElement) {
                clickedElement.classList.add('active');
            } else {
                // If no element clicked, find and activate the matching element
                const matchingElement = document.querySelector(`[data-year="${year}"][data-month="${month}"]`);
                if (matchingElement) {
                    matchingElement.classList.add('active');
                }
            }

            // Get events for this year/month
            let filteredEvents = groupedEvents[year] && groupedEvents[year][month] ? groupedEvents[year][month] : [];

            // Sort events: UPCOMING first (closest to now), then PAST (most recent first)
            const now = new Date();
            filteredEvents.sort((a, b) => {
                const dateA = new Date(a.event_date + ' ' + (a.event_time || '00:00:00'));
                const dateB = new Date(b.event_date + ' ' + (b.event_time || '00:00:00'));

                const isAUpcoming = dateA >= now;
                const isBUpcoming = dateB >= now;

                // If one is upcoming and one is past, upcoming comes first
                if (isAUpcoming && !isBUpcoming) return -1;
                if (!isAUpcoming && isBUpcoming) return 1;

                // Both upcoming: sort by closest (soonest first)
                if (isAUpcoming && isBUpcoming) {
                    return dateA - dateB;
                }

                // Both past: sort by most recent first (reverse chronological)
                return dateB - dateA;
            });

            // Display events
            const container = document.getElementById('eventsList');
            if (!container) return;

            container.innerHTML = '';

            if (filteredEvents.length === 0) {
                container.innerHTML = `
                    <div class="lakum-empty-state">
                        <i class="ri-calendar-line lakum-empty-state__icon"></i>
                        <h3 class="lakum-empty-state__title">${translations.noEvents}</h3>
                        <p class="lakum-empty-state__text">${translations.tryDifferentMonth}</p>
                    </div>
                `;
                return;
            }

            // Create events immediately
            const eventCards = [];
            filteredEvents.forEach(event => {
                const isPast = new Date(event.event_date) < new Date();
                const eventCard = document.createElement('div');
                eventCard.className = 'lakum-calendar-card' + (isPast ? ' lakum-calendar-card--past' : '');
                eventCard.style.cursor = 'pointer';
                eventCard.onclick = () => window.location.href = getEventUrl(event);

                eventCard.innerHTML = `
                    <div class="lakum-calendar-card__image-wrapper">
                        <img src="${event.cover_image || 'assest/img-3.JPG'}" 
                             alt="${event.title}" 
                             class="lakum-calendar-card__image">
                        <div class="lakum-calendar-card__date-badge">
                            <span class="lakum-calendar-card__day">${event.day}</span>
                            <span class="lakum-calendar-card__month">${(event.month_short || event.month || 'JAN').substring(0, 3).toUpperCase()}</span>
                        </div>
                        ${isPast ? `<div class="lakum-calendar-card__past-badge">${translations.pastEvent}</div>` : ''}
                    </div>
                    <div class="lakum-calendar-card__content">
                        <h3 class="lakum-calendar-card__title">${event.title}</h3>
                        <p class="lakum-calendar-card__time">
                            <i class="ri-time-line"></i>
                            ${convertTo12Hour((event.event_time || '17:00') + ' - ' + (event.event_end_time || '22:00'))}
                        </p>
                    </div>
                `;

                container.appendChild(eventCard);
                eventCards.push(eventCard);
            });

            // Translate in background
            if (window.translationHelper && window.translationHelper.needsTranslation()) {
                window.translationHelper.translateArrayProgressive(
                    filteredEvents, ['title', 'description', 'location'],
                    (translated, index) => {
                        const card = eventCards[index];
                        if (card) {
                            const titleEl = card.querySelector('.lakum-calendar-card__title');
                            if (titleEl) titleEl.textContent = translated.title;
                        }
                    },
                    'ar'
                );
            }
        }

        // Load closest 2 upcoming events for featured section
        function loadFeaturedEvents() {
            const lang = LanguageManager.getLanguage();
            // Use type=upcoming to only get future events (today or later)
            fetch(`api/get_events.php?type=upcoming&limit=2&lang=${lang}`)
                .then(response => response.json())
                .then(async data => {
                    const container = document.getElementById('featuredEvents');
                    const section = document.querySelector('.lakum-calendar-featured');
                    container.innerHTML = '';

                    // Handle API response
                    let events = data.data || data || [];
                    if (!Array.isArray(events)) {
                        events = [];
                    }

                    // Filter to only include today or future events
                    const now = new Date();
                    now.setHours(0, 0, 0, 0); // Start of today
                    const upcomingEvents = events.filter(event => {
                        const eventDate = new Date(event.event_date);
                        return eventDate >= now;
                    });

                    // If no upcoming events, show empty state and hide section
                    if (upcomingEvents.length === 0) {
                        container.innerHTML = `
                            <div class="lakum-empty-state" style="grid-column: 1 / -1;">
                                <i class="ri-calendar-line lakum-empty-state__icon"></i>
                                <h3 class="lakum-empty-state__title">${translations.noUpcomingEvents}</h3>
                                <p class="lakum-empty-state__text">${translations.checkBackExhibitions}</p>
                            </div>
                        `;
                        // Optionally hide the entire section
                        if (section) {
                            section.style.display = 'none';
                        }
                        return;
                    }

                    // Show the section if there are upcoming events
                    if (section) {
                        section.style.display = 'block';
                    }

                    // Render upcoming events (up to 2)
                    const featuredCards = [];
                    upcomingEvents.slice(0, 2).forEach(event => {
                        const featuredCard = document.createElement('div');
                        featuredCard.className = 'lakum-featured-card';
                        featuredCard.style.cursor = 'pointer';
                        featuredCard.onclick = () => window.location.href = getEventUrl(event);

                        const monthStr = (event.month_short || event.month || 'JAN').substring(0, 3).toUpperCase();
                        featuredCard.innerHTML = `
                            <div class="lakum-featured-card__image-wrapper">
                                <img src="${event.cover_image || 'assest/img-3.JPG'}" 
                                     alt="${event.title}" 
                                     class="lakum-featured-card__image">
                                <div class="lakum-featured-card__overlay"></div>
                                <div class="lakum-featured-card__content">
                                    <div class="lakum-featured-card__date">
                                        <span class="lakum-featured-card__day">${event.day || '01'}</span>
                                        <span class="lakum-featured-card__month">${monthStr}</span>
                                    </div>
                                    <h3 class="lakum-featured-card__title">${event.title}</h3>
                                    <p class="lakum-featured-card__time">
                                        <i class="ri-time-line"></i>
                                        ${convertTo12Hour((event.event_time || '17:00') + ' - ' + (event.event_end_time || '22:00'))}
                                    </p>
                                    <button class="lakum-btn lakum-btn--outline-light">${translations.discoverMore}</button>
                                </div>
                            </div>
                        `;

                        container.appendChild(featuredCard);
                        featuredCards.push(featuredCard);
                    });

                    // Translate in background
                    if (window.translationHelper && window.translationHelper.needsTranslation()) {
                        window.translationHelper.translateArrayProgressive(
                            upcomingEvents.slice(0, 2), ['title', 'description'],
                            (translated, index) => {
                                const card = featuredCards[index];
                                if (card) {
                                    const titleEl = card.querySelector('.lakum-featured-card__title');
                                    if (titleEl) titleEl.textContent = translated.title;
                                }
                            },
                            'ar'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error loading featured events:', error);
                    // Show empty state on error
                    const container = document.getElementById('featuredEvents');
                    const section = document.querySelector('.lakum-calendar-featured');
                    container.innerHTML = `
                        <div class="lakum-empty-state" style="grid-column: 1 / -1;">
                            <i class="ri-calendar-line lakum-empty-state__icon"></i>
                            <h3 class="lakum-empty-state__title">${translations.noUpcomingEvents}</h3>
                            <p class="lakum-empty-state__text">${translations.checkBackExhibitions}</p>
                        </div>
                    `;
                    // Hide section on error
                    if (section) {
                        section.style.display = 'none';
                    }
                });
        }

        // Call the function when page loads
        function initCalendarPage() {
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initCalendarPage, 100);
                return;
            }
            loadAllEventsFromDatabase();
            loadFeaturedEvents();
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCalendarPage);
        } else {
            initCalendarPage();
        }
    </script>

    <!-- Mobile Performance Optimizer -->
    <!-- Fun Interactions & Animations -->
    <script src="assest/fun-interactions.js?v=5.0.0" defer></script>

    <!-- Critical Performance Optimizations -->
    <script>
        (function() {
            'use strict';

            // 1. Detect connection speed and adjust quality
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            const slowConnection = connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g');

            if (slowConnection) {
                document.documentElement.classList.add('slow-connection');
                sessionStorage.setItem('slowConnection', 'true');
            }

            // 2. Monitor LCP
            if ('PerformanceObserver' in window) {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    const lcpTime = Math.round(lastEntry.renderTime || lastEntry.loadTime);
                    console.log('LCP:', lcpTime, 'ms');

                    if (lcpTime > 2500) {
                        console.warn('Slow LCP detected:', lcpTime, 'ms');
                        document.documentElement.classList.add('reduce-motion');
                    }
                });

                try {
                    lcpObserver.observe({
                        entryTypes: ['largest-contentful-paint']
                    });
                } catch (e) {
                    console.error('LCP observation failed:', e);
                }
            }

            // 3. Prevent layout shifts - add dimensions to images without them
            const images = document.querySelectorAll('img:not([width]):not([height])');
            images.forEach(img => {
                if (img.naturalWidth && img.naturalHeight) {
                    img.setAttribute('width', img.naturalWidth);
                    img.setAttribute('height', img.naturalHeight);
                    img.style.aspectRatio = img.naturalWidth + '/' + img.naturalHeight;
                }
            });

            // 4. Optimize images on slow connections
            if (slowConnection) {
                document.addEventListener('DOMContentLoaded', function() {
                    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                    lazyImages.forEach(img => {
                        const src = img.getAttribute('src');
                        if (src && src.includes('large')) {
                            img.setAttribute('src', src.replace('large', 'medium'));
                        }
                    });
                });
            }
        })();
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

    // Also listen for storage changes (multi-tab sync)
    window.addEventListener('storage', (e) => {
        if (e.key === 'lakum_language' && e.newValue) {
            const lang = e.newValue;
            fetch(`api/get-translations.php?lang=${lang}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.translations) {
                        Object.assign(translations, data.translations);
                        updateNavbarFooterLanguage();
                    }
                })
                .catch(err => console.log('Language update skipped'));
        }
    });

    // Call on page load
    updateNavbarFooterLanguage();
</script>

    <!-- Expandable Floating Contact Button -->
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

</body>

</html>



















