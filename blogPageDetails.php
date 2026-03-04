<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Blog - LAKUM Artspace</title>
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

    <!-- Preload critical fonts -->
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="prefetch" href="assest/fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

    <!-- Greta Arabic Font - Universal for both Arabic and English -->
    <!-- Core Styles - Critical CSS loaded synchronously -->
    <link rel="stylesheet" href="critical-inline.css">
    <link rel="stylesheet" href="global-styles.css">
    <link rel="stylesheet" href="lakum-components.css">
    <link rel="stylesheet" href="assest/mobile-menu.css">
    <link rel="stylesheet" href="assest/fab-button.css">
    <link rel="stylesheet" href="assest/app-header.css">
    <link rel="stylesheet" href="Home.min.css">

    <!-- RTL Styles -->
    <link rel="stylesheet" href="rtl.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="fonts/greta-arabic.css">

    <!-- Remixicon Font for Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"></noscript>

    <!-- Event Detail Styles -->
    <link rel="stylesheet" href="event-detail.css">

    <!-- Blog Styles -->
    <link rel="stylesheet" href="blog.css">

    <!-- Blog Page Details Styles -->
    <link rel="stylesheet" href="blog-page-details.css">

    <!-- Image Optimizer - Critical for performance -->
    <script src="assest/navbar-mobile-toggle.js?v=5.0.0" defer></script>
    <script src="assest/fab-button.js" defer></script>

    <!-- Scripts - Defer non-critical JavaScript -->
    <script src="assest/settings-links-loader.js?v=5.0.0" defer></script>
    <script src="js/LanguageManager.js?v=1.0.0" defer></script>
    <script src="assest/fun-interactions.js" defer></script>
    <script>
        // Set current language from PHP
        window.LAKUM_LANG = '<?php echo getCurrentLanguage(); ?>';
        window.LAKUM_DIR = window.LAKUM_LANG === 'ar' ? 'rtl' : 'ltr';
    </script>
<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/blogPageDetails.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/blogPageDetails.php?lang=ar" />
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script></head>

