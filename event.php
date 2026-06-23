<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';

// Get title from query parameter (rewritten by .htaccess)
$title = $_GET['title'] ?? null;
$lang = $_GET['lang'] ?? 'en';

// If no title, show error
if (!$title) {
    header('HTTP/1.0 404 Not Found');
    exit('Event not found');
}
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

    <!-- Preload LCP image (hero) - Mobile-first with responsive variants -->
    <link rel="preload" as="image" 
          href="heroImage/img-4.webp"
          imagesrcset="heroImage/img-4.webp 1200w"
          imagesizes="(max-width: 768px) 100vw, 650px"
          fetchpriority="high">
    <!-- Preload critical fonts -->
    <link rel="preload" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>

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
    <link rel="stylesheet" href="event-detail.css">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>

    <link id="hreflang-en" rel="alternate" hreflang="en" href="" />
    <link id="hreflang-ar" rel="alternate" hreflang="ar" href="" />
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <!-- Hero Section -->
    <section class="lakum-hero">
        <div class="lakum-hero__image-wrapper">
            <img id="hero-image" src="assest/img-4.webp" alt="Event" class="lakum-hero__image" loading="eager" fetchpriority="high" decoding="async" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title" id="event-title">Loading...</h1>
            <p class="lakum-hero__subtitle">
                <span id="event-date">Loading...</span> • <span id="event-location">Loading...</span>
            </p>
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
    <section class="event-section event-section--video" id="videoSection">
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
                <a href="spaces.php" id="bookingCtaButton" class="event-cta__button">
                    <?php echo t('book_space', 'Book LAKUM Space'); ?>
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    <script>
        // Load booking link immediately after button is rendered
        (function() {
            async function updateBookingLink() {
                try {
                    console.log('Fetching booking link from settings...');
                    const response = await fetch('/api/get_settings.php');
                    const data = await response.json();
                    
                    if (data.success && data.data && data.data.booking_link) {
                        const btn = document.getElementById('bookingCtaButton');
                        if (btn) {
                            btn.href = data.data.booking_link;
                            btn.target = '_blank';
                            btn.rel = 'noopener noreferrer';
                            console.log('? Booking link updated to:', data.data.booking_link);
                        }
                    }
                } catch (e) {
                    console.error('Error updating booking link:', e);
                }
            }
            updateBookingLink();
        })();
    </script>

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
                        <li><a href="index.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Home</a></li>
                        <li><a href="about.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">About</a></li>
                        <li><a href="spaces.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Spaces</a></li>
                        <li><a href="exhibitions.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Exhibitions</a></li>
                    </ul>
                </nav>

                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title">Explore</h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="calendar.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Calendar</a></li>
                        <li><a href="blog.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Blog</a></li>
                        <li><a href="press.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Press</a></li>
                        <li><a href="contact.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link">Contact</a></li>
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
                        <a href="https://www.snapchat.com/@lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Snapchat">
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

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
       <button class="lightbox__close" onclick="closeLightbox()" title="Close"><i class="ri-close-line"></i></button>
        <button class="lightbox__prev" onclick="event.stopPropagation(); prevImage()">
            <i class="ri-arrow-left-s-line"></i>
        </button>
        <button class="lightbox__next" onclick="event.stopPropagation(); nextImage()">
            <i class="ri-arrow-right-s-line"></i>
        </button>
        <div class="lightbox__content" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="Gallery Image" class="lightbox__image" loading="lazy">
            <div class="lightbox__counter">
                <span id="lightboxCounter"></span>
            </div>
        </div>
    </div>

    <script src="assest/fun-interactions.js" defer></script>
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

        // Pass title from PHP to JavaScript (for clean URLs)
        window.LAKUM_EVENT_TITLE = '<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>';
        window.LAKUM_LANG = '<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>';

        // Mock data - REMOVED - Now using only real database data
        const mockEvents = {};
        let currentLanguage = 'en'; // Track current language
        
        async function loadEventData() {
            const params = new URLSearchParams(window.location.search);
            
            console.log('🚀 loadEventData started');
            console.log('Current URL:', window.location.href);
            
            // Support multiple URL formats:
            // 1. /dior-exhibition (rewritten to /event.php?title=dior-exhibition)
            // 2. /event.php?title=dior-exhibition
            // 3. /event.php?id=18 (backward compatibility)
            // 4. /dior-exhibition?lang=en (clean URL - title from .htaccess rewrite)
            let eventTitleParam = window.LAKUM_EVENT_TITLE || params.get('title') || params.get('id') || '1';
            let lang = window.LAKUM_LANG || params.get('lang');
            
            console.log('📍 eventTitleParam:', eventTitleParam);
            console.log('📍 lang initial:', lang);
            
            // If no lang in URL, get from localStorage or default to 'en'
            if (!lang) {
                lang = localStorage.getItem('lakum_language') || 'en';
                // Update URL to include language parameter for consistency
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('lang', lang);
                window.history.replaceState({}, '', newUrl);
            }
            
            currentLanguage = lang;

            console.log('✅ Loading event with title/ID:', eventTitleParam, 'Language:', lang);

            try {
                // Try to fetch from API (supports both numeric ID and slug/title)
                // Use the parameter name that matches what was provided
                let apiUrl = `/api/get_event_details.php?lang=${lang}`;
                
                // Determine if it's numeric (ID) or text (slug/title)
                if (!isNaN(eventTitleParam) && eventTitleParam.trim() !== '') {
                    apiUrl += `&id=${eventTitleParam}`;
                    console.log('📱 Detected numeric ID format');
                } else {
                    apiUrl += `&title=${encodeURIComponent(eventTitleParam)}`;
                    console.log('📱 Detected slug/title format');
                }
                
                console.log('🔗 API URL:', apiUrl);
                
                let response = await fetch(apiUrl);
                console.log('📨 API Response status:', response.status, response.statusText);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                let data = await response.json();
                console.log('📦 API Response data:', data);

                // If event not found and ID was default (1), try to get first available event
                if (!data.success && eventTitleParam === '1') {
                    console.log('⚠️ Event not found, fetching first available event...');
                    const lang = (typeof LanguageManager !== 'undefined') ? LanguageManager.getLanguage() : 'en';
                    response = await fetch(`/api/get_events.php?lang=${lang}`);
                    const eventsData = await response.json();
                    
                    if (eventsData.success && eventsData.data && eventsData.data.length > 0) {
                        const firstEventId = eventsData.data[0].id;
                        console.log('✅ Found first event ID:', firstEventId);
                        response = await fetch(`/api/get_event_details.php?id=${firstEventId}&lang=${lang}`);
                        data = await response.json();
                    }
                }

                if (data.success && data.event) {
                    console.log('✅ Loaded from database:', data.event);
                    displayEvent(data.event, data.gallery, lang);
                } else {
                    throw new Error(data.message || 'API returned success=false');
                }
            } catch (error) {
                console.error('❌ Error loading event from API:', error);
                console.error('Error details:', error.message, error.stack);
                
                // Hide loader even on error
                const pageLoader = document.getElementById('pageLoader');
                if (pageLoader) {
                    pageLoader.style.display = 'none';
                    pageLoader.style.visibility = 'hidden';
                    console.log('🔴 Error: Loader hidden');
                }
                
                // Show error message instead of falling back to mock data
                const descElement = document.getElementById('event-description');
                if (descElement) {
                    descElement.textContent = 'Error loading event: ' + error.message;
                    descElement.style.color = '#d32f2f';
                    descElement.style.padding = '20px';
                    descElement.style.border = '1px solid #d32f2f';
                }
                
                const titleElement = document.getElementById('event-title');
                if (titleElement) {
                    titleElement.textContent = 'Event Not Found';
                }
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
            console.log('Event event_video:', event.event_video);
            
            // Hide the page loader
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                pageLoader.style.display = 'none';
                pageLoader.style.visibility = 'hidden';
                pageLoader.style.opacity = '0';
                console.log('✅ Page loader hidden');
            }
            
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

            // Update URL to use ENGLISH title slug (always use English for URL, not Arabic)
            // This ensures URLs are consistent regardless of language
            const englishTitle = event.title || event.title_en || title;
            const eventSlug = englishTitle.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
            
            // Update browser URL with English slug and current language
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('title', eventSlug);
            newUrl.searchParams.set('lang', lang);
            // Remove 'id' parameter if it exists (for backward compatibility)
            newUrl.searchParams.delete('id');
            window.history.replaceState({}, '', newUrl);

            // Update hreflang tags with event title (always use English slug)
            const hreflangEn = document.getElementById('hreflang-en');
            const hreflangAr = document.getElementById('hreflang-ar');
            if (hreflangEn) hreflangEn.href = `${window.location.origin}/event/${eventSlug}?lang=en`;
            if (hreflangAr) hreflangAr.href = `${window.location.origin}/event/${eventSlug}?lang=ar`;

            // Update hero section with real data
            document.getElementById('event-title').textContent = title;
            
            // Use cover image from database, fallback to default
            const heroImage = document.getElementById('hero-image');
            heroImage.src = event.cover_image || 'assest/img-4.webp';
            heroImage.alt = title;

            // Format and display date/time
            document.getElementById('event-date').textContent = formatEventDateTime(event);
            document.getElementById('event-location').textContent = location || 'LAKUM Artspace';

            // Update description - all from database
            const descElement = document.getElementById('event-description');
            descElement.textContent = description || 'No description available';

            // Display video if available - Handle both field names (events table uses video_url, exhibitions table uses event_video)
            console.log('=== CHECKING FOR VIDEO ===');
            console.log('event.video_url:', event.video_url);
            console.log('event.event_video:', event.event_video);
            console.log('event.category:', event.category);
            
            // Try ALL possible video fields in order of priority
            let videoUrl = null;
            
            // Check both fields with fallback
            if (event.event_video && String(event.event_video).trim() && String(event.event_video).trim() !== 'null') {
                videoUrl = String(event.event_video).trim();
                console.log('✅ Found video in event_video field:', videoUrl);
            } else if (event.video_url && String(event.video_url).trim() && String(event.video_url).trim() !== 'null') {
                videoUrl = String(event.video_url).trim();
                console.log('✅ Found video in video_url field:', videoUrl);
            }
            
            console.log('Final videoUrl:', videoUrl);
            
            // Check if valid URL
            if (videoUrl && videoUrl !== '' && videoUrl !== 'null' && videoUrl !== 'undefined') {
                console.log('✅ VIDEO FOUND! Calling displayVideo with:', videoUrl);
                displayVideo(videoUrl);
            } else {
                console.log('❌ No valid video URL found - video section will be hidden');
                const videoSection = document.getElementById('videoSection');
                if (videoSection) {
                    videoSection.style.display = 'none';
                }
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
            console.log('🎬 === displayVideo CALLED ===');
            console.log('Input videoUrl:', videoUrl);
            console.log('Type:', typeof videoUrl);
            
            // Get elements
            const videoSection = document.getElementById('videoSection');
            const videoFrame = document.getElementById('event-video');
            
            if (!videoSection || !videoFrame) {
                console.error('🔴 Video elements not found!');
                return;
            }
            
            // Ensure videoUrl is a string
            videoUrl = String(videoUrl).trim();
            
            if (!videoUrl || videoUrl === '' || videoUrl === 'null' || videoUrl === 'undefined') {
                console.log('❌ No valid video URL provided');
                videoSection.style.display = 'none';
                return;
            }

            console.log('✅ Processing video URL:', videoUrl);
            let embedUrl = '';

            // Handle YouTube URLs
            if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                console.log('📺 Detected YouTube URL');
                let videoId = '';
                
                try {
                    if (videoUrl.includes('youtube.com/watch')) {
                        // youtube.com/watch?v=ID
                        const url = new URL(videoUrl);
                        videoId = url.searchParams.get('v');
                    } else if (videoUrl.includes('youtu.be')) {
                        // youtu.be/ID or youtu.be/ID?si=...
                        const match = videoUrl.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
                        if (match) {
                            videoId = match[1];
                        }
                    }
                    
                    if (videoId) {
                        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=0&controls=1&rel=0`;
                        console.log('✅ YouTube ID:', videoId);
                    } else {
                        console.error('❌ Could not extract YouTube ID from:', videoUrl);
                    }
                } catch (e) {
                    console.error('❌ Error parsing YouTube URL:', e);
                }
            }
            // Handle Vimeo URLs
            else if (videoUrl.includes('vimeo.com')) {
                console.log('🎥 Detected Vimeo URL');
                try {
                    const match = videoUrl.match(/vimeo\.com\/(\d+)/);
                    if (match) {
                        const videoId = match[1];
                        embedUrl = `https://player.vimeo.com/video/${videoId}`;
                        console.log('✅ Vimeo ID:', videoId);
                    } else {
                        console.error('❌ Could not extract Vimeo ID from:', videoUrl);
                    }
                } catch (e) {
                    console.error('❌ Error parsing Vimeo URL:', e);
                }
            }
            // If direct embed URL provided
            else if (videoUrl.includes('/embed/')) {
                embedUrl = videoUrl;
                console.log('✅ Direct embed URL detected');
            }
            else {
                console.error('❌ Unsupported video URL format:', videoUrl);
            }

            if (embedUrl) {
                console.log('🚀 Setting iframe src to:', embedUrl);
                videoFrame.src = embedUrl;
                videoSection.style.display = 'block';
                videoSection.style.visibility = 'visible';
                videoSection.style.opacity = '1';
                if (videoSection.classList) videoSection.classList.add('active');
                console.log('✅ Video section now visible');
            } else {
                console.error('🔴 Could not generate embed URL from:', videoUrl);
                videoSection.style.display = 'none';
            }
            
            console.log('🎬 === displayVideo END ===');
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
                return `${formattedDate} | ${startTime} - ${formattedEndDate} | ${endTime}`;
            }
            
            return `${formattedDate} | ${startTime} - ${endTime}`;
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
                "image": event.cover_image || "/assest/img-4.webp"
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
            console.log('🎬 Initializing event page...');
            loadEventData();
        }
        
        // Handle both cases: if script loads before DOM is ready OR after
        if (document.readyState === 'loading') {
            window.addEventListener('DOMContentLoaded', initEventPage);
            console.log('📍 Document still loading, waiting for DOMContentLoaded');
        } else {
            console.log('📍 Document already loaded, initializing immediately');
            initEventPage();
        }
        
        // Listen for URL changes (when user clicks different event links)
        window.addEventListener('popstate', loadEventData);
        
        // Listen for language changes - reload event data with new language
        document.addEventListener('lakum-language-changed', (e) => {
            console.log('🌍 Language changed to:', e.detail?.lang);
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
                console.log('📍 URL changed, reloading event');
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
                langSwitcher.setAttribute('title', 'Language: العربية');
            }
        }

        // Update on page load
        updateLanguageSwitcher();

        // Watch for language changes
        const observer = new MutationObserver(() => {
            if (document.documentElement.lang) {
                updateLanguageSwitcher();
                updateSectionTitles(); // Update titles when language changes
            }
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['lang']
        });

        // Update section titles based on current language
        function updateSectionTitles() {
            const lang = document.documentElement.lang || 'en';
            const titles = {
                'about_this_event': lang === 'ar' ? '??? ??? ?????' : 'About This Event',
                'gallery': lang === 'ar' ? '??????' : 'Gallery',
                'event_video': lang === 'ar' ? '????? ?????' : 'Event Video'
            };

            // Update all section titles
            document.querySelectorAll('.event-section__title').forEach(el => {
                const text = el.textContent.trim();
                if (text === 'About This Event' || text === '??? ??? ?????') {
                    el.textContent = titles['about_this_event'];
                } else if (text === 'Gallery' || text === '??????') {
                    el.textContent = titles['gallery'];
                } else if (text === 'Event Video' || text === '????? ?????') {
                    el.textContent = titles['event_video'];
                }
            });
        }

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
            footer_copyright: "<?php echo t('footer_copyright', '� 2026 LAKUM Artspace. All rights reserved.'); ?>",
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

        // Also listen for storage changes (multi-tab sync)
        window.addEventListener('storage', (e) => {
            if (e.key === 'lakum_language' && e.newValue) {
                const lang = e.newValue;
                fetch(`/api/get-translations.php?lang=${lang}`)
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

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
    </body>

</html>



















