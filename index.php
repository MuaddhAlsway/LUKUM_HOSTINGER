<?php
require_once 'lang/loader.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('page_title', 'LAKUM Artspace - Cultural Hub in Riyadh | Art Exhibitions & Events'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assest/logo/right_section.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assest/logo/right_section.png">
    <link rel="apple-touch-icon" href="assest/logo/right_section.png">
    <meta name="msapplication-TileImage" content="assest/logo/right_section.png">
    <link rel="preload" as="image" href="assest/img-4.png" fetchpriority="high">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="global-styles.css" as="style">
    <link rel="preload" href="lakum-components.css" as="style">
    <!-- Critical CSS - Load synchronously -->
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    
    <!-- Non-critical CSS - Defer loading -->
    <link rel="preload" href="Home.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="rtl.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="fonts/greta-arabic.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="assest/language-switcher.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="assest/popup-notification.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- Fallback for no-JS -->
    <noscript>
        <link rel="stylesheet" href="Home.css">
        <link rel="stylesheet" href="rtl.css">
        <link rel="stylesheet" href="fonts/greta-arabic.css">
        <link rel="stylesheet" href="assest/language-switcher.css">
        <link rel="stylesheet" href="assest/popup-notification.css">
    </noscript>
    
    <!-- Icons - Defer loading (not critical for initial render) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>
    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/language-link-preserver.js?v=1.0.0" defer></script>
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
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0;  }
        html { font-size: 16px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body {  sans-serif; background: #f6f6eb; color: #1a1a1a; overflow-x: hidden; line-height: 1.6; }
        
        .lakum-hero { position: relative; width: 100%; height: 85vh; min-height: 600px; display: flex; align-items: center; justify-content: center; background: #1a1a1a; }
        .lakum-hero__image-wrapper { position: absolute; inset: 0; z-index: 1; overflow: hidden; }
        .lakum-hero__image { width: 100%; height: 100%; object-fit: cover; display: block; }
        .lakum-hero__overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.65) 100%); z-index: 2; }
        .lakum-hero__content { position: relative; z-index: 3; text-align: center; color: #fff; max-width: 1400px; width: 90%; padding: 0 20px; }
        .lakum-hero__title { 
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 500;
    letter-spacing: -0.02em;
    line-height: 1.2;
    margin: 0 0 var(--spacing-lg) 0;
    animation: fadeInUp 0.8s ease-out;
    color: #ffffff;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    font-family: 'Greta Arabic', 'Greta Text Arabic', -apple-system, BlinkMacSystemFont, sans-serif !important;
}
        .lakum-hero__subtitle { font-size: clamp(1.1rem, 2vw, 1.4rem); font-weight: 300; line-height: 1.6; color: #fff; text-align: center; }
        
        .lakum-section { padding: 80px 0; }
        .lakum-section--upcoming { background: #f6f6eb; padding: 60px 0; }
        .lakum-section--exhibitions { background: #f6f6eb; padding: 80px 0; }
        .lakum-container {  
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 clamp(20px, 5vw, 60px); }
        
        .lakum-section-header { margin-bottom: 60px; text-align: center; }
        .lakum-section-header__title { font-size: 2.5rem; font-weight: 300; color: #1a1a1a; margin-bottom: 15px; }
        .lakum-section-header__subtitle { font-size: 1.1rem; color: #666; text-align: center; }
        
        .lakum-featured-banner { padding: 0; background: #edecdf; }
        .lakum-featured-banner__content { display: grid; grid-template-columns: 1fr 1fr; gap: 0; align-items: stretch; width: 100vw; margin-left: calc(-50vw + 50%); }
        .lakum-featured-banner__image { width: 100%; height: 450px; overflow: hidden; border-radius: 0; }
        .lakum-featured-banner__image img { width: 100%; height: 100%; display: block; border-radius: 0; box-shadow: none; object-fit: cover; }
        .lakum-featured-banner__text { padding: 60px; display: flex; flex-direction: column; justify-content: center; }
        .lakum-featured-banner__date { font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 15px; }
        .lakum-featured-banner__title { font-size: 2rem; font-weight: 300; margin-bottom: 20px; color: #1a1a1a; }
        .lakum-featured-banner__description { font-size: 1.05rem; line-height: 1.8; color: #555; margin-bottom: 25px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-clamp: 2; word-break: break-word; }
        
        .lakum-upcoming-grid {          display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: var(--spacing-2xl);
    max-width: 1400px;
    margin: 0 auto;}
        .lakum-event-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s ease; width: 100%; height: 485.56px; display: flex; flex-direction: column; }
        .lakum-event-card:hover { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .lakum-event-card__image {  position: relative;width: 100%;height: 320px;overflow: hidden;background: #edecdf; }
        .lakum-event-card__image img { width: 100%; height: 100%; object-fit: cover; }
        .lakum-event-card__date { position: absolute; top: var(--spacing-lg); left: var(--spacing-lg); background: rgba(246, 246, 235, 0.95); backdrop-filter: blur(10px); padding: var(--spacing-md) var(--spacing-lg); border-radius: var(--radius-sm); display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .lakum-event-card__date-month { font-size: 0.85rem; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; color: #525252; }
        .lakum-event-card__date-day { font-size: 2rem; font-weight: 300; color: #1a1a1a; line-height: 1; }
        .lakum-event-card__content { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .lakum-event-card__title { font-size: 1.3rem; font-weight: 500; margin-bottom: 8px; color: #1a1a1a; }
        .lakum-event-card__time { font-size: 0.9rem; color: #ff6b35; margin-bottom: 12px; }
        .lakum-event-card__link { font-size: 0.9rem; color: #1a1a1a; text-decoration: none; font-weight: 500; }
        
        .lakum-cta { padding: 80px 0; text-align: center; }
        .lakum-cta--primary { background: linear-gradient(135deg, #edecdf 0%, #f6f6eb 100%); }
        .lakum-cta--primary .lakum-cta__title { color: #1a1a1a; }
        .lakum-cta--primary .lakum-cta__text { color: #555; }
        .lakum-cta--dark { position: relative; color: white; overflow: hidden; }
        .lakum-cta__background { position: absolute; inset: 0; z-index: 0; background-image: url('assest/img-4.png'); background-size: cover; background-position: center; background-attachment: scroll; }
        .lakum-cta__background::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.4) 100%); z-index: 1; }
        .lakum-cta__content { position: relative; z-index: 2; text-align: center; color: white; }
        .lakum-cta__title { font-size: 2.5rem; font-weight: 300; margin-bottom: 20px; color: white; }
        .lakum-cta__text { font-size: 1.1rem; margin-bottom: 30px; line-height: 1.6; color: white; text-align: center; max-width: 600px; margin-left: auto; margin-right: auto; }
        
        .lakum-section-divider { display: flex; align-items: center; gap: 20px; margin-bottom: 60px; }
        .lakum-section-divider__line { flex: 1; height: 1px; background: #d1d1d1; }
        .lakum-section-divider__title { font-size: 2.5rem; font-weight: 300; color: #1a1a1a; white-space: nowrap; }
        
        .lakum-exhibition-grid {      display: grid;

    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));

gap: var(--spacing-2xl);

max-width: 1400px;

margin: 0 auto;}
        .lakum-skeleton-card { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; height: 350px; border-radius: 8px; }
        @keyframes loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        
        .lakum-section-cta { display: flex; justify-content: center; margin-top: 40px; }
        
        @media (max-width: 768px) {
            .lakum-hero { height: 60vh; min-height: 450px; }
            .lakum-featured-banner__content { grid-template-columns: 1fr; gap: 30px; }
            .lakum-cta__title { font-size: 2rem; }
            .lakum-section-divider { flex-direction: column; gap: 15px; }
            .lakum-section-divider__line { display: none; }
        }
        @media (max-width: 480px) {
            .lakum-hero { height: 50vh; min-height: 400px; }
        }
        .lakum-btn { 
            background: #000 !important; 
            color: white !important; 
            border: none !important; 
            padding: 16px 40px !important; 
            display: inline-block !important; 
            width: auto !important;
            min-width: auto !important;
            max-width: none !important;
            flex: none !important;
            margin: 0 !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        .lakum-btn:hover { 
            background: #333 !important; 
            transform: translateY(-2px) !important;
        }
        .lakum-btn--primary { 
            background: #000 !important; 
            color: white !important; 
            padding: 16px 40px !important; 
            display: inline-flex !important; 
            align-items: center !important;
            justify-content: center !important;
            width: fit-content !important;
            border: none !important;
            margin: 0 !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        .lakum-btn--primary:hover { 
            background: #333 !important; 
            transform: translateY(-2px) !important;
        }
        .lakum-btn--outline { 
            background: #000 !important; 
            color: white !important; 
            border: 1px solid #000 !important; 
            padding: 16px 40px !important; 
            display: inline-block !important; 
            width: auto !important;
            min-width: auto !important;
            max-width: none !important;
            flex: none !important;
            margin: 0 !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        .lakum-btn--outline:hover { 
            background: #333 !important; 
            transform: translateY(-2px) !important;
        }
    </style>
</head>
<body class="<?php echo getLanguageClass(); ?>">
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <header class="lakum-header">
        <div class="lakum-header__container">
            <div class="lakum-header__logo">
                <a href="./" class="lakum-logo">
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-logo__left">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-logo__right">
                </a>
            </div>
            <nav class="lakum-nav">
                <ul class="lakum-nav__list">
                    <li class="lakum-nav__item"><a href="index.php" class="lakum-nav__link lakum-nav__link--active"><?php echo t('home', 'Home'); ?></a></li>
                    <li class="lakum-nav__item"><a href="about.php" class="lakum-nav__link"><?php echo t('about', 'About'); ?></a></li>
                    <li class="lakum-nav__item"><a href="spaces.php" class="lakum-nav__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                    <li class="lakum-nav__item"><a href="exhibitions.php" class="lakum-nav__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    <li class="lakum-nav__item"><a href="calendar.php" class="lakum-nav__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                    <li class="lakum-nav__item"><a href="blog.php" class="lakum-nav__link"><?php echo t('blog', 'Blog'); ?></a></li>
                    <li class="lakum-nav__item"><a href="press.php" class="lakum-nav__link"><?php echo t('press', 'Press'); ?></a></li>
                    <li class="lakum-nav__item"><a href="contact.php" class="lakum-nav__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    <li class="lakum-nav__item"><a href="shop.php" class="lakum-nav__link"><?php echo t('shop', 'Shop'); ?></a></li>
                </ul>
            </nav>
            <div class="lakum-language-switcher">
                <a href="<?php echo buildLanguageSwitcherUrl(); ?>" class="lakum-lang-link" title="<?php echo isArabic() ? 'Language: English' : 'Language: ???????'; ?>">
                    <i class="ri-global-line"></i>
                    <span class="lakum-lang-text"><?php echo isArabic() ? 'EN' : 'AR'; ?></span>
                </a>
            </div>
            <button class="lakum-header__mobile-toggle" aria-label="Toggle navigation menu">
                <span class="lakum-header__mobile-icon" aria-hidden="true"></span>
            </button>
        </div>
    </header>

    <section class="lakum-hero" style="aspect-ratio: 16/9">
        <div class="lakum-hero__image-wrapper">
            <img src="assest/img-4.png" alt="LAKUM Artspace" class="lakum-hero__image" width="1920" height="1080" fetchpriority="high" decoding="async" style="aspect-ratio: 16/9">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title"><?php echo t('hero_title', 'Where Encounters Shape Culture'); ?></h1>
            <p class="lakum-hero__subtitle"><?php echo t('hero_subtitle', 'A living space for art, connection, and cultural exchange in the heart of Riyadh'); ?></p>
        </div>
    </section>

    <section class="lakum-section lakum-section--upcoming">
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
                    <img src="assest/img-4.png" alt="Featured Event" loading="lazy">
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
                <h2 class="lakum-cta__title"><?php echo t('cta_title', 'Driven by Soul, Made by Hands'); ?></h2>
                <p class="lakum-cta__text"><?php echo t('cta_description', 'Explore our diverse spaces and discover how LAKUM can bring your artistic vision to life'); ?></p>
                <a href="spaces.php" class="lakum-btn lakum-btn--primary"><?php echo t('discover_more', 'Discover More'); ?></a>
            </div>
        </div>
    </section>

    <section class="lakum-section lakum-section--exhibitions">
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

    <section class="lakum-cta lakum-cta--dark">
        <div class="lakum-cta__background" style="background-image: url('assest/img-4.png'); will-change: background-image; contain: layout style paint;"></div>
        <div class="lakum-container">
            <div class="lakum-cta__content">
                <h2 class="lakum-cta__title"><?php echo t('create_event', 'Create Your Own Event'); ?></h2>
                <p class="lakum-cta__text"><?php echo t('create_event_description', 'Transform your vision into reality with our versatile spaces and comprehensive support services'); ?></p>
                <a href="spaces.php#form" class="lakum-btn lakum-btn--primary"><?php echo t('get_started', 'Get Started'); ?></a>
            </div>
        </div>
    </section>

    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right">
                    </div>
                    <p class="lakum-footer__tagline"><?php echo t('tagline', 'Where Encounters Shape Culture'); ?></p>
                </div>
                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('navigate', 'Navigate'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="index.php" class="lakum-footer__link"><?php echo t('home', 'Home'); ?></a></li>
                        <li><a href="about.php" class="lakum-footer__link"><?php echo t('about', 'About'); ?></a></li>
                        <li><a href="spaces.php" class="lakum-footer__link"><?php echo t('spaces', 'Spaces'); ?></a></li>
                        <li><a href="exhibitions.php" class="lakum-footer__link"><?php echo t('exhibitions', 'Exhibitions'); ?></a></li>
                    </ul>
                </nav>
                <nav class="lakum-footer__nav">
                    <h4 class="lakum-footer__nav-title"><?php echo t('explore', 'Explore'); ?></h4>
                    <ul class="lakum-footer__nav-list">
                        <li><a href="calendar.php" class="lakum-footer__link"><?php echo t('calendar', 'Calendar'); ?></a></li>
                        <li><a href="blog.php" class="lakum-footer__link"><?php echo t('blog', 'Blog'); ?></a></li>
                        <li><a href="press.php" class="lakum-footer__link"><?php echo t('press', 'Press'); ?></a></li>
                        <li><a href="contact.php" class="lakum-footer__link"><?php echo t('contact_us', 'Contact'); ?></a></li>
                    </ul>
                </nav>
                <div class="lakum-footer__social">
                    <h4 class="lakum-footer__nav-title"><?php echo t('connect', 'Connect'); ?></h4>
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
                <p class="lakum-footer__copyright">© 2025 - 2027 LAKUM Artspace. <?php echo t('all_rights_reserved', 'All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms.php" class="lakum-footer__legal-link"><?php echo t('terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <div class="lakum-contact-fab" id="lakumContactFab">
        <button class="lakum-contact-fab__trigger" id="fabTrigger" aria-label="Contact options">
            <i class="ri-mail-line lakum-contact-fab__icon"></i>
            <i class="ri-close-line lakum-contact-fab__close"></i>
        </button>
        <div class="lakum-contact-fab__menu" id="fabMenu">
            <a href="tel:+966920012083" class="lakum-contact-fab__item" data-tooltip="<?php echo t('call_us', 'Call us'); ?>">
                <i class="ri-phone-line"></i>
            </a>
            <a href="https://wa.me/966920012083" target="_blank" class="lakum-contact-fab__item" data-tooltip="WhatsApp">
                <i class="ri-whatsapp-line"></i>
            </a>
            <a href="mailto:info@lakumartspace.com" class="lakum-contact-fab__item" data-tooltip="<?php echo t('email', 'Email'); ?>">
                <i class="ri-mail-line"></i>
            </a>
        </div>
    </div>

    <script src="assest/fun-interactions.js?v=5.0.0" defer></script>
    <script src="mobile-performance-optimizer.js?v=5.0.0" defer></script>
    <script src="js/LanguageManager.js?v=1.0.0"></script>
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
                            <img src="assest/img-4.png" alt="No Featured Event" loading="lazy" style="filter: grayscale(100%);">
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
                        <img src="${featuredEvent.cover_image || 'assest/img-4.png'}" alt="${featuredEvent.title}" loading="lazy">
                    </div>
                    <div class="lakum-featured-banner__text">
                        <span class="lakum-featured-banner__date">${dateStr} • ${timeStr}</span>
                        <h3 class="lakum-featured-banner__title">${featuredEvent.title}</h3>
                        <p class="lakum-featured-banner__description">${featuredEvent.description}</p>
                        <a href="event.php?id=${featuredEvent.id}" class="lakum-btn lakum-btn--primary">` + viewDetailsText + `</a>
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

                const card = document.createElement('div');
                card.className = 'lakum-event-card';
                card.innerHTML = `
                    <div class="lakum-event-card__image">
                        <img src="${event.cover_image || 'assest/img-4.png'}" alt="${event.title}" loading="lazy">
                        <div class="lakum-event-card__date">
                            <span class="lakum-event-card__date-month">${month}</span>
                            <span class="lakum-event-card__date-day">${day}</span>
                        </div>
                    </div>
                    <div class="lakum-event-card__content">
                        <h3 class="lakum-event-card__title">${event.title}</h3>
                        <p class="lakum-event-card__time">${timeStr}</p>
                        <a href="event.php?id=${event.id}" class="lakum-event-card__link">View Details</a>
                    </div>
                `;
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

                const card = document.createElement('div');
                card.className = 'lakum-event-card';
                card.innerHTML = `
                    <div class="lakum-event-card__image">
                        <img src="${event.cover_image || 'assest/img-4.png'}" alt="${event.title}" loading="lazy">
                        <div class="lakum-event-card__date">
                            <span class="lakum-event-card__date-month">${month}</span>
                            <span class="lakum-event-card__date-day">${day}</span>
                        </div>
                    </div>
                    <div class="lakum-event-card__content">
                        <h3 class="lakum-event-card__title">${event.title}</h3>
                        <p class="lakum-event-card__time">${timeStr}</p>
                        <a href="event.php?id=${event.id}" class="lakum-event-card__link">View Details</a>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Initialize
        async function initPage() {
            // Ensure LanguageManager is initialized
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initPage, 100);
                return;
            }
            
            const featuredId = await loadFeaturedEvent();
            await loadUpcomingEvents(featuredId);
            await loadPreviousEvents();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPage);
        } else {
            initPage();
        }

        // Reload when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                initPage();
            }
        });
    </script>

    <script src="assest/popup-notification.js?v=5.0.0" defer></script>
</body>
</html>

