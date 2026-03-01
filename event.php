<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Event - LAKUM Artspace</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">

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
    <link rel="preload" as="image" href="assest/img-4.png" fetchpriority="high">

    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Preload critical assets -->
    <link rel="preload" href="global-styles.css" as="style">
    <link rel="preload" href="lakum-components.css" as="style">

    <!-- Preload critical fonts -->
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

    <!-- Greta Arabic Font - Universal for both Arabic and English -->
    <!-- Core Styles - Critical CSS loaded synchronously -->
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    
    <!-- Non-critical CSS - Defer loading -->
    <link rel="preload" href="Home.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="rtl.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="fonts/greta-arabic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Fallback for no-JS -->
    <noscript>
        <link rel="stylesheet" href="Home.css">
        <link rel="stylesheet" href="rtl.css">
        <link rel="stylesheet" href="fonts/greta-arabic.css">
    </noscript>

    <!-- Icons - Critical for UI elements like close button -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Image Optimizer - Critical for performance -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="js/LanguageManager.js?v=1.0.0"></script>

    <!-- Scripts - Defer non-critical JavaScript -->
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

    <!-- Event Detail Styles -->
    <link rel="stylesheet" href="event-detail.css">
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/event.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/event.php?lang=ar" />
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">

    <!-- Page Loader -->
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <header class="lakum-header">
        <div class="lakum-header__container">
            <div class="lakum-header__logo">
                <a href="index.php" class="lakum-logo">
                    <!-- English: Swapped -->
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
                </a>
            </div>

            <nav class="lakum-nav">
                <ul class="lakum-nav__list">
                    <li class="lakum-nav__item">
                        <a href="index.php" class="lakum-nav__link "><?php echo t('home', 'Home'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="about.php" class="lakum-nav__link "><?php echo t('about', 'About'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="spaces.php" class="lakum-nav__link "><?php echo t('spaces', 'Spaces'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="exhibitions.php" class="lakum-nav__link "><?php echo t('exhibitions', 'Exhibitions'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="calendar.php" class="lakum-nav__link "><?php echo t('calendar', 'Calendar'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="blog.php" class="lakum-nav__link "><?php echo t('blog', 'Blog'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="press.php" class="lakum-nav__link "><?php echo t('press', 'Press'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="contact.php" class="lakum-nav__link "><?php echo t('contact_us', 'Contact'); ?></a>
                    </li>
                    <li class="lakum-nav__item">
                        <a href="shop.php" class="lakum-nav__link "><?php echo t('shop', 'Shop'); ?></a>
                    </li>

                </ul>
            </nav>

            <!-- Language Switcher -->
            <div class="lakum-language-switcher">
                <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
                <i class="ri-global-line"></i>
                <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
            </a>
            </div>

            <button class="lakum-header__mobile-toggle" aria-label="Toggle menu">
            <span class="lakum-header__mobile-icon"></span>
        </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="event-hero">
        <div class="event-hero__image-wrapper">
            <img id="hero-image" src="assest/img-4.png" alt="Event" class="event-hero__image">
            <div class="event-hero__overlay"></div>
        </div>
        <div class="event-hero__content">
            <div class="event-container">
                <h1 class="event-hero__title" id="event-title">Loading...</h1>
                <div class="event-hero__meta">
                    <span class="event-hero__meta-item">
                        <i class="ri-calendar-line"></i>
                        <span id="event-date">Loading...</span>
                    </span>
                    <span class="event-hero__meta-item">
                        <i class="ri-map-pin-line"></i>
                        <span id="event-location">Loading...</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Event Description -->
    <section class="event-section">
        <div class="event-container event-container--narrow">
            <div class="event-description">
                <h2 class="event-section__title"><?php echo t('about_this_event', 'About This Event'); ?></h2>
                <div class="event-description__text" id="event-description">
                    Loading...
                </div>
            </div>
        </div>
    </section>

    <!-- Event Gallery -->
    <section class="event-section event-section--gallery">
        <div class="event-container">
            <h2 class="event-section__title"><?php echo t('gallery', 'Gallery'); ?></h2>
            <div class="event-gallery" id="event-gallery">
                <div style="text-align: center; padding: 40px; grid-column: 1/-1;">Loading gallery...</div>
            </div>
        </div>
    </section>

    <!-- Event Video Section -->
    <section class="event-section event-section--video" id="videoSection" style="display: none; background: #ffffff; padding: 80px 0;">
        <div class="event-container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
            <h2 class="event-section__title" style="font-size: 2.5rem; font-weight: 300; color: #1a1a1a; margin-bottom: 50px; text-align: center;"><?php echo t('event_video', 'Event Video'); ?></h2>
            <div class="event-video-wrapper" id="event-video-wrapper" style="position: relative; width: 100%; height: 700px; overflow: hidden; border-radius: 4px; background: #000;">
                <iframe id="event-video" class="event-video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 4px;"></iframe>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="event-cta">
        <div class="event-container">
            <div class="event-cta__content">
                <h2 class="event-cta__title"><?php echo t('create_exhibition', 'Create Your Own Exhibition'); ?></h2>
                <p class="event-cta__text"><?php echo t('create_exhibition_desc', "Transform your vision into reality with LAKUM's versatile spaces and comprehensive support services"); ?></p>
                <a href="spaces.php" class="event-cta__button">
                    <?php echo t('book_space', 'Book LAKUM Space'); ?>
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <!-- English: Swapped -->
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right">
                    </div>
                    <p class="lakum-footer__tagline">Where Encounters Shape Culture</p>
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
                <p class="lakum-footer__copyright">Â© 2025 - 2027 LAKUM Artspace. All rights reserved.</p>
                <div class="lakum-footer__legal">
                    <a href="terms.php" class="lakum-footer__legal-link">Terms & Conditions</a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php" class="lakum-footer__legal-link">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Expandable Floating Contact Button -->
    <div class="lakum-contact-fab" id="lakumContactFab">
        <button class="lakum-contact-fab__trigger" id="fabTrigger" aria-label="Contact options">
        <i class="ri-mail-line lakum-contact-fab__icon"></i>
        <i class="ri-close-line lakum-contact-fab__close"></i>
    </button>

        <div class="lakum-contact-fab__menu" id="fabMenu">
            <a href="tel:+966920012083" class="lakum-contact-fab__item" data-tooltip="Call us">
            <i class="ri-phone-line"></i>
        </a>
            <a href="https://wa.me/966920012083" target="_blank" class="lakum-contact-fab__item" data-tooltip="WhatsApp">
            <i class="ri-whatsapp-line"></i>
        </a>
            <a href="mailto:info@lakumartspace.com" class="lakum-contact-fab__item" data-tooltip="Email">
            <i class="ri-mail-line"></i>
        </a>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
       <button class="lightbox__close" onclick="closeLightbox()" title="Close">×</button>
        <button class="lightbox__prev" onclick="event.stopPropagation(); prevImage()">
            <i class="ri-arrow-left-s-line"></i>
        </button>
        <button class="lightbox__next" onclick="event.stopPropagation(); nextImage()">
            <i class="ri-arrow-right-s-line"></i>
        </button>
        <div class="lightbox__content" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="Gallery Image" class="lightbox__image">
            <div class="lightbox__counter">
                <span id="lightboxCounter"></span>
            </div>
        </div>
    </div>

    <script>
        // Expandable FAB functionality
        (function() {
            const fab = document.getElementById('lakumContactFab');
            const trigger = document.getElementById('fabTrigger');
            const menu = document.getElementById('fabMenu');

            if (trigger && fab) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    fab.classList.toggle('lakum-contact-fab--active');
                });

                // Close when clicking outside
                document.addEventListener('click', function(e) {
                    if (!fab.contains(e.target)) {
                        fab.classList.remove('lakum-contact-fab--active');
                    }
                });
            }
        })();

        // Mobile menu toggle
        (function() {
            const toggle = document.querySelector('.lakum-header__mobile-toggle');
            const nav = document.querySelector('.lakum-nav');
            const header = document.querySelector('.lakum-header');

            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    toggle.classList.toggle('lakum-header__mobile-toggle--active');
                    nav.classList.toggle('lakum-nav--active');
                    header.classList.toggle('lakum-header--menu-open');
                    document.body.style.overflow = nav.classList.contains('lakum-nav--active') ? 'hidden' : '';
                });

                // Close menu when clicking nav link
                const navLinks = document.querySelectorAll('.lakum-nav__link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        toggle.classList.remove('lakum-header__mobile-toggle--active');
                        nav.classList.remove('lakum-nav--active');
                        header.classList.remove('lakum-header--menu-open');
                        document.body.style.overflow = '';
                    });
                });
            }
        })();
    </script>

    <script>
        // Gallery lightbox
        let currentImageIndex = 0;
        let galleryImages = [];
        let currentEvent = null;

        function openLightbox(index) {
            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            const image = document.getElementById('lightboxImage');
            image.src = galleryImages[index];
            updateCounter();
            lightbox.classList.add('active');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            document.getElementById('lightboxImage').src = galleryImages[currentImageIndex];
            updateCounter();
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            document.getElementById('lightboxImage').src = galleryImages[currentImageIndex];
            updateCounter();
        }

        function updateCounter() {
            document.getElementById('lightboxCounter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        }

        // Mock data - REMOVED - Now using only real database data
        const mockEvents = {};
        let currentLanguage = 'en'; // Track current language
        
        async function loadEventData() {
            const params = new URLSearchParams(window.location.search);
            
            // Support multiple URL formats:
            // 1. /event/dior (rewritten to /event.php?title=dior)
            // 2. /event.php?title=dior
            // 3. /event.php?id=18 (backward compatibility)
            let eventTitleParam = params.get('title') || params.get('id') || '1';
            let lang = params.get('lang');
            
            // If no lang in URL, get from localStorage or default to 'en'
            if (!lang) {
                lang = localStorage.getItem('lakum_language') || 'en';
                // Update URL to include language parameter for consistency
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('lang', lang);
                window.history.replaceState({}, '', newUrl);
            }
            
            currentLanguage = lang;

            console.log('Loading event with title/ID:', eventTitleParam, 'Language:', lang);

            try {
                // Try to fetch from API (supports both numeric ID and slug/title)
                let response = await fetch(`api/get_event_details.php?id=${eventTitleParam}&lang=${lang}`);
                
                let data = await response.json();
                console.log('API Response:', data);

                // If event not found and ID was default (1), try to get first available event
                if (!data.success && eventTitleParam === '1') {
                    console.log('Event not found, fetching first available event...');
                    const lang = LanguageManager.getLanguage();
                    response = await fetch(`api/get_events.php?lang=${lang}`);
                    const eventsData = await response.json();
                    
                    if (eventsData.success && eventsData.data && eventsData.data.length > 0) {
                        const firstEventId = eventsData.data[0].id;
                        console.log('Found first event ID:', firstEventId);
                        response = await fetch(`api/get_event_details.php?id=${firstEventId}&lang=${lang}`);
                        data = await response.json();
                    }
                }

                if (data.success && data.event) {
                    console.log('Loaded from database:', data.event);
                    displayEvent(data.event, data.gallery, lang);
                } else {
                    throw new Error(data.message || 'Failed to load event');
                }
            } catch (error) {
                console.error('Error loading event from API:', error);
                // Show error message instead of falling back to mock data
                const descElement = document.getElementById('event-description');
                descElement.textContent = 'Error loading event: ' + error.message;
                document.getElementById('event-title').textContent = 'Event Not Found';
            }
        }

        // Use mock data (for testing or when API is unavailable)
        function useMockData(eventId) {
            const mockData = mockEvents[eventId];
            if (mockData) {
                console.log('Mock data found for event:', eventId, mockData);
                displayEvent(mockData.event, mockData.gallery, currentLanguage);
            } else {
                console.log('No mock data found for event:', eventId, 'Using event 1');
                const fallbackData = mockEvents[1];
                displayEvent(fallbackData.event, fallbackData.gallery, currentLanguage);
            }
        }
        
        // Display event data
        function displayEvent(event, gallery, lang = 'en') {
            console.log('=== displayEvent called ===');
            console.log('Event object:', event);
            console.log('Language:', lang);
            console.log('Event video_url:', event.video_url);
            
            currentEvent = event;
            currentLanguage = lang;

            // Select the correct language fields
            let title = event.title;
            let description = event.description;
            let location = event.location;

            // If Arabic is requested and Arabic fields exist and are not empty, use them
            if (lang === 'ar') {
                if (event.title_ar && event.title_ar.trim()) title = event.title_ar;
                if (event.description_ar && event.description_ar.trim()) description = event.description_ar;
                if (event.location_ar && event.location_ar.trim()) location = event.location_ar;
            }

            // Update page title and meta tags
            document.getElementById('page-title').textContent = `${title} - LAKUM Artspace`;
            
            // Update meta description
            const metaDesc = document.querySelector('meta[name="description"]');
            if (metaDesc) metaDesc.setAttribute('content', description || title);

            // Update hero section with real data
            document.getElementById('event-title').textContent = title;
            
            // Use cover image from database, fallback to default
            const heroImage = document.getElementById('hero-image');
            heroImage.src = event.cover_image || 'assest/img-4.png';
            heroImage.alt = title;

            // Format and display date/time
            document.getElementById('event-date').textContent = formatEventDateTime(event);
            document.getElementById('event-location').textContent = location || 'LAKUM Artspace';

            // Update description - all from database
            const descElement = document.getElementById('event-description');
            descElement.textContent = description || 'No description available';

            // Display video if available
            console.log('Checking for video_url...');
            if (event.video_url) {
                console.log('Video URL found, calling displayVideo with:', event.video_url);
                displayVideo(event.video_url);
            } else {
                console.log('No video_url in event object');
            }

            // Load gallery images from database
            if (gallery && gallery.length > 0) {
                galleryImages = gallery.map(img => img.image_url);
                renderGallery(gallery);
            } else {
                // If no gallery, show cover image as fallback
                if (event.cover_image) {
                    galleryImages = [event.cover_image];
                    renderGallery([{ image_url: event.cover_image, caption: event.title }]);
                }
            }

            // Update structured data
            updateStructuredData(event);
            console.log('=== displayEvent END ===');
        }

        // Display video from URL (YouTube or Vimeo)
        function displayVideo(videoUrl) {
            const videoSection = document.getElementById('videoSection');
            const videoFrame = document.getElementById('event-video');
            
            console.log('=== displayVideo DEBUG ===');
            console.log('videoUrl:', videoUrl);
            console.log('videoUrl type:', typeof videoUrl);
            console.log('videoUrl length:', videoUrl ? videoUrl.length : 'null');
            console.log('videoSection element:', videoSection);
            console.log('videoFrame element:', videoFrame);
            
            if (!videoUrl || videoUrl.trim() === '') {
                console.log('No video URL provided - hiding section');
                if (videoSection) videoSection.classList.remove('active');
                return;
            }

            let embedUrl = '';

            // Handle YouTube URLs
            if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                let videoId = '';
                
                try {
                    if (videoUrl.includes('youtube.com/watch')) {
                        const url = new URL(videoUrl);
                        videoId = url.searchParams.get('v');
                        console.log('YouTube watch URL - videoId:', videoId);
                    } else if (videoUrl.includes('youtu.be')) {
                        // Extract video ID from youtu.be short URL
                        const match = videoUrl.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
                        if (match) {
                            videoId = match[1];
                            console.log('YouTube short URL - videoId:', videoId);
                        }
                    }
                } catch (e) {
                    console.error('Error parsing YouTube URL:', e);
                }
                
                if (videoId) {
                    embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=0&controls=1`;
                    console.log('Generated YouTube embed URL:', embedUrl);
                }
            }
            // Handle Vimeo URLs
            else if (videoUrl.includes('vimeo.com')) {
                try {
                    const match = videoUrl.match(/vimeo\.com\/(\d+)/);
                    if (match) {
                        const videoId = match[1];
                        embedUrl = `https://player.vimeo.com/video/${videoId}`;
                        console.log('Generated Vimeo embed URL:', embedUrl);
                    }
                } catch (e) {
                    console.error('Error parsing Vimeo URL:', e);
                }
            }

            console.log('Final embedUrl:', embedUrl);
            
            if (embedUrl) {
                console.log('Setting iframe src to:', embedUrl);
                if (videoFrame) {
                    videoFrame.src = embedUrl;
                    console.log('iframe src set successfully');
                }
                if (videoSection) {
                    videoSection.classList.add('active');
                    console.log('Video section active class added');
                }
                console.log('Video section displayed');
            } else {
                console.log('No embed URL generated from:', videoUrl);
                if (videoSection) videoSection.classList.remove('active');
            }
            console.log('=== displayVideo END ===');
        }

        // Format event date and time from database
        function formatEventDateTime(event) {
            const eventDate = new Date(event.event_date);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            const formattedDate = eventDate.toLocaleDateString('en-US', options);
            
            // Convert 24h to 12h format
            function convertTo12Hour(time24h) {
                if (!time24h) return '10:00 AM';
                const [hours, minutes] = time24h.substring(0, 5).split(':');
                let hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                hour = hour % 12 || 12;
                return `${hour}:${minutes} ${ampm}`;
            }
            
            // Get start time
            let startTime = '10:00 AM';
            if (event.event_time) {
                startTime = convertTo12Hour(event.event_time);
            }
            
            // Get end time
            let endTime = '6:00 PM';
            if (event.event_end_time) {
                endTime = convertTo12Hour(event.event_end_time);
            }
            
            // Check if multi-day event
            if (event.end_date && event.end_date !== event.event_date) {
                const endEventDate = new Date(event.end_date);
                const formattedEndDate = endEventDate.toLocaleDateString('en-US', options);
                return `${formattedDate} • ${startTime} - ${formattedEndDate} • ${endTime}`;
            }
            
            return `${formattedDate} • ${startTime} - ${endTime}`;
        }

        // Render gallery from database
        function renderGallery(gallery) {
            const galleryContainer = document.getElementById('event-gallery');
            galleryContainer.innerHTML = '';

            if (!gallery || gallery.length === 0) {
                galleryContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">' + (translations.no_gallery || 'No gallery images available') + '</div>';
                return;
            }

            gallery.forEach((item, index) => {
                const galleryItem = document.createElement('div');
                const itemClass = (index % 5) + 1;
                galleryItem.className = `event-gallery__item event-gallery__item--${itemClass}`;
                galleryItem.style.cursor = 'pointer';
                galleryItem.onclick = () => openLightbox(index);
                
                galleryItem.innerHTML = `
                    <img src="${item.image_url}" alt="${item.caption || 'Gallery Image ' + (index + 1)}" class="event-gallery__image" loading="lazy">
                    <div class="event-gallery__overlay">
                        <i class="ri-zoom-in-line"></i>
                    </div>
                `;
                galleryContainer.appendChild(galleryItem);
            });
        }

        // Update structured data for SEO
        function updateStructuredData(event) {
            // Update Event schema
            const eventSchema = {
                "@context": "https://schema.org",
                "@type": "Event",
                "name": event.title,
                "description": event.description,
                "startDate": event.event_date + "T" + (event.event_time || "10:00:00"),
                "endDate": event.end_date ? event.end_date + "T" + (event.event_end_time || "18:00:00") : event.event_date + "T" + (event.event_end_time || "18:00:00"),
                "location": {
                    "@type": "Place",
                    "name": event.location || "LAKUM Artspace",
                    "address": {
                        "@type": "PostalAddress",
                        "addressLocality": "Riyadh",
                        "addressCountry": "SA"
                    }
                },
                "organizer": {
                    "@type": "Organization",
                    "name": "LAKUM Artspace",
                    "url": "/"
                },
                "image": event.cover_image || "/assest/img-4.png"
            };

            // Update or create schema script
            let schemaScript = document.querySelector('script[type="application/ld+json"]');
            if (!schemaScript) {
                schemaScript = document.createElement('script');
                schemaScript.type = 'application/ld+json';
                document.head.appendChild(schemaScript);
            }
            schemaScript.textContent = JSON.stringify(eventSchema);
        }

        // Load event data on page load
        function initEventPage() {
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initEventPage, 100);
                return;
            }
            loadEventData();
        }
        
        window.addEventListener('DOMContentLoaded', initEventPage);
        
        // Listen for URL changes (when user clicks different event links)
        window.addEventListener('popstate', loadEventData);
        
        // Listen for language changes - reload event data with new language
        document.addEventListener('lakum-language-changed', (e) => {
            console.log('Language changed to:', e.detail?.lang);
            loadEventData();
        });
        
        // Also check for URL changes periodically to reload when ID or language changes
        let lastEventId = null;
        let lastLanguage = null;
        setInterval(() => {
            const params = new URLSearchParams(window.location.search);
            const currentEventTitle = params.get('title') || params.get('id') || 1;
            const currentLanguage = params.get('lang') || 'en';
            if (currentEventTitle !== lastEventId || currentLanguage !== lastLanguage) {
                lastEventId = currentEventTitle;
                lastLanguage = currentLanguage;
                loadEventData();
            }
        }, 500);

        // Keyboard navigation for lightbox
        document.addEventListener('keydown', (e) => {
            if (document.getElementById('lightbox').classList.contains('active')) {
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'ArrowRight') nextImage();
                if (e.key === 'Escape') closeLightbox();
            }
        });

        // Update language switcher based on current language
        function updateLanguageSwitcher() {
            const langSwitcher = document.getElementById('langSwitcher');
            if (!langSwitcher) return;
            
            const currentLang = document.documentElement.lang || 'en';
            
            if (currentLang === 'ar') {
                // Currently in Arabic, show EN to switch to English
                langSwitcher.setAttribute('data-lang-switch', 'en');
                langSwitcher.querySelector('.lakum-lang-text').textContent = 'EN';
                langSwitcher.setAttribute('title', 'Language: English');
            } else {
                // Currently in English, show AR to switch to Arabic
                langSwitcher.setAttribute('data-lang-switch', 'ar');
                langSwitcher.querySelector('.lakum-lang-text').textContent = 'AR';
                langSwitcher.setAttribute('title', 'Language: Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©');
            }
        }

        // Update on page load
        updateLanguageSwitcher();

        // Watch for language changes
        const observer = new MutationObserver(() => {
            if (document.documentElement.lang) {
                updateLanguageSwitcher();
            }
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['lang']
        });

    </script>

    <script>
        // Listen for dynamic content loaded event and render event details based on language
        document.addEventListener('lakum-content-loaded', (e) => {
            if (e.detail.contentType === 'event') {
                const events = e.detail.content;
                
                if (!events || events.length === 0) return;
                
                // Get event ID from URL
                const urlParams = new URLSearchParams(window.location.search);
                const eventId = parseInt(urlParams.get('id'));
                
                // Find the event with matching ID
                const event = Array.isArray(events) ? events.find(ev => ev.id === eventId) : events;
                
                if (!event) return;
                
                // Update page content
                const titleEl = document.getElementById('event-title');
                const dateEl = document.getElementById('event-date');
                const locationEl = document.getElementById('event-location');
                const descriptionEl = document.getElementById('event-description');
                const heroImageEl = document.getElementById('hero-image');
                
                if (titleEl) titleEl.textContent = event.title;
                if (dateEl) dateEl.textContent = event.date ? new Date(event.date).toLocaleDateString() : '';
                if (locationEl) locationEl.textContent = event.location || '';
                if (descriptionEl) descriptionEl.innerHTML = event.description || '';
                if (heroImageEl && event.cover_image) heroImageEl.src = event.cover_image;
            }
        });
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
            footer_copyright: "<?php echo t('footer_copyright', '© 2026 LAKUM Artspace. All rights reserved.'); ?>",
            footer_terms: "<?php echo t('footer_terms', 'Terms & Conditions'); ?>",
            footer_privacy: "<?php echo t('footer_privacy', 'Privacy Policy'); ?>",
            about_this_event: "<?php echo t('about_this_event', 'About This Event'); ?>",
            gallery: "<?php echo t('gallery', 'Gallery'); ?>",
            event_video: "<?php echo t('event_video', 'Event Video'); ?>",
            create_exhibition: "<?php echo t('create_exhibition', 'Create Your Own Exhibition'); ?>",
            create_exhibition_desc: '<?php echo t("create_exhibition_desc", "Transform your vision into reality with LAKUM\'s versatile spaces and comprehensive support services"); ?>',
            book_space: "<?php echo t('book_space', 'Book LAKUM Space'); ?>",
            no_gallery: "<?php echo t('no_gallery', 'No gallery images available'); ?>"
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
            const navItems = document.querySelectorAll('.lakum-nav__link');
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
                footerCopyright.textContent = translations.footer_copyright || '© 2026 LAKUM Artspace. All rights reserved.';
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

    </body>

</html>











