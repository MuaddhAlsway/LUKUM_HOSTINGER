<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';

// Get title from query parameter (rewritten by .htaccess)
$title = $_GET['title'] ?? null;
$lang = $_GET['lang'] ?? 'en';

// If no title, show error
if (!$title) {
    header('HTTP/1.0 404 Not Found');
    exit('Blog not found');
}
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

    <!-- Preload Hero Image (Critical for LCP) -->
    <link rel="preload" as="image" href="heroImage/img-3.webp" fetchpriority="high">

    <!-- Preload LCP image (hero) - Mobile-first with responsive variants -->
    <link rel="preload" as="image" 
          href="heroImage/img-4.webp"
          imagesrcset="heroImage/img-4.webp 1200w"
          imagesizes="(max-width: 768px) 100vw, 650px"
          fetchpriority="high">
    <!-- Preload critical fonts -->
    <link rel="preload" href="fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>

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
    <link rel="prefetch" href="fonts/GretaArabicAR+LT-Regular.otf" as="font" type="font/otf" crossorigin>
    <link rel="prefetch" href="fonts/GretaArabicAR+LT-Light.otf" as="font" type="font/otf" crossorigin>

    <!-- Global Stylesheets (Centralized) -->
    <?php include('includes/stylesheets.php'); ?>

    <!-- Page-specific styles -->
    <link rel="stylesheet" href="blog-page-details.css">
    
    <!-- Alternate language links for SEO -->
    <link rel="alternate" hreflang="en" href="https://lakumartspace.com/blogPageDetails.php?lang=en" />
    <link rel="alternate" hreflang="ar" href="https://lakumartspace.com/blogPageDetails.php?lang=ar" />