<body>
    <div class="lakum-page-loader" id="pageLoader">
        <div class="lakum-page-loader__content">
            <div class="lakum-page-loader__spinner"></div>
        </div>
    </div>

    <header class="app-header" role="banner">
        <div class="app-header__container">
            <div class="app-header__logo">
                <a href="index.php" class="app-logo">
                    <img src="assest/logo/right_section.png" alt="LAKUM" class="app-logo__left" width="105" height="80" decoding="async">
                    <img src="assest/logo/left_section.png" alt="Artspace" class="app-logo__right" width="105" height="80" decoding="async">
                </a>
            </div>

            <nav class="app-nav" id="appNav" role="navigation">
                <ul class="app-nav__list">
                    <li class="app-nav__item"><a href="index.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Home</a></li>
                    <li class="app-nav__item"><a href="about.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">About</a></li>
                    <li class="app-nav__item"><a href="spaces.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Spaces</a></li>
                    <li class="app-nav__item"><a href="exhibitions.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Exhibitions</a></li>
                    <li class="app-nav__item"><a href="calendar.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Calendar</a></li>
                    <li class="app-nav__item"><a href="blog.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Blog</a></li>
                    <li class="app-nav__item"><a href="press.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Press</a></li>
                    <li class="app-nav__item"><a href="contact.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Contact</a></li>
                    <li class="app-nav__item"><a href="shop.php<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '?lang=ar' : ''; ?>" class="app-nav__link">Shop</a></li>
                </ul>
            </nav>

            <div class="app-header__controls">
                <div class="app-language-switcher">
                    <a href="<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? '/exhibitions.php?lang=en' : '/exhibitions.php?lang=ar'; ?>" class="app-lang-link" title="<?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'Language: English' : 'Language: العربية'; ?>">
                        <i class="ri-global-line"></i>
                        <span class="app-lang-text"><?php echo isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'En' : 'Ar'; ?></span>
                    </a>
                </div>

                <button class="app-header__menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="appNav">
                    <span class="app-header__menu-icon"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="event-hero">
        <div class="event-hero__image-wrapper">
            <img id="hero-image" src="assest/img-4.webp" alt="Blog" class="event-hero__image">
            <div class="event-hero__overlay"></div>
        </div>
        <div class="event-hero__content">
            <div class="event-container">
                <h1 class="event-hero__title" id="blog-title">Loading...</h1>
                <div class="event-hero__meta">
                    <span class="event-hero__meta-item">
                        <i class="ri-calendar-line"></i>
                        <span id="blog-date">Loading...</span>
                    </span>
                    <span class="event-hero__meta-item">
                        <i class="ri-user-line"></i>
                        <span id="blog-author">Loading...</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Description -->
    <section class="event-section">
        <div class="event-container event-container--narrow">
            <div class="event-description">
                <h2 class="event-section__title" id="article-title"><?php echo t('article', 'Article'); ?></h2>
                <div class="event-description__text" id="blog-content">
                    Loading...
                </div>
            </div>
        </div>
    </section>

    <!-- Related Blogs -->
    <section class="event-section">
        <div class="event-container">
            <h2 class="event-section__title" id="related-articles-title"><?php echo t('related_articles', 'Related Articles'); ?></h2>
            <div class="event-related" id="related-blogs">
                <!-- Related blogs will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="lakum-footer">
        <div class="lakum-footer__container">
            <div class="lakum-footer__content">
                <div class="lakum-footer__brand">
                    <div class="lakum-footer__logo">
                        <img src="assest/logo/right_section.png" alt="LAKUM" class="lakum-footer__logo-left" width="105" height="80" decoding="async">
                        <img src="assest/logo/left_section.png" alt="Artspace" class="lakum-footer__logo-right" width="105" height="80" decoding="async">
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
                <p class="lakum-footer__copyright"><?php echo t('footer_copyright', '� 2025 - 2027 LAKUM Artspace. All rights reserved.'); ?></p>
                <div class="lakum-footer__legal">
                    <a href="terms.php" class="lakum-footer__legal-link"><?php echo t('footer_terms', 'Terms & Conditions'); ?></a>
                    <span class="lakum-footer__legal-divider">|</span>
                    <a href="privacy.php" class="lakum-footer__legal-link"><?php echo t('footer_privacy', 'Privacy Policy'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assest/fun-interactions.js?v=5.0.0" defer></script>
    <script src="mobile-performance-optimizer.js?v=5.0.0" defer></script>

    <script>
        // Blog Details Page Script
        let blogIdentifier = new URLSearchParams(window.location.search).get('title') || new URLSearchParams(window.location.search).get('id');
        let urlLang = new URLSearchParams(window.location.search).get('lang');

        async function loadBlogDetails() {
            try {
                if (!blogIdentifier) {
                    console.error('No blog ID or title provided');
                    document.getElementById('blog-title').textContent = 'Blog not found';
                    return;
                }

                // Fetch blog details from API with language parameter
                // Priority: URL parameter > window.LAKUM_LANG > localStorage > 'en'
                const lang = urlLang || window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
                const response = await fetch(`api/get_blogs_working.php?id=${blogIdentifier}&lang=${lang}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const blog = result.data;

                    // Update URL to use English title slug (only once)
                    if (!new URLSearchParams(window.location.search).get('title')) {
                        const englishTitle = blog.title_en || blog.title;
                        const blogSlug = englishTitle.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('title', blogSlug);
                        newUrl.searchParams.delete('id');
                        window.history.replaceState({}, '', newUrl);
                        blogIdentifier = blog.id; // Update for related blogs
                    }

                    // Update page title
                    document.title = `${blog.title} - LAKUM Artspace`;
                    document.getElementById('page-title').textContent = `${blog.title} - LAKUM Artspace`;

                    // Update hero section
                    document.getElementById('blog-title').textContent = blog.title;
                    document.getElementById('blog-date').textContent = new Date(blog.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    document.getElementById('blog-author').textContent = blog.author || 'LAKUM Team';

                    // Update hero image
                    if (blog.cover_image) {
                        document.getElementById('hero-image').src = blog.cover_image;
                    }

                    // Update content
                    document.getElementById('blog-content').innerHTML = blog.content;

                    // Load gallery images if available
                    if (blog.gallery && blog.gallery.length > 0) {
                        loadGallery(blog.gallery);
                    }

                    // Load related blogs
                    loadRelatedBlogs(blog.id);
                } else {
                    document.getElementById('blog-title').textContent = 'Blog not found';
                }
            } catch (error) {
                console.error('Error loading blog details:', error);
                document.getElementById('blog-title').textContent = 'Error loading blog';
            }
        }

        function loadGallery(images) {
            const gallery = document.getElementById('blog-gallery');
            gallery.innerHTML = '';

            images.forEach((image, index) => {
                const item = document.createElement('div');
                item.className = 'event-gallery__item';
                item.innerHTML = `<img src="${image}" alt="Blog gallery image ${index + 1}" loading="lazy">`;
                gallery.appendChild(item);
            });
        }

        async function loadRelatedBlogs(currentBlogId) {
            try {
                const lang = urlLang || window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
                const response = await fetch(`api/get_blogs_working.php?limit=3&lang=${lang}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const relatedBlogs = result.data.filter(blog => blog.id !== currentBlogId).slice(0, 3);
                    const container = document.getElementById('related-blogs');
                    container.innerHTML = '';

                    relatedBlogs.forEach(blog => {
                        const card = document.createElement('div');
                        card.className = 'event-related__item';
                        card.innerHTML = `
                            <a href="blogPageDetails.php?id=${blog.id}" class="event-related__link">
                                <div class="event-related__image">
                                    <img src="${blog.cover_image}" alt="${blog.title}" loading="lazy">
                                </div>
                                <div class="event-related__content">
                                    <h3 class="event-related__title">${blog.title}</h3>
                                    <p class="event-related__date">${new Date(blog.created_at).toLocaleDateString()}</p>
                                </div>
                            </a>
                        `;
                        container.appendChild(card);
                    });
                }
            } catch (error) {
                console.error('Error loading related blogs:', error);
            }
        }

        // Load blog details when page loads
        function initBlogDetailsPage() {
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initBlogDetailsPage, 100);
                return;
            }
            loadBlogDetails();
        }
        
        document.addEventListener('DOMContentLoaded', initBlogDetailsPage);

        // Update section titles when language changes
        function updateBlogSectionTitles() {
            const lang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('lakum_language') || 'en';
            
            const translations = {
                'article': lang === 'ar' ? '????' : 'Article',
                'related_articles': lang === 'ar' ? '?????? ??? ???' : 'Related Articles'
            };

            const articleTitle = document.getElementById('article-title');
            const relatedTitle = document.getElementById('related-articles-title');

            if (articleTitle) {
                articleTitle.textContent = translations['article'];
            }
            if (relatedTitle) {
                relatedTitle.textContent = translations['related_articles'];
            }
        }

        // Watch for language changes
        let currentBlogLang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('lakum_language') || 'en';
        
        const blogObserver = new MutationObserver(() => {
            const newLang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('lakum_language') || 'en';
            if (newLang !== currentBlogLang) {
                currentBlogLang = newLang;
                updateBlogSectionTitles();
            }
        });

        blogObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['lang']
        });

        // Update titles on page load
        updateBlogSectionTitles();

        // Load external scripts for FAB and mobile menu
        const fabScript = document.createElement('script');
        fabScript.src = 'assest/fun-interactions.js';
        fabScript.defer = true;
        document.head.appendChild(fabScript);

        const navScript = document.createElement('script');
        navScript.src = 'assest/navbar-mobile-toggle.js';
        navScript.defer = true;
        document.head.appendChild(navScript);

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
        // Listen for dynamic content loaded event and render blog details based on language
        document.addEventListener('lakum-content-loaded', (e) => {
            if (e.detail.contentType === 'blog') {
                const blogs = e.detail.content;
                
                if (!blogs || blogs.length === 0) return;
                
                // Get blog ID from URL
                const urlParams = new URLSearchParams(window.location.search);
                const blogId = parseInt(urlParams.get('id'));
                
                // Find the blog with matching ID
                const blog = Array.isArray(blogs) ? blogs.find(b => b.id === blogId) : blogs;
                
                if (!blog) return;
                
                // Update page content
                const titleEl = document.getElementById('blog-title');
                const contentEl = document.getElementById('blog-content');
                const authorEl = document.getElementById('blog-author');
                const coverEl = document.getElementById('blog-cover');
                
                if (titleEl) titleEl.textContent = blog.title;
                if (contentEl) contentEl.innerHTML = blog.content;
                if (authorEl) authorEl.textContent = blog.author || 'LAKUM Team';
                if (coverEl && blog.cover_image) coverEl.src = blog.cover_image;
            }
        });
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').catch(err => console.log('SW registration failed:', err));
        }
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

    <div class="fab-button" id="fabButton"><button class="fab-button__trigger" id="fabTrigger" aria-label="Contact options" aria-expanded="false"><i class="ri-mail-line fab-button__icon"></i><i class="ri-close-line fab-button__close"></i></button><div class="fab-button__menu" id="fabMenu" role="menu"><a href="tel:+966920012083" class="fab-button__item" role="menuitem" data-tooltip="Call us"><i class="ri-phone-line"></i></a><a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp"><i class="ri-whatsapp-line"></i></a><a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email"><i class="ri-mail-line"></i></a></div></div><script src="assest/fab-button.js" defer></script>
    <script src="assest/app-header.js" defer></script>
</body>

</html>













