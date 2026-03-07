<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('spaces_page_title', 'Event Spaces for Rent in Riyadh | LAKUM Artspace Venues'); ?></title>
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
    <link rel="stylesheet" href="spaces.css">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>
<!-- Pricing is loaded dynamically via inline JavaScript below -->
<!-- <script src="assest/spaces-pricing-loader.js?v=1.0.0" defer></script> --></head>

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
        
        // Listen for language changes and reload page to apply RTL/LTR to navigation
        window.addEventListener('storage', (e) => {
            if (e.key === 'lakum_language' && e.newValue) {
                console.log('Language changed to:', e.newValue);
                // Reload page to apply language changes to navigation
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
    <section class="lakum-hero">
        <div class="lakum-hero__image-wrapper">
            <img src="heroImage/img-3.webp" alt="LAKUM Spaces" class="lakum-hero__image" loading="eager" fetchpriority="high" decoding="async" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo t('spaces_hero_title', 'Discover Our Dynamic'); ?> <span><?php echo t('spaces_hero_subtitle', ''); ?></span></h1>
            <ul class="lakum-spaces-hero__tags">
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_art', 'Art'); ?></li>
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_gallery', 'Gallery'); ?></li>
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_hub', 'Hub'); ?></li>
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_library', 'Library'); ?></li>
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_shop', 'Shop'); ?></li>
                <li class="lakum-spaces-hero__tag"><?php echo t('spaces_tag_cafe', 'Café'); ?></li>
            </ul>
        </div>
    </section>

    <!-- Venue Introduction -->
    <section class="lakum-spaces-intro">
        <div class="lakum-container">
            <h2 class="lakum-spaces-intro__title"><?php echo t('spaces_venue_title', 'LAKUM VENUE'); ?></h2>
            <div class="lakum-spaces-intro__content">
                <p><?php echo t('spaces_intro_p1', 'Lakum Artspace offers a versatile and elegantly designed venue, thoughtfully created to accommodate a wide range of events, from art exhibitions and product launches to private celebrations, talks, and cultural programs. The space unfolds across several distinctive areas, each with its own atmosphere and flexibility: Hall 1, a spacious gallery ideal for large-scale installations or receptions; Hall 2, perfectly suited for intimate showcases, creative workshops, and panel discussions; and the Mezzanine Floor, home to a welcoming Caf�, a curated Library, and the Lakum Shop, a retail corner that encourages relaxed breaks, quiet exploration, and moments of discovery.'); ?></p>

                <p><?php echo t('spaces_intro_p2', 'To complement every occasion, Lakum offers a suite of additional services designed to ensure a seamless and memorable experience. These include valet parking, assisted catering, professional security, and access to trusted photographers and videographers to capture each moment. The venue can also accommodate live music performances, adding an artistic and atmospheric touch to any gathering. Fully equipped with an in-house sound system, projectors, and a curated catalogue of furniture available for rental, Lakum allows every event to be tailored to its unique atmosphere and design vision.'); ?></p>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="lakum-spaces-facilities">
        <div class="lakum-container">
            <h2 class="lakum-spaces-facilities__title"><?php echo t('spaces_facilities_title', 'Our Facilities'); ?></h2>
            <div class="lakum-spaces-facilities__grid">
                <div class="lakum-facility-card" onclick="openFacilityPopup('hall1')">
                    <div class="lakum-facility-card__image">
                        <?php echo ImageHelper::render('HADAFCompany/Hall1.png', 'Hall 1', 'gallery'); ?>
                    </div>
                    <h3 class="lakum-facility-card__name"><?php echo t('spaces_hall1', 'Hall 1'); ?></h3>
                </div>
                <div class="lakum-facility-card" onclick="openFacilityPopup('hall2')">
                    <div class="lakum-facility-card__image">
                        <?php echo ImageHelper::render('HADAFCompany/Hall2.png', 'Hall 2', 'gallery'); ?>
                    </div>
                    <h3 class="lakum-facility-card__name"><?php echo t('spaces_hall2', 'Hall 2'); ?></h3>
                </div>
                <div class="lakum-facility-card" onclick="openFacilityPopup('cafe')">
                    <div class="lakum-facility-card__image">
                        <?php echo ImageHelper::render('HADAFCompany/Hall3.png', 'Café', 'gallery'); ?>
                    </div>
                    <h3 class="lakum-facility-card__name"><?php echo t('spaces_cafe', 'Café'); ?></h3>
                </div>
                <div class="lakum-facility-card" onclick="openFacilityPopup('meeting')">
                    <div class="lakum-facility-card__image">
                        <?php echo ImageHelper::render('HADAFCompany/Hall4.png', 'Meeting Room', 'gallery'); ?>
                    </div>
                    <h3 class="lakum-facility-card__name"><?php echo t('spaces_meeting_room', 'Meeting Room'); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <!-- LAKUM ArtSpaces Gallery -->
    <section class="lakum-gallery-section">
        <div class="lakum-container">
            <h2 class="lakum-gallery-section__title"><?php echo t('spaces_gallery_title', 'LAKUM ArtSpaces Gallery'); ?></h2>
        </div>
        <div class="lakum-gallery-carousel" id="galleryCarousel">
            <div class="lakum-gallery-track" id="galleryTrack">
                <?php
                $galleryImages = [
                    'gallery/img28.jpg', 'gallery/img30.jpg', 'gallery/img34.jpg', 'gallery/img38.jpg',
                    'gallery/img40.jpg', 'gallery/img44.jpg', 'gallery/img46.jpg', 'gallery/img50.jpg',
                    'gallery/img52.jpg', 'gallery/img56.jpg', 'gallery/img58.jpg', 'gallery/img6.jpg',
                    'gallery/img62.jpg', 'gallery/img64.jpg', 'gallery/img68.jpg', 'gallery/img70.jpg',
                    'gallery/img8.jpg'
                ];
                
                // Display images twice for infinite carousel effect
                foreach (array_merge($galleryImages, $galleryImages) as $image) {
                    echo '<div class="lakum-gallery-item">';
                    echo ImageHelper::render($image, 'LAKUM Gallery', 'gallery');
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Facility Popup -->
    <div class="lakum-facility-popup" id="facilityPopup">
        <div class="lakum-facility-popup__overlay" onclick="closeFacilityPopup()"></div>
        <div class="lakum-facility-popup__content">
            <button class="lakum-facility-popup__close" onclick="closeFacilityPopup()">
                <i class="ri-close-line"></i>
            </button>
            <h3 class="lakum-facility-popup__title" id="facilityPopupTitle"></h3>
            <div class="lakum-facility-popup__slider">
                <button class="lakum-facility-popup__nav lakum-facility-popup__nav--prev" onclick="prevFacilityImage()">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <div class="lakum-facility-popup__image-wrapper">
                    <img id="facilityPopupImage" src="" alt="Facility Image">
                </div>
                <button class="lakum-facility-popup__nav lakum-facility-popup__nav--next" onclick="nextFacilityImage()">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
            <div class="lakum-facility-popup__counter" id="facilityPopupCounter"></div>
        </div>
    </div>

    <!-- Floor Map Section -->
    <section class="lakum-spaces-floor">
        <div class="lakum-container">
            <h2 class="lakum-spaces-floor__title"><?php echo t('spaces_floor_title', 'Our Floor Maps with Measurements'); ?></h2>
            <div class="lakum-spaces-floor__grid">
                <!-- Ground Floor Row -->
                <div class="lakum-spaces-floor__row">
                    <h3 class="lakum-spaces-floor__row-title"><?php echo t('spaces_ground_floor', 'LAKUM ARTSPACE | GROUND FLOOR MAP'); ?></h3>
                    <div class="lakum-spaces-floor__images">
                        <div class="lakum-spaces-floor__image">
                            <img src="assest/floor-plan-1.png" alt="Ground Floor Map">
                        </div>
                        <div class="lakum-spaces-floor__image">
                            <img src="assest/floor-plan3d-1.jpg" alt="Ground Floor Map">
                        </div>
                    </div>
                </div>

                <!-- Mezzanine Floor Row -->
                <div class="lakum-spaces-floor__row">
                    <h3 class="lakum-spaces-floor__row-title"><?php echo t('spaces_mezzanine_floor', 'LAKUM ARTSPACE | MEZZANINE FLOOR MAP'); ?></h3>
                    <div class="lakum-spaces-floor__images">
                        <div class="lakum-spaces-floor__image">
                            <img src="assest/floor-plan-2.png" alt="Mezzanine Floor Map">
                        </div>
                        <div class="lakum-spaces-floor__image">
                            <img src="assest/floor-plan3d-2.jpg" alt="Mezzanine Floor Map">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Past Exhibitions Gallery -->
    <section class="lakum-spaces-exhibitions">
        <div class="lakum-container">
            <h2 class="lakum-spaces-exhibitions__title"><?php echo t('spaces_past_exhibitions', 'Past Exhibitions'); ?></h2>
        </div>
        <div class="lakum-spaces-exhibitions__carousel" id="pastExhibitionsCarousel">
            <div class="lakum-spaces-exhibitions__track" id="pastExhibitionsTrack">
                <!-- Dynamic content loaded by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="lakum-spaces-pricing">
        <div class="lakum-container">
            <h2 class="lakum-spaces-pricing__title"><?php echo t('spaces_pricing_title', 'Spaces Pricing'); ?></h2>

            <div class="lakum-spaces-pricing__grid" id="pricingGrid">
                <!-- Pricing will be loaded dynamically from API -->
            </div>
        </div>
    </section>

    <script>
        // Fetch and render pricing based on current language
        (function() {
            const pricingGrid = document.getElementById('pricingGrid');
            
            if (!pricingGrid) return;

            // Get current language - use window.LAKUM_LANG set by PHP
            function getCurrentLang() {
                return window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            }

            // Load and render pricing
            function loadPricing() {
                const currentLang = getCurrentLang();
                
                fetch('api/get_pricing.php')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success || !data.data || data.data.length === 0) {
                            console.warn('No pricing data available');
                            return;
                        }

                        // Clear existing content
                        pricingGrid.innerHTML = '';

                        // Render each pricing item
                        data.data.forEach((item, index) => {
                            const pricingCard = createPricingCard(item, currentLang, index + 1);
                            pricingGrid.appendChild(pricingCard);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching pricing:', error);
                        pricingGrid.innerHTML = '<p>Unable to load pricing information. Please try again later.</p>';
                    });
            }

            // Create pricing card element
            function getDurationLabel(priceUnit) {
                // Extract duration from price unit (e.g., "SAR/day" -> "day", "ريال سعودي / ساعة" -> "hour")
                if (!priceUnit) return '';
                
                // English: SAR/day, SAR/hour, SAR
                if (priceUnit.includes('/day')) return 'per day';
                if (priceUnit.includes('/hour')) return 'per hour';
                if (priceUnit === 'SAR' || priceUnit === 'ريال سعودي') return '';
                
                // Arabic: ريال سعودي / يوم, ريال سعودي / ساعة
                if (priceUnit.includes('يوم')) return 'لكل يوم';
                if (priceUnit.includes('ساعة')) return 'لكل ساعة';
                
                return '';
            }

            function createPricingCard(item, lang, index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'pricing-card-wrapper';
                wrapper.setAttribute('data-pricing-id', item.id);

                // Get language-specific content
                const name = lang === 'ar' ? (item.name_ar || item.name_en) : (item.name_en || item.name_ar);
                const description = lang === 'ar' ? (item.description_ar || item.description_en) : (item.description_en || item.description_ar);
                const priceUnit = lang === 'ar' ? (item.price_unit_ar || item.price_unit) : (item.price_unit || 'SAR');
                const vatNote = lang === 'ar' ? (item.vat_note_ar || item.vat_note) : (item.vat_note || '*(excluding VAT)');
                const currencyImage = item.currency_image || 'RS/OIP.png';

                // Parse price_sec for secondary pricing (e.g., "Hall 1: 1,000 SAR/hour")
                const hasSecondaryPrice = item.price_sec && item.price_sec.trim() !== '';
                
                // Create the card HTML
                wrapper.innerHTML = `
                    <details class="pricing-accordion">
                        <summary class="pricing-accordion__header">
                            <div class="pricing-accordion__info">
                                <h3 class="pricing-accordion__name">${escapeHtml(name)}</h3>
                                <div class="pricing-accordion__price${hasSecondaryPrice ? ' pricing-accordion__price--multi' : ''}">
                                    ${hasSecondaryPrice ? 
                                        `<div>${escapeHtml(item.price_sec)}</div>` :
                                        `<span class="pricing-accordion__amount">${formatPrice(item.price)}</span>
                                         <div class="pricing-accordion__currency-wrapper">
                                             ${currencyImage ? `<img src="${currencyImage}" alt="Currency" class="pricing-accordion__currency-image" loading="lazy">` : ''}
                                             <span class="pricing-accordion__currency">${getDurationLabel(priceUnit)}</span>
                                         </div>`
                                    }
                                </div>
                                <span class="pricing-accordion__vat">${escapeHtml(vatNote)}</span>
                            </div>
                            <span class="pricing-accordion__icon"></span>
                        </summary>
                        <div class="pricing-accordion__content">
                            ${description ? `<p>${escapeHtml(description)}</p>` : ''}
                            ${item.content ? `<div>${item.content}</div>` : ''}
                        </div>
                    </details>
                    <div class="pricing-button-fixed">
                        <a href="#form" class="lakum-btn lakum-btn--primary">${lang === 'ar' ? 'احجز الآن' : 'Book Now'}</a>
                    </div>
                `;

                return wrapper;
            }

            // Utility function to escape HTML
            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            // Format price with thousands separator
            function formatPrice(price) {
                if (!price) return '0';
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            // Initial load
            loadPricing();

            // Listen for language changes
            window.addEventListener('storage', (e) => {
                if (e.key === 'lakum_language' && e.newValue) {
                    loadPricing();
                }
            });
        })();
    </script>

    <!-- Book Your Space Section -->
    <section class="lakum-spaces-booking" id="form">
        <div class="lakum-container">
            <div class="lakum-spaces-booking__content">
                <h2 class="lakum-spaces-booking__title"><?php echo t('spaces_booking_title', 'Book Your Space'); ?></h2>
                <p class="lakum-spaces-booking__text"><?php echo t('spaces_booking_text', 'Ready to host your next event at LAKUM? Click below to fill out our booking form and our team will get back to you shortly.'); ?></p>
                <a href="https://form.typeform.com/to/d6ltE0yW" target="_blank" class="lakum-btn lakum-btn--primary lakum-btn--large" data-link-type="booking">
                    <i class="ri-external-link-line"></i> <?php echo t('spaces_open_booking_form', 'Open Booking Form'); ?></a>
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

                <div class="lakum-footer__social"><h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4><div class="lakum-footer__social-links"><a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram"><i class="ri-instagram-fill"></i></a><a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter"><i class="ri-twitter-x-fill"></i></a></div></div>
            </div>

            <div class="lakum-footer__bottom">
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright', '� 2025 - 2027 LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

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

    <script src="assest/navbar-mobile-toggle.js" defer></script>
    <script src="assest/fab-button.js" defer></script>
    <script src="js/LanguageManager.js?v=1.0.0" defer></script>
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
        // Static background image for hero section
        // Background is now set via CSS

        // Facility Popup Data
        const facilityData = {
            hall1: {
                name: 'Hall 1',
                images: [
                    'HadafCompany/hall1.png'
                ]
            },
            hall2: {
                name: 'Hall 2',
                images: [
                    'HadafCompany/hall2.png'
                ]
            },
            cafe: {
                name: 'Caf�',
                images: [
                    'HadafCompany/hall3.png'
                ]
            },
            meeting: {
                name: 'Meeting Room',
                images: [
                    'HadafCompany/hall4.png'
                ]
            }
        };

        let currentFacility = null;
        let currentFacilityIndex = 0;

        function openFacilityPopup(facilityKey) {
            currentFacility = facilityData[facilityKey];
            currentFacilityIndex = 0;
            document.getElementById('facilityPopupTitle').textContent = currentFacility.name;
            updateFacilityImage();
            document.getElementById('facilityPopup').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeFacilityPopup() {
            document.getElementById('facilityPopup').classList.remove('active');
            document.body.style.overflow = '';
        }

        function nextFacilityImage() {
            if (currentFacility) {
                currentFacilityIndex = (currentFacilityIndex + 1) % currentFacility.images.length;
                updateFacilityImage();
            }
        }

        function prevFacilityImage() {
            if (currentFacility) {
                currentFacilityIndex = (currentFacilityIndex - 1 + currentFacility.images.length) % currentFacility.images.length;
                updateFacilityImage();
            }
        }

        function updateFacilityImage() {
            if (currentFacility) {
                document.getElementById('facilityPopupImage').src = currentFacility.images[currentFacilityIndex];
                document.getElementById('facilityPopupCounter').textContent = `${currentFacilityIndex + 1} / ${currentFacility.images.length}`;
            }
        }

        // Load Past Exhibitions Dynamically
        async function loadPastExhibitions() {
            try {
                // Use language from PHP (respects URL parameter ?lang=en or ?lang=ar)
                // Falls back to LanguageManager if window.LAKUM_LANG not set
                const lang = window.LAKUM_LANG || LanguageManager.getLanguage() || 'en';
                const response = await fetch(`api/get_events.php?type=all&limit=1000&lang=${lang}`);
                const data = await response.json();
                
                if (!data.success || !data.data || !Array.isArray(data.data)) {
                    console.error('Failed to load exhibitions');
                    return;
                }

                const now = new Date();
                now.setHours(0, 0, 0, 0);
                
                // Filter past events and sort by date (newest first)
                const pastEvents = data.data.filter(e => {
                    const eventDate = new Date(e.event_date);
                    eventDate.setHours(0, 0, 0, 0);
                    return eventDate < now;
                }).sort((a, b) => new Date(b.event_date) - new Date(a.event_date));

                const track = document.getElementById('pastExhibitionsTrack');
                if (!track) return;

                track.innerHTML = '';

                if (pastEvents.length === 0) {
                    track.innerHTML = '<p style="text-align: center; padding: 40px; color: #999; grid-column: 1/-1;">No past exhibitions</p>';
                    return;
                }

                // Create slides for past events (duplicate for carousel effect)
                const slidesToShow = Math.min(pastEvents.length, 12);
                const eventsToDisplay = pastEvents.slice(0, slidesToShow);
                
                // Add slides twice for infinite carousel effect
                [...eventsToDisplay, ...eventsToDisplay].forEach(event => {
                    const eventDate = new Date(event.event_date);
                    const month = eventDate.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }).toUpperCase();
                    
                    const slide = document.createElement('div');
                    slide.className = 'lakum-spaces-exhibition-slide';
                    slide.setAttribute('data-exhibition-id', event.id);
                    slide.innerHTML = `
                        <div class="lakum-spaces-exhibition-slide__image">
                            <img src="${event.cover_image || 'assest/img-4.png'}" alt="${event.title}" draggable="false" loading="lazy">
                        </div>
                        <div class="lakum-spaces-exhibition-slide__content">
                            <h3 class="lakum-spaces-exhibition-slide__title">${event.title}</h3>
                            <span class="lakum-spaces-exhibition-slide__date">${month}</span>
                        </div>
                    `;
                    track.appendChild(slide);
                });

            } catch (error) {
                console.error('Error loading past exhibitions:', error);
            }
        }

        // Load past exhibitions when page loads
        function initSpacesPage() {
            // LanguageManager is guaranteed to be ready by now
            // because it's loaded with defer and this runs on DOMContentLoaded
            if (typeof LanguageManager === 'undefined') {
                console.error('LanguageManager failed to load');
                return;
            }
            loadPastExhibitions();
        }
        
        // Wait for LanguageManager to be fully initialized
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSpacesPage);
        } else {
            // DOM already loaded, run immediately
            initSpacesPage();
        }

        // Keyboard navigation for facility popup
        document.addEventListener('keydown', (e) => {
            const popup = document.getElementById('facilityPopup');
            if (popup.classList.contains('active')) {
                if (e.key === 'Escape') closeFacilityPopup();
                if (e.key === 'ArrowRight') nextFacilityImage();
                if (e.key === 'ArrowLeft') prevFacilityImage();
            }
        });

        // ===== INFINITE GALLERY CAROUSEL =====
        (function() {
            const carousel = document.getElementById('galleryCarousel');
            const track = document.getElementById('galleryTrack');

            if (!carousel || !track) return;

            let scrollPosition = 0;
            let animationId = null;
            let isHovering = false;
            let isDragging = false;
            let startX = 0;
            let scrollLeft = 0;
            const scrollSpeed = 1.2; // pixels per frame (increased from 0.5)

            // Calculate when to reset (halfway through duplicated content)
            const items = track.querySelectorAll('.lakum-gallery-item');
            const totalItems = items.length;
            const halfItems = totalItems / 2;

            function animate() {
                // Auto-scroll only when not hovering and not dragging
                if (!isHovering && !isDragging) {
                    scrollPosition += scrollSpeed;

                    // Get the width of one set of images
                    const firstItem = items[0];
                    if (firstItem) {
                        const itemWidth = firstItem.offsetWidth;
                        const gap = parseFloat(getComputedStyle(track).gap) || 24;
                        const setWidth = (itemWidth + gap) * halfItems;

                        // Reset position seamlessly when reaching halfway
                        if (scrollPosition >= setWidth) {
                            scrollPosition = 0;
                        }
                    }
                }

                // Always update transform (so manual scrolling is visible)
                track.style.transform = `translateX(-${scrollPosition}px)`;

                animationId = requestAnimationFrame(animate);
            }

            // Start animation
            animate();

            // Pause auto-scroll on hover (but keep animation running for manual scroll)
            carousel.addEventListener('mouseenter', () => {
                isHovering = true;
            });

            carousel.addEventListener('mouseleave', () => {
                isHovering = false;
            });

            // Drag functionality
            carousel.addEventListener('mousedown', (e) => {
                isDragging = true;
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = scrollPosition;
                carousel.style.cursor = 'grabbing';
            });

            carousel.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                const walk = (startX - x) * 2; // Multiply for faster drag
                scrollPosition = scrollLeft + walk;

                // Ensure we stay within bounds with seamless loop
                const firstItem = items[0];
                if (firstItem) {
                    const itemWidth = firstItem.offsetWidth;
                    const gap = parseFloat(getComputedStyle(track).gap) || 24;
                    const setWidth = (itemWidth + gap) * halfItems;

                    // Wrap around seamlessly
                    if (scrollPosition < 0) scrollPosition = setWidth + scrollPosition;
                    if (scrollPosition >= setWidth) scrollPosition = scrollPosition - setWidth;
                }
            });

            carousel.addEventListener('mouseup', () => {
                isDragging = false;
                carousel.style.cursor = 'grab';
            });

            carousel.addEventListener('mouseleave', () => {
                if (isDragging) {
                    isDragging = false;
                    carousel.style.cursor = 'grab';
                }
            });

            // Touch support for mobile
            let touchStartX = 0;
            let touchScrollLeft = 0;

            carousel.addEventListener('touchstart', (e) => {
                isDragging = true;
                isHovering = true; // Pause auto-scroll on touch
                touchStartX = e.touches[0].pageX;
                touchScrollLeft = scrollPosition;
            });

            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const x = e.touches[0].pageX;
                const walk = (touchStartX - x) * 2;
                scrollPosition = touchScrollLeft + walk;

                const firstItem = items[0];
                if (firstItem) {
                    const itemWidth = firstItem.offsetWidth;
                    const gap = parseFloat(getComputedStyle(track).gap) || 24;
                    const setWidth = (itemWidth + gap) * halfItems;

                    if (scrollPosition < 0) scrollPosition = setWidth + scrollPosition;
                    if (scrollPosition >= setWidth) scrollPosition = scrollPosition - setWidth;
                }
            });

            carousel.addEventListener('touchend', () => {
                isDragging = false;
                isHovering = false; // Resume auto-scroll after touch
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            });
        })();

        // Pricing Cards Height Equalization (Per Row)
        const pricingWrappers = document.querySelectorAll('.pricing-card-wrapper');

        if (pricingWrappers.length > 0) {
            function equalizeHeights() {
                // Wait for animation to complete
                setTimeout(() => {
                    // Group cards by row based on their Y position
                    const rows = new Map();

                    pricingWrappers.forEach(wrapper => {
                        const rect = wrapper.getBoundingClientRect();
                        const rowKey = Math.round(rect.top); // Group by Y position

                        if (!rows.has(rowKey)) {
                            rows.set(rowKey, []);
                        }
                        rows.get(rowKey).push(wrapper);
                    });

                    // Equalize height within each row
                    rows.forEach(rowWrappers => {
                        let maxAccordionHeight = 0;
                        let hasOpenAccordion = false;
                        const openWrappers = [];

                        // Find open accordions in this row and get max height
                        rowWrappers.forEach(wrapper => {
                            const accordion = wrapper.querySelector('.pricing-accordion');
                            if (accordion && accordion.hasAttribute('open')) {
                                hasOpenAccordion = true;
                                openWrappers.push(wrapper);

                                // Measure accordion height only (not including button)
                                const accordionHeight = accordion.offsetHeight;

                                if (accordionHeight > maxAccordionHeight) {
                                    maxAccordionHeight = accordionHeight;
                                }
                            }
                        });

                        // Apply heights with smooth animation for this row
                        if (hasOpenAccordion && maxAccordionHeight > 0) {
                            // Set all open accordions in this row to max height
                            openWrappers.forEach(wrapper => {
                                const accordion = wrapper.querySelector('.pricing-accordion');
                                if (accordion) {
                                    accordion.style.height = maxAccordionHeight + 'px';
                                    accordion.style.transition = 'height 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                                }
                            });

                            // Reset closed accordions in this row to auto height
                            rowWrappers.forEach(wrapper => {
                                const accordion = wrapper.querySelector('.pricing-accordion');
                                if (accordion && !accordion.hasAttribute('open')) {
                                    accordion.style.height = 'auto';
                                }
                            });
                        } else {
                            // Reset all heights in this row when all are closed
                            rowWrappers.forEach(wrapper => {
                                const accordion = wrapper.querySelector('.pricing-accordion');
                                if (accordion) {
                                    accordion.style.height = 'auto';
                                }
                            });
                        }
                    });
                }, 50);
            }

            // Add event listeners to all accordions
            pricingWrappers.forEach(wrapper => {
                const accordion = wrapper.querySelector('.pricing-accordion');
                if (accordion) {
                    accordion.addEventListener('toggle', equalizeHeights);
                }
            });

            // Re-equalize on window resize (to handle row changes)
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(equalizeHeights, 100);
            });

            // Initial check
            equalizeHeights();
        }

        // Translate Past Exhibitions if needed
        if (typeof translationHelper !== 'undefined' && translationHelper.needsTranslation()) {
            const exhibitions = [{
                "id": "27",
                "title": "YSL",
                "slug": "ysl",
                "cover_image": "uploads\/covers\/cover_1770028987_69807fbb0eae2.webp",
                "event_date": "2025-09-18"
            }, {
                "id": "34",
                "title": "Maysar",
                "slug": "maysar",
                "cover_image": "uploads\/covers\/cover_1769693914_697b62daed1f1.webp",
                "event_date": "2025-09-13"
            }, {
                "id": "17",
                "title": "Eyewa",
                "slug": "eyewa",
                "cover_image": "uploads\/covers\/cover_1770029247_698080bfaef36.webp",
                "event_date": "2025-06-19"
            }, {
                "id": "20",
                "title": "Anastasia Beverly Hills",
                "slug": "anastasia-beverly-hills",
                "cover_image": "uploads\/covers\/cover_1769691296_697b58a04538c.webp",
                "event_date": "2025-05-28"
            }, {
                "id": "19",
                "title": "Dior",
                "slug": "dior",
                "cover_image": "uploads\/covers\/event_11_1765952986.jpg",
                "event_date": "2025-05-12"
            }, {
                "id": "12",
                "title": "Chalhoub Group",
                "slug": "chalhoub-group",
                "cover_image": "uploads\/covers\/event_4_1765952986.jpg",
                "event_date": "2025-05-11"
            }];
            const exhibitionSlides = document.querySelectorAll('.lakum-spaces-exhibition-slide');

            if (typeof translationHelper !== 'undefined') {
                translationHelper.translateArrayProgressive(
                    exhibitions, ['title'],
                    (translated, index) => {
                        const slide = exhibitionSlides[index];
                        if (slide) {
                            const titleEl = slide.querySelector('.lakum-spaces-exhibition-slide__title');
                            if (titleEl) titleEl.textContent = translated.title;
                        }
                    },
                    'ar'
                );
            }
        }

        // ===== PAST EXHIBITIONS CAROUSEL (Like Gallery, No Auto-Scroll) =====
        (function() {
            const carousel = document.getElementById('pastExhibitionsCarousel');
            const track = document.getElementById('pastExhibitionsTrack');

            if (!carousel || !track) return;

            let scrollPosition = 0;
            let isDragging = false;
            let startX = 0;
            let scrollLeft = 0;
            let hasMoved = false; // Track if user actually dragged

            // Calculate when to reset (halfway through duplicated content)
            const items = track.querySelectorAll('.lakum-spaces-exhibition-slide');
            const totalItems = items.length;
            const halfItems = totalItems / 2;

            function updateTransform() {
                track.style.transform = `translateX(-${scrollPosition}px)`;
            }

            // Drag functionality
            carousel.addEventListener('mousedown', (e) => {
                // Don't start drag if clicking on a link or button
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;

                isDragging = true;
                hasMoved = false;
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = scrollPosition;
                carousel.style.cursor = 'grabbing';
            });

            carousel.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                const walk = (startX - x) * 2; // 2x scroll speed
                scrollPosition = scrollLeft + walk;

                // Mark as moved if dragged more than 5px
                if (Math.abs(walk) > 5) {
                    hasMoved = true;
                }

                // Ensure we stay within bounds with seamless loop
                const firstItem = items[0];
                if (firstItem) {
                    const itemWidth = firstItem.offsetWidth;
                    const gap = parseFloat(getComputedStyle(track).gap) || 24;
                    const setWidth = (itemWidth + gap) * halfItems;

                    // Wrap around seamlessly
                    if (scrollPosition < 0) scrollPosition = setWidth + scrollPosition;
                    if (scrollPosition >= setWidth) scrollPosition = scrollPosition - setWidth;
                }

                updateTransform();
            });

            carousel.addEventListener('mouseup', () => {
                isDragging = false;
                carousel.style.cursor = 'grab';
            });

            carousel.addEventListener('mouseleave', () => {
                if (isDragging) {
                    isDragging = false;
                    carousel.style.cursor = 'grab';
                }
            });

            // Touch support for mobile
            let touchStartX = 0;
            let touchScrollLeft = 0;

            carousel.addEventListener('touchstart', (e) => {
                isDragging = true;
                hasMoved = false;
                touchStartX = e.touches[0].pageX;
                touchScrollLeft = scrollPosition;
            });

            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const x = e.touches[0].pageX;
                const walk = (touchStartX - x) * 2;
                scrollPosition = touchScrollLeft + walk;

                if (Math.abs(walk) > 5) {
                    hasMoved = true;
                }

                const firstItem = items[0];
                if (firstItem) {
                    const itemWidth = firstItem.offsetWidth;
                    const gap = parseFloat(getComputedStyle(track).gap) || 24;
                    const setWidth = (itemWidth + gap) * halfItems;

                    if (scrollPosition < 0) scrollPosition = setWidth + scrollPosition;
                    if (scrollPosition >= setWidth) scrollPosition = scrollPosition - setWidth;
                }

                updateTransform();
            });

            carousel.addEventListener('touchend', () => {
                isDragging = false;
            });

            // Click handling - navigate to event if not dragged
            items.forEach((item, index) => {
                item.addEventListener('click', (e) => {
                    console.log('Click detected on item', index);
                    console.log('hasMoved:', hasMoved);
                    console.log('data-slug:', item.dataset.slug);

                    if (!hasMoved) {
                        e.preventDefault();
                        e.stopPropagation();
                        const slug = item.dataset.slug;
                        const eventUrl = `/event/${slug}`;
                        console.log('Navigating to:', eventUrl);
                        window.location.href = eventUrl;
                    } else {
                        console.log('Navigation prevented - user dragged');
                    }
                });

                // Set cursor
                item.style.cursor = 'pointer';
            });

            // Reset hasMoved on mouseup/touchend
            carousel.addEventListener('mouseup', () => {
                setTimeout(() => {
                    console.log('Resetting hasMoved to false');
                    hasMoved = false;
                }, 100);
            });

            carousel.addEventListener('touchend', () => {
                setTimeout(() => {
                    hasMoved = false;
                }, 100);
            });

            // Set initial cursor
            carousel.style.cursor = 'grab';
        })();

        // Load Spaces Pricing from API
        function loadSpacesPricingFromAPI() {
            (async function loadSpacesPricing() {
                try {
                    console.log('Loading pricing from API...');
                    // Get current language
                    const urlParams = new URLSearchParams(window.location.search);
                    const lang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
                    
                    // Add cache-busting parameter to force fresh data
                    const timestamp = new Date().getTime();
                    const apiUrl = `api/get_pricing.php?lang=${lang}&t=${timestamp}`;
                    console.log('API URL:', apiUrl);
                    
                    const response = await fetch(apiUrl);
                    
                    console.log('Response status:', response.status);
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Error:', response.status, errorText);
                        console.log('Keeping hardcoded pricing');
                        return; // Keep hardcoded pricing
                    }
                    
                    const data = await response.json();
                    console.log('Pricing data received:', data);

                    if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                        const pricingGrid = document.getElementById('pricingGrid');
                        if (!pricingGrid) {
                            console.warn('pricingGrid element not found');
                            return;
                        }

                        console.log('Clearing hardcoded pricing and loading from API...');
                        // Clear existing pricing cards
                        pricingGrid.innerHTML = '';

                        // Render pricing cards from API
                        data.data.forEach(item => {
                            const card = createPricingCard(item);
                            pricingGrid.appendChild(card);
                        });

                        console.log('Pricing loaded successfully:', data.data.length, 'items from', data.source);
                        
                        // Re-equalize pricing card heights after loading
                        setTimeout(() => {
                            const pricingWrappers = document.querySelectorAll('.pricing-card-wrapper');
                            if (pricingWrappers.length > 0) {
                                function equalizeHeights() {
                                    setTimeout(() => {
                                        const rows = new Map();
                                        pricingWrappers.forEach(wrapper => {
                                            const rect = wrapper.getBoundingClientRect();
                                            const rowKey = Math.round(rect.top);
                                            if (!rows.has(rowKey)) {
                                                rows.set(rowKey, []);
                                            }
                                            rows.get(rowKey).push(wrapper);
                                        });

                                        rows.forEach(rowWrappers => {
                                            let maxAccordionHeight = 0;
                                            let hasOpenAccordion = false;
                                            const openWrappers = [];

                                            rowWrappers.forEach(wrapper => {
                                                const accordion = wrapper.querySelector('.pricing-accordion');
                                                if (accordion && accordion.hasAttribute('open')) {
                                                    hasOpenAccordion = true;
                                                    openWrappers.push(wrapper);
                                                    const accordionHeight = accordion.offsetHeight;
                                                    if (accordionHeight > maxAccordionHeight) {
                                                        maxAccordionHeight = accordionHeight;
                                                    }
                                                }
                                            });

                                            if (hasOpenAccordion && maxAccordionHeight > 0) {
                                                openWrappers.forEach(wrapper => {
                                                    const accordion = wrapper.querySelector('.pricing-accordion');
                                                    if (accordion) {
                                                        accordion.style.height = maxAccordionHeight + 'px';
                                                        accordion.style.transition = 'height 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                                                    }
                                                });

                                                rowWrappers.forEach(wrapper => {
                                                    const accordion = wrapper.querySelector('.pricing-accordion');
                                                    if (accordion && !accordion.hasAttribute('open')) {
                                                        accordion.style.height = 'auto';
                                                    }
                                                });
                                            } else {
                                                rowWrappers.forEach(wrapper => {
                                                    const accordion = wrapper.querySelector('.pricing-accordion');
                                                    if (accordion) {
                                                        accordion.style.height = 'auto';
                                                    }
                                                });
                                            }
                                        });
                                    }, 50);
                                }

                                pricingWrappers.forEach(wrapper => {
                                    const accordion = wrapper.querySelector('.pricing-accordion');
                                    if (accordion) {
                                        accordion.addEventListener('toggle', equalizeHeights);
                                    }
                                });

                                let resizeTimer;
                                window.addEventListener('resize', () => {
                                    clearTimeout(resizeTimer);
                                    resizeTimer = setTimeout(equalizeHeights, 100);
                                });

                                equalizeHeights();
                            }
                        }, 100);
                    } else {
                        console.warn('Invalid pricing data format or empty:', data);
                        console.log('Keeping hardcoded pricing');
                    }
                } catch (error) {
                    console.error('Error loading pricing:', error);
                    console.log('Keeping hardcoded pricing');
                    // Fallback to hardcoded pricing - keep existing cards
                }
            })();
        }

        // Wait for translationHelper to be defined, then load pricing
        if (typeof translationHelper !== 'undefined') {
            loadSpacesPricingFromAPI();
        } else {
            // Wait for translationHelper to load
            window.addEventListener('load', () => {
                setTimeout(loadSpacesPricingFromAPI, 100);
            });
        }

        // Track current language for change detection
        let currentPricingLang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('lakum_language') || 'en';

        // Listen for language changes and reload pricing
        document.addEventListener('languageChanged', function() {
            console.log('Language changed, reloading pricing...');
            loadSpacesPricingFromAPI();
        });
        
        // Also listen for storage changes (in case language is changed in another tab)
        window.addEventListener('storage', function(e) {
            if (e.key === 'lakum_language') {
                console.log('Language changed in another tab, reloading pricing...');
                loadSpacesPricingFromAPI();
            }
        });

        // Watch for URL parameter changes (language switcher)
        const pricingObserver = new MutationObserver(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const newLang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
            if (newLang !== currentPricingLang) {
                console.log('Pricing language changed from', currentPricingLang, 'to', newLang);
                currentPricingLang = newLang;
                loadSpacesPricingFromAPI();
            }
        });

        pricingObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['lang']
        });

        function createPricingCard(item) {
            const wrapper = document.createElement('div');
            wrapper.className = 'pricing-card-wrapper';
            wrapper.setAttribute('data-pricing-id', item.id);

            // Get current language from URL or localStorage
            const urlParams = new URLSearchParams(window.location.search);
            const lang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
            
            // Get bilingual name and content
            const name = lang === 'ar' ? (item.name_ar || item.title) : (item.name_en || item.title);
            const content = lang === 'ar' ? item.description_ar : item.description_en;
            const displayContent = content || item.content || '';
            
            // Get bilingual price unit and VAT note
            const priceUnit = lang === 'ar' ? (item.price_unit_ar || '?.?') : (item.price_unit || 'SAR');
            const vatNote = lang === 'ar' ? (item.vat_note_ar || '*(??? ???? ???????)') : (item.vat_note || '*(excluding VAT)');
            
            // Get "Book Now" button text based on language
            const bookNowText = lang === 'ar' ? '???? ????' : 'Book Now';
            
            // Format price
            let priceHTML = '';
            if (item.price_sec) {
                // Multi-line price (like Hourly Rate)
                priceHTML = `<div class="pricing-accordion__price pricing-accordion__price--multi">
                    <div>${item.price_sec}</div>
                </div>`;
            } else if (item.price && priceUnit) {
                // Standard price
                const formattedPrice = parseInt(item.price).toLocaleString('en-US');
                const currencyImage = item.currency_image || 'RS/OIP.png';
                priceHTML = `<div class="pricing-accordion__price">
                    <span class="pricing-accordion__amount">${formattedPrice}</span>
                    <div class="pricing-accordion__currency-wrapper">
                        ${currencyImage ? `<img src="${currencyImage}" alt="Currency" class="pricing-accordion__currency-image" loading="lazy">` : ''}
                        <span class="pricing-accordion__currency">${getDurationLabel(priceUnit)}</span>
                    </div>
                </div>`;
            }
            
            // Add RTL direction for Arabic
            const contentDir = lang === 'ar' ? 'dir="rtl"' : '';

            wrapper.innerHTML = `
                <details class="pricing-accordion">
                    <summary class="pricing-accordion__header">
                        <div class="pricing-accordion__info">
                            <h3 class="pricing-accordion__name" ${contentDir}>${name}</h3>
                            ${priceHTML}
                            <span class="pricing-accordion__vat">${vatNote}</span>
                        </div>
                        <span class="pricing-accordion__icon"></span>
                    </summary>
                    <div class="pricing-accordion__content" ${contentDir}>
                        ${displayContent}
                    </div>
                </details>
                <div class="pricing-button-fixed">
                    <a href="#form" class="lakum-btn lakum-btn--primary">${bookNowText}</a>
                </div>
            `;

            return wrapper;
        }

        // Translate Pricing if needed
        // NOTE: Pricing is now loaded dynamically with bilingual support from the database
        // The old translation code has been removed as it's no longer needed
    </script>

    <script>
        // Listen for dynamic content loaded event and render pricing based on language
        document.addEventListener('lakum-content-loaded', (e) => {
            if (e.detail.contentType === 'pricing') {
                const pricingItems = e.detail.content;
                const pricingGrid = document.getElementById('pricingGrid');
                
                if (!pricingGrid || !pricingItems || pricingItems.length === 0) return;
                
                // Clear existing content
                pricingGrid.innerHTML = '';
                
                // Render pricing items
                pricingItems.forEach((item) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'pricing-card-wrapper';
                    wrapper.setAttribute('data-pricing-id', item.id);
                    
                    wrapper.innerHTML = `
                        <details class="pricing-accordion">
                            <summary class="pricing-accordion__header">
                                <div class="pricing-accordion__title-wrapper">
                                    <h3 class="pricing-accordion__title">${item.title}</h3>
                                    <span class="pricing-accordion__price">${item.price ? item.price + ' SAR' : 'Contact'}</span>
                                </div>
                                <i class="ri-add-line pricing-accordion__icon"></i>
                            </summary>
                            <div class="pricing-accordion__content">
                                ${item.content || ''}
                            </div>
                        </details>
                    `;
                    
                    pricingGrid.appendChild(wrapper);
                });
            }
        });
    </script>

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>

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






















