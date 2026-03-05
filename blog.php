<?php
require_once 'lang/loader.php';
require_once 'api/image-helper.php';
?><!DOCTYPE html>
<html <?php echo getLanguageAttributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('blog_page_title', 'Blog - LAKUM Artspace'); ?></title>
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
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            text-align: center;
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

        .lakum-blog-hero__overlay {
            position: absolute;
            inset: 0;
            background-image: url(assest/img-4.png);
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 1;
        }

        .lakum-blog-hero__title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 300;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin: 0 0 clamp(16px, 2vw, 24px) 0;
            line-height: 1.2;
            text-align: center !important;
        }

        .lakum-blog-hero__subtitle {
            font-size: clamp(1rem, 1.5vw, 1.2rem);
            font-weight: 300;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            text-align: center !important;
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
    <link rel="stylesheet" href="blog.css">
    <script src="assest/static-json-translator.js?v=1.0.0" defer></script>

<link rel="alternate" hreflang="en" href="https://lakumartspace.infinityfree.me/blog.php?lang=en" />
<link rel="alternate" hreflang="ar" href="https://lakumartspace.infinityfree.me/blog.php?lang=ar" />
</head>

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
                 alt="Blog"
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
            <h1 class="lakum-hero__title"><?php echo t('blog_hero_title', 'Stories & Insights'); ?></h1>
            <p class="lakum-hero__subtitle"><?php echo t('blog_hero_subtitle', 'Explore the world of art, culture, and creativity through our curated collection of articles, interviews, and behind-the-scenes stories'); ?></p>
        </div>
    </section>

    <!-- Blog Content -->
    <section class="lakum-blog-content">
        <div class="lakum-container">
            <!-- Filters -->
            <div class="lakum-blog-filters" id="blogFilters">
                <button class="lakum-blog-filter active" data-category="all"><?php echo t('blog_all_posts', 'All Posts'); ?></button>
            </div>

            <!-- Blog Grid -->
            <div class="lakum-blog-grid" id="blogGrid">
                <!-- Loading skeleton -->
                <div class="lakum-skeleton-card lakum-skeleton-card--featured"></div>
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
                <div class="lakum-skeleton-card"></div>
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

    <script src="assest/fun-interactions.js" defer></script>
    <script src="assest/navbar-mobile-toggle.js" defer></script>
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
        // Helper function to get blog URL (using title instead of ID)
        const getBlogUrl = (blog) => {
            // Always use English title for user-friendly slug
            const title = blog.title_en || blog.title || 'blog';
            const lang = window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
            return `blogPageDetails.php?title=${encodeURIComponent(title)}&lang=${lang}`;
        };

        // Translation strings for JavaScript
        const translations = {
            allPosts: "<?php echo t('blog_all_posts', 'All Posts'); ?>",
            noBlogPosts: "<?php echo t('blog_no_posts', 'No Blog Posts Yet'); ?>",
            checkBackSoon: "<?php echo t('blog_check_back_soon', 'Check back soon for articles and stories'); ?>",
            editorsPick: "<?php echo t('blog_editors_pick', 'Editors Pick'); ?>",
            minRead: "<?php echo t('blog_min_read', 'min read'); ?>",
            readMore: "<?php echo t('blog_read_more', 'Read More'); ?>",
            by: "<?php echo t('blog_by', 'By'); ?>",
            lakumTeam: "<?php echo t('blog_lakum_team', 'LAKUM Team'); ?>",
            blog_category_art_history: "<?php echo t('blog_category_art_history', 'Art History'); ?>",
            blog_category_career: "<?php echo t('blog_category_career', 'Career'); ?>",
            blog_category_digital_art: "<?php echo t('blog_category_digital_art', 'Digital Art'); ?>",
            blog_category_photography: "<?php echo t('blog_category_photography', 'Photography'); ?>",
            blog_category_sculpture: "<?php echo t('blog_category_sculpture', 'Sculpture'); ?>",
            blog_category_art_culture: "<?php echo t('blog_category_art_culture', 'Art & Culture'); ?>",
            blog_category_exhibition: "<?php echo t('blog_category_exhibition', 'Exhibition'); ?>",
            blog_category_community: "<?php echo t('blog_category_community', 'Community'); ?>",
            blog_category_news: "<?php echo t('blog_category_news', 'News'); ?>",
            blog_category_tutorial: "<?php echo t('blog_category_tutorial', 'Tutorial'); ?>",
            blog_category_behind_scenes: "<?php echo t('blog_category_behind_scenes', 'Behind the Scenes'); ?>",
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

        // Check if RTL
        const isRTL = document.documentElement.dir === 'rtl';
        const arrowIcon = isRTL ? 'ri-arrow-left-line' : 'ri-arrow-right-line';

        const blogGrid = document.getElementById('blogGrid');
        let allBlogs = [];

        // Load blogs from API
        function loadBlogs() {
            const lang = localStorage.getItem('selectedLanguage') || 'en';
            const apiUrl = `api/get_blogs_working.php?type=all&lang=${lang}`;
            console.log('Fetching blogs from:', apiUrl);

            fetch(apiUrl)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('API result:', result);
                    // Extract data array from API response
                    const blogs = result.data || result || [];
                    console.log('Blogs received:', blogs);
                    console.log('Number of blogs:', blogs.length);
                    allBlogs = blogs;
                    generateFilters(blogs);
                    renderBlogs(blogs);
                })
                .catch(error => {
                    console.error('Error loading blogs:', error);
                    blogGrid.innerHTML = `
                        <div class="lakum-empty-state">
                            <i class="ri-article-line lakum-empty-state__icon"></i>
                            <h3 class="lakum-empty-state__title">${translations.noBlogPosts}</h3>
                            <p class="lakum-empty-state__text">${translations.checkBackSoon}</p>
                            <p style="color: red; font-size: 12px; margin-top: 10px;">Error: ${error.message}</p>
                        </div>
                    `;
                });
        }

        // Map category names to translation keys (camelCase for translations object)
        function getCategoryTranslationKey(category) {
            const categoryMap = {
                'Art History': 'blog_category_art_history',
                'Career': 'blog_category_career',
                'Digital Art': 'blog_category_digital_art',
                'Photography': 'blog_category_photography',
                'Sculpture': 'blog_category_sculpture',
                'Art & Culture': 'blog_category_art_culture',
                'Exhibition': 'blog_category_exhibition',
                'Community': 'blog_category_community',
                'News': 'blog_category_news',
                'Tutorial': 'blog_category_tutorial',
                'Behind the Scenes': 'blog_category_behind_scenes'
            };
            return categoryMap[category] || category;
        }

        // Get translated category name
        function getTranslatedCategory(category) {
            const key = getCategoryTranslationKey(category);
            // Try to get from translations object (loaded from PHP)
            if (translations[key]) {
                return translations[key];
            }
            // Fallback to original category name
            return category;
        }

        // Generate filter buttons dynamically based on blog categories
        function generateFilters(blogs) {
            const filterContainer = document.getElementById('blogFilters');
            const categories = new Set();
            
            // Extract unique categories from blogs
            blogs.forEach(blog => {
                if (blog.category) {
                    categories.add(blog.category);
                }
            });
            
            // Clear existing filters except "All Posts"
            const allPostsBtn = filterContainer.querySelector('[data-category="all"]');
            filterContainer.innerHTML = '';
            filterContainer.appendChild(allPostsBtn);
            
            // Add category buttons with translated names
            Array.from(categories).sort().forEach(category => {
                const btn = document.createElement('button');
                btn.className = 'lakum-blog-filter';
                btn.dataset.category = category;
                btn.textContent = getTranslatedCategory(category);
                filterContainer.appendChild(btn);
            });
            
            // Re-attach filter event listeners
            attachFilterListeners();
        }

        // Render blogs
        async function renderBlogs(blogs, isCategoryFilter = false) {
            // Check if blogs is an error object
            if (blogs && blogs.error) {
                blogGrid.innerHTML = `
                    <div class="lakum-empty-state">
                        <i class="ri-error-warning-line lakum-empty-state__icon"></i>
                        <h3 class="lakum-empty-state__title">Error Loading Blogs</h3>
                        <p class="lakum-empty-state__text">${blogs.error}</p>
                    </div>
                `;
                return;
            }

            if (!blogs || blogs.length === 0) {
                blogGrid.innerHTML = `
                    <div class="lakum-empty-state">
                        <i class="ri-article-line lakum-empty-state__icon"></i>
                        <h3 class="lakum-empty-state__title">${translations.noBlogPosts}</h3>
                        <p class="lakum-empty-state__text">${translations.checkBackSoon}</p>
                    </div>
                `;
                return;
            }

            blogGrid.innerHTML = '';

            // Render immediately
            const blogCards = [];
            blogs.forEach((blog, index) => {
                const card = document.createElement('article');
                const isFeatured = isCategoryFilter && index === 0;
                card.className = `lakum-blog-card${isFeatured ? ' lakum-blog-card--featured' : ''}`;
                card.dataset.category = blog.category || 'news';
                card.style.cursor = 'pointer';
                card.onclick = () => window.location.href = getBlogUrl(blog);

                const coverImage = blog.cover_image || 'assest/img-4.webp';
                const categoryLabel = getTranslatedCategory(blog.category || 'News');
                const formattedDate = blog.created_at ? new Date(blog.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Unknown Date';
                const readTime = blog.read_time || '2';
                const authorInitials = blog.author ? blog.author.split(' ').map(n => n[0]).join('').toUpperCase() : 'LT';

                card.innerHTML = `<div class="lakum-blog-card__image-wrapper"><img src="${coverImage}" alt="${blog.title}" class="lakum-blog-card__image"><span class="lakum-blog-card__category">${categoryLabel}</span></div><div class="lakum-blog-card__content"><div class="lakum-blog-card__meta"><span class="lakum-blog-card__meta-item"><i class="ri-calendar-line"></i>${formattedDate}</span><span class="lakum-blog-card__meta-item"><i class="ri-time-line"></i>${readTime} ${translations.minRead}</span></div><h3 class="lakum-blog-card__title" data-blog-id="${blog.id}" data-original-title="${blog.title}">${blog.title}</h3><p class="lakum-blog-card__excerpt" data-blog-id="${blog.id}" data-original-excerpt="${blog.excerpt}">${blog.excerpt}</p><div class="lakum-blog-card__footer"><div class="lakum-blog-card__author"><div class="lakum-blog-card__author-avatar">${authorInitials}</div><span class="lakum-blog-card__author-name">${blog.author || translations.lakumTeam}</span></div><span class="lakum-blog-card__read-more">${translations.readMore} <i class="ri-arrow-right-line"></i></span></div></div>`;

                blogGrid.appendChild(card);
                blogCards.push(card);
            });

            // Note: Blog titles and excerpts are stored in database
            // When admin panel adds translation fields, update this to fetch translated content
            // For now, UI strings (min read, Read More, etc.) are translated via translations object
        }

        // Attach filter event listeners
        function attachFilterListeners() {
            const filterBtns = document.querySelectorAll('.lakum-blog-filter');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const category = btn.dataset.category;

                    if (category === 'all') {
                        renderBlogs(allBlogs, false);
                    } else {
                        const filtered = allBlogs.filter(blog =>
                            blog.category && blog.category.toLowerCase() === category.toLowerCase()
                        );
                        renderBlogs(filtered, true);
                    }
                });
            });
        }

        // Update filter button text when language changes
        function updateFilterButtonsLanguage() {
            const filterBtns = document.querySelectorAll('.lakum-blog-filter');
            filterBtns.forEach(btn => {
                const category = btn.dataset.category;
                if (category === 'all') {
                    btn.textContent = translations.allPosts || "<?php echo t('blog_all_posts', 'All Posts'); ?>";
                } else {
                    btn.textContent = getTranslatedCategory(category);
                }
            });
        }

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
                        // Update filter buttons
                        updateFilterButtonsLanguage();
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
                            updateFilterButtonsLanguage();
                            updateNavbarFooterLanguage();
                        }
                    })
                    .catch(err => console.log('Language update skipped'));
            }
        });

        // Load blogs on page load
        function initBlogPage() {
            if (typeof LanguageManager === 'undefined') {
                console.warn('LanguageManager not ready, retrying...');
                setTimeout(initBlogPage, 100);
                return;
            }
            loadBlogs();
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBlogPage);
        } else {
            initBlogPage();
        }
    </script>

    <script>
        // Listen for dynamic content loaded event and render blogs based on language
        document.addEventListener('lakum-content-loaded', (e) => {
            if (e.detail.contentType === 'blog') {
                const blogs = e.detail.content;
                const blogGrid = document.getElementById('blogGrid');
                
                if (!blogGrid || !blogs || blogs.length === 0) return;
                
                // Clear existing content
                blogGrid.innerHTML = '';
                
                // Render blogs
                blogs.forEach((blog, index) => {
                    const card = document.createElement('article');
                    card.className = 'lakum-blog-card';
                    card.dataset.category = blog.category || 'news';
                    card.style.cursor = 'pointer';
                    
                    const coverImage = blog.cover_image || 'assest/img-4.webp';
                    const formattedDate = new Date(blog.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    const authorInitials = blog.author ? blog.author.split(' ').map(n => n[0]).join('').toUpperCase() : 'LT';
                    
                    card.innerHTML = `
                        <div class="lakum-blog-card__image-wrapper">
                            <img src="${coverImage}" alt="${blog.title}" class="lakum-blog-card__image">
                            <span class="lakum-blog-card__category">${blog.category || 'News'}</span>
                        </div>
                        <div class="lakum-blog-card__content">
                            <div class="lakum-blog-card__meta">
                                <span class="lakum-blog-card__meta-item"><i class="ri-calendar-line"></i>${formattedDate}</span>
                            </div>
                            <h3 class="lakum-blog-card__title">${blog.title}</h3>
                            <p class="lakum-blog-card__excerpt">${blog.content ? blog.content.substring(0, 150) + '...' : ''}</p>
                            <div class="lakum-blog-card__footer">
                                <div class="lakum-blog-card__author">
                                    <div class="lakum-blog-card__author-avatar">${authorInitials}</div>
                                    <span class="lakum-blog-card__author-name">${blog.author || 'LAKUM Team'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    card.addEventListener('click', () => {
                        const lang = window.LAKUM_LANG || localStorage.getItem('lakum_language') || 'en';
                        const title = blog.title_en || blog.title || 'blog';
                        window.location.href = `blogPageDetails.php?title=${encodeURIComponent(title)}&lang=${lang}`;
                    });
                    
                    blogGrid.appendChild(card);
                });
            }
        });
    </script>

    <script src="assest/popup-notification.js?v=5.0.0" defer></script>

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




