</head>

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
            <img id="hero-image" src="assest/img-4.webp" alt="Blog" class="lakum-hero__image" loading="eager" fetchpriority="high" decoding="async" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="lakum-hero__overlay"></div>
        </div>
        <div class="lakum-hero__content">
            <h1 class="lakum-hero__title" id="blog-title">Loading...</h1>
            <p class="lakum-hero__subtitle">
                <span id="blog-date">Loading...</span> • <span id="blog-author">Loading...</span>
            </p>
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
            <a href="mailto:info@lakumartspace.com" class="fab-button__item" role="menuitem" data-tooltip="Email us">
                <i class="ri-mail-line"></i>
            </a>
            <a href="https://wa.me/966920012083" target="_blank" class="fab-button__item" role="menuitem" data-tooltip="WhatsApp">
                <i class="ri-whatsapp-line"></i>
            </a>
        </div>
    </div>

    <script src="assest/fab-button.js" defer></script>

    <script>
        // Blog Details Page Script
        // Pass title from PHP to JavaScript (for clean URLs)
        window.LAKUM_BLOG_TITLE = '<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>';
        window.LAKUM_LANG = '<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>';
        
        let blogIdentifier = window.LAKUM_BLOG_TITLE || new URLSearchParams(window.location.search).get('title') || new URLSearchParams(window.location.search).get('id');
        let urlLang = window.LAKUM_LANG || new URLSearchParams(window.location.search).get('lang');
        let shouldRedirect = false;

        // IMMEDIATE REDIRECT: If ID parameter exists, redirect to title slug
        if (new URLSearchParams(window.location.search).get('id')) {
            shouldRedirect = true;
            // Fetch blog to get title slug
            const blogId = new URLSearchParams(window.location.search).get('id');
            const lang = urlLang || window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            
            fetch(`/api/get_blogs.php?lang=${lang}&id=${blogId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data) {
                        const blog = data.data;
                        const englishTitle = blog.title_en || blog.title || '';
                        const titleSlug = englishTitle.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                        
                        if (titleSlug) {
                            const newUrl = new URL(window.location);
                            newUrl.searchParams.set('title', titleSlug);
                            newUrl.searchParams.delete('id');
                            if (!newUrl.searchParams.get('lang')) {
                                newUrl.searchParams.set('lang', lang);
                            }
                            window.location.replace(newUrl.toString());
                        }
                    } else {
                        console.error('Blog not found:', data);
                        document.getElementById('blog-title').textContent = 'Blog not found';
                    }
                })
                .catch(err => {
                    console.error('Redirect error:', err);
                    document.getElementById('blog-title').textContent = 'Error loading blog';
                });
            
            // Exit early - don't load blog details yet
            document.addEventListener('DOMContentLoaded', () => {
                if (shouldRedirect) return;
            });
        }

        async function loadBlogDetails() {
            try {
                if (!blogIdentifier) {
                    console.error('No blog ID or title provided');
                    document.getElementById('blog-title').textContent = 'Blog not found';
                    // Hide page loader
                    const loader = document.getElementById('pageLoader');
                    if (loader) loader.classList.remove('lakum-page-loader--active');
                    return;
                }

                // Fetch blog details from API with language parameter
                // Priority: URL parameter > window.LAKUM_LANG > localStorage > 'en'
                const lang = urlLang || window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
                
                // Build API URL - if blogIdentifier is numeric, use as ID, otherwise use as slug
                let apiUrl = `/api/get_blogs_working.php?lang=${lang}`;
                let isNumericId = !isNaN(blogIdentifier) && blogIdentifier.trim() !== '';
                
                if (isNumericId) {
                    apiUrl += `&id=${blogIdentifier}`;
                } else {
                    // Use slug parameter for slug-based lookup (more efficient)
                    apiUrl += `&slug=${encodeURIComponent(blogIdentifier)}`;
                }
                
                const response = await fetch(apiUrl);
                const result = await response.json();

                if (result.success && result.data) {
                    // API returns single blog object when using slug or ID
                    const blog = result.data;
                    
                    if (!blog || !blog.id) {
                        document.getElementById('blog-title').textContent = 'Blog not found';
                        return;
                    }

                    // Update page title
                    document.title = `${blog.title} - LAKUM Artspace`;
                    document.getElementById('page-title').textContent = `${blog.title} - LAKUM Artspace`;
                    
                    // Format and display date
                    const formattedDate = blog.created_at ? new Date(blog.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : 'Unknown Date';
                    document.getElementById('blog-date').textContent = formattedDate;
                    document.getElementById('blog-author').textContent = blog.author || 'LAKUM Team';

                    // Update hero image
                    if (blog.cover_image) {
                        document.getElementById('hero-image').src = blog.cover_image;
                    }

                    // Update content with proper UTF-8 handling
                    const contentEl = document.getElementById('blog-content');
                    if (contentEl) {
                        // Ensure proper text encoding for Arabic and other languages
                        contentEl.innerHTML = blog.content || '';
                        // Force text direction based on language
                        if (lang === 'ar') {
                            contentEl.setAttribute('dir', 'rtl');
                            contentEl.style.textAlign = 'right';
                        } else {
                            contentEl.setAttribute('dir', 'ltr');
                            contentEl.style.textAlign = 'left';
                        }
                    }

                    // Load gallery images if available
                    if (blog.gallery && blog.gallery.length > 0) {
                        loadGallery(blog.gallery);
                    }

                    // Load related blogs
                    loadRelatedBlogs(blog.id);
                    
                    // Hide page loader after content is loaded
                    const loader = document.getElementById('pageLoader');
                    if (loader) loader.classList.remove('lakum-page-loader--active');
                } else {
                    document.getElementById('blog-title').textContent = 'Blog not found';
                    // Hide page loader
                    const loader = document.getElementById('pageLoader');
                    if (loader) loader.classList.remove('lakum-page-loader--active');
                }
            } catch (error) {
                console.error('Error loading blog details:', error);
                document.getElementById('blog-title').textContent = 'Error loading blog';
                // Hide page loader
                const loader = document.getElementById('pageLoader');
                if (loader) loader.classList.remove('lakum-page-loader--active');
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
                const response = await fetch(`/api/get_blogs.php?limit=3&lang=${lang}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const relatedBlogs = result.data.filter(blog => blog.id !== currentBlogId).slice(0, 3);
                    const container = document.getElementById('related-blogs');
                    container.innerHTML = '';

                    relatedBlogs.forEach(blog => {
                        const card = document.createElement('div');
                        card.className = 'event-related__item';
                        // Always use English title for user-friendly slug
                        const blogSlug = (blog.title_en || '').toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                        const langParam = lang !== 'en' ? `&lang=${lang}` : '';
                        card.innerHTML = `
                            <a href="blogPageDetails.php?title=${blogSlug}${langParam}" class="event-related__link">
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
            // Load blog immediately - don't wait for LanguageManager
            loadBlogDetails();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initBlogDetailsPage();
            // Ensure page loader is hidden after 3 seconds max
            setTimeout(function() {
                const loader = document.getElementById('pageLoader');
                if (loader) loader.classList.remove('lakum-page-loader--active');
            }, 3000);
        });

        // Update section titles when language changes
        function updateBlogSectionTitles() {
            const lang = new URLSearchParams(window.location.search).get('lang') || localStorage.getItem('lakum_language') || 'en';
            
            // Use PHP-provided translations or fallback
            const translations = {
                'article': lang === 'ar' ? 'المقالة' : 'Article',
                'related_articles': lang === 'ar' ? 'مقالات ذات صلة' : 'Related Articles'
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

    <!-- Service Worker Registration (optional) -->
    <script>
        if ('serviceWorker' in navigator && false) {
            // Disabled - sw.js not available
            // navigator.serviceWorker.register('sw.js').catch(err => console.log('SW registration failed:', err));
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

    <!-- Global Scripts (Centralized) -->
    <?php include('includes/scripts.php'); ?>
</body>

</html>















