<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
require_once 'includes/hero-settings.php';
require_once 'includes/site-settings.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('page_title', 'LAKUM Artspace - Cultural Hub in Riyadh | Art Exhibitions & Events'); ?></title>
    
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

    <!-- Global Stylesheets (Centralized) -->
    <?php include('includes/stylesheets.php'); ?>
    <meta name="title" content="LAKUM Artspace - Cultural Hub in Riyadh | Art Exhibitions & Events">
    <meta name="description" content="LAKUM Artspace is Riyadh's premier cultural destination for contemporary art exhibitions, creative workshops, and cultural events.">
    <meta name="keywords" content="art gallery Riyadh, cultural events Riyadh, art exhibitions Saudi Arabia">
    <meta name="author" content="LAKUM Artspace">
    <meta name="language" content="<?php echo isArabic() ? 'Arabic' : 'English'; ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <link rel="canonical" href="https://lakumartspace.infinityfree.me/?i=1">
    <link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/?lang=en" />
    <link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/?lang=ar" />
    <meta name="theme-color" content="#1a1a1a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    
</head>
<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <section class="lakum-hero">
        <div class="lakum-hero__image-wrapper">
            <?php renderHero('index', 'LAKUM Artspace'); ?>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo getHeroTitle('index', 'hero_title', 'Where Encounters Shape Culture'); ?></h1>
            <p class="lakum-hero__subtitle"><?php echo getHeroSubtitle('index', 'hero_subtitle', 'A living space for art, connection, and cultural exchange in the heart of Riyadh'); ?></p>
        </div>
    </section>

    <section class="lakum-section lakum-section--upcoming" id="upcoming-exhibitions">
        <div class="lakum-container">
            <div class="lakum-section-header">
                <h2 class="lakum-section-header__title"><?php echo t('upcoming_exhibitions', 'Upcoming Exhibitions'); ?></h2>
                <p class="lakum-section-header__subtitle"><?php echo t('explore_exhibitions', 'Explore our recent artistic journeys and cultural moments'); ?></p>
            </div>
        </div>
    </section>

    <section class="lakum-featured-banner" id="featuredBanner">
        <div class="lakum-featured-banner__content">
                <div class="lakum-featured-banner__image">
                    <img 
                        src="heroImage/img-4.webp" 
                        alt="Featured Event" 
                        loading="lazy" 
                        decoding="async" 
                        width="800" 
                        height="450"
                        style="content-visibility: auto;">
                </div>
                <div class="lakum-featured-banner__text">
                    <span class="lakum-featured-banner__date"><?php echo t('closest_event', 'Closest Event'); ?></span>
                    <h3 class="lakum-featured-banner__title"><?php echo t('featured_exhibition', 'Featured Exhibition'); ?></h3>
                    <p class="lakum-featured-banner__description"><?php echo t('featured_description', 'Discover this amazing exhibition showcasing contemporary art and culture.'); ?></p>
                    <a href="exhibitions.php" class="lakum-btn lakum-btn--primary"><?php echo t('view_details', 'View Details'); ?></a>
                </div>
            </div>
    </section>

    <section class="lakum-section">
        <div class="lakum-container">
            <div class="lakum-upcoming-grid" id="nextTwoEvents">
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
            </div>
            <div class="lakum-section-cta">
                <a href="exhibitions.php" class="lakum-btn lakum-btn--outline"><?php echo t('discover_more', 'Discover More'); ?></a>
            </div>
        </div>
    </section>

    <section class="lakum-cta lakum-cta--primary">
        <div class="lakum-container">
            <div class="lakum-cta__content">
                <h2 class="lakum-cta__title"><?php echo ss('home','cta1_title_en','cta_title','Driven by Soul, Made by Hands'); ?></h2>
                <p class="lakum-cta__text"><?php echo ss('home','cta1_desc_en','cta_description','Explore our diverse spaces and discover how LAKUM can bring your artistic vision to life'); ?></p>
                <a href="spaces.php" class="lakum-btn lakum-btn--primary"><?php echo t('discover_more', 'Discover More'); ?></a>
            </div>
        </div>
    </section>

    <section class="lakum-section lakum-section--exhibitions" id="past-exhibitions">
        <div class="lakum-container">
            <div class="lakum-section-divider">
                <span class="lakum-section-divider__line"></span>
                <h2 class="lakum-section-divider__title"><?php echo t('previous_exhibitions', 'Previous Exhibitions'); ?></h2>
                <span class="lakum-section-divider__line"></span>
            </div>
            <div class="lakum-exhibition-grid" id="recentEvents">
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
            </div>
        </div>
    </section>

    <section class="lakum-cta lakum-cta--dark" id="create-event">
        <div class="lakum-cta__background" style="background-image: url('<?php echo ssRaw('home','cta2_image','heroImage/img-4.webp'); ?>');"></div>
        <div class="lakum-container">
            <div class="lakum-cta__content">
                <h2 class="lakum-cta__title"><?php echo ss('home','cta2_title_en','create_event','Create Your Own Event'); ?></h2>
                <p class="lakum-cta__text"><?php echo ss('home','cta2_desc_en','create_event_description','Transform your vision into reality with our versatile spaces and comprehensive support services'); ?></p>
                <a href="spaces.php#form" class="lakum-btn lakum-btn--primary"><?php echo t('get_started', 'Get Started'); ?></a>
            </div>
        </div>
    </section>

    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" loading="eager" decoding="async" onerror="this.style.display='none'">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" loading="eager" decoding="async" onerror="this.style.display='none'">
                    </div>
                    <p class="lakum-footer__tagline"><?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?></p>
                </div>
                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('navigate', 'Navigate'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="index.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                        <li><a href="about.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                        <li><a href="spaces.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                        <li><a href="exhibitions.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    </ul>
                </nav>
                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('explore', 'Explore'); ?></h4>
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
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright_prefix', '© 2025 - '); ?><span id="year"></span><?php echo t('footer_copyright_suffix', ' LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <div class="fab-button" id="fabButton"><button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false"><i class="ri-mail-line fab-button__icon"></i><i class="ri-close-line fab-button__close"></i></button><div class="fab-button__menu" id="fabMenu" role="menu"><a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div>
    <script>
        // Set current language from PHP
        window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
        
        // Translation strings for JavaScript
        const viewDetailsText = '<?php echo t("view_details", "View Details"); ?>';
        const pastEventText = 'Past Event';

        // Load all events from API (real database data only)
        async function loadAllEvents() {
            try {
                // Add timestamp to bypass cache
                const timestamp = new Date().getTime();
                // CRITICAL: Only use window.LAKUM_LANG (set from PHP)
                // Do NOT use localStorage as it contains the previous page's language
                const lang = window.LAKUM_LANG || 'en';
                const response = await fetch(`api/get_events.php?type=all&limit=1000&lang=${lang}&t=${timestamp}`, {
                    cache: 'no-store'
                });
                const data = await response.json();
                if (data.success && data.data && Array.isArray(data.data)) {
                    console.log('Loaded events from database:', data.data.length);
                    return data.data;
                }
            } catch (error) {
                console.error('Error loading events:', error);
            }
            // Return empty array if API fails - no mock data
            return [];
        }

        // Convert 24h time to 12h format with AM/PM
        function convertTo12HourFormat(time24h) {
            if (!time24h) return '10:00 AM';
            const [hours, minutes] = time24h.substring(0, 5).split(':');
            let hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            hour = hour % 12 || 12;
            return `${hour}:${minutes} ${ampm}`;
        }

        // Load featured event
        async function loadFeaturedEvent() {
            const events = await loadAllEvents();
            const container = document.getElementById('featuredBanner');
            
            // Look for event marked as featured
            const featuredEvent = events.find(e => e.is_featured === 1 || e.is_featured === '1');
            
            if (!featuredEvent) {
                // No featured event selected - show empty state
                container.innerHTML = `
                    <div class="lakum-featured-banner__content" style="opacity: 0.5;">
                        <div class="lakum-featured-banner__image">
                            <img src="heroImage/img-4.webp" alt="No Featured Event" loading="lazy" style="filter: grayscale(100%);">
                        </div>
                        <div class="lakum-featured-banner__text">
                            <span class="lakum-featured-banner__date">No Featured Event</span>
                            <h3 class="lakum-featured-banner__title">Coming Soon</h3>
                            <p class="lakum-featured-banner__description">Check back soon for our featured event selection.</p>
                            <a href="exhibitions.php" class="lakum-btn lakum-btn--primary">` + viewDetailsText + `</a>
                        </div>
                    </div>
                `;
                return null;
            }
            
            const eventDate = new Date(featuredEvent.event_date);
            const dateStr = eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const timeStr = convertTo12HourFormat(featuredEvent.event_time);

            container.innerHTML = `
                <div class="lakum-featured-banner__content">
                    <div class="lakum-featured-banner__image">
                        <img src="${featuredEvent.cover_image || 'heroImage/img-4.webp'}" alt="${featuredEvent.title}" loading="lazy" decoding="async" style="content-visibility: auto;">
                    </div>
                    <div class="lakum-featured-banner__text">
                        <span class="lakum-featured-banner__date">${dateStr} • ${timeStr}</span>
                        <h3 class="lakum-featured-banner__title">${featuredEvent.title}</h3>
                        <p class="lakum-featured-banner__description">${featuredEvent.description}</p>
                        <a href="event.php?title=${(featuredEvent.slug || featuredEvent.title.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, ''))}&lang=${window.LAKUM_LANG || 'en'}" class="lakum-btn lakum-btn--primary">` + viewDetailsText + `</a>
                    </div>
                </div>
            `;
            
            return featuredEvent.id;
        }

        // Load upcoming events
        async function loadUpcomingEvents(excludeId = null) {
            const events = await loadAllEvents();
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            
            const upcomingEvents = events.filter(e => {
                const eventDate = new Date(e.event_date);
                eventDate.setHours(0, 0, 0, 0);
                return eventDate >= now;
            }).sort((a, b) => new Date(a.event_date) - new Date(b.event_date));
            
            const container = document.getElementById('nextTwoEvents');
            container.innerHTML = '';

            const filteredEvents = excludeId ? upcomingEvents.filter(e => e.id != excludeId).slice(0, 3) : upcomingEvents.slice(0, 3);

            if (filteredEvents.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No upcoming events</p>';
                return;
            }

            filteredEvents.forEach(event => {
                const eventDate = new Date(event.event_date);
                const month = eventDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
                const day = eventDate.getDate();
                const timeStr = convertTo12HourFormat(event.event_time);
                const slug = event.slug || event.title.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                const lang = window.LAKUM_LANG || 'en';

                const card = document.createElement('div');
                card.className = 'lakum-event-card';
                card.style.cursor = 'pointer';
                card.innerHTML = `
                    <div class="lakum-event-card__image">
                        <img src="${event.cover_image || 'heroImage/img-4.webp'}" alt="${event.title}" loading="lazy" decoding="async" style="content-visibility: auto;">
                        <div class="lakum-event-card__date">
                            <span class="lakum-event-card__date-month">${month}</span>
                            <span class="lakum-event-card__date-day">${day}</span>
                        </div>
                    </div>
                    <div class="lakum-event-card__content">
                        <h3 class="lakum-event-card__title">${event.title}</h3>
                        <p class="lakum-event-card__time">${timeStr}</p>
                        <a href="event.php?title=${slug}&lang=${lang}" class="lakum-event-card__link">View Details</a>
                    </div>
                `;
                card.addEventListener('click', () => {
                    window.location.href = `event.php?title=${slug}&lang=${lang}`;
                });
                container.appendChild(card);
            });
        }

        // Load previous events
        async function loadPreviousEvents() {
            const events = await loadAllEvents();
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            
            const previousEvents = events.filter(e => {
                const eventDate = new Date(e.event_date);
                eventDate.setHours(0, 0, 0, 0);
                return eventDate < now;
            }).sort((a, b) => new Date(b.event_date) - new Date(a.event_date));
            
            const container = document.getElementById('recentEvents');
            container.innerHTML = '';

            if (previousEvents.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No previous events</p>';
                return;
            }

            previousEvents.slice(0, 3).forEach(event => {
                const eventDate = new Date(event.event_date);
                const month = eventDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
                const day = eventDate.getDate();
                const timeStr = convertTo12HourFormat(event.event_time);
                const slug = event.slug || event.title.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                const lang = window.LAKUM_LANG || 'en';

                const card = document.createElement('div');
                card.className = 'lakum-event-card';
                card.style.cursor = 'pointer';
                card.innerHTML = `
                    <div class="lakum-event-card__image">
                        <img src="${event.cover_image || 'heroImage/img-4.webp'}" alt="${event.title}" loading="lazy" decoding="async" style="content-visibility: auto;">
                        <div class="lakum-event-card__date">
                            <span class="lakum-event-card__date-month">${month}</span>
                            <span class="lakum-event-card__date-day">${day}</span>
                        </div>
                    </div>
                    <div class="lakum-event-card__content">
                        <h3 class="lakum-event-card__title">${event.title}</h3>
                        <p class="lakum-event-card__time">${timeStr}</p>
                        <a href="event.php?title=${slug}&lang=${lang}" class="lakum-event-card__link">View Details</a>
                    </div>
                `;
                card.addEventListener('click', () => {
                    window.location.href = `event.php?title=${slug}&lang=${lang}`;
                });
                container.appendChild(card);
            });
        }

        // Initialize - DEFERRED until after LCP
        async function initPage() {
            const featuredId = await loadFeaturedEvent();
            await loadUpcomingEvents(featuredId);
            await loadPreviousEvents();
        }

        // Defer API loading until after page load
        if (document.readyState === 'complete') {
            // Page already loaded
            setTimeout(initPage, 200);
        } else {
            // Wait for load event
            window.addEventListener('load', function() {
                setTimeout(initPage, 200);
            });
        }

        // Reload when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                initPage();
            }
        });
    </script>

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
</body>
</html>




