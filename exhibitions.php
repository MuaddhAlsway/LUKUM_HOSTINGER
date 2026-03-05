<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('exhibitions_page_title', 'Art Exhibitions in Riyadh | LAKUM Artspace Gallery'); ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">
    <!-- Preload LCP image (hero) - Mobile-first (400w) -->
    <!-- Preload LCP image (hero) - Mobile-first with responsive variants -->
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
    <link rel="stylesheet" href="critical-inline.css">
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    <link rel="stylesheet" href="assest/mobile-menu.css">
    <link rel="stylesheet" href="assest/fab-button.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="Home.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="rtl.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="fonts/greta-arabic.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="exhibitions.css" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="Home.min.css">
        <link rel="stylesheet" href="rtl.css">
        <link rel="stylesheet" href="fonts/greta-arabic.css">
        <link rel="stylesheet" href="exhibitions.css">
    </noscript>
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/exhibitions.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/exhibitions.php?lang=ar" />
<script src="assest/static-json-translator.js?v=1.0.0" defer></script>
<script src="assest/fun-interactions.js" defer></script></head>
<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- LAKUM Header -->
    <header class="lakum-header" role="banner">
        <div class="lakum-header__container">
            <div class="lakum-header__logo">
                <a href="./" class="lakum-logo">
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
                </a>
            </div>
            <nav class="lakum-nav">
                <ul class="lakum-nav__list">
                    <li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link"><?php echo t('home', 'Home'); ?></a></li>
                    <li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link"><?php echo t('about', 'About'); ?></a></li>
                    <li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                    <li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link lakum-nav__link--active"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    <li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                    <li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog', 'Blog'); ?></a></li>
                    <li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press', 'Press'); ?></a></li>
                    <li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    <li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop', 'Shop'); ?></a></li>
                </ul>
            </nav>
            <div class="lakum-language-switcher">
                <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: العربية'; ?>">
                    <i class="ri-global-line"></i>
                    <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
                </a>
            </div>
            <button class="lakum-header__mobile-toggle" aria-label="Toggle menu">
                <span class="lakum-header__mobile-icon" aria-hidden="true"></span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="lakum-hero" style="aspect-ratio: 16/9">
        <div class="lakum-hero__image-wrapper">
            <img src="heroImage/img-4.webp"
                 alt="Exhibitions"
                 fetchpriority="high"
                 loading="eager"
                 decoding="async"
                 width="1200"
                 height="800"
                 class="lakum-hero__image"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
    </section>

    <!-- Upcoming Section -->
    <section class="lakum-exhibitions-upcoming">
        <div class="lakum-container">
            <h2 class="lakum-exhibitions-upcoming__title"><?php echo t('exhibitions_upcoming', 'Upcoming'); ?></h2>
            <div class="lakum-exhibitions-upcoming__grid" id="upcomingPreview">
                <!-- Events will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Featured Event Banner -->
    <section class="lakum-exhibitions-featured" id="featuredEvent">
        <!-- Featured event will be loaded here -->
    </section>

    <!-- CTA Section -->
    <section class="lakum-exhibitions-cta" >
        <div class="lakum-exhibitions-cta__background" id="upcomingPreview"></div>
        <div class="lakum-container">
            <div class="lakum-exhibitions-cta__content">
                <h2 class="lakum-exhibitions-cta__title"><?php echo t('exhibitions_cta_title', 'Exclusive Paid Workshops'); ?></h2>
                <p class="lakum-exhibitions-cta__text"><?php echo t('exhibitions_cta_text', 'Discover our curated selection of hands-on workshops and creative sessions'); ?></p>
                <a href="calendar.php" class="lakum-btn lakum-btn--primary"><?php echo t('exhibitions_explore_more', 'Explore More'); ?></a>
            </div>
        </div>
    </section>

    <!-- Previous Exhibitions Section -->
    <section class="lakum-exhibitions-previous">
        <div class="lakum-container">
            <h2 class="lakum-exhibitions-previous__title"><?php echo t('exhibitions_previous', 'Previous Exhibitions'); ?></h2>
            <div class="lakum-exhibitions-previous__grid" id="previousEvents">
                <!-- Events will be loaded here -->
            </div>
        </div>
    </section>

    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
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
                    <a href="terms.php" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="js/LanguageManager.js?v=1.0.0" defer></script>
    <script>
        // Wait for LanguageManager to be ready before calling functions
        function initPage() {
            // Ensure LanguageManager is initialized
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initPage, 100);
                return;
            }
            
            // Load upcoming events which will display featured event
            loadUpcomingEvents();
            loadPreviousExhibitions();
        }
        
        // Set current language from PHP
        window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
        
        const translations = {
            noUpcomingEvents: '<?php echo t("no_upcoming_events", "No upcoming events at this time"); ?>',
            checkBackSoon: '<?php echo t("check_back_soon", "Check back soon to see our previous exhibitions and events"); ?>',
            noPreviousExhibitions: '<?php echo t("no_previous_exhibitions", "No Past Exhibitions Yet"); ?>',
            discoverMore: '<?php echo t("discover_more", "Discover More"); ?>'
        };

        // Convert title to URL-friendly slug
        const slugify = (text) => {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        };

        const getEventUrl = (event) => {
            const lang = window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            return `event.php?id=${event.id}&lang=${lang}`;
        };

        function displayFeaturedEvent(event) {
            const container = document.getElementById('featuredEvent');
            if (!container) return;
            const eventDate = new Date(event.event_date);
            const dateStr = `${eventDate.getDate()} ${eventDate.toLocaleString('en-US', {month: 'short'}).toUpperCase()} ${eventDate.getFullYear()}`;
            const coverImage = event.cover_image || 'assest/img-4.webp';
            container.innerHTML = `
                <div class="lakum-exhibitions-featured__image">
                    <img src="${coverImage}" alt="${event.title}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="lakum-exhibitions-featured__content">
                    <div class="lakum-exhibitions-featured__text">
                        <span class="lakum-exhibitions-featured__date">${dateStr}</span>
                        <h2 class="lakum-exhibitions-featured__title">${event.title}</h2>
                        <p class="lakum-exhibitions-featured__description">${event.description}</p>
                        <a href="${getEventUrl(event)}" class="lakum-btn lakum-btn--primary">${translations.discoverMore}</a>
                    </div>
                </div>
            `;
        }

        function displayUpcomingEvents(events, excludeId = null) {
            const container = document.getElementById('upcomingPreview');
            if (!container) return;
            const filtered = excludeId ? events.filter(e => e.id != excludeId).slice(0, 6) : events.slice(0, 6);
            container.innerHTML = '';
            if (filtered.length === 0) {
                container.innerHTML = `<div class="lakum-empty-state"><h3>${translations.noUpcomingEvents}</h3></div>`;
                return;
            }
            filtered.forEach(event => {
                const eventDate = new Date(event.event_date);
                const dateStr = `${eventDate.getDate()} ${eventDate.toLocaleString('en-US', {month: 'short'})} ${eventDate.getFullYear()}`;
                const card = document.createElement('div');
                card.className = 'lakum-upcoming-card';
                card.style.cursor = 'pointer';
                card.onclick = () => window.location.href = getEventUrl(event);
                const coverImage = event.cover_image || 'assest/img-4.webp';
                card.innerHTML = `
                    <div class="lakum-upcoming-card__image-wrapper">
                        <img src="${coverImage}" alt="${event.title}" class="lakum-upcoming-card__image" loading="lazy">
                    </div>
                    <div class="lakum-upcoming-card__content">
                        <span class="lakum-upcoming-card__date">${dateStr}</span>
                        <h3 class="lakum-upcoming-card__title">${event.title}</h3>
                        <p class="lakum-upcoming-card__location"><i class="ri-map-pin-line"></i> ${event.location}</p>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function displayPreviousExhibitions(events) {
            const container = document.getElementById('previousEvents');
            if (!container) return;
            container.innerHTML = '';
            if (events.length === 0) {
                container.innerHTML = `<div class="lakum-empty-state"><h3>${translations.noPreviousExhibitions}</h3></div>`;
                return;
            }
            events.forEach(event => {
                const eventDate = new Date(event.event_date);
                const dateStr = `${eventDate.getDate()} ${eventDate.toLocaleString('en-US', {month: 'short'})} ${eventDate.getFullYear()}`;
                const card = document.createElement('div');
                card.className = 'lakum-upcoming-card';
                card.style.cursor = 'pointer';
                card.onclick = () => window.location.href = getEventUrl(event);
                const coverImage = event.cover_image || 'assest/img-4.webp';
                card.innerHTML = `
                    <div class="lakum-upcoming-card__image-wrapper">
                        <img src="${coverImage}" alt="${event.title}" class="lakum-upcoming-card__image" loading="lazy">
                    </div>
                    <div class="lakum-upcoming-card__content">
                        <span class="lakum-upcoming-card__date">${dateStr}</span>
                        <h3 class="lakum-upcoming-card__title">${event.title}</h3>
                        <p class="lakum-upcoming-card__location"><i class="ri-map-pin-line"></i> ${event.location}</p>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function loadUpcomingEvents(excludeId = null) {
            const lang = LanguageManager.getLanguage();
            const timestamp = new Date().getTime();
            fetch(`api/get_events.php?type=upcoming&limit=7&lang=${lang}&t=${timestamp}`, {
                cache: 'no-store'
            })
                .then(r => r.json())
                .then(result => {
                    let events = result.data || result;
                    if (!Array.isArray(events)) {
                        events = [];
                    }
                    // Load featured event from upcoming
                    if (events.length > 0) {
                        displayFeaturedEvent(events[0]);
                    }
                    displayUpcomingEvents(events);
                })
                .catch(error => {
                    console.error('Error loading upcoming events:', error);
                    // Show empty state on error
                    const container = document.getElementById('upcomingPreview');
                    if (container) {
                        container.innerHTML = `<div class="lakum-empty-state"><h3>${translations.noUpcomingEvents}</h3></div>`;
                    }
                });
        }

        function loadPreviousExhibitions() {
            const lang = LanguageManager.getLanguage();
            const timestamp = new Date().getTime();
            fetch(`api/get_events.php?type=past&limit=8&lang=${lang}&t=${timestamp}`, {
                cache: 'no-store'
            })
                .then(r => r.json())
                .then(result => {
                    let events = result.data || result;
                    if (!Array.isArray(events)) {
                        events = [];
                    }
                    displayPreviousExhibitions(events);
                })
                .catch(error => {
                    console.error('Error loading previous exhibitions:', error);
                    // Show empty state on error
                    const container = document.getElementById('previousEvents');
                    if (container) {
                        container.innerHTML = `<div class="lakum-empty-state"><h3>${translations.noPreviousExhibitions}</h3></div>`;
                    }
                });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPage);
        } else {
            initPage();
        }

    </script>

    <script src="assest/fab-button.js" defer></script>
    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/lakum-header.js" defer></script>

    <div class="fab-button" id="fabButton"><button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false"><i class="ri-mail-line fab-button__icon"></i><i class="ri-close-line fab-button__close"></i></button><div class="fab-button__menu" id="fabMenu" role="menu"><a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div>
</body>
</html>



















