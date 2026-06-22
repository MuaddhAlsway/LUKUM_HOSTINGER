<?php
require_once 'lang/loader.php';
if (file_exists('api/image-helper.php')) { require_once 'api/image-helper.php'; }
require_once 'includes/site-settings.php';
require_once 'includes/hero-settings.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo t('page_title', 'About LAKUM Artspace'); ?></title>
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
<style>/* Critical CSS - Inline for instant rendering */*,*::before,*::after {box-sizing: border-box;margin: 0;padding: 0}html {font-size: 16px;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale}body {font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;background: #f6f6eb;color: #1a1a1a;overflow-x: hidden;line-height: 1.6}* {font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;}.lakum-hero {position: relative;width: 100%;height: 85vh;min-height: 600px;display: flex;align-items: center;justify-content: center;background: #1a1a1a;contain: layout style paint}.lakum-hero__image-wrapper {position: absolute;inset: 0;z-index: 1;overflow: hidden}.lakum-hero__image {width: 100%;height: 100%;object-fit: cover;display: block;will-change: transform;transform: translateZ(0)}.lakum-hero__overlay {position: absolute;inset: 0;background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.65) 100%);z-index: 2}.lakum-hero__content {position: relative;z-index: 3;text-align: center;color: #fff;max-width: 1400px;width: 90%;padding: 0 20px}.lakum-hero__title {font-size: clamp(2.5rem, 6vw, 4.5rem);font-weight: 300;line-height: 1.2;margin: 0 0 20px 0;color: #fff;text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3)}.lakum-hero__subtitle {font-size: clamp(1.1rem, 2vw, 1.4rem);font-weight: 300;line-height: 1.6;color: #fff;text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3)}@media(max-width:768px) {.lakum-hero {height: 60vh;min-height: 450px}}@media(max-width:480px) {.lakum-hero {height: 50vh;min-height: 400px}}</style>

<!-- Global Stylesheets (Centralized) -->
<?php include('includes/stylesheets.php'); ?>

<!-- Page-specific styles -->
<link rel="stylesheet" href="about.css">

<meta name="title" content="About LAKUM Artspace - Our Story & Mission">
<meta name="description" content="Learn about LAKUM Artspace, a premier cultural destination in Riyadh dedicated to fostering artistic expression, cultural exchange, and creative innovation.">
<meta name="keywords" content="about LAKUM, art gallery Riyadh, cultural hub, art exhibitions, creative workshops">
<meta name="author" content="LAKUM Artspace">
<meta name="language" content="<?php echo isArabic() ? 'Arabic' : 'English'; ?>">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<link rel="canonical" href="https://lakumartspace.infinityfree.me/about.php">
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/about.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/about.php?lang=ar" />
<meta name="theme-color" content="#1a1a1a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="format-detection" content="telephone=no">
<script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>
<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <!-- Global Header Navigation (Unified) -->
    <?php include('lakum-header-unified.php'); ?>

    <section class="lakum-hero" style="aspect-ratio: 16/9">
        <?php renderHero('about', 'About LAKUM Artspace'); ?>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo getHeroTitle('about', 'about_hero_title', 'About LAKUM Artspace'); ?></h1>
            <p class="lakum-hero__subtitle"><?php echo getHeroSubtitle('about', 'about_hero_subtitle', 'A living space for art, connection, and cultural exchange in the heart of Riyadh'); ?></p>
        </div>
    </section>

<div class="page-content">
<section class="lakum-about-section"><div class="lakum-container"><div class="lakum-about-section__content"><div class="lakum-about-section__text"><h2 class="lakum-about-section__title"><?php echo ss('about','about_heading_en','about_heading','About Us'); ?></h2><div class="lakum-about-section__description"><div class="lakum-about-text__paragraphs"><p><strong><?php echo ss('about','about_intro_en','about_intro','Lakum Artspace is more than a gallery. It is a living space for connection.'); ?></strong></p><p><?php echo ss('about','about_belief_en','about_belief','We believe art is not only seen but shared, not only displayed but lived.'); ?></p><p><?php echo ss('about','about_exchange_en','about_exchange','As a dynamic space for cultural exchange, Lakum Artspace embraces the unexpected.'); ?></p><p><strong><?php echo ss('about','about_closing_en','about_closing','For those who seek meaning through connection, this is the new face of Lakum Artspace.'); ?></strong></p></div></div></div><div class="lakum-about-section__image"><?php $aboutImg = ssRaw('about','about_image','about/1.jpg'); echo ImageHelper::render($aboutImg, 'LAKUM Artspace', 'gallery'); ?></div></div></div></section>
<section class="lakum-workshops-section"><div class="lakum-container"><div class="lakum-workshops-section__content"><div class="lakum-workshops-section__image"><?php $wsImg = ssRaw('about','workshops_image','about/2.jpg'); echo ImageHelper::render($wsImg, 'Workshops & Seminars', 'gallery'); ?></div><div class="lakum-workshops-section__text"><h2 class="lakum-workshops-section__title"><?php echo ss('about','workshops_heading_en','workshops_heading','Workshops & Seminars'); ?></h2><p class="lakum-workshops-section__description"><?php echo ss('about','workshops_desc1_en','workshops_description_1','We offer a diverse range of workshops, seminars, and educational programs designed to inspire creativity and foster artistic growth.'); ?></p><p class="lakum-workshops-section__description"><?php echo ss('about','workshops_desc2_en','workshops_description_2','Whether you\'re looking to develop new skills, explore different artistic mediums, or connect with fellow creatives.'); ?></p></div></div></div></section>
<section class="lakum-stats-section"><div class="lakum-container"><div class="lakum-stats-grid"><div class="lakum-stat-card"><div class="lakum-stat-card__number"><?php echo ss('about','stat1_number','','+39'); ?></div><div class="lakum-stat-card__label"><?php echo ss('about','stat1_label','stat_exhibitions','Exhibitions & Workshops'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number"><?php echo ss('about','stat2_number','','+200K'); ?></div><div class="lakum-stat-card__label"><?php echo ss('about','stat2_label','stat_pieces','Art Pieces'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number"><?php echo ss('about','stat3_number','','+300K'); ?></div><div class="lakum-stat-card__label"><?php echo ss('about','stat3_label','stat_artists','Artist & Instructor'); ?></div></div><div class="lakum-stat-card"><div class="lakum-stat-card__number"><?php echo ss('about','stat4_number','','+55K'); ?></div><div class="lakum-stat-card__label"><?php echo ss('about','stat4_label','stat_participants','Participants & Visitors'); ?></div></div></div></div></section>
<section class="lakum-tagline-section"><div class="lakum-container"><p class="lakum-tagline-section__spaces"><?php echo t('tagline_spaces', 'Artspace, Gallery, Hub, Library, Shop, Caf�'); ?></p><h2 class="lakum-tagline-section__title"><?php echo t('tagline_main', 'Where Encounters Shape Culture'); ?></h2></div></section>
<section class="lakum-section"><div class="lakum-container"><div class="lakum-section-header"><h2 class="lakum-section-header__title"><?php echo t('upcoming_exhibitions', 'Upcoming Exhibitions'); ?></h2></div><div class="lakum-upcoming-grid" id="upcomingEvents"><div class="lakum-skeleton-card"></div><div class="lakum-skeleton-card"></div><div class="lakum-skeleton-card"></div></div><div class="lakum-section-cta"><a href="exhibitions.php" class="lakum-btn lakum-btn--outline"><?php echo t('view_more', 'View More'); ?></a></div></div></section>
</div>
<footer class="lakum-footer"><div class="lakum-footer__container"><div class="lakum-footer__content"><div class="lakum-footer__brand"><div class="lakum-footer__logo"><img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" width="105" height="80" decoding="async"><img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" width="105" height="80" decoding="async"></div><p class="lakum-footer__tagline"><?php echo t('footer_tagline', 'Where Encounters Shape Culture'); ?></p></div><nav class="lakum-footer__nav"><h4 class="lakum-footer__nav-title"><?php echo t('footer_navigate', 'Navigate'); ?></h4><ul class="lakum-footer__nav-list"><li><a href="index.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li><li><a href="about.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li><li><a href="spaces.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li><li><a href="exhibitions.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li></ul></nav><nav class="lakum-footer__nav"><h4 class="lakum-footer__nav-title"><?php echo t('footer_explore', 'Explore'); ?></h4><ul class="lakum-footer__nav-list"><li><a href="calendar.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li><li><a href="blog.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li><li><a href="press.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li><li><a href="contact.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li></ul></nav><div class="lakum-footer__social"><h4 class="lakum-footer__nav-title"><?php echo t('footer_connect', 'Connect'); ?></h4><div class="lakum-footer__social-links"><a href="https://www.instagram.com/lakumartspace/" target="_blank" class="lakum-footer__social-link" aria-label="Instagram"><i class="ri-instagram-fill"></i></a><a href="https://x.com/Lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Twitter"><i class="ri-twitter-x-fill"></i></a><a href="https://www.snapchat.com/add/lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="Snapchat"><i class="ri-snapchat-line"></i></a><a href="https://www.tiktok.com/@lakumartspace" target="_blank" class="lakum-footer__social-link" aria-label="TikTok"><i class="ri-tiktok-fill"></i></a></div></div></div><div class="lakum-footer__bottom"><p class="lakum-footer__copyright"><?php echo t('footer_copyright_prefix', '� 2025 - '); ?><span id="year"></span><?php echo t('footer_copyright_suffix', ' LAKUM Artspace. All rights reserved.'); ?></p><div class="lakum-footer__legal"><a href="terms.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a><span class="lakum-footer__legal-divider">|</span><a href="privacy.php?lang=<?php echo getCurrentLanguage(); ?>" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a></div></div></div></footer>
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

<script src="js/LanguageManager.js?v=1.0.0" defer></script>
<script>
    // Set current language from PHP
    window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
    
    // Load all events from API (real database data only)
    async function loadAllEvents() {
        try {
            // Add timestamp to bypass cache
            const timestamp = new Date().getTime();
            const lang = LanguageManager.getLanguage();
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

    // Load upcoming events
    async function loadUpcomingEvents() {
        const events = await loadAllEvents();
        const now = new Date();
        now.setHours(0, 0, 0, 0);
        
        const upcomingEvents = events.filter(e => {
            const eventDate = new Date(e.event_date);
            eventDate.setHours(0, 0, 0, 0);
            return eventDate >= now;
        }).sort((a, b) => new Date(a.event_date) - new Date(b.event_date));
        
        const container = document.getElementById('upcomingEvents');
        container.innerHTML = '';

        const filteredEvents = upcomingEvents.slice(0, 3);

        if (filteredEvents.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No upcoming events</p>';
            return;
        }

        filteredEvents.forEach(event => {
            const eventDate = new Date(event.event_date);
            const month = eventDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
            const day = eventDate.getDate();
            const dateStr = `${month} ${day}`;
            const slug = event.slug || event.title.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
            const lang = window.LAKUM_LANG || 'en';

            const card = document.createElement('div');
            card.className = 'lakum-event-card';
            card.style.cursor = 'pointer';
            card.innerHTML = `
                <div class="lakum-event-card__image">
                    <img src="${event.cover_image || 'heroImage/img-4.webp'}" alt="${event.title}" loading="lazy">
                </div>
                <div class="lakum-event-card__content">
                    <h3 class="lakum-event-card__title">${event.title}</h3>
                    <p class="lakum-event-card__time">${dateStr}</p>
                </div>
            `;
            card.addEventListener('click', () => {
                window.location.href = `event.php?title=${slug}&lang=${lang}`;
            });
            container.appendChild(card);
        });
    }

    // Initialize
    function initAboutPage() {
        if (typeof LanguageManager === 'undefined') {
            console.warn('LanguageManager not ready, retrying...');
            setTimeout(initAboutPage, 100);
            return;
        }
        loadUpcomingEvents();
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAboutPage);
    } else {
        initAboutPage();
    }

    // Reload when page becomes visible
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            initAboutPage();
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

    // Counter Animation for Stats
    function animateCounters() {
        const statNumbers = document.querySelectorAll('.lakum-stat-card__number');
        
        statNumbers.forEach(element => {
            const finalValue = element.textContent.trim();
            const numericValue = parseInt(finalValue.replace(/\D/g, ''));
            const prefix = finalValue.match(/^\D+/) ? finalValue.match(/^\D+/)[0] : '';
            const suffix = finalValue.match(/\D+$/) ? finalValue.match(/\D+$/)[0] : '';
            
            if (isNaN(numericValue)) return;
            
            let currentValue = 0;
            const duration = 2000; // 2 seconds
            const startTime = Date.now();
            
            function updateCounter() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function for smooth animation
                const easeOutQuad = 1 - Math.pow(1 - progress, 2);
                currentValue = Math.floor(numericValue * easeOutQuad);
                
                element.textContent = prefix + currentValue + suffix;
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = finalValue;
                }
            }
            
            updateCounter();
        });
    }

    // Trigger counter animation when stats section comes into view
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = 'true';
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Start observing stats section
    const statsSection = document.querySelector('.lakum-stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
</script>

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
</body>
</html>


















